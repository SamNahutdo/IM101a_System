<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\AuditService;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            if (!$user->is_active) {
                Auth::logout();
                return back()->withErrors(['username' => 'Your account is deactivated. Please contact an administrator.']);
            }

            AuditService::log('LOGIN', 'users', $user->id, null, ['status' => 'success']);

            return $this->redirectBasedOnRole($user);
        }

        return back()->withErrors([
            'username' => 'Invalid credentials provided. Check your username and password.',
        ])->onlyInput('username');
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            AuditService::log('LOGOUT', 'users', Auth::id(), null, ['status' => 'logged_out']);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'You have been securely logged out.');
    }

    protected function redirectBasedOnRole($user)
    {
        $primaryRole = $user->getPrimaryRole();
        $roleName = $primaryRole ? $primaryRole->name : 'athlete';

        return match ($roleName) {
            'admin' => redirect()->route('admin.dashboard'),
            'staff' => redirect()->route('staff.dashboard'),
            'coach' => redirect()->route('coach.dashboard'),
            'athlete' => redirect()->route('athlete.dashboard'),
            default => redirect()->route('athlete.dashboard'),
        };
    }
}
