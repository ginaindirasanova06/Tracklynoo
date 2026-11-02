<?php echo $this->extend('layouts/main'); ?>

<?php echo $this->section('content'); ?>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; flex-wrap:wrap; gap:16px;">
  <div>
    <h2 style="font-size:1.5rem; font-weight:800; color:var(--text-primary); display:flex; align-items:center; gap:10px;">
      <span style="display:inline-flex; align-items:center; justify-content:center; width:38px; height:38px; border-radius:12px; background:linear-gradient(135deg, rgba(56,189,248,0.18), rgba(244,114,182,0.12)); color:var(--primary);">
        <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      </span>
      Riwayat Peminjaman Berkas
    </h2>
    <p style="font-size:0.875rem; color:var(--text-secondary); margin-top:4px;">Seluruh rekaman jejak sirkulasi berkas rekam medis secara kronologis</p>
  </div>
  <?php if (in_array($userRole, ['admin', 'petugas_rm'])): ?>
  <a href="<?= base_url('peminjaman/scan') ?>" class="btn btn-primary" style="width:auto; padding:10px 18px;">
    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
    Scan Pinjam Baru
  </a>
  <?php endif; ?>
</div>

<!-- Filter Bar -->
<div class="card" style="margin-bottom:20px; padding:18px 20px;">
  <form method="get" action="<?= base_url('riwayat') ?>" style="display:flex; flex-wrap:wrap; gap:12px; align-items:flex-end;">
    <div style="flex:1; min-width:160px;">
      <label style="font-size:0.75rem; font-weight:700; color:var(--text-secondary); text-transform:uppercase; letter-spacing:0.5px; display:block; margin-bottom:6px;">Pencarian</label>
      <input type="text" name="cari" value="<?= esc($keyword) ?>" class="form-control" placeholder="No. RM, nama pasien, peminjam..." style="height:38px;">
    </div>
    <div style="min-width:150px;">
      <label style="font-size:0.75rem; font-weight:700; color:var(--text-secondary); text-transform:uppercase; letter-spacing:0.5px; display:block; margin-bottom:6px;">Unit</label>
      <select name="unit_id" class="form-control" style="height:38px;">
        <option value="">Semua Unit</option>
        <?php foreach ($units as $u): ?>
        <option value="<?= $u['id'] ?>" <?= $unitId == $u['id'] ? 'selected' : '' ?>><?= esc($u['nama_unit']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div style="min-width:130px;">
      <label style="font-size:0.75rem; font-weight:700; color:var(--text-secondary); text-transform:uppercase; letter-spacing:0.5px; display:block; margin-bottom:6px;">Status</label>
      <select name="status" class="form-control" style="height:38px;">
        <option value="all" <?= $status==='all'||!$status?'selected':'' ?>>Semua Status</option>
        <option value="dipinjam" <?= $status==='dipinjam'?'selected':'' ?>>Dipinjam</option>
        <option value="dikembalikan" <?= $status==='dikembalikan'?'selected':'' ?>>Dikembalikan</option>
        <option value="terlambat" <?= $status==='terlambat'?'selected':'' ?>>Terlambat</option>
      </select>
    </div>
    <div style="min-width:130px;">
      <label style="font-size:0.75rem; font-weight:700; color:var(--text-secondary); text-transform:uppercase; letter-spacing:0.5px; display:block; margin-bottom:6px;">Dari Tanggal</label>
      <input type="date" name="dari" value="<?= esc($dari) ?>" class="form-control" style="height:38px;">
    </div>
    <div style="min-width:130px;">
      <label style="font-size:0.75rem; font-weight:700; color:var(--text-secondary); text-transform:uppercase; letter-spacing:0.5px; display:block; margin-bottom:6px;">Sampai</label>
      <input type="date" name="sampai" value="<?= esc($sampai) ?>" class="form-control" style="height:38px;">
    </div>
    <button type="submit" class="btn btn-primary btn-sm" style="height:38px; width:auto; padding:0 20px; display:inline-flex; align-items:center; gap:6px;">
      <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
      Filter
    </button>
    <a href="<?= base_url('riwayat') ?>" class="btn btn-secondary btn-sm" style="height:38px; padding:0 16px; display:inline-flex; align-items:center;">Reset</a>
  </form>
</div>

<!-- Table -->
<div class="card" style="padding:0; overflow:hidden;">
  <div class="table-responsive">
    <table class="table">
      <thead>
        <tr>
          <th style="width:140px;">Tgl Pinjam</th>
          <th>No. RM &amp; Pasien</th>
          <th>Unit &amp; Peminjam</th>
          <th>Tujuan</th>
          <th style="width:120px;">Batas Kembali</th>
          <th style="width:130px;">Tgl Kembali</th>
          <th style="width:110px;">Status</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($peminjaman)): ?>
        <tr>
          <td colspan="7" style="text-align:center; padding:50px; color:var(--text-muted);">
            <div style="width:64px; height:64px; background:var(--bg-secondary); border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 12px;">
              <svg width="32" height="32" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="opacity:0.4;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            Tidak ada data riwayat peminjaman yang cocok dengan filter.
          </td>
        </tr>
        <?php else: ?>
        <?php foreach ($peminjaman as $p):
          $isOverdue = in_array($p['status'], ['terlambat']) || ($p['status']==='dipinjam' && $p['batas_kembali'] < date('Y-m-d'));
        ?>
        <tr style="<?= $isOverdue ? 'background:rgba(239,68,68,0.03);' : '' ?>">
          <td style="font-size:0.82rem; color:var(--text-secondary);"><?= date('d/m/Y H:i', strtotime($p['tgl_pinjam'])) ?></td>
          <td>
            <a href="<?= base_url('berkas/' . $p['berkas_id']) ?>" style="font-family:var(--font-mono); font-weight:800; font-size:0.92rem; color:var(--primary); text-decoration:none;">
              <?= esc($p['no_rm']) ?>
            </a>
            <div style="font-size:0.8rem; color:var(--text-secondary);"><?= esc($p['nama_pasien']) ?></div>
          </td>
          <td>
            <div style="font-weight:700; font-size:0.88rem;"><?= esc($p['nama_unit']) ?></div>
            <div style="font-size:0.78rem; color:var(--text-secondary);"><?= esc($p['nama_peminjam']) ?></div>
          </td>
          <td style="font-size:0.83rem;"><?= esc($p['tujuan']) ?></td>
          <td style="font-size:0.83rem; font-weight:600; color:<?= $isOverdue ? '#dc2626' : 'var(--text-primary)' ?>;">
            <?= date('d/m/Y', strtotime($p['batas_kembali'])) ?>
          </td>
          <td style="font-size:0.82rem; color:var(--text-secondary);">
            <?= $p['tgl_kembali'] ? date('d/m/Y H:i', strtotime($p['tgl_kembali'])) : '<span style="color:var(--text-muted);">—</span>' ?>
          </td>
          <td>
            <?php if ($p['status'] === 'dikembalikan'): ?>
              <span class="badge badge-tersedia" style="font-size:0.7rem;">KEMBALI</span>
            <?php elseif ($p['status'] === 'terlambat' || $isOverdue): ?>
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

<!-- Pagination -->
<?php if ($pager): ?>
<div style="margin-top:20px; display:flex; justify-content:center;">
  <?= $pager->links('peminjaman', 'default_full') ?>
</div>
<?php endif; ?>

<?php echo $this->endSection(); ?>

