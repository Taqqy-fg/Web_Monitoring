<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — WebMonitor Pro</title>
    <meta name="description" content="Login ke WebMonitor Pro untuk memantau performa dan uptime website Anda secara real-time.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    <style>
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --primary-glow: rgba(99, 102, 241, 0.4);
            --success: #10b981;
            --danger: #ef4444;
            --bg-deep: #0a0a1a;
            --bg-card: rgba(255, 255, 255, 0.05);
            --border: rgba(255, 255, 255, 0.1);
            --text-primary: #f1f5f9;
            --text-muted: #94a3b8;
        }

        html, body {
            height: 100%;
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-deep);
            color: var(--text-primary);
            overflow: hidden;
        }

        /* ============================================================
           ANIMATED BACKGROUND
        ============================================================ */
        .bg-scene {
            position: fixed;
            inset: 0;
            z-index: 0;
            background: radial-gradient(ellipse at 20% 50%, rgba(99, 102, 241, 0.15) 0%, transparent 60%),
                        radial-gradient(ellipse at 80% 20%, rgba(139, 92, 246, 0.12) 0%, transparent 50%),
                        radial-gradient(ellipse at 60% 90%, rgba(16, 185, 129, 0.08) 0%, transparent 50%),
                        linear-gradient(135deg, #0a0a1a 0%, #0f0f2e 50%, #0a0a1a 100%);
        }

        /* Grid overlay */
        .bg-scene::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(99, 102, 241, 0.06) 1px, transparent 1px),
                linear-gradient(90deg, rgba(99, 102, 241, 0.06) 1px, transparent 1px);
            background-size: 60px 60px;
            animation: gridPan 20s linear infinite;
        }

        @keyframes gridPan {
            0% { transform: translate(0, 0); }
            100% { transform: translate(60px, 60px); }
        }

        /* Floating orbs */
        .orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            animation: float linear infinite;
            z-index: 0;
            pointer-events: none;
        }
        .orb-1 {
            width: 400px; height: 400px;
            background: rgba(99, 102, 241, 0.2);
            top: -100px; left: -100px;
            animation-duration: 18s;
        }
        .orb-2 {
            width: 300px; height: 300px;
            background: rgba(139, 92, 246, 0.15);
            bottom: -80px; right: -80px;
            animation-duration: 22s;
            animation-direction: reverse;
        }
        .orb-3 {
            width: 200px; height: 200px;
            background: rgba(16, 185, 129, 0.1);
            top: 50%; right: 20%;
            animation-duration: 15s;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0) scale(1); }
            25% { transform: translate(30px, -30px) scale(1.05); }
            50% { transform: translate(-20px, 20px) scale(0.95); }
            75% { transform: translate(20px, 30px) scale(1.02); }
        }

        /* Particles */
        .particles {
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
        }

        .particle {
            position: absolute;
            width: 2px;
            height: 2px;
            background: rgba(99, 102, 241, 0.6);
            border-radius: 50%;
            animation: particleFloat linear infinite;
        }

        @keyframes particleFloat {
            0% { opacity: 0; transform: translateY(100vh) scale(0); }
            10% { opacity: 1; }
            90% { opacity: 1; }
            100% { opacity: 0; transform: translateY(-100px) scale(1); }
        }

        /* ============================================================
           STATUS INDICATOR (Animated)
        ============================================================ */
        .status-bar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px;
            background: rgba(10, 10, 26, 0.6);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--border);
            font-size: 12px;
            color: var(--text-muted);
        }

        .pulse-dot {
            width: 8px;
            height: 8px;
            background: var(--success);
            border-radius: 50%;
            animation: pulse 2s ease-in-out infinite;
            box-shadow: 0 0 6px var(--success);
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(0.8); }
        }

        /* ============================================================
           LOGIN CONTAINER
        ============================================================ */
        .login-wrapper {
            position: relative;
            z-index: 5;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 24px;
        }

        .login-card {
            width: 100%;
            max-width: 420px;
            background: rgba(255, 255, 255, 0.04);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 24px;
            padding: 40px;
            box-shadow:
                0 0 0 1px rgba(99, 102, 241, 0.1),
                0 25px 50px rgba(0, 0, 0, 0.5),
                0 0 80px rgba(99, 102, 241, 0.08) inset;
            animation: cardSlideIn 0.6s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        @keyframes cardSlideIn {
            from {
                opacity: 0;
                transform: translateY(30px) scale(0.96);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        /* ============================================================
           LOGO & HEADER
        ============================================================ */
        .login-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 8px;
        }

        .logo-icon {
            width: 52px;
            height: 52px;
            background: linear-gradient(135deg, var(--primary), #8b5cf6);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            box-shadow: 0 8px 24px rgba(99, 102, 241, 0.4);
            animation: iconBounce 2s ease-in-out infinite;
        }

        @keyframes iconBounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-4px); }
        }

        .logo-text {
            font-size: 22px;
            font-weight: 700;
            background: linear-gradient(135deg, #f1f5f9, #c7d2fe);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .login-subtitle {
            text-align: center;
            color: var(--text-muted);
            font-size: 14px;
            margin-bottom: 32px;
        }

        .login-title {
            text-align: center;
            font-size: 18px;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 6px;
        }

        /* ============================================================
           ALERT MESSAGES
        ============================================================ */
        .alert {
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 13px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #6ee7b7;
        }

        .alert-danger {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #fca5a5;
        }

        /* ============================================================
           FORM ELEMENTS
        ============================================================ */
        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: var(--text-muted);
            margin-bottom: 8px;
            letter-spacing: 0.3px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 16px;
            pointer-events: none;
            transition: color 0.2s;
        }

        .form-input {
            width: 100%;
            padding: 12px 14px 12px 44px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            color: var(--text-primary);
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            outline: none;
            transition: border-color 0.2s, background 0.2s, box-shadow 0.2s;
        }

        .form-input::placeholder {
            color: rgba(148, 163, 184, 0.5);
        }

        .form-input:focus {
            border-color: var(--primary);
            background: rgba(99, 102, 241, 0.06);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
        }

        .form-input:focus + .input-icon,
        .input-wrapper:focus-within .input-icon {
            color: var(--primary);
        }

        .form-input.is-invalid {
            border-color: var(--danger);
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.15);
        }

        .invalid-feedback {
            color: #fca5a5;
            font-size: 12px;
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        /* Toggle password visibility */
        .toggle-password {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            font-size: 16px;
            padding: 4px;
            transition: color 0.2s;
        }

        .toggle-password:hover {
            color: var(--text-primary);
        }

        /* Password input padding */
        .form-input.has-toggle {
            padding-right: 44px;
        }

        /* ============================================================
           REMEMBER ME
        ============================================================ */
        .remember-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 24px;
        }

        .checkbox-custom {
            width: 18px;
            height: 18px;
            accent-color: var(--primary);
            cursor: pointer;
        }

        .remember-label {
            font-size: 13px;
            color: var(--text-muted);
            cursor: pointer;
            user-select: none;
        }

        /* ============================================================
           SUBMIT BUTTON
        ============================================================ */
        .btn-login {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, var(--primary) 0%, #8b5cf6 100%);
            border: none;
            border-radius: 12px;
            color: #fff;
            font-size: 15px;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            transition: transform 0.15s, box-shadow 0.15s;
            box-shadow: 0 4px 20px rgba(99, 102, 241, 0.4);
        }

        .btn-login::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.15), transparent);
            opacity: 0;
            transition: opacity 0.2s;
        }

        .btn-login:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 30px rgba(99, 102, 241, 0.5);
        }

        .btn-login:hover::before {
            opacity: 1;
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .btn-login:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .btn-login .spinner {
            display: none;
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255,255,255,0.3);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
            margin: 0 auto;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* ============================================================
           DIVIDER + ROLE HINTS
        ============================================================ */
        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 24px 0 20px;
        }

        .divider-line {
            flex: 1;
            height: 1px;
            background: rgba(255, 255, 255, 0.08);
        }

        .divider-text {
            font-size: 11px;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .role-hints {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .role-hint-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 10px;
            padding: 12px;
            text-align: center;
            transition: border-color 0.2s, background 0.2s;
            cursor: pointer;
        }

        .role-hint-card:hover {
            border-color: rgba(99, 102, 241, 0.3);
            background: rgba(99, 102, 241, 0.05);
        }

        .role-hint-card.admin-card:hover {
            border-color: rgba(99, 102, 241, 0.4);
        }

        .role-hint-card.user-card:hover {
            border-color: rgba(16, 185, 129, 0.4);
        }

        .role-hint-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 11px;
            font-weight: 600;
            padding: 3px 8px;
            border-radius: 6px;
            margin-bottom: 6px;
        }

        .badge-admin {
            background: rgba(99, 102, 241, 0.2);
            color: #a5b4fc;
        }

        .badge-user {
            background: rgba(16, 185, 129, 0.15);
            color: #6ee7b7;
        }

        .role-hint-email {
            font-size: 11px;
            color: var(--text-muted);
            word-break: break-all;
        }

        /* ============================================================
           FOOTER
        ============================================================ */
        .login-footer {
            text-align: center;
            margin-top: 24px;
            font-size: 12px;
            color: rgba(148, 163, 184, 0.5);
        }

        /* ============================================================
           RESPONSIVE
        ============================================================ */
        @media (max-width: 480px) {
            .login-card {
                padding: 28px 24px;
                border-radius: 20px;
            }
        }
    </style>
</head>
<body>

    <!-- Background scene -->
    <div class="bg-scene"></div>
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>
    <div class="particles" id="particles"></div>

    <!-- Top status bar -->
    <div class="status-bar">
        <div class="pulse-dot"></div>
        <span>WebMonitor Pro — Sistem Monitoring Website Real-time</span>
    </div>

    <!-- Main login wrapper -->
    <div class="login-wrapper">
        <div class="login-card">

            <!-- Logo -->
            <div class="login-logo">
                <div class="logo-icon">🛡️</div>
                <span class="logo-text">WebMonitor Pro</span>
            </div>
            <p class="login-title">Selamat Datang Kembali</p>
            <p class="login-subtitle">Masuk untuk memantau sistem Anda</p>

            <!-- Flash messages -->
            @if(session('success'))
                <div class="alert alert-success">
                    <i class="bi bi-check-circle-fill"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            <!-- Login Form -->
            <form method="POST" action="{{ route('login.post') }}" id="loginForm" novalidate>
                @csrf

                <!-- Email -->
                <div class="form-group">
                    <label class="form-label" for="email">Alamat Email</label>
                    <div class="input-wrapper">
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-input {{ $errors->has('email') ? 'is-invalid' : '' }}"
                            placeholder="contoh@email.com"
                            value="{{ old('email') }}"
                            autocomplete="email"
                            required
                        >
                        <i class="bi bi-envelope input-icon"></i>
                    </div>
                    @error('email')
                        <div class="invalid-feedback">
                            <i class="bi bi-x-circle-fill"></i> {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Password -->
                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <div class="input-wrapper">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-input has-toggle {{ $errors->has('password') ? 'is-invalid' : '' }}"
                            placeholder="Masukkan password"
                            autocomplete="current-password"
                            required
                        >
                        <i class="bi bi-lock input-icon"></i>
                        <button type="button" class="toggle-password" id="togglePassword" tabindex="-1" aria-label="Toggle password visibility">
                            <i class="bi bi-eye-slash" id="togglePasswordIcon"></i>
                        </button>
                    </div>
                    @error('password')
                        <div class="invalid-feedback">
                            <i class="bi bi-x-circle-fill"></i> {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="remember-row">
                    <input type="checkbox" id="remember" name="remember" class="checkbox-custom" {{ old('remember') ? 'checked' : '' }}>
                    <label for="remember" class="remember-label">Ingat saya selama 30 hari</label>
                </div>

                <!-- Submit -->
                <button type="submit" class="btn-login" id="btnLogin">
                    <span id="btnText">
                        <i class="bi bi-box-arrow-in-right"></i> Masuk ke Dashboard
                    </span>
                    <div class="spinner" id="btnSpinner"></div>
                </button>
            </form>

            <!-- Demo account hints -->
            <div class="divider">
                <div class="divider-line"></div>
                <span class="divider-text">Akun Demo</span>
                <div class="divider-line"></div>
            </div>

            <div class="role-hints">
                <div class="role-hint-card admin-card" onclick="fillCredentials('admin@webmonitor.com')">
                    <div class="role-hint-badge badge-admin">
                        <i class="bi bi-shield-fill-check"></i> Admin
                    </div>
                    <div class="role-hint-email">admin@webmonitor.com</div>
                </div>
                <div class="role-hint-card user-card" onclick="fillCredentials('user@webmonitor.com')">
                    <div class="role-hint-badge badge-user">
                        <i class="bi bi-person-fill"></i> User
                    </div>
                    <div class="role-hint-email">user@webmonitor.com</div>
                </div>
            </div>

            <div class="login-footer">
                Password default: <strong style="color: #94a3b8;">password</strong> &nbsp;·&nbsp;
                &copy; {{ date('Y') }} WebMonitor Pro
            </div>

        </div>
    </div>

    <script>
        // ── Particles ──────────────────────────────────────────────
        const particlesContainer = document.getElementById('particles');
        const particleCount = 30;

        for (let i = 0; i < particleCount; i++) {
            const p = document.createElement('div');
            p.className = 'particle';
            p.style.left = Math.random() * 100 + 'vw';
            p.style.animationDuration = (8 + Math.random() * 12) + 's';
            p.style.animationDelay = (-Math.random() * 20) + 's';
            p.style.width = p.style.height = (1 + Math.random() * 2) + 'px';
            p.style.opacity = 0.3 + Math.random() * 0.5;
            particlesContainer.appendChild(p);
        }

        // ── Toggle Password ────────────────────────────────────────
        const toggleBtn  = document.getElementById('togglePassword');
        const toggleIcon = document.getElementById('togglePasswordIcon');
        const passwordInput = document.getElementById('password');

        toggleBtn.addEventListener('click', () => {
            const isPassword = passwordInput.type === 'password';
            passwordInput.type = isPassword ? 'text' : 'password';
            toggleIcon.className = isPassword ? 'bi bi-eye' : 'bi bi-eye-slash';
        });

        // ── Loading state on submit ────────────────────────────────
        document.getElementById('loginForm').addEventListener('submit', function () {
            const btn     = document.getElementById('btnLogin');
            const btnText = document.getElementById('btnText');
            const spinner = document.getElementById('btnSpinner');

            btn.disabled     = true;
            btnText.style.display = 'none';
            spinner.style.display = 'block';
        });

        // ── Quick-fill demo credentials ────────────────────────────
        function fillCredentials(email) {
            document.getElementById('email').value    = email;
            document.getElementById('password').value = 'password';

            // Subtle visual feedback
            document.getElementById('email').dispatchEvent(new Event('input'));
        }
    </script>
</body>
</html>
