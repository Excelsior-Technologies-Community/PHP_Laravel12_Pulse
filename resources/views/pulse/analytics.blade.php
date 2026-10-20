@extends('layouts.app')

@section('title', 'Laravel Pulse Analytics & Telemetry')

@section('content')

<!-- Header Banner -->
<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <h3 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                <span class="badge bg-primary bg-opacity-10 text-primary p-2 rounded-3"><i class="bi bi-graph-up fs-5"></i></span>
                Pulse Analytics & Performance Center
            </h3>
            <span class="badge {{ $status === 'Critical' ? 'bg-danger' : ($status === 'Warning' ? 'bg-warning text-dark' : 'bg-success') }} font-mono px-3 py-1.5 rounded-pill">
                {{ $status }}
            </span>
        </div>
        <p class="text-muted small mb-0">Inspect real-time telemetry distributions, hourly event buckets, system exceptions, and custom business activity.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('pulse.simulator.index') }}" class="btn btn-warning rounded-pill px-3 shadow-sm text-dark fw-semibold">
            <i class="bi bi-lightning-charge-fill me-1"></i> Anomaly Lab
        </a>
        <a href="{{ route('pulse.incidents.index') }}" class="btn btn-outline-danger rounded-pill px-3 shadow-sm">
            <i class="bi bi-shield-exclamation me-1"></i> Incidents ({{ $openIncidentsCount }})
        </a>
        <a href="{{ route('pulse.reports.index') }}" class="btn btn-outline-primary rounded-pill px-3 shadow-sm">
            <i class="bi bi-file-earmark-bar-graph me-1"></i> Reports
        </a>
    </div>
</div>

<!-- Primary Telemetry Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-2">
        <div class="card card-custom p-3 text-center">
            <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.7rem;">Registered Users</small>
            <div class="fs-4 fw-bold text-dark font-mono mt-1">{{ number_format($totalUsers) }}</div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-2">
        <div class="card card-custom p-3 text-center">
            <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.7rem;">Pulse Entries</small>
            <div class="fs-4 fw-bold text-dark font-mono mt-1">{{ number_format($totalPulseEntries) }}</div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-2">
        <div class="card card-custom p-3 text-center border-start border-warning border-4">
            <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.7rem;">Slow Requests</small>
            <div class="fs-4 fw-bold text-warning font-mono mt-1">{{ number_format($slowRequests) }}</div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-2">
        <div class="card card-custom p-3 text-center border-start border-danger border-4">
            <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.7rem;">Exceptions</small>
            <div class="fs-4 fw-bold text-danger font-mono mt-1">{{ number_format($exceptions) }}</div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-2">
        <div class="card card-custom p-3 text-center border-start border-info border-4">
            <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.7rem;">Slow Queries</small>
            <div class="fs-4 fw-bold text-info font-mono mt-1">{{ number_format($slowQueries ?? 0) }}</div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-2">
        <div class="card card-custom p-3 text-center border-start border-primary border-4">
            <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.7rem;">Cache Misses</small>
            <div class="fs-4 fw-bold text-primary font-mono mt-1">{{ number_format($cacheMisses) }}</div>
        </div>
    </div>
</div>

<!-- Business Metrics Card (Feature 4) -->
<div class="card card-custom p-4 mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="fw-bold mb-0 text-dark">
                <i class="bi bi-briefcase text-primary me-2"></i>Business Activity Metrics (Custom Recorder)
            </h5>
            <small class="text-muted">Recorded via <code>BusinessPulseRecorder</code> service into Pulse storage</small>
        </div>
        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-3 py-1 font-mono">
            Feature 4
        </span>
    </div>

    <div class="row g-3 text-center">
        <div class="col-sm-4">
            <div class="p-3 bg-light rounded-4 border">
                <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Activity Events (Today)</small>
                <div class="fs-3 fw-bold text-primary font-mono mt-1">{{ number_format($businessSummary['today_activities'] ?? 0) }}</div>
                <small class="text-muted">{{ number_format($activityLogs) }} all time</small>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="p-3 bg-light rounded-4 border">
                <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Business Logins</small>
                <div class="fs-3 fw-bold text-success font-mono mt-1">{{ number_format($businessSummary['business_logins'] ?? 0) }}</div>
                <small class="text-muted">User sessions</small>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="p-3 bg-light rounded-4 border">
                <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">API Calls Consumed</small>
                <div class="fs-3 fw-bold text-info font-mono mt-1">{{ number_format($businessSummary['business_api_count'] ?? 0) }}</div>
                <small class="text-muted">API hits</small>
            </div>
        </div>
    </div>
</div>

<!-- Telemetry Breakdown & Hourly Stats -->
<div class="row g-4 mb-4">
    <!-- Top Pulse Types -->
    <div class="col-lg-6">
        <div class="card card-custom p-4 h-100">
            <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-pie-chart text-primary me-2"></i>Pulse Telemetry Distribution</h5>
            @if($pulseTypes->isEmpty())
                <div class="text-center py-4 text-muted small bg-light rounded-3 border">No pulse records captured yet.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-sm align-middle font-mono small mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Recorder Type</th>
                                <th class="text-end">Total Hits</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pulseTypes as $item)
                                <tr>
                                    <td><span class="badge bg-light text-dark border font-mono">{{ $item->type }}</span></td>
                                    <td class="text-end fw-bold text-dark">{{ number_format($item->total) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <!-- Hourly Distribution -->
    <div class="col-lg-6">
        <div class="card card-custom p-4 h-100">
            <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-clock text-info me-2"></i>Hourly Telemetry Velocity</h5>
            @if($hourlyStats->isEmpty())
                <div class="text-center py-4 text-muted small bg-light rounded-3 border">No hourly data available yet.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-sm align-middle font-mono small mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Hour of Day (UTC)</th>
                                <th class="text-end">Events Logged</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($hourlyStats as $hour)
                                <tr>
                                    <td>{{ sprintf('%02d:00', $hour->hour) }} - {{ sprintf('%02d:59', $hour->hour) }}</td>
                                    <td class="text-end fw-bold text-primary">{{ number_format($hour->total) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Recent Records Tables -->
<div class="row g-4">
    <div class="col-lg-6">
        <div class="card card-custom p-4 h-100">
            <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-list-check text-secondary me-2"></i>Latest Pulse Entries</h5>
            @if($latestEntries->isEmpty())
                <div class="text-center py-4 text-muted small bg-light rounded-3 border">No entries recorded.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-sm align-middle font-mono small mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Type</th>
                                <th>Key</th>
                                <th class="text-end">Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($latestEntries as $entry)
                                <tr>
                                    <td><span class="badge bg-light text-dark border">{{ $entry->type }}</span></td>
                                    <td><code class="text-dark small text-truncate d-inline-block" style="max-width: 200px;">{{ $entry->key }}</code></td>
                                    <td class="text-end fw-bold">{{ $entry->value }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card card-custom p-4 h-100">
            <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-cursor-fill text-primary me-2"></i>Recent Activity Logs</h5>
            @if($latestActivities->isEmpty())
                <div class="text-center py-4 text-muted small bg-light rounded-3 border">No activity logged.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-sm align-middle font-mono small mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Method</th>
                                <th>URL Path</th>
                                <th class="text-end">Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($latestActivities as $act)
                                <tr>
                                    <td><span class="badge bg-secondary">{{ $act->method }}</span></td>
                                    <td><code>{{ $act->url }}</code></td>
                                    <td class="text-end text-muted small">{{ \Carbon\Carbon::parse($act->created_at)->diffForHumans() }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

@endsection
