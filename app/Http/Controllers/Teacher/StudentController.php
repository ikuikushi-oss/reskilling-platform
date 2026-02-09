<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    use \App\Http\Controllers\Traits\Sortable;

    public function index(Request $request)
    {
        $teacher = Auth::user();
        $assignedCompanyIds = $teacher->assignedCompanies()->pluck('id');

        $query = User::where('role', User::ROLE_STUDENT)
            ->whereIn('users.company_id', $assignedCompanyIds)
            ->join('companies', 'users.company_id', '=', 'companies.id')
            ->select('users.*')
            ->with('company');

        if ($request->filled('search')) {
            $query->where('users.name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('company_id')) {
            // Ensure the requested company is one of the teacher's assigned companies
            if ($assignedCompanyIds->contains($request->company_id)) {
                $query->where('users.company_id', $request->company_id);
            }
        }

        // Apply sorting
        if ($request->sort === 'company_name') {
            $direction = $request->input('direction', 'asc');
            $query->orderBy('companies.name', $direction);
        } else {
            $this->applySorting($query, $request, [
                'name',
                'email',
                'created_at',
            ], 'users.created_at', 'desc');
        }

        $students = $query->paginate(20)->withQueryString();
        // Passed for the filter dropdown
        $companies = $teacher->assignedCompanies;

        return view('teacher.students.index', compact('students', 'companies'));
    }
}
