<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 — Akses Ditolak | WebMonitor Pro</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: radial-gradient(ellipse at 30% 50%, rgba(99,102,241,0.12) 0%, transparent 60%),
                        linear-gradient(135deg, #0a0a1a 0%, #0f0f2e 100%);
            color: #f1f5f9;
            text-align: center;
            padding: 24px;
        }
        .error-card {
            max-width: 500px;
        }
        .error-icon {
            font-size: 80px;
            line-height: 1;
            margin-bottom: 24px;
            filter: drop-shadow(0 0 30px rgba(239, 68, 68, 0.4));
            animation: iconShake 0.5s ease 0.3s both;
        }
        @keyframes iconShake {
            0%, 100% { transform: translateX(0); }
            20% { transform: translateX(-8px) rotate(-5deg); }
            40% { transform: translateX(8px) rotate(5deg); }
            60% { transform: translateX(-5px); }
            80% { transform: translateX(5px); }
        }
        .error-code {
            font-size: 100px;
            font-weight: 700;
            background: linear-gradient(135deg, #ef4444, #f97316);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1;
            margin-bottom: 16px;
        }
        .error-title {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 12px;
            color: #f1f5f9;
        }
        .error-message {
            font-size: 15px;
            color: #94a3b8;
            line-height: 1.6;
            margin-bottom: 32px;
        }
        .error-role-info {
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 28px;
            font-size: 13px;
            color: #94a3b8;
        }
        .error-role-info strong { color: #c7d2fe; }
        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 28px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border: none;
            border-radius: 12px;
            color: #fff;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            transition: transform 0.15s, box-shadow 0.15s;
            box-shadow: 0 4px 20px rgba(99,102,241,0.4);
        }
        .btn-back:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(99,102,241,0.5);
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="error-card">
        <div class="error-icon">🚫</div>
        <div class="error-code">403</div>
        <h1 class="error-title">Akses Ditolak</h1>
        <p class="error-message">
            Anda tidak memiliki izin untuk mengakses halaman ini.<br>
            Fitur ini hanya tersedia untuk role <strong>Admin</strong>.
        </p>
        @auth
        <div class="error-role-info">
            <i class="bi bi-info-circle me-1"></i>
            Anda login sebagai <strong>{{ auth()->user()->name }}</strong>
            dengan role <strong>{{ ucfirst(auth()->user()->role) }}</strong>.
        </div>
        @endauth
        <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('dashboard') }}" class="btn-back">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
</body>
</html>
