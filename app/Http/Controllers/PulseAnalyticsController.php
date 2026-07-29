<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class PulseAnalyticsController extends Controller
{
    public function index()
    {
        // Dashboard Statistics
        $totalUsers = DB::table('users')->count();

        $totalPulseEntries = DB::table('pulse_entries')->count();

        $slowRequests = DB::table('pulse_entries')
            ->where('type', 'slow_request')
            ->count();

        $exceptions = DB::table('pulse_entries')
            ->where('type', 'exception')
            ->count();

        $cacheMisses = DB::table('pulse_entries')
            ->where('type', 'cache_miss')
            ->count();

        $activityLogs = DB::table('activity_logs')->count();

        /*
        |--------------------------------------------------------------------------
        | System Health Status
        |--------------------------------------------------------------------------
        */

        $status = 'Healthy';

        if ($slowRequests > 10 || $exceptions > 10) {
            $status = 'Warning';
        }

        if ($slowRequests > 25 || $exceptions > 25) {
            $status = 'Critical';
        }

        // Latest Pulse Records
        $latestEntries = DB::table('pulse_entries')
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        // Latest Activity Logs
        $latestActivities = DB::table('activity_logs')
            ->latest()
            ->limit(10)
            ->get();

        // Top Pulse Types
        $pulseTypes = DB::table('pulse_entries')
            ->select(
                'type',
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('type')
            ->orderByDesc('total')
            ->get();

        // Hourly Statistics (Last 24 Hours)
        $hourlyStats = DB::table('pulse_entries')
            ->selectRaw("
                HOUR(FROM_UNIXTIME(timestamp)) as hour,
                COUNT(*) as total
            ")
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        return view(
            'pulse.analytics',
            compact(
                'totalUsers',
                'totalPulseEntries',
                'slowRequests',
                'exceptions',
                'cacheMisses',
                'activityLogs',
                'status',
                'latestEntries',
                'latestActivities',
                'pulseTypes',
                'hourlyStats'
            )
        );
    }
}
