<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

/**
 * Plan entries — one row per exercise in one session on one date.
 *
 * target_json and actual_json are variable-shape bags whose expected keys
 * differ by exercise_type:
 *   - Cardio: { max_hr_pct: int, duration_min: int }
 *   - Weights: { sets: int, reps: int, weight: float, rest_sec: int }
 *   - Agility: { reps: int, rest_sec: int }
 *
 * Shape validation happens at the controller layer (Coach\Plans::store) since
 * it depends on the exercise_type row, which this model doesn't know about.
 * See CLAUDE.md §5.6 — the JSON-blob pattern is intentional (reuses
 * ltat-fitness Task 21 design) so the table stays narrow instead of becoming
 * 80% NULLs across type-specific columns.
 */
final class PlanEntriesModel extends Model
{
    protected $table            = 'plan_entries';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;

    protected $allowedFields = [
        'training_plan_id',
        'training_date',
        'session_period',
        'exercise_type_id',
        'fitness_category_id',
        'fitness_subcategory_id',
        'sort_order',
        'target_json',
        'actual_json',
        'actual_by_user_id',
        'actual_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules = [
        'training_plan_id'       => 'required|is_natural_no_zero',
        'training_date'          => 'required|valid_date[Y-m-d]',
        'session_period'         => 'required|in_list[morning,afternoon,evening]',
        'exercise_type_id'       => 'required|is_natural_no_zero',
        'fitness_category_id'    => 'required|is_natural_no_zero',
        'fitness_subcategory_id' => 'required|is_natural_no_zero',
        'sort_order'             => 'required|is_natural',
    ];
}
