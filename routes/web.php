<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PulseDashboardController;
use App\Http\Controllers\PulseExportController;
use Illuminate\Support\Facades\DB;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test-query', function () {
    DB::table('users')->get();
    return 'Query Executed';
});

Route::get('/slow-page', function () {
    sleep(3);
    return 'Slow Page Loaded';
});


Route::get(
    '/custom-pulse',
    [
        PulseDashboardController::class,
        'index'
    ]
);

Route::get('/pulse-export', function () {
    return view('pulse.export');
})->name('pulse.export');

Route::get('/pulse-export/download', [PulseExportController::class, 'export'])
    ->name('pulse.export.csv');
