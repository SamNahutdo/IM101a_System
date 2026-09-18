@extends('layouts.app')

@section('page_title', 'Submit Team Equipment Reservation')

@section('content')
<div class="card-table-container" style="max-width: 780px; margin: 0 auto; padding: 28px;">
    <h3 style="font-size: 1.15rem; font-weight: 700; margin-bottom: 24px; color: var(--slate-900);">Team Reservation Request</h3>

    <form action="{{ route('coach.reservations.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label class="form-label">Select Team *</label>
            <select name="team_id" class="form-select" required>
                @foreach ($myTeams as $t)
                    <option value="{{ $t->id }}">{{ $t->name }} ({{ $t->sport }})</option>
                @endforeach
            </select>
        </div>

        <div class="grid-2" style="gap: 16px;">
            <div class="form-group">
                <label class="form-label">Reservation Start *</label>
                <input type="datetime-local" name="start_time" class="form-control" value="{{ old('start_time', date('Y-m-d\TH:i', strtotime('+1 day 14:00'))) }}" required>
            </div>
            <div class="form-group">
                <label class="form-label">Reservation End *</label>
                <input type="datetime-local" name="end_time" class="form-control" value="{{ old('end_time', date('Y-m-d\TH:i', strtotime('+1 day 18:00'))) }}" required>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Select Equipment Items to Reserve *</label>
            <div style="max-height: 200px; overflow-y: auto; border: 1px solid var(--slate-300); border-radius: var(--radius); padding: 12px; background: #fff;">
                @forelse ($availableEquipment as $eq)
                    <label style="display: flex; align-items: center; gap: 10px; padding: 6px 0; border-bottom: 1px solid var(--slate-100); font-size: 0.88rem; cursor: pointer;">
                        <input type="checkbox" name="equipment_ids[]" value="{{ $eq->id }}">
                        <span>
                            <strong>{{ $eq->name }}</strong> 
                            <code style="color: var(--slate-500);">({{ $eq->asset_code }})</code> 
                            &bull; Category: {{ $eq->category->name }}
                        </span>
                    </label>
                @empty
                    <p style="color: var(--slate-400); font-size: 0.85rem;">No available equipment in catalog.</p>
                @endforelse
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Purpose / Event Details *</label>
            <textarea name="purpose" class="form-control" rows="3" required placeholder="Specify tournament match, official team training, scrimmage schedule...">{{ old('purpose') }}</textarea>
        </div>

        <div style="display: flex; gap: 12px; margin-top: 24px;">
            <button type="submit" class="btn btn-primary">Submit for Staff Approval</button>
            <a href="{{ route('coach.reservations.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
