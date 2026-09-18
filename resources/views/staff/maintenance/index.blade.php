@extends('layouts.app')

@section('page_title', 'Equipment Maintenance & Repair Orders')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <p style="color: var(--slate-600); font-size: 0.9rem;">Manage repair work orders, track servicing expenditures, and restore equipment condition.</p>
    <button type="button" class="btn btn-primary" onclick="openModal('scheduleModal')">+ Schedule Maintenance Order</button>
</div>

<div class="card-table-container">
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Equipment Asset</th>
                    <th>Maintenance Type</th>
                    <th>Scheduled Date</th>
                    <th>Assigned Staff</th>
                    <th>Repair Cost</th>
                    <th>Status</th>
                    <th>Update Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($records as $rec)
                    <tr>
                        <td>
                            <strong>{{ $rec->equipment->name }}</strong>
                            <div><code>{{ $rec->equipment->asset_code }}</code> &bull; <span class="badge badge-secondary">{{ $rec->equipment->category->name }}</span></div>
                        </td>
                        <td>
                            <strong>{{ $rec->maintenance_type }}</strong>
                            <div style="font-size: 0.78rem; color: var(--slate-500);">{{ Str::limit($rec->description, 50) }}</div>
                        </td>
                        <td>{{ $rec->scheduled_date->format('M j, Y') }}</td>
                        <td>{{ $rec->staff->full_name }}</td>
                        <td><strong>PHP {{ number_format($rec->cost, 2) }}</strong></td>
                        <td>
                            @php
                                $badge = match($rec->status) {
                                    'Completed' => 'badge-available',
                                    'In Progress' => 'badge-borrowed',
                                    'Scheduled' => 'badge-maintenance',
                                    default => 'badge-secondary'
                                };
                            @endphp
                            <span class="badge {{ $badge }}">{{ $rec->status }}</span>
                        </td>
                        <td>
                            @if ($rec->status !== 'Completed')
                                <form action="{{ route('staff.maintenance.status', $rec) }}" method="POST" style="display: flex; gap: 6px; align-items: center;">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" class="form-select" style="padding: 4px 8px; font-size: 0.8rem; width: auto;">
                                        @if ($rec->status === 'Scheduled')
                                            <option value="In Progress">Start (In Progress)</option>
                                        @endif
                                        <option value="Completed">Mark Completed</option>
                                        <option value="Cancelled">Cancel Order</option>
                                    </select>
                                    <button type="submit" class="btn btn-secondary btn-sm">Update</button>
                                </form>
                            @else
                                <span style="font-size: 0.8rem; color: var(--slate-400);">Completed {{ $rec->completion_date?->format('M j, Y') }}</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; color: var(--slate-400); padding: 24px;">No maintenance work orders found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding: 16px 20px;">
        {{ $records->links() }}
    </div>
</div>

<!-- Modal to Schedule New Maintenance -->
<div id="scheduleModal" class="modal-backdrop" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 100; align-items: center; justify-content: center;">
    <div style="background: #fff; border-radius: var(--radius-lg); width: 100%; max-width: 600px; padding: 28px; box-shadow: var(--shadow-lg);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="font-size: 1.15rem; font-weight: 700;">Schedule Maintenance Order</h3>
            <button type="button" onclick="closeModal('scheduleModal')" style="border: none; background: none; font-size: 1.25rem; cursor: pointer;">&times;</button>
        </div>

        <form action="{{ route('staff.maintenance.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Equipment Asset *</label>
                <select name="equipment_id" class="form-select" required>
                    <option value="">Select Equipment...</option>
                    @foreach ($equipmentList as $eq)
                        <option value="{{ $eq->id }}">{{ $eq->name }} ({{ $eq->asset_code }}) — {{ $eq->status }}</option>
                    @endforeach
                </select>
            </div>

            @if ($damageReportsNeedingRepair->count() > 0)
                <div class="form-group">
                    <label class="form-label">Linked Damage Report (Optional)</label>
                    <select name="damage_report_id" class="form-select">
                        <option value="">None (Routine preventative maintenance)</option>
                        @foreach ($damageReportsNeedingRepair as $dmg)
                            <option value="{{ $dmg->id }}">{{ $dmg->report_code }}: {{ $dmg->damage_type }} ({{ $dmg->severity }})</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="grid-2" style="gap: 16px;">
                <div class="form-group">
                    <label class="form-label">Maintenance / Service Type *</label>
                    <input type="text" name="maintenance_type" class="form-control" required placeholder="e.g. Patch, Stitch, Calibration">
                </div>
                <div class="form-group">
                    <label class="form-label">Scheduled Date *</label>
                    <input type="date" name="scheduled_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Estimated / Actual Cost (PHP) *</label>
                <input type="number" step="0.01" min="0" name="cost" class="form-control" value="0.00" required>
            </div>

            <div class="form-group">
                <label class="form-label">Description of Service Required *</label>
                <textarea name="description" class="form-control" rows="3" required placeholder="Scope of maintenance work to be performed..."></textarea>
            </div>

            <div style="display: flex; gap: 12px; margin-top: 24px;">
                <button type="submit" class="btn btn-primary">Schedule Work Order</button>
                <button type="button" class="btn btn-secondary" onclick="closeModal('scheduleModal')">Cancel</button>
            </div>
        </form>
    </div>
</div>
@endsection
