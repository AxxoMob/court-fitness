<?php

declare(strict_types=1);

namespace App\Database\Seeds;

use CodeIgniter\CLI\CLI;
use CodeIgniter\Database\Seeder;

/**
 * Seeds the static catalogue tables for court-fitness:
 *   exercise_types (3), fitness_categories (12), fitness_subcategories (204),
 *   training_targets (7).
 *
 * The 204 subcategory rows are parsed directly from the ltat_fitness SQL dump
 * on disk — authoritative source for the tennis-aware catalogue. See HL-9 for
 * why we copy from the SQL dump rather than inventing our own list.
 *
 * Idempotent: disables FK checks, truncates, re-inserts.
 *
 * Run: php spark db:seed CourtFitnessCatalogSeeder
 */
final class CourtFitnessCatalogSeeder extends Seeder
{
    private const LTAT_SQL_PATH = 'C:/xampp/htdocs/ltat-fitness-module/Database/ltat_fitness.sql';

    public function run(): void
    {
        $this->db->disableForeignKeyChecks();

        $this->seedExerciseTypes();
        $this->seedFitnessCategories();
        $this->seedFitnessSubcategories();
        $this->seedTrainingTargets();

        $this->db->enableForeignKeyChecks();
    }

    private function seedExerciseTypes(): void
    {
        $this->db->table('exercise_types')->truncate();
        $now = date('Y-m-d H:i:s');
        $this->db->table('exercise_types')->insertBatch([
            ['id' => 1, 'name' => 'Cardio',  'sort_order' => 1, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'name' => 'Weights', 'sort_order' => 2, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'name' => 'Agility', 'sort_order' => 3, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
        ]);
        CLI::write('  exercise_types: 3 rows seeded', 'green');
    }

    private function seedFitnessCategories(): void
    {
        $this->db->table('fitness_categories')->truncate();
        $now = date('Y-m-d H:i:s');
        $rows = [
            ['id' =>  1, 'exercise_type_id' => 1, 'name' => 'Aerobic Cardio',    'slug' => 'aerobic-cardio',    'sort_order' => 1],
            ['id' =>  2, 'exercise_type_id' => 1, 'name' => 'Anaerobic Alactic', 'slug' => 'anaerobic-alactic', 'sort_order' => 2],
            ['id' =>  3, 'exercise_type_id' => 2, 'name' => 'Push',              'slug' => 'push',              'sort_order' => 1],
            ['id' =>  4, 'exercise_type_id' => 2, 'name' => 'Pull',              'slug' => 'pull',              'sort_order' => 2],
            ['id' =>  5, 'exercise_type_id' => 2, 'name' => 'Hinge',             'slug' => 'hinge',             'sort_order' => 3],
            ['id' =>  6, 'exercise_type_id' => 2, 'name' => 'Squat',             'slug' => 'squat',             'sort_order' => 4],
            ['id' =>  7, 'exercise_type_id' => 2, 'name' => 'Lunge',             'slug' => 'lunge',             'sort_order' => 5],
            ['id' =>  8, 'exercise_type_id' => 2, 'name' => 'Carry',             'slug' => 'carry',             'sort_order' => 6],
            ['id' =>  9, 'exercise_type_id' => 2, 'name' => 'Accessory',         'slug' => 'accessory',         'sort_order' => 7],
            ['id' => 10, 'exercise_type_id' => 2, 'name' => 'Core',              'slug' => 'core',              'sort_order' => 8],
            ['id' => 11, 'exercise_type_id' => 3, 'name' => 'Speed',             'slug' => 'speed',             'sort_order' => 1],
            ['id' => 12, 'exercise_type_id' => 3, 'name' => 'Agility',           'slug' => 'agility',           'sort_order' => 2],
        ];
        $rows = array_map(static function (array $r) use ($now): array {
            $r['is_active']  = 1;
            $r['created_at'] = $now;
            $r['updated_at'] = $now;
            return $r;
        }, $rows);
        $this->db->table('fitness_categories')->insertBatch($rows);
        CLI::write('  fitness_categories: 12 rows seeded', 'green');
    }

    private function seedFitnessSubcategories(): void
    {
        if (! is_file(self::LTAT_SQL_PATH)) {
            CLI::write('  fitness_subcategories: SKIPPED — ltat_fitness SQL dump not found at ' . self::LTAT_SQL_PATH, 'yellow');
            return;
        }

        $sql = file_get_contents(self::LTAT_SQL_PATH);
        if ($sql === false) {
            CLI::write('  fitness_subcategories: SKIPPED — could not read SQL dump', 'red');
            return;
        }

        // Isolate the INSERT block
        if (! preg_match('/INSERT INTO `fitness_subcategories`[^;]+;/s', $sql, $blockMatch)) {
            CLI::write('  fitness_subcategories: SKIPPED — INSERT block not found in dump', 'yellow');
            return;
        }

        // Parse each tuple: (id, category_id, 'name', 'slug', exercise_type, is_active, NULL|'...', NULL|'...')
        if (! preg_match_all(
            "/\((\d+),\s*(\d+),\s*'((?:[^'\\\\]|\\\\.)*)',\s*'((?:[^'\\\\]|\\\\.)*)',\s*\d+,\s*\d+,\s*(?:NULL|'[^']*'),\s*(?:NULL|'[^']*')\)/",
            $blockMatch[0],
            $tuples,
            PREG_SET_ORDER
        )) {
            CLI::write('  fitness_subcategories: SKIPPED — no tuples matched regex', 'yellow');
            return;
        }

        $now  = date('Y-m-d H:i:s');
        $rows = array_map(static function (array $t) use ($now): array {
            return [
                'id'                  => (int) $t[1],
                'fitness_category_id' => (int) $t[2],
                'name'                => stripslashes($t[3]),
                'slug'                => stripslashes($t[4]),
                'description'         => null,
                'sort_order'          => 0,
                'is_active'           => 1,
                'created_at'          => $now,
                'updated_at'          => $now,
            ];
        }, $tuples);

        $this->db->table('fitness_subcategories')->truncate();
        // insertBatch in chunks of 100 to avoid max packet issues
        foreach (array_chunk($rows, 100) as $chunk) {
            $this->db->table('fitness_subcategories')->insertBatch($chunk);
        }
        CLI::write('  fitness_subcategories: ' . count($rows) . ' rows seeded from ltat_fitness SQL dump', 'green');
    }

    private function seedTrainingTargets(): void
    {
        $this->db->table('training_targets')->truncate();
        $now  = date('Y-m-d H:i:s');
        $rows = [
            'Endurance', 'Strength', 'Power', 'Speed', 'Agility', 'Recovery', 'Mixed',
        ];
        $data = [];
        foreach ($rows as $i => $name) {
            $data[] = [
                'id'         => $i + 1,
                'name'       => $name,
                'sort_order' => $i + 1,
                'is_active'  => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        $this->db->table('training_targets')->insertBatch($data);
        CLI::write('  training_targets: 7 rows seeded', 'green');
    }
}
