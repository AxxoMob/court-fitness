<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?php
$mode       = 'coach-edit';
$action_url = base_url('/coach/plans/' . $obfuscated_id);
include __DIR__ . '/_grid.php';
?>

<a class="cf-btn cf-btn--ghost cf-btn--block mt-3" href="<?= base_url('/coach/plans') ?>">&larr; Back to My Plans</a>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/js/plan-builder.js') ?>"></script>
<?= $this->endSection() ?>
