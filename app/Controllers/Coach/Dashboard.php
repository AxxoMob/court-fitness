<?php

declare(strict_types=1);

namespace App\Controllers\Coach;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\RedirectResponse;

/**
 * Coach landing. Minimal Session 3 placeholder — Session 4 replaces with
 * a real "My Players" + "My Plans" surface.
 */
final class Dashboard extends BaseController
{
    public function index(): string|RedirectResponse
    {
        if (session()->get('role') !== 'coach') {
            return redirect()->to('/');
        }

        $db     = db_connect();
        $userId = (int) session()->get('user_id');

        $playerCount = (int) $db->query(
            'SELECT COUNT(*) AS c FROM coach_player_assignments WHERE coach_user_id = ? AND is_active = 1 AND deleted_at IS NULL',
            [$userId]
        )->getRow()->c;

        $planCount = (int) $db->query(
            'SELECT COUNT(*) AS c FROM training_plans WHERE coach_user_id = ? AND deleted_at IS NULL',
            [$userId]
        )->getRow()->c;

        return view('coach/dashboard', [
            'playerCount' => $playerCount,
            'planCount'   => $planCount,
        ]);
    }
}
