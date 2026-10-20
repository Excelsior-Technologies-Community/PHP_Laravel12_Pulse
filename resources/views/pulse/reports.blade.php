@extends('layouts.app')

@section('title', 'Report Studio & Metrics Export')

@section('content')

<!-- Header Banner -->
<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
            <span class="badge bg-primary bg-opacity-10 text-primary p-2 rounded-3"><i class="bi bi-file-earmark-bar-graph fs-5"></i></span>
            Advanced Metrics Export & Report Studio
        </h3>
        <p class="text-muted small mb-0">Filter, explore, and export formatted Pulse telemetry into CSV, JSON, or printable executive summary reports.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('pulse.reports.export.csv', ['range' => $range, 'type' => $type]) }}" class="btn btn-success rounded-pill px-3 shadow-sm">
            <i class="bi bi-filetype-csv me-1"></i> Export CSV
        </a>
        <a href="{{ route('pulse.reports.export.json', ['range' => $range, 'type' => $type]) }}" class="btn btn-outline-dark rounded-pill px-3 shadow-sm">
            <i class="bi bi-filetype-json me-1"></i> Export JSON
        </a>
        <a href="{{ route('pulse.reports.executive', ['range' => $range]) }}" target="_blank" class="btn btn-primary rounded-pill px-3 shadow-sm">
            <i class="bi bi-printer me-1"></i> Executive Report
        </a>
    </div>
</div>

<!-- Filters & KPI Bar -->
<div class="card card-custom p-4 mb-4">
    <form method="GET" action="{{ route('pulse.reports.index') }}" class="row g-3 align-items-end">
        <div class="col-md-4">
            <label class="form-label fw-semibold small text-muted">Time Window</label>
            <select name="range" class="form-select rounded-3" onchange="this.form.submit()">
                <option value="1h" {{ $range === '1h' ? 'selected' : '' }}>Last 1 Hour</option>
                <option value="24h" {{ $range === '24h' ? 'selected' : '' }}>Last 24 Hours</option>
                <option value="7d" {{ $range === '7d' ? 'selected' : '' }}>Last 7 Days</option>
                <option value="30d" {{ $range === '30d' ? 'selected' : '' }}>Last 30 Days</option>
                <option value="all" {{ $range === 'all' ? 'selected' : '' }}>All Time</option>
            </select>
        </div>

        <div class="col-md-5">
            <label class="form-label fw-semibold small text-muted">Metric Category Filter</label>
            <select name="type" class="form-select rounded-3" onchange="this.form.submit()">
                <option value="all" {{ $type === 'all' ? 'selected' : '' }}>All Metric Types</option>
                <option value="slow_request" {{ $type === 'slow_request' ? 'selected' : '' }}>Slow HTTP Requests (slow_request)</option>
                <option value="exception" {{ $type === 'exception' ? 'selected' : '' }}>Application Exceptions (exception)</option>
                <option value="slow_query" {{ $type === 'slow_query' ? 'selected' : '' }}>Slow Database Queries (slow_query)</option>
                <option value="cache_miss" {{ $type === 'cache_miss' ? 'selected' : '' }}>Cache Misses (cache_miss)</option>
                <option value="memory_surge" {{ $type === 'memory_surge' ? 'selected' : '' }}>Memory Spikes (memory_surge)</option>
                <option value="slow_outgoing_request" {{ $type === 'slow_outgoing_request' ? 'selected' : '' }}>Slow Outgoing HTTP (slow_outgoing_request)</option>
                <option value="business_login" {{ $type === 'business_login' ? 'selected' : '' }}>Business Logins (business_login)</option>
                <option value="business_api_call" {{ $type === 'business_api_call' ? 'selected' : '' }}>Business API Calls (business_api_call)</option>
            </select>
        </div>

        <div class="col-md-3">
            <button type="submit" class="btn btn-outline-secondary w-100 rounded-3">
                <i class="bi bi-funnel me-1"></i> Apply Filter
            </button>
        </div>
    </form>

    <!-- KPI Summary Pills -->
    <div class="row g-2 mt-3 pt-3 border-top text-center font-mono small">
        <div class="col-6 col-md-3">
            <div class="p-2 bg-light rounded-3">
                <span class="text-muted d-block" style="font-size: 0.72rem;">Slow Requests</span>
                <span class="fw-bold text-warning fs-6">{{ number_format($stats['slow_requests']) }}</span>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="p-2 bg-light rounded-3">
                <span class="text-muted d-block" style="font-size: 0.72rem;">Exceptions</span>
                <span class="fw-bold text-danger fs-6">{{ number_format($stats['exceptions']) }}</span>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="p-2 bg-light rounded-3">
                <span class="text-muted d-block" style="font-size: 0.72rem;">Slow Queries</span>
                <span class="fw-bold text-info fs-6">{{ number_format($stats['slow_queries']) }}</span>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="p-2 bg-light rounded-3">
                <span class="text-muted d-block" style="font-size: 0.72rem;">Cache Misses</span>
                <span class="fw-bold text-purple fs-6">{{ number_format($stats['cache_misses']) }}</span>
            </div>
        </div>
    </div>
</div>

<!-- Filtered Data Table -->
<div class="card card-custom p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0 text-dark">
            <i class="bi bi-table text-primary me-2"></i>Filtered Pulse Telemetry
        </h5>
        <span class="badge bg-secondary rounded-pill px-3 py-2 font-mono">
            {{ number_format($totalMatching) }} Matching Entries
        </span>
    </div>

    @if($entries->isEmpty())
        <div class="text-center py-5 bg-light rounded-4 border">
            <i class="bi bi-inboxes text-muted fs-1 mb-2 d-block"></i>
            <h6 class="fw-bold text-dark">No telemetry records match your filters</h6>
            <small class="text-muted">Try selecting a broader date range or use the <a href="{{ route('pulse.simulator.index') }}">Anomaly Lab</a> to generate test traffic.</small>
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-3">
                <thead class="table-light font-mono small">
                    <tr>
                        <th>Time (UTC)</th>
                        <th>Type</th>
                        <th>Key / Endpoint / Target</th>
                        <th class="text-end">Value</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($entries as $row)
                        <tr>
                            <td class="font-mono text-muted small text-nowrap">
                                {{ date('Y-m-d H:i:s', $row->timestamp) }}
                                <div style="font-size: 0.7rem;">{{ \Carbon\Carbon::createFromTimestamp($row->timestamp)->diffForHumans() }}</div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border font-mono small rounded-pill">
                                    {{ $row->type }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $displayKey = $row->key;
                                    if (str_starts_with($displayKey, '[') || str_starts_with($displayKey, '{')) {
                                        $decoded = json_decode($displayKey, true);
                                        if (is_array($decoded)) {
                                            $displayKey = implode(' &bull; ', array_filter($decoded, fn($v) => is_string($v) || is_numeric($v)));
                                        }
                                    }
                                @endphp
                                <code class="text-dark text-break">{!! $displayKey !!}</code>
                            </td>
                            <td class="text-end font-mono fw-bold">
                                @if(in_array($row->type, ['slow_request', 'slow_query', 'slow_outgoing_request']))
                                    <span class="text-warning">{{ number_format($row->value) }} ms</span>
                                @elseif($row->type === 'memory_surge')
                                    <span class="text-info">{{ $row->value }} MB</span>
                                @elseif($row->type === 'exception')
                                    <span class="text-danger">{{ $row->value }} error</span>
                                @else
                                    <span class="text-dark">{{ $row->value }}</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center flex-wrap pt-2">
            <small class="text-muted">Showing {{ $entries->firstItem() ?? 0 }} to {{ $entries->lastItem() ?? 0 }} of {{ $entries->total() }}</small>
            <div>
                {{ $entries->links() }}
            </div>
        </div>
    @endif
</div>

@endsection
