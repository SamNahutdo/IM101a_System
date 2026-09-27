@extends('layouts.app')

@section('title', 'Teams & Rosters (N:M)')
@section('page_title', 'Varsity Teams & Athlete Roster (N:M Relationship)')

@section('content')
<div class="row g-4 mb-4">
    <!-- TEAMS LIST WITH EXPANDABLE ROSTERS -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-diagram-3 me-2 text-primary"></i>Teams & Many-to-Many Athlete Rosters</h6>
                <small class="text-muted">Junction table <code>team_members</code> stores intersection attributes: <code>joined_at</code>, <code>position</code>, <code>status</code></small>
            </div>
            <div class="card-body p-0">
                <div class="accordion accordion-flush" id="teamsAccordion">
                    @forelse ($teams as $idx => $team)
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading{{ $team->id }}">
                                <button class="accordion-button {{ $idx === 0 ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $team->id }}">
                                    <div class="d-flex justify-content-between align-items-center w-100 me-3">
                                        <div>
                                            <strong class="text-dark">{{ $team->team_name }}</strong>
                                            <span class="badge bg-secondary ms-2">{{ $team->sport->sport_name }}</span>
                                            <small class="text-muted ms-2">Coach: {{ $team->coach->fullName ?? 'None' }}</small>
                                        </div>
                                        <span class="badge bg-primary">{{ $team->athletes->count() }} Athletes Enrolled</span>
                                    </div>
                                </button>
                            </h2>
                            <div id="collapse{{ $team->id }}" class="accordion-collapse collapse {{ $idx === 0 ? 'show' : '' }}" data-bs-parent="#teamsAccordion">
                                <div class="accordion-body bg-light">
                                    <div class="table-responsive bg-white rounded border mb-3">
                                        <table class="table table-sm align-middle mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Student Number</th>
                                                    <th>Athlete Name</th>
                                                    <th>Position</th>
                                                    <th>Enrolled Date</th>
                                                    <th class="text-end">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($team->athletes as $ath)
                                                    <tr>
                                                        <td class="font-monospace">{{ $ath->student_number }}</td>
                                                        <td><strong>{{ $ath->fullName }}</strong></td>
                                                        <td><span class="badge bg-info text-dark">{{ $ath->pivot->position ?? 'Player' }}</span></td>
                                                        <td>{{ \Carbon\Carbon::parse($ath->pivot->joined_at)->format('M d, Y') }}</td>
                                                        <td class="text-end">
                                                            <form action="{{ route('admin.teams.members.remove', $ath->pivot->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove athlete from roster?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-outline-danger btn-sm py-0 px-2">
                                                                    <i class="bi bi-x"></i>
                                                                </button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="5" class="text-center py-2 text-muted small">No athletes enrolled in roster yet.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- FORM TO ENROLL ATHLETE IN N:M JUNCTION TABLE -->
                                    <form action="{{ route('admin.teams.members.add', $team->id) }}" method="POST" class="row g-2 align-items-center">
                                        @csrf
                                        <div class="col-md-5">
                                            <select name="athlete_id" class="form-select form-select-sm" required>
                                                <option value="">-- Enroll Athlete into Team --</option>
                                                @foreach ($athletes as $a)
                                                    <option value="{{ $a->id }}">{{ $a->fullName }} ({{ $a->student_number }})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <input type="text" name="position" class="form-control form-control-sm" placeholder="Roster position (e.g. Pitcher, Forward)">
                                        </div>
                                        <div class="col-md-3">
                                            <button type="submit" class="btn btn-success btn-sm w-100">
                                                <i class="bi bi-person-plus"></i> Add to Roster
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-4 text-center text-muted">No teams created yet.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- CREATE NEW TEAM FORM -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-plus-circle me-2 text-primary"></i>Create Varsity Team</h6>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('admin.teams.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Sport Division <span class="text-danger">*</span></label>
                        <select name="sport_id" class="form-select form-select-sm" required>
                            <option value="">-- Select Sport --</option>
                            @foreach ($sports as $s)
                                <option value="{{ $s->id }}">{{ $s->sport_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Head Coach</label>
                        <select name="coach_id" class="form-select form-select-sm">
                            <option value="">-- Assign Coach (Optional) --</option>
                            @foreach ($coaches as $c)
                                <option value="{{ $c->id }}">Coach {{ $c->fullName }} ({{ $c->employee_number }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Team Name <span class="text-danger">*</span></label>
                        <input type="text" name="team_name" class="form-control form-control-sm" placeholder="e.g. Falcons Men Baseball" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">School Year <span class="text-danger">*</span></label>
                        <input type="text" name="school_year" class="form-control form-control-sm" value="2026-2027" required>
                    </div>

                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="bi bi-save me-1"></i> Create Team Entity
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
