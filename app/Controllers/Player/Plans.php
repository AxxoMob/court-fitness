<?php

declare(strict_types=1);

namespace App\Controllers\Player;

use App\Controllers\BaseController;
use App\Support\IdObfuscator;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;
use Throwable;

/**
 * Player → Plan Detail (editable-actuals grid).
 *
 * The player mounts the SAME inline-grid partial as the coach (mode=player-edit).
 * Targets are display-only on this view; the server-side update() silently drops
 * any target_<key> in the POST so a malicious client can't push them through.
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
                    fs.name AS subcategory_name,
                    CONCAT(au.first_name, ' ', au.family_name) AS audit_user_name,
                    au.role AS audit_role
             FROM plan_entries pe
             JOIN exercise_types       et ON et.id = pe.exercise_type_id
             JOIN fitness_categories   fc ON fc.id = pe.fitness_category_id
             JOIN fitness_subcategories fs ON fs.id = pe.fitness_subcategory_id
             LEFT JOIN users           au ON au.id = pe.actual_by_user_id
             WHERE pe.training_plan_id = ? AND pe.deleted_at IS NULL
             ORDER BY pe.training_date, pe.session_period, pe.sort_order",
            [$planId]
        )->getResultArray();

        return view('player/plans/show', $this->buildShowContext() + [
            'plan'           => $plan,
            'entries'        => $entries,
            'obfuscated_id'  => $obfuscatedId,
            'mainClass'      => 'cf-main--wide',
        ]);
    }

    public function update(string $obfuscatedId): RedirectResponse
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

        // Re-verify ownership; never trust the client.
        $plan = $db->query(
            'SELECT id FROM training_plans
              WHERE id = ? AND player_user_id = ? AND deleted_at IS NULL',
            [$planId, $playerId]
        )->getRowArray();

        if ($plan === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        $entriesRaw = (string) ($this->request->getPost('entries_json') ?? '[]');
        $entries    = json_decode($entriesRaw, true);

        if (! is_array($entries)) {
            return redirect()->back()->with('errors', ['Could not read submitted entries.']);
        }

        $errors  = [];
        $updated = 0;

        try {
            $db->transStart();

            foreach ($entries as $e) {
                $entryId = (int) ($e['id'] ?? 0);
                if ($entryId <= 0) {
                    continue;
                }

                // Verify entry belongs to this plan.
                $row = $db->query(
                    'SELECT id, actual_json FROM plan_entries
                      WHERE id = ? AND training_plan_id = ? AND deleted_at IS NULL',
                    [$entryId, $planId]
                )->getRowArray();
                if ($row === null) {
                    continue;
                }

                // Player CANNOT update targets. Silently drop any target_* fields.
                if (! array_key_exists('actual', $e)) {
                    continue; // nothing for the player to do on this entry
                }

                $aBag = is_array($e['actual']) ? $e['actual'] : [];
                $aBag = $this->stripNullsAndEmpties($aBag);

                $update = ['updated_at' => date('Y-m-d H:i:s')];
                if ($aBag === []) {
                    if ($row['actual_json'] !== null) {
                        // Empty submitted actual but DB has one — preserve (no-clobber).
                        continue;
                    }
                    $update['actual_json']       = null;
                    $update['actual_by_user_id'] = null;
                    $update['actual_at']         = null;
                } else {
                    $update['actual_json']       = json_encode($aBag, JSON_UNESCAPED_UNICODE);
                    $update['actual_by_user_id'] = $playerId;
                    $update['actual_at']         = date('Y-m-d H:i:s');
                }

                $db->table('plan_entries')->where('id', $entryId)->update($update);
                $updated++;
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \RuntimeException('DB transaction rolled back during player update.');
            }
        } catch (Throwable $ex) {
            log_message('error', 'Player update failed: ' . $ex->getMessage());
            $errors[] = 'Could not save your actuals. Please try again.';
        }

        if ($errors !== []) {
            return redirect()->back()->with('errors', $errors);
        }

        return redirect()->to('/player/plans/' . $obfuscatedId)
            ->with('notice', 'Saved ' . $updated . ' ' . ($updated === 1 ? 'entry' : 'entries') . '.');
    }

    /**
     * @param array<string, mixed> $bag
     * @return array<string, mixed>
     */
    private function stripNullsAndEmpties(array $bag): array
    {
        $out = [];
        foreach ($bag as $k => $v) {
            if ($v === null || $v === '' || (is_string($v) && trim($v) === '')) continue;
            $out[$k] = $v;
        }
        return $out;
    }

    /**
     * Player needs the same taxonomy data as the coach grid; reuse the same context shape.
     *
     * @return array<string, mixed>
     */
    private function buildShowContext(): array
    {
        $db = db_connect();

        $types = $db->query(
            'SELECT id, name FROM exercise_types
              WHERE is_active = 1
              ORDER BY sort_order, name'
        )->getResultArray();

        $categories = $db->query(
            'SELECT id, exercise_type_id, name FROM fitness_categories
              WHERE is_active = 1
              ORDER BY sort_order, name'
        )->getResultArray();

        $subcategories = $db->query(
            'SELECT id, fitness_category_id, name FROM fitness_subcategories
              WHERE is_active = 1
              ORDER BY sort_order, name'
        )->getResultArray();

        // Player view doesn't list players or training_targets — pass empty for the partial.
        return [
            'players'       => [],
            'targets'       => [],
            'types'         => $types,
            'categories'    => $categories,
            'subcategories' => $subcategories,
            'next_monday'   => null,
            'errors'        => session()->getFlashdata('errors') ?? [],
        ];
    }
}
