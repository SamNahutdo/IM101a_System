<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'FalconSystem') - Athletic Equipment & Resource Management</title>
    <!-- Google Font: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    <style>
        :root {
            --bg-body: #F4F6FC;
            --sidebar-bg: #20295B;
            --sidebar-active: #354280;
            --sidebar-hover: rgba(255, 255, 255, 0.08);
            --sidebar-text: #9EABCE;
            --sidebar-header: #6D7CA7;
            --coral: #FF6F59;
            --coral-hover: #f55a42;
            --coral-gradient: linear-gradient(135deg, #FF6F59 0%, #FF533B 100%);
            --coral-shadow: 0 10px 24px rgba(255, 111, 89, 0.28);
            --cyan-gradient: linear-gradient(135deg, #0EA5E9 0%, #0284C7 100%);
            --green-gradient: linear-gradient(135deg, #10B981 0%, #059669 100%);
            --indigo-gradient: linear-gradient(135deg, #6366F1 0%, #4F46E5 100%);
            --amber-gradient: linear-gradient(135deg, #F59E0B 0%, #D97706 100%);
            --card-radius: 20px;
            --card-shadow: 0 8px 24px rgba(26, 40, 95, 0.04);
            --border-soft: #E2E8F0;
        }

        * {
            box-sizing: border-box;
        }

        body {
            background-color: var(--bg-body);
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            color: #1E293B;
            min-height: 100vh;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }

        /* SIDEBAR */
        .sidebar {
            background-color: var(--sidebar-bg);
            color: #FFFFFF;
            min-height: 100vh;
            width: 260px;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1050;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 1.5rem 0 1rem;
            box-shadow: 4px 0 20px rgba(15, 23, 42, 0.08);
            overflow-y: auto;
        }

        .sidebar-brand {
            padding: 0 1.25rem 1.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .sidebar-brand img {
            max-height: 70px;
            max-width: 100%;
            height: auto;
            width: auto;
            object-fit: contain;
            filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.35));
            transition: transform 0.25s ease;
        }

        .sidebar-brand img:hover {
            transform: scale(1.05);
        }

        .sidebar-section-title {
            font-size: 0.72rem;
            text-transform: uppercase;
            font-weight: 700;
            color: var(--sidebar-header);
            padding: 1.1rem 1.5rem 0.35rem;
            letter-spacing: 0.06em;
        }

        .sidebar-nav {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar-nav li a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0.65rem 1.15rem;
            margin: 0.2rem 1rem;
            color: var(--sidebar-text);
            text-decoration: none;
            font-size: 0.88rem;
            font-weight: 500;
            border-radius: 12px;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sidebar-nav li a i {
            font-size: 1.15rem;
            width: 22px;
            text-align: center;
        }

        .sidebar-nav li a:hover {
            color: #FFFFFF;
            background-color: var(--sidebar-hover);
            transform: translateX(3px);
        }

        .sidebar-nav li a.active {
            color: #FFFFFF;
            background-color: var(--sidebar-active);
            font-weight: 600;
            box-shadow: 0 4px 14px rgba(15, 23, 42, 0.25);
        }

        /* BOTTOM USER CARD */
        .sidebar-profile-card {
            background: var(--coral-gradient);
            margin: 1.25rem 1rem 0.5rem;
            padding: 0.75rem 1rem;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: var(--coral-shadow);
            color: #FFFFFF;
            text-decoration: none;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .sidebar-profile-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 28px rgba(255, 111, 89, 0.38);
            color: #FFFFFF;
        }

        .profile-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.25);
            border: 2px solid #FFFFFF;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 0.85rem;
            color: #FFFFFF;
            flex-shrink: 0;
        }

        /* TOPBAR */
        .topbar {
            margin-left: 260px;
            padding: 1.5rem 2.25rem 0.75rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: transparent;
        }

        .topbar-search-wrap {
            position: relative;
            width: 360px;
            max-width: 100%;
        }

        .topbar-search-input {
            width: 100%;
            background: #FFFFFF;
            border: 1px solid var(--border-soft);
            border-radius: 9999px;
            padding: 0.65rem 2.85rem 0.65rem 1.35rem;
            font-size: 0.88rem;
            color: #1E293B;
            outline: none;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
            transition: all 0.2s ease;
        }

        .topbar-search-input:focus {
            border-color: var(--coral);
            box-shadow: 0 0 0 3px rgba(255, 111, 89, 0.15);
        }

        .topbar-search-icon {
            position: absolute;
            right: 1.25rem;
            top: 50%;
            transform: translateY(-50%);
            color: #94A3B8;
            font-size: 1rem;
            pointer-events: none;
        }

        .topbar-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .topbar-circle-btn {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: #FFFFFF;
            border: 1px solid var(--border-soft);
            color: #475569;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
            transition: all 0.2s ease;
            position: relative;
        }

        .topbar-circle-btn:hover {
            background: #F8FAFC;
            color: #1E293B;
            transform: translateY(-1px);
        }

        .topbar-badge-dot {
            position: absolute;
            top: 11px;
            right: 11px;
            width: 8px;
            height: 8px;
            background-color: var(--coral);
            border-radius: 50%;
            border: 2px solid #FFFFFF;
        }

        .topbar-logout-btn {
            background: #FFFFFF;
            border: 1px solid #FFE4DF;
            color: var(--coral);
            border-radius: 9999px;
            padding: 0.65rem 1.35rem;
            font-size: 0.88rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
            transition: all 0.2s ease;
        }

        .topbar-logout-btn:hover {
            background: var(--coral);
            color: #FFFFFF;
            border-color: var(--coral);
            box-shadow: var(--coral-shadow);
            transform: translateY(-1px);
        }

        /* MAIN CONTENT */
        .main-content {
            margin-left: 260px;
            padding: 1.25rem 2.25rem 3rem;
        }

        /* MODERN CARDS & DESIGN SYSTEM */
        .card-modern {
            background: #FFFFFF;
            border-radius: var(--card-radius);
            border: 1px solid rgba(226, 232, 240, 0.8);
            box-shadow: var(--card-shadow);
            padding: 1.5rem;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card-stat-modern {
            background: #FFFFFF;
            border-radius: var(--card-radius);
            border: 1px solid rgba(226, 232, 240, 0.8);
            box-shadow: var(--card-shadow);
            padding: 1.35rem 1.25rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card-stat-modern:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 28px rgba(26, 40, 95, 0.08);
        }

        .stat-icon-circle {
            width: 54px;
            height: 54px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
            color: #FFFFFF;
            margin-bottom: 0.85rem;
            flex-shrink: 0;
        }

        .stat-icon-coral {
            background: var(--coral-gradient);
            box-shadow: 0 8px 18px rgba(255, 111, 89, 0.35);
        }

        .stat-icon-cyan {
            background: var(--cyan-gradient);
            box-shadow: 0 8px 18px rgba(14, 165, 233, 0.35);
        }

        .stat-icon-green {
            background: var(--green-gradient);
            box-shadow: 0 8px 18px rgba(16, 185, 129, 0.35);
        }

        .stat-icon-indigo {
            background: var(--indigo-gradient);
            box-shadow: 0 8px 18px rgba(99, 102, 241, 0.35);
        }

        .stat-icon-amber {
            background: var(--amber-gradient);
            box-shadow: 0 8px 18px rgba(245, 158, 11, 0.35);
        }

        .stat-label {
            font-size: 0.85rem;
            font-weight: 700;
            color: #475569;
            margin-bottom: 0.15rem;
        }

        .stat-trend {
            font-size: 0.72rem;
            font-weight: 600;
            color: #94A3B8;
            margin-bottom: 0.65rem;
        }

        .stat-value {
            font-size: 1.85rem;
            font-weight: 800;
            letter-spacing: -0.5px;
            line-height: 1;
            margin: 0;
        }

        /* TABLES */
        .modern-table-card {
            background: #FFFFFF;
            border-radius: var(--card-radius);
            border: 1px solid rgba(226, 232, 240, 0.8);
            box-shadow: var(--card-shadow);
            overflow: hidden;
        }

        .modern-table-header-bar {
            background: var(--sidebar-bg);
            color: #FFFFFF;
            border-radius: 12px;
            padding: 0.75rem 1rem;
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .table-modern {
            width: 100%;
            margin-bottom: 0;
        }

        .table-modern th {
            background-color: var(--sidebar-bg);
            color: #FFFFFF;
            font-size: 0.8rem;
            font-weight: 700;
            padding: 0.85rem 1.25rem;
            border: none;
        }

        .table-modern th:first-child {
            border-top-left-radius: 12px;
            border-bottom-left-radius: 12px;
        }

        .table-modern th:last-child {
            border-top-right-radius: 12px;
            border-bottom-right-radius: 12px;
        }

        .table-modern td {
            padding: 1rem 1.25rem;
            vertical-align: middle;
            border-bottom: 1px solid #F1F5F9;
            font-size: 0.9rem;
        }

        .table-modern tr:last-child td {
            border-bottom: none;
        }

        /* PILLS & BADGES */
        .pill-soft {
            border-radius: 9999px;
            font-weight: 700;
            font-size: 0.75rem;
            padding: 0.35rem 0.85rem;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .pill-coral {
            background: #FFEBE7;
            color: var(--coral);
        }

        .pill-green {
            background: #DCFCE7;
            color: #15803D;
        }

        .pill-blue {
            background: #E0F2FE;
            color: #0369A1;
        }

        .pill-amber {
            background: #FEF3C7;
            color: #B45309;
        }

        .pill-btn {
            border-radius: 9999px;
            padding: 0.45rem 1.15rem;
            font-size: 0.82rem;
            font-weight: 700;
            border: 1px solid var(--border-soft);
            background: #FFFFFF;
            color: #475569;
            text-decoration: none;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .pill-btn:hover {
            border-color: var(--coral);
            color: var(--coral);
            background: #FFF7F5;
        }

        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            .sidebar.show {
                transform: translateX(0);
            }
            .topbar, .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>

    <!-- SIDEBAR -->
    <aside class="sidebar">
        <div>
            <!-- BRAND / LOGO ONLY -->
            <div class="sidebar-brand">
                <a href="{{ url('/') }}" class="d-inline-flex justify-content-center align-items-center">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="sidebar-logo-img">
                </a>
            </div>

            @php
                $currentRole = strtolower(auth()->user()->role->name ?? '');
            @endphp

            <ul class="sidebar-nav mt-3">
                <!-- MAIN MENU -->
                <div class="sidebar-section-title">Menu</div>
                @if ($currentRole === 'admin')
                    <li><a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><i class="bi bi-grid-fill"></i> Dashboard</a></li>
                @elseif ($currentRole === 'staff')
                    <li><a href="{{ route('staff.dashboard') }}" class="{{ request()->routeIs('staff.dashboard') ? 'active' : '' }}"><i class="bi bi-grid-fill"></i> Dashboard</a></li>
                @elseif ($currentRole === 'coach')
                    <li><a href="{{ route('coach.dashboard') }}" class="{{ request()->routeIs('coach.dashboard') ? 'active' : '' }}"><i class="bi bi-grid-fill"></i> Dashboard</a></li>
                @elseif ($currentRole === 'student')
                    <li><a href="{{ route('student.dashboard') }}" class="{{ request()->routeIs('student.dashboard') ? 'active' : '' }}"><i class="bi bi-grid-fill"></i> Dashboard</a></li>
                @endif

                <!-- ADMIN PORTAL LINKS -->
                @if ($currentRole === 'admin')
                    <div class="sidebar-section-title">Management</div>
                    <li><a href="{{ route('admin.equipment.index') }}" class="{{ request()->routeIs('admin.equipment.*') ? 'active' : '' }}"><i class="bi bi-box-seam"></i> Equipment Stock</a></li>
                    <li><a href="{{ route('admin.borrowing.index') }}" class="{{ request()->routeIs('admin.borrowing.*') ? 'active' : '' }}"><i class="bi bi-arrow-left-right"></i> Borrowing (N:M)</a></li>
                    <li><a href="{{ route('admin.maintenance.index') }}" class="{{ request()->routeIs('admin.maintenance.*') ? 'active' : '' }}"><i class="bi bi-tools"></i> Maintenance</a></li>
                    <li><a href="{{ route('admin.athletes.index') }}" class="{{ request()->routeIs('admin.athletes.*') ? 'active' : '' }}"><i class="bi bi-people"></i> Athletes</a></li>
                    <li><a href="{{ route('admin.coaches.index') }}" class="{{ request()->routeIs('admin.coaches.*') ? 'active' : '' }}"><i class="bi bi-whistle"></i> Coaches</a></li>
                    <li><a href="{{ route('admin.teams.index') }}" class="{{ request()->routeIs('admin.teams.*') ? 'active' : '' }}"><i class="bi bi-diagram-3"></i> Teams</a></li>

                    <div class="sidebar-section-title">Database & Records</div>
                    <li><a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}"><i class="bi bi-shield-lock"></i> Users & Roles</a></li>
                    <li><a href="{{ route('admin.audit.index') }}" class="{{ request()->routeIs('admin.audit.*') ? 'active' : '' }}"><i class="bi bi-journal-text"></i> Audit Logs</a></li>
                    <li><a href="{{ route('admin.reports.index') }}" class="{{ request()->routeIs('admin.reports.*') ? 'active' : '' }}"><i class="bi bi-file-earmark-bar-graph"></i> Reports (Views)</a></li>
                @endif

                <!-- STAFF PORTAL LINKS -->
                @if ($currentRole === 'staff')
                    <div class="sidebar-section-title">Equipment Desk</div>
                    <li><a href="{{ route('staff.equipment.index') }}" class="{{ request()->routeIs('staff.equipment.*') ? 'active' : '' }}"><i class="bi bi-box-seam"></i> Equipment Inventory</a></li>
                    <li><a href="{{ route('staff.borrowing.create') }}" class="{{ request()->routeIs('staff.borrowing.create') ? 'active' : '' }}"><i class="bi bi-cart-plus"></i> New Borrowing (SP)</a></li>
                    <li><a href="{{ route('staff.borrowing.index') }}" class="{{ request()->routeIs('staff.borrowing.index') ? 'active' : '' }}"><i class="bi bi-arrow-left-right"></i> Active Loans & Returns</a></li>
                    <li><a href="{{ route('staff.maintenance.index') }}" class="{{ request()->routeIs('staff.maintenance.*') ? 'active' : '' }}"><i class="bi bi-tools"></i> Maintenance Queue</a></li>
                @endif

                <!-- COACH PORTAL LINKS -->
                @if ($currentRole === 'coach')
                    <div class="sidebar-section-title">Team Management</div>
                    <li><a href="{{ route('coach.teams.index') }}" class="{{ request()->routeIs('coach.teams.*') ? 'active' : '' }}"><i class="bi bi-diagram-3"></i> My Teams</a></li>
                    <li><a href="{{ route('coach.athletes.index') }}" class="{{ request()->routeIs('coach.athletes.*') ? 'active' : '' }}"><i class="bi bi-people"></i> Team Athletes</a></li>
                    <li><a href="{{ route('coach.equipment.index') }}" class="{{ request()->routeIs('coach.equipment.*') ? 'active' : '' }}"><i class="bi bi-box-seam"></i> View Equipment</a></li>
                    <li><a href="{{ route('coach.borrowing.index') }}" class="{{ request()->routeIs('coach.borrowing.*') ? 'active' : '' }}"><i class="bi bi-arrow-left-right"></i> Equipment Requests</a></li>
                    <li><a href="{{ route('coach.maintenance.index') }}" class="{{ request()->routeIs('coach.maintenance.*') ? 'active' : '' }}"><i class="bi bi-exclamation-triangle"></i> Report Damage</a></li>
                @endif

                <!-- STUDENT / ATHLETE LINKS -->
                @if ($currentRole === 'student')
                    <div class="sidebar-section-title">Student Athlete</div>
                    <li><a href="{{ route('student.equipment.index') }}" class="{{ request()->routeIs('student.equipment.*') ? 'active' : '' }}"><i class="bi bi-box-seam"></i> Available Equipment</a></li>
                    <li><a href="{{ route('student.borrowing.create') }}" class="{{ request()->routeIs('student.borrowing.create') ? 'active' : '' }}"><i class="bi bi-hand-index-thumb"></i> Request Loan</a></li>
                    <li><a href="{{ route('student.borrowing.index') }}" class="{{ request()->routeIs('student.borrowing.index') ? 'active' : '' }}"><i class="bi bi-clock-history"></i> My Borrowings</a></li>
                    <li><a href="{{ route('student.damage.create') }}" class="{{ request()->routeIs('student.damage.create') ? 'active' : '' }}"><i class="bi bi-exclamation-octagon"></i> Report Damage</a></li>
                @endif
            </ul>
        </div>

        <!-- BOTTOM USER PROFILE CARD (Matches AR Jakir Widget) -->
        <div>
            <div class="sidebar-profile-card">
                <div class="d-flex align-items-center gap-2 overflow-hidden">
                    <div class="profile-avatar">
                        {{ auth()->user()->initials ?? 'U' }}
                    </div>
                    <div class="overflow-hidden">
                        <div class="text-truncate fw-bold" style="font-size: 0.88rem;">{{ auth()->user()->displayName }}</div>
                        <div class="text-truncate" style="font-size: 0.72rem; color: rgba(255, 255, 255, 0.85);">{{ ucfirst($currentRole) }}</div>
                    </div>
                </div>
                <div>
                    <i class="bi bi-chevron-down" style="font-size: 0.75rem;"></i>
                </div>
            </div>
        </div>
    </aside>

    <!-- TOPBAR -->
    <header class="topbar">
        <!-- SEARCH BAR (Matching screenshot) -->
        <div class="topbar-search-wrap">
            <input type="text" class="topbar-search-input" placeholder="Search equipment, transactions, athletes...">
            <i class="bi bi-search topbar-search-icon"></i>
        </div>

        <!-- RIGHT ACTIONS -->
        <div class="topbar-actions">
            <!-- Logout Pill Button -->
            <form action="{{ route('logout') }}" method="POST" class="d-inline m-0">
                @csrf
                <button type="submit" class="topbar-logout-btn" title="Sign out">
                    <i class="bi bi-box-arrow-right"></i> Log Out
                </button>
            </form>
        </div>
    </header>

    <!-- MAIN BODY CONTENT -->
    <main class="main-content">
        <!-- Flash Messages -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm py-3 px-4 border-0 mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2 text-success"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show rounded-4 shadow-sm py-3 px-4 border-0 mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2 text-danger"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show rounded-4 shadow-sm py-3 px-4 border-0 mb-4" role="alert">
                <div class="fw-bold mb-1"><i class="bi bi-x-circle-fill me-2"></i> Please check the following:</div>
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
