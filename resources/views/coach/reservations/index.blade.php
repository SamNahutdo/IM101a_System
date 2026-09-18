@extends('layouts.app')

@section('page_title', 'Team Equipment Reservations')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <p style="color: var(--slate-600); font-size: 0.9rem;">Submit and track reservation requests for team scrimmages, tournaments, and practices.</p>
    <a href="{{ route('coach.reservations.create') }}" class="btn btn-primary">+ Reserve Equipment for Team</a>
</div>

<div class="card-table-container">
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Team</th>
                    <th>Reservation Schedule</th>
                    <th>Equipment Items</th>
                    <th>Purpose</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($reservations as $r)
                    <tr>
                        <td><strong>{{ $r->reservation_code }}</strong></td>
                        <td>{{ $r->team->name }}</td>
                        <td style="font-size: 0.82rem;">
                            {{ $r->start_time->format('M j, g:i A') }} &ndash; {{ $r->end_time->format('M j, g:i A') }}
                        </td>
                        <td>
                            @foreach ($r->items as $it)
                                <div>&bull; {{ $it->equipment->name }}</div>
                            @endforeach
                        </td>
                        <td style="font-size: 0.85rem; color: var(--slate-600);">{{ Str::limit($r->purpose, 45) }}</td>
                        <td>
                            @php
                                $badge = match($r->status) {
                                    'Approved' => 'badge-available',
                                    'Pending' => 'badge-reserved',
                                    'Rejected' => 'badge-damaged',
                                    'Completed' => 'badge-completed',
                                    default => 'badge-secondary'
                                };
                            @endphp
                            <span class="badge {{ $badge }}">{{ $r->status }}</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; color: var(--slate-400); padding: 24px;">No team reservations submitted yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding: 16px 20px;">
        {{ $reservations->links() }}
    </div>
</div>
@endsection
