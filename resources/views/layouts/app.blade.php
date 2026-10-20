<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Laravel 12 Pulse Studio Pro')</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            background: #f8fafc;
            color: #1e293b;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .font-mono {
            font-family: 'JetBrains Mono', monospace;
        }

        /* Top Header Navbar */
        .top-navbar {
            background: #0f172a;
            border-bottom: 1px solid #1e293b;
            padding: 12px 30px;
        }

        .brand-logo {
            font-size: 19px;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #ffffff;
            text-decoration: none;
        }

        .pulse-heart-icon {
            color: #ec4899;
            animation: pulse-beat 1.6s infinite;
        }

        @keyframes pulse-beat {
            0% { transform: scale(1); }
            14% { transform: scale(1.22); }
            28% { transform: scale(1); }
            42% { transform: scale(1.22); }
            70% { transform: scale(1); }
        }

        .nav-link-custom {
            color: #94a3b8;
            text-decoration: none;
            padding: 8px 14px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .nav-link-custom:hover {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.08);
        }

        .nav-link-custom.active {
            color: #ffffff;
            background: #3b82f6;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.35);
        }

        .container-wrapper {
            max-width: 1300px;
            margin: 25px auto;
            padding: 0 20px;
            flex: 1;
            width: 100%;
        }

        .card-custom {
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            background: #ffffff;
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.04);
            transition: all 0.25s;
        }

        .card-custom:hover {
            box-shadow: 0 10px 25px -4px rgba(0, 0, 0, 0.08);
        }

        footer {
            background: #0f172a;
            color: #94a3b8;
            text-align: center;
            padding: 20px;
            margin-top: auto;
            font-size: 13px;
            border-top: 1px solid #1e293b;
        }
    </style>
    @yield('styles')
</head>
<body>

    <!-- Header Navbar -->
    <header class="top-navbar">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <a href="{{ route('pulse.dashboard') }}" class="brand-logo">
                <i class="bi bi-activity pulse-heart-icon fs-4"></i>
                <span>Pulse Studio <span class="badge bg-primary fs-xs ms-1 py-1 px-2 rounded-pill font-mono">PRO</span></span>
            </a>

            <!-- Navigation Links -->
            <nav class="d-flex align-items-center gap-1 flex-wrap">
                <a href="{{ route('pulse.dashboard') }}" class="nav-link-custom {{ request()->routeIs('pulse.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i> Overview
                </a>
                <a href="{{ route('pulse.simulator.index') }}" class="nav-link-custom {{ request()->routeIs('pulse.simulator*') ? 'active' : '' }}">
                    <i class="bi bi-lightning-charge-fill text-warning"></i> Anomaly Lab
                </a>
                <a href="{{ route('pulse.incidents.index') }}" class="nav-link-custom {{ request()->routeIs('pulse.incidents*') ? 'active' : '' }}">
                    <i class="bi bi-shield-exclamation text-danger"></i> Incident Guard
                </a>
                <a href="{{ route('pulse.reports.index') }}" class="nav-link-custom {{ request()->routeIs('pulse.reports*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-bar-graph"></i> Report Studio
                </a>
                <a href="{{ route('pulse.analytics') }}" class="nav-link-custom {{ request()->routeIs('pulse.analytics') ? 'active' : '' }}">
                    <i class="bi bi-graph-up"></i> Analytics
                </a>
                <a href="/pulse" target="_blank" class="nav-link-custom text-info" title="Open Official Laravel Pulse Livewire Dashboard">
                    <i class="bi bi-box-arrow-up-right"></i> Official Pulse
                </a>
            </nav>
        </div>
    </header>

    <!-- Main Container -->
    <main class="container-wrapper">
        <!-- Flash Alerts -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center rounded-3 shadow-sm mb-4 border-0" role="alert">
                <i class="bi bi-check-circle-fill fs-5 me-2 text-success"></i>
                <div class="fw-semibold">{{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show d-flex align-items-center rounded-3 shadow-sm mb-4 border-0" role="alert">
                <i class="bi bi-exclamation-triangle-fill fs-5 me-2 text-warning"></i>
                <div class="fw-semibold">{{ session('warning') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('info'))
            <div class="alert alert-info alert-dismissible fade show d-flex align-items-center rounded-3 shadow-sm mb-4 border-0" role="alert">
                <i class="bi bi-info-circle-fill fs-5 me-2 text-info"></i>
                <div class="fw-semibold">{{ session('info') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center rounded-3 shadow-sm mb-4 border-0" role="alert">
                <i class="bi bi-x-circle-fill fs-5 me-2 text-danger"></i>
                <div class="fw-semibold">{{ session('error') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer>
        <p class="mb-1"><strong>Laravel 12 Pulse Studio Pro</strong> &bull; Real-Time Application Health, Chaos Testing & Incident Engine</p>
        <p class="text-secondary mb-0">Powered by <code>laravel/pulse: ^1.5</code>, MySQL &bull; No external third-party APM services required.</p>
    </footer>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>
