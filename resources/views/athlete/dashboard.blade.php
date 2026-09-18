@extends('layouts.app')

@section('page_title', 'Student Athlete Equipment Portal')

@section('content')
<!-- Metric Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Active Borrowed Items</span>
            <div class="stat-icon blue">📦</div>
        </div>
        <div class="stat-value">{{ $stats['active_loans'] }}</div>
        <div class="stat-subtext">Currently in your custody</div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Overdue Alerts</span>
            <div class="stat-icon rose">⚠️</div>
        </div>
        <div class="stat-value" style="{{ $stats['overdue_loans'] > 0 ? 'color: #dc2626;' : '' }}">{{ $stats['overdue_loans'] }}</div>
        <div class="stat-subtext">{{ $stats['overdue_loans'] > 0 ? 'Urgent return needed!' : 'All returns on schedule' }}</div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Pending Requests</span>
            <div class="stat-icon purple">📋</div>
        </div>
        <div class="stat-value">{{ $stats['pending_requests'] }}</div>
        <div class="stat-subtext"><a href="{{ route('athlete.requests.index') }}">Check request status &rarr;</a></div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Team Assigned Gear</span>
            <div class="stat-icon green">🛡️</div>
        </div>
        <div class="stat-value">{{ $stats['assigned_items'] }}</div>
        <div class="stat-subtext">Season roster equipment</div>
    </div>
</div>

<div class="grid-2">
    <!-- Active Borrowed Items -->
    <div class="card-table-container">
        <div class="card-table-header">
            <h3 class="card-table-title">Currently Borrowed Gear</h3>
            <a href="{{ route('athlete.loans.index') }}" class="btn btn-secondary btn-sm">All Loans</a>
        </div>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Equipment</th>
                        <th>Checked Out</th>
                        <th>Return Deadline</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($activeLoans as $loan)
                        @foreach ($loan->items as $item)
                            <tr>
                                <td>
                                    <strong>{{ $item->equipment->name }}</strong>
                                    <div style="font-size: 0.75rem; color: var(--slate-400);">{{ $item->equipment->asset_code }}</div>
                                </td>
                                <td>{{ $loan->checkout_time->format('M j, g:i A') }}</td>
                                <td>
                                    <span style="{{ $loan->is_overdue ? 'color: #dc2626; font-weight: 700;' : '' }}">
                                        {{ $loan->expected_return_time->format('M j, g:i A') }}
                                    </span>
                                </td>
                                <td>
                                    @if ($loan->is_overdue)
                                        <span class="badge badge-damaged">OVERDUE</span>
                                    @else
                                        <span class="badge badge-borrowed">Active</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="4" style="text-align: center; color: var(--slate-400); padding: 20px;">You have no active equipment loans.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Quick Action / Equipment Request -->
    <div class="card-table-container" style="padding: 24px;">
        <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 12px; color: var(--slate-900);">Quick Athlete Actions</h3>
        <p style="font-size: 0.88rem; color: var(--slate-600); margin-bottom: 20px;">Need equipment for practice or match conditioning? Browse available gear or report defects.</p>
        
        <div style="display: flex; flex-direction: column; gap: 12px;">
            <a href="{{ route('athlete.catalog.index') }}" class="btn btn-primary" style="justify-content: flex-start; padding: 14px 18px;">
                <span style="font-size: 1.25rem; margin-right: 8px;">📦</span>
                <div>
                    <div style="font-weight: 700;">Browse Available Equipment</div>
                    <div style="font-size: 0.75rem; font-weight: 400; opacity: 0.9;">Check availability and submit borrowing reservation</div>
                </div>
            </a>

            <a href="{{ route('athlete.damage.create') }}" class="btn btn-secondary" style="justify-content: flex-start; padding: 14px 18px; border-color: var(--slate-300);">
                <span style="font-size: 1.25rem; margin-right: 8px;">⚠️</span>
                <div>
                    <div style="font-weight: 700;">Report Damaged / Defective Equipment</div>
                    <div style="font-size: 0.75rem; font-weight: 400; color: var(--slate-500);">Notify equipment staff immediately regarding safety issues</div>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection
