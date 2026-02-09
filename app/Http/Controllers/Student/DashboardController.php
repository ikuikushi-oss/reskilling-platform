<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\LecturePage;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $lecturePages = LecturePage::active()->where('sort_order', '>', 0)->orderBy('sort_order')->get();
        return view('student.dashboard', compact('lecturePages'));
    }
}
