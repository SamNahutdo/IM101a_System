@extends('layouts.app')

@section('page_title', 'My Assigned Athletic Teams & Roster')

@section('content')
<div class="card-table-container">
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Team Name</th>
                    <th>Sport</th>
                    <th>Division</th>
                    <th>Roster Count</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($teams as $t)
                    <tr>
                        <td><strong>{{ $t->name }}</strong></td>
                        <td>{{ $t->sport }}</td>
                        <td><span class="badge badge-secondary">{{ $t->gender_category }}</span></td>
                        <td>{{ $t->teamMembers->count() }} registered athletes</td>
                        <td><span class="badge badge-available">{{ $t->status }}</span></td>
                        <td>
                            <a href="{{ route('coach.teams.show', $t) }}" class="btn btn-secondary btn-sm">View Roster & Gear &rarr;</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; color: var(--slate-400); padding: 24px;">No teams assigned to your profile.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
