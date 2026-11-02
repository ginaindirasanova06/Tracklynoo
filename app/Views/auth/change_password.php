<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div style="max-width:540px; margin:0 auto;">
  <div class="card">
    <div class="card-header">
      <div>
        <h3 class="card-title">Perbarui Kata Sandi</h3>
        <p style="font-size:0.85rem; color:var(--text-secondary); margin:0;">Ganti kata sandi akun Anda secara berkala untuk menjaga keamanan data rekam medis</p>
      </div>
    </div>

    <form action="<?= base_url('profil/ubah-sandi') ?>" method="post">
      <?= csrf_field() ?>

      <div class="form-group">
        <label for="sandi_lama" class="form-label">Kata Sandi Saat Ini</label>
        <div style="position:relative;">
          <input type="password" id="sandi_lama" name="sandi_lama" class="form-control" placeholder="Masukkan kata sandi lama / sementara" required autofocus style="padding-left:40px;">
          <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--text-muted); pointer-events:none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
        </div>
      </div>

      <div class="form-group">
        <label for="sandi_baru" class="form-label">Kata Sandi Baru</label>
        <div style="position:relative;">
          <input type="password" id="sandi_baru" name="sandi_baru" class="form-control" placeholder="Minimal 8 karakter kombinasi huruf & angka" required style="padding-left:40px;">
          <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--text-muted); pointer-events:none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
        </div>
      </div>

      <div class="form-group">
        <label for="konfirmasi_sandi" class="form-label">Konfirmasi Kata Sandi Baru</label>
        <div style="position:relative;">
          <input type="password" id="konfirmasi_sandi" name="konfirmasi_sandi" class="form-control" placeholder="Ulangi kata sandi baru" required style="padding-left:40px;">
          <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--text-muted); pointer-events:none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
      </div>

      <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:var(--radius-sm); padding:12px 16px; font-size:0.78rem; color:var(--text-secondary); margin-bottom:24px; line-height:1.6;">
        <strong style="color:var(--text-primary); display:block; margin-bottom:4px;">Ketentuan Keamanan Sandi:</strong>
        &bull; Minimal 8 karakter<br>
        &bull; Wajib memuat kombinasi huruf (A-Z, a-z) dan angka (0-9)<br>
        &bull; Tidak boleh sama persis dengan kata sandi saat ini
      </div>

      <div style="display:flex; gap:12px;">
        <button type="submit" class="btn btn-primary" id="btn-update-pass" style="flex:1;">
          Simpan Kata Sandi Baru
        </button>
        <a href="<?= base_url('dashboard') ?>" class="btn btn-secondary" style="width:auto;">
          Batal
        </a>
      </div>
    </form>
  </div>
</div>
<?= $this->endSection() ?>
