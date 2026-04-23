<?php

declare(strict_types=1);

namespace App\Database\Seeds;

use CodeIgniter\CLI\CLI;
use CodeIgniter\Database\Seeder;

/**
 * Seeds demo users + one training plan so the Player Dashboard has something
 * real to render at Sprint 01 Session 1 (Session 3).
 *
 *   users                    : 1 coach (Rajat) + 2 players (Rohan, Priya)
 *   coach_player_assignments : coach linked to both players
 *   training_plans           : 1 plan for Rohan, week_of = next Monday
 *   plan_entries             : 3 exercises across 2 days + 2 sessions
 *
 * These hitcourt_user_id values (1001 / 2001 / 2002) are reserved for dev
 * stub-SSO to identify demo users. Run: php spark db:seed CourtFitnessDemoSeeder
 */
final class CourtFitnessDemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->db->disableForeignKeyChecks();

        $this->db->table('plan_entries')->truncate();
        $this->db->table('training_plans')->truncate();
        $this->db->table('coach_player_assignments')->truncate();
        $this->db->table('users')->truncate();

        $this->db->enableForeignKeyChecks();

        $now = date('Y-m-d H:i:s');

        // Users: 1 coach + 2 players
        $this->db->table('users')->insertBatch([
            ['id' => 1, 'hitcourt_user_id' => 1001, 'email' => 'rajat.coach@hitcourt.example', 'first_name' => 'Rajat',  'family_name' => 'Kapoor', 'role' => 'coach',  'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'hitcourt_user_id' => 2001, 'email' => 'rohan.player@hitcourt.example','first_name' => 'Rohan',  'family_name' => 'Mehta',  'role' => 'player', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'hitcourt_user_id' => 2002, 'email' => 'priya.player@hitcourt.example','first_name' => 'Priya',  'family_name' => 'Sharma', 'role' => 'player', 'created_at' => $now, 'updated_at' => $now],
        ]);
        CLI::write('  users: 3 rows (1 coach + 2 players)', 'green');

        // Assignments
        $this->db->table('coach_player_assignments')->insertBatch([
            ['coach_user_id' => 1, 'player_user_id' => 2, 'assigned_date' => '2026-04-01', 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['coach_user_id' => 1, 'player_user_id' => 3, 'assigned_date' => '2026-04-10', 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
        ]);
        CLI::write('  coach_player_assignments: 2 rows', 'green');

        // Plan for Rohan — next Monday
        $todayTs     = time();
        $nextMonTs   = strtotime('next Monday', $todayTs);
        $monday      = date('Y-m-d', $nextMonTs);
        $wednesday   = date('Y-m-d', strtotime('+2 days', $nextMonTs));

        $this->db->table('training_plans')->insert([
            'id'              => 1,
            'coach_user_id'   => 1,
            'player_user_id'  => 2,
            'week_of'         => $monday,
            'training_target' => 'Endurance',
            'weight_unit'     => 'kg',
            'notes'           => 'Light week — recovery focus after ITF Futures.',
            'created_at'      => $now,
            'updated_at'      => $now,
        ]);
        CLI::write('  training_plans: 1 plan for Rohan, week_of=' . $monday, 'green');

        // 3 plan entries: Mon-morning Cardio x2, Wed-evening Weights x1
        // Subcategory IDs reference ltat_fitness seed data:
        //   1  = Recovery run            (cat 1 Aerobic Cardio)
        //   19 = Pro agility 5 10 5      (cat 2 Anaerobic Alactic)
        //   32 = Bench Press             (cat 3 Push)
        $this->db->table('plan_entries')->insertBatch([
            [
                'training_plan_id'       => 1,
                'training_date'          => $monday,
                'session_period'         => 'morning',
                'exercise_type_id'       => 1,   // Cardio
                'fitness_category_id'    => 1,   // Aerobic Cardio
                'fitness_subcategory_id' => 1,   // Recovery run
                'sort_order'             => 1,
                'target_json'            => json_encode(['duration_min' => 30, 'max_hr_pct' => 70]),
                'actual_json'            => null,
                'created_at'             => $now,
                'updated_at'             => $now,
            ],
            [
                'training_plan_id'       => 1,
                'training_date'          => $monday,
                'session_period'         => 'morning',
                'exercise_type_id'       => 1,   // Cardio
                'fitness_category_id'    => 2,   // Anaerobic Alactic
                'fitness_subcategory_id' => 19,  // Pro agility 5 10 5
                'sort_order'             => 2,
                'target_json'            => json_encode(['reps' => 6, 'rest_sec' => 90]),
                'actual_json'            => null,
                'created_at'             => $now,
                'updated_at'             => $now,
            ],
            [
                'training_plan_id'       => 1,
                'training_date'          => $wednesday,
                'session_period'         => 'evening',
                'exercise_type_id'       => 2,   // Weights
                'fitness_category_id'    => 3,   // Push
                'fitness_subcategory_id' => 32,  // Bench Press
                'sort_order'             => 1,
                'target_json'            => json_encode(['sets' => 4, 'reps' => 8, 'weight_kg' => 60, 'rest_sec' => 120]),
                'actual_json'            => null,
                'created_at'             => $now,
                'updated_at'             => $now,
            ],
        ]);
        CLI::write('  plan_entries: 3 rows (Mon morning x2, Wed evening x1)', 'green');
    }
}
