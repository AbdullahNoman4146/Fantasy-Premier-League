<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private function tableExists(string $table): bool
    {
        $row = DB::selectOne("SELECT COUNT(*) AS c FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ?", [$table]);
        return (int) ($row->c ?? 0) > 0;
    }

    private function columnExists(string $table, string $column): bool
    {
        $row = DB::selectOne("SELECT COUNT(*) AS c FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ?", [$table, $column]);
        return (int) ($row->c ?? 0) > 0;
    }

    public function up(): void
    {
        if (!$this->tableExists('player_market_values')) {
            DB::statement("
                CREATE TABLE player_market_values (
                    player_market_value_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    team_id BIGINT UNSIGNED NOT NULL,
                    jersey_number INT NOT NULL,
                    season VARCHAR(20) NOT NULL,
                    market_value DECIMAL(15,2) NOT NULL,
                    currency VARCHAR(10) NOT NULL DEFAULT 'GBP',
                    notes VARCHAR(255) NULL,
                    created_at TIMESTAMP NULL DEFAULT NULL,
                    updated_at TIMESTAMP NULL DEFAULT NULL,
                    UNIQUE KEY uq_player_market_value_season (team_id, jersey_number, season),
                    CONSTRAINT fk_player_market_values_player
                        FOREIGN KEY (team_id, jersey_number)
                        REFERENCES players(team_id, jersey_number)
                        ON UPDATE CASCADE ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
        }

        if (!$this->tableExists('transfer_posts')) {
            DB::statement("
                CREATE TABLE transfer_posts (
                    transfer_post_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    title VARCHAR(255) NOT NULL,
                    summary TEXT NULL,
                    content LONGTEXT NOT NULL,
                    status VARCHAR(20) NOT NULL DEFAULT 'published',
                    posted_at DATETIME NULL,
                    created_at TIMESTAMP NULL DEFAULT NULL,
                    updated_at TIMESTAMP NULL DEFAULT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
        }

        if ($this->tableExists('market_values') && $this->columnExists('market_values', 'year') && !$this->columnExists('market_values', 'season')) {
            DB::statement("ALTER TABLE market_values ADD COLUMN season VARCHAR(20) NULL AFTER year");
            DB::statement("UPDATE market_values SET season = CAST(year AS CHAR(20)) WHERE season IS NULL");
        }
    }

    public function down(): void
    {
        if ($this->tableExists('transfer_posts')) {
            DB::statement('DROP TABLE IF EXISTS transfer_posts');
        }

        if ($this->tableExists('player_market_values')) {
            DB::statement('DROP TABLE IF EXISTS player_market_values');
        }
    }
};
