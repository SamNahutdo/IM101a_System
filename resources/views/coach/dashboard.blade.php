@extends('layouts.app')

@section('page_title', 'Coach Athletics Management Dashboard')

@section('content')
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Assigned Teams</span>
            <div class="stat-icon blue">🏆</div>
        </div>
        <div class="stat-value">{{ $stats['total_teams'] }}</div>
        <div class="stat-subtext">Active collegiate squads</div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Student Athletes</span>
            <div class="stat-icon green">👥</div>
        </div>
        <div class="stat-value">{{ $stats['total_athletes'] }}</div>
        <div class="stat-subtext">Across team rosters</div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Team Reservations</span>
            <div class="stat-icon purple">📋</div>
        </div>
        <div class="stat-value">{{ $stats['active_reservations'] }}</div>
        <div class="stat-subtext"><a href="{{ route('coach.reservations.index') }}">Manage reservations &rarr;</a></div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Active Team Loans</span>
            <div class="stat-icon blue">🏃</div>
        </div>
        <div class="stat-value">{{ $stats['active_borrowings'] }}</div>
        <div class="stat-subtext">Gear currently with team</div>
    </div>
</div>

<div class="grid-2">
    <!-- Assigned Teams Overview -->
    <div class="card-table-container">
        <div class="card-table-header">
            <h3 class="card-table-title">My Athletic Teams</h3>
            <a href="{{ route('coach.teams.index') }}" class="btn btn-secondary btn-sm">Full Roster</a>
        </div>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Team Name</th>
                        <th>Sport</th>
                        <th>Division</th>
                        <th>Athletes</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($myTeams as $t)
                        <tr>
                            <td>
                                <strong><a href="{{ route('coach.teams.show', $t) }}" style="color: var(--primary-700); text-decoration: none;">{{ $t->name }}</a></strong>
                            </td>
                            <td>{{ $t->sport }}</td>
                            <td><span class="badge badge-secondary">{{ $t->gender_category }}</span></td>
                            <td><strong>{{ $t->athletes->count() }} athletes</strong></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align: center; color: var(--slate-400); padding: 20px;">No teams assigned yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Active Equipment Assignments -->
    <div class="card-table-container">
        <div class="card-table-header">
            <h3 class="card-table-title">Current Equipment Assignments</h3>
            <a href="{{ route('coach.assignments.create') }}" class="btn btn-primary btn-sm">+ Assign Gear</a>
        </div>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Equipment</th>
                        <th>Assigned To</th>
                        <th>Condition</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($activeAssignments as $asgn)
                        <tr>
                            <td>
                                <strong>{{ $asgn->equipment->name }}</strong>
                                <div style="font-size: 0.75rem; color: var(--slate-400);">{{ $asgn->equipment->asset_code }}</div>
                            </td>
                            <td>
                                @if ($asgn->athlete)
                                    {{ $asgn->athlete->user->full_name }} (#{{ $asgn->athlete->teamMemberships->first()?->jersey_number ?? '—' }})
                                @else
                                    {{ $asgn->team->name }}
                                @endif
                            </td>
                            <td><span class="badge badge-available">{{ $asgn->condition_on_assignment }}</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" style="text-align: center; color: var(--slate-400); padding: 20px;">No long-term equipment assignments.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
