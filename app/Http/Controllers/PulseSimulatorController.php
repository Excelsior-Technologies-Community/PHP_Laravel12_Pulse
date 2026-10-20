<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\PulseAlertService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Laravel\Pulse\Facades\Pulse;

class PulseSimulatorController extends Controller
{
    protected PulseAlertService $alertService;

    public function __construct(PulseAlertService $alertService)
    {
        $this->alertService = $alertService;
    }

    /**
     * Display the Chaos & Traffic Simulator Studio.
     */
    public function index()
    {
        $totalPulseEntries = DB::table('pulse_entries')->count();
        $slowRequests = DB::table('pulse_entries')->where('type', 'slow_request')->count();
        $exceptions = DB::table('pulse_entries')->where('type', 'exception')->count();
        $cacheMisses = DB::table('pulse_entries')->where('type', 'cache_miss')->count();
        $slowQueries = DB::table('pulse_entries')->where('type', 'slow_query')->count();
        $activityLogs = DB::table('activity_logs')->count();

        $recentSimulations = DB::table('activity_logs')
            ->where('url', 'like', '/pulse/simulate%')
            ->orWhere('url', 'like', '/api/v1/%')
            ->latest()
            ->limit(8)
            ->get();

        return view('pulse.simulator', compact(
            'totalPulseEntries',
            'slowRequests',
            'exceptions',
            'cacheMisses',
            'slowQueries',
            'activityLogs',
            'recentSimulations'
        ));
    }

    /**
     * 1. Traffic Burst Simulator.
     */
    public function trafficBurst(Request $request)
    {
        $count = (int) $request->input('count', 15);
        $count = max(1, min(50, $count));
        $endpoints = ['/api/v1/users', '/api/v1/products', '/api/v1/orders', '/api/v1/checkout', '/search'];
        $methods = ['GET', 'POST', 'GET', 'PUT', 'GET'];

        $startTime = microtime(true);

        for ($i = 0; $i < $count; $i++) {
            $endpoint = $endpoints[array_rand($endpoints)];
            $method = $methods[array_rand($methods)];
            $isSlow = ($i % 4 === 0);
            $durationMs = $isSlow ? rand(1100, 2800) : rand(15, 120);

            // Record into activity_logs
            DB::table('activity_logs')->insert([
                'url' => $endpoint,
                'method' => $method,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Record into Pulse
            $pulseKey = json_encode([$method, $endpoint, 'App\Http\Controllers\PulseSimulatorController@trafficBurst']);
            try {
                if ($isSlow) {
                    Pulse::record('slow_request', $pulseKey, $durationMs, now()->timestamp)->count()->max();
                }
                Pulse::record('request', $pulseKey, $durationMs, now()->timestamp)->count()->avg();
            } catch (\Throwable $e) {
                // Direct fallback insertion into pulse_entries if needed
                DB::table('pulse_entries')->insert([
                    'timestamp' => now()->timestamp,
                    'type' => $isSlow ? 'slow_request' : 'request',
                    'key' => $pulseKey,
                    'value' => $durationMs,
                ]);
            }
        }

        $elapsedMs = round((microtime(true) - $startTime) * 1000, 1);
        $this->alertService->evaluateRules();

        return response()->json([
            'success' => true,
            'message' => "Generated burst of {$count} synthetic HTTP requests in {$elapsedMs}ms.",
            'requests_generated' => $count,
            'elapsed_ms' => $elapsedMs,
        ]);
    }

    /**
     * 2. Slow Queries & N+1 Relational Loop Simulator.
     */
    public function slowQuery(Request $request)
    {
        $startTime = microtime(true);

        // 1. Trigger actual SQL delay
        try {
            DB::select("SELECT SLEEP(1.1) as delayed_execution");
        } catch (\Throwable $e) {
            usleep(1100000);
        }

        // 2. Intentional N+1 Query Loop
        $users = User::limit(10)->get();
        $activityCount = 0;
        foreach ($users as $user) {
            $activityCount += DB::table('activity_logs')->where('url', 'like', '%user%')->count();
        }

        $durationMs = round((microtime(true) - $startTime) * 1000, 1);
        $sqlKey = "SELECT * FROM `users` LEFT JOIN `activity_logs` ON `users`.`id` WHERE `created_at` >= ?";
        $pulseKey = json_encode([$sqlKey, 'app/Http/Controllers/PulseSimulatorController.php:125']);

        try {
            Pulse::record('slow_query', $pulseKey, (int) $durationMs, now()->timestamp)->count()->max();
        } catch (\Throwable $e) {
            DB::table('pulse_entries')->insert([
                'timestamp' => now()->timestamp,
                'type' => 'slow_query',
                'key' => $pulseKey,
                'value' => (int) $durationMs,
            ]);
        }

        $this->alertService->evaluateRules();

        return response()->json([
            'success' => true,
            'message' => "Executed simulated N+1 query loop and 1.1s delay. Query latency: {$durationMs}ms.",
            'duration_ms' => $durationMs,
            'sql_sample' => $sqlKey,
        ]);
    }

    /**
     * 3. Memory Surge Simulator.
     */
    public function memorySurge(Request $request)
    {
        $mb = (int) $request->input('mb', 35);
        $mb = max(10, min(80, $mb));

        $memoryBefore = memory_get_usage(true);

        // Allocate temporary buffer
        $buffer = [];
        $chunk = str_repeat('LARAVEL_PULSE_MEMORY_STRESS_TEST_BUFFER_', 1024); // ~40KB per element
        $targetCount = (int) (($mb * 1024 * 1024) / strlen($chunk));

        for ($i = 0; $i < $targetCount; $i++) {
            $buffer[] = $chunk . '_' . $i;
        }

        $memoryPeak = memory_get_peak_usage(true);
        $allocatedMb = round(($memoryPeak - $memoryBefore) / (1024 * 1024), 2);
        $totalPeakMb = round($memoryPeak / (1024 * 1024), 2);

        // Record into Pulse
        try {
            Pulse::set('system_memory', 'server_primary', json_encode([
                'peak_mb' => $totalPeakMb,
                'allocated_mb' => $allocatedMb,
                'timestamp' => now()->toIso8601String(),
            ]));
        } catch (\Throwable $e) {}

        DB::table('pulse_entries')->insert([
            'timestamp' => now()->timestamp,
            'type' => 'memory_surge',
            'key' => 'php_worker_peak',
            'value' => (int) $totalPeakMb,
        ]);

        unset($buffer); // Free memory
        $this->alertService->evaluateRules();

        return response()->json([
            'success' => true,
            'message' => "Spiked PHP memory buffer by +{$allocatedMb}MB. Server Peak: {$totalPeakMb}MB.",
            'allocated_mb' => $allocatedMb,
            'peak_mb' => $totalPeakMb,
        ]);
    }

    /**
     * 4. Categorized Exception Simulator.
     */
    public function exceptionTrigger(Request $request)
    {
        $type = $request->input('type', 'PaymentGatewayTimeoutException');

        $exceptionsMap = [
            'PaymentGatewayTimeoutException' => [
                'class' => 'App\Exceptions\PaymentGatewayTimeoutException',
                'message' => 'Stripe webhook timed out after 10000ms: ETIMEDOUT 54.187.205.235:443',
            ],
            'DatabaseDeadlockException' => [
                'class' => 'Illuminate\Database\QueryException',
                'message' => 'SQLSTATE[40001]: Serialization failure: 1213 Deadlock found when trying to get lock; try restarting transaction',
            ],
            'ThirdPartyServiceException' => [
                'class' => 'App\Exceptions\ThirdPartyServiceException',
                'message' => 'SendGrid API returned HTTP 503: Mail Delivery Service Temporarily Unavailable',
            ],
            'ValidationException' => [
                'class' => 'Illuminate\Validation\ValidationException',
                'message' => 'The given payload data was invalid for parameter [account_number].',
            ],
        ];

        $selected = $exceptionsMap[$type] ?? $exceptionsMap['PaymentGatewayTimeoutException'];

        // Record in Pulse
        $pulseKey = json_encode([$selected['class'], 'app/Http/Controllers/PulseSimulatorController.php:240']);

        try {
            Pulse::record(
                type: 'exception',
                key: $pulseKey,
                value: 1,
                timestamp: now()->timestamp
            )->count();
        } catch (\Throwable $e) {
            DB::table('pulse_entries')->insert([
                'timestamp' => now()->timestamp,
                'type' => 'exception',
                'key' => $pulseKey,
                'value' => 1,
            ]);
        }

        $this->alertService->evaluateRules();

        return response()->json([
            'success' => true,
            'message' => "Simulated exception [{$type}] recorded in Pulse exceptions tracker.",
            'exception_class' => $selected['class'],
            'error_message' => $selected['message'],
        ]);
    }

    /**
     * 5. Cache Churn / Stampede Simulator.
     */
    public function cacheChurn(Request $request)
    {
        $iterations = (int) $request->input('iterations', 40);
        $misses = 0;
        $hits = 0;

        for ($i = 0; $i < $iterations; $i++) {
            $key = 'pulse_stress_cache_' . rand(1, 15);
            if (Cache::has($key)) {
                Cache::get($key);
                $hits++;
                Pulse::record('cache_hit', $key, 1, now()->timestamp)->count();
            } else {
                $misses++;
                Cache::put($key, 'stress_val_' . rand(100, 999), 10);
                Pulse::record('cache_miss', $key, 1, now()->timestamp)->count();
            }
        }

        $this->alertService->evaluateRules();

        return response()->json([
            'success' => true,
            'message' => "Simulated {$iterations} cache operations: {$hits} hits, {$misses} misses.",
            'hits' => $hits,
            'misses' => $misses,
        ]);
    }

    /**
     * 6. Slow External Outgoing API Simulator.
     */
    public function slowExternalApi(Request $request)
    {
        $startTime = microtime(true);
        // Simulate remote network latency
        usleep(rand(1200000, 2000000));
        $durationMs = round((microtime(true) - $startTime) * 1000, 1);

        $outgoingUrl = 'https://api.stripe.com/v1/payment_intents/pi_3NxExample';
        $pulseKey = json_encode(['POST', $outgoingUrl]);

        try {
            Pulse::record('slow_outgoing_request', $pulseKey, (int) $durationMs, now()->timestamp)->count()->max();
        } catch (\Throwable $e) {
            DB::table('pulse_entries')->insert([
                'timestamp' => now()->timestamp,
                'type' => 'slow_outgoing_request',
                'key' => $pulseKey,
                'value' => (int) $durationMs,
            ]);
        }

        $this->alertService->evaluateRules();

        return response()->json([
            'success' => true,
            'message' => "Simulated slow external request to Stripe API. Response time: {$durationMs}ms.",
            'duration_ms' => $durationMs,
            'url' => $outgoingUrl,
        ]);
    }
}
