<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<section class="cf-section">
    <header class="cf-section__head">
        <h1 class="cf-h1">Welcome, Coach <?= esc(session()->get('first_name')) ?></h1>
        <p class="cf-subtle">Build a week of training or review your squad.</p>
    </header>

    <div class="cf-stat-grid">
        <a class="cf-card cf-stat" href="<?= base_url('/coach/players') ?>">
            <span class="cf-stat__value"><?= (int) ($playerCount ?? 0) ?></span>
            <span class="cf-stat__label">Assigned players</span>
        </a>
        <a class="cf-card cf-stat" href="<?= base_url('/coach/plans') ?>">
            <span class="cf-stat__value"><?= (int) ($planCount ?? 0) ?></span>
            <span class="cf-stat__label">Training plans</span>
        </a>
    </div>

    <div class="cf-btn-stack">
        <a class="cf-btn cf-btn--primary cf-btn--block" href="<?= base_url('/coach/plans/new') ?>">
            + New training plan
        </a>
        <a class="cf-btn cf-btn--secondary cf-btn--block" href="<?= base_url('/coach/plans') ?>">
            My plans
        </a>
        <a class="cf-btn cf-btn--ghost cf-btn--block" href="<?= base_url('/coach/players') ?>">
            My players
        </a>
    </div>
</section>
<?= $this->endSection() ?>
