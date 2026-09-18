@extends('layouts.app')

@section('page_title', 'Register New User Account')

@section('content')
<div class="card-table-container" style="max-width: 760px; margin: 0 auto; padding: 28px;">
    <h3 style="font-size: 1.15rem; font-weight: 700; margin-bottom: 24px; color: var(--slate-900);">User Credentials & Role Assignment</h3>

    <form action="{{ route('admin.users.store') }}" method="POST">
        @csrf

        <div class="grid-2" style="gap: 16px;">
            <div class="form-group">
                <label class="form-label">First Name *</label>
                <input type="text" name="first_name" class="form-control" value="{{ old('first_name') }}" required placeholder="e.g. Michael">
            </div>
            <div class="form-group">
                <label class="form-label">Last Name *</label>
                <input type="text" name="last_name" class="form-control" value="{{ old('last_name') }}" required placeholder="e.g. Jordan">
            </div>
        </div>

        <div class="grid-2" style="gap: 16px;">
            <div class="form-group">
                <label class="form-label">Username *</label>
                <input type="text" name="username" class="form-control" value="{{ old('username') }}" required placeholder="Unique username">
            </div>
            <div class="form-group">
                <label class="form-label">Email Address *</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required placeholder="user@falconsystem.edu">
            </div>
        </div>

        <div class="grid-2" style="gap: 16px;">
            <div class="form-group">
                <label class="form-label">Temporary Password *</label>
                <input type="password" name="password" class="form-control" required placeholder="Minimum 8 characters">
            </div>
            <div class="form-group">
                <label class="form-label">Phone Number</label>
                <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="+63 900 000 0000">
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">System Role Assignment *</label>
            <select name="role_id" id="roleSelect" class="form-select" required onchange="toggleRoleFields()">
                <option value="">Select an authorization role...</option>
                @foreach ($roles as $r)
                    <option value="{{ $r->id }}" data-role="{{ $r->name }}" {{ old('role_id') == $r->id ? 'selected' : '' }}>
                        {{ $r->display_name }} ({{ $r->name }})
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Dynamic Role-Specific Attributes -->
        <div id="athleteFields" style="display: none; background: var(--slate-50); padding: 18px; border-radius: var(--radius); margin-bottom: 20px; border: 1px solid var(--slate-200);">
            <h4 style="font-size: 0.85rem; font-weight: 700; color: var(--slate-700); margin-bottom: 12px;">Athlete Profile Setup</h4>
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Student ID Number</label>
                <input type="text" name="identifier" class="form-control" placeholder="e.g. 2026-00451">
            </div>
        </div>

        <div id="coachFields" style="display: none; background: var(--slate-50); padding: 18px; border-radius: var(--radius); margin-bottom: 20px; border: 1px solid var(--slate-200);">
            <h4 style="font-size: 0.85rem; font-weight: 700; color: var(--slate-700); margin-bottom: 12px;">Coach Profile Setup</h4>
            <div class="grid-2" style="gap: 16px;">
                <div class="form-group">
                    <label class="form-label">Employee ID</label>
                    <input type="text" name="identifier" class="form-control" placeholder="e.g. EMP-CCH-201">
                </div>
                <div class="form-group">
                    <label class="form-label">Sport Specialization</label>
                    <input type="text" name="specialization" class="form-control" placeholder="e.g. Track & Field, Swimming">
                </div>
            </div>
        </div>

        <div style="display: flex; gap: 12px; margin-top: 24px;">
            <button type="submit" class="btn btn-primary">Create Account</button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<script>
    function toggleRoleFields() {
        const select = document.getElementById('roleSelect');
        const selected = select.options[select.selectedIndex];
        const role = selected ? selected.getAttribute('data-role') : '';

        document.getElementById('athleteFields').style.display = (role === 'athlete') ? 'block' : 'none';
        document.getElementById('coachFields').style.display = (role === 'coach') ? 'block' : 'none';
    }
    document.addEventListener('DOMContentLoaded', toggleRoleFields);
</script>
@endsection
