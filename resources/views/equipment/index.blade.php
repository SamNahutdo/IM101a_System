@extends('layouts.app')

@section('title', 'Equipment Master')
@section('page_title', 'Athletic Equipment Inventory')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
        <div>
            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-box-seam me-2 text-primary"></i>Equipment Records</h6>
            <small class="text-muted">Managed with MySQL 3NF schema, check constraints & audit triggers</small>
        </div>
        @if (auth()->user()->isAdmin() || auth()->user()->isStaff())
            <a href="{{ route(auth()->user()->role->name . '.equipment.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-circle me-1"></i> Add Equipment
            </a>
        @endif
    </div>

    <!-- FILTERS -->
    <div class="card-body bg-light border-bottom py-3">
        <form method="GET" action="{{ url()->current() }}" class="row g-2 align-items-center">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search code or name..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="sport_id" class="form-select form-select-sm">
                    <option value="">-- All Sports --</option>
                    @foreach ($sports as $s)
                        <option value="{{ $s->id }}" {{ request('sport_id') == $s->id ? 'selected' : '' }}>{{ $s->sport_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="category" class="form-select form-select-sm">
                    <option value="">-- Category --</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="">-- Status --</option>
                    <option value="Available" {{ request('status') == 'Available' ? 'selected' : '' }}>Available</option>
                    <option value="In Use" {{ request('status') == 'In Use' ? 'selected' : '' }}>In Use</option>
                    <option value="Under Maintenance" {{ request('status') == 'Under Maintenance' ? 'selected' : '' }}>Under Maintenance</option>
                    <option value="Retired" {{ request('status') == 'Retired' ? 'selected' : '' }}>Retired</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-dark btn-sm w-100"><i class="bi bi-filter"></i> Filter</button>
                <a href="{{ url()->current() }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-counterclockwise"></i></a>
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Equipment Name</th>
                    <th>Sport</th>
                    <th>Category</th>
                    <th class="text-center">Total Qty</th>
                    <th class="text-center">Available</th>
                    <th>Condition</th>
                    <th>Status</th>
                    @if (auth()->user()->isAdmin() || auth()->user()->isStaff())
                        <th class="text-end">Actions</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse ($equipment as $item)
                    <tr>
                        <td><span class="badge bg-dark font-monospace">{{ $item->equipment_code }}</span></td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $item->equipment_name }}</div>
                        </td>
                        <td>{{ $item->sport->sport_name ?? 'N/A' }}</td>
                        <td><span class="badge bg-secondary">{{ $item->category }}</span></td>
                        <td class="text-center fw-bold">{{ $item->quantity }}</td>
                        <td class="text-center">
                            @if ($item->available_quantity > 2)
                                <span class="badge bg-success">{{ $item->available_quantity }}</span>
                            @elseif ($item->available_quantity > 0)
                                <span class="badge bg-warning text-dark">{{ $item->available_quantity }} Low</span>
                            @else
                                <span class="badge bg-danger">0 None</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $item->condition === 'Damaged' ? 'bg-danger' : ($item->condition === 'New' ? 'bg-info text-dark' : 'bg-light text-dark border') }}">
                                {{ $item->condition }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $item->status === 'Available' ? 'bg-success' : ($item->status === 'Under Maintenance' ? 'bg-danger' : 'bg-warning text-dark') }}">
                                {{ $item->status }}
                            </span>
                        </td>
                        @if (auth()->user()->isAdmin() || auth()->user()->isStaff())
                            <td class="text-end">
                                <a href="{{ route(auth()->user()->role->name . '.equipment.edit', $item->id) }}" class="btn btn-outline-primary btn-sm py-0 px-2" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @if (auth()->user()->isAdmin())
                                    <form action="{{ route(auth()->user()->role->name . '.equipment.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this equipment? MySQL audit trigger will log this action.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm py-0 px-2" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-3 d-block mb-1"></i> No athletic equipment found matching criteria.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($equipment->hasPages())
        <div class="card-footer bg-white py-2">
            {{ $equipment->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection
