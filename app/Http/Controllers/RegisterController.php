<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use App\Models\Setting;

class RegisterController extends Controller
{
    public function create()
    {
        abort_unless(Setting::bool('registration_open', true), 403, 'Registration is currently closed.');
        return view('auth.register', [
            'roles'         => UserRole::cases(),
            'approvalRoles' => collect(UserRole::cases())->filter->requiresApproval()->map->value->values()->all(),
            'companies'     => Company::where('is_active', true)->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request)
    {
        abort_unless(Setting::bool('registration_open', true), 403, 'Registration is currently closed.');
        $data = $request->validate([
            'role'     => ['required', Rule::enum(UserRole::class)],
            'username' => ['required', 'string', 'min:3', 'max:30', 'alpha_dash', 'unique:users,username'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],

            // Role-specific fields: only validated (and kept) for the matching role
            'student_number' => ['exclude_unless:role,student', 'required', 'string', 'max:30', 'exists:students,student_number', 'unique:users,student_number'],
            'company_id'     => ['exclude_unless:role,company_supervisor', 'required', 'exists:companies,id'],
            'department'     => ['exclude_unless:role,faculty_supervisor', 'required', 'string', 'max:80'],
        ], [
            'student_number.exists' => 'We could not find that student number. Please check with your OJT coordinator.',
            'student_number.unique' => 'An account already exists for that student number.',
            'company_id.required'   => 'Please choose your company.',
        ]);

        $role = UserRole::from($data['role']);

        $user = User::create([
            'username'       => $data['username'],
            'email'          => $data['email'],
            'password'       => $data['password'],      // hashed by the model cast
            'role'           => $role,
            'status' => ($role->requiresApproval() || Setting::bool('approve_students', false)) ? 'pending' : 'active',
            'student_number' => $data['student_number'] ?? null,
            'company_id'     => $data['company_id'] ?? null,
            'department'     => $data['department'] ?? null,
        ]);

        if ($user->status === 'pending') {
            return redirect(Route::has('login') ? route('login') : url('/'))
                ->with('status', 'Account created. An administrator needs to approve it before you can sign in.');
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }
}