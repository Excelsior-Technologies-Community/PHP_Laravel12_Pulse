<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pulse_incidents', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('metric_type'); // slow_requests, exceptions, cache_misses, memory, queries
            $table->string('severity')->default('warning'); // notice, warning, critical
            $table->float('threshold_value')->default(0);
            $table->float('actual_value')->default(0);
            $table->string('status')->default('open'); // open, acknowledged, resolved
            $table->text('details')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });

        Schema::create('pulse_threshold_rules', function (Blueprint $table) {
            $table->id();
            $table->string('metric_key')->unique();
            $table->string('label');
            $table->float('warning_threshold')->default(10);
            $table->float('critical_threshold')->default(25);
            $table->string('unit')->default('count'); // count, ms, mb, %
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pulse_incidents');
        Schema::dropIfExists('pulse_threshold_rules');
    }
};
