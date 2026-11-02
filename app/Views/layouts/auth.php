<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= esc($title ?? 'TRACKLY — Pelacakan Rekam Medis') ?></title>
  <meta name="description" content="TRACKLY - Sistem Pelacakan dan Monitoring Peminjaman Berkas Rekam Medis Berbasis QR Code">
  <link rel="stylesheet" href="<?= base_url('assets/css/trackly.css') ?>">
</head>
<body>
  <div class="auth-wrapper">
    <div class="auth-card">
      <div class="auth-header">
        <div class="brand-badge">TRACKLY &bull; HEALTHCARE RECORD</div>
        <h1 class="auth-title"><?= esc($heading ?? 'TRACKLY') ?></h1>
        <p class="auth-subtitle"><?= esc($subheading ?? 'Sistem Pelacakan Berkas Rekam Medis') ?></p>
      </div>

      <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-error">
          <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
          <div><?= session()->getFlashdata('error') ?></div>
        </div>
      <?php endif; ?>

      <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success">
          <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM16.707 7.707a1 1 0 00-1.414-1.414L9 12.586 6.707 10.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l7-7z" clip-rule="evenodd"></path></svg>
          <div><?= session()->getFlashdata('success') ?></div>
        </div>
      <?php endif; ?>

      <?php if (session()->getFlashdata('info')): ?>
        <div class="alert alert-info">
          <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
          <div><?= session()->getFlashdata('info') ?></div>
        </div>
      <?php endif; ?>

      <?= $this->renderSection('content') ?>
    </div>
  </div>

  <script src="<?= base_url('assets/js/trackly.js') ?>"></script>
</body>
</html>
