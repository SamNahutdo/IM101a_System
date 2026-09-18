@extends('layouts.app')

@section('page_title', 'Reservation Details & Staff Evaluation')

@section('content')
<div style="margin-bottom: 20px;">
    <a href="{{ route('staff.reservations.index') }}" class="btn btn-secondary btn-sm">&larr; Back to Queue</a>
</div>

<div class="grid-2">
    <!-- Reservation Overview -->
    <div class="card-table-container" style="padding: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px;">
            <h3 style="font-size: 1.15rem; font-weight: 700;">Reservation: {{ $reservation->reservation_code }}</h3>
            <span class="badge {{ $reservation->status === 'Approved' ? 'badge-available' : ($reservation->status === 'Pending' ? 'badge-reserved' : 'badge-damaged') }}">
                {{ $reservation->status }}
            </span>
        </div>

        <p style="font-size: 0.9rem; margin-bottom: 8px;"><strong>Requester:</strong> {{ $reservation->requester->full_name }} ({{ $reservation->requester->email }})</p>
        <p style="font-size: 0.9rem; margin-bottom: 8px;"><strong>Team Affiliation:</strong> {{ $reservation->team ? $reservation->team->name : 'Individual Student Request' }}</p>
        <p style="font-size: 0.9rem; margin-bottom: 8px;"><strong>Reservation Start:</strong> {{ $reservation->start_time->format('l, F j, Y g:i A') }}</p>
        <p style="font-size: 0.9rem; margin-bottom: 8px;"><strong>Reservation End:</strong> {{ $reservation->end_time->format('l, F j, Y g:i A') }}</p>
        
        <div style="background: var(--slate-50); border: 1px solid var(--slate-200); padding: 14px; border-radius: var(--radius); margin-top: 16px;">
            <h4 style="font-size: 0.82rem; text-transform: uppercase; color: var(--slate-500); font-weight: 700; margin-bottom: 4px;">Purpose / Athletic Activity</h4>
            <p style="font-size: 0.88rem; color: var(--slate-700);">{{ $reservation->purpose }}</p>
        </div>

        @if ($reservation->reviewed_by)
            <div style="margin-top: 18px; font-size: 0.82rem; color: var(--slate-500);">
                Reviewed by <strong>{{ $reservation->reviewer->full_name }}</strong> on {{ $reservation->reviewed_at->format('M j, Y g:i A') }}
                @if ($reservation->review_notes)
                    <div>Notes: {{ $reservation->review_notes }}</div>
                @endif
            </div>
        @endif

        @if ($reservation->status === 'Approved')
            <div style="margin-top: 24px;">
                <a href="{{ route('staff.checkout.create', ['reservation_id' => $reservation->id]) }}" class="btn btn-primary">
                    Proceed to Equipment Checkout &rarr;
                </a>
            </div>
        @endif
    </div>

    <!-- Requested Equipment & Evaluation Action -->
    <div>
        <div class="card-table-container" style="padding: 24px; margin-bottom: 24px;">
            <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 16px;">Requested Equipment Items</h3>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Equipment</th>
                        <th>Current Status</th>
                        <th>Condition</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($reservation->items as $it)
                        <tr>
                            <td>
                                <strong>{{ $it->equipment->name }}</strong>
                                <div><code>{{ $it->equipment->asset_code }}</code></div>
                            </td>
                            <td><span class="badge badge-secondary">{{ $it->equipment->status }}</span></td>
                            <td><span class="badge badge-available">{{ $it->equipment->current_condition }}</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if ($reservation->status === 'Pending')
            <div class="card-table-container" style="padding: 24px;">
                <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 16px;">Staff Decision</h3>
                
                <form action="{{ route('staff.reservations.review', $reservation) }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">Review Decision *</label>
                        <select name="decision" class="form-select" required>
                            <option value="Approve">Approve Reservation</option>
                            <option value="Reject">Reject Reservation</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Review Notes / Instructions</label>
                        <textarea name="review_notes" class="form-control" rows="3" placeholder="Provide pick-up guidelines or justification if rejected..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%;">Submit Decision</button>
                </form>
            </div>
        @endif
    </div>
</div>
@endsection
