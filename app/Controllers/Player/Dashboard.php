<?php

declare(strict_types=1);

namespace App\Controllers\Player;

use App\Controllers\BaseController;
use App\Support\IdObfuscator;
use CodeIgniter\HTTP\RedirectResponse;

/**
 * Player landing — the player's plan list with filters.
 *
 * Mirrors Coach\Plans::index in shape: Year + Week Of From/To + Coach
 * dropdowns at the top, fixed-size cards with format chips below. The only
 * meaningful difference is that the per-plan filter dropdown is "Coach"
 * (the trainer assigned to the player) instead of "Player."
 */
final class Dashboard extends BaseController
{
    public function index(): string|RedirectResponse
    {
        if (session()->get('role') !== 'player') {
            return redirect()->to('/');
        }

        $playerId = (int) session()->get('user_id');
        $db       = db_connect();
        $req      = $this->request;

        // ---- Filter defaults (mirrors Coach\Plans::index) ----
        $today           = new \DateTimeImmutable();
        $thisMonday      = $today->modify('-' . ((int) $today->format('N') - 1) . ' days');
        $defaultYear     = (int) $today->format('Y');
        $defaultWeekTo   = $thisMonday->format('Y-m-d');
        $defaultWeekFrom = $thisMonday->modify('-4 weeks')->format('Y-m-d');

        $yearRaw     = $req->getGet('year');
        $weekFromRaw = $req->getGet('week_from');
        $weekToRaw   = $req->getGet('week_to');
        $coachRaw    = $req->getGet('coach_id');

        $year      = $yearRaw     === null ? $defaultYear     : (int)    $yearRaw;
        $weekFrom  = $weekFromRaw === null ? $defaultWeekFrom : (string) $weekFromRaw;
        $weekTo    = $weekToRaw   === null ? $defaultWeekTo   : (string) $weekToRaw;
        $coachId   = $coachRaw    === null ? 0                : (int)    $coachRaw;

        // ---- Filtered query ----
        $where  = ['tp.player_user_id = ?', 'tp.deleted_at IS NULL'];
        $params = [$playerId];
        if ($year > 0) {
            $where[]  = 'YEAR(tp.week_of) = ?';
            $params[] = $year;
        }
        if ($weekFrom !== '') {
            $where[]  = 'tp.week_of >= ?';
            $params[] = $weekFrom;
        }
        if ($weekTo !== '') {
            $where[]  = 'tp.week_of <= ?';
            $params[] = $weekTo;
        }
        if ($coachId > 0) {
            $where[]  = 'tp.coach_user_id = ?';
            $params[] = $coachId;
        }

        $sql = "SELECT
                    tp.id, tp.week_of, tp.training_target, tp.weight_unit, tp.notes,
                    CONCAT(u.first_name, ' ', u.family_name) AS coach_name,
                    (SELECT COUNT(*) FROM plan_entries pe
                      WHERE pe.training_plan_id = tp.id AND pe.deleted_at IS NULL) AS entry_count,
                    (SELECT COUNT(*) FROM plan_entries pe
                      WHERE pe.training_plan_id = tp.id
                        AND pe.deleted_at IS NULL
                        AND pe.actual_json IS NOT NULL) AS logged_count,
                    (SELECT GROUP_CONCAT(DISTINCT et.name ORDER BY et.sort_order SEPARATOR ',')
                     FROM plan_entries pe
                     JOIN exercise_types et ON et.id = pe.exercise_type_id
                     WHERE pe.training_plan_id = tp.id AND pe.deleted_at IS NULL) AS formats
                FROM training_plans tp
                JOIN users u ON u.id = tp.coach_user_id
                WHERE " . implode(' AND ', $where) . "
                ORDER BY tp.week_of DESC";

        $plans = $db->query($sql, $params)->getResultArray();
        foreach ($plans as &$p) {
            $p['obfuscated_id'] = IdObfuscator::encode((int) $p['id']);
            $p['format_list']   = $p['formats'] ? explode(',', (string) $p['formats']) : [];
        }
        unset($p);

        // Assigned coaches (for the Coach filter dropdown — mirrors the coach side)
        $coaches = $db->query(
            "SELECT u.id, u.first_name, u.family_name
             FROM coach_player_assignments cpa
             JOIN users u ON u.id = cpa.coach_user_id
             WHERE cpa.player_user_id = ?
               AND cpa.is_active = 1 AND cpa.deleted_at IS NULL AND u.deleted_at IS NULL
             ORDER BY u.first_name, u.family_name",
            [$playerId]
        )->getResultArray();

        $years = [$defaultYear - 2, $defaultYear - 1, $defaultYear, $defaultYear + 1];

        return view('player/dashboard', [
            'plans'     => $plans,
            'coaches'   => $coaches,
            'years'     => $years,
            'filters'   => [
                'year'      => $year,
                'week_from' => $weekFrom,
                'week_to'   => $weekTo,
                'coach_id'  => $coachId,
            ],
            'mainClass' => 'cf-main--wide',
        ]);
    }
}
