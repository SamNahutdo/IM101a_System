<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\AthleteProfile;
use App\Models\CoachProfile;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['roles', 'athleteProfile', 'coachProfile']);

        if ($request->filled('role')) {
            $query->whereHas('roles', fn ($q) => $q->where('name', $request->role));
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('username', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%")
                  ->orWhere('first_name', 'like', "%{$s}%")
                  ->orWhere('last_name', 'like', "%{$s}%");
            });
        }

        $users = $query->latest()->paginate(15);
        $roles = Role::all();

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:50', 'unique:users'],
            'email' => ['required', 'email', 'max:100', 'unique:users'],
            'password' => ['required', Password::min(8)],
            'first_name' => ['required', 'string', 'max:60'],
            'last_name' => ['required', 'string', 'max:60'],
            'phone' => ['nullable', 'string', 'max:20'],
            'role_id' => ['required', 'exists:roles,id'],
            'identifier' => ['nullable', 'string', 'max:30'], // Student ID or Employee ID
            'specialization' => ['nullable', 'string', 'max:100'],
        ]);

        $user = User::create([
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'phone' => $validated['phone'],
            'is_active' => true,
        ]);

        $user->roles()->attach($validated['role_id'], ['is_primary' => true]);

        $role = Role::find($validated['role_id']);
        if ($role->name === 'athlete') {
            AthleteProfile::create([
                'user_id' => $user->id,
                'student_id' => $validated['identifier'] ?? ('STU-' . date('Y') . '-' . rand(1000, 9999)),
                'emergency_contact_name' => 'Department Records',
                'emergency_contact_phone' => '+63 900 000 0000',
                'medical_clearance_status' => 'Pending',
                'year_level' => 1,
            ]);
        } elseif ($role->name === 'coach') {
            CoachProfile::create([
                'user_id' => $user->id,
                'employee_id' => $validated['identifier'] ?? ('EMP-' . rand(100, 999)),
                'specialization' => $validated['specialization'] ?? 'Athletic Training',
                'department' => 'Athletics & Physical Education',
            ]);
        }

        AuditService::log('INSERT', 'users', $user->id, null, $user->toArray());

        return redirect()->route('admin.users.index')->with('success', "User account {$user->username} created successfully.");
    }

    public function toggleStatus(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot deactivate your own administrative account.');
        }

        $old = $user->is_active;
        $user->update(['is_active' => !$old]);

        AuditService::log('UPDATE', 'users', $user->id, ['is_active' => $old], ['is_active' => !$old]);

        $msg = $user->is_active ? "User account {$user->username} activated." : "User account {$user->username} deactivated.";
        return back()->with('success', $msg);
    }
}
