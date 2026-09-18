@extends('layouts.app')

@section('page_title', 'Equipment Assignment to Athletes & Teams')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <p style="color: var(--slate-600); font-size: 0.9rem;">Assign seasonal or training gear to individual student athletes or squads.</p>
    <a href="{{ route('coach.assignments.create') }}" class="btn btn-primary">+ Assign Equipment</a>
</div>

<div class="card-table-container">
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Equipment</th>
                    <th>Assigned To</th>
                    <th>Type</th>
                    <th>Team</th>
                    <th>Assigned Date</th>
                    <th>Expected End</th>
                    <th>Condition</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($assignments as $asgn)
                    <tr>
                        <td>
                            <strong>{{ $asgn->equipment->name }}</strong>
                            <div><code>{{ $asgn->equipment->asset_code }}</code></div>
                        </td>
                        <td>
                            @if ($asgn->athlete)
                                <strong>{{ $asgn->athlete->user->full_name }}</strong>
                                <div style="font-size: 0.75rem; color: var(--slate-400);">ID: {{ $asgn->athlete->student_id }}</div>
                            @else
                                <strong>{{ $asgn->team->name }}</strong>
                            @endif
                        </td>
                        <td><span class="badge badge-secondary">{{ $asgn->assignable_type }}</span></td>
                        <td>{{ $asgn->team->name }}</td>
                        <td style="font-size: 0.82rem;">{{ $asgn->assigned_date->format('M j, Y') }}</td>
                        <td style="font-size: 0.82rem;">{{ $asgn->expected_end_date->format('M j, Y') }}</td>
                        <td><span class="badge badge-available">{{ $asgn->condition_on_assignment }}</span></td>
                        <td><span class="badge badge-available">{{ $asgn->assignment_status }}</span></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center; color: var(--slate-400); padding: 24px;">No equipment currently assigned.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding: 16px 20px;">
        {{ $assignments->links() }}
    </div>
</div>
@endsection
