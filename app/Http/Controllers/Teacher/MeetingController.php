<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Company;
use App\Models\Meeting;
use App\Models\User;
use App\Services\Zoom\ZoomClient;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MeetingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $assignedCompanyIds = $user->assignedCompanies()->pluck('companies.id');

        // 1. Get Scheduled Meetings
        $meetings = Meeting::whereIn('company_id', $assignedCompanyIds)
            ->with(['company', 'participants'])
            ->get();

        // 2. Get Meeting Logs (Zoom Meetings created via Hub)
        $logs = \App\Models\MeetingLog::whereIn('company_id', $assignedCompanyIds)
            ->whereNotNull('zoom_meeting_id') // Only show Zoom logs as "Meetings"? Or all? User said "Created MTG not reflected". Hub creates logs. 
            ->with(['company', 'students'])
            ->get();

        // 3. Merge and Sort
        $merged = $meetings->concat($logs)->sortByDesc(function ($item) {
            return $item->scheduled_at; // Uses accessor for Log
        });

        // 4. Paginate
        $page = request()->get('page', 1);
        $perPage = 10;
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $merged->forPage($page, $perPage),
            $merged->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('teacher.meetings.index', ['meetings' => $paginator]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();

        // Get assigned companies with their students
        $companies = $user->assignedCompanies()->with('students')->get();

        // Default Time: Next hour 00:00
        $defaultTime = now()->addHour()->startOfHour()->format('Y-m-d\TH:i');

        return view('teacher.meetings.create', compact('companies', 'defaultTime'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, ZoomClient $zoomClient)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id', // Should check if assigned to teacher
            'title' => 'required|string|max:255',
            'scheduled_at' => 'required|date',
            'duration_minutes' => 'required|integer|min:15|max:480',
            'participants' => 'array',
            'participants.*' => 'exists:users,id',
        ]);

        // Security check: ensure teacher is assigned to this company
        $user = Auth::user();
        if (!$user->assignedCompanies()->where('companies.id', $request->company_id)->exists()) {
            abort(403, 'この企業のMTGを作成する権限がありません。');
        }

        DB::beginTransaction();
        try {
            // 1. Create Zoom Meeting
            $zoomResult = $zoomClient->createMeeting(
                $request->title,
                date('Y-m-d\TH:i:s', strtotime($request->scheduled_at)),
                (int) $request->duration_minutes
            );

            if (!$zoomResult) {
                return back()->withInput()->with('error', 'Zoomミーティングの作成に失敗しました。');
            }

            // 2. Create DB Record
            $meeting = Meeting::create([
                'company_id' => $request->company_id,
                'title' => $request->title,
                'scheduled_at' => $request->scheduled_at,
                'duration_minutes' => $request->duration_minutes,
                'zoom_meeting_id' => $zoomResult['id'] ?? null,
                'zoom_join_url' => $zoomResult['join_url'] ?? null,
                'zoom_start_url' => $zoomResult['start_url'] ?? null,
                'zoom_passcode' => $zoomResult['password'] ?? null, // 'password' is the key in Zoom API response
                'created_by' => $user->id,
            ]);

            // 3. Attach Participants
            if (!empty($request->participants)) {
                // Ensure participants belong to the selected company
                $validStudents = User::whereIn('id', $request->participants)
                    ->where('company_id', $request->company_id)
                    ->where('role', User::ROLE_STUDENT)
                    ->pluck('id');

                // Add validation warning if count mismatches? For now, just sync valid ones.
                foreach ($validStudents as $studentId) {
                    \App\Models\MeetingParticipant::create([
                        'meeting_id' => $meeting->id,
                        'student_id' => $studentId,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('teacher.meetings.index')->with('success', 'MTGを作成しました！');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'エラーが発生しました: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Meeting $meeting)
    {
        $user = Auth::user();

        // Security check: teacher must be assigned to the company of the meeting
        if (!$user->assignedCompanies()->where('companies.id', $meeting->company_id)->exists()) {
            abort(403);
        }

        $meeting->load(['company', 'participants.student']);

        return view('teacher.meetings.show', compact('meeting'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Meeting $meeting)
    {
        // MVP: Not implementing Edit for now as per minimal requirments, 
        // or simple logic similar to create. Leaving empty for now.
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Meeting $meeting, ZoomClient $zoomClient)
    {
        $user = Auth::user();
        if (!$user->assignedCompanies()->where('companies.id', $meeting->company_id)->exists()) {
            abort(403);
        }

        // Delete from Zoom
        if ($meeting->zoom_meeting_id) {
            $zoomClient->deleteMeeting($meeting->zoom_meeting_id);
        }

        $meeting->delete();

        return redirect()->route('teacher.meetings.index')->with('success', 'MTGを削除しました。');
    }
}
