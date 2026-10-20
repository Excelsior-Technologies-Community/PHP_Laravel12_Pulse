<?php

namespace App\Http\Controllers;

use App\Services\BusinessPulseRecorder;
use App\Services\PulseAlertService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PulseDashboardController extends Controller
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
        $users = DB::table('users')->count();
        $queries = DB::table('pulse_entries')->count();

        // System Health
        $systemHealth = $this->alertService->getSystemHealthStatus();
        $openIncidentsCount = DB::table('pulse_incidents')->where('status', '!=', 'resolved')->count();

        // Standard Pulse counters
        $slowRequests = DB::table('pulse_entries')->where('type', 'slow_request')->count();
        $exceptions = DB::table('pulse_entries')->where('type', 'exception')->count();
        $cacheMisses = DB::table('pulse_entries')->where('type', 'cache_miss')->count();

        // Business Metrics (Feature 4)
        $businessSummary = $this->businessRecorder->getSummary();

        // Recent Activity Logs
        $recentActivities = DB::table('activity_logs')->latest()->limit(6)->get();

        return view('pulse.dashboard', compact(
            'users',
            'queries',
            'systemHealth',
            'openIncidentsCount',
            'slowRequests',
            'exceptions',
            'cacheMisses',
            'businessSummary',
            'recentActivities'
        ));
    }

    /**
     * Dispatch interactive custom business event (Feature 4).
     */
    public function dispatchBusinessEvent(Request $request)
    {
        $eventType = $request->input('event_type', 'login');

        switch ($eventType) {
            case 'login':
                $email = 'user_' . rand(10, 99) . '@example.com';
                $this->businessRecorder->recordLogin($email, true);
                $msg = "Recorded user login for [{$email}].";
                break;

            case 'api_call':
                $endpoints = ['/api/v1/orders', '/api/v1/analytics', '/api/v1/customers', '/api/v1/search'];
                $endpoint = $endpoints[array_rand($endpoints)];
                $this->businessRecorder->recordApiCall($endpoint, 'GET', 200, rand(18, 95));
                $msg = "Recorded API consumption for [{$endpoint}].";
                break;

            case 'registration':
                $name = 'New User ' . rand(100, 999);
                $email = 'signup_' . rand(100, 999) . '@example.com';
                $this->businessRecorder->recordUserRegistration($name, $email);
                $msg = "Recorded user registration event for [{$email}].";
                break;

            case 'checkout':
                $amount = rand(25, 450);
                $this->businessRecorder->recordCheckoutEvent($amount, 'USD');
                $msg = "Recorded business checkout transaction for \${$amount} USD.";
                break;

            default:
                $msg = "Unknown business event type.";
        }

        return back()->with('success', $msg);
    }
}
