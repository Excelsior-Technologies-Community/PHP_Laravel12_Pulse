@extends('layouts.app')

@section('title', 'Incident Guard & Threshold Alerts')

@section('content')

<!-- System Health Alert Banner -->
<div class="card card-custom p-4 mb-4 border-0 text-white shadow-sm"
     style="background: {{ $systemHealth === 'Critical' ? 'linear-gradient(135deg, #b91c1c 0%, #991b1b 100%)' : ($systemHealth === 'Warning' ? 'linear-gradient(135deg, #d97706 0%, #b45309 100%)' : 'linear-gradient(135deg, #059669 0%, #047857 100%)') }};">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div class="d-flex align-items-center gap-3">
            <div class="p-3 bg-white bg-opacity-20 rounded-circle fs-3">
                @if($systemHealth === 'Critical')
                    <i class="bi bi-exclamation-octagon-fill"></i>
                @elseif($systemHealth === 'Warning')
                    <i class="bi bi-exclamation-triangle-fill"></i>
                @else
                    <i class="bi bi-shield-check"></i>
                @endif
            </div>
            <div>
                <div class="text-white-50 text-uppercase fw-bold small">System Performance Status</div>
                <h2 class="fw-bold mb-0">System Health: {{ $systemHealth }}</h2>
                <small class="text-white-50">
                    @if($openCount > 0)
                        {{ $openCount }} active incident(s) require attention ({{ $criticalCount }} Critical).
                    @else
                        All performance metrics are currently operating within safe threshold boundaries.
                    @endif
                </small>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2">
            <form method="POST" action="{{ route('pulse.incidents.evaluate') }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-light rounded-pill px-3 fw-semibold shadow-sm text-dark">
                    <i class="bi bi-arrow-repeat me-1"></i> Re-Evaluate Rules
                </button>
            </form>
            <form method="POST" action="{{ route('pulse.incidents.test.alert') }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-outline-light rounded-pill px-3 fw-semibold">
                    <i class="bi bi-bell-fill me-1"></i> Test Alert
                </button>
            </form>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Active Incidents Column -->
    <div class="col-lg-8">
        <div class="card card-custom p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-bell-fill text-danger me-2"></i>Active Performance Incidents</h5>
                    <small class="text-muted">Breaches detected by automated Pulse threshold evaluation</small>
                </div>
                <span class="badge {{ $openCount > 0 ? 'bg-danger' : 'bg-success' }} rounded-pill px-3 py-2 font-mono">
                    {{ $openCount }} Open
                </span>
            </div>

            @if($openIncidents->isEmpty())
                <div class="text-center py-5 bg-light rounded-4 border">
                    <i class="bi bi-check-circle-fill text-success fs-1 mb-2 d-block"></i>
                    <h6 class="fw-bold text-dark">Zero Active Incidents</h6>
                    <small class="text-muted">No threshold breaches currently recorded. Use Anomaly Lab or "Test Alert" above to test alerting flow.</small>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Severity</th>
                                <th>Incident Details</th>
                                <th>Observed vs Limit</th>
                                <th>Status</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($openIncidents as $incident)
                                <tr>
                                    <td>
                                        <span class="badge {{ $incident->severity === 'critical' ? 'bg-danger' : 'bg-warning text-dark' }} text-uppercase rounded-pill px-2.5 py-1">
                                            {{ $incident->severity }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $incident->title }}</div>
                                        <small class="text-muted font-mono" style="font-size: 0.75rem;">
                                            Metric: <code>{{ $incident->metric_type }}</code> &bull; {{ \Carbon\Carbon::parse($incident->created_at)->diffForHumans() }}
                                        </small>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-danger">{{ $incident->actual_value }}</span>
                                        <span class="text-muted small">/ {{ $incident->threshold_value }} limit</span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $incident->status === 'acknowledged' ? 'bg-info text-dark' : 'bg-secondary' }} rounded-pill">
                                            {{ ucfirst($incident->status) }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            @if($incident->status !== 'acknowledged')
                                                <form method="POST" action="{{ route('pulse.incidents.acknowledge', $incident->id) }}" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-outline-secondary" title="Acknowledge incident">
                                                        <i class="bi bi-eye"></i> Ack
                                                    </button>
                                                </form>
                                            @endif
                                            <form method="POST" action="{{ route('pulse.incidents.resolve', $incident->id) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-success" title="Mark as Resolved">
                                                    <i class="bi bi-check-lg"></i> Resolve
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- Historical Incidents -->
        @if($resolvedIncidents->isNotEmpty())
            <div class="card card-custom p-4">
                <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-clock-history text-secondary me-2"></i>Resolved Incident Archive</h5>
                <div class="table-responsive">
                    <table class="table table-sm align-middle text-muted small mb-0">
                        <thead>
                            <tr>
                                <th>Incident</th>
                                <th>Metric</th>
                                <th>Triggered At</th>
                                <th>Resolved At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($resolvedIncidents as $res)
                                <tr>
                                    <td class="fw-medium text-dark">{{ $res->title }}</td>
                                    <td><code>{{ $res->metric_type }}</code></td>
                                    <td>{{ \Carbon\Carbon::parse($res->created_at)->format('M d, H:i') }}</td>
                                    <td>
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill">
                                            Resolved {{ \Carbon\Carbon::parse($res->resolved_at)->diffForHumans() }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>

    <!-- Threshold Rules Configuration Column -->
    <div class="col-lg-4">
        <div class="card card-custom p-4 h-100">
            <h5 class="fw-bold mb-2 text-dark"><i class="bi bi-sliders text-primary me-2"></i>Threshold Rules</h5>
            <p class="text-muted small mb-3">Configure automated breach triggers for warning and critical state escalations.</p>

            <form method="POST" action="{{ route('pulse.incidents.update.thresholds') }}">
                @csrf

                @foreach($thresholdRules as $rule)
                    <div class="p-3 bg-light rounded-4 mb-3 border">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <strong class="text-dark small">{{ $rule->label }}</strong>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="rules[{{ $rule->id }}][active]" value="1" {{ $rule->is_active ? 'checked' : '' }}>
                            </div>
                        </div>

                        <div class="row g-2 align-items-center font-mono small">
                            <div class="col-6">
                                <label class="text-muted" style="font-size: 0.7rem;">Warning ({{ $rule->unit }})</label>
                                <input type="number" step="0.1" name="rules[{{ $rule->id }}][warning]" class="form-control form-control-sm rounded-3" value="{{ $rule->warning_threshold }}">
                            </div>
                            <div class="col-6">
                                <label class="text-muted" style="font-size: 0.7rem;">Critical ({{ $rule->unit }})</label>
                                <input type="number" step="0.1" name="rules[{{ $rule->id }}][critical]" class="form-control form-control-sm rounded-3 text-danger fw-bold" value="{{ $rule->critical_threshold }}">
                            </div>
                        </div>
                    </div>
                @endforeach

                <button type="submit" class="btn btn-primary w-100 rounded-pill fw-semibold shadow-sm mt-2">
                    <i class="bi bi-save me-1"></i> Save Threshold Rules
                </button>
            </form>
        </div>
    </div>
</div>

@endsection
