@extends('layouts.app')

@section('page_title', 'Equipment Staff Operations Portal')

@section('content')
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Active Loans</span>
            <div class="stat-icon blue">📦</div>
        </div>
        <div class="stat-value">{{ $stats['active_checkouts'] }}</div>
        <div class="stat-subtext">Currently out in field</div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Pending Reservations</span>
            <div class="stat-icon purple">📋</div>
        </div>
        <div class="stat-value">{{ $stats['pending_reservations'] }}</div>
        <div class="stat-subtext"><a href="{{ route('staff.reservations.index') }}">Review queue &rarr;</a></div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Overdue Items</span>
            <div class="stat-icon rose">⚠️</div>
        </div>
        <div class="stat-value" style="color: #dc2626;">{{ $stats['overdue_count'] }}</div>
        <div class="stat-subtext">Needs follow-up with borrower</div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Active Repairs</span>
            <div class="stat-icon amber">🛠️</div>
        </div>
        <div class="stat-value">{{ $stats['active_maintenance'] }}</div>
        <div class="stat-subtext"><a href="{{ route('staff.maintenance.index') }}">Maintenance orders &rarr;</a></div>
    </div>
</div>

<div class="grid-2">
    <!-- Pending Reservations Queue -->
    <div class="card-table-container">
        <div class="card-table-header">
            <h3 class="card-table-title">Pending Reservation Requests</h3>
            <a href="{{ route('staff.reservations.index') }}" class="btn btn-secondary btn-sm">View All</a>
        </div>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Requester</th>
                        <th>Start Time</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pendingReservations as $res)
                        <tr>
                            <td><strong>{{ $res->reservation_code }}</strong></td>
                            <td>{{ $res->requester->full_name }}</td>
                            <td>{{ $res->start_time->format('M j, g:i A') }}</td>
                            <td>
                                <a href="{{ route('staff.reservations.show', $res) }}" class="btn btn-cyan btn-sm">Review</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align: center; color: var(--slate-400); padding: 20px;">No pending reservation requests.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Active Borrowing Transactions -->
    <div class="card-table-container">
        <div class="card-table-header">
            <h3 class="card-table-title">Current Active Loans</h3>
            <a href="{{ route('staff.returns.index') }}" class="btn btn-primary btn-sm">Process Return</a>
        </div>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Borrower</th>
                        <th>Expected Return</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($activeBorrowings as $ab)
                        <tr>
                            <td><strong>{{ $ab->transaction_code }}</strong></td>
                            <td>{{ $ab->borrower->full_name }}</td>
                            <td>{{ $ab->expected_return_time->format('M j, g:i A') }}</td>
                            <td>
                                @if ($ab->is_overdue)
                                    <span class="badge badge-damaged">OVERDUE</span>
                                @else
                                    <span class="badge badge-borrowed">Active</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align: center; color: var(--slate-400); padding: 20px;">No active borrowings currently.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
