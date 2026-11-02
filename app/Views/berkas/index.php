<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; flex-wrap:wrap; gap:16px;">
  <div>
    <h2 style="font-size:1.5rem; font-weight:800; color:var(--text-primary); display:flex; align-items:center; gap:10px;">
      <span style="display:inline-flex; align-items:center; justify-content:center; width:38px; height:38px; border-radius:12px; background:linear-gradient(135deg, rgba(56,189,248,0.18), rgba(244,114,182,0.15)); color:var(--primary);">
        <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
      </span>
      Data Berkas Rekam Medis (RM)
    </h2>
    <p style="font-size:0.875rem; color:var(--text-secondary); margin-top:4px;">
      Katalog dan registrasi seluruh berkas rekam medis fisik beserta rak penyimpanan dan QR Code
    </p>
  </div>
  <?php if (in_array($userRole, ['admin', 'petugas_rm'])): ?>
    <div style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
      <button type="button" id="btn-print-massal" onclick="printSelectedQr()" class="btn btn-secondary btn-sm" style="display:none; width:auto; padding:9px 14px; color:var(--primary); border-color:var(--primary);">
        <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
        Cetak QR Terpilih (<span id="selected-count">0</span>)
      </button>
      <a href="<?= base_url('berkas/print-qr-massal?ids=all') ?>" target="_blank" class="btn btn-secondary btn-sm" style="width:auto; padding:9px 14px; color:var(--primary);" title="Cetak Semua Label QR">
        <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
        Cetak Semua QR
      </a>
      <button type="button" class="btn btn-secondary btn-sm" onclick="openImportModal()" style="width:auto; padding:9px 16px; color:#059669; border-color:#a7f3d0; background:rgba(16,185,129,0.06);">
        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
        Impor CSV
      </button>
      <button type="button" class="btn btn-primary" onclick="openAddBerkasModal()" style="width:auto; padding:10px 18px;">
        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Registrasi Berkas
      </button>
    </div>
  <?php endif; ?>
</div>

<!-- Import Warnings (FR-09) -->
<?php if (session()->getFlashdata('import_warnings')): ?>
<div class="card" style="margin-bottom:20px; border:1px solid #fde68a; background:rgba(254, 243, 199, 0.5); padding:16px 20px;">
  <div style="font-weight:700; color:#b45309; margin-bottom:6px; display:flex; align-items:center; gap:8px;">
    <svg width="18" height="18" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
    Catatan Baris yang Dilewati Saat Impor:
  </div>
  <ul style="margin:0; padding-left:20px; font-size:0.85rem; color:#92400e;">
    <?php foreach (session()->getFlashdata('import_warnings') as $w): ?>
      <li><?= esc($w) ?></li>
    <?php endforeach; ?>
  </ul>
</div>
<?php endif; ?>

<!-- Search & Filter Card -->
<div class="card" style="margin-bottom:24px; padding:16px 20px;">
  <form method="get" action="<?= base_url('berkas') ?>" style="display:flex; gap:12px; align-items:center; flex-wrap:wrap;">
    <div style="flex:1; min-width:240px; position:relative;">
      <input type="text" name="cari" class="form-control" placeholder="Cari No. RM, nama pasien, atau lokasi rak..." value="<?= esc($keyword ?? '') ?>" style="padding-left:14px;">
    </div>
    <div style="width:180px;">
      <select name="status" class="form-control">
        <option value="all">Semua Status</option>
        <option value="tersedia" <?= ($status === 'tersedia') ? 'selected' : '' ?>>Tersedia di Rak</option>
        <option value="dipinjam" <?= ($status === 'dipinjam') ? 'selected' : '' ?>>Sedang Dipinjam</option>
      </select>
    </div>
    <button type="submit" class="btn btn-primary btn-sm" style="width:auto; padding:10px 18px; display:inline-flex; align-items:center; gap:6px;">
      <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
      Cari &amp; Filter
    </button>
    <?php if (! empty($keyword) || (! empty($status) && $status !== 'all')): ?>
      <a href="<?= base_url('berkas') ?>" class="btn btn-secondary btn-sm" style="width:auto; padding:10px 14px; color:#ef4444;">
        Reset
      </a>
    <?php endif; ?>
  </form>
</div>

<!-- Table of Medical Records -->
<div class="card" style="padding:0; overflow:hidden;">
  <div class="table-responsive">
    <table class="table">
      <thead>
        <tr>
          <?php if (in_array($userRole, ['admin', 'petugas_rm'])): ?>
          <th style="width:40px; padding:12px 14px;">
            <input type="checkbox" id="check-all" onchange="toggleAll(this)" style="width:16px; height:16px; cursor:pointer; accent-color:var(--primary);">
          </th>
          <?php endif; ?>
          <th style="width:140px;">No. RM</th>
          <th>Nama Pasien</th>
          <th style="width:110px;">Tahun Buka</th>
          <th style="width:140px;">Lokasi Rak</th>
          <th style="width:160px;">Status Berkas</th>
          <th style="width:200px; text-align:right;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($berkas)): ?>
          <tr>
            <td colspan="<?= in_array($userRole, ['admin', 'petugas_rm']) ? '7' : '6' ?>" style="text-align:center; padding:48px; color:var(--text-muted);">
              <div style="width:64px; height:64px; background:var(--bg-secondary); border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 12px;">
                <svg width="32" height="32" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="opacity:0.4;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
              </div>
              Tidak ada data berkas rekam medis yang cocok.
            </td>
          </tr>
        <?php else: ?>
          <?php foreach ($berkas as $b): ?>
            <tr>
              <?php if (in_array($userRole, ['admin', 'petugas_rm'])): ?>
              <td style="padding:12px 14px;">
                <input type="checkbox" class="row-check" value="<?= $b['id'] ?>" onchange="updateSelectedCount()" style="width:16px; height:16px; cursor:pointer; accent-color:var(--primary);">
              </td>
              <?php endif; ?>
              <td>
                <a href="<?= base_url('berkas/' . $b['id']) ?>" style="font-family:var(--font-mono); font-weight:800; font-size:0.95rem; color:var(--primary); text-decoration:none;">
                  <?= esc($b['no_rm']) ?>
                </a>
              </td>
              <td>
                <strong style="color:var(--text-primary); font-size:0.95rem;"><?= esc($b['nama_pasien']) ?></strong>
              </td>
              <td style="color:var(--text-secondary);"><?= esc($b['tahun_buka']) ?></td>
              <td>
                <span style="background:var(--bg-secondary); padding:4px 9px; border-radius:6px; font-size:0.83rem; font-weight:700; color:var(--text-primary); border:1px solid var(--border);">
                  <?= esc($b['lokasi_rak']) ?>
                </span>
              </td>
              <td>
                <?php if ($b['status'] === 'tersedia'): ?>
                  <span class="badge badge-tersedia">TERSEDIA</span>
                <?php else: ?>
                  <span class="badge badge-dipinjam">DIPINJAM</span>
                  <?php if (! empty($activeLoans[$b['id']])): ?>
                    <div style="font-size:0.75rem; color:#b45309; margin-top:3px; font-weight:600;">
                      di <?= esc($activeLoans[$b['id']]['nama_unit']) ?>
                    </div>
                  <?php endif; ?>
                <?php endif; ?>
              </td>
              <td style="text-align:right;">
                <div style="display:inline-flex; gap:6px; align-items:center; flex-wrap:wrap; justify-content:flex-end;">
                  <a href="<?= base_url('berkas/' . $b['id']) ?>" class="btn btn-secondary btn-sm" style="padding:6px 10px; font-size:0.78rem;" title="Lihat Detail">Detail</a>

                  <?php if (in_array($userRole, ['admin', 'petugas_rm'])): ?>
                    <!-- Print QR Button -->
                    <a href="<?= base_url('berkas/print-qr/' . $b['id']) ?>" target="_blank" class="btn btn-secondary btn-sm" style="padding:6px 8px; color:var(--primary);" title="Cetak Label QR">
                      <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    </a>

                    <!-- Edit Button -->
                    <button type="button" class="btn btn-secondary btn-sm" style="padding:6px 10px; font-size:0.78rem;"
                            onclick="openEditBerkasModal(<?= $b['id'] ?>, '<?= esc($b['no_rm'], 'js') ?>', '<?= esc($b['nama_pasien'], 'js') ?>', <?= $b['tahun_buka'] ?>, '<?= esc($b['lokasi_rak'], 'js') ?>')">
                      Ubah
                    </button>

                    <!-- Delete Button -->
                    <?php if ($b['status'] === 'tersedia'): ?>
                      <form action="<?= base_url('berkas/delete/' . $b['id']) ?>" method="post" style="margin:0; display:inline;"
                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus berkas RM <?= esc($b['no_rm'], 'js') ?> a/n <?= esc($b['nama_pasien'], 'js') ?>?');">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-secondary btn-sm" style="padding:6px 8px; color:#ef4444;" title="Hapus Berkas">
                          <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                      </form>
                    <?php endif; ?>
                  <?php endif; ?>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <?php if ($pager): ?>
    <div style="padding:16px 20px; border-top:1px solid var(--border); display:flex; justify-content:center;">
      <?= $pager->links('berkas', 'default_full') ?>
    </div>
  <?php endif; ?>
</div>

<!-- Modal Registrasi Berkas Baru -->
<div id="modalAddBerkas" class="modal-backdrop" style="display:none; position:fixed; inset:0; background:rgba(15,23,42,0.6); z-index:1000; align-items:center; justify-content:center; padding:20px; backdrop-filter:blur(6px);">
  <div class="card" style="width:100%; max-width:500px; padding:28px; animation:modalIn 0.25s cubic-bezier(0.16, 1, 0.3, 1); box-shadow:var(--shadow-xl);">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
      <h3 class="card-title" style="display:flex; align-items:center; gap:8px;">
        <span style="width:32px; height:32px; border-radius:8px; background:rgba(56,189,248,0.15); display:inline-flex; align-items:center; justify-content:center; color:var(--primary);">
          <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        </span>
        Registrasi Berkas RM Baru
      </h3>
      <button type="button" onclick="closeAddBerkasModal()" style="background:none; border:none; font-size:1.5rem; cursor:pointer; color:var(--text-muted); line-height:1;">&times;</button>
    </div>

    <form action="<?= base_url('berkas') ?>" method="post">
      <?= csrf_field() ?>

      <div class="form-group">
        <label for="add_no_rm" class="form-label">Nomor Rekam Medis (No. RM) *</label>
        <input type="text" id="add_no_rm" name="no_rm" class="form-control" placeholder="Contoh: 00-12-34" required maxlength="20" style="font-family:var(--font-mono); font-weight:700;">
        <small style="color:var(--text-muted); font-size:0.75rem; margin-top:4px; display:block;">Format standar: angka &amp; tanda hubung (00-12-34)</small>
      </div>

      <div class="form-group">
        <label for="add_nama_pasien" class="form-label">Nama Lengkap Pasien *</label>
        <input type="text" id="add_nama_pasien" name="nama_pasien" class="form-control" placeholder="Nama lengkap pasien" required maxlength="100">
      </div>

      <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
        <div class="form-group">
          <label for="add_tahun_buka" class="form-label">Tahun Buka RM *</label>
          <input type="number" id="add_tahun_buka" name="tahun_buka" class="form-control" value="<?= date('Y') ?>" min="1900" max="<?= date('Y') ?>" required>
        </div>

        <div class="form-group">
          <label for="add_lokasi_rak" class="form-label">Lokasi Rak Filing *</label>
          <input type="text" id="add_lokasi_rak" name="lokasi_rak" class="form-control" placeholder="Contoh: Rak A-01" required maxlength="30">
        </div>
      </div>

      <div style="background:rgba(56,189,248,0.08); border:1px solid rgba(56,189,248,0.25); border-radius:12px; padding:12px 14px; font-size:0.8rem; color:#0369a1; margin-bottom:20px; display:flex; align-items:center; gap:8px;">
        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span>Token QR Code unik 36-karakter akan digenerate otomatis oleh sistem secara aman.</span>
      </div>

      <div style="display:flex; gap:12px; margin-top:20px;">
        <button type="submit" class="btn btn-primary" style="flex:1; justify-content:center;">Simpan Berkas</button>
        <button type="button" class="btn btn-secondary" onclick="closeAddBerkasModal()" style="width:auto; padding:0 18px;">Batal</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Ubah Berkas -->
<div id="modalEditBerkas" class="modal-backdrop" style="display:none; position:fixed; inset:0; background:rgba(15,23,42,0.6); z-index:1000; align-items:center; justify-content:center; padding:20px; backdrop-filter:blur(6px);">
  <div class="card" style="width:100%; max-width:500px; padding:28px; animation:modalIn 0.25s cubic-bezier(0.16, 1, 0.3, 1); box-shadow:var(--shadow-xl);">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
      <h3 class="card-title" style="display:flex; align-items:center; gap:8px;">
        <span style="width:32px; height:32px; border-radius:8px; background:rgba(244,114,182,0.15); display:inline-flex; align-items:center; justify-content:center; color:var(--pink);">
          <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
        </span>
        Ubah Data Berkas RM
      </h3>
      <button type="button" onclick="closeEditBerkasModal()" style="background:none; border:none; font-size:1.5rem; cursor:pointer; color:var(--text-muted); line-height:1;">&times;</button>
    </div>

    <form id="formEditBerkas" method="post">
      <?= csrf_field() ?>

      <div class="form-group">
        <label for="edit_no_rm" class="form-label">Nomor Rekam Medis (No. RM) *</label>
        <input type="text" id="edit_no_rm" name="no_rm" class="form-control" required maxlength="20" style="font-family:var(--font-mono); font-weight:700;">
        <small style="color:var(--text-muted); font-size:0.75rem; margin-top:4px; display:block;">Format: angka &amp; tanda hubung (misal: 00-12-34)</small>
      </div>

      <div class="form-group">
        <label for="edit_nama_pasien" class="form-label">Nama Lengkap Pasien *</label>
        <input type="text" id="edit_nama_pasien" name="nama_pasien" class="form-control" required maxlength="100">
      </div>

      <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
        <div class="form-group">
          <label for="edit_tahun_buka" class="form-label">Tahun Buka RM *</label>
          <input type="number" id="edit_tahun_buka" name="tahun_buka" class="form-control" min="1900" max="<?= date('Y') ?>" required>
        </div>

        <div class="form-group">
          <label for="edit_lokasi_rak" class="form-label">Lokasi Rak Filing *</label>
          <input type="text" id="edit_lokasi_rak" name="lokasi_rak" class="form-control" required maxlength="30">
        </div>
      </div>

      <div style="display:flex; gap:12px; margin-top:20px;">
        <button type="submit" class="btn btn-primary" style="flex:1; justify-content:center;">Perbarui Berkas</button>
        <button type="button" class="btn btn-secondary" onclick="closeEditBerkasModal()" style="width:auto; padding:0 18px;">Batal</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Impor CSV/Excel (FR-09) -->
<div id="modalImportCsv" class="modal-backdrop" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(15,23,42,0.6); z-index:1000; align-items:center; justify-content:center; backdrop-filter:blur(6px);">
  <div class="card" style="width:100%; max-width:540px; margin:20px; animation:modalIn 0.25s cubic-bezier(0.16, 1, 0.3, 1); box-shadow:var(--shadow-xl);">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
      <h3 class="card-title" style="margin:0; font-size:1.15rem; display:flex; align-items:center; gap:8px;">
        <span style="width:32px; height:32px; border-radius:8px; background:rgba(16,185,129,0.15); display:inline-flex; align-items:center; justify-content:center; color:#059669;">
          <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
        </span>
        Impor Berkas Rekam Medis (CSV)
      </h3>
      <button type="button" onclick="closeImportModal()" style="background:none; border:none; font-size:1.5rem; color:var(--text-muted); cursor:pointer; line-height:1;">&times;</button>
    </div>

    <!-- Download Template Prompt -->
    <div style="background:rgba(16,185,129,0.08); border:1px solid rgba(16,185,129,0.25); border-radius:12px; padding:14px 16px; margin-bottom:18px; display:flex; justify-content:space-between; align-items:center; gap:12px;">
      <div>
        <div style="font-size:0.85rem; font-weight:700; color:#065f46;">Format File Impor</div>
        <div style="font-size:0.78rem; color:#047857; margin-top:2px;">Unduh file template CSV agar format kolom sesuai standar sistem.</div>
      </div>
      <a href="<?= base_url('berkas/template-import') ?>" class="btn btn-secondary btn-sm" style="white-space:nowrap; background:#fff; font-size:0.78rem; border-color:#86efac; color:#047857; display:inline-flex; align-items:center; gap:6px;">
        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
        Unduh Template
      </a>
    </div>

    <form action="<?= base_url('berkas/import') ?>" method="post" enctype="multipart/form-data">
      <?= csrf_field() ?>

      <div class="form-group" style="margin-bottom:16px;">
        <label for="file_csv" class="form-label">Pilih File CSV (.csv) *</label>
        <input type="file" id="file_csv" name="file_csv" class="form-control" accept=".csv,text/csv" required style="padding:10px 14px;">
        <small style="color:var(--text-muted); font-size:0.75rem; margin-top:6px; display:block;">
          Header wajib: <code>no_rm, nama_pasien, tahun_buka, lokasi_rak</code>. Sistem akan membuat QR Code unik otomatis.
        </small>
      </div>

      <div style="display:flex; gap:12px; margin-top:20px;">
        <button type="submit" class="btn btn-primary" style="flex:1; justify-content:center; background:linear-gradient(135deg, #10b981, #059669); border-color:#059669;">
          <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
          Mulai Impor Berkas
        </button>
        <button type="button" class="btn btn-secondary" onclick="closeImportModal()" style="width:auto; padding:0 18px;">Batal</button>
      </div>
    </form>
  </div>
</div>

<script>
function openImportModal() {
  document.getElementById('modalImportCsv').style.display = 'flex';
}
function closeImportModal() {
  document.getElementById('modalImportCsv').style.display = 'none';
}

function openAddBerkasModal() {
  document.getElementById('modalAddBerkas').style.display = 'flex';
}
function closeAddBerkasModal() {
  document.getElementById('modalAddBerkas').style.display = 'none';
}

function openEditBerkasModal(id, noRm, namaPasien, tahunBuka, lokasiRak) {
  document.getElementById('formEditBerkas').action = '<?= base_url('berkas/update') ?>/' + id;
  document.getElementById('edit_no_rm').value = noRm;
  document.getElementById('edit_nama_pasien').value = namaPasien;
  document.getElementById('edit_tahun_buka').value = tahunBuka;
  document.getElementById('edit_lokasi_rak').value = lokasiRak;
  document.getElementById('modalEditBerkas').style.display = 'flex';
}
function closeEditBerkasModal() {
  document.getElementById('modalEditBerkas').style.display = 'none';
}

// QR Massal
function toggleAll(masterCb) {
  document.querySelectorAll('.row-check').forEach(cb => { cb.checked = masterCb.checked; });
  updateSelectedCount();
}

function updateSelectedCount() {
  const selected = document.querySelectorAll('.row-check:checked').length;
  const countSpan = document.getElementById('selected-count');
  const btn = document.getElementById('btn-print-massal');
  if (!countSpan || !btn) return;
  countSpan.textContent = selected;
  btn.style.display = selected > 0 ? 'inline-flex' : 'none';
  const allCb = document.getElementById('check-all');
  if (allCb) {
    const total = document.querySelectorAll('.row-check').length;
    allCb.indeterminate = selected > 0 && selected < total;
    allCb.checked = selected === total && total > 0;
  }
}

function printSelectedQr() {
  const ids = Array.from(document.querySelectorAll('.row-check:checked')).map(cb => cb.value);
  if (ids.length === 0) { alert('Pilih berkas terlebih dahulu.'); return; }
  window.open('<?= base_url('berkas/print-qr-massal') ?>?ids=' + ids.join(','), '_blank');
}
</script>
<?= $this->endSection() ?>


