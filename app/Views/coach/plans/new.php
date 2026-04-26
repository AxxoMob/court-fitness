<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?php
$mode       = 'new';
$action_url = base_url('/coach/plans');
$entries    = [];
$plan       = null;
include __DIR__ . '/_grid.php';
?>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/js/plan-builder.js') ?>"></script>
<?= $this->endSection() ?>
