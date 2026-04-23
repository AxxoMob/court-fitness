<?php

declare(strict_types=1);

namespace App\Controllers\Coach;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\RedirectResponse;

/**
 * Coach → My Players (list only).
 *
 * No add-player form — per owner (2026-04-23): "The Coach can NOT add any
 * player who is not already registered with HitCourt. That's the basis for
 * everything." Identity always originates on HitCourt. Sprint 02+ may add a
 * HitCourt-API lookup to pre-assign players who've registered but not yet
 * SSO'd; until then, a player shows up in this list as soon as they land in
 * court-fitness via SSO and a coach-player assignment exists.
 */
final class Players extends BaseController
{
    public function index(): string|RedirectResponse
    {
        if (session()->get('role') !== 'coach') {
            return redirect()->to('/');
        }

        $coachId = (int) session()->get('user_id');
        $db      = db_connect();

        $players = $db->query(
            "SELECT u.id,
                    u.first_name,
                    u.family_name,
                    u.email,
                    cpa.assigned_date
             FROM coach_player_assignments cpa
             JOIN users u ON u.id = cpa.player_user_id
             WHERE cpa.coach_user_id = ?
               AND cpa.is_active = 1
               AND cpa.deleted_at IS NULL
               AND u.deleted_at IS NULL
             ORDER BY u.first_name, u.family_name",
            [$coachId]
        )->getResultArray();

        return view('coach/players/index', ['players' => $players]);
    }
}
