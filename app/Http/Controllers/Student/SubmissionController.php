<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\LecturePage;
use App\Models\Submission;
use App\Models\SubmissionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SubmissionController extends Controller
{
    public function index()
    {
        $submissions = Submission::where('user_id', Auth::id())
            ->with(['lecturePage', 'items'])
            ->latest()
            ->get();

        return view('student.submissions.index', compact('submissions'));
    }

    public function store(Request $request, LecturePage $lecturePage)
    {
        $request->validate([
            'files.*' => 'required|file|max:102400', // 100MB max per file
        ]);

        // Create Submission
        $submission = Submission::create([
            'user_id' => Auth::id(),
            'lecture_page_id' => $lecturePage->id,
            'status' => Submission::STATUS_SUBMITTED,
        ]);

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                // Determine type
                $type = str_starts_with($file->getMimeType(), 'image/') ? 'image' : 'file';

                $path = $file->store('submissions', 'public');

                SubmissionItem::create([
                    'submission_id' => $submission->id,
                    'file_path' => $path,
                    'file_type' => $type,
                    'original_name' => $file->getClientOriginalName(),
                ]);
            }
        }

        return redirect()->back()->with('success', '課題を提出しました。');
    }
}
