<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?php
$mode       = 'coach-edit';
$action_url = base_url('/coach/plans/' . $obfuscated_id);
$back_url   = base_url('/coach/plans');
$back_label = 'Back To My Plans';
include __DIR__ . '/_grid.php';
?>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/js/plan-builder.js') ?>"></script>
<?= $this->endSection() ?>
