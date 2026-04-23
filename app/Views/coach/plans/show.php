<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<section class="cf-section">
    <header class="cf-section__head">
        <?php if (session()->getFlashdata('notice')): ?>
            <div class="alert alert-success"><?= esc(session()->getFlashdata('notice')) ?></div>
        <?php endif; ?>
        <h1 class="cf-h1"><?= esc($plan['player_name']) ?></h1>
        <p class="cf-subtle">
            Week of <?= esc(date('D, j M Y', strtotime((string) $plan['week_of']))) ?>
            · Target: <?= esc($plan['training_target']) ?>
            · <?= esc(strtoupper((string) $plan['weight_unit'])) ?>
        </p>
    </header>

    <?php if (empty($entries)): ?>
        <p class="cf-subtle">No exercises on this plan yet.</p>
    <?php else: ?>
        <?php
        $grouped = [];
        foreach ($entries as $e) {
            $grouped[$e['training_date']][$e['session_period']][] = $e;
        }
        ?>
        <?php foreach ($grouped as $date => $periods): ?>
            <div class="card cf-card mb-3">
                <div class="card-body">
                    <h2 class="cf-h2 mb-3">
                        <?= esc(date('D, j M Y', strtotime($date))) ?>
                    </h2>
                    <?php foreach ($periods as $period => $items): ?>
                        <h3 class="h6 text-uppercase text-muted mt-3"><?= esc(ucfirst($period)) ?></h3>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($items as $e): ?>
                                <li class="list-group-item px-0">
                                    <div class="fw-semibold"><?= esc($e['subcategory_name']) ?></div>
                                    <small class="text-muted">
                                        <?= esc($e['type_name']) ?> · <?= esc($e['category_name']) ?>
                                    </small>
                                    <?php
                                    $target = $e['target_json'] ? json_decode($e['target_json'], true) : [];
                                    if (is_array($target) && $target !== []):
                                    ?>
                                        <div class="small mt-1">
                                            <?php foreach ($target as $k => $v): ?>
                                                <?php if ($v !== null && $v !== ''): ?>
                                                    <span class="badge text-bg-light me-1"><?= esc($k) ?>: <?= esc((string) $v) ?></span>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <a class="cf-btn cf-btn--ghost cf-btn--block" href="<?= base_url('/coach/plans') ?>">&larr; Back to My Plans</a>
</section>
<?= $this->endSection() ?>
