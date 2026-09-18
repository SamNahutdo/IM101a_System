<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Services\AuditService;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user()->load(['roles', 'athleteProfile.teams', 'coachProfile.teams']);
        return view('auth.profile', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:60'],
            'last_name' => ['required', 'string', 'max:60'],
            'email' => ['required', 'email', 'max:100', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        $old = $user->only(['first_name', 'last_name', 'email', 'phone']);
        $user->update($validated);

        AuditService::log('UPDATE', 'users', $user->id, $old, $validated);

        return back()->with('success', 'Profile information updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = Auth::user();
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        AuditService::log('PASSWORD_CHANGE', 'users', $user->id, null, ['status' => 'password_updated']);

        return back()->with('success', 'Password successfully changed.');
    }
}
