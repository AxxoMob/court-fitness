<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<section class="cf-section cf-plan-builder">
    <header class="cf-section__head">
        <h1 class="cf-h1">New training plan</h1>
        <p class="cf-subtle">Pick a player and a week, then add exercises per day.</p>
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

    <?php if (empty($players)): ?>
        <div class="cf-empty">
            <div class="cf-empty__icon" aria-hidden="true">👥</div>
            <h2 class="cf-h2">No players assigned</h2>
            <p>A player has to register with HitCourt and be assigned to you before you can build a plan for them.</p>
            <a class="cf-btn cf-btn--ghost" href="<?= base_url('/coach') ?>">Back to dashboard</a>
        </div>
    <?php else: ?>

    <form method="post" action="<?= base_url('/coach/plans') ?>" id="cf-plan-form" novalidate>
        <?= csrf_field() ?>

        <!-- ------- Plan fundamentals ------- -->
        <div class="card cf-card mb-3">
            <div class="card-body">
                <div class="mb-3">
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

                <div class="mb-3">
                    <label for="week_of" class="form-label">Week starting (Monday)</label>
                    <input type="date" class="form-control" id="week_of" name="week_of"
                           value="<?= esc(old('week_of') ?? $next_monday) ?>" required>
                    <div class="form-text" id="week_of_hint">The week runs Monday through Sunday.</div>
                </div>

                <div class="mb-3">
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

                <div class="mb-3">
                    <label class="form-label d-block">Weight unit</label>
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

                <div class="mb-1">
                    <label for="notes" class="form-label">Notes <span class="cf-subtle">(optional)</span></label>
                    <textarea class="form-control" id="notes" name="notes" rows="2"
                              maxlength="5000"><?= esc(old('notes') ?? '') ?></textarea>
                </div>
            </div>
        </div>

        <!-- ------- Day-by-day accordion (rendered by JS when week_of loads) ------- -->
        <h2 class="cf-h2">Week schedule</h2>
        <p class="cf-subtle">Tap a day to expand, then add exercises to a session.</p>
        <div class="accordion mb-4" id="cf-days-accordion"></div>

        <!-- Serialised entries — the JS mirror of the schedule, sent to the server on submit -->
        <input type="hidden" name="entries_json" id="entries_json" value="[]">

        <!-- ------- Sticky save footer ------- -->
        <div class="cf-save-bar">
            <div class="cf-save-bar__inner">
                <span class="cf-subtle" id="cf-entries-count">0 exercises</span>
                <button type="submit" class="btn btn-primary btn-lg">Save plan</button>
            </div>
        </div>
    </form>

    <!-- ------- Add-exercise modal ------- -->
    <div class="modal fade" id="cf-exercise-modal" tabindex="-1" aria-labelledby="cf-exercise-modal-title" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-fullscreen-sm-down">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cf-exercise-modal-title">Add exercise</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="cf-subtle mb-3" id="cf-modal-context"></p>

                    <div class="mb-3">
                        <label for="mx_type" class="form-label">Format</label>
                        <select class="form-select" id="mx_type">
                            <option value="">— pick a format —</option>
                            <?php foreach ($types as $t): ?>
                                <option value="<?= (int) $t['id'] ?>"><?= esc($t['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="mx_category" class="form-label">Category</label>
                        <select class="form-select" id="mx_category" disabled>
                            <option value="">— pick a category —</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="mx_subcategory" class="form-label">Exercise</label>
                        <select class="form-select" id="mx_subcategory" disabled>
                            <option value="">— pick an exercise —</option>
                        </select>
                    </div>

                    <!-- Type-specific target fields — toggled by JS -->
                    <div class="row g-2 cf-target-group d-none" data-target-group="cardio">
                        <div class="col-6">
                            <label class="form-label">Max HR %</label>
                            <input type="number" min="40" max="100" class="form-control" id="mx_max_hr_pct" placeholder="75">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Duration (min)</label>
                            <input type="number" min="1" max="300" class="form-control" id="mx_duration_min" placeholder="30">
                        </div>
                    </div>
                    <div class="row g-2 cf-target-group d-none" data-target-group="weights">
                        <div class="col-6 col-md-3">
                            <label class="form-label">Sets</label>
                            <input type="number" min="1" max="20" class="form-control" id="mx_sets" placeholder="3">
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label">Reps</label>
                            <input type="number" min="1" max="100" class="form-control" id="mx_reps" placeholder="8">
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label">Weight</label>
                            <input type="number" step="0.5" min="0" max="500" class="form-control" id="mx_weight" placeholder="40">
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label">Rest (sec)</label>
                            <input type="number" min="0" max="600" class="form-control" id="mx_rest_sec_w" placeholder="90">
                        </div>
                    </div>
                    <div class="row g-2 cf-target-group d-none" data-target-group="agility">
                        <div class="col-6">
                            <label class="form-label">Reps</label>
                            <input type="number" min="1" max="100" class="form-control" id="mx_reps_a" placeholder="6">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Rest (sec)</label>
                            <input type="number" min="0" max="600" class="form-control" id="mx_rest_sec_a" placeholder="45">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="cf-modal-add">Add to session</button>
                </div>
            </div>
        </div>
    </div>

    <?php endif; ?>
</section>

<script id="cf-taxonomy-data" type="application/json"><?php
    // Keep inline JSON minimal — three small tables (3+12+204).
    echo json_encode([
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
    ], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
?></script>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/js/plan-builder.js') ?>"></script>
<?= $this->endSection() ?>
