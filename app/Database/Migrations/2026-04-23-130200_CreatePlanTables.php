<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration: training targets + training plans + plan entries.
 *
 *   training_targets — 7 seeded suggestions that populate the dropdown in the
 *     Plan Builder. Coach can pick one OR type custom text ("Upcoming ITF
 *     Futures Swing"). Custom text is stored directly in
 *     training_plans.training_target (VARCHAR) — no FK, to keep the
 *     suggestions catalogue from bloating with one-off values. Per owner call
 *     on 2026-04-23.
 *
 *   training_plans — one row per (coach, player, week_of) triple. week_of is
 *     always a Monday date (model-layer enforcement). weight_unit is 'kg' or
 *     'lb' per Weight Format picker in the UI.
 *
 *   plan_entries — one row per exercise prescribed on one date in one session
 *     period (morning/afternoon/evening). target_json & actual_json are the
 *     variable-shape bags for per-exercise data (reuses ltat-fitness Task 21
 *     pattern). actual_by_user_id records WHO filled in the actuals (coach or
 *     player) for audit.
 */
final class CreatePlanTables extends Migration
{
    public function up(): void
    {
        // training_targets — suggestions catalogue
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
        $this->forge->createTable('training_targets');

        // training_plans
        $this->forge->addField([
            'id'              => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'coach_user_id'   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => false],
            'player_user_id'  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => false],
            'week_of'         => ['type' => 'DATE', 'null' => false, 'comment' => 'Must be a Monday — enforced at model layer'],
            'training_target' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => false, 'comment' => 'Picked suggestion or coach custom text (max 100 chars)'],
            'weight_unit'     => ['type' => 'VARCHAR', 'constraint' => 3, 'null' => false, 'default' => 'kg', 'comment' => 'kg | lb'],
            'notes'           => ['type' => 'TEXT', 'null' => true],
            'created_at'      => ['type' => 'DATETIME', 'null' => true],
            'updated_at'      => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'      => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey(['player_user_id', 'week_of']);
        $this->forge->addKey(['coach_user_id', 'week_of']);
        $this->forge->addForeignKey('coach_user_id', 'users', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->addForeignKey('player_user_id', 'users', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('training_plans');

        // plan_entries
        $this->forge->addField([
            'id'                      => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'training_plan_id'        => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => false],
            'training_date'           => ['type' => 'DATE', 'null' => false],
            'session_period'          => ['type' => 'VARCHAR', 'constraint' => 10, 'null' => false, 'comment' => 'morning | afternoon | evening'],
            'exercise_type_id'        => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => false],
            'fitness_category_id'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => false],
            'fitness_subcategory_id'  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => false],
            'sort_order'              => ['type' => 'INT', 'constraint' => 4, 'unsigned' => true, 'null' => false, 'default' => 0],
            'target_json'             => ['type' => 'JSON', 'null' => true, 'comment' => 'Coach-prescribed targets (reps, weight, duration, …)'],
            'actual_json'             => ['type' => 'JSON', 'null' => true, 'comment' => 'Recorded actuals (reps, weight, …)'],
            'actual_by_user_id'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true, 'comment' => 'Who filled in actuals — coach or player'],
            'actual_at'               => ['type' => 'DATETIME', 'null' => true],
            'created_at'              => ['type' => 'DATETIME', 'null' => true],
            'updated_at'              => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'              => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey(['training_plan_id', 'training_date', 'session_period', 'sort_order']);
        $this->forge->addForeignKey('training_plan_id', 'training_plans', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('exercise_type_id', 'exercise_types', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->addForeignKey('fitness_category_id', 'fitness_categories', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->addForeignKey('fitness_subcategory_id', 'fitness_subcategories', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->addForeignKey('actual_by_user_id', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('plan_entries');
    }

    public function down(): void
    {
        $this->forge->dropTable('plan_entries', true);
        $this->forge->dropTable('training_plans', true);
        $this->forge->dropTable('training_targets', true);
    }
}
