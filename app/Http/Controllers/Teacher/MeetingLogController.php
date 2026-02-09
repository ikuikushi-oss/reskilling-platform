<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\MeetingLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MeetingLogController extends Controller
{
    /**
     * Display a listing of the resource for a specific student.
     */
    public function index(Request $request, User $student)
    {
        // Redirect to Hub as main entry point
        return redirect()->route('teacher.students.mtgs', $student);
    }

    /**
     * Display the MTG Hub (Create Form + List) for a specific student.
     */
    public function hub(Request $request, User $student)
    {
        $user = Auth::user();

        // 1. Security Check: Teacher must be assigned to the student's company
        if (!$user->assignedCompanies()->where('companies.id', $student->company_id)->exists()) {
            abort(403, 'この生徒のMTGログを閲覧する権限がありません。');
        }

        // 2. Get Logs (for List)
        $logs = MeetingLog::whereHas('students', function ($query) use ($student) {
            $query->where('users.id', $student->id);
        })
            ->where('company_id', $student->company_id)
            ->with(['students:id,name']) // Eager load students (id, name only)
            ->orderBy('started_at', 'desc')
            ->paginate(20);

        // 3. Prepare Data for Create Form (though view uses $student relations)
        $assignedCompanies = $user->assignedCompanies;

        // Default Time: Next hour 00:00
        $defaultTime = now()->setTimezone('Asia/Tokyo')->addHour()->startOfHour()->format('Y-m-d\TH:i');

        return view('teacher.meeting_logs.hub', compact('student', 'logs', 'assignedCompanies', 'defaultTime'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        // Redirect to Hub if student is specified
        if ($request->route('student')) {
            return redirect()->route('teacher.students.mtgs', $request->route('student'));
        }
        $user = Auth::user();
        // Optimize: Select only necessary columns for students to reduce memory usage
        $assignedCompanies = $user->assignedCompanies()->with([
            'students' => function ($query) {
                $query->select('users.id', 'users.company_id', 'users.name');
            }
        ])->get();

        $selectedStudent = null;
        $selectedCompanyId = null;

        // If accessed via student route
        if ($request->route('student')) {
            $studentId = $request->route('student');
            $selectedStudent = User::where('id', $studentId)->where('role', User::ROLE_STUDENT)->firstOrFail();

            // Check if teacher is assigned to this student's company
            if (!$assignedCompanies->contains('id', $selectedStudent->company_id)) {
                abort(403, 'この生徒のMTGログを作成する権限がありません。');
            }

            $selectedCompanyId = $selectedStudent->company_id;
        }

        return view('teacher.meeting_logs.create', compact('assignedCompanies', 'selectedStudent', 'selectedCompanyId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, \App\Services\ZoomService $zoomService)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'students' => 'required|array|min:1',
            'students.*' => 'exists:users,id',
            'title' => 'required|string|max:255',
            'started_at' => 'required|date',
            'youtube_url' => 'nullable|url',
            // 'zoom_meeting_id' is no longer in input, it is generated
            'memo' => 'nullable|string',
        ]);

        $user = Auth::user();

        // 1. Security Check: Teacher must be assigned to the company
        if (!$user->assignedCompanies()->where('companies.id', $request->company_id)->exists()) {
            abort(403, 'この企業のMTGログを作成する権限がありません。');
        }

        // 2. Data Consistency Check: All students must belong to the selected company
        $count = User::whereIn('id', $request->students)
            ->where('company_id', $request->company_id)
            ->where('role', User::ROLE_STUDENT)
            ->count();

        if ($count !== count($request->students)) {
            return back()->withInput()->withErrors(['students' => '選択された生徒の中に、指定された企業に所属していない生徒が含まれています。']);
        }

        // Zoom Logic
        $zoomMeetingId = null;
        $zoomJoinUrl = null;
        $zoomStartUrl = null;
        $zoomStatus = 'scheduled'; // Default status

        try {
            DB::beginTransaction();

            // Prepare Zoom Meeting Data
            $mode = config('zoom.mode', 'mock');

            if ($mode === 'production') {
                // Production: Call Zoom API
                // Default duration 60 mins as it's not in form
                $meetingData = $zoomService->createMeeting(
                    $request->title,
                    \Carbon\Carbon::parse($request->started_at)->toIso8601String(),
                    60
                );

                $zoomMeetingId = $meetingData['id'];
                $zoomJoinUrl = $meetingData['join_url'];
                $zoomStartUrl = $meetingData['start_url'] ?? null;
                $zoomStatus = 'scheduled';

            } else {
                // Mock: URLs are null, status is scheduled (but displayed as Not Issued)
                $zoomMeetingId = null;
                $zoomJoinUrl = null;
                $zoomStartUrl = null;
                // Actually user said: "zoom_join_url / zoom_start_url は null のまま保存"
                // "zoom_status は 'scheduled' でもOK（ただしURL未発行表示にする）"
                // So I will set status to 'scheduled' to indicate it is a valid meeting intent, but URLs are missing (mock/pending).
                $zoomStatus = 'scheduled';
            }

            $log = MeetingLog::create([
                'company_id' => $request->company_id,
                'title' => $request->title,
                'started_at' => $request->started_at,
                'youtube_url' => $request->youtube_url,
                'zoom_meeting_id' => $zoomMeetingId,
                'zoom_join_url' => $zoomJoinUrl,
                'zoom_start_url' => $zoomStartUrl,
                'zoom_status' => $zoomStatus,
                'memo' => $request->memo,
                'created_by' => $user->id,
            ]);

            $log->students()->sync($request->students);

            DB::commit();

            return back()->with('success', 'MTGログを作成しました。Zoomステータス: ' . $mode);

        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('MTG Creation Failed: ' . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'MTGの作成に失敗しました: ' . $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MeetingLog $meetingLog)
    {
        $user = Auth::user();

        // Security Check: Teacher must be assigned to the company of the log
        if (!$user->assignedCompanies()->where('companies.id', $meetingLog->company_id)->exists()) {
            abort(403, 'このMTGログを編集する権限がありません。');
        }

        return view('teacher.meeting_logs.edit', compact('meetingLog'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MeetingLog $meetingLog)
    {
        $user = Auth::user();

        // Security Check
        if (!$user->assignedCompanies()->where('companies.id', $meetingLog->company_id)->exists()) {
            abort(403, 'このMTGログを編集する権限がありません。');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'youtube_url' => 'nullable|url',
            'memo' => 'nullable|string',
        ]);

        $meetingLog->update([
            'title' => $request->title,
            'youtube_url' => $request->youtube_url,
            'memo' => $request->memo,
        ]);

        // Redirect back to the hub if possible
        $student = $meetingLog->students->first();
        if ($student) {
            return redirect()->route('teacher.students.mtgs', $student)->with('success', 'MTGログを更新しました。');
        }

        return redirect()->route('teacher.meeting-logs.index')->with('success', 'MTGログを更新しました。');
    }

    /**
     * Display the specified resource.
     */
    public function show(MeetingLog $meetingLog)
    {
        $user = Auth::user();

        // Security Check
        if (!$user->assignedCompanies()->where('companies.id', $meetingLog->company_id)->exists()) {
            abort(403, 'このMTGログを閲覧する権限がありません。');
        }

        // Eager load for display
        $meetingLog->load(['students', 'participants']);

        return view('teacher.meeting_logs.show', compact('meetingLog'));
    }

    /**
     * Upload a transcript file for the meeting log.
     */
    public function uploadTranscript(Request $request, MeetingLog $meetingLog, \App\Services\SubtitleParserService $parser)
    {
        $user = Auth::user();

        // Security Check
        if (!$user->assignedCompanies()->where('companies.id', $meetingLog->company_id)->exists()) {
            abort(403, 'このMTGログを編集する権限がありません。');
        }

        $request->validate([
            'transcript_file' => 'required|file|mimes:srt|max:2048',
        ]);

        $file = $request->file('transcript_file');
        $extension = $file->getClientOriginalExtension();
        $content = $file->get();

        try {
            $text = $parser->parse($content, $extension);

            $meetingLog->update([
                'transcript_text' => $text,
                'transcript_status' => 'ready', // User requested 'ready'
                'transcript_source' => 'youtube_caption',
                'transcript_uploaded_at' => now(),
            ]);

            return back()->with('success', '文字起こしファイルをアップロードしました。');
        } catch (\Exception $e) {
            $meetingLog->update(['transcript_status' => 'failed']);
            return back()->with('error', 'ファイルの解析に失敗しました: ' . $e->getMessage());
        }
    }

    /**
     * Generate summary using AI.
     */
    public function summarize(MeetingLog $meetingLog, \App\Services\MeetingSummaryService $summaryService)
    {
        $user = Auth::user();
        if (!$user->assignedCompanies()->where('companies.id', $meetingLog->company_id)->exists()) {
            abort(403, 'このMTGログを編集する権限がありません。');
        }

        if (empty($meetingLog->transcript_text)) {
            return back()->with('error', '文字起こしテキストがありません。先にファイルをアップロードしてください。');
        }

        try {
            $summary = $summaryService->summarize($meetingLog->transcript_text);
            $meetingLog->update(['transcript_summary' => $summary]);
            return back()->with('success', 'AI要約を生成しました。');
        } catch (\Exception $e) {
            return back()->with('error', 'AI要約の生成に失敗しました: ' . $e->getMessage());
        }
    }
}
