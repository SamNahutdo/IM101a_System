@extends('layouts.app')

@section('page_title', 'My Equipment Loans & Custody History')

@section('content')
<div class="card-table-container">
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Loan Code</th>
                    <th>Equipment Items</th>
                    <th>Checkout Date</th>
                    <th>Return Deadline</th>
                    <th>Return Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($loans as $loan)
                    <tr>
                        <td><strong>{{ $loan->transaction_code }}</strong></td>
                        <td>
                            @foreach ($loan->items as $it)
                                <div>&bull; <strong>{{ $it->equipment->name }}</strong> (<code>{{ $it->equipment->asset_code }}</code>)</div>
                            @endforeach
                        </td>
                        <td style="font-size: 0.85rem;">{{ $loan->checkout_time->format('M j, Y g:i A') }}</td>
                        <td>
                            <span style="{{ $loan->is_overdue ? 'color: #dc2626; font-weight: 700;' : '' }}">
                                {{ $loan->expected_return_time->format('M j, Y g:i A') }}
                            </span>
                        </td>
                        <td>
                            @if ($loan->is_overdue)
                                <span class="badge badge-damaged">OVERDUE</span>
                            @elseif ($loan->status === 'Active')
                                <span class="badge badge-borrowed">Active (In Use)</span>
                            @else
                                <span class="badge badge-completed">Returned ({{ $loan->actual_return_time?->format('M j') }})</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; color: var(--slate-400); padding: 24px;">No equipment loans on record.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding: 16px 20px;">
        {{ $loans->links() }}
    </div>
</div>
@endsection
