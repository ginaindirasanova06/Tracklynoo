<?= $this->extend('layouts/auth') ?>

<?= $this->section('content') ?>
<form action="<?= base_url('lupa-sandi') ?>" method="post">
  <?= csrf_field() ?>

  <div class="form-group">
    <label for="username_or_email" class="form-label">Username atau Email Terdaftar</label>
    <div style="position:relative;">
      <input type="text" id="username_or_email" name="username_or_email" class="form-control" placeholder="Masukkan username atau alamat email" required autofocus style="padding-left:40px;">
      <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--text-muted); pointer-events:none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206"/></svg>
    </div>
    <small style="color:var(--text-muted); font-size:0.75rem; display:block; margin-top:6px;">
      Kode verifikasi 6 digit akan dikirimkan ke email yang terhubung dengan akun Anda.
    </small>
  </div>

  <div style="margin-top:24px;">
    <button type="submit" class="btn btn-primary" id="btn-forgot" style="width:100%; padding:12px 20px; font-size:0.95rem;">
      <span>Kirim Kode Verifikasi</span>
      <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
    </button>
  </div>
</form>

<div style="text-align:center; margin-top:24px; padding-top:16px; border-top:1px dashed var(--border);">
  <a href="<?= base_url('login') ?>" style="font-size:0.82rem; font-weight:700; color:var(--text-secondary);">
    &larr; Kembali ke halaman Login
  </a>
</div>
<?= $this->endSection() ?>
