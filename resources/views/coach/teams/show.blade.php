@extends('layouts.app')

@section('page_title', $team->name . ' — Roster & Equipment')

@section('content')
<div style="margin-bottom: 20px;">
    <a href="{{ route('coach.teams.index') }}" class="btn btn-secondary btn-sm">&larr; Back to My Teams</a>
    <a href="{{ route('coach.reservations.create') }}" class="btn btn-primary btn-sm" style="margin-left: 8px;">Request Gear for this Team</a>
</div>

<!-- Team Header -->
<div class="card-table-container" style="padding: 24px; margin-bottom: 24px;">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h2 style="font-size: 1.35rem; font-weight: 800; color: var(--slate-900);">{{ $team->name }}</h2>
            <p style="font-size: 0.88rem; color: var(--slate-500);">
                Sport: <strong>{{ $team->sport }}</strong> &bull; Category: <strong>{{ $team->gender_category }}</strong> &bull; Head Coach: <strong>{{ $team->coach->user->full_name }}</strong>
            </p>
        </div>
        <span class="badge badge-available">{{ $team->status }}</span>
    </div>
</div>

<!-- Team Members Table (Demonstrating N:M team_members junction table) -->
<div class="card-table-container">
    <div class="card-table-header">
        <h3 class="card-table-title">Team Roster (team_members junction relation)</h3>
        <span class="badge badge-secondary">{{ $team->teamMembers->count() }} Registered Athletes</span>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Jersey #</th>
                    <th>Athlete Name</th>
                    <th>Student ID</th>
                    <th>Playing Position</th>
                    <th>Joined Team</th>
                    <th>Roster Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($team->teamMembers as $tm)
                    <tr>
                        <td><strong style="font-size: 1.1rem; color: var(--primary-700);">#{{ $tm->jersey_number ?? '—' }}</strong></td>
                        <td>
                            <strong>{{ $tm->athlete->user->full_name }}</strong>
                            <div style="font-size: 0.78rem; color: var(--slate-400);">{{ $tm->athlete->user->email }}</div>
                        </td>
                        <td><code>{{ $tm->athlete->student_id }}</code></td>
                        <td>{{ $tm->position ?? 'Unassigned' }}</td>
                        <td style="font-size: 0.82rem;">{{ $tm->joined_at ? $tm->joined_at->format('M j, Y') : '—' }}</td>
                        <td><span class="badge badge-available">{{ $tm->membership_status }}</span></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; color: var(--slate-400); padding: 24px;">No athletes enrolled in this team roster yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
