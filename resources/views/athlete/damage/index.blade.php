@extends('layouts.app')

@section('page_title', 'Report Damaged / Defective Athletic Equipment')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <p style="color: var(--slate-600); font-size: 0.9rem;">Report damaged, broken, or unsafe gear directly to athletic maintenance staff.</p>
    <a href="{{ route('athlete.damage.create') }}" class="btn btn-danger">+ Report Damaged Equipment</a>
</div>

<div class="card-table-container">
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Report Code</th>
                    <th>Equipment</th>
                    <th>Damage Type</th>
                    <th>Severity</th>
                    <th>Reported Date</th>
                    <th>Resolution Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($reports as $rep)
                    <tr>
                        <td><strong>{{ $rep->report_code }}</strong></td>
                        <td>
                            <strong>{{ $rep->equipment->name }}</strong>
                            <div><code>{{ $rep->equipment->asset_code }}</code></div>
                        </td>
                        <td>{{ $rep->damage_type }}</td>
                        <td>
                            <span class="badge {{ $rep->severity === 'Severe' ? 'badge-damaged' : 'badge-maintenance' }}">
                                {{ $rep->severity }}
                            </span>
                        </td>
                        <td style="font-size: 0.82rem;">{{ $rep->reported_at->format('M j, Y g:i A') }}</td>
                        <td>
                            <span class="badge badge-secondary">{{ $rep->status }}</span>
                            @if ($rep->resolution)
                                <div style="font-size: 0.75rem; color: var(--slate-500); margin-top: 2px;">{{ $rep->resolution }}</div>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; color: var(--slate-400); padding: 24px;">No damage reports filed by you.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding: 16px 20px;">
        {{ $reports->links() }}
    </div>
</div>
@endsection
