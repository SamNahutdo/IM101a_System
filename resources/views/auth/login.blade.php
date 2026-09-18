@extends('layouts.auth')

@section('title', 'FalconSystem — Sign In')

@section('content')
<div class="auth-card">
    <div class="auth-header">
        <div class="sidebar-logo" style="margin: 0 auto 16px; width: 48px; height: 48px; font-size: 1.5rem;">FS</div>
        <h2>FalconSystem</h2>
        <p>Athletic Equipment Services & Resource Management</p>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-error">
            <ul style="list-style: none;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('login.post') }}" method="POST" id="loginForm">
        @csrf
        <div class="form-group">
            <label class="form-label" for="username">Username or System ID</label>
            <input type="text" name="username" id="username" class="form-control" value="{{ old('username') }}" required autofocus placeholder="Enter your username">
        </div>

        <div class="form-group">
            <label class="form-label" for="password">Password</label>
            <input type="password" name="password" id="password" class="form-control" required placeholder="••••••••">
        </div>

        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; font-size: 0.85rem;">
            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; color: var(--slate-600);">
                <input type="checkbox" name="remember">
                <span>Remember me</span>
            </label>
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; font-size: 0.95rem;">
            Sign In to Portal
        </button>
    </form>

    <!-- Quick Role Demo Switcher for Evaluation/Defense -->
    <div style="margin-top: 32px; padding-top: 24px; border-top: 1px solid var(--slate-200);">
        <p style="font-size: 0.75rem; text-transform: uppercase; font-weight: 700; color: var(--slate-400); text-align: center; margin-bottom: 12px; letter-spacing: 0.5px;">
            Demo Accounts for Course Defense
        </p>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
            <button type="button" class="btn btn-secondary btn-sm" onclick="fillCredentials('admin', 'password123')">
                🛡️ Admin
            </button>
            <button type="button" class="btn btn-secondary btn-sm" onclick="fillCredentials('staff1', 'password123')">
                📦 Staff (Marcus)
            </button>
            <button type="button" class="btn btn-secondary btn-sm" onclick="fillCredentials('coach_carter', 'password123')">
                📋 Coach (Carter)
            </button>
            <button type="button" class="btn btn-secondary btn-sm" onclick="fillCredentials('athlete1', 'password123')">
                🏃 Athlete (James)
            </button>
        </div>
    </div>
</div>

<script>
    function fillCredentials(user, pass) {
        document.getElementById('username').value = user;
        document.getElementById('password').value = pass;
    }
</script>
@endsection
