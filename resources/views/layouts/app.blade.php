<!DOCTYPE html>
<html lang="id" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Web Monitoring System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        }
        [data-bs-theme="dark"] body {
            background-color: #121212;
        }
        .card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.05) !important;
        }

        /* Role badge styles */
        .role-badge {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            padding: 2px 8px;
            border-radius: 20px;
        }
        .role-badge-admin {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: #fff;
        }
        .role-badge-user {
            background: rgba(16, 185, 129, 0.2);
            color: #10b981;
            border: 1px solid rgba(16, 185, 129, 0.4);
        }
        [data-bs-theme="dark"] .role-badge-user {
            color: #6ee7b7;
        }

        /* User info in navbar */
        .navbar-user-info {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 5px 12px;
            background: rgba(255,255,255,0.08);
            border-radius: 20px;
            border: 1px solid rgba(255,255,255,0.12);
        }
        .navbar-user-name {
            font-size: 13px;
            font-weight: 500;
            color: #e2e8f0;
            max-width: 130px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        /* Logout button */
        .btn-logout {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 5px 14px;
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.35);
            border-radius: 20px;
            color: #fca5a5;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.2s, border-color 0.2s;
            text-decoration: none;
        }
        .btn-logout:hover {
            background: rgba(239, 68, 68, 0.28);
            border-color: rgba(239, 68, 68, 0.6);
            color: #fca5a5;
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container-fluid px-4">
            <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="{{ route('dashboard') }}">
                <i class="bi bi-shield-fill-check text-success"></i> WebMonitor Pro
            </a>
            <div class="ms-auto d-flex align-items-center gap-3">

                @auth
                    {{-- User info & role badge --}}
                    <div class="navbar-user-info d-none d-sm-flex">
                        <i class="bi {{ auth()->user()->isAdmin() ? 'bi-shield-fill-check text-primary' : 'bi-person-fill text-success' }}" style="font-size:15px;"></i>
                        <span class="navbar-user-name">{{ auth()->user()->name }}</span>
                        @if(auth()->user()->isAdmin())
                            <span class="role-badge role-badge-admin">Admin</span>
                        @else
                            <span class="role-badge role-badge-user">User</span>
                        @endif
                    </div>

                    {{-- Theme toggler --}}
                    <button class="btn btn-sm btn-outline-light" id="themeToggler" title="Ganti tema">
                        <i class="bi bi-sun-fill" id="themeIcon"></i>
                    </button>

                    {{-- Logout --}}
                    <form method="POST" action="{{ route('logout') }}" class="d-inline m-0">
                        @csrf
                        <button type="submit" class="btn-logout">
                            <i class="bi bi-box-arrow-right"></i>
                            <span class="d-none d-sm-inline">Logout</span>
                        </button>
                    </form>
                @else
                    <button class="btn btn-sm btn-outline-light" id="themeToggler" title="Ganti tema">
                        <i class="bi bi-sun-fill" id="themeIcon"></i>
                    </button>
                    <a href="{{ route('login') }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Login
                    </a>
                @endauth

            </div>
        </div>
    </nav>

    <div class="container-fluid px-4 mt-3">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    </div>

    <main>
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const toggler = document.getElementById('themeToggler');
        const icon = document.getElementById('themeIcon');

        toggler.addEventListener('click', () => {
            const html = document.documentElement;
            if (html.getAttribute('data-bs-theme') === 'light') {
                html.setAttribute('data-bs-theme', 'dark');
                icon.className = 'bi bi-moon-stars-fill';
            } else {
                html.setAttribute('data-bs-theme', 'light');
                icon.className = 'bi bi-sun-fill';
            }
        });
    </script>
</body>
</html>