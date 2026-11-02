<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<!-- Header Section -->
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; flex-wrap:wrap; gap:16px;">
  <div>
    <h2 style="font-size:1.5rem; font-weight:800; color:var(--text-primary); display:flex; align-items:center; gap:10px;">
      <span style="display:inline-flex; align-items:center; justify-content:center; width:38px; height:38px; border-radius:12px; background:linear-gradient(135deg, rgba(56,189,248,0.18), rgba(244,114,182,0.15)); color:var(--primary);">
        <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
      </span>
      Laporan Peminjaman Berkas Rekam Medis
    </h2>
    <p style="font-size:0.875rem; color:var(--text-secondary); margin-top:4px;">
      Analisis sirkulasi, kepatuhan batas pengembalian, dan riwayat peminjaman berkas (FR-26 &amp; FR-27)
    </p>
  </div>
  <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
    <!-- Export Excel Button -->
    <a href="<?= base_url('laporan/export-excel?' . http_build_query($filters)) ?>" class="btn btn-secondary" style="display:inline-flex; align-items:center; gap:8px;">
      <svg width="18" height="18" fill="none" stroke="#16a34a" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
      <span>Ekspor Excel</span>
    </a>
    <!-- Export PDF / Print Button -->
    <a href="<?= base_url('laporan/export-pdf?' . http_build_query($filters)) ?>" target="_blank" class="btn btn-primary" style="display:inline-flex; align-items:center; gap:8px;">
      <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
      <span>Cetak / Ekspor PDF</span>
    </a>
  </div>
</div>

<!-- Filter Panel (FR-26) -->
<div class="card" style="margin-bottom:24px; padding:20px;">
  <form method="get" action="<?= base_url('laporan') ?>">
    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap:16px; align-items:flex-end;">
      <!-- Tanggal Mulai -->
      <div class="form-group" style="margin-bottom:0;">
        <label class="form-label" style="font-size:0.75rem; font-weight:700; text-transform:uppercase; letter-spacing:0.5px;">Tanggal Mulai</label>
        <input type="date" name="tgl_mulai" class="form-control" value="<?= esc($filters['tgl_mulai']) ?>" required style="height:40px;">
      </div>

      <!-- Tanggal Selesai -->
      <div class="form-group" style="margin-bottom:0;">
        <label class="form-label" style="font-size:0.75rem; font-weight:700; text-transform:uppercase; letter-spacing:0.5px;">Tanggal Selesai</label>
        <input type="date" name="tgl_selesai" class="form-control" value="<?= esc($filters['tgl_selesai']) ?>" required style="height:40px;">
      </div>

      <!-- Unit Peminjam -->
      <?php if (session()->get('role') !== 'petugas_unit'): ?>
      <div class="form-group" style="margin-bottom:0;">
        <label class="form-label" style="font-size:0.75rem; font-weight:700; text-transform:uppercase; letter-spacing:0.5px;">Unit Peminjam</label>
        <select name="unit_id" class="form-control" style="height:40px;">
          <option value="">Semua Unit</option>
          <?php foreach ($units as $u): ?>
          <option value="<?= $u['id'] ?>" <?= ($filters['unit_id'] == $u['id']) ? 'selected' : '' ?>>
            <?= esc($u['nama_unit']) ?> (<?= esc($u['kode_unit']) ?>)
          </option>
          <?php endforeach; ?>
        </select>
      </div>
      <?php endif; ?>

      <!-- Status Peminjaman -->
      <div class="form-group" style="margin-bottom:0;">
        <label class="form-label" style="font-size:0.75rem; font-weight:700; text-transform:uppercase; letter-spacing:0.5px;">Status Transaksi</label>
        <select name="status" class="form-control" style="height:40px;">
          <option value="">Semua Status</option>
          <option value="dipinjam" <?= ($filters['status'] === 'dipinjam') ? 'selected' : '' ?>>Sedang Dipinjam</option>
          <option value="terlambat" <?= ($filters['status'] === 'terlambat') ? 'selected' : '' ?>>Terlambat</option>
          <option value="dikembalikan" <?= ($filters['status'] === 'dikembalikan') ? 'selected' : '' ?>>Sudah Dikembalikan</option>
        </select>
      </div>

      <!-- Kondisi Kembali -->
      <div class="form-group" style="margin-bottom:0;">
        <label class="form-label" style="font-size:0.75rem; font-weight:700; text-transform:uppercase; letter-spacing:0.5px;">Kondisi Berkas</label>
        <select name="kondisi_kembali" class="form-control" style="height:40px;">
          <option value="">Semua Kondisi</option>
          <option value="lengkap" <?= ($filters['kondisi_kembali'] === 'lengkap') ? 'selected' : '' ?>>Lengkap</option>
          <option value="tidak_lengkap" <?= ($filters['kondisi_kembali'] === 'tidak_lengkap') ? 'selected' : '' ?>>Tidak Lengkap</option>
        </select>
      </div>

      <!-- Filter Buttons -->
      <div style="display:flex; gap:8px;">
        <button type="submit" class="btn btn-primary" style="flex:1; height:40px; justify-content:center;">
          <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
          Filter
        </button>
        <a href="<?= base_url('laporan') ?>" class="btn btn-secondary" style="height:40px; padding:0 14px; display:inline-flex; align-items:center;" title="Reset Filter">
          Reset
        </a>
      </div>
    </div>
  </form>
</div>

<!-- KPI Summary Metrics (FR-26) -->
<div class="stats-grid" style="margin-bottom:24px;">
  <div class="stat-card">
    <div class="stat-icon blue">
      <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
    </div>
    <div class="stat-info">
      <span class="stat-label">Total Transaksi</span>
      <span class="stat-value" data-countup="<?= (int) $metrics['total'] ?>"><?= number_format($metrics['total']) ?></span>
    </div>
  </div>

  <div class="stat-card">
    <div class="stat-icon emerald">
      <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
    </div>
    <div class="stat-info">
      <span class="stat-label">Sudah Dikembalikan</span>
      <span class="stat-value" data-countup="<?= (int) $metrics['dikembalikan'] ?>"><?= number_format($metrics['dikembalikan']) ?></span>
    </div>
  </div>

  <div class="stat-card" style="<?= $metrics['terlambat'] > 0 ? 'border-color:rgba(239,68,68,0.3); background:rgba(239,68,68,0.02);' : '' ?>">
    <div class="stat-icon red">
      <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
    </div>
    <div class="stat-info">
      <span class="stat-label">Belum Kembali / Terlambat</span>
      <span class="stat-value" style="<?= $metrics['terlambat'] > 0 ? 'color:#ef4444;' : '' ?>">
        <?= number_format($metrics['dipinjam'] + $metrics['terlambat']) ?>
        <small style="font-size:0.75rem; font-weight:600; color:#dc2626;">(<?= $metrics['terlambat'] ?> terlambat)</small>
      </span>
    </div>
  </div>

  <div class="stat-card">
    <div class="stat-icon amber">
      <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
    </div>
    <div class="stat-info">
      <span class="stat-label">Tingkat Kepatuhan Waktu</span>
      <span class="stat-value" style="color:<?= $metrics['persen_tepat_waktu'] >= 85 ? '#10b981' : '#f59e0b' ?>;">
        <?= $metrics['persen_tepat_waktu'] ?>%
      </span>
    </div>
  </div>
</div>

<!-- Report Table -->
<div class="card" style="padding:0; overflow:hidden;">
  <div style="padding:20px 24px; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
    <div>
      <h3 class="card-title" style="margin:0;">Data Rinci Transaksi Peminjaman</h3>
      <p style="font-size:0.82rem; color:var(--text-secondary); margin-top:2px;">
        Ditemukan <strong><?= count($loans) ?></strong> data transaksi untuk rentang <?= date('d/m/Y', strtotime($filters['tgl_mulai'])) ?> s/d <?= date('d/m/Y', strtotime($filters['tgl_selesai'])) ?>
      </p>
    </div>
  </div>

  <div class="table-responsive">
    <table class="table">
      <thead>
        <tr>
          <th style="width:40px;">No.</th>
          <th>No. RM</th>
          <th>Nama Pasien</th>
          <th>Unit Peminjam</th>
          <th>Petugas Peminjam</th>
          <th>Tujuan</th>
          <th>Tgl Pinjam</th>
          <th>Batas Kembali</th>
          <th>Tgl Kembali</th>
          <th>Kondisi</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($loans)): ?>
          <tr>
            <td colspan="11" style="text-align:center; padding:48px; color:var(--text-muted);">
              <div style="width:64px; height:64px; background:var(--bg-secondary); border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 12px;">
                <svg width="32" height="32" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="opacity:0.4;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
              </div>
              <div style="font-weight:700; font-size:0.95rem; color:var(--text-primary);">Tidak Ada Data Ditemukan</div>
              <div style="font-size:0.82rem; color:var(--text-secondary); margin-top:2px;">Silakan sesuaikan parameter tanggal atau filter lainnya.</div>
            </td>
          </tr>
        <?php else: ?>
          <?php $no = 1; foreach ($loans as $row): ?>
            <tr>
              <td style="color:var(--text-muted); font-size:0.82rem;"><?= $no++ ?></td>
              <td>
                <a href="<?= base_url('berkas/' . $row['berkas_id']) ?>" style="font-weight:800; color:var(--primary); font-family:var(--font-mono); text-decoration:none;">
                  <?= esc($row['no_rm']) ?>
                </a>
              </td>
              <td style="font-weight:600;"><?= esc($row['nama_pasien']) ?></td>
              <td>
                <span style="font-size:0.82rem; background:var(--bg-secondary); padding:3px 8px; border-radius:6px; font-weight:700; color:var(--text-primary); border:1px solid var(--border);">
                  <?= esc($row['nama_unit']) ?>
                </span>
              </td>
              <td style="font-size:0.85rem;"><?= esc($row['nama_peminjam']) ?></td>
              <td style="font-size:0.82rem; color:var(--text-secondary);"><?= esc($row['tujuan']) ?></td>
              <td style="font-size:0.83rem; color:var(--text-secondary);"><?= date('d/m/Y H:i', strtotime($row['tgl_pinjam'])) ?></td>
              <td style="font-size:0.83rem; font-weight:600;"><?= date('d/m/Y', strtotime($row['batas_kembali'])) ?></td>
              <td style="font-size:0.83rem; color:var(--text-secondary);">
                <?= $row['tgl_kembali'] ? date('d/m/Y H:i', strtotime($row['tgl_kembali'])) : '<span style="color:var(--text-muted);">-</span>' ?>
              </td>
              <td>
                <?php if ($row['kondisi_kembali'] === 'lengkap'): ?>
                  <span class="badge badge-tersedia" style="font-size:0.7rem;">Lengkap</span>
                <?php elseif ($row['kondisi_kembali'] === 'tidak_lengkap'): ?>
                  <span class="badge badge-terlambat" style="font-size:0.7rem;" title="<?= esc($row['catatan'] ?? '') ?>">Tidak Lengkap</span>
                <?php else: ?>
                  <span style="color:var(--text-muted); font-size:0.82rem;">-</span>
                <?php endif; ?>
              </td>
              <td>
                <?php if ($row['status'] === 'dikembalikan'): ?>
                  <span class="badge badge-tersedia" style="font-size:0.7rem;">DIKEMBALIKAN</span>
                <?php elseif ($row['status'] === 'terlambat'): ?>
                  <span class="badge badge-terlambat" style="font-size:0.7rem;">TERLAMBAT</span>
                <?php else: ?>
                  <span class="badge badge-dipinjam" style="font-size:0.7rem;">DIPINJAM</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?= $this->endSection() ?>

