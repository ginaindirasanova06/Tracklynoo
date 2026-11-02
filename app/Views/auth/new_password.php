<?= $this->extend('layouts/auth') ?>

<?= $this->section('content') ?>
<form action="<?= base_url('reset-sandi') ?>" method="post">
  <?= csrf_field() ?>
  <input type="hidden" name="token" value="<?= esc($token) ?>">

  <div class="form-group">
    <label for="password_baru" class="form-label">Kata Sandi Baru</label>
    <div style="position:relative;">
      <input type="password" id="password_baru" name="password_baru" class="form-control" placeholder="Minimal 8 karakter (huruf & angka)" required autofocus style="padding-left:40px;">
      <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--text-muted); pointer-events:none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
    </div>
  </div>

  <div class="form-group">
    <label for="konfirmasi_password" class="form-label">Konfirmasi Kata Sandi Baru</label>
    <div style="position:relative;">
      <input type="password" id="konfirmasi_password" name="konfirmasi_password" class="form-control" placeholder="Ulangi kata sandi baru" required style="padding-left:40px;">
      <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--text-muted); pointer-events:none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
    </div>
  </div>

  <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:var(--radius-sm); padding:12px 14px; font-size:0.75rem; color:var(--text-secondary); margin-bottom:22px; line-height:1.6;">
    <div style="font-weight:700; color:var(--text-primary); margin-bottom:2px;">Ketentuan Keamanan Sandi:</div>
    &bull; Minimal 8 karakter<br>
    &bull; Mengandung perpaduan huruf dan angka<br>
    &bull; Tidak boleh sama dengan sandi sebelumnya
  </div>

  <button type="submit" class="btn btn-primary" id="btn-save-new-pass" style="width:100%; padding:12px 20px; font-size:0.95rem;">
    <span>Simpan Sandi Baru</span>
    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
  </button>
</form>
<?= $this->endSection() ?>
