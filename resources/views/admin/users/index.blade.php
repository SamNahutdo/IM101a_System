@extends('layouts.app')

@section('page_title', 'User Accounts & Access Management')

@section('content')
<div class="filter-bar">
    <form action="{{ route('admin.users.index') }}" method="GET" style="display: flex; gap: 12px; width: 100%; flex-wrap: wrap;">
        <input type="text" name="search" class="form-control" style="max-width: 280px;" placeholder="Search name, username, email..." value="{{ request('search') }}">
        
        <select name="role" class="form-select" style="max-width: 180px;">
            <option value="">All Roles</option>
            @foreach ($roles as $r)
                <option value="{{ $r->name }}" {{ request('role') === $r->name ? 'selected' : '' }}>{{ $r->display_name }}</option>
            @endforeach
        </select>

        <button type="submit" class="btn btn-secondary">Filter</button>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Reset</a>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary" style="margin-left: auto;">+ Create New User</a>
    </form>
</div>

<div class="card-table-container">
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>ID Reference</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $u)
                    <tr>
                        <td>
                            <strong>{{ $u->full_name }}</strong>
                            <div style="font-size: 0.78rem; color: var(--slate-400);">{{ $u->email }}</div>
                        </td>
                        <td><code>{{ $u->username }}</code></td>
                        <td>
                            @foreach ($u->roles as $r)
                                <span class="badge badge-secondary">{{ $r->display_name }}</span>
                            @endforeach
                        </td>
                        <td style="font-size: 0.82rem;">
                            @if ($u->athleteProfile)
                                <span>ID: {{ $u->athleteProfile->student_id }}</span>
                            @elseif ($u->coachProfile)
                                <span>EMP: {{ $u->coachProfile->employee_id }}</span>
                            @else
                                <span style="color: var(--slate-400);">—</span>
                            @endif
                        </td>
                        <td>
                            @if ($u->is_active)
                                <span class="badge badge-available">Active</span>
                            @else
                                <span class="badge badge-damaged">Inactive</span>
                            @endif
                        </td>
                        <td>
                            @if ($u->id !== auth()->id())
                                <form action="{{ route('admin.users.toggle', $u) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm {{ $u->is_active ? 'btn-danger' : 'btn-secondary' }}" data-confirm="Are you sure you want to change this user's access status?">
                                        {{ $u->is_active ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </form>
                            @else
                                <span style="font-size: 0.75rem; color: var(--slate-400);">(Current User)</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div style="padding: 16px 20px;">
        {{ $users->withQueryString()->links() }}
    </div>
</div>
@endsection
