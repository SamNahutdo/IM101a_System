<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - FalconSystem Athletic Resource Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            padding: 1.5rem;
        }
        .login-card {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3), 0 10px 10px -5px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 440px;
            overflow: hidden;
        }
        .login-header {
            background: #0f172a;
            color: #ffffff;
            padding: 2rem;
            text-align: center;
        }
        .login-body {
            padding: 2rem;
        }
        .demo-box {
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            border-radius: 8px;
            padding: 1rem;
            margin-top: 1.5rem;
            font-size: 0.8rem;
        }
        .demo-btn {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            margin-right: 0.25rem;
            margin-top: 0.25rem;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="login-header">
            <div class="d-inline-flex align-items-center justify-content-center bg-warning text-dark rounded-circle mb-2" style="width: 48px; height: 48px; font-size: 1.5rem;">
                <i class="bi bi-shield-shaded"></i>
            </div>
            <h4 class="fw-bold mb-0 text-white">FALCON<span class="text-warning">SYSTEM</span></h4>
            <p class="text-secondary small mb-0">Athletic Equipment Services & Resource Management</p>
            <div class="badge bg-primary mt-2">Advanced Database Systems Project</div>
        </div>

        <div class="login-body">
            @if (session('success'))
                <div class="alert alert-success py-2 small mb-3">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger py-2 small mb-3">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold text-secondary small">Username</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                        <input type="text" id="username" name="username" class="form-control" value="{{ old('username') }}" placeholder="Enter username" required autofocus>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold text-secondary small">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password" id="password" name="password" class="form-control" placeholder="Enter password" required>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="form-check">
                        <input type="checkbox" name="remember" class="form-check-input" id="remember">
                        <label class="form-check-label small text-muted" for="remember">Remember me</label>
                    </div>
                    <span class="small text-muted">Course Defense Mode</span>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Sign In to Portal
                </button>
            </form>

            <!-- 1-CLICK DEMO ACCOUNTS FOR DEFENSE -->
            <div class="demo-box">
                <div class="fw-bold text-dark mb-1"><i class="bi bi-person-badge"></i> Quick Defense Logins:</div>
                <div class="d-flex flex-wrap">
                    <button type="button" class="btn btn-outline-primary btn-sm demo-btn" onclick="fillCredentials('admin', 'password123')">
                        👑 Admin (Ma'am Arbe)
                    </button>
                    <button type="button" class="btn btn-outline-success btn-sm demo-btn" onclick="fillCredentials('staff1', 'password123')">
                        📦 Staff (Custodian John)
                    </button>
                    <button type="button" class="btn btn-outline-warning btn-sm demo-btn" onclick="fillCredentials('coach_baseball', 'password123')">
                        ⚾ Coach (Michael Reyes)
                    </button>
                    <button type="button" class="btn btn-outline-info btn-sm demo-btn" onclick="fillCredentials('athlete_juan', 'password123')">
                        🏃 Student (Juan Dela Cruz)
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function fillCredentials(user, pass) {
            document.getElementById('username').value = user;
            document.getElementById('password').value = pass;
        }
    </script>
</body>
</html>
