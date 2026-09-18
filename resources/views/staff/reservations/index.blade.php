@extends('layouts.app')

@section('page_title', 'Equipment Reservation Review Queue')

@section('content')
<div class="filter-bar">
    <form action="{{ route('staff.reservations.index') }}" method="GET" style="display: flex; gap: 12px; width: 100%;">
        <select name="status" class="form-select" style="max-width: 200px;">
            <option value="">All Statuses</option>
            <option value="Pending" {{ request('status') === 'Pending' ? 'selected' : '' }}>Pending Review</option>
            <option value="Approved" {{ request('status') === 'Approved' ? 'selected' : '' }}>Approved</option>
            <option value="Rejected" {{ request('status') === 'Rejected' ? 'selected' : '' }}>Rejected</option>
            <option value="Completed" {{ request('status') === 'Completed' ? 'selected' : '' }}>Completed</option>
        </select>
        <button type="submit" class="btn btn-secondary">Filter</button>
        <a href="{{ route('staff.reservations.index') }}" class="btn btn-secondary">Reset</a>
    </form>
</div>

<div class="card-table-container">
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Requester</th>
                    <th>Team</th>
                    <th>Window</th>
                    <th>Equipment Items</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($reservations as $r)
                    <tr>
                        <td><strong>{{ $r->reservation_code }}</strong></td>
                        <td>{{ $r->requester->full_name }}</td>
                        <td>{{ $r->team ? $r->team->name : 'Individual' }}</td>
                        <td style="font-size: 0.8rem;">
                            {{ $r->start_time->format('M j, g:i A') }} &ndash; {{ $r->end_time->format('M j, g:i A') }}
                        </td>
                        <td>
                            @foreach ($r->items as $item)
                                <div>&bull; {{ $item->equipment->name }} ({{ $item->requested_quantity }}x)</div>
                            @endforeach
                        </td>
                        <td>
                            @php
                                $badgeClass = match($r->status) {
                                    'Approved' => 'badge-available',
                                    'Pending' => 'badge-reserved',
                                    'Rejected' => 'badge-damaged',
                                    'Completed' => 'badge-completed',
                                    default => 'badge-secondary'
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }}">{{ $r->status }}</span>
                        </td>
                        <td>
                            <a href="{{ route('staff.reservations.show', $r) }}" class="btn btn-secondary btn-sm">Review &amp; Details</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; color: var(--slate-400); padding: 24px;">No reservations found matching filter.</td>
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
