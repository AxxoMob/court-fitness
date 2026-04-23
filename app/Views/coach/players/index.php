<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<section class="cf-section">
    <header class="cf-section__head">
        <h1 class="cf-h1">My players</h1>
        <p class="cf-subtle">Players currently assigned to you through HitCourt.</p>
    </header>

    <?php if (empty($players)): ?>
        <div class="cf-empty">
            <div class="cf-empty__icon" aria-hidden="true">👥</div>
            <h2 class="cf-h2">No players yet</h2>
            <p>Players appear here as soon as they register on HitCourt and get assigned to you.</p>
        </div>
    <?php else: ?>
        <ul class="list-group mb-3">
            <?php foreach ($players as $p): ?>
                <li class="list-group-item d-flex justify-content-between align-items-start">
                    <div>
                        <div class="fw-semibold"><?= esc($p['first_name'] . ' ' . $p['family_name']) ?></div>
                        <small class="text-muted"><?= esc($p['email']) ?></small>
                    </div>
                    <small class="text-muted">
                        since <?= esc(date('j M Y', strtotime((string) $p['assigned_date']))) ?>
                    </small>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <p class="cf-subtle small">
        Players can only be assigned once they've registered on HitCourt. Adding new HitCourt users is not part of court-fitness.
    </p>
</section>
<?= $this->endSection() ?>
