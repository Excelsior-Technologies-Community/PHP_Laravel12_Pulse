<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;


class PulseCheck extends Command
{

    protected $signature = 'pulse:check';


    protected $description = 'Check Pulse Records';


    public function handle()
    {

        $count = DB::table('pulse_entries')
            ->count();


        if ($count > 100) {

            $this->error(
                "High Pulse Activity Detected"
            );
        } else {

            $this->info(
                "Pulse System Normal"
            );
        }
    }
}
