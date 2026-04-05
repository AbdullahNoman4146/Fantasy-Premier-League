<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            $table->string('live_phase', 30)
                ->nullable()
                ->after('match_time');

            $table->unsignedInteger('first_half_added_minutes')
                ->default(0)
                ->after('live_phase');

            $table->unsignedInteger('second_half_added_minutes')
                ->default(0)
                ->after('first_half_added_minutes');

            $table->dateTime('second_half_started_at')
                ->nullable()
                ->after('second_half_added_minutes');
        });
    }

    public function down(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            $table->dropColumn([
                'live_phase',
                'first_half_added_minutes',
                'second_half_added_minutes',
                'second_half_started_at',
            ]);
        });
    }
};