<?= $this->extend('layouts/auth') ?>

<?= $this->section('content') ?>
<div style="text-align:center; margin-bottom:18px;">
  <p style="font-size:0.88rem; color:var(--text-secondary); margin:0 0 4px;">
    Verifikasi keamanan 6 digit untuk login:<br>
    <strong style="color:var(--primary); font-size:1.02rem;"><?= esc($userEmail ?? $maskedEmail) ?></strong>
  </p>
  <?php if (! empty($emailSent)): ?>
    <div style="font-size:0.75rem; color:#15803d; background:#dcfce7; border:1px solid #bbf7d0; border-radius:6px; padding:4px 10px; display:inline-block; margin-top:4px;">
      ✓ Kode OTP berhasil dikirim ke email Anda
    </div>
  <?php endif; ?>
</div>

<?php if (! empty($devOtpCode)): ?>
  <div class="dev-otp-banner" style="background:#f0f9ff; border:1px solid #bae6fd; border-radius:10px; padding:14px; margin-bottom:20px; text-align:center;">
    <div style="font-size:0.75rem; text-transform:uppercase; letter-spacing:0.8px; color:#0369a1; font-weight:700; margin-bottom:4px;">
      Kode OTP Verifikasi Akun:
    </div>
    <div style="display:flex; align-items:center; justify-content:center; gap:8px; margin:6px 0;">
      <span class="dev-otp-code" id="display-otp-code" style="font-size:1.6rem; font-weight:800; font-family:monospace; color:#0284c7; letter-spacing:6px; background:#ffffff; padding:4px 16px; border-radius:8px; border:1px dashed #7dd3fc;">
        <?= esc($devOtpCode) ?>
      </span>
      <button type="button" onclick="fillOtp('<?= esc($devOtpCode) ?>')" class="btn btn-secondary btn-sm" style="font-size:0.75rem; padding:6px 10px; height:auto; background:#ffffff; border-color:#bae6fd; color:#0284c7;" title="Klik untuk langsung mengisi kode OTP">
        Isi Otomatis
      </button>
    </div>
    <div style="font-size:0.73rem; color:#64748b; margin-top:6px; line-height:1.4;">
      Masukkan 6 digit angka di atas pada kolom verifikasi.<br>
      <span style="font-size:0.70rem; color:#94a3b8;">(Pada XAMPP / server lokal tanpa konfigurasi SMTP email, kode disediakan langsung di atas agar login tidak terhambat)</span>
    </div>
  </div>
<?php endif; ?>

<form action="<?= base_url('login/otp') ?>" method="post" id="form-otp">
  <?= csrf_field() ?>

  <div class="otp-inputs">
    <input type="text" name="kode_otp[]" class="otp-box" maxlength="1" inputmode="numeric" pattern="[0-9]*" autocomplete="off" required>
    <input type="text" name="kode_otp[]" class="otp-box" maxlength="1" inputmode="numeric" pattern="[0-9]*" autocomplete="off" required>
    <input type="text" name="kode_otp[]" class="otp-box" maxlength="1" inputmode="numeric" pattern="[0-9]*" autocomplete="off" required>
    <input type="text" name="kode_otp[]" class="otp-box" maxlength="1" inputmode="numeric" pattern="[0-9]*" autocomplete="off" required>
    <input type="text" name="kode_otp[]" class="otp-box" maxlength="1" inputmode="numeric" pattern="[0-9]*" autocomplete="off" required>
    <input type="text" name="kode_otp[]" class="otp-box" maxlength="1" inputmode="numeric" pattern="[0-9]*" autocomplete="off" required>
  </div>

  <div style="text-align:center; font-size:0.82rem; color:var(--text-secondary); margin-bottom:22px;">
    Masa berlaku kode: <strong id="otp-timer" style="color:var(--text-primary); font-family:var(--font-mono); font-size:0.95rem;">05:00</strong>
  </div>

  <button type="submit" class="btn btn-primary" id="btn-verify-otp" style="width:100%; padding:12px 20px; font-size:0.95rem;">
    <span>Verifikasi & Masuk</span>
    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
  </button>
</form>

<div style="display:flex; justify-content:space-between; align-items:center; margin-top:24px; padding-top:16px; border-top:1px dashed var(--border);">
  <form action="<?= base_url('login/otp/kirim-ulang') ?>" method="post" style="margin:0;">
    <?= csrf_field() ?>
    <button type="submit" id="btn-resend-otp" class="btn btn-secondary btn-sm" disabled style="opacity:0.6; pointer-events:none; font-size:0.78rem;">
      Kirim ulang kode <span id="resend-countdown"> (60s)</span>
    </button>
  </form>

  <a href="<?= base_url('login') ?>" style="font-size:0.82rem; font-weight:700; color:var(--text-secondary);">
    &larr; Kembali ke Login
  </a>
</div>

<script>
function fillOtp(code) {
  const inputs = document.querySelectorAll('.otp-box');
  const digits = String(code).trim().split('');
  digits.forEach((d, i) => {
    if (inputs[i]) inputs[i].value = d;
  });
  if (inputs[5]) inputs[5].focus();
}
</script>
<?= $this->endSection() ?>
