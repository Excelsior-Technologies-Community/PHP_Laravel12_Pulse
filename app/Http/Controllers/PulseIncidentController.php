<?php

namespace App\Http\Controllers;

use App\Services\PulseAlertService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PulseIncidentController extends Controller
{
    protected PulseAlertService $alertService;

    public function __construct(PulseAlertService $alertService)
    {
        $this->alertService = $alertService;
    }

    /**
     * Display Incident Guard & Alerts Studio.
     */
    public function index()
    {
        // Auto-evaluate current metrics
        $evalResult = $this->alertService->evaluateRules();
        $systemHealth = $evalResult['status'];

        $openIncidents = DB::table('pulse_incidents')
            ->whereIn('status', ['open', 'acknowledged'])
            ->orderByDesc('id')
            ->get();

        $resolvedIncidents = DB::table('pulse_incidents')
            ->where('status', 'resolved')
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        $thresholdRules = DB::table('pulse_threshold_rules')
            ->orderBy('id')
            ->get();

        // Count metrics for summary
        $totalIncidents = DB::table('pulse_incidents')->count();
        $criticalCount = DB::table('pulse_incidents')->where('severity', 'critical')->count();
        $openCount = $openIncidents->count();

        return view('pulse.incidents', compact(
            'systemHealth',
            'openIncidents',
            'resolvedIncidents',
            'thresholdRules',
            'totalIncidents',
            'criticalCount',
            'openCount'
        ));
    }

    /**
     * Run Manual Rules Evaluation.
     */
    public function evaluate()
    {
        $res = $this->alertService->evaluateRules();
        return back()->with('success', "Threshold guard evaluated. System Health: {$res['status']}. New breaches: {$res['new_incidents']}.");
    }

    /**
     * Acknowledge Incident.
     */
    public function acknowledge(int $id)
    {
        DB::table('pulse_incidents')->where('id', $id)->update([
            'status' => 'acknowledged',
            'updated_at' => now(),
        ]);

        return back()->with('info', "Incident #{$id} marked as Acknowledged (Under Investigation).");
    }

    /**
     * Resolve Incident.
     */
    public function resolve(int $id)
    {
        DB::table('pulse_incidents')->where('id', $id)->update([
            'status' => 'resolved',
            'resolved_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', "Incident #{$id} resolved successfully.");
    }

    /**
     * Dispatch Synthetic Test Alert.
     */
    public function testAlert()
    {
        $id = $this->alertService->dispatchTestAlert();
        return back()->with('warning', "Simulated Critical Incident #{$id} created and dispatched to the active alert feed.");
    }

    /**
     * Update Threshold Values.
     */
    public function updateThresholds(Request $request)
    {
        $rules = $request->input('rules', []);

        foreach ($rules as $id => $data) {
            DB::table('pulse_threshold_rules')
                ->where('id', $id)
                ->update([
                    'warning_threshold' => (float) ($data['warning'] ?? 10),
                    'critical_threshold' => (float) ($data['critical'] ?? 25),
                    'is_active' => isset($data['active']),
                    'updated_at' => now(),
                ]);
        }

        $this->alertService->evaluateRules();

        return back()->with('success', 'Threshold rules updated and recalculated.');
    }
}
