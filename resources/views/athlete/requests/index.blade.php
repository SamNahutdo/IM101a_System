@extends('layouts.app')

@section('page_title', 'My Equipment Borrowing Requests')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <p style="color: var(--slate-600); font-size: 0.9rem;">Track equipment reservation requests submitted to the Equipment Office.</p>
    <a href="{{ route('athlete.catalog.index') }}" class="btn btn-primary">+ Browse Catalog & Request</a>
</div>

<div class="card-table-container">
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Request Code</th>
                    <th>Equipment Requested</th>
                    <th>Requested Timeframe</th>
                    <th>Purpose</th>
                    <th>Review Status</th>
                    <th>Staff Reviewer</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($requests as $r)
                    <tr>
                        <td><strong>{{ $r->reservation_code }}</strong></td>
                        <td>
                            @foreach ($r->items as $it)
                                <div>&bull; {{ $it->equipment->name }}</div>
                            @endforeach
                        </td>
                        <td style="font-size: 0.82rem;">
                            {{ $r->start_time->format('M j, g:i A') }} &ndash; {{ $r->end_time->format('M j, g:i A') }}
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
                        <td style="font-size: 0.82rem;">
                            {{ $r->reviewer ? $r->reviewer->full_name : 'Under Review' }}
                            @if ($r->review_notes)
                                <div style="color: var(--slate-500); font-style: italic;">"{{ $r->review_notes }}"</div>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; color: var(--slate-400); padding: 24px;">You have not submitted any equipment requests yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding: 16px 20px;">
        {{ $requests->links() }}
    </div>
</div>
@endsection
