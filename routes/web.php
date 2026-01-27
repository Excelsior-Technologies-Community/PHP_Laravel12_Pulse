<?php

use Illuminate\Support\Facades\Route;
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
