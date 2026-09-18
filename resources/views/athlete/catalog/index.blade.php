@extends('layouts.app')

@section('page_title', 'Available Equipment Catalog')

@section('content')
<div class="filter-bar">
    <form action="{{ route('athlete.catalog.index') }}" method="GET" style="display: flex; gap: 12px; width: 100%; flex-wrap: wrap;">
        <input type="text" name="search" class="form-control" style="max-width: 280px;" placeholder="Search equipment name, brand..." value="{{ request('search') }}">
        
        <select name="category_id" class="form-select" style="max-width: 220px;">
            <option value="">All Categories</option>
            @foreach ($categories as $cat)
                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
            @endforeach
        </select>

        <button type="submit" class="btn btn-secondary">Search</button>
        <a href="{{ route('athlete.catalog.index') }}" class="btn btn-secondary">Reset</a>
    </form>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; margin-bottom: 28px;">
    @forelse ($equipment as $eq)
        <div class="stat-card" style="justify-content: space-between;">
            <div>
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                    <span class="badge badge-secondary" style="font-size: 0.72rem;">{{ $eq->category->name }}</span>
                    <span class="badge badge-available">Available</span>
                </div>
                <h3 style="font-size: 1.05rem; font-weight: 700; color: var(--slate-900); margin-bottom: 4px;">{{ $eq->name }}</h3>
                <div style="font-size: 0.78rem; color: var(--slate-500); margin-bottom: 8px;">Asset Code: <code>{{ $eq->asset_code }}</code></div>
                
                <p style="font-size: 0.82rem; color: var(--slate-600); margin-bottom: 12px;">
                    Location: {{ $eq->location->building }} ({{ $eq->location->room }}) &bull; Condition: <strong>{{ $eq->current_condition }}</strong>
                </p>
            </div>

            <button type="button" class="btn btn-primary btn-sm" style="width: 100%; margin-top: 12px;" onclick="openRequestModal({{ $eq->id }}, '{{ addslashes($eq->name) }}', '{{ $eq->asset_code }}')">
                Request Borrowing &rarr;
            </button>
        </div>
    @empty
        <div style="grid-column: 1 / -1; background: #fff; padding: 40px; text-align: center; border-radius: var(--radius-lg); border: 1px solid var(--slate-200); color: var(--slate-500);">
            No equipment assets currently available matching your criteria.
        </div>
    @endforelse
</div>

<div>
    {{ $equipment->withQueryString()->links() }}
</div>

<!-- Modal to Request Equipment -->
<div id="requestModal" class="modal-backdrop" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 100; align-items: center; justify-content: center;">
    <div style="background: #fff; border-radius: var(--radius-lg); width: 100%; max-width: 540px; padding: 28px; box-shadow: var(--shadow-lg);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px;">
            <h3 style="font-size: 1.15rem; font-weight: 700;">Submit Borrowing Request</h3>
            <button type="button" onclick="closeModal('requestModal')" style="border: none; background: none; font-size: 1.25rem; cursor: pointer;">&times;</button>
        </div>

        <form action="{{ route('athlete.catalog.request') }}" method="POST">
            @csrf
            <input type="hidden" name="equipment_id" id="modalEqId">

            <div class="form-group">
                <label class="form-label">Selected Equipment Asset</label>
                <input type="text" id="modalEqTitle" class="form-control" disabled style="background: var(--slate-100);">
            </div>

            <div class="grid-2" style="gap: 16px;">
                <div class="form-group">
                    <label class="form-label">Start Date & Time *</label>
                    <input type="datetime-local" name="start_time" class="form-control" value="{{ date('Y-m-d\TH:i', strtotime('+1 hour')) }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">End Date & Time *</label>
                    <input type="datetime-local" name="end_time" class="form-control" value="{{ date('Y-m-d\TH:i', strtotime('+4 hours')) }}" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Purpose / Training Details *</label>
                <textarea name="purpose" class="form-control" rows="3" required placeholder="Individual free throw drill, conditioning session, varsity practice..."></textarea>
            </div>

            <div style="display: flex; gap: 12px; margin-top: 20px;">
                <button type="submit" class="btn btn-primary">Submit Request for Staff Review</button>
                <button type="button" class="btn btn-secondary" onclick="closeModal('requestModal')">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openRequestModal(id, name, code) {
        document.getElementById('modalEqId').value = id;
        document.getElementById('modalEqTitle').value = name + ' (' + code + ')';
        openModal('requestModal');
    }
</script>
@endsection
