@extends('layouts.app')

@section('page_title', 'Equipment Lifecycle & Asset Timeline')

@section('content')
<div style="margin-bottom: 20px;">
    <a href="{{ route('admin.equipment.index') }}" class="btn btn-secondary btn-sm">← Back to Equipment Master</a>
    <a href="{{ route('admin.equipment.edit', $equipment) }}" class="btn btn-primary btn-sm" style="margin-left: 8px;">Edit Asset</a>
</div>

<!-- Header Card -->
<div class="card-table-container" style="padding: 24px;">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 16px;">
        <div>
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 6px;">
                <h2 style="font-size: 1.4rem; font-weight: 800; color: var(--slate-900);">{{ $equipment->name }}</h2>
                @php
                    $badgeClass = match($equipment->status) {
                        'Available' => 'badge-available',
                        'Borrowed' => 'badge-borrowed',
                        'Reserved' => 'badge-reserved',
                        'Maintenance' => 'badge-maintenance',
                        'Damaged', 'Lost' => 'badge-damaged',
                        default => 'badge-secondary'
                    };
                @endphp
                <span class="badge {{ $badgeClass }}">{{ $equipment->status }}</span>
            </div>
            <p style="font-size: 0.88rem; color: var(--slate-500);">
                Asset Code: <strong>{{ $equipment->asset_code }}</strong> &bull; Category: <strong>{{ $equipment->category->name }}</strong> &bull; Condition: <strong>{{ $equipment->current_condition }}</strong>
            </p>
        </div>

        <div style="text-align: right; background: var(--slate-50); padding: 12px 18px; border-radius: var(--radius); border: 1px solid var(--slate-200);">
            <div style="font-size: 0.75rem; text-transform: uppercase; color: var(--slate-500); font-weight: 700;">Purchase Valuation</div>
            <div style="font-size: 1.25rem; font-weight: 800; color: var(--slate-900);">PHP {{ number_format($equipment->purchase_cost, 2) }}</div>
            <div style="font-size: 0.75rem; color: var(--slate-500);">Acquired on {{ $equipment->purchase_date->format('M j, Y') }}</div>
        </div>
    </div>

    <div class="grid-2" style="margin-top: 20px; font-size: 0.88rem; border-top: 1px solid var(--slate-100); padding-top: 16px;">
        <div>
            <p><strong>Storage Facility:</strong> {{ $equipment->location->building }} — Room {{ $equipment->location->room }} ({{ $equipment->location->shelf_bin ?? 'Shelf / Bin' }})</p>
            <p><strong>Serial Number:</strong> {{ $equipment->serial_number ?? 'N/A' }}</p>
            <p><strong>Brand / Model:</strong> {{ $equipment->brand ?? 'Generic' }} {{ $equipment->model ?? '' }}</p>
        </div>
        <div>
            <p><strong>Asset Description:</strong></p>
            <p style="color: var(--slate-600); margin-top: 4px;">{{ $equipment->description ?? 'No specific notes recorded for this asset.' }}</p>
        </div>
    </div>
</div>

<div class="grid-2">
    <!-- Status History (Populated by DB Triggers / Stored Procedures) -->
    <div class="card-table-container">
        <div class="card-table-header">
            <h3 class="card-table-title">Database Status Transition History</h3>
        </div>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Date/Time</th>
                        <th>Transition</th>
                        <th>Changed By</th>
                        <th>Reason</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($equipment->statusHistory as $hist)
                        <tr>
                            <td style="font-size: 0.78rem;">{{ $hist->created_at->format('M j, Y g:i A') }}</td>
                            <td>
                                <code>{{ $hist->old_status }}</code> &rarr; <strong>{{ $hist->new_status }}</strong>
                            </td>
                            <td>{{ $hist->user ? $hist->user->full_name : 'System Trigger' }}</td>
                            <td style="font-size: 0.8rem;">{{ $hist->reason }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align: center; color: var(--slate-400); padding: 20px;">No status changes recorded.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Maintenance Records -->
    <div class="card-table-container">
        <div class="card-table-header">
            <h3 class="card-table-title">Maintenance & Repairs</h3>
        </div>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Scheduled</th>
                        <th>Staff</th>
                        <th>Cost</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($equipment->maintenanceRecords as $m)
                        <tr>
                            <td><strong>{{ $m->maintenance_type }}</strong></td>
                            <td style="font-size: 0.8rem;">{{ $m->scheduled_date->format('M j, Y') }}</td>
                            <td>{{ $m->staff->full_name }}</td>
                            <td>PHP {{ number_format($m->cost, 2) }}</td>
                            <td><span class="badge badge-secondary">{{ $m->status }}</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; color: var(--slate-400); padding: 20px;">No maintenance records for this asset.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
