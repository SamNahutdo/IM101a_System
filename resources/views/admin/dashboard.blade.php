@extends('layouts.app')

@section('page_title', 'System Administrator Dashboard')

@section('content')
<!-- Metric Summary Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Total Equipment</span>
            <div class="stat-icon blue">📦</div>
        </div>
        <div class="stat-value">{{ $stats['total_equipment'] }}</div>
        <div class="stat-subtext">Catalogued assets in system</div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Available for Loan</span>
            <div class="stat-icon green">✅</div>
        </div>
        <div class="stat-value">{{ $stats['available_equipment'] }}</div>
        <div class="stat-subtext">Ready for checkout/reservation</div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Currently Borrowed</span>
            <div class="stat-icon blue">🏃</div>
        </div>
        <div class="stat-value">{{ $stats['borrowed_equipment'] }}</div>
        <div class="stat-subtext">Active student/coach loans</div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Maintenance / Repair</span>
            <div class="stat-icon amber">🛠️</div>
        </div>
        <div class="stat-value">{{ $stats['maintenance_equipment'] + $stats['damaged_equipment'] }}</div>
        <div class="stat-subtext">{{ $stats['damaged_equipment'] }} damaged, {{ $stats['maintenance_equipment'] }} in maintenance</div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Overdue Returns</span>
            <div class="stat-icon rose">⚠️</div>
        </div>
        <div class="stat-value" style="color: #dc2626;">{{ $stats['overdue_count'] }}</div>
        <div class="stat-subtext">Past expected return time</div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Pending Reservations</span>
            <div class="stat-icon purple">📋</div>
        </div>
        <div class="stat-value">{{ $stats['pending_reservations'] }}</div>
        <div class="stat-subtext">Awaiting staff review</div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Active System Users</span>
            <div class="stat-icon blue">👥</div>
        </div>
        <div class="stat-value">{{ $stats['active_users'] }}</div>
        <div class="stat-subtext">Admins, staff, coaches, athletes</div>
    </div>
</div>

<div class="grid-2">
    <!-- Recent Borrowing Activity -->
    <div class="card-table-container">
        <div class="card-table-header">
            <h3 class="card-table-title">Recent Borrowing Activity</h3>
            <a href="{{ route('admin.reports.index') }}" class="btn btn-secondary btn-sm">Full Report</a>
        </div>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Borrower</th>
                        <th>Checked Out</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recentBorrowings as $b)
                        <tr>
                            <td><strong>{{ $b->transaction_code }}</strong></td>
                            <td>{{ $b->borrower->full_name }}</td>
                            <td>{{ $b->checkout_time->format('M j, g:i A') }}</td>
                            <td>
                                @if ($b->is_overdue)
                                    <span class="badge badge-overdue">OVERDUE</span>
                                @elseif ($b->status === 'Active')
                                    <span class="badge badge-borrowed">Active</span>
                                @else
                                    <span class="badge badge-completed">{{ $b->status }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align: center; color: var(--slate-400); padding: 24px;">No borrowing activity recorded.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Damage Reports -->
    <div class="card-table-container">
        <div class="card-table-header">
            <h3 class="card-table-title">Recent Damage Reports</h3>
            <a href="{{ route('admin.equipment.index', ['status' => 'Damaged']) }}" class="btn btn-secondary btn-sm">View Damaged</a>
        </div>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Equipment</th>
                        <th>Severity</th>
                        <th>Reported By</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recentDamageReports as $d)
                        <tr>
                            <td>
                                <strong>{{ $d->equipment->name }}</strong>
                                <div style="font-size: 0.75rem; color: var(--slate-400);">{{ $d->equipment->asset_code }}</div>
                            </td>
                            <td>
                                <span class="badge {{ $d->severity === 'Severe' ? 'badge-damaged' : 'badge-maintenance' }}">
                                    {{ $d->severity }}
                                </span>
                            </td>
                            <td>{{ $d->reporter->full_name }}</td>
                            <td><span class="badge badge-secondary">{{ $d->status }}</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align: center; color: var(--slate-400); padding: 24px;">No active damage reports.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Database Audit Activity Trail -->
<div class="card-table-container">
    <div class="card-table-header">
        <h3 class="card-table-title">Recent Database Audit Trail (MySQL DB Trigger / App Events)</h3>
        <a href="{{ route('admin.audit.index') }}" class="btn btn-secondary btn-sm">View All Logs</a>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Timestamp</th>
                    <th>Action</th>
                    <th>Table Affected</th>
                    <th>Record ID</th>
                    <th>Triggered By</th>
                    <th>IP Address</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($recentAudits as $log)
                    <tr>
                        <td style="font-size: 0.8rem; color: var(--slate-500);">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                        <td>
                            <span class="badge {{ $log->action === 'INSERT' ? 'badge-available' : ($log->action === 'UPDATE' ? 'badge-borrowed' : 'badge-damaged') }}">
                                {{ $log->action }}
                            </span>
                        </td>
                        <td><code>{{ $log->table_name }}</code></td>
                        <td>#{{ $log->record_id }}</td>
                        <td>{{ $log->user ? $log->user->full_name : 'MySQL Trigger / System' }}</td>
                        <td style="font-size: 0.78rem;">{{ $log->ip_address ?? '127.0.0.1' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; color: var(--slate-400); padding: 24px;">No audit events found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
