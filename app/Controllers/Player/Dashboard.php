<?php

declare(strict_types=1);

namespace App\Controllers\Player;

use App\Controllers\BaseController;
use App\Support\IdObfuscator;
use CodeIgniter\HTTP\RedirectResponse;

/**
 * Player landing — THE demo-visible screen for Session 3.
 *
 * Shows all training plans assigned to the logged-in player, ordered upcoming
 * first, with exercise counts per plan. Mobile-first layout; orange brand
 * header. Tapping a plan card leads (Session 4+) to the plan detail + log
 * actuals flow.
 */
final class Dashboard extends BaseController
{
    public function index(): string|RedirectResponse
    {
        if (session()->get('role') !== 'player') {
            return redirect()->to('/');
        }

        $db     = db_connect();
        $userId = (int) session()->get('user_id');

        $plans = $db->query(
            "SELECT
                tp.id,
                tp.week_of,
                tp.training_target,
                tp.weight_unit,
                tp.notes,
                CONCAT(u.first_name, ' ', u.family_name) AS coach_name,
                (SELECT COUNT(*) FROM plan_entries pe
                  WHERE pe.training_plan_id = tp.id AND pe.deleted_at IS NULL) AS entry_count,
                (SELECT COUNT(*) FROM plan_entries pe
                  WHERE pe.training_plan_id = tp.id
                    AND pe.deleted_at IS NULL
                    AND pe.actual_json IS NOT NULL) AS logged_count
             FROM training_plans tp
             JOIN users u ON u.id = tp.coach_user_id
             WHERE tp.player_user_id = ? AND tp.deleted_at IS NULL
             ORDER BY tp.week_of DESC",
            [$userId]
        )->getResultArray();

        foreach ($plans as &$p) {
            $p['obfuscated_id'] = IdObfuscator::encode((int) $p['id']);
        }
        unset($p);

        return view('player/dashboard', ['plans' => $plans]);
    }
}
