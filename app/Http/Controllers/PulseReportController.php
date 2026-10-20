<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PulseReportController extends Controller
{
    /**
     * Display Report Studio & Filtered Explorer.
     */
    public function index(Request $request)
    {
        $range = $request->input('range', '24h');
        $type = $request->input('type', 'all');

        $query = DB::table('pulse_entries');

        // Apply Time Filter
        $cutoff = $this->getCutoffTimestamp($range);
        if ($cutoff) {
            $query->where('timestamp', '>=', $cutoff);
        }

        // Apply Metric Type Filter
        if ($type !== 'all') {
            $query->where('type', $type);
        }

        $totalMatching = (clone $query)->count();
        $entries = $query->orderByDesc('timestamp')->paginate(15)->withQueryString();

        // High-level metrics for selected filter
        $availableTypes = DB::table('pulse_entries')->select('type')->distinct()->pluck('type');

        $stats = [
            'total' => $totalMatching,
            'slow_requests' => DB::table('pulse_entries')->where('type', 'slow_request')->when($cutoff, fn($q) => $q->where('timestamp', '>=', $cutoff))->count(),
            'exceptions' => DB::table('pulse_entries')->where('type', 'exception')->when($cutoff, fn($q) => $q->where('timestamp', '>=', $cutoff))->count(),
            'slow_queries' => DB::table('pulse_entries')->where('type', 'slow_query')->when($cutoff, fn($q) => $q->where('timestamp', '>=', $cutoff))->count(),
            'cache_misses' => DB::table('pulse_entries')->where('type', 'cache_miss')->when($cutoff, fn($q) => $q->where('timestamp', '>=', $cutoff))->count(),
        ];

        return view('pulse.reports', compact(
            'entries',
            'range',
            'type',
            'availableTypes',
            'stats',
            'totalMatching'
        ));
    }

    /**
     * Export Filtered Metrics to Professional CSV.
     */
    public function exportCsv(Request $request)
    {
        $range = $request->input('range', '24h');
        $type = $request->input('type', 'all');

        $query = DB::table('pulse_entries');
        $cutoff = $this->getCutoffTimestamp($range);
        if ($cutoff) {
            $query->where('timestamp', '>=', $cutoff);
        }
        if ($type !== 'all') {
            $query->where('type', $type);
        }

        $records = $query->orderByDesc('timestamp')->get();
        $fileName = 'pulse_metrics_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            "Content-Type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename={$fileName}",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0",
        ];

        $callback = function () use ($records) {
            $handle = fopen('php://output', 'w');
            // Write UTF-8 BOM for Excel compatibility
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            // CSV Header
            fputcsv($handle, ['Entry ID', 'Timestamp (UTC)', 'Metric Type', 'Key / Identifier', 'Value', 'Unit Description']);

            foreach ($records as $row) {
                $unit = match ($row->type) {
                    'slow_request', 'slow_query', 'slow_outgoing_request' => 'ms (latency)',
                    'memory_surge' => 'MB',
                    'exception' => 'occurrences',
                    default => 'count',
                };

                fputcsv($handle, [
                    $row->id ?? 'N/A',
                    date('Y-m-d H:i:s', $row->timestamp),
                    $row->type,
                    $row->key,
                    $row->value,
                    $unit,
                ]);
            }

            fclose($handle);
        };

        return new StreamedResponse($callback, 200, $headers);
    }

    /**
     * Export Filtered Metrics to JSON Payload.
     */
    public function exportJson(Request $request)
    {
        $range = $request->input('range', '24h');
        $type = $request->input('type', 'all');

        $query = DB::table('pulse_entries');
        $cutoff = $this->getCutoffTimestamp($range);
        if ($cutoff) {
            $query->where('timestamp', '>=', $cutoff);
        }
        if ($type !== 'all') {
            $query->where('type', $type);
        }

        $records = $query->orderByDesc('timestamp')->limit(1000)->get();

        $payload = [
            'generator' => 'Laravel Pulse Studio Pro',
            'exported_at' => now()->toIso8601String(),
            'filter_range' => $range,
            'filter_type' => $type,
            'record_count' => $records->count(),
            'data' => $records->map(function ($row) {
                return [
                    'id' => $row->id,
                    'timestamp' => date('Y-m-d H:i:s', $row->timestamp),
                    'unix_timestamp' => $row->timestamp,
                    'type' => $row->type,
                    'key' => $row->key,
                    'value' => $row->value,
                ];
            }),
        ];

        $fileName = 'pulse_metrics_' . now()->format('Ymd_His') . '.json';

        return response()->json($payload, 200, [
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ], JSON_PRETTY_PRINT);
    }

    /**
     * Executive Performance Summary (Printable / PDF format).
     */
    public function executiveReport(Request $request)
    {
        $range = $request->input('range', '7d');
        $cutoff = $this->getCutoffTimestamp($range);

        $totalPulseEntries = DB::table('pulse_entries')
            ->when($cutoff, fn($q) => $q->where('timestamp', '>=', $cutoff))
            ->count();

        $slowRequests = DB::table('pulse_entries')
            ->where('type', 'slow_request')
            ->when($cutoff, fn($q) => $q->where('timestamp', '>=', $cutoff))
            ->count();

        $exceptions = DB::table('pulse_entries')
            ->where('type', 'exception')
            ->when($cutoff, fn($q) => $q->where('timestamp', '>=', $cutoff))
            ->count();

        $topSlowEndpoints = DB::table('pulse_entries')
            ->where('type', 'slow_request')
            ->when($cutoff, fn($q) => $q->where('timestamp', '>=', $cutoff))
            ->select('key', DB::raw('COUNT(*) as occurrences'), DB::raw('MAX(value) as max_latency'), DB::raw('AVG(value) as avg_latency'))
            ->groupBy('key')
            ->orderByDesc('occurrences')
            ->limit(5)
            ->get();

        $topExceptions = DB::table('pulse_entries')
            ->where('type', 'exception')
            ->when($cutoff, fn($q) => $q->where('timestamp', '>=', $cutoff))
            ->select('key', DB::raw('COUNT(*) as total'))
            ->groupBy('key')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $activeIncidents = DB::table('pulse_incidents')
            ->where('status', '!=', 'resolved')
            ->get();

        $resolvedIncidentsCount = DB::table('pulse_incidents')
            ->where('status', 'resolved')
            ->count();

        $totalUsers = DB::table('users')->count();

        return view('pulse.executive_report', compact(
            'range',
            'totalPulseEntries',
            'slowRequests',
            'exceptions',
            'topSlowEndpoints',
            'topExceptions',
            'activeIncidents',
            'resolvedIncidentsCount',
            'totalUsers'
        ));
    }

    /**
     * Helper to compute cutoff unix timestamp.
     */
    protected function getCutoffTimestamp(string $range): ?int
    {
        return match ($range) {
            '1h' => Carbon::now()->subHour()->timestamp,
            '24h' => Carbon::now()->subDay()->timestamp,
            '7d' => Carbon::now()->subDays(7)->timestamp,
            '30d' => Carbon::now()->subDays(30)->timestamp,
            default => null,
        };
    }
}
