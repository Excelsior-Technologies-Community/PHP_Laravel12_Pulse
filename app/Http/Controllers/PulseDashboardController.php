<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PulseDashboardController extends Controller
{
    public function index()
    {
        $users = DB::table('users')->count();

        $queries = DB::table('pulse_entries')
            ->count();

        return view('pulse.dashboard', compact(
            'users',
            'queries'
        ));
    }
}
