<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<section class="cf-section">
    <header class="cf-section__head">
        <h1 class="cf-h1">My plans</h1>
        <p class="cf-subtle">Weekly training plans you have created.</p>
    </header>

    <?php if (empty($plans)): ?>
        <div class="cf-empty">
            <div class="cf-empty__icon" aria-hidden="true">📋</div>
            <h2 class="cf-h2">No plans yet</h2>
            <p>Create your first plan for a player.</p>
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
                        <span class="cf-plan-card__target-chip"><?= esc($plan['training_target']) ?></span>
                    </header>
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
