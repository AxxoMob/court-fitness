<?php

declare(strict_types=1);

namespace App\Controllers\Player;

use App\Controllers\BaseController;
use App\Support\IdObfuscator;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;

/**
 * Player → Plan Detail (read-only in Sprint 01 Session 5).
 *
 * Log-actuals UX lands in Session 6. This controller just renders each
 * exercise grouped by day + session with its prescribed target, so the plan
 * card on the player dashboard finally links somewhere instead of 404-ing.
 */
final class Plans extends BaseController
{
    public function show(string $obfuscatedId): string|RedirectResponse
    {
        if (session()->get('role') !== 'player') {
            return redirect()->to('/');
        }

        $planId = IdObfuscator::decode($obfuscatedId);
        if ($planId === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        $playerId = (int) session()->get('user_id');
        $db       = db_connect();

        $plan = $db->query(
            "SELECT tp.*, CONCAT(u.first_name, ' ', u.family_name) AS coach_name
             FROM training_plans tp
             JOIN users u ON u.id = tp.coach_user_id
             WHERE tp.id = ? AND tp.player_user_id = ? AND tp.deleted_at IS NULL",
            [$planId, $playerId]
        )->getRowArray();

        if ($plan === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        $entries = $db->query(
            "SELECT pe.*,
                    et.name AS type_name,
                    fc.name AS category_name,
                    fs.name AS subcategory_name
             FROM plan_entries pe
             JOIN exercise_types       et ON et.id = pe.exercise_type_id
             JOIN fitness_categories   fc ON fc.id = pe.fitness_category_id
             JOIN fitness_subcategories fs ON fs.id = pe.fitness_subcategory_id
             WHERE pe.training_plan_id = ? AND pe.deleted_at IS NULL
             ORDER BY pe.training_date, pe.session_period, pe.sort_order",
            [$planId]
        )->getResultArray();

        return view('player/plans/show', [
            'plan'    => $plan,
            'entries' => $entries,
        ]);
    }
}
