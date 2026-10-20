<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Laravel\Pulse\Facades\Pulse;

class BusinessPulseRecorder
{
    /**
     * Record a user login event.
     */
    public function recordLogin(string $email, bool $success = true): void
    {
        $statusStr = $success ? 'success' : 'failed';

        // Record in Pulse
        try {
            Pulse::record(
                type: 'business_login',
                key: $email,
                value: 1,
                timestamp: now()->timestamp
            )->count();
        } catch (\Throwable $e) {
            // Graceful fallback
        }

        // Record in Activity Logs
        DB::table('activity_logs')->insert([
            'url' => '/auth/login',
            'method' => 'POST',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Record an API endpoint consumption.
     */
    public function recordApiCall(string $endpoint, string $method = 'GET', int $statusCode = 200, float $durationMs = 25.0): void
    {
        try {
            Pulse::record(
                type: 'business_api_call',
                key: "{$method} {$endpoint}",
                value: (int) $durationMs,
                timestamp: now()->timestamp
            )->count()->avg();
        } catch (\Throwable $e) {
            // Graceful fallback
        }

        DB::table('activity_logs')->insert([
            'url' => $endpoint,
            'method' => $method,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Record a new user registration event.
     */
    public function recordUserRegistration(string $name, string $email): void
    {
        try {
            Pulse::record(
                type: 'business_registration',
                key: $email,
                value: 1,
                timestamp: now()->timestamp
            )->count();
        } catch (\Throwable $e) {
            // Graceful fallback
        }

        DB::table('activity_logs')->insert([
            'url' => '/auth/register',
            'method' => 'POST',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Record a business checkout transaction.
     */
    public function recordCheckoutEvent(float $amount, string $currency = 'USD'): void
    {
        try {
            Pulse::record(
                type: 'business_checkout',
                key: "checkout_{$currency}",
                value: (int) $amount,
                timestamp: now()->timestamp
            )->count()->sum();
        } catch (\Throwable $e) {
            // Graceful fallback
        }

        DB::table('activity_logs')->insert([
            'url' => '/api/v1/checkout',
            'method' => 'POST',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Get aggregate business metrics summary for Dashboard cards.
     */
    public function getSummary(): array
    {
        $todayActivities = DB::table('activity_logs')
            ->whereDate('created_at', now()->today())
            ->count();

        $totalActivities = DB::table('activity_logs')->count();

        // Top API endpoints consumed
        $topEndpoints = DB::table('activity_logs')
            ->select('url', 'method', DB::raw('COUNT(*) as total'))
            ->groupBy('url', 'method')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // Business Pulse Entries
        $businessLogins = DB::table('pulse_entries')
            ->where('type', 'business_login')
            ->count();

        $businessRegistrations = DB::table('pulse_entries')
            ->where('type', 'business_registration')
            ->count();

        $businessApiCount = DB::table('pulse_entries')
            ->where('type', 'business_api_call')
            ->count();

        return [
            'today_activities' => $todayActivities,
            'total_activities' => $totalActivities,
            'top_endpoints' => $topEndpoints,
            'business_logins' => $businessLogins,
            'business_registrations' => $businessRegistrations,
            'business_api_count' => $businessApiCount,
        ];
    }
}
