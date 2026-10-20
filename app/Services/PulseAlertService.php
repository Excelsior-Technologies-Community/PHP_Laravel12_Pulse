<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class PulseAlertService
{
    /**
     * Evaluate live Pulse metrics against threshold rules and create incidents if breached.
     */
    public function evaluateRules(): array
    {
        $rules = DB::table('pulse_threshold_rules')->where('is_active', true)->get();
        $newIncidentsCount = 0;

        foreach ($rules as $rule) {
            $currentValue = $this->getCurrentMetricValue($rule->metric_key);

            if ($currentValue >= $rule->critical_threshold) {
                $severity = 'critical';
            } elseif ($currentValue >= $rule->warning_threshold) {
                $severity = 'warning';
            } else {
                continue;
            }

            // Check if open incident already exists for this metric type
            $existing = DB::table('pulse_incidents')
                ->where('metric_type', $rule->metric_key)
                ->where('status', '!=', 'resolved')
                ->first();

            if (!$existing) {
                DB::table('pulse_incidents')->insert([
                    'title' => "{$rule->label} threshold breach ({$currentValue} {$rule->unit})",
                    'metric_type' => $rule->metric_key,
                    'severity' => $severity,
                    'threshold_value' => $severity === 'critical' ? $rule->critical_threshold : $rule->warning_threshold,
                    'actual_value' => $currentValue,
                    'status' => 'open',
                    'details' => json_encode([
                        'message' => "Observed {$currentValue} {$rule->unit}, exceeding the {$severity} threshold of " .
                            ($severity === 'critical' ? $rule->critical_threshold : $rule->warning_threshold) . " {$rule->unit}.",
                        'timestamp' => now()->toIso8601String(),
                    ]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $newIncidentsCount++;
            } else {
                // Escalate severity if changed to critical
                if ($existing->severity !== 'critical' && $severity === 'critical') {
                    DB::table('pulse_incidents')->where('id', $existing->id)->update([
                        'severity' => 'critical',
                        'actual_value' => $currentValue,
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        return [
            'status' => $this->getSystemHealthStatus(),
            'new_incidents' => $newIncidentsCount,
        ];
    }

    /**
     * Get real-time count or metric value.
     */
    public function getCurrentMetricValue(string $metricKey): float
    {
        switch ($metricKey) {
            case 'slow_requests':
                return (float) DB::table('pulse_entries')
                    ->where('type', 'slow_request')
                    ->count();

            case 'exceptions':
                return (float) DB::table('pulse_entries')
                    ->where('type', 'exception')
                    ->count();

            case 'cache_misses':
                return (float) DB::table('pulse_entries')
                    ->where('type', 'cache_miss')
                    ->count();

            case 'slow_queries':
                return (float) DB::table('pulse_entries')
                    ->where('type', 'slow_query')
                    ->count();

            case 'memory':
                $memoryBytes = memory_get_peak_usage(true);
                return round($memoryBytes / (1024 * 1024), 1);

            default:
                return (float) DB::table('pulse_entries')
                    ->where('type', $metricKey)
                    ->count();
        }
    }

    /**
     * Determine system health: Healthy, Warning, or Critical.
     */
    public function getSystemHealthStatus(): string
    {
        $hasCritical = DB::table('pulse_incidents')
            ->where('status', '!=', 'resolved')
            ->where('severity', 'critical')
            ->exists();

        if ($hasCritical) {
            return 'Critical';
        }

        $hasWarning = DB::table('pulse_incidents')
            ->where('status', '!=', 'resolved')
            ->where('severity', 'warning')
            ->exists();

        if ($hasWarning) {
            return 'Warning';
        }

        return 'Healthy';
    }

    /**
     * Trigger a synthetic test alert for demonstration.
     */
    public function dispatchTestAlert(): int
    {
        return DB::table('pulse_incidents')->insertGetId([
            'title' => 'Simulated Critical Performance Anomaly: Gateway Timeout Spike',
            'metric_type' => 'slow_requests',
            'severity' => 'critical',
            'threshold_value' => 25.0,
            'actual_value' => 42.0,
            'status' => 'open',
            'details' => json_encode([
                'source' => 'Manual Diagnostic Test Dispatcher',
                'description' => 'Synthetically triggered alert to verify incident response, web notifications, and incident triage pipeline.',
                'timestamp' => now()->toIso8601String(),
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
