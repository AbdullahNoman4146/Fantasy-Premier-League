<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            CREATE TABLE match_goal_events (
                goal_event_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                match_id BIGINT UNSIGNED NOT NULL,
                team_id BIGINT UNSIGNED NOT NULL,
                person_id BIGINT UNSIGNED NOT NULL,
                jersey_number INT NOT NULL,
                minute_label VARCHAR(20) NULL,
                sort_order INT NOT NULL DEFAULT 1,
                created_at TIMESTAMP NULL DEFAULT NULL,
                updated_at TIMESTAMP NULL DEFAULT NULL,
                CONSTRAINT fk_match_goal_events_match FOREIGN KEY (match_id) REFERENCES matches(match_id)
                    ON UPDATE CASCADE ON DELETE CASCADE,
                CONSTRAINT fk_match_goal_events_team FOREIGN KEY (team_id) REFERENCES teams(team_id)
                    ON UPDATE CASCADE ON DELETE CASCADE,
                CONSTRAINT fk_match_goal_events_person FOREIGN KEY (person_id) REFERENCES persons(person_id)
                    ON UPDATE CASCADE ON DELETE CASCADE,
                INDEX idx_match_goal_events_match (match_id),
                INDEX idx_match_goal_events_person (person_id),
                INDEX idx_match_goal_events_team (team_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }

    public function down(): void
    {
        DB::statement("DROP TABLE IF EXISTS match_goal_events");
    }
};
