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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
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

    <main class="cf-main <?= esc($mainClass ?? '') ?>">
        <?= $this->renderSection('content') ?>
    </main>

    <footer class="cf-footer">
        <small>court-fitness · part of HitCourt · v0.1 (Sprint 01)</small>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <?= $this->renderSection('scripts') ?>
</body>
</html>
