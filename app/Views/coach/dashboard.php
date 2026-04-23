<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<section class="cf-section">
    <header class="cf-section__head">
        <h1 class="cf-h1">Welcome, Coach <?= esc(session()->get('first_name')) ?></h1>
        <p class="cf-subtle">Your dashboard — more screens unlock in Session 4.</p>
    </header>

    <div class="cf-stat-grid">
        <div class="cf-card cf-stat">
            <span class="cf-stat__value"><?= (int) ($playerCount ?? 0) ?></span>
            <span class="cf-stat__label">Assigned players</span>
        </div>
        <div class="cf-card cf-stat">
            <span class="cf-stat__value"><?= (int) ($planCount ?? 0) ?></span>
            <span class="cf-stat__label">Training plans</span>
        </div>
    </div>

    <div class="cf-card">
        <h2 class="cf-h2">Coming up</h2>
        <p class="cf-subtle">Session 4 delivers:</p>
        <ul class="cf-list">
            <li>My Players (add/search players assigned to you)</li>
            <li>My Plans (weekly plans list with filters)</li>
            <li>Plan Builder — the mobile-first screen for scheduling a player's week</li>
        </ul>
    </div>
</section>
<?= $this->endSection() ?>
