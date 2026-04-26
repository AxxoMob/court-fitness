<?php
/**
 * Inline grid partial — used by:
 *   - coach/plans/new.php          ($mode = 'new')
 *   - coach/plans/show.php         ($mode = 'coach-edit')
 *   - player/plans/show.php        ($mode = 'player-edit')
 *
 * Canonical UX in .ai/core/plan_builder_ux.md (LOCKED 2026-04-23). Wide inline
 * grid on desktop/tablet/iPad; CSS-driven collapse to per-row cards <768px.
 *
 * Required view variables:
 *   $mode         string      — 'new' | 'coach-edit' | 'player-edit'
 *   $action_url   string      — POST target
 *   $players      array       — assigned players (only used when $mode === 'new')
 *   $targets      array       — training_targets rows
 *   $types        array       — exercise_types rows (id, name)
 *   $categories   array       — fitness_categories rows (id, exercise_type_id, name)
 *   $subcategories array      — fitness_subcategories rows (id, fitness_category_id, name)
 *   $next_monday  string|null — default week_of for new mode
 *   $errors       array       — validation messages from prior POST
 *
 * Optional (show modes):
 *   $plan         array|null  — the existing plan row (week_of, training_target, weight_unit, notes,
 *                               player_name OR coach_name)
 *   $entries      array       — prefilled entries with id, dates, JSON blobs, and audit JOIN data
 *                               (audit_user_name, audit_role, actual_at)
 *
 * Server-side trust boundary: this partial trusts the controller to have done role
 * + ownership checks. The form posts to $action_url which itself re-checks before
 * persisting. Player mode hides target inputs from the wire; even if a malicious
 * player POSTs target_<key>, Player\Plans::update silently drops them.
 */
$isNew         = ($mode === 'new');
$isCoachEdit   = ($mode === 'coach-edit');
$isPlayerEdit  = ($mode === 'player-edit');
$canEditTargets = $isNew || $isCoachEdit;     // false on player-edit
$canEditActuals = $isCoachEdit || $isPlayerEdit; // not on new (no plan id yet)
$showActuals    = $canEditActuals;            // alias for clarity
?>
<section class="cf-section cf-plan-builder" data-cf-mode="<?= esc($mode) ?>">
    <header class="cf-section__head">
        <?php if (session()->getFlashdata('notice')): ?>
            <div class="alert alert-success"><?= esc(session()->getFlashdata('notice')) ?></div>
        <?php endif; ?>

        <?php if ($isNew): ?>
            <h1 class="cf-h1">New training plan</h1>
            <p class="cf-subtle">Pick a player and a week, then add exercises in the grid below.</p>
        <?php else: ?>
            <div class="cf-plan-summary">
                <h1 class="cf-plan-summary__title">
                    <?= $isCoachEdit
                        ? esc($plan['player_name'] ?? '—')
                        : 'Your week with Coach ' . esc($plan['coach_name'] ?? '—') ?>
                </h1>
                <span class="cf-plan-summary__pill"><?= esc($plan['training_target'] ?? '—') ?></span>
                <span class="cf-plan-summary__sub">
                    Week of <?= esc(date('D, j M Y', strtotime((string) ($plan['week_of'] ?? 'now')))) ?>
                    · <?= esc(strtoupper((string) ($plan['weight_unit'] ?? 'kg'))) ?>
                </span>
                <?php if (! empty($plan['notes'])): ?>
                    <span class="cf-plan-summary__sub">· <?= esc($plan['notes']) ?></span>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </header>

    <?php if (! empty($errors)): ?>
        <div class="alert alert-danger" role="alert">
            <strong>Fix these before saving:</strong>
            <ul class="mb-0">
                <?php foreach ($errors as $e): ?>
                    <li><?= esc($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if ($isNew && empty($players)): ?>
        <div class="cf-empty">
            <div class="cf-empty__icon" aria-hidden="true">👥</div>
            <h2 class="cf-h2">No players assigned</h2>
            <p>A player has to register with HitCourt and be assigned to you before you can build a plan for them.</p>
            <a class="cf-btn cf-btn--ghost" href="<?= base_url('/coach') ?>">Back to dashboard</a>
        </div>
    <?php else: ?>

    <form method="post" action="<?= esc($action_url) ?>" id="cf-plan-form" novalidate>
        <?= csrf_field() ?>

        <?php if ($isNew): ?>
            <!-- Fundamentals strip — only editable in new mode (per Sprint 1: write-once at creation). -->
            <div class="cf-fundamentals">
                <div class="cf-fundamentals__row">
                    <div>
                        <label for="player_user_id" class="form-label">Player</label>
                        <?php $oldPlayer = (int) (old('player_user_id') ?? 0); ?>
                        <select class="form-select" id="player_user_id" name="player_user_id" required>
                            <option value="" <?= $oldPlayer === 0 ? 'selected' : '' ?>>— pick a player —</option>
                            <?php foreach ($players as $p): ?>
                                <option value="<?= (int) $p['id'] ?>"
                                    <?= $oldPlayer === (int) $p['id'] ? 'selected' : '' ?>>
                                    <?= esc($p['first_name'] . ' ' . $p['family_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label for="week_of" class="form-label">Weekof (Monday)</label>
                        <input type="date" class="form-control" id="week_of" name="week_of"
                               value="<?= esc(old('week_of') ?? $next_monday) ?>" required>
                        <div class="form-text" id="week_of_hint">The week runs Monday through Sunday.</div>
                    </div>
                    <div>
                        <label for="training_target" class="form-label">Training target</label>
                        <?php $oldTarget = (string) (old('training_target') ?? ''); ?>
                        <select class="form-select" id="training_target" name="training_target">
                            <option value="">— pick a target —</option>
                            <?php foreach ($targets as $t): ?>
                                <option value="<?= esc($t['name']) ?>"
                                    <?= $oldTarget === $t['name'] ? 'selected' : '' ?>>
                                    <?= esc($t['name']) ?>
                                </option>
                            <?php endforeach; ?>
                            <option value="__custom__">+ Add custom target…</option>
                        </select>
                        <input type="text"
                               class="form-control mt-2 d-none"
                               id="training_target_custom"
                               name="training_target_custom"
                               maxlength="100"
                               placeholder="e.g. Upcoming ITF Futures Swing"
                               value="<?= esc(old('training_target_custom') ?? '') ?>">
                    </div>
                    <div>
                        <label class="form-label d-block">Weight format</label>
                        <?php $oldUnit = (string) (old('weight_unit') ?? 'kg'); ?>
                        <div class="btn-group" role="group" aria-label="Weight unit">
                            <input type="radio" class="btn-check" name="weight_unit" id="unit_kg" value="kg"
                                <?= $oldUnit === 'kg' ? 'checked' : '' ?>>
                            <label class="btn btn-outline-primary" for="unit_kg">kg</label>

                            <input type="radio" class="btn-check" name="weight_unit" id="unit_lb" value="lb"
                                <?= $oldUnit === 'lb' ? 'checked' : '' ?>>
                            <label class="btn btn-outline-primary" for="unit_lb">lb</label>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Blocks (one per (date, period, format)) — rendered by JS. -->
        <div id="cf-blocks" class="mb-3">
            <p class="cf-empty-blocks" id="cf-empty-blocks">
                <?= $isNew
                    ? 'No training dates yet — click "+ Add training date" below to start.'
                    : ($showActuals
                        ? 'No exercises on this plan. Add one below.'
                        : 'No exercises on this plan yet.') ?>
            </p>
        </div>

        <button type="button" class="cf-add-block" id="cf-add-block">
            + Add training date <?= $isNew ? '' : '/ session' ?>
        </button>

        <?php if ($isNew): ?>
            <!-- Notes — moved to the bottom of the page per owner directive 2026-04-26.
                 Optional free text saved on the parent training_plans row. -->
            <div class="cf-fundamentals mt-4">
                <label for="notes" class="form-label">Notes <span class="cf-subtle">(optional)</span></label>
                <textarea class="form-control" id="notes" name="notes" rows="3"
                          maxlength="5000" placeholder="Anything the coach wants the player to remember (warm-up, equipment, recovery focus, etc.)"><?= esc(old('notes') ?? '') ?></textarea>
            </div>
        <?php endif; ?>

        <!-- Serialised state (the JS mirror of the grid, sent on submit). -->
        <input type="hidden" name="entries_json" id="entries_json" value="[]">

        <!-- Sticky save bar -->
        <div class="cf-save-bar">
            <div class="cf-save-bar__inner">
                <span class="cf-subtle" id="cf-entries-count">0 exercises</span>
                <button type="submit" class="btn btn-primary btn-lg">
                    <?= $isNew ? 'Save plan' : 'Save changes' ?>
                </button>
            </div>
        </div>
    </form>

    <?php endif; /* end empty-players check */ ?>
</section>

<!-- Inline JSON: taxonomy + (in show modes) the existing entries with audit join. -->
<script id="cf-taxonomy-data" type="application/json"><?php
echo json_encode([
    'mode'            => $mode,
    'can_edit_target' => $canEditTargets,
    'can_edit_actual' => $canEditActuals,
    'types'         => array_map(static fn ($r) => ['id' => (int) $r['id'], 'name' => $r['name']], $types),
    'categories'    => array_map(static fn ($r) => [
        'id'               => (int) $r['id'],
        'exercise_type_id' => (int) $r['exercise_type_id'],
        'name'             => $r['name'],
    ], $categories),
    'subcategories' => array_map(static fn ($r) => [
        'id'                  => (int) $r['id'],
        'fitness_category_id' => (int) $r['fitness_category_id'],
        'name'                => $r['name'],
    ], $subcategories),
    'entries'       => array_map(static function ($e) {
        return [
            'id'                     => (int) ($e['id'] ?? 0),
            'training_date'          => (string) ($e['training_date'] ?? ''),
            'session_period'         => (string) ($e['session_period'] ?? ''),
            'exercise_type_id'       => (int)    ($e['exercise_type_id'] ?? 0),
            'fitness_category_id'    => (int)    ($e['fitness_category_id'] ?? 0),
            'fitness_subcategory_id' => (int)    ($e['fitness_subcategory_id'] ?? 0),
            'target'                 => $e['target_json'] ? json_decode($e['target_json'], true) : new \stdClass(),
            'actual'                 => $e['actual_json'] ? json_decode($e['actual_json'], true) : new \stdClass(),
            'audit_name'             => $e['audit_user_name'] ?? null,
            'audit_role'             => $e['audit_role']      ?? null,
            'actual_at'              => $e['actual_at']       ?? null,
        ];
    }, $entries ?? []),
], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
?></script>
