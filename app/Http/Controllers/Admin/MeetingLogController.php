<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MeetingLog;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MeetingLogController extends Controller
{
    use \App\Http\Controllers\Traits\Sortable;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = MeetingLog::with(['company', 'creator', 'participants']);

        $this->applySorting($query, $request, [
            'started_at',
            'title',
            'transcript_status',
            'created_at'
        ], 'started_at', 'desc');

        $logs = $query->paginate(20)->withQueryString();

        return view('admin.meeting_logs.index', compact('logs'));
    }

    public function export()
    {
        $companies = Company::all();
        $teachers = User::where('role', User::ROLE_TEACHER)->get();
        $students = User::where('role', User::ROLE_STUDENT)->get();

        return view('admin.meeting_logs.export', compact('companies', 'teachers', 'students'));
    }

    public function downloadCsv(Request $request)
    {
        $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
            'company_id' => 'nullable|exists:companies,id',
            'teacher_id' => 'nullable|exists:users,id',
            'student_id' => 'nullable|exists:users,id',
            'type' => 'required|in:summary,participants',
        ]);

        $query = MeetingLog::query();

        // Date Range
        if ($request->date_from && $request->date_to) {
            $query->whereBetween('started_at', [
                $request->date_from . ' 00:00:00',
                $request->date_to . ' 23:59:59'
            ]);
        }

        // Company
        if ($request->company_id) {
            $query->where('company_id', $request->company_id);
        }

        // Teacher (Created By or Host Email)
        if ($request->teacher_id) {
            $teacher = User::find($request->teacher_id);
            if ($teacher) {
                $query->where(function ($q) use ($teacher) {
                    $q->where('created_by', $teacher->id)
                        ->orWhere('host_email', $teacher->email);
                });
            }
        }

        // Student (Planned)
        if ($request->student_id) {
            $query->whereHas('students', function ($q) use ($request) {
                $q->where('users.id', $request->student_id);
            });
        }

        // Eager Load
        $query->with(['company', 'students', 'creator']);

        if ($request->type === 'participants') {
            $filename = 'mtg_participants_' . str_replace('-', '', $request->date_from) . '-' . str_replace('-', '', $request->date_to) . '.csv';

            $response = new StreamedResponse(function () use ($query) {
                $handle = fopen('php://output', 'w');
                fwrite($handle, "\xEF\xBB\xBF"); // BOM

                // Header
                fputcsv($handle, [
                    'zoom_meeting_id',
                    'topic',
                    'start_time',
                    'host_email',
                    'company_name',
                    'planned_student_names',
                    'matched_student_name',
                    'participant_name',
                    'participant_email',
                    'system_email',
                    'join_time',
                    'leave_time',
                    'attend_minutes',
                ]);

                // Chunking to handle large datasets
                // Use 'participants' relation which is now MeetingLogParticipant
                $query->with('participants')->chunk(50, function ($logs) use ($handle) {
                    foreach ($logs as $log) {
                        $studentsMap = $log->students->keyBy('email');
                        $plannedStudentNames = $log->students->pluck('name')->join('/');

                        foreach ($log->participants as $p) {
                            $matchedStudentName = '';
                            $student = null;

                            $systemEmail = '';

                            // Strict Matching Logic
                            if ($p->participant_email) {
                                // If email exists, match ONLY by email
                                if ($studentsMap->has($p->participant_email)) {
                                    $student = $studentsMap->get($p->participant_email);
                                }
                            } else {
                                // If email is missing, match by name (Exact match)
                                if ($p->participant_name) {
                                    $student = $log->students->firstWhere('name', $p->participant_name);
                                }
                            }

                            if ($student) {
                                $matchedStudentName = $student->name;
                                $systemEmail = $student->email;
                            }

                            fputcsv($handle, [
                                $log->zoom_meeting_id,
                                $log->title,
                                $log->started_at->format('Y-m-d H:i'),
                                $log->host_email,
                                $log->company->name ?? '',
                                $plannedStudentNames,
                                $matchedStudentName,
                                $p->participant_name,
                                $p->participant_email,
                                $systemEmail,
                                $p->join_time ? $p->join_time->format('Y-m-d H:i') : '',
                                $p->leave_time ? $p->leave_time->format('Y-m-d H:i') : '',
                                $p->attend_minutes,
                            ]);
                        }
                    }
                });
                fclose($handle);
            });

            $response->headers->set('Content-Type', 'text/csv');
            $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');
            return $response;
        }

        // Summary CSV
        $filename = 'mtg_summary_' . str_replace('-', '', $request->date_from) . '-' . str_replace('-', '', $request->date_to) . '.csv';

        $response = new StreamedResponse(function () use ($query) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF"); // BOM

            // Header
            fputcsv($handle, [
                'mtg_id',
                'zoom_meeting_id',
                'topic',
                'start_time',
                'end_time',
                'duration_minutes',
                'host_email',
                'company_name',
                'student_names',
                'zoom_participants', // New Column
                'youtube_url',
                'memo',
            ]);

            $query->with('participants')->chunk(100, function ($logs) use ($handle) {
                foreach ($logs as $log) {
                    $zoomParticipants = $log->participants->pluck('participant_name')->filter()->unique()->join(' / ');

                    fputcsv($handle, [
                        $log->id,
                        $log->zoom_meeting_id,
                        $log->title,
                        $log->started_at->format('Y-m-d H:i'),
                        $log->end_time ? $log->end_time->format('Y-m-d H:i') : '',
                        $log->duration_minutes,
                        $log->creator->email ?? '',
                        $log->company->name ?? '',
                        $log->students->pluck('name')->join('/'),
                        $zoomParticipants, // Real attendees
                        $log->youtube_url,
                        $log->memo,
                    ]);
                }
            });

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');

        return $response;
    }

    /**
     * Display the specified resource.
     */
    public function show(MeetingLog $meetingLog)
    {
        // Admin can view any log
        $meetingLog->load(['students', 'participants']);
        return view('admin.meeting_logs.show', compact('meetingLog'));
    }

    /**
     * Upload a transcript file for the meeting log.
     */
    public function uploadTranscript(Request $request, MeetingLog $meetingLog, \App\Services\SubtitleParserService $parser)
    {
        // Admin can edit any log
        $request->validate([
            'transcript_file' => 'required|file|mimes:txt,vtt,srt|max:2048',
        ]);

        $file = $request->file('transcript_file');
        $extension = $file->getClientOriginalExtension();
        $content = $file->get();

        try {
            $text = $parser->parse($content, $extension);

            $meetingLog->update([
                'transcript_text' => $text,
                'transcript_status' => 'ready',
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
        // Admin can edit any log
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

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MeetingLog $meetingLog)
    {
        // Admin can update log
        $request->validate([
            'transcript_summary' => 'nullable|string',
            // Add other fields if needed, but primarily for summary now
        ]);

        $meetingLog->update($request->only('transcript_summary'));

        return back()->with('success', 'MTGログを更新しました。');
    }
}
