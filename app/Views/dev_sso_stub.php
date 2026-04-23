<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<section class="cf-section">
    <header class="cf-section__head">
        <h1 class="cf-h1">Dev Stub SSO</h1>
        <p class="cf-subtle">Dev-only. Mints a local JWT and hands off to <code>/sso</code>.</p>
    </header>

    <div class="cf-card">
        <h2 class="cf-h2">Sign in as…</h2>
        <div class="cf-btn-stack">
            <a class="cf-btn cf-btn--primary cf-btn--block" href="<?= base_url('/dev/sso-stub?as=player') ?>">Rohan Mehta (Player)</a>
            <a class="cf-btn cf-btn--secondary cf-btn--block" href="<?= base_url('/dev/sso-stub?as=player2') ?>">Priya Sharma (Player)</a>
            <a class="cf-btn cf-btn--secondary cf-btn--block" href="<?= base_url('/dev/sso-stub?as=coach') ?>">Rajat Kapoor (Coach)</a>
            <a class="cf-btn cf-btn--ghost cf-btn--block" href="<?= base_url('/dev/sso-stub?as=admin') ?>">Demo Admin (placeholder)</a>
        </div>
        <p class="cf-subtle cf-mt-2">
            These buttons only work when <code>CI_ENVIRONMENT = development</code>. In production, only HitCourt can issue SSO tokens.
        </p>
    </div>
</section>
<?= $this->endSection() ?>
