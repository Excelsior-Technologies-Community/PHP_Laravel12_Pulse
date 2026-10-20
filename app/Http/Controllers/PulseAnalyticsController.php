<?php

namespace App\Http\Controllers;

use App\Services\BusinessPulseRecorder;
use App\Services\PulseAlertService;
use Illuminate\Support\Facades\DB;

class PulseAnalyticsController extends Controller
{
    protected BusinessPulseRecorder $businessRecorder;
    protected PulseAlertService $alertService;

    public function __construct(BusinessPulseRecorder $businessRecorder, PulseAlertService $alertService)
    {
        $this->businessRecorder = $businessRecorder;
        $this->alertService = $alertService;
    }

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

        $slowQueries = DB::table('pulse_entries')
            ->where('type', 'slow_query')
            ->count();

        $activityLogs = DB::table('activity_logs')->count();

        // Real-Time System Health Status via Threshold Rules Engine
        $status = $this->alertService->getSystemHealthStatus();
        $openIncidentsCount = DB::table('pulse_incidents')->where('status', '!=', 'resolved')->count();

        // Business Metrics (Feature 4)
        $businessSummary = $this->businessRecorder->getSummary();

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
            ->select('type', DB::raw('COUNT(*) as total'))
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
                'slowQueries',
                'activityLogs',
                'status',
                'openIncidentsCount',
                'businessSummary',
                'latestEntries',
                'latestActivities',
                'pulseTypes',
                'hourlyStats'
            )
        );
    }
}
