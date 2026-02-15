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
    Schema::table('match_fixtures', function (Blueprint $table) {
        $table->string('status')->default('upcoming'); // current | upcoming | finished
        $table->dateTime('kickoff_at')->nullable();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('match_fixtures', function (Blueprint $table) {
            //
        });
    }
};
