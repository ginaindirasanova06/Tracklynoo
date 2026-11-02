<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; flex-wrap:wrap; gap:16px;">
  <div>
    <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
      <h2 style="font-size:1.5rem; font-weight:800; color:var(--text-primary); display:flex; align-items:center; gap:10px;">
        <span style="display:inline-flex; align-items:center; justify-content:center; width:38px; height:38px; border-radius:12px; background:linear-gradient(135deg, rgba(56,189,248,0.18), rgba(244,114,182,0.15)); color:var(--primary);">
          <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        </span>
        Berkas RM No. <?= esc($berkas['no_rm']) ?>
      </h2>
      <?php if ($berkas['status'] === 'tersedia'): ?>
        <span class="badge badge-tersedia" style="font-size:0.8rem; padding:5px 12px;">TERSEDIA DI RAK</span>
      <?php else: ?>
        <span class="badge badge-dipinjam" style="font-size:0.8rem; padding:5px 12px;">SEDANG DIPINJAM</span>
      <?php endif; ?>
    </div>
    <p style="font-size:0.875rem; color:var(--text-secondary); margin-top:4px;">
      Pasien: <strong style="color:var(--text-primary);"><?= esc($berkas['nama_pasien']) ?></strong> &bull; Terdaftar sejak <?= esc($berkas['tahun_buka']) ?>
    </p>
  </div>

  <div style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
    <?php if (in_array($userRole, ['admin', 'petugas_rm'])): ?>
      <!-- Print QR Button -->
      <a href="<?= base_url('berkas/print-qr/' . $berkas['id']) ?>" target="_blank" class="btn btn-secondary btn-sm" style="padding:9px 16px; color:var(--primary);" title="Cetak Label QR">
        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
        Cetak QR
      </a>
      <!-- Edit Button -->
      <button type="button" class="btn btn-secondary btn-sm" onclick="openEditModalDetail()" style="padding:9px 16px;">
        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
        Ubah Data
      </button>
    <?php endif; ?>
    <?php if ($userRole === 'admin'): ?>
      <!-- Regenerate QR (Admin only) -->
      <form action="<?= base_url('berkas/regenerate-qr/' . $berkas['id']) ?>" method="post" style="margin:0;"
            onsubmit="return confirm('Yakin ingin membuat ulang QR Code? Kode QR lama akan TIDAK BERLAKU dan harus dicetak ulang.');">
        <?= csrf_field() ?>
        <button type="submit" class="btn btn-secondary btn-sm" style="padding:9px 14px; color:#f59e0b;" title="Buat Ulang QR Code">
          <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
          Regenerate QR
        </button>
      </form>
    <?php endif; ?>
    <a href="<?= base_url('berkas') ?>" class="btn btn-secondary btn-sm" style="padding:9px 16px;">
      &larr; Kembali
    </a>
  </div>
</div>

<!-- Active Loan Alert (if borrowed) -->
<?php if ($berkas['status'] === 'dipinjam' && $activeLoan): ?>
  <?php
    $isOverdue = strtotime($activeLoan['batas_kembali']) < strtotime(date('Y-m-d'));
  ?>
  <div style="background:<?= $isOverdue ? 'rgba(239, 68, 68, 0.05)' : 'linear-gradient(135deg, rgba(56,189,248,0.08), rgba(244,114,182,0.08))' ?>; border:1.5px solid <?= $isOverdue ? 'rgba(239,68,68,0.3)' : 'rgba(56,189,248,0.3)' ?>; border-radius:18px; padding:20px 24px; margin-bottom:28px; box-shadow:var(--shadow-sm);">
    <div style="display:flex; align-items:center; gap:10px; margin-bottom:14px; flex-wrap:wrap;">
      <span class="badge <?= $isOverdue ? 'badge-terlambat' : 'badge-dipinjam' ?>" style="font-size:0.75rem;">
        <?= $isOverdue ? 'STATUS: TERLAMBAT KEMBALI' : 'STATUS: SEDANG DIPINJAM' ?>
      </span>
      <span style="font-size:0.85rem; color:var(--text-secondary);">
        Dipinjam pada: <strong style="color:var(--text-primary);"><?= date('d M Y, H:i', strtotime($activeLoan['tgl_pinjam'])) ?></strong>
      </span>
    </div>

    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:16px;">
      <div>
        <div style="font-size:0.72rem; text-transform:uppercase; color:var(--text-secondary); font-weight:700; letter-spacing:0.5px;">Unit Peminjam</div>
        <div style="font-size:1.05rem; font-weight:800; color:var(--text-primary); margin-top:2px;">
          <?= esc($activeLoan['nama_unit']) ?> (<?= esc($activeLoan['kode_unit']) ?>)
        </div>
      </div>
      <div>
        <div style="font-size:0.72rem; text-transform:uppercase; color:var(--text-secondary); font-weight:700; letter-spacing:0.5px;">Petugas Peminjam</div>
        <div style="font-size:1.05rem; font-weight:800; color:var(--text-primary); margin-top:2px;">
          <?= esc($activeLoan['nama_peminjam']) ?>
        </div>
      </div>
      <div>
        <div style="font-size:0.72rem; text-transform:uppercase; color:var(--text-secondary); font-weight:700; letter-spacing:0.5px;">Tujuan Peminjaman</div>
        <div style="font-size:0.95rem; font-weight:700; color:var(--text-primary); margin-top:2px;">
          <?= esc($activeLoan['tujuan']) ?>
        </div>
      </div>
      <div>
        <div style="font-size:0.72rem; text-transform:uppercase; color:var(--text-secondary); font-weight:700; letter-spacing:0.5px;">Batas Waktu Kembali</div>
        <div style="font-size:1.05rem; font-weight:800; color:<?= $isOverdue ? '#dc2626' : 'var(--text-primary)' ?>; margin-top:2px;">
          <?= date('d M Y', strtotime($activeLoan['batas_kembali'])) ?>
        </div>
      </div>
    </div>
  </div>
<?php endif; ?>

<div style="display:grid; grid-template-columns:1fr 2fr; gap:24px; margin-bottom:28px; align-items:start;">
  <!-- File Identity Card -->
  <div class="card">
    <h3 class="card-title" style="margin-bottom:16px; display:flex; align-items:center; gap:8px;">
      <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
      Identitas Berkas
    </h3>
    <div style="display:flex; flex-direction:column; gap:16px;">
      <div>
        <div style="font-size:0.72rem; font-weight:700; text-transform:uppercase; color:var(--text-secondary); letter-spacing:0.5px;">Nomor Rekam Medis</div>
        <div style="font-size:1.3rem; font-weight:800; font-family:var(--font-mono); color:var(--primary); margin-top:2px;">
          <?= esc($berkas['no_rm']) ?>
        </div>
      </div>
      <div>
        <div style="font-size:0.72rem; font-weight:700; text-transform:uppercase; color:var(--text-secondary); letter-spacing:0.5px;">Nama Pasien</div>
        <div style="font-size:1.05rem; font-weight:700; color:var(--text-primary); margin-top:2px;">
          <?= esc($berkas['nama_pasien']) ?>
        </div>
      </div>
      <div>
        <div style="font-size:0.72rem; font-weight:700; text-transform:uppercase; color:var(--text-secondary); letter-spacing:0.5px;">Tahun Buka RM</div>
        <div style="font-size:0.95rem; font-weight:600; color:var(--text-primary); margin-top:2px;">
          <?= esc($berkas['tahun_buka']) ?>
        </div>
      </div>
      <div>
        <div style="font-size:0.72rem; font-weight:700; text-transform:uppercase; color:var(--text-secondary); letter-spacing:0.5px;">Lokasi Rak Filing</div>
        <div style="margin-top:4px;">
          <span style="background:var(--bg-secondary); padding:4px 10px; border-radius:6px; font-size:0.9rem; font-weight:700; color:var(--text-primary); border:1px solid var(--border);">
            <?= esc($berkas['lokasi_rak']) ?>
          </span>
        </div>
      </div>
      <div style="padding-top:14px; border-top:1px solid var(--border);">
        <div style="font-size:0.72rem; font-weight:700; text-transform:uppercase; color:var(--text-secondary); letter-spacing:0.5px;">Token QR Code Unik</div>
        <code style="display:block; font-size:0.75rem; background:rgba(56,189,248,0.08); padding:8px 12px; border-radius:8px; margin-top:6px; word-break:break-all; color:#0369a1; border:1px solid rgba(56,189,248,0.2);">
          <?= esc($berkas['kode_qr']) ?>
        </code>
      </div>
    </div>
  </div>

  <!-- Loan History for this File -->
  <div class="card" style="padding:0; overflow:hidden;">
    <div style="padding:20px 24px; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:center;">
      <div>
        <h3 class="card-title" style="margin:0;">Riwayat Peminjaman Berkas Ini</h3>
        <p style="font-size:0.85rem; color:var(--text-secondary); margin-top:2px;">Daftar transaksi sirkulasi peminjaman berkas rekam medis ini</p>
      </div>
      <span class="badge badge-dipinjam" style="font-size:0.72rem;"><?= count($history) ?> Transaksi</span>
    </div>

    <div class="table-responsive">
      <table class="table">
        <thead>
          <tr>
            <th>Tanggal Pinjam</th>
            <th>Unit &amp; Peminjam</th>
            <th>Batas Kembali</th>
            <th>Tgl Kembali</th>
            <th>Kondisi</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($history)): ?>
            <tr>
              <td colspan="6" style="text-align:center; padding:40px; color:var(--text-muted);">
                <svg width="40" height="40" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="opacity:0.3; margin:0 auto 10px; display:block;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Belum ada riwayat peminjaman untuk berkas ini.
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($history as $h): ?>
              <tr>
                <td style="font-size:0.83rem; color:var(--text-secondary);"><?= date('d/m/Y H:i', strtotime($h['tgl_pinjam'])) ?></td>
                <td>
                  <strong style="color:var(--text-primary);"><?= esc($h['nama_unit']) ?></strong>
                  <div style="font-size:0.78rem; color:var(--text-secondary);"><?= esc($h['nama_peminjam']) ?></div>
                </td>
                <td style="font-size:0.83rem;"><?= date('d/m/Y', strtotime($h['batas_kembali'])) ?></td>
                <td style="font-size:0.83rem; color:var(--text-secondary);">
                  <?= $h['tgl_kembali'] ? date('d/m/Y H:i', strtotime($h['tgl_kembali'])) : '<span style="color:var(--text-muted);">&mdash;</span>' ?>
                </td>
                <td>
                  <?php if ($h['kondisi_kembali'] === 'lengkap'): ?>
                    <span style="color:#059669; font-weight:700; font-size:0.8rem; display:inline-flex; align-items:center; gap:4px;">
                      <svg width="14" height="14" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                      Lengkap
                    </span>
                  <?php elseif ($h['kondisi_kembali'] === 'tidak_lengkap'): ?>
                    <span style="color:#dc2626; font-weight:700; font-size:0.8rem;" title="<?= esc($h['catatan']) ?>">Tidak Lengkap</span>
                  <?php else: ?>
                    <span style="color:var(--text-muted);">&mdash;</span>
                  <?php endif; ?>
                </td>
                <td>
                  <?php if ($h['status'] === 'dikembalikan'): ?>
                    <span class="badge badge-tersedia" style="font-size:0.7rem;">DIKEMBALIKAN</span>
                  <?php elseif ($h['status'] === 'terlambat'): ?>
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
</div>

<!-- Modal Ubah Berkas Detail -->
<div id="modalEditDetail" class="modal-backdrop" style="display:none; position:fixed; inset:0; background:rgba(15,23,42,0.6); z-index:1000; align-items:center; justify-content:center; padding:20px; backdrop-filter:blur(6px);">
  <div class="card" style="width:100%; max-width:500px; padding:28px; animation:modalIn 0.25s cubic-bezier(0.16, 1, 0.3, 1); box-shadow:var(--shadow-xl);">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
      <h3 class="card-title" style="display:flex; align-items:center; gap:8px;">
        <span style="width:32px; height:32px; border-radius:8px; background:rgba(244,114,182,0.15); display:inline-flex; align-items:center; justify-content:center; color:var(--pink);">
          <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
        </span>
        Ubah Data Berkas RM
      </h3>
      <button type="button" onclick="closeEditModalDetail()" style="background:none; border:none; font-size:1.5rem; cursor:pointer; color:var(--text-muted); line-height:1;">&times;</button>
    </div>

    <form action="<?= base_url('berkas/update/' . $berkas['id']) ?>" method="post">
      <?= csrf_field() ?>

      <div class="form-group">
        <label for="detail_no_rm" class="form-label">Nomor Rekam Medis (No. RM) *</label>
        <input type="text" id="detail_no_rm" name="no_rm" class="form-control" value="<?= esc($berkas['no_rm']) ?>" required maxlength="20" style="font-family:var(--font-mono); font-weight:700;">
        <small style="color:var(--text-muted); font-size:0.75rem; margin-top:4px; display:block;">Format: angka &amp; tanda hubung (misal: 00-12-34)</small>
      </div>

      <div class="form-group">
        <label for="detail_nama_pasien" class="form-label">Nama Lengkap Pasien *</label>
        <input type="text" id="detail_nama_pasien" name="nama_pasien" class="form-control" value="<?= esc($berkas['nama_pasien']) ?>" required maxlength="100">
      </div>

      <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
        <div class="form-group">
          <label for="detail_tahun_buka" class="form-label">Tahun Buka RM *</label>
          <input type="number" id="detail_tahun_buka" name="tahun_buka" class="form-control" value="<?= esc($berkas['tahun_buka']) ?>" min="1900" max="<?= date('Y') ?>" required>
        </div>

        <div class="form-group">
          <label for="detail_lokasi_rak" class="form-label">Lokasi Rak Filing *</label>
          <input type="text" id="detail_lokasi_rak" name="lokasi_rak" class="form-control" value="<?= esc($berkas['lokasi_rak']) ?>" required maxlength="30">
        </div>
      </div>

      <div style="display:flex; gap:12px; margin-top:20px;">
        <button type="submit" class="btn btn-primary" style="flex:1; justify-content:center;">Simpan Perubahan</button>
        <button type="button" class="btn btn-secondary" onclick="closeEditModalDetail()" style="width:auto; padding:0 18px;">Batal</button>
      </div>
    </form>
  </div>
</div>

<script>
function openEditModalDetail() {
  document.getElementById('modalEditDetail').style.display = 'flex';
}
function closeEditModalDetail() {
  document.getElementById('modalEditDetail').style.display = 'none';
}
</script>
<?= $this->endSection() ?>

