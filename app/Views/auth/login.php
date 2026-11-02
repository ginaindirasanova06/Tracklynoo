<?= $this->extend('layouts/auth') ?>

<?= $this->section('content') ?>
<form action="<?= base_url('login') ?>" method="post">
  <?= csrf_field() ?>

  <div class="form-group">
    <label for="username" class="form-label">Username atau Email</label>
    <div style="position:relative;">
      <input type="text" id="username" name="username" class="form-control" placeholder="Contoh: admin atau petugas_rm" value="<?= old('username') ?>" required autofocus style="padding-left:40px;">
      <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--text-muted); pointer-events:none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
    </div>
  </div>

  <div class="form-group">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
      <label for="password" class="form-label" style="margin-bottom:0;">Kata Sandi</label>
      <a href="<?= base_url('lupa-sandi') ?>" style="font-size:0.8rem; font-weight:700; color:var(--pastel-pink);">Lupa sandi?</a>
    </div>
    <div style="position:relative;">
      <input type="password" id="password" name="password" class="form-control" placeholder="Masukkan kata sandi akun" required style="padding-left:40px;">
      <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--text-muted); pointer-events:none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
    </div>
  </div>

  <div style="margin-top:24px;">
    <button type="submit" class="btn btn-primary" id="btn-login" style="width:100%; padding:12px 20px; font-size:0.95rem;">
      <span>Masuk ke TRACKLY</span>
      <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
    </button>
  </div>
</form>

<div style="margin-top:24px; padding-top:18px; border-top:1px dashed var(--border); font-size:0.8rem; color:var(--text-secondary);">
  <div style="font-weight:700; color:var(--text-primary); margin-bottom:6px; display:flex; align-items:center; gap:6px;">
    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    Akun Uji Coba:
  </div>
  <div style="display:grid; grid-template-columns:1fr 1fr; gap:6px; font-size:0.75rem;">
    <div style="background:#f8fafc; padding:6px 10px; border-radius:6px; border:1px solid #e2e8f0;">
      <strong>Admin:</strong> <code>admin</code>
    </div>
    <div style="background:#f8fafc; padding:6px 10px; border-radius:6px; border:1px solid #e2e8f0;">
      <strong>Petugas RM:</strong> <code>petugas_rm</code>
    </div>
    <div style="background:#f8fafc; padding:6px 10px; border-radius:6px; border:1px solid #e2e8f0;">
      <strong>Petugas Unit:</strong> <code>petugas_unit</code>
    </div>
    <div style="background:#f8fafc; padding:6px 10px; border-radius:6px; border:1px solid #e2e8f0;">
      <strong>Kepala RM:</strong> <code>kepala</code>
    </div>
  </div>
  <div style="font-size:0.72rem; color:var(--text-muted); margin-top:6px; text-align:center;">
    Kata sandi semua akun: <code>password123</code>
  </div>
</div>
<?= $this->endSection() ?>
