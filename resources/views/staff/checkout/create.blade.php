@extends('layouts.app')

@section('page_title', 'Process Equipment Checkout (Atomic DB Transaction)')

@section('content')
<div class="card-table-container" style="max-width: 800px; margin: 0 auto; padding: 28px;">
    <h3 style="font-size: 1.15rem; font-weight: 700; margin-bottom: 24px; color: var(--slate-900);">Equipment Checkout Processing</h3>

    @if ($selectedReservation)
        <div style="background: #eff6ff; border: 1px solid #bfdbfe; padding: 16px; border-radius: var(--radius); margin-bottom: 24px;">
            <h4 style="font-size: 0.88rem; font-weight: 700; color: #1e40af; margin-bottom: 4px;">Fulfilling Approved Reservation: {{ $selectedReservation->reservation_code }}</h4>
            <p style="font-size: 0.82rem; color: #1e3a8a;">Borrower: {{ $selectedReservation->requester->full_name }} | Purpose: {{ $selectedReservation->purpose }}</p>
        </div>
    @endif

    <form action="{{ route('staff.checkout.store') }}" method="POST">
        @csrf
        @if ($selectedReservation)
            <input type="hidden" name="reservation_id" value="{{ $selectedReservation->id }}">
        @endif

        <div class="grid-2" style="gap: 16px;">
            <div class="form-group">
                <label class="form-label">Borrower (Athlete or Coach) *</label>
                <select name="borrower_id" class="form-select" required>
                    <option value="">Select Borrower...</option>
                    @foreach ($borrowers as $b)
                        <option value="{{ $b->id }}" {{ (old('borrower_id', $selectedReservation?->requester_id) == $b->id) ? 'selected' : '' }}>
                            {{ $b->full_name }} ({{ $b->getPrimaryRole()?->display_name }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Associated Team (Optional)</label>
                <select name="team_id" class="form-select">
                    <option value="">None (Individual Loan)</option>
                    @foreach ($teams as $t)
                        <option value="{{ $t->id }}" {{ (old('team_id', $selectedReservation?->team_id) == $t->id) ? 'selected' : '' }}>
                            {{ $t->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Expected Return Date & Time *</label>
            <input type="datetime-local" name="expected_return_time" class="form-control" value="{{ old('expected_return_time', date('Y-m-d\TH:i', strtotime('+4 hours'))) }}" required>
        </div>

        <div class="form-group">
            <label class="form-label">Select Equipment Items to Dispatch *</label>
            <div style="max-height: 220px; overflow-y: auto; border: 1px solid var(--slate-300); border-radius: var(--radius); padding: 12px; background: #fff;">
                @forelse ($availableEquipment as $eq)
                    <label style="display: flex; align-items: center; gap: 10px; padding: 6px 0; border-bottom: 1px solid var(--slate-100); font-size: 0.88rem; cursor: pointer;">
                        <input type="checkbox" name="equipment_ids[]" value="{{ $eq->id }}" 
                            {{ ($selectedReservation && $selectedReservation->items->pluck('equipment_id')->contains($eq->id)) ? 'checked' : '' }}>
                        <span>
                            <strong>{{ $eq->name }}</strong> 
                            <code style="color: var(--slate-500);">({{ $eq->asset_code }})</code> 
                            &bull; Condition: <span class="badge badge-available">{{ $eq->current_condition }}</span>
                        </span>
                    </label>
                @empty
                    <p style="color: var(--slate-400); font-size: 0.85rem;">No available equipment in catalog.</p>
                @endforelse
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Checkout Remarks / Inspection Notes</label>
            <textarea name="notes" class="form-control" rows="2" placeholder="Item air pressure checked, standard varsity condition verified...">{{ old('notes') }}</textarea>
        </div>

        <div style="display: flex; gap: 12px; margin-top: 24px;">
            <button type="submit" class="btn btn-primary">Authorize & Dispatch Checkout</button>
            <a href="{{ route('staff.checkout.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
