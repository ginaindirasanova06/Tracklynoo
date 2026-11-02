<?php echo $this->extend('layouts/main'); ?>

<?php echo $this->section('content'); ?>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; flex-wrap:wrap; gap:16px;">
  <div>
    <h2 style="font-size:1.5rem; font-weight:800; color:var(--text-primary); display:flex; align-items:center; gap:10px;">
      <span style="display:inline-flex; align-items:center; justify-content:center; width:38px; height:38px; border-radius:12px; background:linear-gradient(135deg, rgba(244,114,182,0.18), rgba(239,68,68,0.12)); color:#ec4899;">
        <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      </span>
      Monitoring Berkas Belum Kembali
    </h2>
    <p style="font-size:0.875rem; color:var(--text-secondary); margin-top:4px;">
      <span style="font-weight:700; color:var(--text-primary);"><?= count($peminjaman) ?> berkas</span> dalam peminjaman aktif &bull; 
      <span style="color:#dc2626; font-weight:700; background:rgba(239,68,68,0.08); padding:3px 8px; border-radius:var(--radius-full); font-size:0.8rem;">
        <?= count(array_filter($peminjaman, fn($p) => $p['status']==='terlambat' || (in_array($p['status'],['dipinjam','terlambat']) && $p['batas_kembali'] < date('Y-m-d')))) ?> terlambat dikembalikan
      </span>
    </p>
  </div>
  <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
    <!-- Export Excel Button -->
    <a href="<?= base_url('belum-kembali/export-excel' . ($unitId ? '?unit_id=' . $unitId : '')) ?>" class="btn btn-secondary" style="display:inline-flex; align-items:center; gap:8px;">
      <svg width="18" height="18" fill="none" stroke="#16a34a" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
      <span>Ekspor Excel</span>
    </a>
    <?php if (in_array($userRole, ['admin', 'petugas_rm'])): ?>
    <a href="<?= base_url('pengembalian/scan') ?>" class="btn btn-primary" style="width:auto; padding:10px 18px; background:linear-gradient(135deg, #10b981, #059669);">
      <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
      Scan Pengembalian
    </a>
    <?php endif; ?>
  </div>
</div>

<!-- Unit Filter -->
<?php if (in_array($userRole, ['admin', 'petugas_rm', 'kepala'])): ?>
<div class="card" style="margin-bottom:20px; padding:16px 20px;">
  <form method="get" action="<?= base_url('belum-kembali') ?>" style="display:flex; gap:12px; align-items:center; flex-wrap:wrap;">
    <div style="display:flex; align-items:center; gap:8px;">
      <svg width="18" height="18" fill="none" stroke="var(--primary)" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
      <label style="font-size:0.85rem; font-weight:700; color:var(--text-secondary);">Filter Unit Peminjam:</label>
    </div>
    <select name="unit_id" class="form-control" style="width:240px; height:38px;" onchange="this.form.submit()">
      <option value="">Semua Unit Pelayanan</option>
      <?php foreach ($units as $u): ?>
      <option value="<?= $u['id'] ?>" <?= $unitId == $u['id'] ? 'selected' : '' ?>><?= esc($u['nama_unit']) ?></option>
      <?php endforeach; ?>
    </select>
    <?php if ($unitId): ?>
    <a href="<?= base_url('belum-kembali') ?>" class="btn btn-secondary btn-sm" style="padding:8px 16px; height:38px; display:inline-flex; align-items:center;">Reset Filter</a>
    <?php endif; ?>
  </form>
</div>
<?php endif; ?>

<?php if (empty($peminjaman)): ?>
<div class="card" style="text-align:center; padding:64px 32px;">
  <div style="width:84px; height:84px; background:linear-gradient(135deg, rgba(16,185,129,0.15), rgba(56,189,248,0.15)); border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 20px; border:1px solid rgba(16,185,129,0.3);">
    <svg width="40" height="40" fill="#059669" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
  </div>
  <h3 style="font-size:1.25rem; font-weight:800; color:#059669; margin-bottom:8px;">Semua Berkas Tertata Rapi!</h3>
  <p style="color:var(--text-secondary); font-size:0.92rem; max-width:400px; margin:0 auto;">Seluruh berkas rekam medis yang dipinjam telah dikembalikan ke rak penyimpanan.</p>
</div>
<?php else: ?>

<!-- Table -->
<div class="card" style="padding:0; overflow:hidden;">
  <div class="table-responsive">
    <table class="table">
      <thead>
        <tr>
          <th>No. RM &amp; Pasien</th>
          <th>Lokasi Rak</th>
          <th>Unit &amp; Peminjam</th>
          <th>Tujuan</th>
          <th>Tgl Pinjam</th>
          <th>Batas Kembali</th>
          <th>Lama (Hari)</th>
          <th>Status</th>
          <?php if (in_array($userRole, ['admin', 'petugas_rm'])): ?>
          <th style="text-align:right;">Aksi</th>
          <?php endif; ?>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($peminjaman as $p):
          $today = new DateTime();
          $batas = new DateTime($p['batas_kembali']);
          $tglPinjam = new DateTime($p['tgl_pinjam']);
          $isOverdue = $batas < $today && $p['status'] !== 'dikembalikan';
          $lamaPinjam = (int) $today->diff($tglPinjam)->days;
          $selisihBatas = $today->diff($batas)->days;
        ?>
        <tr style="<?= $isOverdue ? 'background:rgba(239,68,68,0.03);' : '' ?>">
          <td>
            <a href="<?= base_url('berkas/' . $p['berkas_id']) ?>" style="font-family:var(--font-mono); font-weight:800; font-size:0.92rem; color:var(--primary); text-decoration:none;">
              <?= esc($p['no_rm']) ?>
            </a>
            <div style="font-size:0.8rem; color:var(--text-secondary);"><?= esc($p['nama_pasien']) ?></div>
          </td>
          <td>
            <code style="background:var(--bg-secondary); padding:4px 8px; border-radius:6px; font-size:0.82rem; font-weight:700; border:1px solid var(--border);"><?= esc($p['lokasi_rak']) ?></code>
          </td>
          <td>
            <div style="font-weight:700; font-size:0.88rem;"><?= esc($p['nama_unit']) ?></div>
            <div style="font-size:0.78rem; color:var(--text-secondary);"><?= esc($p['nama_peminjam']) ?></div>
          </td>
          <td style="font-size:0.82rem;"><?= esc($p['tujuan']) ?></td>
          <td style="font-size:0.82rem; color:var(--text-secondary);"><?= date('d/m/Y', strtotime($p['tgl_pinjam'])) ?></td>
          <td style="font-weight:700; font-size:0.85rem; color:<?= $isOverdue ? '#dc2626' : 'var(--text-primary)' ?>;">
            <?= date('d/m/Y', strtotime($p['batas_kembali'])) ?>
            <?php if ($isOverdue): ?>
            <div style="font-size:0.72rem; color:#dc2626; font-weight:800;">+<?= $selisihBatas ?> hari lewat</div>
            <?php endif; ?>
          </td>
          <td style="font-size:0.88rem; font-weight:700; color:<?= $lamaPinjam > 3 ? '#dc2626' : 'var(--text-primary)' ?>;">
            <?= $lamaPinjam ?> hari
          </td>
          <td>
            <?php if ($isOverdue): ?>
            <span class="badge badge-terlambat" style="font-size:0.7rem; display:inline-flex; align-items:center; gap:5px;">
              <span style="width:6px; height:6px; background:#dc2626; border-radius:50%; display:inline-block; animation:pulse 1s infinite;"></span>
              TERLAMBAT
            </span>
            <?php else: ?>
            <span class="badge badge-dipinjam" style="font-size:0.7rem;">DIPINJAM</span>
            <?php endif; ?>
          </td>
          <?php if (in_array($userRole, ['admin', 'petugas_rm'])): ?>
          <td style="text-align:right;">
            <div style="display:inline-flex; gap:6px;">
              <button type="button" class="btn btn-secondary btn-sm" style="padding:5px 10px; font-size:0.75rem;" onclick="openPerpanjangModal(<?= $p['id'] ?>, '<?= esc($p['no_rm']) ?>', '<?= esc($p['nama_pasien']) ?>', '<?= $p['batas_kembali'] ?>')">
                Perpanjang
              </button>
              <a href="<?= base_url('pengembalian/scan') ?>" class="btn btn-primary btn-sm" style="padding:5px 10px; font-size:0.75rem; background:linear-gradient(135deg, #10b981, #059669); border-color:#059669;">
                Kembalikan
              </a>
            </div>
          </td>
          <?php endif; ?>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php endif; ?>

<!-- Modal Perpanjang Peminjaman (FR-18) -->
<div id="modalPerpanjang" class="modal-backdrop" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(15,23,42,0.6); z-index:1000; align-items:center; justify-content:center; backdrop-filter:blur(6px);">
  <div class="card" style="width:100%; max-width:480px; margin:20px; animation:modalIn 0.25s cubic-bezier(0.16, 1, 0.3, 1); box-shadow:var(--shadow-xl);">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
      <h3 class="card-title" style="margin:0; font-size:1.1rem; display:flex; align-items:center; gap:8px;">
        <span style="width:32px; height:32px; border-radius:8px; background:rgba(56,189,248,0.15); display:inline-flex; align-items:center; justify-content:center; color:var(--primary);">
          <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </span>
        Perpanjang Masa Peminjaman
      </h3>
      <button type="button" onclick="closePerpanjangModal()" style="background:none; border:none; font-size:1.5rem; color:var(--text-muted); cursor:pointer; line-height:1;">&times;</button>
    </div>

    <div style="background:var(--bg-secondary); border:1px solid var(--border); border-radius:12px; padding:14px; margin-bottom:18px; font-size:0.85rem; line-height:1.7;">
      <div>No. RM: <strong id="ext_no_rm" style="font-family:var(--font-mono); color:var(--primary);">-</strong></div>
      <div>Pasien: <strong id="ext_pasien" style="color:var(--text-primary);">-</strong></div>
      <div>Batas Saat Ini: <strong id="ext_batas_lama" style="color:#dc2626;">-</strong></div>
    </div>

    <form id="formPerpanjang" method="post">
      <?= csrf_field() ?>

      <div class="form-group" style="margin-bottom:14px;">
        <label for="batas_kembali_baru" class="form-label">Batas Kembali Baru *</label>
        <input type="date" id="batas_kembali_baru" name="batas_kembali_baru" class="form-control" required>
      </div>

      <div class="form-group" style="margin-bottom:18px;">
        <label for="alasan_perpanjang" class="form-label">Alasan Perpanjangan *</label>
        <textarea id="alasan_perpanjang" name="alasan" class="form-control" rows="2" placeholder="Contoh: Pasien masih rawat inap / instruksi dokter penanggung jawab..." required></textarea>
      </div>

      <div style="display:flex; gap:12px;">
        <button type="submit" class="btn btn-primary" style="flex:1; justify-content:center;">Simpan Perpanjangan</button>
        <button type="button" class="btn btn-secondary" onclick="closePerpanjangModal()" style="width:auto; padding:0 18px;">Batal</button>
      </div>
    </form>
  </div>
</div>

<script>
function openPerpanjangModal(loanId, noRm, namaPasien, batasLama) {
  document.getElementById('formPerpanjang').action = '<?= base_url('peminjaman/perpanjang') ?>/' + loanId;
  document.getElementById('ext_no_rm').textContent = noRm;
  document.getElementById('ext_pasien').textContent = namaPasien;
  document.getElementById('ext_batas_lama').textContent = batasLama;
  document.getElementById('batas_kembali_baru').min = batasLama;
  document.getElementById('batas_kembali_baru').value = '';
  document.getElementById('alasan_perpanjang').value = '';
  document.getElementById('modalPerpanjang').style.display = 'flex';
}
function closePerpanjangModal() {
  document.getElementById('modalPerpanjang').style.display = 'none';
}
</script>

<?php echo $this->endSection(); ?>

