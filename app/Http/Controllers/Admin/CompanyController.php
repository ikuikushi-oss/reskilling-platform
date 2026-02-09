<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    use \App\Http\Controllers\Traits\Sortable;

    public function index(Request $request)
    {
        // Eager load single teacher instead of teachers list
        $query = Company::with(['teacher', 'students'])->withCount('students');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('teacher_id')) {
            $query->where('teacher_id', $request->teacher_id);
        }

        $this->applySorting($query, $request, ['name', 'status', 'contract_start_date'], 'created_at', 'desc');

        $companies = $query->paginate(20)->withQueryString();
        $teachers = User::where('role', User::ROLE_TEACHER)->get();

        return view('admin.companies.index', compact('companies', 'teachers'));
    }

    public function create()
    {
        $teachers = User::where('role', User::ROLE_TEACHER)->get();
        return view('admin.companies.create', compact('teachers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'business_description' => 'nullable|string',
            'status' => 'required|in:free_trial,active,finished',
            'contract_start_date' => 'nullable|date',
            'teacher_id' => 'nullable|exists:users,id',
        ]);

        Company::create($validated);

        return redirect()->route('admin.companies.index')->with('success', '企業を登録しました。');
    }

    public function edit(Company $company)
    {
        $teachers = User::where('role', User::ROLE_TEACHER)->get();
        return view('admin.companies.edit', compact('company', 'teachers'));
    }

    public function update(Request $request, Company $company)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'business_description' => 'nullable|string',
            'status' => 'required|in:free_trial,active,finished',
            'contract_start_date' => 'nullable|date',
            'teacher_id' => 'nullable|exists:users,id',
        ]);

        $company->update($validated);

        return redirect()->route('admin.companies.index')->with('success', '企業情報を更新しました。');
    }

    public function destroy(Company $company)
    {
        $company->delete();
        return redirect()->route('admin.companies.index')->with('success', '企業を削除しました。');
    }
}
