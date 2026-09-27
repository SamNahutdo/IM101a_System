@extends('layouts.app')

@section('title', 'Sports Management')
@section('page_title', 'Varsity Sports & Divisions')

@section('content')
<div class="row g-4">
    <div class="col-md-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-trophy me-2 text-primary"></i>Active Sports</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Sport Name</th>
                            <th>Description</th>
                            <th class="text-center">Teams</th>
                            <th class="text-center">Equipment Items</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sports as $s)
                            <tr>
                                <td class="fw-bold text-dark">{{ $s->sport_name }}</td>
                                <td>{{ $s->description ?? 'N/A' }}</td>
                                <td class="text-center"><span class="badge bg-primary">{{ $s->teams_count }}</span></td>
                                <td class="text-center"><span class="badge bg-secondary">{{ $s->equipment_count }}</span></td>
                                <td><span class="badge bg-success">{{ $s->status }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-5">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-plus-circle me-2 text-primary"></i>Add New Sport</h6>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('admin.sports.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Sport Name <span class="text-danger">*</span></label>
                        <input type="text" name="sport_name" class="form-control" placeholder="e.g. Badminton, Volleyball" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Description</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Brief sport division description..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-save me-1"></i> Create Sport
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
