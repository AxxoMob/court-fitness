<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#F26522">
    <title><?= esc($title ?? 'court-fitness') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/court-fitness.css') ?>">
</head>
<body>
    <header class="cf-header">
        <div class="cf-header__inner">
            <a class="cf-header__brand" href="<?= base_url('/') ?>">
                <span class="cf-header__mark">CF</span>
                <span class="cf-header__title">court-fitness</span>
            </a>
            <?php if (session()->get('is_authenticated')): ?>
                <div class="cf-header__user">
                    <span class="cf-header__name"><?= esc(session()->get('first_name')) ?></span>
                    <span class="cf-header__role-chip"><?= esc(ucfirst((string) session()->get('role'))) ?></span>
                </div>
            <?php endif; ?>
        </div>
    </header>

    <main class="cf-main">
        <?= $this->renderSection('content') ?>
    </main>

    <footer class="cf-footer">
        <small>court-fitness · part of HitCourt · v0.1 (Sprint 01)</small>
    </footer>
</body>
</html>
