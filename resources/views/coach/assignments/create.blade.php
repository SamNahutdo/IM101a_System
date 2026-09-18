@extends('layouts.app')

@section('page_title', 'Assign Equipment to Athlete or Team')

@section('content')
<div class="card-table-container" style="max-width: 750px; margin: 0 auto; padding: 28px;">
    <h3 style="font-size: 1.15rem; font-weight: 700; margin-bottom: 24px; color: var(--slate-900);">Equipment Custody Assignment</h3>

    <form action="{{ route('coach.assignments.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label class="form-label">Select Equipment Asset *</label>
            <select name="equipment_id" class="form-select" required>
                <option value="">Select Equipment...</option>
                @foreach ($availableEquipment as $eq)
                    <option value="{{ $eq->id }}">{{ $eq->name }} ({{ $eq->asset_code }}) &bull; {{ $eq->current_condition }}</option>
                @endforeach
            </select>
        </div>

        <div class="grid-2" style="gap: 16px;">
            <div class="form-group">
                <label class="form-label">Team *</label>
                <select name="team_id" id="teamSelect" class="form-select" required onchange="filterAthletes()">
                    <option value="">Select Team...</option>
                    @foreach ($teams as $t)
                        <option value="{{ $t->id }}">{{ $t->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Assign to Specific Athlete (Optional)</label>
                <select name="athlete_id" id="athleteSelect" class="form-select">
                    <option value="">Whole Team Custody</option>
                    @foreach ($teams as $t)
                        @foreach ($t->athletes as $ath)
                            <option value="{{ $ath->id }}" data-team="{{ $t->id }}">
                                {{ $ath->user->full_name }} ({{ $ath->student_id }})
                            </option>
                        @endforeach
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid-2" style="gap: 16px;">
            <div class="form-group">
                <label class="form-label">Assignment Duration End Date *</label>
                <input type="date" name="expected_end_date" class="form-control" value="{{ date('Y-m-d', strtotime('+3 months')) }}" required>
            </div>

            <div class="form-group">
                <label class="form-label">Condition on Assignment *</label>
                <select name="condition_on_assignment" class="form-select" required>
                    <option value="New">New</option>
                    <option value="Excellent" selected>Excellent</option>
                    <option value="Good">Good</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Assignment Remarks / Season Purpose</label>
            <textarea name="notes" class="form-control" rows="3" placeholder="Varsity league jersey assignment, tournament protective pads..."></textarea>
        </div>

        <div style="display: flex; gap: 12px; margin-top: 24px;">
            <button type="submit" class="btn btn-primary">Authorize Assignment</button>
            <a href="{{ route('coach.assignments.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<script>
    function filterAthletes() {
        const teamId = document.getElementById('teamSelect').value;
        const athleteSelect = document.getElementById('athleteSelect');
        const options = athleteSelect.querySelectorAll('option[data-team]');

        options.forEach(opt => {
            opt.style.display = (!teamId || opt.getAttribute('data-team') === teamId) ? 'block' : 'none';
        });
    }
</script>
@endsection
