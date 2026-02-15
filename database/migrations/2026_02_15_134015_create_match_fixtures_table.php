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
    Schema::create('match_fixtures', function (Blueprint $table) {
        $table->id();
        $table->string('team1');
        $table->string('team2');
        $table->integer('score1')->default(0);
        $table->integer('score2')->default(0);
        $table->string('match_time')->nullable();
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('match_fixtures');
    }
};
