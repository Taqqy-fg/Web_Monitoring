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
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container-fluid px-4">
            <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="{{ route('dashboard') }}">
                <i class="bi bi-shield-fill-check text-success"></i> WebMonitor Pro
            </a>
            <div class="ms-auto d-flex align-items-center gap-3">
                <button class="btn btn-sm btn-outline-light" id="themeToggler">
                    <i class="bi bi-sun-fill" id="themeIcon"></i>
                </button>
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