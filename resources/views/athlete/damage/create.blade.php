@extends('layouts.app')

@section('page_title', 'File Equipment Damage Report')

@section('content')
<div class="card-table-container" style="max-width: 700px; margin: 0 auto; padding: 28px;">
    <h3 style="font-size: 1.15rem; font-weight: 700; margin-bottom: 24px; color: var(--slate-900);">Equipment Incident & Damage Report</h3>

    <form action="{{ route('athlete.damage.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label class="form-label">Select Damaged Equipment Asset *</label>
            <select name="equipment_id" class="form-select" required>
                <option value="">Select Equipment Item...</option>
                @foreach ($equipmentList as $eq)
                    <option value="{{ $eq->id }}">{{ $eq->name }} ({{ $eq->asset_code }}) &bull; Current Status: {{ $eq->status }}</option>
                @endforeach
            </select>
        </div>

        <div class="grid-2" style="gap: 16px;">
            <div class="form-group">
                <label class="form-label">Damage Type / Symptom *</label>
                <input type="text" name="damage_type" class="form-control" required placeholder="e.g. Broken buckle, Torn seam, Bent rim, Punctured ball">
            </div>

            <div class="form-group">
                <label class="form-label">Severity Level *</label>
                <select name="severity" class="form-select" required>
                    <option value="Minor">Minor (Usable with minor cosmetic defect)</option>
                    <option value="Moderate" selected>Moderate (Reduced performance)</option>
                    <option value="Severe">Severe (Unusable / Safety hazard - auto sets to Damaged)</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Incident Description & Location *</label>
            <textarea name="description" class="form-control" rows="4" required placeholder="Explain how and when the damage occurred, court/field location, and observed safety hazard..."></textarea>
        </div>

        <div style="display: flex; gap: 12px; margin-top: 24px;">
            <button type="submit" class="btn btn-danger">Submit Official Damage Report</button>
            <a href="{{ route('athlete.damage.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
