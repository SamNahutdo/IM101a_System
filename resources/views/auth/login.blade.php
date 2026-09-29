<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - Falcon Portal</title>
    <!-- Google Font: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --sidebar-bg: #20295B;
            --coral: #FF6F59;
            --coral-gradient: linear-gradient(135deg, #FF6F59 0%, #FF533B 100%);
            --coral-shadow: 0 10px 24px rgba(255, 111, 89, 0.32);
            --border-color: #E2E8F0;
        }

        * {
            box-sizing: border-box;
        }

        body {
            background: radial-gradient(circle at top center, #26336F 0%, #171E45 100%);
            min-height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            padding: 2rem 1.5rem;
            color: #1E293B;
        }

        .login-card {
            background: #FFFFFF;
            border-radius: 26px;
            box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.45);
            width: 100%;
            max-width: 440px;
            padding: 2.75rem 2.25rem 2.25rem;
            position: relative;
        }

        .login-logo-wrap {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 1.25rem;
        }

        .login-logo-wrap img {
            max-height: 90px;
            max-width: 100%;
            height: auto;
            width: auto;
            object-fit: contain;
            filter: drop-shadow(0 6px 14px rgba(0, 0, 0, 0.15));
            transition: transform 0.25s ease;
        }

        .login-logo-wrap img:hover {
            transform: scale(1.05);
        }

        .form-label-custom {
            font-size: 0.82rem;
            font-weight: 700;
            color: #475569;
            margin-bottom: 0.4rem;
        }

        .input-group-custom {
            display: flex;
            align-items: center;
            background: #F8FAFC;
            border: 1.5px solid var(--border-color);
            border-radius: 14px;
            padding: 0.2rem 0.85rem;
            transition: all 0.2s ease;
        }

        .input-group-custom:focus-within {
            border-color: var(--coral);
            background: #FFFFFF;
            box-shadow: 0 0 0 3px rgba(255, 111, 89, 0.12);
        }

        .input-group-custom i {
            color: #94A3B8;
            font-size: 1.1rem;
            margin-right: 0.65rem;
        }

        .input-group-custom input {
            border: none;
            background: transparent;
            padding: 0.65rem 0;
            font-size: 0.92rem;
            color: #1E293B;
            width: 100%;
            outline: none;
            font-family: inherit;
        }

        .btn-signin {
            background: var(--coral-gradient);
            border: none;
            border-radius: 9999px;
            padding: 0.85rem 1.5rem;
            font-size: 0.95rem;
            font-weight: 700;
            color: #FFFFFF;
            box-shadow: var(--coral-shadow);
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .btn-signin:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 28px rgba(255, 111, 89, 0.42);
            color: #FFFFFF;
        }

        /* QUICK DEMO LOGINS WIDGET */
        .demo-box {
            background: #F8FAFC;
            border: 1px solid var(--border-color);
            border-radius: 18px;
            padding: 1.15rem;
            margin-top: 1.75rem;
        }

        .demo-pill {
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 700;
            padding: 0.4rem 0.85rem;
            border: 1px solid transparent;
            cursor: pointer;
            transition: all 0.15s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .demo-pill:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
        }

        .demo-pill-admin {
            background: #E0F2FE;
            color: #0369A1;
            border-color: #BAE6FD;
        }

        .demo-pill-staff {
            background: #DCFCE7;
            color: #15803D;
            border-color: #BBF7D0;
        }

        .demo-pill-coach {
            background: #FEF3C7;
            color: #B45309;
            border-color: #FDE68A;
        }

        .demo-pill-student {
            background: #EDE9FE;
            color: #6D28D9;
            border-color: #DDD6FE;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <!-- LOGO ONLY (Centered and clean) -->
        <div class="login-logo-wrap">
            <img src="{{ asset('images/logo.png') }}" alt="Logo">
        </div>

        <div class="text-center mb-4">
            <span class="badge rounded-pill px-3 py-1" style="background: #EEF2F8; color: #475569; font-weight: 700; font-size: 0.72rem; letter-spacing: 0.05em; text-transform: uppercase;">
                Portal Access
            </span>
        </div>

        <!-- NOTIFICATIONS -->
        @if (session('success'))
            <div class="alert alert-success py-2 px-3 small mb-3 rounded-3 border-0 d-flex align-items-center gap-2" style="background: #DCFCE7; color: #15803D;">
                <i class="bi bi-check-circle-fill"></i>
                <div>{{ session('success') }}</div>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger py-2 px-3 small mb-3 rounded-3 border-0" style="background: #FEE2E2; color: #991B1B;">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- FORM -->
        <form action="{{ route('login.post') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label-custom">Username</label>
                <div class="input-group-custom">
                    <i class="bi bi-person"></i>
                    <input type="text" id="username" name="username" value="{{ old('username') }}" placeholder="Enter username" required autofocus>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label-custom">Password</label>
                <div class="input-group-custom">
                    <i class="bi bi-lock"></i>
                    <input type="password" id="password" name="password" placeholder="Enter password" required>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="form-check">
                    <input type="checkbox" name="remember" class="form-check-input" id="remember" style="cursor: pointer;">
                    <label class="form-check-label small text-muted" for="remember" style="cursor: pointer; font-size: 0.82rem;">Remember me</label>
                </div>
                <span class="small fw-semibold" style="color: #94A3B8; font-size: 0.8rem;">Defense Mode</span>
            </div>

            <button type="submit" class="btn-signin">
                <span>Sign In to Portal</span>
                <i class="bi bi-arrow-right"></i>
            </button>
        </form>

        <!-- 1-CLICK DEMO ACCOUNTS FOR DEFENSE -->
        <div class="demo-box">
            <div class="d-flex align-items-center gap-2 mb-2 text-dark fw-bold" style="font-size: 0.76rem; text-transform: uppercase; letter-spacing: 0.04em;">
                <i class="bi bi-lightning-charge-fill text-warning"></i> Quick Defense Logins:
            </div>
            <div class="d-flex flex-wrap gap-2">
                <button type="button" class="demo-pill demo-pill-admin" onclick="fillCredentials('admin', 'password123')">
                    👑 Admin (Ma'am Arbe)
                </button>
                <button type="button" class="demo-pill demo-pill-staff" onclick="fillCredentials('staff1', 'password123')">
                    📦 Staff (Custodian John)
                </button>
                <button type="button" class="demo-pill demo-pill-coach" onclick="fillCredentials('coach_baseball', 'password123')">
                    ⚾ Coach (Michael Reyes)
                </button>
                <button type="button" class="demo-pill demo-pill-student" onclick="fillCredentials('athlete_juan', 'password123')">
                    🏃 Student (Juan Dela Cruz)
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
</body>
</html>
