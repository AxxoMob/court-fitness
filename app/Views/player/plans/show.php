<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?php
$mode       = 'player-edit';
$action_url = base_url('/player/plans/' . $obfuscated_id);
// Reuse the coach plans partial — same shared HTML, mode flag drives field disabled-ness.
include APPPATH . 'Views/coach/plans/_grid.php';
?>

<a class="cf-btn cf-btn--ghost cf-btn--block mt-3" href="<?= base_url('/player') ?>">&larr; Back to my plans</a>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/js/plan-builder.js') ?>"></script>
<?= $this->endSection() ?>
