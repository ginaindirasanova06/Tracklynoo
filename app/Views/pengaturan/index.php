<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div style="max-width:800px; margin:0 auto;">
  <div style="margin-bottom:28px;">
    <h2 style="font-size:1.5rem; font-weight:800; color:var(--text-primary); display:flex; align-items:center; gap:10px;">
      <span style="display:inline-flex; align-items:center; justify-content:center; width:38px; height:38px; border-radius:12px; background:linear-gradient(135deg, rgba(56,189,248,0.18), rgba(244,114,182,0.15)); color:var(--primary);">
        <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
      </span>
      Pengaturan Sistem TRACKLY
    </h2>
    <p style="font-size:0.875rem; color:var(--text-secondary); margin-top:4px;">
      Konfigurasi parameter sirkulasi peminjaman berkas, keamanan autentikasi, dan identitas instansi
    </p>
  </div>

  <div class="card" style="padding:32px;">
    <form action="<?= base_url('pengaturan') ?>" method="post">
      <?= csrf_field() ?>

      <!-- Section 1 -->
      <div style="border-bottom:1.5px solid var(--border); padding-bottom:14px; margin-bottom:24px; display:flex; align-items:center; gap:10px;">
        <span style="width:28px; height:28px; border-radius:8px; background:rgba(56,189,248,0.15); display:inline-flex; align-items:center; justify-content:center; color:var(--primary); font-weight:800; font-size:0.85rem;">1</span>
        <div>
          <h4 style="font-size:1.05rem; font-weight:800; color:var(--text-primary); margin:0;">Kebijakan Sirkulasi Berkas</h4>
          <span style="font-size:0.8rem; color:var(--text-secondary);">Parameter batas waktu peminjaman berkas rekam medis</span>
        </div>
      </div>

      <div class="form-group">
        <label for="durasi_standar_hari" class="form-label">Durasi Standar Peminjaman (Hari) *</label>
        <div style="display:flex; align-items:center; gap:12px;">
          <input type="number" id="durasi_standar_hari" name="durasi_standar_hari" class="form-control" style="max-width:140px; font-weight:800; font-size:1.15rem; font-family:var(--font-mono);"
                 value="<?= esc($settings['durasi_standar_hari']) ?>" min="1" max="30" required>
          <span style="font-size:0.9rem; font-weight:700; color:var(--text-primary);">Hari kalender</span>
        </div>
        <small style="color:var(--text-muted); font-size:0.78rem; display:block; margin-top:6px;">
          Nilai awal batas kembali pada form peminjaman otomatis dihitung: <code>Tanggal Pinjam + Durasi Standar</code>.
        </small>
      </div>

      <div class="form-group" style="margin-top:20px;">
        <label for="nama_instansi" class="form-label">Nama Fasilitas Pelayanan Kesehatan / Rumah Sakit *</label>
        <input type="text" id="nama_instansi" name="nama_instansi" class="form-control" value="<?= esc($settings['nama_instansi']) ?>" required maxlength="150" style="font-weight:600;">
        <small style="color:var(--text-muted); font-size:0.78rem; display:block; margin-top:6px;">
          Nama instansi akan dicetak pada kepala surat laporan dan label cetak QR Code.
        </small>
      </div>

      <!-- Section 2 -->
      <div style="border-bottom:1.5px solid var(--border); padding-bottom:14px; margin-bottom:24px; margin-top:36px; display:flex; align-items:center; gap:10px;">
        <span style="width:28px; height:28px; border-radius:8px; background:rgba(244,114,182,0.18); display:inline-flex; align-items:center; justify-content:center; color:var(--pink); font-weight:800; font-size:0.85rem;">2</span>
        <div>
          <h4 style="font-size:1.05rem; font-weight:800; color:var(--text-primary); margin:0;">Keamanan &amp; Verifikasi Dua Langkah (2FA OTP)</h4>
          <span style="font-size:0.8rem; color:var(--text-secondary);">Parameter keamanan akun dan perlindungan brute-force</span>
        </div>
      </div>

      <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
        <div class="form-group">
          <label for="maks_percobaan_otp" class="form-label">Maks. Percobaan OTP Salah *</label>
          <div style="display:flex; align-items:center; gap:10px;">
            <input type="number" id="maks_percobaan_otp" name="maks_percobaan_otp" class="form-control"
                   value="<?= esc($settings['maks_percobaan_otp']) ?>" min="1" max="5" required style="font-family:var(--font-mono); font-weight:700;">
            <span style="font-size:0.85rem; font-weight:600; color:var(--text-secondary);">Kali</span>
          </div>
          <small style="color:var(--text-muted); font-size:0.75rem; margin-top:4px; display:block;">OTP akan otomatis hangus setelah batas ini tercapai.</small>
        </div>

        <div class="form-group">
          <label for="masa_berlaku_otp_menit" class="form-label">Masa Berlaku OTP (Menit) *</label>
          <div style="display:flex; align-items:center; gap:10px;">
            <input type="number" id="masa_berlaku_otp_menit" name="masa_berlaku_otp_menit" class="form-control"
                   value="<?= esc($settings['masa_berlaku_otp_menit']) ?>" min="1" max="15" required style="font-family:var(--font-mono); font-weight:700;">
            <span style="font-size:0.85rem; font-weight:600; color:var(--text-secondary);">Menit</span>
          </div>
          <small style="color:var(--text-muted); font-size:0.75rem; margin-top:4px; display:block;">Batas waktu kedaluwarsa kode OTP sejak dikirim.</small>
        </div>
      </div>

      <div class="form-group" style="margin-top:14px;">
        <label for="waktu_penguncian_menit" class="form-label">Durasi Penguncian Akun (*Lockout*) *</label>
        <div style="display:flex; align-items:center; gap:10px;">
          <input type="number" id="waktu_penguncian_menit" name="waktu_penguncian_menit" class="form-control" style="max-width:140px; font-family:var(--font-mono); font-weight:700;"
                 value="<?= esc($settings['waktu_penguncian_menit']) ?>" min="5" max="60" required>
          <span style="font-size:0.85rem; font-weight:600; color:var(--text-secondary);">Menit</span>
        </div>
        <small style="color:var(--text-muted); font-size:0.75rem; display:block; margin-top:6px;">
          Lama waktu akun dikunci sementara jika terjadi 5 kali kegagalan memasukkan kata sandi berturut-turut.
        </small>
      </div>

      <div style="margin-top:36px; padding-top:20px; border-top:1px solid var(--border); display:flex; justify-content:flex-end;">
        <button type="submit" class="btn btn-primary" style="width:auto; padding:12px 28px;">
          <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
          Simpan Pengaturan Sistem
        </button>
      </div>
    </form>
  </div>
</div>
<?= $this->endSection() ?>

