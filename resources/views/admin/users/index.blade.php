@extends('layouts.app')

@section('title', 'Users & Roles')
@section('page_title', 'System User Accounts & Roles (RBAC)')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
        <div>
            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-people me-2 text-primary"></i>User Accounts & Clearance</h6>
            <small class="text-muted">4 Assigned Roles: Admin, Staff, Coach, Student</small>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Username</th>
                    <th>Email Address</th>
                    <th>Assigned Role</th>
                    <th>Linked Profile</th>
                    <th>Status</th>
                    <th>Created</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $u)
                    <tr>
                        <td class="fw-bold">#{{ $u->id }}</td>
                        <td>
                            <strong>{{ $u->username }}</strong>
                        </td>
                        <td>{{ $u->email }}</td>
                        <td>
                            @php
                                $rName = strtolower($u->role->name ?? '');
                            @endphp
                            <span class="badge {{ $rName === 'admin' ? 'bg-danger' : ($rName === 'staff' ? 'bg-success' : ($rName === 'coach' ? 'bg-warning text-dark' : 'bg-primary')) }}">
                                {{ strtoupper($u->role->name ?? 'User') }}
                            </span>
                        </td>
                        <td>
                            @if ($u->athlete)
                                <span>{{ $u->athlete->fullName }} ({{ $u->athlete->student_number }})</span>
                            @elseif ($u->coach)
                                <span>Coach {{ $u->coach->fullName }} ({{ $u->coach->employee_number }})</span>
                            @else
                                <span class="text-muted">Administrator / Custodian</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-success">{{ $u->status }}</span>
                        </td>
                        <td>{{ $u->created_at ? $u->created_at->format('M d, Y') : 'N/A' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if ($users->hasPages())
        <div class="card-footer bg-white py-2">
            {{ $users->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection
