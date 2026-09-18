@extends('layouts.app')

@section('page_title', 'Athletic Equipment Inventory Master')

@section('content')
<div class="filter-bar">
    <form action="{{ route('admin.equipment.index') }}" method="GET" style="display: flex; gap: 12px; width: 100%; flex-wrap: wrap;">
        <input type="text" name="search" class="form-control" style="max-width: 260px;" placeholder="Search asset, name, S/N..." value="{{ request('search') }}">
        
        <select name="category_id" class="form-select" style="max-width: 200px;">
            <option value="">All Categories</option>
            @foreach ($categories as $cat)
                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
            @endforeach
        </select>

        <select name="status" class="form-select" style="max-width: 160px;">
            <option value="">All Statuses</option>
            @foreach (['Available', 'Reserved', 'Borrowed', 'Assigned', 'Maintenance', 'Damaged', 'Lost', 'Retired'] as $s)
                <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ $s }}</option>
            @endforeach
        </select>

        <select name="condition" class="form-select" style="max-width: 150px;">
            <option value="">All Conditions</option>
            @foreach (['New', 'Excellent', 'Good', 'Fair', 'Poor'] as $c)
                <option value="{{ $c }}" {{ request('condition') === $c ? 'selected' : '' }}>{{ $c }}</option>
            @endforeach
        </select>

        <button type="submit" class="btn btn-secondary">Filter</button>
        <a href="{{ route('admin.equipment.index') }}" class="btn btn-secondary">Reset</a>
        <a href="{{ route('admin.equipment.create') }}" class="btn btn-primary" style="margin-left: auto;">+ Register Equipment</a>
    </form>
</div>

<div class="card-table-container">
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Asset Code</th>
                    <th>Equipment Name</th>
                    <th>Category</th>
                    <th>Location</th>
                    <th>Condition</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($equipment as $eq)
                    <tr>
                        <td><strong><code>{{ $eq->asset_code }}</code></strong></td>
                        <td>
                            <strong>{{ $eq->name }}</strong>
                            @if ($eq->brand)
                                <div style="font-size: 0.75rem; color: var(--slate-400);">{{ $eq->brand }} {{ $eq->model }}</div>
                            @endif
                        </td>
                        <td>{{ $eq->category->name }}</td>
                        <td style="font-size: 0.82rem;">{{ $eq->location->building }} - {{ $eq->location->room }}</td>
                        <td>
                            <span class="badge {{ in_array($eq->current_condition, ['New', 'Excellent', 'Good']) ? 'badge-available' : 'badge-maintenance' }}">
                                {{ $eq->current_condition }}
                            </span>
                        </td>
                        <td>
                            @php
                                $badgeClass = match($eq->status) {
                                    'Available' => 'badge-available',
                                    'Borrowed' => 'badge-borrowed',
                                    'Reserved' => 'badge-reserved',
                                    'Maintenance' => 'badge-maintenance',
                                    'Damaged', 'Lost' => 'badge-damaged',
                                    default => 'badge-secondary'
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }}">{{ $eq->status }}</span>
                        </td>
                        <td>
                            <a href="{{ route('admin.equipment.show', $eq) }}" class="btn btn-secondary btn-sm">Timeline</a>
                            <a href="{{ route('admin.equipment.edit', $eq) }}" class="btn btn-secondary btn-sm">Edit</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div style="padding: 16px 20px;">
        {{ $equipment->withQueryString()->links() }}
    </div>
</div>
@endsection
