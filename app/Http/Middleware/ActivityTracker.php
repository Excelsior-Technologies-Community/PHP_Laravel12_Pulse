<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class ActivityTracker
{

    public function handle(Request $request, Closure $next)
    {

        DB::table('activity_logs')->insert([

            'url' => $request->url(),

            'method' => $request->method(),

            'created_at' => now(),

            'updated_at' => now()

        ]);


        return $next($request);
    }
}
