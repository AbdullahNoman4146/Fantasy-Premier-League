<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MatchFixture extends Model
{
   
    // protected table = 'match_fixtures'

    protected $fillable = [
        'team1',
        'team2',
        'score1',
        'score2',
        'match_time',
        'status',      // current | upcoming | finished
        'kickoff_at',  // datetime (nullable)
    ];

    protected $casts = [
        'score1' => 'integer',
        'score2' => 'integer',
        'kickoff_at' => 'datetime',
    ];
}
