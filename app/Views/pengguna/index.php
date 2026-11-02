<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; flex-wrap:wrap; gap:16px;">
  <div>
    <h2 style="font-size:1.5rem; font-weight:800; color:var(--text-primary); display:flex; align-items:center; gap:10px;">
      <span style="display:inline-flex; align-items:center; justify-content:center; width:38px; height:38px; border-radius:12px; background:linear-gradient(135deg, rgba(56,189,248,0.18), rgba(244,114,182,0.15)); color:var(--primary);">
        <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
      </span>
      Manajemen Akun Pengguna
    </h2>
    <p style="font-size:0.875rem; color:var(--text-secondary); margin-top:4px;">
      Kelola akun, peran akses, unit peminjam, dan pengaturan keamanan pengguna sistem
    </p>
  </div>
  <button type="button" class="btn btn-primary" onclick="openAddUserModal()" style="width:auto; padding:10px 18px;">
    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
    Tambah Pengguna Baru
  </button>
</div>

<!-- Admin Reset Password Success Banner -->
<?php if (session()->getFlashdata('success_reset_info')): ?>
  <?php $reset = session()->getFlashdata('success_reset_info'); ?>
  <div style="background:rgba(16,185,129,0.08); border:1.5px solid rgba(16,185,129,0.3); border-radius:16px; padding:20px 24px; margin-bottom:24px;">
    <div style="display:flex; align-items:center; gap:10px; font-weight:800; color:#065f46; font-size:1rem; margin-bottom:8px;">
      <svg width="22" height="22" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
      KATA SANDI BERHASIL DIRESET OLEH ADMIN
    </div>
    <p style="font-size:0.875rem; color:#047857; margin-bottom:12px;">
      Kata sandi sementara untuk pengguna <strong><?= esc($reset['nama']) ?> (<?= esc($reset['username']) ?>)</strong> telah diterbitkan. Berikan kata sandi ini kepada pengguna bersangkutan:
    </p>
    <div style="display:inline-flex; align-items:center; gap:14px; background:#ffffff; padding:10px 20px; border-radius:12px; border:1.5px solid #a7f3d0; box-shadow:var(--shadow-sm);">
      <span style="font-family:var(--font-mono); font-size:1.25rem; font-weight:800; color:#0f172a; letter-spacing:1px;"><?= esc($reset['password']) ?></span>
      <span style="font-size:0.75rem; color:#059669; font-weight:600;">(Wajib ganti sandi saat login)</span>
    </div>
  </div>
<?php endif; ?>

<!-- Search & Filter Card -->
<div class="card" style="margin-bottom:24px; padding:16px 20px;">
  <form method="get" action="<?= base_url('pengguna') ?>" style="display:flex; gap:12px; align-items:center; flex-wrap:wrap;">
    <div style="flex:1; min-width:220px;">
      <input type="text" name="cari" class="form-control" placeholder="Cari nama, username, atau email..." value="<?= esc($keyword ?? '') ?>">
    </div>
    <div style="width:170px;">
      <select name="role" class="form-control">
        <option value="all">Semua Peran</option>
        <option value="admin" <?= ($role === 'admin') ? 'selected' : '' ?>>Admin Sistem</option>
        <option value="petugas_rm" <?= ($role === 'petugas_rm') ? 'selected' : '' ?>>Petugas RM (Filing)</option>
        <option value="petugas_unit" <?= ($role === 'petugas_unit') ? 'selected' : '' ?>>Petugas Unit</option>
        <option value="kepala" <?= ($role === 'kepala') ? 'selected' : '' ?>>Kepala RM</option>
      </select>
    </div>
    <div style="width:150px;">
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
    <?php if (! empty($keyword) || (! empty($role) && $role !== 'all') || ($status !== null && $status !== '' && $status !== 'all')): ?>
      <a href="<?= base_url('pengguna') ?>" class="btn btn-secondary btn-sm" style="width:auto; padding:10px 14px; color:#ef4444;">
        Reset
      </a>
    <?php endif; ?>
  </form>
</div>

<!-- Table of Users -->
<div class="card" style="padding:0; overflow:hidden;">
  <div class="table-responsive">
    <table class="table">
      <thead>
        <tr>
          <th>Pengguna</th>
          <th>Username</th>
          <th>Peran</th>
          <th>Unit Asal</th>
          <th>Status Akun</th>
          <th style="text-align:right;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($users)): ?>
          <tr>
            <td colspan="6" style="text-align:center; padding:48px; color:var(--text-muted);">
              <div style="width:64px; height:64px; background:var(--bg-secondary); border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 12px;">
                <svg width="32" height="32" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="opacity:0.4;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
              </div>
              Tidak ada data pengguna yang ditemukan.
            </td>
          </tr>
        <?php else: ?>
          <?php foreach ($users as $u): ?>
            <?php
              $isLocked = ! empty($u['locked_until']) && strtotime($u['locked_until']) > time();
            ?>
            <tr>
              <td>
                <div style="font-weight:700; color:var(--text-primary); font-size:0.95rem;"><?= esc($u['nama']) ?></div>
                <div style="font-size:0.8rem; color:var(--text-secondary);"><?= esc($u['email']) ?></div>
              </td>
              <td>
                <code style="background:var(--bg-secondary); padding:4px 8px; border-radius:6px; font-weight:700; color:var(--primary); font-family:var(--font-mono); border:1px solid var(--border);">
                  <?= esc($u['username']) ?>
                </code>
              </td>
              <td>
                <span class="role-badge role-<?= esc($u['role']) ?>">
                  <?= esc(str_replace('_', ' ', $u['role'])) ?>
                </span>
              </td>
              <td>
                <?php if ($u['nama_unit']): ?>
                  <span style="font-size:0.85rem; font-weight:700; color:var(--text-primary);">
                    <?= esc($u['nama_unit']) ?> <span style="font-weight:400; color:var(--text-secondary);">(<?= esc($u['kode_unit']) ?>)</span>
                  </span>
                <?php else: ?>
                  <span style="color:var(--text-muted); font-size:0.85rem;">—</span>
                <?php endif; ?>
              </td>
              <td>
                <?php if ($isLocked): ?>
                  <div style="display:flex; align-items:center; gap:6px;">
                    <span class="badge badge-terlambat">TERKUNCI</span>
                    <form action="<?= base_url('pengguna/unlock/' . $u['id']) ?>" method="post" style="margin:0; display:inline;">
                      <?= csrf_field() ?>
                      <button type="submit" class="btn btn-secondary btn-sm" style="padding:2px 8px; font-size:0.7rem; color:#0284c7;" title="Buka Kunci Akun">Buka</button>
                    </form>
                  </div>
                <?php elseif ($u['is_active']): ?>
                  <span class="badge badge-tersedia">AKTIF</span>
                <?php else: ?>
                  <span class="badge" style="background:#f1f5f9; color:#64748b;">NONAKTIF</span>
                <?php endif; ?>

                <?php if ($u['must_change_password']): ?>
                  <span style="display:block; font-size:0.7rem; color:#d97706; margin-top:2px; font-weight:600;">Wajib ganti sandi</span>
                <?php endif; ?>
              </td>
              <td style="text-align:right;">
                <div style="display:inline-flex; gap:6px; align-items:center;">
                  <!-- Edit Button -->
                  <button type="button" class="btn btn-secondary btn-sm" style="padding:6px 10px; font-size:0.78rem;"
                          onclick="openEditUserModal(<?= $u['id'] ?>, '<?= esc($u['nama'], 'js') ?>', '<?= esc($u['email'], 'js') ?>', '<?= esc($u['role'], 'js') ?>', '<?= esc($u['unit_id'] ?? '', 'js') ?>', <?= $u['is_active'] ?>)">
                    Ubah
                  </button>

                  <!-- Admin Reset Password -->
                  <form action="<?= base_url('pengguna/reset-sandi/' . $u['id']) ?>" method="post" style="margin:0; display:inline;"
                        onsubmit="return confirm('Reset kata sandi pengguna <?= esc($u['nama'], 'js') ?>? Sandi sementara baru akan dibuat.');">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-secondary btn-sm" style="padding:6px 10px; font-size:0.78rem; color:var(--primary);" title="Reset Sandi ke Sandi Sementara">
                      Reset Sandi
                    </button>
                  </form>

                  <!-- Toggle Status (Except Self) -->
                  <?php if ($u['id'] != session()->get('user_id')): ?>
                    <form action="<?= base_url('pengguna/toggle/' . $u['id']) ?>" method="post" style="margin:0; display:inline;">
                      <?= csrf_field() ?>
                      <button type="submit" class="btn btn-secondary btn-sm" style="padding:6px 10px; font-size:0.78rem;" title="Ubah Status Aktif/Nonaktif">
                        <?= $u['is_active'] ? 'Nonaktifkan' : 'Aktifkan' ?>
                      </button>
                    </form>

                    <!-- Delete Button -->
                    <form action="<?= base_url('pengguna/delete/' . $u['id']) ?>" method="post" style="margin:0; display:inline;"
                          onsubmit="return confirm('Hapus akun pengguna <?= esc($u['nama'], 'js') ?>?');">
                      <?= csrf_field() ?>
                      <button type="submit" class="btn btn-secondary btn-sm" style="padding:6px 8px; color:#ef4444;" title="Hapus Pengguna">
                        <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                      </button>
                    </form>
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
      <?= $pager->links('users', 'default_full') ?>
    </div>
  <?php endif; ?>
</div>

<!-- Modal Tambah Pengguna -->
<div id="modalAddUser" class="modal-backdrop" style="display:none; position:fixed; inset:0; background:rgba(15,23,42,0.6); z-index:1000; align-items:center; justify-content:center; padding:20px; backdrop-filter:blur(6px);">
  <div class="card" style="width:100%; max-width:540px; padding:28px; animation:modalIn 0.25s cubic-bezier(0.16, 1, 0.3, 1); max-height:90vh; overflow-y:auto; box-shadow:var(--shadow-xl);">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
      <h3 class="card-title" style="display:flex; align-items:center; gap:8px;">
        <span style="width:32px; height:32px; border-radius:8px; background:rgba(56,189,248,0.15); display:inline-flex; align-items:center; justify-content:center; color:var(--primary);">
          <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
        </span>
        Tambah Pengguna Baru
      </h3>
      <button type="button" onclick="closeAddUserModal()" style="background:none; border:none; font-size:1.5rem; cursor:pointer; color:var(--text-muted); line-height:1;">&times;</button>
    </div>

    <form action="<?= base_url('pengguna') ?>" method="post">
      <?= csrf_field() ?>

      <div class="form-group">
        <label for="add_nama" class="form-label">Nama Lengkap &amp; Gelar *</label>
        <input type="text" id="add_nama" name="nama" class="form-control" placeholder="Contoh: dr. Budi Santoso, Sp.A / Ns. Sari" required maxlength="100">
      </div>

      <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
        <div class="form-group">
          <label for="add_username" class="form-label">Username Login *</label>
          <input type="text" id="add_username" name="username" class="form-control" placeholder="Contoh: budi_santoso" required maxlength="50" style="text-transform:lowercase; font-family:var(--font-mono);">
        </div>

        <div class="form-group">
          <label for="add_email" class="form-label">Alamat Email * (untuk OTP)</label>
          <input type="email" id="add_email" name="email" class="form-control" placeholder="budi@rsud.local" required maxlength="100">
        </div>
      </div>

      <div class="form-group">
        <label for="add_password" class="form-label">Kata Sandi Awal * (Min. 8 karakter huruf + angka)</label>
        <input type="password" id="add_password" name="password" class="form-control" placeholder="Minimal 8 karakter kombinasi huruf &amp; angka" required>
      </div>

      <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
        <div class="form-group">
          <label for="add_role" class="form-label">Peran Pengguna *</label>
          <select id="add_role" name="role" class="form-control" required onchange="handleRoleChange('add')">
            <option value="petugas_rm">Petugas RM (Filing)</option>
            <option value="petugas_unit">Petugas Unit / Peminjam</option>
            <option value="kepala">Kepala Rekam Medis</option>
            <option value="admin">Administrator Sistem</option>
          </select>
        </div>

        <div class="form-group" id="group_unit_add" style="display:none;">
          <label for="add_unit_id" class="form-label">Unit Asal Peminjam *</label>
          <select id="add_unit_id" name="unit_id" class="form-control">
            <option value="">Pilih Unit...</option>
            <?php foreach ($units as $unit): ?>
              <option value="<?= $unit['id'] ?>"><?= esc($unit['nama_unit']) ?> (<?= esc($unit['kode_unit']) ?>)</option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <div class="form-group" style="display:flex; align-items:center; gap:10px; margin-top:16px;">
        <input type="checkbox" id="add_is_active_user" name="is_active" value="1" checked style="width:18px; height:18px; accent-color:var(--primary); cursor:pointer;">
        <label for="add_is_active_user" style="font-size:0.9rem; font-weight:600; cursor:pointer; color:var(--text-primary);">Akun Aktif (Dapat masuk ke sistem)</label>
      </div>

      <div style="display:flex; gap:12px; margin-top:24px;">
        <button type="submit" class="btn btn-primary" style="flex:1; justify-content:center;">Simpan Pengguna</button>
        <button type="button" class="btn btn-secondary" onclick="closeAddUserModal()" style="width:auto; padding:0 18px;">Batal</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Ubah Pengguna -->
<div id="modalEditUser" class="modal-backdrop" style="display:none; position:fixed; inset:0; background:rgba(15,23,42,0.6); z-index:1000; align-items:center; justify-content:center; padding:20px; backdrop-filter:blur(6px);">
  <div class="card" style="width:100%; max-width:540px; padding:28px; animation:modalIn 0.25s cubic-bezier(0.16, 1, 0.3, 1); max-height:90vh; overflow-y:auto; box-shadow:var(--shadow-xl);">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
      <h3 class="card-title" style="display:flex; align-items:center; gap:8px;">
        <span style="width:32px; height:32px; border-radius:8px; background:rgba(244,114,182,0.15); display:inline-flex; align-items:center; justify-content:center; color:var(--pink);">
          <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
        </span>
        Ubah Data Pengguna
      </h3>
      <button type="button" onclick="closeEditUserModal()" style="background:none; border:none; font-size:1.5rem; cursor:pointer; color:var(--text-muted); line-height:1;">&times;</button>
    </div>

    <form id="formEditUser" method="post">
      <?= csrf_field() ?>

      <div class="form-group">
        <label for="edit_nama" class="form-label">Nama Lengkap &amp; Gelar *</label>
        <input type="text" id="edit_nama" name="nama" class="form-control" required maxlength="100">
      </div>

      <div class="form-group">
        <label for="edit_email" class="form-label">Alamat Email * (untuk OTP)</label>
        <input type="email" id="edit_email" name="email" class="form-control" required maxlength="100">
      </div>

      <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
        <div class="form-group">
          <label for="edit_role" class="form-label">Peran Pengguna *</label>
          <select id="edit_role" name="role" class="form-control" required onchange="handleRoleChange('edit')">
            <option value="petugas_rm">Petugas RM (Filing)</option>
            <option value="petugas_unit">Petugas Unit / Peminjam</option>
            <option value="kepala">Kepala Rekam Medis</option>
            <option value="admin">Administrator Sistem</option>
          </select>
        </div>

        <div class="form-group" id="group_unit_edit" style="display:none;">
          <label for="edit_unit_id" class="form-label">Unit Asal Peminjam *</label>
          <select id="edit_unit_id" name="unit_id" class="form-control">
            <option value="">Pilih Unit...</option>
            <?php foreach ($units as $unit): ?>
              <option value="<?= $unit['id'] ?>"><?= esc($unit['nama_unit']) ?> (<?= esc($unit['kode_unit']) ?>)</option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <div class="form-group" style="display:flex; align-items:center; gap:10px; margin-top:16px;">
        <input type="checkbox" id="edit_is_active_user" name="is_active" value="1" style="width:18px; height:18px; accent-color:var(--primary); cursor:pointer;">
        <label for="edit_is_active_user" style="font-size:0.9rem; font-weight:600; cursor:pointer; color:var(--text-primary);">Akun Aktif</label>
      </div>

      <div style="display:flex; gap:12px; margin-top:24px;">
        <button type="submit" class="btn btn-primary" style="flex:1; justify-content:center;">Perbarui Pengguna</button>
        <button type="button" class="btn btn-secondary" onclick="closeEditUserModal()" style="width:auto; padding:0 18px;">Batal</button>
      </div>
    </form>
  </div>
</div>

<script>
function handleRoleChange(type) {
  const roleSelect = document.getElementById(type + '_role');
  const unitGroup = document.getElementById('group_unit_' + type);
  if (roleSelect.value === 'petugas_unit') {
    unitGroup.style.display = 'block';
  } else {
    unitGroup.style.display = 'none';
  }
}

function openAddUserModal() {
  document.getElementById('modalAddUser').style.display = 'flex';
  handleRoleChange('add');
}
function closeAddUserModal() {
  document.getElementById('modalAddUser').style.display = 'none';
}

function openEditUserModal(id, nama, email, role, unitId, active) {
  document.getElementById('formEditUser').action = '<?= base_url('pengguna/update') ?>/' + id;
  document.getElementById('edit_nama').value = nama;
  document.getElementById('edit_email').value = email;
  document.getElementById('edit_role').value = role;
  document.getElementById('edit_unit_id').value = unitId || '';
  document.getElementById('edit_is_active_user').checked = active == 1;
  handleRoleChange('edit');
  document.getElementById('modalEditUser').style.display = 'flex';
}
function closeEditUserModal() {
  document.getElementById('modalEditUser').style.display = 'none';
}
</script>
<?= $this->endSection() ?>

