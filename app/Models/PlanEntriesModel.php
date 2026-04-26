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

    /**
     * Decide what (if anything) to write back when a saver POSTs an
     * `actual` bag for an entry that already exists in the DB.
     *
     * The no-clobber rule (Session 6 prompt §"Known Risks #2",
     * plan_builder_ux.md §3.3): if the saver's submitted actual bag is
     * empty AND the row already has actual_json IS NOT NULL, the existing
     * actuals must be preserved — do NOT overwrite with NULL.
     *
     * Pure function — no DB access — so it is unit-testable in isolation.
     *
     * @param string|null         $existingActualJson  Current value of plan_entries.actual_json
     *                                                  for this row, or NULL.
     * @param array<string,mixed> $submittedBag        The saver's submitted actual fields,
     *                                                  already stripped of nulls and empties.
     * @param int                 $savedByUserId       The user who is saving (player or coach).
     * @param string              $nowDatetime         Current datetime in 'Y-m-d H:i:s' (passed
     *                                                  in for testability).
     *
     * @return array<string, mixed>|null
     *   Returns the column → value diff to apply via UPDATE, or null when nothing
     *   should change (existing actual is preserved). Possible keys:
     *     - actual_json        : string|null
     *     - actual_by_user_id  : int|null
     *     - actual_at          : string|null
     */
    public static function decideActualUpdate(
        ?string $existingActualJson,
        array $submittedBag,
        int $savedByUserId,
        string $nowDatetime,
    ): ?array {
        // Empty submission, but DB has actuals — PRESERVE (no-clobber).
        if ($submittedBag === [] && $existingActualJson !== null) {
            return null;
        }

        // Empty submission AND no prior actual — write explicit NULLs (idempotent no-op,
        // but we set the columns just in case the saver wants to "clear" any partial state).
        if ($submittedBag === []) {
            return [
                'actual_json'       => null,
                'actual_by_user_id' => null,
                'actual_at'         => null,
            ];
        }

        // Non-empty submission — write the new bag and stamp the saver.
        return [
            'actual_json'       => json_encode($submittedBag, JSON_UNESCAPED_UNICODE),
            'actual_by_user_id' => $savedByUserId,
            'actual_at'         => $nowDatetime,
        ];
    }
}
