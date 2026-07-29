<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel Pulse Analytics</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background: #f4f7fb;
            font-family: "Segoe UI", sans-serif;
        }

        .header {
            background: linear-gradient(135deg, #2563eb, #4f46e5);
            color: #fff;
            padding: 40px;
            border-radius: 20px;
            margin-bottom: 35px;
        }

        .stat-card {
            border: none;
            border-radius: 18px;
            color: #fff;
            transition: .3s;
            overflow: hidden;
        }

        .stat-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, .15);
        }

        .stat-icon {
            font-size: 48px;
            opacity: .20;
        }

        .stat-value {
            font-size: 38px;
            font-weight: bold;
        }

        .card-blue {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
        }

        .card-green {
            background: linear-gradient(135deg, #16a34a, #15803d);
        }

        .card-red {
            background: linear-gradient(135deg, #dc2626, #b91c1c);
        }

        .card-warning {
            background: linear-gradient(135deg, #f59e0b, #d97706);
        }

        .card-purple {
            background: linear-gradient(135deg, #7c3aed, #6d28d9);
        }

        .card-dark {
            background: linear-gradient(135deg, #111827, #1f2937);
        }

        .table-card {
            border: none;
            border-radius: 18px;
            overflow: hidden;
        }

        .table thead {
            background: #0f172a;
            color: white;
        }

        .progress {
            height: 10px;
        }
    </style>

</head>

<body>

    <div class="container py-5">

        <div class="header">

            <div class="d-flex justify-content-between align-items-center flex-wrap">

                <div>

                    <h1>
                        <i class="bi bi-speedometer2"></i>
                        Laravel Pulse Analytics
                    </h1>

                    <p class="mb-2">
                        Real-Time Monitoring Dashboard
                    </p>

                    @if($status == 'Healthy')

                    <span class="badge bg-success fs-6">
                        <i class="bi bi-check-circle-fill"></i>
                        System Healthy
                    </span>

                    @elseif($status == 'Warning')

                    <span class="badge bg-warning text-dark fs-6">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        Warning
                    </span>

                    @else

                    <span class="badge bg-danger fs-6">
                        <i class="bi bi-x-circle-fill"></i>
                        Critical
                    </span>

                    @endif

                </div>

                <div class="d-flex gap-2 flex-wrap">

                    <a href="/pulse"
                        class="btn btn-light">

                        <i class="bi bi-speedometer2"></i>
                        Pulse

                    </a>

                    <a href="/custom-pulse"
                        class="btn btn-warning">

                        <i class="bi bi-house"></i>
                        Dashboard

                    </a>

                    <a href="{{ route('pulse.export') }}"
                        class="btn btn-success">

                        <i class="bi bi-download"></i>
                        Export

                    </a>

                </div>

            </div>

        </div>

        <div class="row g-4">

            <div class="col-lg-4">

                <div class="stat-card card-blue p-4">

                    <div class="d-flex justify-content-between">

                        <div>

                            <h6>Total Users</h6>

                            <div class="stat-value">
                                {{ $totalUsers }}
                            </div>

                        </div>

                        <i class="bi bi-people-fill stat-icon"></i>

                    </div>

                </div>

            </div>

            <div class="col-lg-4">

                <div class="stat-card card-green p-4">

                    <div class="d-flex justify-content-between">

                        <div>

                            <h6>Pulse Entries</h6>

                            <div class="stat-value">
                                {{ $totalPulseEntries }}
                            </div>

                        </div>

                        <i class="bi bi-bar-chart-fill stat-icon"></i>

                    </div>

                </div>

            </div>

            <div class="col-lg-4">

                <div class="stat-card card-red p-4">

                    <div class="d-flex justify-content-between">

                        <div>

                            <h6>Slow Requests</h6>

                            <div class="stat-value">
                                {{ $slowRequests }}
                            </div>

                        </div>

                        <i class="bi bi-clock-history stat-icon"></i>

                    </div>

                </div>

            </div>

            <div class="col-lg-4">

                <div class="stat-card card-warning p-4">

                    <div class="d-flex justify-content-between">

                        <div>

                            <h6>Exceptions</h6>

                            <div class="stat-value">
                                {{ $exceptions }}
                            </div>

                        </div>

                        <i class="bi bi-exclamation-triangle-fill stat-icon"></i>

                    </div>

                </div>

            </div>

            <div class="col-lg-4">

                <div class="stat-card card-purple p-4">

                    <div class="d-flex justify-content-between">

                        <div>

                            <h6>Cache Misses</h6>

                            <div class="stat-value">
                                {{ $cacheMisses }}
                            </div>

                        </div>

                        <i class="bi bi-database-fill-x stat-icon"></i>

                    </div>

                </div>

            </div>

            <div class="col-lg-4">

                <div class="stat-card card-dark p-4">

                    <div class="d-flex justify-content-between">

                        <div>

                            <h6>Activity Logs</h6>

                            <div class="stat-value">
                                {{ $activityLogs }}
                            </div>

                        </div>

                        <i class="bi bi-list-check stat-icon"></i>

                    </div>

                </div>

            </div>

        </div>

        <div class="row mt-5">

            <div class="col-lg-6">

                <div class="card table-card shadow">

                    <div class="card-header bg-primary text-white">

                        <h5 class="mb-0">

                            Latest Pulse Entries

                        </h5>

                    </div>

                    <div class="table-responsive">

                        <table class="table table-hover mb-0">

                            <thead>

                                <tr>

                                    <th>ID</th>
                                    <th>Type</th>
                                    <th>Value</th>

                                </tr>

                            </thead>

                            <tbody>

                                @forelse($latestEntries as $entry)

                                <tr>

                                    <td>{{ $entry->id }}</td>

                                    <td>

                                        <span class="badge bg-primary">

                                            {{ $entry->type }}

                                        </span>

                                    </td>

                                    <td>

                                        {{ $entry->value ?? '-' }}

                                    </td>

                                </tr>

                                @empty

                                <tr>

                                    <td colspan="3"
                                        class="text-center">

                                        No Pulse Data

                                    </td>

                                </tr>

                                @endforelse

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

            <div class="col-lg-6">

                <div class="card table-card shadow">

                    <div class="card-header bg-success text-white">

                        <h5 class="mb-0">

                            Recent Activity

                        </h5>

                    </div>

                    <div class="table-responsive">

                        <table class="table table-hover mb-0">

                            <thead>

                                <tr>

                                    <th>Method</th>
                                    <th>URL</th>

                                </tr>

                            </thead>

                            <tbody>

                                @forelse($latestActivities as $activity)

                                <tr>

                                    <td>

                                        <span class="badge bg-dark">

                                            {{ $activity->method }}

                                        </span>

                                    </td>

                                    <td>

                                        {{ $activity->url }}

                                    </td>

                                </tr>

                                @empty

                                <tr>

                                    <td colspan="2"
                                        class="text-center">

                                        No Activity

                                    </td>

                                </tr>

                                @endforelse

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </div>

        <div class="card shadow mt-5">

            <div class="card-header bg-dark text-white">

                <h5 class="mb-0">

                    Pulse Types

                </h5>

            </div>

            <div class="card-body">

                @foreach($pulseTypes as $type)

                <div class="mb-4">

                    <div class="d-flex justify-content-between">

                        <strong>

                            {{ ucfirst(str_replace('_',' ',$type->type)) }}

                        </strong>

                        <strong>

                            {{ $type->total }}

                        </strong>

                    </div>

                    <div class="progress mt-2">

                        <div class="progress-bar"

                            style="width:{{ min($type->total,100) }}%">

                        </div>

                    </div>

                </div>

                @endforeach

            </div>

        </div>

        <footer class="text-center py-4 mt-5 text-muted">

            <hr>

            <h6 class="mb-1">
                Laravel Pulse Analytics Dashboard
            </h6>

            <small>
                Built with Laravel 12 • Bootstrap 5 • Laravel Pulse
            </small>

        </footer>

    </div>

</body>

</html>