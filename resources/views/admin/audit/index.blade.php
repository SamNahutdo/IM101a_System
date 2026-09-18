@extends('layouts.app')

@section('page_title', 'Database-Level Audit Logs & History')

@section('content')
<div class="filter-bar">
    <form action="{{ route('admin.audit.index') }}" method="GET" style="display: flex; gap: 12px; width: 100%; flex-wrap: wrap;">
        <select name="table_name" class="form-select" style="max-width: 200px;">
            <option value="">All Database Tables</option>
            @foreach ($tables as $t)
                <option value="{{ $t }}" {{ request('table_name') === $t ? 'selected' : '' }}>{{ $t }}</option>
            @endforeach
        </select>

        <select name="action" class="form-select" style="max-width: 140px;">
            <option value="">All Actions</option>
            <option value="INSERT" {{ request('action') === 'INSERT' ? 'selected' : '' }}>INSERT</option>
            <option value="UPDATE" {{ request('action') === 'UPDATE' ? 'selected' : '' }}>UPDATE</option>
            <option value="DELETE" {{ request('action') === 'DELETE' ? 'selected' : '' }}>DELETE</option>
        </select>

        <input type="date" name="date_from" class="form-control" style="max-width: 160px;" value="{{ request('date_from') }}" title="Date From">
        <input type="date" name="date_to" class="form-control" style="max-width: 160px;" value="{{ request('date_to') }}" title="Date To">

        <button type="submit" class="btn btn-secondary">Filter</button>
        <a href="{{ route('admin.audit.index') }}" class="btn btn-secondary">Reset</a>
    </form>
</div>

<div class="card-table-container">
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Audit ID</th>
                    <th>Timestamp</th>
                    <th>Action</th>
                    <th>Table Affected</th>
                    <th>Record ID</th>
                    <th>Triggered / Performed By</th>
                    <th>Payload Changes</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($logs as $log)
                    <tr>
                        <td>#{{ $log->id }}</td>
                        <td style="font-size: 0.8rem; color: var(--slate-500);">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                        <td>
                            <span class="badge {{ $log->action === 'INSERT' ? 'badge-available' : ($log->action === 'UPDATE' ? 'badge-borrowed' : 'badge-damaged') }}">
                                {{ $log->action }}
                            </span>
                        </td>
                        <td><code>{{ $log->table_name }}</code></td>
                        <td>#{{ $log->record_id }}</td>
                        <td>{{ $log->user ? $log->user->full_name : 'MySQL DB Trigger / System' }}</td>
                        <td>
                            <button type="button" class="btn btn-secondary btn-sm" onclick="showPayloadModal({{ $log->id }}, '{{ addslashes(json_encode($log->old_values)) }}', '{{ addslashes(json_encode($log->new_values)) }}')">
                                View Payload
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; color: var(--slate-400); padding: 24px;">No audit entries found matching criteria.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding: 16px 20px;">
        {{ $logs->withQueryString()->links() }}
    </div>
</div>

<!-- Modal for Payload Inspection -->
<div id="payloadModal" class="modal-backdrop" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 100; align-items: center; justify-content: center;">
    <div style="background: #fff; border-radius: var(--radius-lg); width: 100%; max-width: 650px; padding: 24px; box-shadow: var(--shadow-lg);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <h3 style="font-size: 1.1rem; font-weight: 700;">Audit Record Payload <span id="modalAuditId"></span></h3>
            <button type="button" onclick="closeModal('payloadModal')" style="border: none; background: none; font-size: 1.25rem; cursor: pointer;">&times;</button>
        </div>
        <div class="grid-2" style="gap: 16px;">
            <div>
                <h4 style="font-size: 0.8rem; text-transform: uppercase; color: var(--slate-500); margin-bottom: 6px;">Previous Values (OLD)</h4>
                <pre id="modalOldValues" style="background: var(--slate-50); border: 1px solid var(--slate-200); padding: 12px; border-radius: var(--radius); font-size: 0.75rem; max-height: 250px; overflow-y: auto; white-space: pre-wrap;"></pre>
            </div>
            <div>
                <h4 style="font-size: 0.8rem; text-transform: uppercase; color: var(--slate-500); margin-bottom: 6px;">New Values (NEW)</h4>
                <pre id="modalNewValues" style="background: var(--slate-50); border: 1px solid var(--slate-200); padding: 12px; border-radius: var(--radius); font-size: 0.75rem; max-height: 250px; overflow-y: auto; white-space: pre-wrap;"></pre>
            </div>
        </div>
        <div style="margin-top: 20px; text-align: right;">
            <button type="button" class="btn btn-secondary" onclick="closeModal('payloadModal')">Close</button>
        </div>
    </div>
</div>

<script>
    function showPayloadModal(id, oldVals, newVals) {
        document.getElementById('modalAuditId').innerText = '#' + id;
        try {
            document.getElementById('modalOldValues').innerText = oldVals && oldVals !== 'null' ? JSON.stringify(JSON.parse(oldVals), null, 2) : 'None';
        } catch (e) {
            document.getElementById('modalOldValues').innerText = oldVals || 'None';
        }
        try {
            document.getElementById('modalNewValues').innerText = newVals && newVals !== 'null' ? JSON.stringify(JSON.parse(newVals), null, 2) : 'None';
        } catch (e) {
            document.getElementById('modalNewValues').innerText = newVals || 'None';
        }
        openModal('payloadModal');
    }
</script>
@endsection
