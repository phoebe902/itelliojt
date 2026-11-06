<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function create()
    {
        return view('auth.login', [
            'roles' => UserRole::cases(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'role'     => ['required', Rule::enum(UserRole::class)],
            'login'    => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        // People can sign in with either their email or their username
        $field = filter_var($data['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $user = User::where($field, $data['login'])
            ->where('role', $data['role'])
            ->first();

        // Same message for "no such user", "wrong password" and "wrong user type"
        if (! $user || ! Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'login' => 'These credentials do not match the selected user type.',
            ]);
        }

        if ($user->status !== 'active') {
            throw ValidationException::withMessages([
                'login' => $user->status === 'pending'
                    ? 'Your account is awaiting administrator approval.'
                    : 'This account is not active. Please contact an administrator.',
            ]);
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function destroy(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}