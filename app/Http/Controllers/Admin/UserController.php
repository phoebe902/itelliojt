<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    private const STATUSES = ['pending', 'active', 'suspended'];

    public function index(Request $request)
    {
        $status = in_array($request->query('status'), self::STATUSES, true) ? $request->query('status') : null;
        $role   = UserRole::tryFrom((string) $request->query('role'));
        $search = trim((string) $request->query('search'));

        $users = User::query()
            ->with('company')
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($role, fn ($q) => $q->where('role', $role->value))
            ->when($search, fn ($q) => $q->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            }))
            ->orderByRaw("CASE status WHEN 'pending' THEN 0 WHEN 'active' THEN 1 ELSE 2 END")   // pending first
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        return view('admin.users.index', [
            'users'     => $users,
            'counts'    => User::selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status'),
            'roles'     => UserRole::cases(),
            'companies' => Company::where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'status'    => $status,
            'role'      => $role,
            'search'    => $search,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validateWithBag('createUser', [
            'first_name' => ['required', 'string', 'max:60'],
            'last_name'  => ['required', 'string', 'max:60'],
            'email'      => ['required', 'email', 'max:255', 'unique:users,email'],
            'role'       => ['required', Rule::enum(UserRole::class)],
            'password'   => ['required', Password::defaults()],

            'student_number' => ['exclude_unless:role,student', 'nullable', 'string', 'max:30', 'unique:users,student_number'],
            'company_id'     => ['exclude_unless:role,company_supervisor', 'nullable', 'exists:companies,id'],
            'department'     => ['exclude_unless:role,faculty_supervisor', 'nullable', 'string', 'max:80'],
        ]);

        $user = User::create([
            'username'       => $this->makeUsername($data['email']),
            'first_name'     => $data['first_name'],
            'last_name'      => $data['last_name'],
            'email'          => $data['email'],
            'password'       => $data['password'],   // hashed by the model cast
            'role'           => $data['role'],
            'status'         => 'active',            // created by an administrator, so no approval needed
            'student_number' => $data['student_number'] ?? null,
            'company_id'     => $data['company_id'] ?? null,
            'department'     => $data['department'] ?? null,
        ]);

        return back()->with('status', "Account created for {$user->full_name}. Their username is {$user->username}.");
    }

    public function update(Request $request, User $user)
    {
        if ($user->is($request->user())) {
            return back()->with('error', 'You cannot change your own role or status.');
        }

        $data = $request->validate([
            'status' => ['sometimes', Rule::in(self::STATUSES)],
            'role'   => ['sometimes', Rule::enum(UserRole::class)],
        ]);

        $user->update($data);

        $message = isset($data['role'])
            ? "{$user->full_name} is now " . UserRole::from($data['role'])->label() . '.'
            : match ($data['status'] ?? null) {
                'active'    => "{$user->full_name}'s account is active.",
                'suspended' => "{$user->full_name}'s account was suspended.",
                default     => 'Account updated.',
            };

        return back()->with('status', $message);
    }

    public function destroy(Request $request, User $user)
    {
        if ($user->is($request->user())) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return back()->with('status', "{$user->full_name}'s account was deleted.");
    }

    /** "febb.edano@nmsc.edu.ph" => "febb.edano" (then febb.edano2, febb.edano3 ... if taken) */
    private function makeUsername(string $email): string
    {
        $base = Str::of($email)->before('@')->lower()
            ->replaceMatches('/[^a-z0-9_.-]/', '')
            ->limit(24, '')
            ->toString() ?: 'user';

        $username = $base;
        $i = 1;

        while (User::where('username', $username)->exists()) {
            $username = $base . ++$i;
        }

        return $username;
    }
}