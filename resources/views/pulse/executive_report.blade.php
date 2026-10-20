<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel Pulse Executive Performance Audit Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f8fafc;
            color: #0f172a;
            padding: 30px 15px;
        }
        .report-sheet {
            max-width: 960px;
            margin: auto;
            background: #ffffff;
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 4px 25px rgba(0,0,0,0.06);
            border: 1px solid #e2e8f0;
        }
        .font-mono {
            font-family: 'JetBrains Mono', monospace;
        }
        @media print {
            body { background: #fff; padding: 0; }
            .report-sheet { box-shadow: none; border: none; padding: 20px 0; max-width: 100%; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>

<div class="report-sheet">
    <!-- Top Bar with Print Controls -->
    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom no-print">
        <a href="{{ route('pulse.reports.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
            <i class="bi bi-arrow-left me-1"></i> Back to Report Studio
        </a>
        <button onclick="window.print()" class="btn btn-primary btn-sm rounded-pill px-4 shadow-sm">
            <i class="bi bi-printer-fill me-1"></i> Print / Save as PDF
        </button>
    </div>

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-start mb-4 pb-3 border-bottom">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <i class="bi bi-activity text-danger fs-3"></i>
                <h3 class="fw-bold mb-0">Laravel Pulse Executive Health Report</h3>
            </div>
            <p class="text-muted small mb-0">Application Performance Monitoring (APM) & Incident Audit</p>
        </div>
        <div class="text-end font-mono small">
            <div><strong>Generated:</strong> {{ now()->toFormattedDateString() }} {{ now()->format('H:i') }} UTC</div>
            <div><strong>Window:</strong> Last {{ strtoupper($range) }}</div>
            <div><strong>Registered Users:</strong> {{ number_format($totalUsers) }}</div>
        </div>
    </div>

    <!-- Executive KPI Grid -->
    <div class="row g-3 mb-4 text-center">
        <div class="col-sm-3">
            <div class="p-3 bg-light rounded-3 border">
                <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.7rem;">Total Pulse Entries</small>
                <div class="fs-4 fw-bold text-dark font-mono mt-1">{{ number_format($totalPulseEntries) }}</div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="p-3 bg-light rounded-3 border">
                <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.7rem;">Slow Requests (>1s)</small>
                <div class="fs-4 fw-bold text-warning font-mono mt-1">{{ number_format($slowRequests) }}</div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="p-3 bg-light rounded-3 border">
                <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.7rem;">Runtime Exceptions</small>
                <div class="fs-4 fw-bold text-danger font-mono mt-1">{{ number_format($exceptions) }}</div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="p-3 bg-light rounded-3 border">
                <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.7rem;">Active Incidents</small>
                <div class="fs-4 fw-bold {{ $activeIncidents->count() > 0 ? 'text-danger' : 'text-success' }} font-mono mt-1">
                    {{ $activeIncidents->count() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Top Slow Endpoints -->
    <div class="mb-4">
        <h6 class="fw-bold text-dark text-uppercase font-mono small mb-2">
            <i class="bi bi-clock-history text-warning me-1"></i> Highest Latency Endpoints
        </h6>
        @if($topSlowEndpoints->isEmpty())
            <div class="p-3 bg-light text-muted small rounded-3 border">No slow endpoints recorded in this window.</div>
        @else
            <table class="table table-sm table-bordered align-middle font-mono small mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Endpoint Route</th>
                        <th class="text-center">Slow Hits</th>
                        <th class="text-end">Max Latency</th>
                        <th class="text-end">Avg Latency</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topSlowEndpoints as $ep)
                        <tr>
                            <td><code>{{ $ep->key }}</code></td>
                            <td class="text-center">{{ $ep->occurrences }}</td>
                            <td class="text-end text-danger fw-bold">{{ number_format($ep->max_latency) }} ms</td>
                            <td class="text-end">{{ number_format($ep->avg_latency, 1) }} ms</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <!-- Top Runtime Exceptions -->
    <div class="mb-4">
        <h6 class="fw-bold text-dark text-uppercase font-mono small mb-2">
            <i class="bi bi-bug-fill text-danger me-1"></i> Top Recurring Exceptions
        </h6>
        @if($topExceptions->isEmpty())
            <div class="p-3 bg-light text-muted small rounded-3 border">Zero exceptions recorded in this window.</div>
        @else
            <table class="table table-sm table-bordered align-middle font-mono small mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Exception Class & Message</th>
                        <th class="text-end" style="width: 140px;">Occurrences</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topExceptions as $ex)
                        <tr>
                            <td class="text-danger"><code>{{ $ex->key }}</code></td>
                            <td class="text-end fw-bold">{{ number_format($ex->total) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <!-- Active Incident Guard Review -->
    <div class="mb-4">
        <h6 class="fw-bold text-dark text-uppercase font-mono small mb-2">
            <i class="bi bi-shield-exclamation text-primary me-1"></i> Automated Incident Guard Status
        </h6>
        @if($activeIncidents->isEmpty())
            <div class="p-3 bg-success bg-opacity-10 text-success rounded-3 border border-success border-opacity-25 small">
                <i class="bi bi-check-circle-fill me-1"></i> System health status is <strong>Healthy</strong>. No active breaches detected.
            </div>
        @else
            <table class="table table-sm table-bordered align-middle small mb-0">
                <thead class="table-light font-mono">
                    <tr>
                        <th>Severity</th>
                        <th>Incident Title</th>
                        <th>Threshold</th>
                        <th>Current Value</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($activeIncidents as $inc)
                        <tr>
                            <td>
                                <span class="badge {{ $inc->severity === 'critical' ? 'bg-danger' : 'bg-warning text-dark' }} text-uppercase">
                                    {{ $inc->severity }}
                                </span>
                            </td>
                            <td class="fw-semibold">{{ $inc->title }}</td>
                            <td>{{ $inc->threshold_value }}</td>
                            <td class="text-danger fw-bold">{{ $inc->actual_value }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <!-- Sign-off Footer -->
    <div class="pt-3 border-top d-flex justify-content-between align-items-center text-muted font-mono" style="font-size: 0.72rem;">
        <div>Laravel 12 Pulse Studio Pro &bull; Confidential Executive APM Report</div>
        <div>Page 1 of 1</div>
    </div>
</div>

</body>
</html>
