<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; flex-wrap:wrap; gap:16px;">
  <div>
    <h2 style="font-size:1.5rem; font-weight:800; color:var(--text-primary); display:flex; align-items:center; gap:10px;">
      <span style="display:inline-flex; align-items:center; justify-content:center; width:38px; height:38px; border-radius:12px; background:linear-gradient(135deg, rgba(56,189,248,0.18), rgba(244,114,182,0.15)); color:var(--primary);">
        <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
      </span>
      Master Data Unit / Ruangan
    </h2>
    <p style="font-size:0.875rem; color:var(--text-secondary); margin-top:4px;">
      Kelola poliklinik, bangsal rawat inap, IGD, dan unit peminjam berkas rekam medis
    </p>
  </div>
  <button type="button" class="btn btn-primary" onclick="openAddModal()" style="width:auto; padding:10px 18px;">
    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
    Tambah Unit Baru
  </button>
</div>

<!-- Search & Filter Card -->
<div class="card" style="margin-bottom:24px; padding:16px 20px;">
  <form method="get" action="<?= base_url('unit') ?>" style="display:flex; gap:12px; align-items:center; flex-wrap:wrap;">
    <div style="flex:1; min-width:220px;">
      <input type="text" name="cari" class="form-control" placeholder="Cari kode atau nama unit..." value="<?= esc($keyword ?? '') ?>">
    </div>
    <div style="width:170px;">
      <select name="status" class="form-control">
        <option value="all">Semua Status</option>
        <option value="1" <?= ($status === '1') ? 'selected' : '' ?>>Hanya Aktif</option>
        <option value="0" <?= ($status === '0') ? 'selected' : '' ?>>Nonaktif</option>
      </select>
    </div>
    <button type="submit" class="btn btn-primary btn-sm" style="width:auto; padding:10px 18px; display:inline-flex; align-items:center; gap:6px;">
      <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
      Filter
    </button>
    <?php if (! empty($keyword) || ($status !== null && $status !== '' && $status !== 'all')): ?>
      <a href="<?= base_url('unit') ?>" class="btn btn-secondary btn-sm" style="width:auto; padding:10px 14px; color:#ef4444;">
        Reset
      </a>
    <?php endif; ?>
  </form>
</div>

<!-- Table of Units -->
<div class="card" style="padding:0; overflow:hidden;">
  <div class="table-responsive">
    <table class="table">
      <thead>
        <tr>
          <th style="width:140px;">Kode Unit</th>
          <th>Nama Unit / Ruangan</th>
          <th style="width:130px;">Status</th>
          <th style="width:200px;">Keterkaitan Sistem</th>
          <th style="width:200px; text-align:right;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($units)): ?>
          <tr>
            <td colspan="5" style="text-align:center; padding:48px; color:var(--text-muted);">
              <div style="width:64px; height:64px; background:var(--bg-secondary); border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 12px;">
                <svg width="32" height="32" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="opacity:0.4;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
              </div>
              Tidak ada data unit yang sesuai kriteria pencarian.
            </td>
          </tr>
        <?php else: ?>
          <?php foreach ($units as $u): ?>
            <tr>
              <td>
                <span style="font-family:var(--font-mono); font-weight:700; background:var(--bg-secondary); padding:4px 9px; border-radius:6px; color:var(--primary); font-size:0.85rem; border:1px solid var(--border);">
                  <?= esc($u['kode_unit']) ?>
                </span>
              </td>
              <td>
                <strong style="color:var(--text-primary); font-size:0.95rem;"><?= esc($u['nama_unit']) ?></strong>
              </td>
              <td>
                <?php if ($u['is_active']): ?>
                  <span class="badge badge-tersedia">AKTIF</span>
                <?php else: ?>
                  <span class="badge" style="background:#f1f5f9; color:#64748b;">NONAKTIF</span>
                <?php endif; ?>
              </td>
              <td style="font-size:0.8rem; color:var(--text-secondary);">
                <div><?= $unitStats[$u['id']]['users_count'] ?? 0 ?> Akun Pengguna</div>
                <div><?= $unitStats[$u['id']]['loans_count'] ?? 0 ?> Transaksi Pinjam</div>
              </td>
              <td style="text-align:right;">
                <div style="display:inline-flex; gap:6px; align-items:center;">
                  <!-- Edit Button -->
                  <button type="button" class="btn btn-secondary btn-sm" style="padding:6px 10px; font-size:0.78rem;"
                          onclick="openEditModal(<?= $u['id'] ?>, '<?= esc($u['kode_unit'], 'js') ?>', '<?= esc($u['nama_unit'], 'js') ?>', <?= $u['is_active'] ?>)">
                    Ubah
                  </button>

                  <!-- Toggle Status -->
                  <form action="<?= base_url('unit/toggle/' . $u['id']) ?>" method="post" style="margin:0; display:inline;">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-secondary btn-sm" style="padding:6px 10px; font-size:0.78rem;" title="Ubah Status Aktif/Nonaktif">
                      <?= $u['is_active'] ? 'Nonaktifkan' : 'Aktifkan' ?>
                    </button>
                  </form>

                  <!-- Delete Button -->
                  <form action="<?= base_url('unit/delete/' . $u['id']) ?>" method="post" style="margin:0; display:inline;"
                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus unit <?= esc($u['nama_unit'], 'js') ?>?');">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-secondary btn-sm" style="padding:6px 8px; color:#ef4444;" title="Hapus Unit">
                      <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                  </form>
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
      <?= $pager->links('units', 'default_full') ?>
    </div>
  <?php endif; ?>
</div>

<!-- Modal Tambah Unit -->
<div id="modalAdd" class="modal-backdrop" style="display:none; position:fixed; inset:0; background:rgba(15,23,42,0.6); z-index:1000; align-items:center; justify-content:center; padding:20px; backdrop-filter:blur(6px);">
  <div class="card" style="width:100%; max-width:480px; padding:28px; animation:modalIn 0.25s cubic-bezier(0.16, 1, 0.3, 1); box-shadow:var(--shadow-xl);">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
      <h3 class="card-title" style="display:flex; align-items:center; gap:8px;">
        <span style="width:32px; height:32px; border-radius:8px; background:rgba(56,189,248,0.15); display:inline-flex; align-items:center; justify-content:center; color:var(--primary);">
          <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        </span>
        Tambah Unit Baru
      </h3>
      <button type="button" onclick="closeAddModal()" style="background:none; border:none; font-size:1.5rem; cursor:pointer; color:var(--text-muted); line-height:1;">&times;</button>
    </div>

    <form action="<?= base_url('unit') ?>" method="post">
      <?= csrf_field() ?>
      <div class="form-group">
        <label for="add_kode_unit" class="form-label">Kode Unit (Maks. 10 Karakter) *</label>
        <input type="text" id="add_kode_unit" name="kode_unit" class="form-control" placeholder="Contoh: P-BDH, IGD, ICU" required maxlength="10" style="text-transform:uppercase; font-family:var(--font-mono); font-weight:700;">
      </div>

      <div class="form-group">
        <label for="add_nama_unit" class="form-label">Nama Unit / Ruangan Pelayanan *</label>
        <input type="text" id="add_nama_unit" name="nama_unit" class="form-control" placeholder="Contoh: Poli Bedah Umum" required maxlength="100">
      </div>

      <div class="form-group" style="display:flex; align-items:center; gap:10px; margin-top:16px;">
        <input type="checkbox" id="add_is_active" name="is_active" value="1" checked style="width:18px; height:18px; accent-color:var(--primary); cursor:pointer;">
        <label for="add_is_active" style="font-size:0.9rem; font-weight:600; cursor:pointer; color:var(--text-primary);">Unit Aktif (Dapat meminjam berkas)</label>
      </div>

      <div style="display:flex; gap:12px; margin-top:24px;">
        <button type="submit" class="btn btn-primary" style="flex:1; justify-content:center;">Simpan Unit</button>
        <button type="button" class="btn btn-secondary" onclick="closeAddModal()" style="width:auto; padding:0 18px;">Batal</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Ubah Unit -->
<div id="modalEdit" class="modal-backdrop" style="display:none; position:fixed; inset:0; background:rgba(15,23,42,0.6); z-index:1000; align-items:center; justify-content:center; padding:20px; backdrop-filter:blur(6px);">
  <div class="card" style="width:100%; max-width:480px; padding:28px; animation:modalIn 0.25s cubic-bezier(0.16, 1, 0.3, 1); box-shadow:var(--shadow-xl);">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
      <h3 class="card-title" style="display:flex; align-items:center; gap:8px;">
        <span style="width:32px; height:32px; border-radius:8px; background:rgba(244,114,182,0.15); display:inline-flex; align-items:center; justify-content:center; color:var(--pink);">
          <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
        </span>
        Ubah Data Unit
      </h3>
      <button type="button" onclick="closeEditModal()" style="background:none; border:none; font-size:1.5rem; cursor:pointer; color:var(--text-muted); line-height:1;">&times;</button>
    </div>

    <form id="formEditUnit" method="post">
      <?= csrf_field() ?>
      <div class="form-group">
        <label for="edit_kode_unit" class="form-label">Kode Unit *</label>
        <input type="text" id="edit_kode_unit" name="kode_unit" class="form-control" required maxlength="10" style="text-transform:uppercase; font-family:var(--font-mono); font-weight:700;">
      </div>

      <div class="form-group">
        <label for="edit_nama_unit" class="form-label">Nama Unit / Ruangan *</label>
        <input type="text" id="edit_nama_unit" name="nama_unit" class="form-control" required maxlength="100">
      </div>

      <div class="form-group" style="display:flex; align-items:center; gap:10px; margin-top:16px;">
        <input type="checkbox" id="edit_is_active" name="is_active" value="1" style="width:18px; height:18px; accent-color:var(--primary); cursor:pointer;">
        <label for="edit_is_active" style="font-size:0.9rem; font-weight:600; cursor:pointer; color:var(--text-primary);">Unit Aktif</label>
      </div>

      <div style="display:flex; gap:12px; margin-top:24px;">
        <button type="submit" class="btn btn-primary" style="flex:1; justify-content:center;">Perbarui Unit</button>
        <button type="button" class="btn btn-secondary" onclick="closeEditModal()" style="width:auto; padding:0 18px;">Batal</button>
      </div>
    </form>
  </div>
</div>

<script>
function openAddModal() {
  document.getElementById('modalAdd').style.display = 'flex';
}
function closeAddModal() {
  document.getElementById('modalAdd').style.display = 'none';
}
function openEditModal(id, kode, nama, active) {
  document.getElementById('formEditUnit').action = '<?= base_url('unit/update') ?>/' + id;
  document.getElementById('edit_kode_unit').value = kode;
  document.getElementById('edit_nama_unit').value = nama;
  document.getElementById('edit_is_active').checked = active == 1;
  document.getElementById('modalEdit').style.display = 'flex';
}
function closeEditModal() {
  document.getElementById('modalEdit').style.display = 'none';
}
</script>
<?= $this->endSection() ?>

