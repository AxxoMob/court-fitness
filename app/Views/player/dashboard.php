<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<section class="cf-section">
    <header class="cf-section__head">
        <h1 class="cf-h1">Hi, <?= esc(session()->get('first_name')) ?></h1>
        <p class="cf-subtle">Your training plans</p>
    </header>

    <?php if (empty($plans)): ?>
        <div class="cf-empty">
            <div class="cf-empty__icon" aria-hidden="true">🎾</div>
            <h2 class="cf-h2">No plans yet</h2>
            <p>Your coach hasn't assigned any training plans. You'll see them here as soon as they do.</p>
        </div>
    <?php else: ?>
        <div class="cf-plan-grid">
            <?php foreach ($plans as $plan): ?>
                <?php
                $weekOf      = strtotime((string) $plan['week_of']);
                $weekLabel   = date('D, j M Y', $weekOf); // "Mon, 27 Apr 2026"
                $entries     = (int) $plan['entry_count'];
                $logged      = (int) $plan['logged_count'];
                $percentDone = $entries > 0 ? (int) round($logged / $entries * 100) : 0;
                ?>
                <article class="cf-card cf-plan-card">
                    <header class="cf-plan-card__head">
                        <div class="cf-plan-card__week">
                            <span class="cf-plan-card__monday">Week of</span>
                            <span class="cf-plan-card__date"><?= esc($weekLabel) ?></span>
                        </div>
                        <span class="cf-plan-card__target-chip"><?= esc($plan['training_target']) ?></span>
                    </header>
                    <dl class="cf-plan-card__meta">
                        <div>
                            <dt>Coach</dt>
                            <dd><?= esc($plan['coach_name']) ?></dd>
                        </div>
                        <div>
                            <dt>Exercises</dt>
                            <dd><?= $entries ?></dd>
                        </div>
                        <div>
                            <dt>Units</dt>
                            <dd><?= esc(strtoupper((string) $plan['weight_unit'])) ?></dd>
                        </div>
                    </dl>
                    <div class="cf-plan-card__progress" aria-label="<?= $percentDone ?>% logged">
                        <div class="cf-progress">
                            <div class="cf-progress__bar" style="width: <?= $percentDone ?>%"></div>
                        </div>
                        <span class="cf-subtle"><?= $logged ?> / <?= $entries ?> logged</span>
                    </div>
                    <?php if (! empty($plan['notes'])): ?>
                        <p class="cf-plan-card__notes"><?= esc($plan['notes']) ?></p>
                    <?php endif; ?>
                    <footer class="cf-plan-card__foot">
                        <a class="cf-btn cf-btn--primary cf-btn--block" href="<?= base_url('/player/plans/' . $plan['obfuscated_id']) ?>">
                            View plan &rarr;
                        </a>
                    </footer>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
<?= $this->endSection() ?>
