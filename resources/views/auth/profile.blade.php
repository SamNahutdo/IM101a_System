@extends('layouts.app')

@section('page_title', 'My Profile & Account Settings')

@section('content')
<div class="grid-2">
    <!-- Account Information -->
    <div class="card-table-container" style="padding: 24px;">
        <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 20px; color: var(--slate-900);">Account Details</h3>
        
        <form action="{{ route('profile.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label class="form-label">Username</label>
                <input type="text" class="form-control" value="{{ $user->username }}" disabled style="background-color: var(--slate-100);">
            </div>

            <div class="grid-2" style="gap: 16px;">
                <div class="form-group">
                    <label class="form-label">First Name</label>
                    <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $user->first_name) }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Last Name</label>
                    <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $user->last_name) }}" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
            </div>

            <div class="form-group">
                <label class="form-label">Phone Number</label>
                <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}" placeholder="+63 900 000 0000">
            </div>

            @if ($user->athleteProfile)
                <div style="background-color: var(--slate-50); padding: 14px; border-radius: var(--radius); margin-bottom: 18px; border: 1px solid var(--slate-200);">
                    <h4 style="font-size: 0.85rem; font-weight: 700; color: var(--slate-700); margin-bottom: 6px;">Athlete Profile Info</h4>
                    <p style="font-size: 0.82rem; color: var(--slate-600);"><strong>Student ID:</strong> {{ $user->athleteProfile->student_id }}</p>
                    <p style="font-size: 0.82rem; color: var(--slate-600);"><strong>Medical Clearance:</strong> <span class="badge badge-available">{{ $user->athleteProfile->medical_clearance_status }}</span></p>
                    <p style="font-size: 0.82rem; color: var(--slate-600);"><strong>Emergency Contact:</strong> {{ $user->athleteProfile->emergency_contact_name }} ({{ $user->athleteProfile->emergency_contact_phone }})</p>
                </div>
            @endif

            @if ($user->coachProfile)
                <div style="background-color: var(--slate-50); padding: 14px; border-radius: var(--radius); margin-bottom: 18px; border: 1px solid var(--slate-200);">
                    <h4 style="font-size: 0.85rem; font-weight: 700; color: var(--slate-700); margin-bottom: 6px;">Coach Credentials</h4>
                    <p style="font-size: 0.82rem; color: var(--slate-600);"><strong>Employee ID:</strong> {{ $user->coachProfile->employee_id }}</p>
                    <p style="font-size: 0.82rem; color: var(--slate-600);"><strong>Specialization:</strong> {{ $user->coachProfile->specialization }}</p>
                    <p style="font-size: 0.82rem; color: var(--slate-600);"><strong>Department:</strong> {{ $user->coachProfile->department }}</p>
                </div>
            @endif

            <button type="submit" class="btn btn-primary">Save Profile Changes</button>
        </form>
    </div>

    <!-- Security & Password -->
    <div class="card-table-container" style="padding: 24px;">
        <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 20px; color: var(--slate-900);">Update Password</h3>
        
        <form action="{{ route('profile.password') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label class="form-label">Current Password</label>
                <input type="password" name="current_password" class="form-control" required placeholder="Enter current password">
            </div>

            <div class="form-group">
                <label class="form-label">New Password</label>
                <input type="password" name="password" class="form-control" required placeholder="Minimum 8 characters">
            </div>

            <div class="form-group">
                <label class="form-label">Confirm New Password</label>
                <input type="password" name="password_confirmation" class="form-control" required placeholder="Re-type new password">
            </div>

            <button type="submit" class="btn btn-secondary">Update Security Credentials</button>
        </form>
    </div>
</div>
@endsection
