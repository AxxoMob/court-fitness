<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration: three-level exercise taxonomy (inherited shape from ltat_fitness).
 *
 *   exercise_types          (3 rows)  — Cardio / Weights / Agility (top level)
 *   fitness_categories     (12 rows)  — e.g. Aerobic Cardio, Squat, Push (mid)
 *   fitness_subcategories (204 rows)  — e.g. Recovery run, Bench Press (leaf)
 *
 * Differences from ltat_fitness's original tables:
 *   - exercise_types renamed from `exercise_type` (plural, CI4-idiomatic).
 *   - exercise_types drops the inverted 0=Active / 1=Inactive `status` column
 *     (HL-5) in favour of the clean is_active semantics.
 *   - fitness_categories drops the redundant `code` column (was always equal
 *     to `name` in the source data).
 *   - fitness_subcategories drops the denormalised `exercise_type` column
 *     (HL-5: it was unreliable; use JOIN via fitness_category_id).
 *   - All tables use deleted_at convention (CI4) instead of ltat_fitness's
 *     mixed is_deleted / deleted_at usage.
 *   - description column added to fitness_subcategories (reserved for the
 *     future fitness-directory feature where players can read about an exercise).
 */
final class CreateExerciseCatalogTables extends Migration
{
    public function up(): void
    {
        // exercise_types
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'name'       => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => false],
            'sort_order' => ['type' => 'INT', 'constraint' => 3, 'unsigned' => true, 'null' => false, 'default' => 0],
            'is_active'  => ['type' => 'TINYINT', 'constraint' => 1, 'null' => false, 'default' => 1],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('name');
        $this->forge->createTable('exercise_types');

        // fitness_categories
        $this->forge->addField([
            'id'                => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'exercise_type_id'  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => false],
            'name'              => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => false],
            'slug'              => ['type' => 'VARCHAR', 'constraint' => 191, 'null' => false],
            'sort_order'        => ['type' => 'INT', 'constraint' => 3, 'unsigned' => true, 'null' => false, 'default' => 0],
            'is_active'         => ['type' => 'TINYINT', 'constraint' => 1, 'null' => false, 'default' => 1],
            'created_at'        => ['type' => 'DATETIME', 'null' => true],
            'updated_at'        => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('exercise_type_id');
        $this->forge->addUniqueKey('slug');
        $this->forge->addForeignKey('exercise_type_id', 'exercise_types', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('fitness_categories');

        // fitness_subcategories
        $this->forge->addField([
            'id'                   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'fitness_category_id'  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => false],
            'name'                 => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => false],
            'slug'                 => ['type' => 'VARCHAR', 'constraint' => 191, 'null' => false],
            'description'          => ['type' => 'TEXT', 'null' => true, 'comment' => 'Future: fitness-directory description'],
            'sort_order'           => ['type' => 'INT', 'constraint' => 4, 'unsigned' => true, 'null' => false, 'default' => 0],
            'is_active'            => ['type' => 'TINYINT', 'constraint' => 1, 'null' => false, 'default' => 1],
            'created_at'           => ['type' => 'DATETIME', 'null' => true],
            'updated_at'           => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        // Composite unique: slug only needs to be unique WITHIN a category.
        // (The ltat_fitness source has at least one slug reused across categories —
        // e.g. "pro-agility-5-10-5" — so a global unique on slug would reject valid
        // seed data. Composite also serves as the lookup index for category filters.)
        $this->forge->addUniqueKey(['fitness_category_id', 'slug']);
        $this->forge->addForeignKey('fitness_category_id', 'fitness_categories', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('fitness_subcategories');
    }

    public function down(): void
    {
        $this->forge->dropTable('fitness_subcategories', true);
        $this->forge->dropTable('fitness_categories', true);
        $this->forge->dropTable('exercise_types', true);
    }
}
