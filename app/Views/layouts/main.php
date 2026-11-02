<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= esc($title ?? 'TRACKLY — Sistem Pelacakan Berkas RM') ?></title>
  <meta name="description" content="TRACKLY - Sistem Pelacakan dan Monitoring Peminjaman Berkas Rekam Medis Berbasis QR Code">
  <link rel="stylesheet" href="<?= base_url('assets/css/trackly.css') ?>">
</head>
<body>
  <div class="app-wrapper">
    <!-- Sidebar Navigation -->
    <aside class="sidebar" id="sidebar">
      <div class="sidebar-header">
        <div class="sidebar-logo-icon">T</div>
        <div>
          <div class="sidebar-brand-name">TRACKLY</div>
          <div class="sidebar-brand-tagline">Healthcare Record Tracking</div>
        </div>
      </div>

      <nav class="sidebar-nav">
        <div class="nav-section-title">Menu Utama</div>
        <a href="<?= base_url('dashboard') ?>" class="nav-item <?= service('uri')->getPath() === 'dashboard' ? 'active' : '' ?>">
          <svg width="19" height="19" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
          Dashboard
        </a>

        <?php $role = session()->get('role'); ?>

        <?php if (in_array($role, ['admin', 'petugas_rm', 'kepala'])): ?>
          <a href="<?= base_url('berkas') ?>" class="nav-item <?= str_starts_with(service('uri')->getPath(), 'berkas') ? 'active' : '' ?>">
            <svg width="19" height="19" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            Data Berkas RM
          </a>
        <?php endif; ?>

        <?php if (in_array($role, ['admin', 'petugas_rm'])): ?>
          <div class="nav-section-title">Sirkulasi Berkas</div>
          <a href="<?= base_url('peminjaman/scan') ?>" class="nav-item <?= str_starts_with(service('uri')->getPath(), 'peminjaman') ? 'active' : '' ?>">
            <svg width="19" height="19" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
            Scan Pinjam
          </a>
          <a href="<?= base_url('pengembalian/scan') ?>" class="nav-item <?= str_starts_with(service('uri')->getPath(), 'pengembalian') ? 'active' : '' ?>">
            <svg width="19" height="19" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
            Scan Kembali
          </a>
        <?php endif; ?>

        <div class="nav-section-title">Monitoring & Laporan</div>
        <a href="<?= base_url('belum-kembali') ?>" class="nav-item <?= str_starts_with(service('uri')->getPath(), 'belum-kembali') ? 'active' : '' ?>">
          <svg width="19" height="19" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
          Belum Kembali
        </a>

        <?php if (in_array($role, ['admin', 'petugas_rm', 'kepala'])): ?>
          <a href="<?= base_url('riwayat') ?>" class="nav-item <?= str_starts_with(service('uri')->getPath(), 'riwayat') ? 'active' : '' ?>">
            <svg width="19" height="19" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
            Riwayat Peminjaman
          </a>
          <a href="<?= base_url('laporan') ?>" class="nav-item <?= str_starts_with(service('uri')->getPath(), 'laporan') ? 'active' : '' ?>">
            <svg width="19" height="19" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            Laporan & Analitik
          </a>
        <?php endif; ?>

        <?php if ($role === 'admin'): ?>
          <div class="nav-section-title">Administrasi</div>
          <a href="<?= base_url('unit') ?>" class="nav-item <?= str_starts_with(service('uri')->getPath(), 'unit') ? 'active' : '' ?>">
            <svg width="19" height="19" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
            Data Unit
          </a>
          <a href="<?= base_url('pengguna') ?>" class="nav-item <?= str_starts_with(service('uri')->getPath(), 'pengguna') ? 'active' : '' ?>">
            <svg width="19" height="19" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            Pengguna
          </a>
          <a href="<?= base_url('pengaturan') ?>" class="nav-item <?= str_starts_with(service('uri')->getPath(), 'pengaturan') ? 'active' : '' ?>">
            <svg width="19" height="19" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            Pengaturan Sistem
          </a>
          <a href="<?= base_url('log') ?>" class="nav-item <?= str_starts_with(service('uri')->getPath(), 'log') ? 'active' : '' ?>">
            <svg width="19" height="19" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
            Log Aktivitas
          </a>
        <?php endif; ?>
      </nav>

      <div class="sidebar-footer">
        <a href="<?= base_url('profil/ubah-sandi') ?>" class="sidebar-footer-link">
          <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
          Ubah Kata Sandi
        </a>
      </div>
    </aside>

    <!-- Main Content Layout -->
    <div class="main-content">
      <header class="topbar">
        <div class="topbar-left">
          <button id="sidebar-toggle" style="background:none; border:none; cursor:pointer; color:var(--text-primary); display:none;" aria-label="Menu">
            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
          </button>
          <div class="topbar-title"><?= esc($title ?? 'Dashboard TRACKLY') ?></div>
        </div>

        <div class="topbar-right">
          <!-- Audio Toggle Button -->
          <button type="button" id="sound-toggle-btn" class="btn-sound-toggle" aria-label="Toggle Efek Suara">
            <!-- Rendered by JS -->
          </button>

          <!-- User Badge -->
          <div class="user-badge">
            <div class="user-avatar">
              <?= strtoupper(substr(session()->get('nama') ?? 'U', 0, 1)) ?>
            </div>
            <div class="user-info">
              <span class="user-name"><?= esc(session()->get('nama')) ?></span>
              <span class="role-badge role-<?= esc($role) ?>">
                <?= esc(str_replace('_', ' ', $role)) ?>
              </span>
            </div>
          </div>

          <a href="<?= base_url('logout') ?>" class="btn btn-secondary btn-sm" style="display:inline-flex; width:auto; padding:8px 14px;" title="Keluar">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
            Keluar
          </a>
        </div>
      </header>

      <main class="page-container">
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

        <?php if (session()->getFlashdata('warning')): ?>
          <div class="alert alert-warning">
            <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
            <div><?= session()->getFlashdata('warning') ?></div>
          </div>
        <?php endif; ?>

        <?= $this->renderSection('content') ?>
      </main>
    </div>
  </div>

  <script src="<?= base_url('assets/js/trackly.js') ?>"></script>
</body>
</html>
