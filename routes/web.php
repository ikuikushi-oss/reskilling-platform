<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\RoleMiddleware;
use App\Models\User;

Route::get('/', function () {
    return view('welcome');
});

// Explicit Logout Route (GET) to preventing 419 errors
Route::get('/logout', [App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'destroy'])->name('logout.get');

Route::get('/dashboard', function () {
    // Redirect based on role
    $user = auth()->user();
    if ($user->isAdmin())
        return redirect()->route('admin.dashboard');
    if ($user->isTeacher())
        return redirect()->route('teacher.dashboard');
    if ($user->isStudent())
        return redirect()->route('student.dashboard');

    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin Routes
Route::middleware(['auth', RoleMiddleware::class . ':' . User::ROLE_ADMIN])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/companies/export', [App\Http\Controllers\Admin\CompanyExportController::class, 'index'])->name('companies.export');
        Route::get('/companies/export/csv', [App\Http\Controllers\Admin\CompanyExportController::class, 'export'])->name('companies.export.csv');

        Route::resource('companies', App\Http\Controllers\Admin\CompanyController::class);
        Route::resource('teachers', App\Http\Controllers\Admin\TeacherController::class);
        Route::resource('students', App\Http\Controllers\Admin\StudentController::class);
        Route::resource('students', App\Http\Controllers\Admin\StudentController::class);

        // Settings & Lecture Pages (moved to settings basically)
        Route::get('/settings', [App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('settings.index');
        Route::patch('/settings/profile', [App\Http\Controllers\Admin\SettingsController::class, 'updateProfile'])->name('settings.update-profile');

        // Zoom Settings
        Route::get('/zoom-settings', [App\Http\Controllers\Admin\ZoomSettingController::class, 'edit'])->name('zoom-settings.edit');
        Route::post('/zoom-settings/test', [App\Http\Controllers\Admin\ZoomSettingController::class, 'testConnection'])->name('zoom-settings.test');

        Route::post('/zoom/sync-mtgs', [App\Http\Controllers\Admin\ZoomSyncController::class, 'sync'])->name('zoom.sync');

        Route::get('/mtgs/exports', [App\Http\Controllers\Admin\MeetingLogController::class, 'export'])->name('mtgs.export');
        Route::get('/mtgs/exports.csv', [App\Http\Controllers\Admin\MeetingLogController::class, 'downloadCsv'])->name('mtgs.export.csv');
        Route::get('/meeting-logs', [App\Http\Controllers\Admin\MeetingLogController::class, 'index'])->name('meeting-logs.index');
        // Admin MTG Log Detail/Transcript
        Route::get('/meeting-logs/{meetingLog}', [App\Http\Controllers\Admin\MeetingLogController::class, 'show'])->name('meeting-logs.show');
        Route::post('/meeting-logs/{meetingLog}/transcript', [App\Http\Controllers\Admin\MeetingLogController::class, 'uploadTranscript'])->name('meeting-logs.transcript.upload');
        Route::post('/meeting-logs/{meetingLog}/summarize', [App\Http\Controllers\Admin\MeetingLogController::class, 'summarize'])->name('meeting-logs.summarize');
        Route::post('/meeting-logs/{meetingLog}', [App\Http\Controllers\Admin\MeetingLogController::class, 'update'])->name('meeting-logs.update');

        Route::resource('meetings', App\Http\Controllers\Admin\MeetingController::class)->only(['index']);
        Route::patch('/lecture-pages/{lecturePage}/deactivate', [App\Http\Controllers\Admin\LecturePageController::class, 'deactivate'])->name('lecture-pages.deactivate');
        Route::resource('lecture-pages', App\Http\Controllers\Admin\LecturePageController::class);
    });

// Teacher Routes
Route::middleware(['auth', RoleMiddleware::class . ':' . User::ROLE_TEACHER])
    ->prefix('teacher')
    ->name('teacher.')
    ->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Teacher\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/companies', [App\Http\Controllers\Teacher\CompanyController::class, 'index'])->name('companies.index');
        Route::get('/companies/{company}', [App\Http\Controllers\Teacher\CompanyController::class, 'show'])->name('companies.show');
        Route::get('/students', [App\Http\Controllers\Teacher\StudentController::class, 'index'])->name('students.index');

        // Review specific routes
        Route::get('/submissions', [App\Http\Controllers\Teacher\SubmissionController::class, 'index'])->name('submissions.index');
        Route::get('/submissions/{submission}', [App\Http\Controllers\Teacher\SubmissionController::class, 'show'])->name('submissions.show');
        Route::post('/submissions/{submission}/review', [App\Http\Controllers\Teacher\SubmissionController::class, 'review'])->name('submissions.review');

        // Meetings
        Route::resource('meetings', App\Http\Controllers\Teacher\MeetingController::class);

        // Meeting Logs
        Route::get('/meeting-logs/create', [App\Http\Controllers\Teacher\MeetingLogController::class, 'create'])->name('meeting-logs.create');
        Route::post('/meeting-logs', [App\Http\Controllers\Teacher\MeetingLogController::class, 'store'])->name('meeting-logs.store');
        Route::get('/meeting-logs/{meetingLog}/edit', [App\Http\Controllers\Teacher\MeetingLogController::class, 'edit'])->name('meeting-logs.edit');
        Route::patch('/meeting-logs/{meetingLog}', [App\Http\Controllers\Teacher\MeetingLogController::class, 'update'])->name('meeting-logs.update');
        Route::get('/students/{student}/meeting-logs/create', [App\Http\Controllers\Teacher\MeetingLogController::class, 'create'])->name('students.meeting-logs.create');
        Route::get('/students/{student}/meeting-logs', [App\Http\Controllers\Teacher\MeetingLogController::class, 'index'])->name('students.meeting-logs.index');
        Route::get('/students/{student}/mtgs', [App\Http\Controllers\Teacher\MeetingLogController::class, 'hub'])->name('students.mtgs');

        // MTG Log Detail & Transcript
        Route::get('/meeting-logs/{meetingLog}', [App\Http\Controllers\Teacher\MeetingLogController::class, 'show'])->name('meeting-logs.show');
        Route::post('/meeting-logs/{meetingLog}/transcript', [App\Http\Controllers\Teacher\MeetingLogController::class, 'uploadTranscript'])->name('meeting-logs.transcript.upload');
        Route::post('/meeting-logs/{meetingLog}/summarize', [App\Http\Controllers\Teacher\MeetingLogController::class, 'summarize'])->name('meeting-logs.summarize');
    });

// Student Routes
Route::middleware(['auth', RoleMiddleware::class . ':' . User::ROLE_STUDENT])
    ->prefix('student')
    ->name('student.')
    ->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Student\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/lectures/{lecturePage}', [App\Http\Controllers\Student\LectureController::class, 'show'])->name('lectures.show');
        Route::post('/lectures/{lecturePage}/submit', [App\Http\Controllers\Student\SubmissionController::class, 'store'])->name('submissions.store');
        Route::get('/my-submissions', [App\Http\Controllers\Student\SubmissionController::class, 'index'])->name('submissions.index');

        // Meetings
        Route::resource('meetings', App\Http\Controllers\Student\MeetingController::class)->only(['index', 'show']);
        Route::get('/meeting-logs/{meetingLog}', [App\Http\Controllers\Student\MeetingController::class, 'showLog'])->name('meeting-logs.show');
    });

require __DIR__ . '/auth.php';
