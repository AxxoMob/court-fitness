<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<section class="cf-section">
    <header class="cf-section__head">
        <h1 class="cf-h1">My plans</h1>
        <p class="cf-subtle">Weekly training plans you have created.</p>
    </header>

    <!-- ------- Filter strip ------- -->
    <form method="get" action="<?= base_url('/coach/plans') ?>" class="cf-filters">
        <div class="cf-filters__row">
            <div class="cf-filters__cell">
                <label for="filter_year" class="cf-cell__label">Year</label>
                <select name="year" id="filter_year" class="form-select form-select-sm">
                    <option value="0">All years</option>
                    <?php foreach ($years as $y): ?>
                        <option value="<?= (int) $y ?>" <?= (int) $filters['year'] === (int) $y ? 'selected' : '' ?>>
                            <?= (int) $y ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="cf-filters__cell">
                <label for="filter_week_from" class="cf-cell__label">Week of (from)</label>
                <input type="date" name="week_from" id="filter_week_from"
                       class="form-control form-control-sm"
                       value="<?= esc((string) $filters['week_from']) ?>">
            </div>
            <div class="cf-filters__cell">
                <label for="filter_week_to" class="cf-cell__label">Week of (to)</label>
                <input type="date" name="week_to" id="filter_week_to"
                       class="form-control form-control-sm"
                       value="<?= esc((string) $filters['week_to']) ?>">
            </div>
            <div class="cf-filters__cell">
                <label for="filter_player" class="cf-cell__label">Player</label>
                <select name="player_id" id="filter_player" class="form-select form-select-sm">
                    <option value="0">All players</option>
                    <?php foreach ($players as $p): ?>
                        <option value="<?= (int) $p['id'] ?>"
                            <?= (int) $filters['player_id'] === (int) $p['id'] ? 'selected' : '' ?>>
                            <?= esc($p['first_name'] . ' ' . $p['family_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="cf-filters__actions">
                <button type="submit" class="btn btn-primary btn-sm">Apply</button>
                <a href="<?= base_url('/coach/plans') ?>" class="btn btn-outline-secondary btn-sm">Reset</a>
            </div>
        </div>
    </form>

    <?php if (empty($plans)): ?>
        <div class="cf-empty">
            <div class="cf-empty__icon" aria-hidden="true">📋</div>
            <h2 class="cf-h2">No plans match these filters</h2>
            <p>Try widening the date range or clearing the player filter — or create a new plan.</p>
            <a class="cf-btn cf-btn--primary" href="<?= base_url('/coach/plans/new') ?>">+ New plan</a>
        </div>
    <?php else: ?>
        <div class="cf-plan-grid">
            <?php foreach ($plans as $plan): ?>
                <article class="cf-card cf-plan-card">
                    <header class="cf-plan-card__head">
                        <div class="cf-plan-card__week">
                            <span class="cf-plan-card__monday">Week of</span>
                            <span class="cf-plan-card__date">
                                <?= esc(date('D, j M Y', strtotime((string) $plan['week_of']))) ?>
                            </span>
                        </div>
                        <span class="cf-plan-card__target-chip" title="<?= esc($plan['training_target']) ?>">
                            <?= esc($plan['training_target']) ?>
                        </span>
                    </header>

                    <?php if (! empty($plan['format_list'])): ?>
                        <div class="cf-plan-card__chips">
                            <?php foreach (array_slice($plan['format_list'], 0, 3) as $fmt): ?>
                                <?php $key = strtolower(preg_replace('/[^a-z]/i', '', (string) $fmt)); ?>
                                <span class="cf-format-chip cf-format-chip--<?= esc($key) ?>"><?= esc($fmt) ?></span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <dl class="cf-plan-card__meta">
                        <div>
                            <dt>Player</dt>
                            <dd><?= esc($plan['player_name']) ?></dd>
                        </div>
                        <div>
                            <dt>Exercises</dt>
                            <dd><?= (int) $plan['entry_count'] ?></dd>
                        </div>
                        <div>
                            <dt>Units</dt>
                            <dd><?= esc(strtoupper((string) $plan['weight_unit'])) ?></dd>
                        </div>
                    </dl>

                    <footer class="cf-plan-card__foot">
                        <a class="cf-btn cf-btn--primary cf-btn--block"
                           href="<?= base_url('/coach/plans/' . $plan['obfuscated_id']) ?>">
                            Open plan &rarr;
                        </a>
                    </footer>
                </article>
            <?php endforeach; ?>
        </div>

        <a class="cf-btn cf-btn--primary cf-btn--block cf-mt-2" href="<?= base_url('/coach/plans/new') ?>">
            + New plan
        </a>
    <?php endif; ?>
</section>
<?= $this->endSection() ?>
