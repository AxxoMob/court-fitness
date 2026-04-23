<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<section class="cf-section">
    <div class="cf-card cf-empty">
        <div class="cf-empty__icon" aria-hidden="true">⚙️</div>
        <h1 class="cf-h1">Fitness administration features are coming soon.</h1>
        <p>Your HitCourt role (<?= esc(session()->get('role')) ?>) is recognised. The court-fitness admin surface is planned for a later release.</p>
        <p class="cf-subtle">Signed in as <?= esc(session()->get('first_name')) ?> <?= esc(session()->get('family_name')) ?>.</p>
    </div>
</section>
<?= $this->endSection() ?>
