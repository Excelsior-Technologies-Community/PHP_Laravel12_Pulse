<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PulseCleanupCommand extends Command
{
    protected $signature = 'pulse:cleanup {--days=30}';

    protected $description = 'Clean old Laravel Pulse monitoring records';

    public function handle(): int
    {
        $days = (int) $this->option('days');

        $cutoffTimestamp = Carbon::now()
            ->subDays($days)
            ->timestamp;

        $this->newLine();

        $this->info('Laravel Pulse Cleanup');
        $this->line(str_repeat('-', 45));

        $entriesBefore = DB::table('pulse_entries')->count();
        $valuesBefore = DB::table('pulse_values')->count();
        $aggregatesBefore = DB::table('pulse_aggregates')->count();

        $deletedEntries = DB::table('pulse_entries')
            ->where('timestamp', '<', $cutoffTimestamp)
            ->delete();

        $deletedValues = DB::table('pulse_values')
            ->where('timestamp', '<', $cutoffTimestamp)
            ->delete();

        $deletedAggregates = DB::table('pulse_aggregates')
            ->where('bucket', '<', $cutoffTimestamp)
            ->delete();

        $entriesAfter = DB::table('pulse_entries')->count();
        $valuesAfter = DB::table('pulse_values')->count();
        $aggregatesAfter = DB::table('pulse_aggregates')->count();

        $this->table(
            ['Table', 'Before', 'Deleted', 'Remaining'],
            [
                [
                    'pulse_entries',
                    $entriesBefore,
                    $deletedEntries,
                    $entriesAfter,
                ],
                [
                    'pulse_values',
                    $valuesBefore,
                    $deletedValues,
                    $valuesAfter,
                ],
                [
                    'pulse_aggregates',
                    $aggregatesBefore,
                    $deletedAggregates,
                    $aggregatesAfter,
                ],
            ]
        );

        $this->newLine();

        $this->info("Cleanup completed successfully.");

        $this->line("Records older than {$days} days were removed.");

        $this->newLine();

        return self::SUCCESS;
    }
}