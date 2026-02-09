<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class TeacherController extends Controller
{
    use \App\Http\Controllers\Traits\Sortable;

    public function index(Request $request)
    {
        $query = User::where('role', User::ROLE_TEACHER)->with(['assignedCompanies', 'profile']);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $this->applySorting($query, $request, ['name', 'email', 'created_at'], 'created_at', 'desc');

        $teachers = $query->paginate(20)->withQueryString();

        return view('admin.teachers.index', compact('teachers'));
    }

    public function create()
    {
        $companies = Company::all();
        return view('admin.teachers.create', compact('companies'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'company_ids' => ['array'],
            'company_ids.*' => ['exists:companies,id'],
            'years_of_experience' => ['nullable', 'integer'],
            'specialty_fields' => ['nullable', 'string'],
            'skills' => ['nullable', 'string'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => User::ROLE_TEACHER,
        ]);

        $user->profile()->create([
            'years_of_experience' => $request->years_of_experience,
            'specialty_fields' => $request->specialty_fields,
            'skills' => $request->skills,
        ]);

        if ($request->has('company_ids')) {
            $user->assignedCompanies()->sync($request->company_ids);
        }

        return redirect()->route('admin.teachers.index')->with('success', '講師を登録しました。');
    }

    public function edit(User $teacher)
    {
        $companies = Company::all();
        return view('admin.teachers.edit', compact('teacher', 'companies'));
    }

    public function update(Request $request, User $teacher)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,' . $teacher->id],
            'company_ids' => ['array'],
            'years_of_experience' => ['nullable', 'integer'],
            'specialty_fields' => ['nullable', 'string'],
            'skills' => ['nullable', 'string'],
        ]);

        $teacher->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        if ($request->filled('password')) {
            $request->validate(['password' => ['confirmed', Rules\Password::defaults()]]);
            $teacher->update(['password' => Hash::make($request->password)]);
        }

        $teacher->profile()->updateOrCreate(
            ['user_id' => $teacher->id],
            [
                'years_of_experience' => $request->years_of_experience,
                'specialty_fields' => $request->specialty_fields,
                'skills' => $request->skills,
            ]
        );

        if ($request->has('company_ids')) {
            $teacher->assignedCompanies()->sync($request->company_ids);
        }

        return redirect()->route('admin.teachers.index')->with('success', '講師情報を更新しました。');
    }

    public function destroy(User $teacher)
    {
        $teacher->delete();
        return redirect()->route('admin.teachers.index')->with('success', '講師を削除しました。');
    }
}
