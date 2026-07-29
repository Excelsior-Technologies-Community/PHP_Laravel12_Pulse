<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel Pulse Dashboard</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background: #f4f7fc;
            font-family: "Segoe UI", sans-serif;
        }

        .page-title {
            font-size: 34px;
            font-weight: bold;
            color: #343a40;
        }

        .page-subtitle {
            color: #6c757d;
        }

        .dashboard-card {
            border: none;
            border-radius: 18px;
            color: white;
            overflow: hidden;
            transition: .3s;
            box-shadow: 0 10px 25px rgba(0, 0, 0, .12);
        }

        .dashboard-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 18px 35px rgba(0, 0, 0, .18);
        }

        .card-users {
            background: linear-gradient(135deg, #4e73df, #224abe);
        }

        .card-pulse {
            background: linear-gradient(135deg, #1cc88a, #13855c);
        }

        .card-icon {
            font-size: 55px;
            opacity: .25;
        }

        .card-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 15px;
        }

        .card-value {
            font-size: 48px;
            font-weight: bold;
        }

        .footer-text {
            color: rgba(255, 255, 255, .85);
            font-size: 14px;
        }

        .header-box {
            background: white;
            border-radius: 18px;
            padding: 25px;
            margin-bottom: 35px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, .08);
        }
    </style>

</head>

<body>

    <div class="container py-5">

        <div class="header-box text-center">

            <h1 class="page-title">
                <i class="bi bi-speedometer2 text-primary"></i>
                Laravel Pulse Dashboard
            </h1>

            <p class="page-subtitle">
                Monitor application statistics, users and Pulse activities in real time.
            </p>

            <div class="d-flex justify-content-center flex-wrap gap-2 mt-4">

                <a href="/pulse" class="btn btn-dark">
                    <i class="bi bi-speedometer2"></i>
                    Official Pulse
                </a>

                <a href="{{ route('pulse.analytics') }}" class="btn btn-primary">
                    <i class="bi bi-graph-up-arrow"></i>
                    Analytics
                </a>

                <a href="{{ route('pulse.export') }}" class="btn btn-success">
                    <i class="bi bi-download"></i>
                    Export CSV
                </a>

            </div>

        </div>

        <div class="row g-4">

            <!-- Total Users -->
            <div class="col-lg-6">

                <div class="dashboard-card card-users p-4">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>

                            <div class="card-title">
                                Total Users
                            </div>

                            <div class="card-value">
                                {{ $users }}
                            </div>

                            <div class="footer-text mt-2">
                                Registered application users
                            </div>

                        </div>

                        <i class="bi bi-people-fill card-icon"></i>

                    </div>

                </div>

            </div>

            <!-- Pulse Records -->
            <div class="col-lg-6">

                <div class="dashboard-card card-pulse p-4">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>

                            <div class="card-title">
                                Total Pulse Records
                            </div>

                            <div class="card-value">
                                {{ $queries }}
                            </div>

                            <div class="footer-text mt-2">
                                Captured monitoring entries
                            </div>

                        </div>

                        <i class="bi bi-bar-chart-fill card-icon"></i>

                    </div>

                </div>

            </div>

        </div>

        <!-- Information Card -->
        <div class="card border-0 shadow-lg mt-5 rounded-4">

            <div class="card-header bg-dark text-white py-3">
                <h4 class="mb-0">
                    <i class="bi bi-info-circle-fill"></i>
                    Dashboard Information
                </h4>
            </div>

            <div class="card-body">

                <div class="row text-center">

                    <div class="col-md-4">
                        <i class="bi bi-lightning-charge-fill text-warning display-5"></i>
                        <h5 class="mt-3">Performance</h5>
                        <p class="text-muted">
                            Monitor slow requests and database performance.
                        </p>
                    </div>

                    <div class="col-md-4">
                        <i class="bi bi-database-fill text-success display-5"></i>
                        <h5 class="mt-3">Database</h5>
                        <p class="text-muted">
                            View Pulse records and application statistics.
                        </p>
                    </div>

                    <div class="col-md-4">
                        <i class="bi bi-shield-check text-primary display-5"></i>
                        <h5 class="mt-3">Monitoring</h5>
                        <p class="text-muted">
                            Track your application's health in one place.
                        </p>
                    </div>

                </div>

            </div>

        </div>

    </div>

</body>

</html>