<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // KPIs
        $teacherCount = \App\Models\User::where('role', \App\Models\User::ROLE_TEACHER)->count();
        $companyTotal = \App\Models\Company::count();
        $companyActive = \App\Models\Company::where('status', \App\Models\Company::STATUS_ACTIVE)->count();
        $companyFinished = \App\Models\Company::where('status', \App\Models\Company::STATUS_FINISHED)->count();
        // $companyFreeTrial = \App\Models\Company::where('status', \App\Models\Company::STATUS_FREE_TRIAL)->count(); // If needed later

        // Scheduled MTG (Future)
        $upcomingMeetings = \App\Models\Meeting::with(['company', 'lecturePage'])
            ->where('scheduled_at', '>=', now())
            ->orderBy('scheduled_at', 'asc')
            ->take(5)
            ->get();

        // MTG History (Recent Logs)
        $recentLogs = \App\Models\MeetingLog::with(['company', 'creator'])
            ->orderBy('started_at', 'desc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('teacherCount', 'companyTotal', 'companyActive', 'companyFinished', 'upcomingMeetings', 'recentLogs'));
    }
}
