<?= $this->extend('layouts/auth') ?>

<?= $this->section('content') ?>
<div style="text-align:center; margin-bottom:18px;">
  <p style="font-size:0.88rem; color:var(--text-secondary); margin:0 0 4px;">
    Verifikasi Lupa Sandi untuk akun:<br>
    <strong style="color:var(--primary); font-size:1.02rem;"><?= esc($userEmail ?? $maskedEmail ?? 'Email Terdaftar') ?></strong>
  </p>
  <?php if (! empty($emailSent)): ?>
    <div style="font-size:0.75rem; color:#15803d; background:#dcfce7; border:1px solid #bbf7d0; border-radius:6px; padding:4px 10px; display:inline-block; margin-top:4px;">
      ✓ Kode OTP reset berhasil dikirim ke email Anda
    </div>
  <?php endif; ?>
</div>

<?php if (! empty($devOtpCode)): ?>
  <div class="dev-otp-banner" style="background:#f0f9ff; border:1px solid #bae6fd; border-radius:10px; padding:14px; margin-bottom:20px; text-align:center;">
    <div style="font-size:0.75rem; text-transform:uppercase; letter-spacing:0.8px; color:#0369a1; font-weight:700; margin-bottom:4px;">
      Kode OTP Reset Sandi:
    </div>
    <div style="display:flex; align-items:center; justify-content:center; gap:8px; margin:6px 0;">
      <span class="dev-otp-code" id="display-reset-otp-code" style="font-size:1.6rem; font-weight:800; font-family:monospace; color:#0284c7; letter-spacing:6px; background:#ffffff; padding:4px 16px; border-radius:8px; border:1px dashed #7dd3fc;">
        <?= esc($devOtpCode) ?>
      </span>
      <button type="button" onclick="fillOtp('<?= esc($devOtpCode) ?>')" class="btn btn-secondary btn-sm" style="font-size:0.75rem; padding:6px 10px; height:auto; background:#ffffff; border-color:#bae6fd; color:#0284c7;" title="Klik untuk langsung mengisi kode OTP">
        Isi Otomatis
      </button>
    </div>
    <div style="font-size:0.73rem; color:#64748b; margin-top:6px; line-height:1.4;">
      Salin atau masukkan 6 digit di atas ke kolom verifikasi.<br>
      <span style="font-size:0.70rem; color:#94a3b8;">(Pada server lokal XAMPP tanpa SMTP, kode disediakan di sini agar reset sandi dapat dijalankan)</span>
    </div>
  </div>
<?php endif; ?>

<form action="<?= base_url('lupa-sandi/otp') ?>" method="post" id="form-reset-otp">
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

  <button type="submit" class="btn btn-primary" id="btn-verify-reset-otp" style="width:100%; padding:12px 20px; font-size:0.95rem;">
    <span>Verifikasi & Lanjutkan</span>
    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
  </button>
</form>

<div style="text-align:center; margin-top:24px; padding-top:16px; border-top:1px dashed var(--border);">
  <a href="<?= base_url('login') ?>" style="font-size:0.82rem; font-weight:700; color:var(--text-secondary);">
    &larr; Batalkan & Kembali ke Login
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
