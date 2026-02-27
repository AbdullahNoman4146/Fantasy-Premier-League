<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1) PERSON
        DB::statement("
            CREATE TABLE persons (
                person_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                first_name VARCHAR(100) NULL,
                last_name VARCHAR(100) NULL,
                attribute VARCHAR(255) NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        // 2) TEAM (manager_id FK will be added later to avoid circular FK issue)
        DB::statement("
            CREATE TABLE teams (
                team_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                team_name VARCHAR(255) NOT NULL UNIQUE,
                strength INT NULL,
                manager_id BIGINT UNSIGNED NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        // 3) MATCH
        DB::statement("
            CREATE TABLE matches (
                match_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                team_a_id BIGINT UNSIGNED NOT NULL,
                team_b_id BIGINT UNSIGNED NOT NULL,
                stadium VARCHAR(255) NULL,
                status VARCHAR(20) NOT NULL DEFAULT 'upcoming',
                kickoff_at DATETIME NULL,
                match_time VARCHAR(50) NULL,
                created_at TIMESTAMP NULL DEFAULT NULL,
                updated_at TIMESTAMP NULL DEFAULT NULL,
                CONSTRAINT fk_matches_team_a FOREIGN KEY (team_a_id) REFERENCES teams(team_id)
                    ON UPDATE CASCADE ON DELETE RESTRICT,
                CONSTRAINT fk_matches_team_b FOREIGN KEY (team_b_id) REFERENCES teams(team_id)
                    ON UPDATE CASCADE ON DELETE RESTRICT
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        // 4) RESULT (1–1 with match)
        DB::statement("
            CREATE TABLE results (
                match_id BIGINT UNSIGNED PRIMARY KEY,
                score_a INT NOT NULL DEFAULT 0,
                score_b INT NOT NULL DEFAULT 0,
                winner_team_id BIGINT UNSIGNED NULL,
                CONSTRAINT fk_results_match FOREIGN KEY (match_id) REFERENCES matches(match_id)
                    ON UPDATE CASCADE ON DELETE CASCADE,
                CONSTRAINT fk_results_winner FOREIGN KEY (winner_team_id) REFERENCES teams(team_id)
                    ON UPDATE CASCADE ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        // 5) STANDINGS (1 row per team)
        DB::statement("
            CREATE TABLE standings (
                team_id BIGINT UNSIGNED PRIMARY KEY,
                played INT NOT NULL DEFAULT 0,
                points INT NOT NULL DEFAULT 0,
                wins INT NOT NULL DEFAULT 0,
                losses INT NOT NULL DEFAULT 0,
                draws INT NOT NULL DEFAULT 0,
                goal_diff INT NOT NULL DEFAULT 0,
                CONSTRAINT fk_standings_team FOREIGN KEY (team_id) REFERENCES teams(team_id)
                    ON UPDATE CASCADE ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        // 6) MARKET VALUE (many rows per team by year)
        DB::statement("
            CREATE TABLE market_values (
                team_id BIGINT UNSIGNED NOT NULL,
                year INT NOT NULL,
                value DECIMAL(15,2) NULL,
                PRIMARY KEY (team_id, year),
                CONSTRAINT fk_market_values_team FOREIGN KEY (team_id) REFERENCES teams(team_id)
                    ON UPDATE CASCADE ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        // 7) SPONSOR (many sponsors per team)
        DB::statement("
            CREATE TABLE sponsors (
                sponsor_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                team_id BIGINT UNSIGNED NOT NULL,
                sponsor_name VARCHAR(255) NOT NULL,
                CONSTRAINT fk_sponsors_team FOREIGN KEY (team_id) REFERENCES teams(team_id)
                    ON UPDATE CASCADE ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        // 8) PLAYER (composite key: team_id + jersey_number)
        DB::statement("
            CREATE TABLE players (
                team_id BIGINT UNSIGNED NOT NULL,
                jersey_number INT NOT NULL,
                person_id BIGINT UNSIGNED NOT NULL,
                position VARCHAR(50) NULL,
                PRIMARY KEY (team_id, jersey_number),
                CONSTRAINT fk_players_team FOREIGN KEY (team_id) REFERENCES teams(team_id)
                    ON UPDATE CASCADE ON DELETE CASCADE,
                CONSTRAINT fk_players_person FOREIGN KEY (person_id) REFERENCES persons(person_id)
                    ON UPDATE CASCADE ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        // 9) MANAGER (subtype of person)
        DB::statement("
            CREATE TABLE managers (
                person_id BIGINT UNSIGNED PRIMARY KEY,
                team_id BIGINT UNSIGNED NULL UNIQUE,
                experience_years INT NULL,
                CONSTRAINT fk_managers_person FOREIGN KEY (person_id) REFERENCES persons(person_id)
                    ON UPDATE CASCADE ON DELETE CASCADE,
                CONSTRAINT fk_managers_team FOREIGN KEY (team_id) REFERENCES teams(team_id)
                    ON UPDATE CASCADE ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        // Add team.manager_id FK AFTER managers exist (avoids circular FK problem)
        DB::statement("
            ALTER TABLE teams
            ADD CONSTRAINT fk_teams_manager
            FOREIGN KEY (manager_id) REFERENCES managers(person_id)
                ON UPDATE CASCADE ON DELETE SET NULL
        ");
    }

    public function down(): void
    {
        // Drop in reverse dependency order
        DB::statement("ALTER TABLE teams DROP FOREIGN KEY fk_teams_manager");
        DB::statement("DROP TABLE IF EXISTS managers");
        DB::statement("DROP TABLE IF EXISTS players");
        DB::statement("DROP TABLE IF EXISTS sponsors");
        DB::statement("DROP TABLE IF EXISTS market_values");
        DB::statement("DROP TABLE IF EXISTS standings");
        DB::statement("DROP TABLE IF EXISTS results");
        DB::statement("DROP TABLE IF EXISTS matches");
        DB::statement("DROP TABLE IF EXISTS teams");
        DB::statement("DROP TABLE IF EXISTS persons");
    }
};