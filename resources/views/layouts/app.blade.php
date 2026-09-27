<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'FalconSystem') - Athletic Equipment & Resource Management</title>
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --falcon-navy: #0f172a;
            --falcon-blue: #1e3a8a;
            --falcon-gold: #d97706;
            --falcon-accent: #2563eb;
            --falcon-bg: #f8fafc;
            --falcon-card: #ffffff;
            --falcon-border: #e2e8f0;
        }
        body {
            background-color: var(--falcon-bg);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            color: #334155;
            min-height: 100vh;
        }
        .sidebar {
            background-color: var(--falcon-navy);
            color: #f1f5f9;
            min-height: 100vh;
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            padding-top: 1rem;
            z-index: 1000;
        }
        .sidebar-brand {
            padding: 0.75rem 1.25rem;
            font-size: 1.25rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            color: #ffffff;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .sidebar-brand span {
            color: #fbbf24;
        }
        .nav-item-header {
            font-size: 0.72rem;
            text-transform: uppercase;
            font-weight: 700;
            color: #94a3b8;
            padding: 1rem 1.25rem 0.25rem;
            letter-spacing: 0.5px;
        }
        .sidebar-nav {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .sidebar-nav a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 0.65rem 1.25rem;
            color: #cbd5e1;
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.15s ease-in-out;
        }
        .sidebar-nav a:hover, .sidebar-nav a.active {
            color: #ffffff;
            background-color: rgba(255, 255, 255, 0.08);
            border-left: 3px solid #fbbf24;
        }
        .main-content {
            margin-left: 250px;
            padding: 1.5rem 2rem;
        }
        .topbar {
            background: #ffffff;
            border-bottom: 1px solid var(--falcon-border);
            padding: 0.75rem 2rem;
            margin-left: 250px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 999;
        }
        .card-stat {
            background: #ffffff;
            border: 1px solid var(--falcon-border);
            border-radius: 8px;
            padding: 1.25rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .badge-role {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 0.35rem 0.65rem;
            border-radius: 20px;
        }
        .table-responsive {
            background: #ffffff;
            border: 1px solid var(--falcon-border);
            border-radius: 8px;
            overflow: hidden;
        }
        .table {
            margin-bottom: 0;
        }
        .table th {
            background-color: #f1f5f9;
            color: #475569;
            font-size: 0.82rem;
            text-transform: uppercase;
            font-weight: 600;
            border-bottom: 1px solid var(--falcon-border);
        }
        .table td {
            vertical-align: middle;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

    <!-- SIDEBAR -->
    <aside class="sidebar">
        <div class="sidebar-brand">
            <i class="bi bi-shield-shaded text-warning"></i>
            <div>
                FALCON<span>SYSTEM</span>
                <div style="font-size: 0.65rem; font-weight: normal; color: #94a3b8;">Athletics IM101</div>
            </div>
        </div>

        @php
            $currentRole = strtolower(auth()->user()->role->name ?? '');
        @endphp

        <ul class="sidebar-nav mt-2">
            <!-- COMMON / ROLE DASHBOARD -->
            <li class="nav-item-header">Main</li>
            @if ($currentRole === 'admin')
                <li><a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
            @elseif ($currentRole === 'staff')
                <li><a href="{{ route('staff.dashboard') }}" class="{{ request()->routeIs('staff.dashboard') ? 'active' : '' }}"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
            @elseif ($currentRole === 'coach')
                <li><a href="{{ route('coach.dashboard') }}" class="{{ request()->routeIs('coach.dashboard') ? 'active' : '' }}"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
            @elseif ($currentRole === 'student')
                <li><a href="{{ route('student.dashboard') }}" class="{{ request()->routeIs('student.dashboard') ? 'active' : '' }}"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
            @endif

            <!-- ADMIN PORTAL LINKS -->
            @if ($currentRole === 'admin')
                <li class="nav-item-header">Administration</li>
                <li><a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}"><i class="bi bi-people"></i> Users & Roles</a></li>
                <li><a href="{{ route('admin.athletes.index') }}" class="{{ request()->routeIs('admin.athletes.*') ? 'active' : '' }}"><i class="bi bi-person-badge"></i> Athletes</a></li>
                <li><a href="{{ route('admin.coaches.index') }}" class="{{ request()->routeIs('admin.coaches.*') ? 'active' : '' }}"><i class="bi bi-whistle"></i> Coaches</a></li>
                <li><a href="{{ route('admin.sports.index') }}" class="{{ request()->routeIs('admin.sports.*') ? 'active' : '' }}"><i class="bi bi-trophy"></i> Sports</a></li>
                <li><a href="{{ route('admin.teams.index') }}" class="{{ request()->routeIs('admin.teams.*') ? 'active' : '' }}"><i class="bi bi-diagram-3"></i> Teams (N:M)</a></li>

                <li class="nav-item-header">Equipment & Operations</li>
                <li><a href="{{ route('admin.equipment.index') }}" class="{{ request()->routeIs('admin.equipment.*') ? 'active' : '' }}"><i class="bi bi-box-seam"></i> Equipment Master</a></li>
                <li><a href="{{ route('admin.borrowing.index') }}" class="{{ request()->routeIs('admin.borrowing.*') ? 'active' : '' }}"><i class="bi bi-arrow-left-right"></i> Borrowing (N:M)</a></li>
                <li><a href="{{ route('admin.maintenance.index') }}" class="{{ request()->routeIs('admin.maintenance.*') ? 'active' : '' }}"><i class="bi bi-tools"></i> Maintenance</a></li>
                
                <li class="nav-item-header">Database & Audit</li>
                <li><a href="{{ route('admin.audit.index') }}" class="{{ request()->routeIs('admin.audit.*') ? 'active' : '' }}"><i class="bi bi-journal-text"></i> Audit Logs (Triggers)</a></li>
                <li><a href="{{ route('admin.reports.index') }}" class="{{ request()->routeIs('admin.reports.*') ? 'active' : '' }}"><i class="bi bi-file-earmark-bar-graph"></i> Reports (Views)</a></li>
            @endif

            <!-- STAFF PORTAL LINKS -->
            @if ($currentRole === 'staff')
                <li class="nav-item-header">Equipment Desk</li>
                <li><a href="{{ route('staff.equipment.index') }}" class="{{ request()->routeIs('staff.equipment.*') ? 'active' : '' }}"><i class="bi bi-box-seam"></i> Equipment Inventory</a></li>
                <li><a href="{{ route('staff.borrowing.create') }}" class="{{ request()->routeIs('staff.borrowing.create') ? 'active' : '' }}"><i class="bi bi-cart-plus"></i> New Borrowing (SP)</a></li>
                <li><a href="{{ route('staff.borrowing.index') }}" class="{{ request()->routeIs('staff.borrowing.index') ? 'active' : '' }}"><i class="bi bi-arrow-left-right"></i> Active Loans & Returns</a></li>
                <li><a href="{{ route('staff.maintenance.index') }}" class="{{ request()->routeIs('staff.maintenance.*') ? 'active' : '' }}"><i class="bi bi-tools"></i> Maintenance Queue</a></li>
            @endif

            <!-- COACH PORTAL LINKS -->
            @if ($currentRole === 'coach')
                <li class="nav-item-header">Team Management</li>
                <li><a href="{{ route('coach.teams.index') }}" class="{{ request()->routeIs('coach.teams.*') ? 'active' : '' }}"><i class="bi bi-diagram-3"></i> My Teams</a></li>
                <li><a href="{{ route('coach.athletes.index') }}" class="{{ request()->routeIs('coach.athletes.*') ? 'active' : '' }}"><i class="bi bi-person-badge"></i> Team Athletes</a></li>
                <li><a href="{{ route('coach.equipment.index') }}" class="{{ request()->routeIs('coach.equipment.*') ? 'active' : '' }}"><i class="bi bi-box-seam"></i> View Equipment</a></li>
                <li><a href="{{ route('coach.borrowing.index') }}" class="{{ request()->routeIs('coach.borrowing.*') ? 'active' : '' }}"><i class="bi bi-arrow-left-right"></i> Equipment Requests</a></li>
                <li><a href="{{ route('coach.maintenance.index') }}" class="{{ request()->routeIs('coach.maintenance.*') ? 'active' : '' }}"><i class="bi bi-exclamation-triangle"></i> Report Damage</a></li>
            @endif

            <!-- STUDENT / ATHLETE LINKS -->
            @if ($currentRole === 'student')
                <li class="nav-item-header">Student Athlete</li>
                <li><a href="{{ route('student.equipment.index') }}" class="{{ request()->routeIs('student.equipment.*') ? 'active' : '' }}"><i class="bi bi-box-seam"></i> Available Equipment</a></li>
                <li><a href="{{ route('student.borrowing.create') }}" class="{{ request()->routeIs('student.borrowing.create') ? 'active' : '' }}"><i class="bi bi-hand-index-thumb"></i> Request Equipment</a></li>
                <li><a href="{{ route('student.borrowing.index') }}" class="{{ request()->routeIs('student.borrowing.index') ? 'active' : '' }}"><i class="bi bi-clock-history"></i> My Borrowings</a></li>
                <li><a href="{{ route('student.damage.create') }}" class="{{ request()->routeIs('student.damage.create') ? 'active' : '' }}"><i class="bi bi-exclamation-octagon"></i> Report Damage</a></li>
            @endif
        </ul>
    </aside>

    <!-- TOPBAR -->
    <header class="topbar">
        <div>
            <h5 class="mb-0 text-dark fw-bold">@yield('page_title', 'Dashboard')</h5>
            <small class="text-muted">Falcon Athletics Equipment & Resource System</small>
        </div>
        <div class="d-flex align-items-center gap-3">
            <span class="badge bg-primary badge-role">
                {{ strtoupper(auth()->user()->role->name ?? 'User') }}
            </span>
            <div class="text-end">
                <div class="fw-semibold text-dark" style="font-size: 0.9rem;">
                    {{ auth()->user()->displayName }}
                </div>
                <small class="text-muted" style="font-size: 0.75rem;">
                    {{ auth()->user()->email }}
                </small>
            </div>
            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-outline-danger btn-sm" title="Sign out">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </button>
            </form>
        </div>
    </header>

    <!-- MAIN BODY CONTENT -->
    <main class="main-content">
        <!-- Flash Messages -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <div class="fw-bold mb-1"><i class="bi bi-x-circle-fill me-2"></i> Please fix the following errors:</div>
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
