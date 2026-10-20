@extends('layouts.app')

@section('title', 'Laravel Pulse Overview & Business Metrics')

@section('content')

<!-- System Health Hero -->
<div class="card card-custom p-4 mb-4 border-0 text-white shadow-sm"
     style="background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%);">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-primary bg-opacity-25 text-white border border-white border-opacity-25 rounded-pill px-3 py-1 font-mono small">
                    <i class="bi bi-broadcast text-warning me-1"></i> Live APM Engine
                </span>
                <span class="badge {{ $systemHealth === 'Critical' ? 'bg-danger' : ($systemHealth === 'Warning' ? 'bg-warning text-dark' : 'bg-success') }} rounded-pill px-3 py-1 font-mono small">
                    Health: {{ $systemHealth }}
                </span>
            </div>
            <h2 class="fw-bold mb-1">Laravel Pulse Studio Pro</h2>
            <p class="text-white-50 small mb-0">Unified application telemetry, real-time chaos simulation, threshold incident guard, and business metrics.</p>
        </div>

        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('pulse.simulator.index') }}" class="btn btn-warning rounded-pill px-4 fw-semibold text-dark shadow-sm">
                <i class="bi bi-lightning-charge-fill me-1"></i> Open Anomaly Lab
            </a>
            <a href="{{ route('pulse.incidents.index') }}" class="btn btn-outline-light rounded-pill px-3 shadow-sm">
                <i class="bi bi-shield-exclamation me-1"></i> Incidents ({{ $openIncidentsCount }})
            </a>
        </div>
    </div>
</div>

<!-- Primary System Metrics Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="card card-custom p-3 border-0 shadow-sm text-white" style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <small class="text-white-50 text-uppercase fw-semibold" style="font-size: 0.72rem;">Registered Users</small>
                    <div class="fs-3 fw-bold font-mono mt-1">{{ number_format($users) }}</div>
                    <small class="text-white-50">App accounts</small>
                </div>
                <div class="bg-white bg-opacity-20 p-3 rounded-circle">
                    <i class="bi bi-people-fill fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="card card-custom p-3 border-0 shadow-sm text-white" style="background: linear-gradient(135deg, #10b981 0%, #047857 100%);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <small class="text-white-50 text-uppercase fw-semibold" style="font-size: 0.72rem;">Total Pulse Entries</small>
                    <div class="fs-3 fw-bold font-mono mt-1">{{ number_format($queries) }}</div>
                    <small class="text-white-50">Captured telemetry</small>
                </div>
                <div class="bg-white bg-opacity-20 p-3 rounded-circle">
                    <i class="bi bi-bar-chart-fill fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="card card-custom p-3 border-0 shadow-sm text-white" style="background: linear-gradient(135deg, #f59e0b 0%, #b45309 100%);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <small class="text-white-50 text-uppercase fw-semibold" style="font-size: 0.72rem;">Slow Requests (>1s)</small>
                    <div class="fs-3 fw-bold font-mono mt-1">{{ number_format($slowRequests) }}</div>
                    <small class="text-white-50">Performance drops</small>
                </div>
                <div class="bg-white bg-opacity-20 p-3 rounded-circle">
                    <i class="bi bi-clock-history fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="card card-custom p-3 border-0 shadow-sm text-white" style="background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <small class="text-white-50 text-uppercase fw-semibold" style="font-size: 0.72rem;">Runtime Exceptions</small>
                    <div class="fs-3 fw-bold font-mono mt-1">{{ number_format($exceptions) }}</div>
                    <small class="text-white-50">Error tracker</small>
                </div>
                <div class="bg-white bg-opacity-20 p-3 rounded-circle">
                    <i class="bi bi-bug-fill fs-4"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- FEATURE 4: CUSTOM PULSE BUSINESS METRICS & ACTIVITY CARD -->
<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card card-custom p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="fw-bold mb-0 text-dark">
                        <i class="bi bi-briefcase-fill text-primary me-2"></i>Business Metrics & Activity Tracker (Custom Pulse Card)
                    </h5>
                    <small class="text-muted">Recorded directly into Pulse storage via <code>Pulse::record('business_*')</code></small>
                </div>
                <span class="badge bg-primary rounded-pill px-3 py-1 font-mono">Feature 4</span>
            </div>

            <!-- Business KPI Metrics Row -->
            <div class="row g-3 mb-4 text-center">
                <div class="col-sm-4">
                    <div class="p-3 bg-light rounded-4 border">
                        <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.7rem;">Today's Activity Events</small>
                        <div class="fs-4 fw-bold text-primary font-mono mt-1">{{ number_format($businessSummary['today_activities']) }}</div>
                        <small class="text-muted font-mono" style="font-size: 0.72rem;">{{ number_format($businessSummary['total_activities']) }} all time</small>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="p-3 bg-light rounded-4 border">
                        <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.7rem;">Business Logins</small>
                        <div class="fs-4 fw-bold text-success font-mono mt-1">{{ number_format($businessSummary['business_logins']) }}</div>
                        <small class="text-muted font-mono" style="font-size: 0.72rem;">Tracked sessions</small>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="p-3 bg-light rounded-4 border">
                        <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.7rem;">API Consumption</small>
                        <div class="fs-4 fw-bold text-info font-mono mt-1">{{ number_format($businessSummary['business_api_count']) }}</div>
                        <small class="text-muted font-mono" style="font-size: 0.72rem;">Endpoint hits</small>
                    </div>
                </div>
            </div>

            <!-- Top Endpoints Consumed -->
            <h6 class="fw-bold text-dark small text-uppercase font-mono mb-2">
                <i class="bi bi-diagram-3 text-secondary me-1"></i> Most Consumed Application Endpoints
            </h6>
            @if($businessSummary['top_endpoints']->isEmpty())
                <div class="p-3 bg-light text-muted small rounded-3 border text-center">No activity records logged yet.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-sm align-middle font-mono small mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Method</th>
                                <th>Endpoint URL</th>
                                <th class="text-end">Calls Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($businessSummary['top_endpoints'] as $ep)
                                <tr>
                                    <td><span class="badge bg-secondary rounded-pill">{{ $ep->method }}</span></td>
                                    <td><code>{{ $ep->url }}</code></td>
                                    <td class="text-end fw-bold">{{ number_format($ep->total) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <!-- Interactive Event Dispatcher Column -->
    <div class="col-lg-4">
        <div class="card card-custom p-4 h-100">
            <h5 class="fw-bold mb-1 text-dark"><i class="bi bi-hand-index-thumb text-warning me-2"></i>Emit Business Event</h5>
            <p class="text-muted small mb-3">Simulate live domain events to see real-time updates in Pulse.</p>

            <div class="d-grid gap-2">
                <form method="POST" action="{{ route('pulse.business.event') }}">
                    @csrf
                    <input type="hidden" name="event_type" value="login">
                    <button type="submit" class="btn btn-outline-success w-100 text-start py-2.5 rounded-3 d-flex align-items-center justify-content-between">
                        <div>
                            <strong class="d-block"><i class="bi bi-box-arrow-in-right me-1"></i> User Login</strong>
                            <small class="text-muted">Records login session in Pulse</small>
                        </div>
                        <i class="bi bi-chevron-right"></i>
                    </button>
                </form>

                <form method="POST" action="{{ route('pulse.business.event') }}">
                    @csrf
                    <input type="hidden" name="event_type" value="api_call">
                    <button type="submit" class="btn btn-outline-info w-100 text-start py-2.5 rounded-3 d-flex align-items-center justify-content-between">
                        <div>
                            <strong class="d-block text-dark"><i class="bi bi-arrow-repeat me-1"></i> API Consumption</strong>
                            <small class="text-muted">Records endpoint hit & latency</small>
                        </div>
                        <i class="bi bi-chevron-right text-dark"></i>
                    </button>
                </form>

                <form method="POST" action="{{ route('pulse.business.event') }}">
                    @csrf
                    <input type="hidden" name="event_type" value="registration">
                    <button type="submit" class="btn btn-outline-primary w-100 text-start py-2.5 rounded-3 d-flex align-items-center justify-content-between">
                        <div>
                            <strong class="d-block"><i class="bi bi-person-plus me-1"></i> New Registration</strong>
                            <small class="text-muted">Increments signup velocity</small>
                        </div>
                        <i class="bi bi-chevron-right"></i>
                    </button>
                </form>

                <form method="POST" action="{{ route('pulse.business.event') }}">
                    @csrf
                    <input type="hidden" name="event_type" value="checkout">
                    <button type="submit" class="btn btn-outline-dark w-100 text-start py-2.5 rounded-3 d-flex align-items-center justify-content-between">
                        <div>
                            <strong class="d-block"><i class="bi bi-credit-card me-1"></i> Checkout Transaction</strong>
                            <small class="text-muted">Simulates e-commerce checkout</small>
                        </div>
                        <i class="bi bi-chevron-right"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Fast Navigation Modules Cards -->
<div class="row g-4">
    <div class="col-md-4">
        <div class="card card-custom p-4 h-100">
            <div class="fs-2 text-warning mb-2"><i class="bi bi-lightning-charge-fill"></i></div>
            <h5 class="fw-bold text-dark">1. Anomaly Lab</h5>
            <p class="text-muted small">Stress-test Pulse by simulating traffic bursts, N+1 query bottlenecks, memory spikes, and exception cascades on demand.</p>
            <a href="{{ route('pulse.simulator.index') }}" class="btn btn-outline-warning text-dark rounded-pill fw-semibold mt-auto">
                Open Anomaly Lab &rarr;
            </a>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card card-custom p-4 h-100">
            <div class="fs-2 text-danger mb-2"><i class="bi bi-shield-exclamation"></i></div>
            <h5 class="fw-bold text-dark">2. Incident Guard</h5>
            <p class="text-muted small">Automated threshold evaluation rules tracking health state (Healthy, Warning, Critical) with incident lifecycle management.</p>
            <a href="{{ route('pulse.incidents.index') }}" class="btn btn-outline-danger rounded-pill fw-semibold mt-auto">
                Manage Incidents &rarr;
            </a>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card card-custom p-4 h-100">
            <div class="fs-2 text-primary mb-2"><i class="bi bi-file-earmark-bar-graph"></i></div>
            <h5 class="fw-bold text-dark">3. Report Studio</h5>
            <p class="text-muted small">Multi-format export engine for CSV, JSON payload, and printable executive performance audit reports with time filters.</p>
            <a href="{{ route('pulse.reports.index') }}" class="btn btn-outline-primary rounded-pill fw-semibold mt-auto">
                Generate Reports &rarr;
            </a>
        </div>
    </div>
</div>

@endsection
