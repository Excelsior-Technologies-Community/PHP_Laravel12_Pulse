<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PulseDashboardController;
use App\Http\Controllers\PulseAnalyticsController;
use App\Http\Controllers\PulseSimulatorController;
use App\Http\Controllers\PulseIncidentController;
use App\Http\Controllers\PulseReportController;
use App\Http\Controllers\PulseExportController;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| Web Routes - Laravel Pulse Studio Pro
|--------------------------------------------------------------------------
*/

// Root Redirect to Pulse Custom Dashboard
Route::get('/', function () {
    return redirect('/custom-pulse');
});

// Demo query & slow test routes
Route::get('/test-query', function () {
    DB::table('users')->get();
    return 'Query Executed';
});

Route::get('/slow-page', function () {
    sleep(3);
    return 'Slow Page Loaded';
});

// Custom Dashboard & Business Metrics (Feature 4)
Route::get('/custom-pulse', [PulseDashboardController::class, 'index'])->name('pulse.dashboard');
Route::post('/pulse/business-event', [PulseDashboardController::class, 'dispatchBusinessEvent'])->name('pulse.business.event');

// Feature 1: Interactive Chaos & Traffic Simulator (Anomaly Lab)
Route::prefix('pulse/simulator')->name('pulse.simulator.')->group(function () {
    Route::get('/', [PulseSimulatorController::class, 'index'])->name('index');
    Route::post('/traffic-burst', [PulseSimulatorController::class, 'trafficBurst'])->name('traffic.burst');
    Route::post('/slow-query', [PulseSimulatorController::class, 'slowQuery'])->name('slow.query');
    Route::post('/memory-surge', [PulseSimulatorController::class, 'memorySurge'])->name('memory.surge');
    Route::post('/exception-trigger', [PulseSimulatorController::class, 'exceptionTrigger'])->name('exception.trigger');
    Route::post('/cache-churn', [PulseSimulatorController::class, 'cacheChurn'])->name('cache.churn');
    Route::post('/slow-external-api', [PulseSimulatorController::class, 'slowExternalApi'])->name('slow.external.api');
});

// Feature 2: Real-Time Health Incident Alerting & Threshold Guard
Route::prefix('pulse/incidents')->name('pulse.incidents.')->group(function () {
    Route::get('/', [PulseIncidentController::class, 'index'])->name('index');
    Route::post('/evaluate', [PulseIncidentController::class, 'evaluate'])->name('evaluate');
    Route::post('/{id}/acknowledge', [PulseIncidentController::class, 'acknowledge'])->name('acknowledge');
    Route::post('/{id}/resolve', [PulseIncidentController::class, 'resolve'])->name('resolve');
    Route::post('/test-alert', [PulseIncidentController::class, 'testAlert'])->name('test.alert');
    Route::post('/update-thresholds', [PulseIncidentController::class, 'updateThresholds'])->name('update.thresholds');
});

// Feature 3: Advanced Multi-Format Metrics Export & Report Studio
Route::prefix('pulse/reports')->name('pulse.reports.')->group(function () {
    Route::get('/', [PulseReportController::class, 'index'])->name('index');
    Route::get('/export-csv', [PulseReportController::class, 'exportCsv'])->name('export.csv');
    Route::get('/export-json', [PulseReportController::class, 'exportJson'])->name('export.json');
    Route::get('/executive', [PulseReportController::class, 'executiveReport'])->name('executive');
});

// Legacy Export compatibility
Route::get('/pulse-export', function () {
    return redirect()->route('pulse.reports.index');
})->name('pulse.export');

Route::get('/pulse-export/download', [PulseExportController::class, 'export'])->name('pulse.export.csv');

// Analytics Center
Route::get('/pulse-analytics', [PulseAnalyticsController::class, 'index'])->name('pulse.analytics');
