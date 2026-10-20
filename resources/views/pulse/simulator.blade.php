@extends('layouts.app')

@section('title', 'Anomaly Lab - Chaos & Traffic Simulator')

@section('content')

<!-- Header Banner -->
<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
            <span class="badge bg-warning bg-opacity-25 text-warning p-2 rounded-3"><i class="bi bi-lightning-charge-fill fs-5"></i></span>
            Interactive Chaos & Traffic Simulator (Anomaly Lab)
        </h3>
        <p class="text-muted small mb-0">Generate synthetic traffic bursts, intentional slow queries, memory spikes, cache churn, and exceptions on demand to test Pulse detectors.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="/pulse" target="_blank" class="btn btn-outline-dark rounded-pill px-3 shadow-sm">
            <i class="bi bi-box-arrow-up-right me-1"></i> Verify in Official Pulse
        </a>
        <a href="{{ route('pulse.incidents.index') }}" class="btn btn-primary rounded-pill px-3 shadow-sm">
            <i class="bi bi-shield-exclamation me-1"></i> Check Incident Guard
        </a>
    </div>
</div>

<!-- Telemetry Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-2">
        <div class="card card-custom p-3 text-center">
            <small class="text-muted text-uppercase fw-bold" style="font-size: 0.72rem;">Total Pulse Entries</small>
            <div class="fs-4 fw-bold text-dark font-mono mt-1" id="statTotalEntries">{{ number_format($totalPulseEntries) }}</div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-2">
        <div class="card card-custom p-3 text-center border-start border-warning border-4">
            <small class="text-muted text-uppercase fw-bold" style="font-size: 0.72rem;">Slow Requests</small>
            <div class="fs-4 fw-bold text-warning font-mono mt-1" id="statSlowRequests">{{ number_format($slowRequests) }}</div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-2">
        <div class="card card-custom p-3 text-center border-start border-danger border-4">
            <small class="text-muted text-uppercase fw-bold" style="font-size: 0.72rem;">Exceptions</small>
            <div class="fs-4 fw-bold text-danger font-mono mt-1" id="statExceptions">{{ number_format($exceptions) }}</div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-2">
        <div class="card card-custom p-3 text-center border-start border-info border-4">
            <small class="text-muted text-uppercase fw-bold" style="font-size: 0.72rem;">Slow Queries</small>
            <div class="fs-4 fw-bold text-info font-mono mt-1" id="statSlowQueries">{{ number_format($slowQueries) }}</div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-2">
        <div class="card card-custom p-3 text-center border-start border-purple border-4">
            <small class="text-muted text-uppercase fw-bold" style="font-size: 0.72rem;">Cache Misses</small>
            <div class="fs-4 fw-bold text-purple font-mono mt-1" id="statCacheMisses">{{ number_format($cacheMisses) }}</div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-2">
        <div class="card card-custom p-3 text-center border-start border-primary border-4">
            <small class="text-muted text-uppercase fw-bold" style="font-size: 0.72rem;">Activity Logs</small>
            <div class="fs-4 fw-bold text-primary font-mono mt-1" id="statActivityLogs">{{ number_format($activityLogs) }}</div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Chaos Injection Triggers Column -->
    <div class="col-lg-7">
        <div class="card card-custom p-4 mb-4">
            <h5 class="fw-bold mb-3 text-dark d-flex align-items-center gap-2">
                <i class="bi bi-cpu text-primary"></i> Synthetic Load Generators
            </h5>

            <!-- 1. Traffic Burst -->
            <div class="p-3 bg-light rounded-4 mb-3 border">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <strong class="text-dark"><i class="bi bi-speedometer text-primary me-1"></i> Traffic Burst Runner</strong>
                        <div class="text-muted small">Dispatches rapid synthetic HTTP requests across random API endpoints with latency variation.</div>
                    </div>
                    <span class="badge bg-primary rounded-pill font-mono" id="burstCountLabel">20 requests</span>
                </div>
                <div class="d-flex align-items-center gap-3 mt-3">
                    <input type="range" class="form-range" id="burstSlider" min="5" max="50" step="5" value="20" oninput="document.getElementById('burstCountLabel').innerText = this.value + ' requests'">
                    <button class="btn btn-primary px-3 rounded-pill fw-semibold text-nowrap" onclick="triggerTrafficBurst()">
                        <i class="bi bi-play-fill me-1"></i> Fire Burst
                    </button>
                </div>
            </div>

            <!-- 2. Slow Queries & N+1 -->
            <div class="p-3 bg-light rounded-4 mb-3 border">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <strong class="text-dark"><i class="bi bi-database-exclamation text-warning me-1"></i> Slow Query & N+1 Relational Loop</strong>
                        <div class="text-muted small">Executes an intentional 1.1s SQL sleep and an unoptimized nested query loop to register on Pulse.</div>
                    </div>
                    <button class="btn btn-warning px-3 rounded-pill fw-semibold text-dark text-nowrap ms-2" onclick="triggerSlowQuery()">
                        <i class="bi bi-clock-history me-1"></i> Trigger Query
                    </button>
                </div>
            </div>

            <!-- 3. Memory Surge -->
            <div class="p-3 bg-light rounded-4 mb-3 border">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <strong class="text-dark"><i class="bi bi-memory text-info me-1"></i> PHP Memory Surge Simulator</strong>
                        <div class="text-muted small">Allocates temporary memory buffers to simulate high memory worker spikes.</div>
                    </div>
                    <span class="badge bg-info text-dark rounded-pill font-mono" id="memoryMbLabel">35 MB buffer</span>
                </div>
                <div class="d-flex align-items-center gap-3 mt-3">
                    <input type="range" class="form-range" id="memorySlider" min="15" max="75" step="10" value="35" oninput="document.getElementById('memoryMbLabel').innerText = this.value + ' MB buffer'">
                    <button class="btn btn-info px-3 rounded-pill fw-semibold text-dark text-nowrap" onclick="triggerMemorySurge()">
                        <i class="bi bi-arrow-up-right-circle me-1"></i> Spike Memory
                    </button>
                </div>
            </div>

            <!-- 4. Categorized Exception -->
            <div class="p-3 bg-light rounded-4 mb-3 border">
                <strong class="text-dark d-block mb-1"><i class="bi bi-exclamation-octagon-fill text-danger me-1"></i> Exception Injector</strong>
                <div class="text-muted small mb-3">Simulates realistic runtime errors to populate the Pulse Exceptions card and trigger Incident Guard.</div>
                <div class="row g-2 align-items-center">
                    <div class="col-sm-8">
                        <select class="form-select rounded-3" id="exceptionSelect">
                            <option value="PaymentGatewayTimeoutException">PaymentGatewayTimeoutException (Stripe 10s Timeout)</option>
                            <option value="DatabaseDeadlockException">DatabaseDeadlockException (SQLSTATE 1213)</option>
                            <option value="ThirdPartyServiceException">ThirdPartyServiceException (SendGrid HTTP 503)</option>
                            <option value="ValidationException">ValidationException (Unprocessable Payload)</option>
                        </select>
                    </div>
                    <div class="col-sm-4 text-end">
                        <button class="btn btn-danger w-100 rounded-pill fw-semibold" onclick="triggerException()">
                            <i class="bi bi-bug-fill me-1"></i> Throw Error
                        </button>
                    </div>
                </div>
            </div>

            <!-- 5. Cache Churn & Slow External API -->
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="p-3 bg-light rounded-4 border h-100 d-flex flex-column justify-content-between">
                        <div>
                            <strong class="text-dark"><i class="bi bi-archive-fill text-secondary me-1"></i> Cache Churn</strong>
                            <div class="text-muted small mt-1">Executes 40 random cache operations with mixed hits and misses.</div>
                        </div>
                        <button class="btn btn-outline-secondary w-100 rounded-pill fw-semibold mt-3" onclick="triggerCacheChurn()">
                            <i class="bi bi-shuffle me-1"></i> Run Cache Storm
                        </button>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="p-3 bg-light rounded-4 border h-100 d-flex flex-column justify-content-between">
                        <div>
                            <strong class="text-dark"><i class="bi bi-cloud-arrow-up-fill text-primary me-1"></i> Outgoing API Mock</strong>
                            <div class="text-muted small mt-1">Simulates 1.8s slow outbound call to Stripe PaymentIntents API.</div>
                        </div>
                        <button class="btn btn-outline-primary w-100 rounded-pill fw-semibold mt-3" onclick="triggerSlowExternalApi()">
                            <i class="bi bi-send-fill me-1"></i> Call Mock API
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Live Execution Console & Output -->
    <div class="col-lg-5">
        <div class="card card-custom p-4 h-100 d-flex flex-column">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                    <i class="bi bi-terminal-fill text-dark"></i> Anomaly Lab Console
                </h5>
                <button class="btn btn-sm btn-outline-secondary rounded-pill px-2 py-0.5" onclick="clearConsole()">
                    <i class="bi bi-trash3"></i> Clear
                </button>
            </div>

            <!-- Terminal Output Window -->
            <div id="terminalWindow" class="bg-dark text-white rounded-3 p-3 font-mono small flex-grow-1" style="min-height: 380px; max-height: 480px; overflow-y: auto; font-size: 0.8rem;">
                <div class="text-success">[READY] Pulse Anomaly Lab initialized.</div>
                <div class="text-secondary">[INFO] Click any generator on the left to fire synthetic telemetry.</div>
                <div class="text-secondary">[INFO] Recorded metrics are instantly indexed in MySQL `pulse_entries`.</div>
            </div>

            <div class="mt-3 p-2 bg-light rounded-3 d-flex justify-content-between align-items-center text-muted small">
                <span><i class="bi bi-broadcast text-danger me-1"></i> Live AJAX Dispatcher</span>
                <span id="lastActionTimestamp">Idle</span>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const terminal = document.getElementById('terminalWindow');

    function logTerminal(message, type = 'info') {
        const time = new Date().toLocaleTimeString();
        let colorClass = 'text-white';
        if (type === 'success') colorClass = 'text-success';
        if (type === 'warning') colorClass = 'text-warning';
        if (type === 'danger') colorClass = 'text-danger';
        if (type === 'muted') colorClass = 'text-secondary';

        const line = document.createElement('div');
        line.className = colorClass + ' mt-1';
        line.innerHTML = `<span class="text-secondary">[${time}]</span> ${message}`;
        terminal.appendChild(line);
        terminal.scrollTop = terminal.scrollHeight;
        document.getElementById('lastActionTimestamp').innerText = `Last run at ${time}`;
    }

    function clearConsole() {
        terminal.innerHTML = '<div class="text-secondary">[Console Cleared] Ready for next simulation.</div>';
    }

    function postSimulation(url, data, label) {
        logTerminal(`[DISPATCH] Triggering ${label}...`, 'muted');

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(res => res.json())
        .then(result => {
            if (result.success) {
                logTerminal(`[SUCCESS] ${result.message}`, 'success');
            } else {
                logTerminal(`[ERROR] ${result.message || 'Simulation failed'}`, 'danger');
            }
        })
        .catch(err => {
            logTerminal(`[FAIL] Network or server error: ${err.message}`, 'danger');
        });
    }

    function triggerTrafficBurst() {
        const count = document.getElementById('burstSlider').value;
        postSimulation('{{ route("pulse.simulator.traffic.burst") }}', { count: count }, `Traffic Burst (${count} reqs)`);
    }

    function triggerSlowQuery() {
        postSimulation('{{ route("pulse.simulator.slow.query") }}', {}, 'Slow Query & N+1 Loop');
    }

    function triggerMemorySurge() {
        const mb = document.getElementById('memorySlider').value;
        postSimulation('{{ route("pulse.simulator.memory.surge") }}', { mb: mb }, `Memory Surge (+${mb} MB)`);
    }

    function triggerException() {
        const type = document.getElementById('exceptionSelect').value;
        postSimulation('{{ route("pulse.simulator.exception.trigger") }}', { type: type }, `Exception [${type}]`);
    }

    function triggerCacheChurn() {
        postSimulation('{{ route("pulse.simulator.cache.churn") }}', { iterations: 40 }, 'Cache Churn (40 ops)');
    }

    function triggerSlowExternalApi() {
        postSimulation('{{ route("pulse.simulator.slow.external.api") }}', {}, 'Slow Outbound API (Stripe Mock)');
    }
</script>
@endsection
