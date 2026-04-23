<?php

declare(strict_types=1);

namespace App\Controllers\Coach;

use App\Controllers\BaseController;
use App\Models\PlanEntriesModel;
use App\Models\TrainingPlansModel;
use App\Support\IdObfuscator;
use CodeIgniter\HTTP\RedirectResponse;
use Throwable;

final class Plans extends BaseController
{
    private const SESSION_PERIODS       = ['morning', 'afternoon', 'evening'];
    private const WEIGHT_UNITS          = ['kg', 'lb'];
    private const TRAINING_TARGET_CHARS = 100;

    public function index(): string|RedirectResponse
    {
        if (session()->get('role') !== 'coach') {
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
                CONCAT(u.first_name, ' ', u.family_name) AS player_name,
                (SELECT COUNT(*) FROM plan_entries pe
                  WHERE pe.training_plan_id = tp.id AND pe.deleted_at IS NULL) AS entry_count
             FROM training_plans tp
             JOIN users u ON u.id = tp.player_user_id
             WHERE tp.coach_user_id = ? AND tp.deleted_at IS NULL
             ORDER BY tp.week_of DESC",
            [$userId]
        )->getResultArray();

        foreach ($plans as &$p) {
            $p['obfuscated_id'] = IdObfuscator::encode((int) $p['id']);
        }
        unset($p);

        return view('coach/plans/index', ['plans' => $plans]);
    }

    public function new(): string|RedirectResponse
    {
        if (session()->get('role') !== 'coach') {
            return redirect()->to('/');
        }

        return view('coach/plans/new', $this->buildFormContext());
    }

    public function store(): RedirectResponse
    {
        if (session()->get('role') !== 'coach') {
            return redirect()->to('/');
        }

        $coachId = (int) session()->get('user_id');
        $post    = $this->request->getPost();

        $playerId      = (int) ($post['player_user_id'] ?? 0);
        $weekOf        = trim((string) ($post['week_of'] ?? ''));
        $targetChoice  = trim((string) ($post['training_target'] ?? ''));
        $targetCustom  = trim((string) ($post['training_target_custom'] ?? ''));
        $weightUnit    = (string) ($post['weight_unit'] ?? 'kg');
        $notes         = trim((string) ($post['notes'] ?? ''));
        $entriesRaw    = (string) ($post['entries_json'] ?? '[]');

        // Combobox: custom wins if present, otherwise the picked suggestion.
        $trainingTarget = $targetCustom !== '' ? $targetCustom : $targetChoice;

        $errors = $this->validatePlanInput(
            $coachId,
            $playerId,
            $weekOf,
            $trainingTarget,
            $weightUnit,
        );

        $entries = json_decode($entriesRaw, true);
        if (! is_array($entries)) {
            $errors[] = 'Plan entries are malformed — please re-add exercises.';
            $entries  = [];
        }

        if ($entries === []) {
            $errors[] = 'Add at least one exercise before saving.';
        }

        if ($errors !== []) {
            return redirect()->back()->withInput()->with('errors', $errors);
        }

        [$entryRows, $entryErrors] = $this->normaliseEntries($entries, $weekOf);
        if ($entryErrors !== []) {
            return redirect()->back()->withInput()->with('errors', $entryErrors);
        }

        $planId = null;
        $db     = db_connect();

        try {
            $db->transStart();

            $plans  = new TrainingPlansModel();
            $planId = $plans->insert([
                'coach_user_id'   => $coachId,
                'player_user_id'  => $playerId,
                'week_of'         => $weekOf,
                'training_target' => $trainingTarget,
                'weight_unit'     => $weightUnit,
                'notes'           => $notes !== '' ? $notes : null,
            ], true);

            $entriesModel = new PlanEntriesModel();
            foreach ($entryRows as $i => $row) {
                $row['training_plan_id'] = (int) $planId;
                $row['sort_order']       = $i;
                $entriesModel->insert($row);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \RuntimeException('Database transaction rolled back.');
            }
        } catch (Throwable $e) {
            log_message('error', 'Plan save failed: ' . $e->getMessage());

            return redirect()->back()->withInput()->with('errors', [
                'Could not save the plan. Please try again. If this keeps happening, contact support.',
            ]);
        }

        return redirect()->to('/coach/plans/' . IdObfuscator::encode((int) $planId))
            ->with('notice', 'Plan saved.');
    }

    public function show(string $obfuscatedId): string|RedirectResponse
    {
        if (session()->get('role') !== 'coach') {
            return redirect()->to('/');
        }

        $planId = IdObfuscator::decode($obfuscatedId);
        if ($planId === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $coachId = (int) session()->get('user_id');
        $db      = db_connect();

        $plan = $db->query(
            "SELECT tp.*, CONCAT(u.first_name, ' ', u.family_name) AS player_name
             FROM training_plans tp
             JOIN users u ON u.id = tp.player_user_id
             WHERE tp.id = ? AND tp.coach_user_id = ? AND tp.deleted_at IS NULL",
            [$planId, $coachId]
        )->getRowArray();

        if ($plan === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
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

        return view('coach/plans/show', [
            'plan'           => $plan,
            'entries'        => $entries,
            'obfuscated_id'  => $obfuscatedId,
        ]);
    }

    // ------------------------------------------------------------------

    /**
     * @return array{0: array<int, array<string, mixed>>, 1: array<int, string>}
     */
    private function normaliseEntries(array $entries, string $weekOf): array
    {
        $rows   = [];
        $errors = [];

        try {
            $monday = new \DateTimeImmutable($weekOf);
        } catch (Throwable) {
            return [[], ['Invalid week_of date.']];
        }
        $windowStart = $monday->format('Y-m-d');
        $windowEnd   = $monday->modify('+6 days')->format('Y-m-d');

        foreach ($entries as $i => $e) {
            $date   = (string) ($e['training_date'] ?? '');
            $period = (string) ($e['session_period'] ?? '');
            $typeId = (int)    ($e['exercise_type_id'] ?? 0);
            $catId  = (int)    ($e['fitness_category_id'] ?? 0);
            $subId  = (int)    ($e['fitness_subcategory_id'] ?? 0);
            $target = is_array($e['target'] ?? null) ? $e['target'] : [];

            if ($date < $windowStart || $date > $windowEnd) {
                $errors[] = 'Exercise ' . ($i + 1) . ': date must fall within the selected week.';
                continue;
            }
            if (! in_array($period, self::SESSION_PERIODS, true)) {
                $errors[] = 'Exercise ' . ($i + 1) . ': invalid session period.';
                continue;
            }
            if ($typeId <= 0 || $catId <= 0 || $subId <= 0) {
                $errors[] = 'Exercise ' . ($i + 1) . ': missing type/category/subcategory.';
                continue;
            }

            $rows[] = [
                'training_date'          => $date,
                'session_period'         => $period,
                'exercise_type_id'       => $typeId,
                'fitness_category_id'    => $catId,
                'fitness_subcategory_id' => $subId,
                'target_json'            => json_encode($target, JSON_UNESCAPED_UNICODE),
            ];
        }

        return [$rows, $errors];
    }

    /**
     * @return array<int, string>
     */
    private function validatePlanInput(
        int $coachId,
        int $playerId,
        string $weekOf,
        string $trainingTarget,
        string $weightUnit,
    ): array {
        $errors = [];

        if ($playerId <= 0) {
            $errors[] = 'Please choose a player.';
        } elseif (! $this->coachOwnsPlayer($coachId, $playerId)) {
            $errors[] = 'That player is not assigned to you.';
        }

        if ($weekOf === '') {
            $errors[] = 'Please pick a Monday for the week.';
        } else {
            try {
                $d = new \DateTimeImmutable($weekOf);
                if ((int) $d->format('N') !== 1) {
                    $errors[] = 'Week start must be a Monday.';
                }
            } catch (Throwable) {
                $errors[] = 'Invalid week date.';
            }
        }

        if ($trainingTarget === '') {
            $errors[] = 'Please pick or type a training target.';
        } elseif (mb_strlen($trainingTarget) > self::TRAINING_TARGET_CHARS) {
            $errors[] = 'Training target is too long (max ' . self::TRAINING_TARGET_CHARS . ' characters).';
        }

        if (! in_array($weightUnit, self::WEIGHT_UNITS, true)) {
            $errors[] = 'Weight unit must be kg or lb.';
        }

        return $errors;
    }

    private function coachOwnsPlayer(int $coachId, int $playerId): bool
    {
        $row = db_connect()->query(
            'SELECT 1 FROM coach_player_assignments
              WHERE coach_user_id = ? AND player_user_id = ?
                AND is_active = 1 AND deleted_at IS NULL
              LIMIT 1',
            [$coachId, $playerId]
        )->getRowArray();

        return $row !== null;
    }

    /**
     * @return array<string, mixed>
     */
    private function buildFormContext(): array
    {
        $db      = db_connect();
        $coachId = (int) session()->get('user_id');

        $players = $db->query(
            "SELECT u.id, u.first_name, u.family_name
             FROM coach_player_assignments cpa
             JOIN users u ON u.id = cpa.player_user_id
             WHERE cpa.coach_user_id = ?
               AND cpa.is_active = 1
               AND cpa.deleted_at IS NULL
               AND u.deleted_at IS NULL
             ORDER BY u.first_name, u.family_name",
            [$coachId]
        )->getResultArray();

        $targets = $db->query(
            'SELECT name FROM training_targets
              WHERE is_active = 1
              ORDER BY sort_order, name'
        )->getResultArray();

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

        $nextMonday = (new \DateTimeImmutable('next monday'))->format('Y-m-d');

        return [
            'players'       => $players,
            'targets'       => $targets,
            'types'         => $types,
            'categories'    => $categories,
            'subcategories' => $subcategories,
            'next_monday'   => $nextMonday,
            'errors'        => session()->getFlashdata('errors') ?? [],
        ];
    }
}
