<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private function columnExists(string $table, string $column): bool
    {
        $row = DB::selectOne("
            SELECT COUNT(*) AS c
            FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = ?
              AND COLUMN_NAME = ?
        ", [$table, $column]);

        return (int)($row->c ?? 0) > 0;
    }

    public function up(): void
    {
        if (!$this->columnExists('teams', 'team_name')) {
            DB::statement("ALTER TABLE teams ADD COLUMN team_name VARCHAR(255) NOT NULL UNIQUE");
        }
        if (!$this->columnExists('teams', 'goals_scored')) {
            DB::statement("ALTER TABLE teams ADD COLUMN goals_scored INT NULL DEFAULT 0");
        }
        if (!$this->columnExists('teams', 'goals_conceded')) {
            DB::statement("ALTER TABLE teams ADD COLUMN goals_conceded INT NULL DEFAULT 0");
        }
        if (!$this->columnExists('teams', 'strength')) {
            DB::statement("ALTER TABLE teams ADD COLUMN strength INT NULL");
        }
        if (!$this->columnExists('teams', 'manager_id')) {
            DB::statement("ALTER TABLE teams ADD COLUMN manager_id BIGINT UNSIGNED NULL");
        }
    }

    public function down(): void
    {
        // Optional: keep empty, or drop columns if you want rollback support.
    }
};