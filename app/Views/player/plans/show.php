<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?php
$mode       = 'player-edit';
$action_url = base_url('/player/plans/' . $obfuscated_id);
$back_url   = base_url('/player');
$back_label = 'Back To My Plans';
// Reuse the coach plans partial — same shared HTML, mode flag drives field disabled-ness.
include APPPATH . 'Views/coach/plans/_grid.php';
?>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/js/plan-builder.js') ?>"></script>
<?= $this->endSection() ?>
