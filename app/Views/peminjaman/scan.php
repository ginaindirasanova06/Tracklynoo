<?php echo $this->extend('layouts/main'); ?>

<?php echo $this->section('content'); ?>

<!-- Page Header -->
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:28px; flex-wrap:wrap; gap:16px;">
  <div>
    <h2 style="font-size:1.5rem; font-weight:800; color:var(--text-primary);">
      <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="vertical-align:middle; margin-right:8px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
      Scanner Peminjaman Berkas
    </h2>
    <p style="font-size:0.875rem; color:var(--text-secondary); margin-top:4px;">
      Scan QR Code atau input manual No. RM untuk mencatat peminjaman
    </p>
  </div>
  <a href="<?= base_url('berkas') ?>" class="btn btn-secondary btn-sm" style="padding:10px 16px;">
    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
    Data Berkas
  </a>
</div>

<div style="display:grid; grid-template-columns:1fr 1fr; gap:24px; align-items:start;">

  <!-- LEFT: Scanner Panel -->
  <div>
    <div class="card" style="margin-bottom:20px;">
      <h3 class="card-title" style="margin-bottom:16px; display:flex; align-items:center; gap:8px;">
        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1" stroke-width="2"/><rect x="14" y="3" width="7" height="7" rx="1" stroke-width="2"/><rect x="3" y="14" width="7" height="7" rx="1" stroke-width="2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 14h.01M14 18h.01M18 14h.01M18 18h3"/></svg>
        Scan QR Code
      </h3>

      <!-- Camera Viewfinder -->
      <div id="camera-container" class="scanner-viewfinder" style="position:relative; width:100%; min-height:300px; max-width:100%; height:auto; overflow:hidden; border-radius:var(--radius-lg); background:#090d16; display:flex; align-items:center; justify-content:center;">
        <video id="camera-video" playsinline muted autoplay style="width:100%; height:100%; object-fit:cover; display:none;"></video>
        <canvas id="snapshot-canvas" style="display:none;"></canvas>

        <!-- Scan overlay target -->
        <div id="scanner-overlay" class="scanner-target-box" style="display:none; pointer-events:none;">
          <div style="position:absolute; top:0; left:0; width:36px; height:36px; border-top:3px solid #38bdf8; border-left:3px solid #38bdf8; border-radius:8px 0 0 0;"></div>
          <div style="position:absolute; top:0; right:0; width:36px; height:36px; border-top:3px solid #38bdf8; border-right:3px solid #38bdf8; border-radius:0 8px 0 0;"></div>
          <div style="position:absolute; bottom:0; left:0; width:36px; height:36px; border-bottom:3px solid #f472b6; border-left:3px solid #f472b6; border-radius:0 0 0 8px;"></div>
          <div style="position:absolute; bottom:0; right:0; width:36px; height:36px; border-bottom:3px solid #f472b6; border-right:3px solid #f472b6; border-radius:0 0 8px 0;"></div>
          <div class="scanner-laser-line"></div>
        </div>

        <!-- Shutter flash animation -->
        <div id="shutter-flash" style="position:absolute; inset:0; background:#fff; opacity:0; pointer-events:none; transition:opacity 0.15s; z-index:15;"></div>

        <!-- Camera status overlay -->
        <div id="cam-status" style="position:absolute; bottom:14px; left:0; right:0; text-align:center; z-index:20; pointer-events:none;">
          <span style="background:rgba(15,23,42,0.85); color:#fff; font-size:0.75rem; padding:6px 16px; border-radius:var(--radius-full); backdrop-filter:blur(6px); border:1px solid rgba(255,255,255,0.15);">
            Klik "Aktifkan Kamera" untuk mulai
          </span>
        </div>
      </div>

      <!-- Controls when camera is OFF -->
      <div id="cam-off-controls" style="display:flex; gap:10px; margin-top:14px; flex-wrap:wrap;">
        <button id="btn-start-cam" type="button" onclick="startCamera()" class="btn btn-primary" style="flex:1;">
          <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.069A1 1 0 0121 8.82v6.36a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
          Aktifkan Kamera
        </button>
        <label class="btn btn-secondary btn-sm" style="display:inline-flex; align-items:center; gap:6px; cursor:pointer; padding:10px 16px; margin:0;" title="Pilih foto/screenshot QR dari komputer atau HP">
          <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
          Upload Foto QR
          <input type="file" id="qr-file-input" accept="image/jpeg,image/png,image/webp" style="display:none;" onchange="scanQrFromFile(this)">
        </label>
      </div>

      <!-- Controls when camera is ACTIVE -->
      <div id="cam-on-controls" style="display:none; flex-direction:column; gap:10px; margin-top:14px;">
        <!-- Manual Snap Photo Button as requested -->
        <button id="btn-snap-photo" type="button" onclick="captureAndScan()" class="btn btn-primary btn-lg" style="width:100%; justify-content:center; font-weight:800; font-size:1rem; background:linear-gradient(135deg, #0284c7 0%, #ec4899 100%); box-shadow:0 6px 20px rgba(2,132,199,0.35); padding:13px 18px;">
          <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3" stroke-width="2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/></svg>
          📸 Ambil Foto &amp; Pindai QR Sekarang
        </button>

        <div style="display:flex; justify-content:space-between; align-items:center; gap:8px; flex-wrap:wrap;">
          <button id="btn-stop-cam" type="button" onclick="stopCamera()" class="btn btn-secondary btn-sm" style="padding:8px 14px;">
            Matikan Kamera
          </button>
          <label class="btn btn-secondary btn-sm" style="display:inline-flex; align-items:center; gap:6px; cursor:pointer; padding:8px 14px; margin:0;">
            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            Upload File
            <input type="file" id="qr-file-input-active" accept="image/jpeg,image/png,image/webp" style="display:none;" onchange="scanQrFromFile(this)">
          </label>
          <label style="display:flex; align-items:center; gap:6px; font-size:0.8rem; color:var(--text-secondary); cursor:pointer; margin-left:auto;">
            <input type="checkbox" id="auto-scan-toggle" checked onchange="toggleAutoScan(this.checked)">
            <span>Pindai Otomatis</span>
          </label>
        </div>

        <!-- Helpful Tips (Auto-shown if QR not detected after 5 seconds) -->
        <div id="cam-tips-banner" style="display:none; background:#eff6ff; border:1px solid #bfdbfe; border-radius:10px; padding:10px 14px; font-size:0.8rem; color:#1e40af; align-items:center; gap:8px; margin-top:4px;">
          <span style="font-size:1.15rem; flex-shrink:0;">💡</span>
          <div>
            <strong>Tips:</strong> Dekatkan posisi QR ke tengah kamera, kurangi pantulan cahaya layar, atau naikkan kecerahan layar HP.
          </div>
        </div>
      </div>
    </div>

    <!-- Manual Input -->
    <div class="card">
      <h3 class="card-title" style="margin-bottom:14px; font-size:1rem;">
        Input Manual No. RM
        <span style="font-size:0.75rem; font-weight:400; color:var(--text-secondary); margin-left:6px;">(jika QR Code tidak terbaca)</span>
      </h3>
      <div style="display:flex; gap:10px;">
        <input type="text" id="manual-rm-input" class="form-control" placeholder="Contoh: 00-12-34" style="font-family:var(--font-mono); font-weight:600; flex:1;">
        <button onclick="lookupByRm()" class="btn btn-primary btn-sm" style="width:auto; padding:10px 20px; white-space:nowrap;">
          Cari Berkas
        </button>
      </div>
      <p id="manual-error" style="color:#dc2626; font-size:0.82rem; margin-top:8px; display:none;"></p>
    </div>
  </div>

  <!-- RIGHT: Result & Form Panel -->
  <div>
    <!-- Result: Not Scanned Yet -->
    <div id="result-empty" class="card" style="text-align:center; padding:48px 32px;">
      <div style="width:80px; height:80px; background:var(--bg-secondary); border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 20px;">
        <svg width="36" height="36" fill="none" stroke="#94a3b8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
      </div>
      <h4 style="font-size:1.1rem; font-weight:700; color:var(--text-primary); margin-bottom:8px;">Menunggu Scan</h4>
      <p style="font-size:0.875rem; color:var(--text-secondary);">Arahkan kamera ke QR Code berkas atau input manual No. RM untuk melihat informasi berkas.</p>
    </div>

    <!-- Result: Berkas Found (Tersedia) -->
    <div id="result-tersedia" style="display:none;">
      <div class="card" style="border:2px solid #10b981; margin-bottom:16px;">
        <div style="display:flex; align-items:center; gap:10px; margin-bottom:16px;">
          <div style="width:40px; height:40px; background:#d1fae5; border-radius:50%; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
            <svg width="20" height="20" fill="#059669" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
          </div>
          <div>
            <div style="font-size:0.75rem; text-transform:uppercase; font-weight:700; color:#059669; letter-spacing:0.5px;">Berkas Tersedia</div>
            <div style="font-size:1rem; font-weight:800; color:var(--text-primary);" id="res-no-rm">—</div>
          </div>
          <span class="badge badge-tersedia" style="margin-left:auto;">TERSEDIA</span>
        </div>
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; padding-top:14px; border-top:1px solid var(--border);">
          <div>
            <div style="font-size:0.72rem; text-transform:uppercase; color:var(--text-secondary); font-weight:700;">Nama Pasien</div>
            <div style="font-size:0.95rem; font-weight:700; margin-top:2px;" id="res-nama">—</div>
          </div>
          <div>
            <div style="font-size:0.72rem; text-transform:uppercase; color:var(--text-secondary); font-weight:700;">Lokasi Rak</div>
            <div style="font-size:0.95rem; font-weight:700; font-family:var(--font-mono); margin-top:2px;" id="res-rak">—</div>
          </div>
        </div>
      </div>

      <!-- Loan Form -->
      <div class="card">
        <h3 class="card-title" style="margin-bottom:20px;">Form Peminjaman</h3>
        <form id="form-peminjaman" action="<?= base_url('peminjaman') ?>" method="post">
          <?= csrf_field() ?>
          <input type="hidden" name="berkas_id" id="form-berkas-id">

          <div class="form-group">
            <label for="loan-unit" class="form-label">Unit Peminjam *</label>
            <select id="loan-unit" name="unit_id" class="form-control" required>
              <option value="">— Pilih Unit —</option>
              <?php foreach ($units as $u): ?>
              <option value="<?= $u['id'] ?>"><?= esc($u['nama_unit']) ?> (<?= esc($u['kode_unit']) ?>)</option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group">
            <label for="loan-nama" class="form-label">Nama Petugas Peminjam *</label>
            <input type="text" id="loan-nama" name="nama_peminjam" class="form-control" placeholder="Masukkan nama petugas" required maxlength="100">
          </div>

          <div class="form-group">
            <label for="loan-tujuan" class="form-label">Tujuan Peminjaman *</label>
            <select id="loan-tujuan" name="tujuan" class="form-control" required>
              <option value="">— Pilih Tujuan —</option>
              <option value="Pelayanan pasien">Pelayanan Pasien</option>
              <option value="Kontrol/Poli">Kontrol / Poli</option>
              <option value="Klaim/Asuransi">Klaim / Asuransi</option>
              <option value="Audit/Akreditasi">Audit / Akreditasi</option>
              <option value="Penelitian/Pendidikan">Penelitian / Pendidikan</option>
              <option value="Lainnya">Lainnya</option>
            </select>
          </div>

          <div class="form-group">
            <label for="loan-batas" class="form-label">Batas Tanggal Kembali *</label>
            <input type="date" id="loan-batas" name="batas_kembali" class="form-control" required>
          </div>

          <button type="submit" class="btn btn-primary" style="margin-top:8px;">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            Simpan Peminjaman
          </button>
        </form>
      </div>
    </div>

    <!-- Result: Berkas Dipinjam (Rejected) -->
    <div id="result-dipinjam" style="display:none;">
      <div class="card" style="border:2px solid #f59e0b;">
        <div style="display:flex; align-items:center; gap:10px; margin-bottom:16px;">
          <div style="width:40px; height:40px; background:#fef3c7; border-radius:50%; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
            <svg width="20" height="20" fill="#d97706" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
          </div>
          <div>
            <div style="font-size:0.75rem; text-transform:uppercase; font-weight:700; color:#d97706;">Peminjaman Ditolak</div>
            <div style="font-size:1rem; font-weight:800; color:var(--text-primary);" id="rej-no-rm">—</div>
          </div>
          <span class="badge badge-dipinjam" id="rej-badge" style="margin-left:auto;">DIPINJAM</span>
        </div>
        <div style="background:#fffbeb; border:1px solid #fde68a; border-radius:8px; padding:14px; font-size:0.875rem; color:#92400e; margin-bottom:16px;">
          <strong>Berkas sedang dipinjam oleh:</strong><br>
          <span id="rej-unit">—</span> sejak <span id="rej-tgl">—</span><br>
          Batas kembali: <span id="rej-batas" style="font-weight:700;">—</span>
        </div>
        <button onclick="resetScan()" class="btn btn-secondary btn-sm" style="width:100%;">
          Scan Berkas Lain
        </button>
      </div>
    </div>

    <!-- Result: Error / Not Found -->
    <div id="result-error" style="display:none;">
      <div class="card" style="border:2px solid #ef4444; text-align:center; padding:32px;">
        <div style="width:56px; height:56px; background:#fee2e2; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 16px;">
          <svg width="24" height="24" fill="#dc2626" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
        </div>
        <p id="error-msg" style="color:#dc2626; font-weight:600; margin-bottom:16px;">QR Code tidak terdaftar.</p>
        <button onclick="resetScan()" class="btn btn-secondary btn-sm">Coba Lagi</button>
      </div>
    </div>
  </div>
</div>

<style>
@keyframes scanMove {
  0%, 100% { top: 10px; }
  50% { top: calc(100% - 12px); }
}
</style>

<div id="html5qr-hidden-container" style="display:none;"></div>

<script src="<?= base_url('assets/js/jsqr.min.js') ?>"></script>
<script src="<?= base_url('assets/js/html5-qrcode.min.js') ?>"></script>
<script>
const BASE_URL = '<?= base_url() ?>';
const CSRF_TOKEN = '<?= csrf_hash() ?>';
const DEFAULT_DURASI = <?= (int)($durasi ?? 2) ?>;

let videoStream = null;
let streamActive = false;
let scanLocked = false;
let autoScanEnabled = true; // Default aktif untuk pemindaian instan
let isScanningBusy = false;
let rafScanId = null;
let lastScanTimestamp = 0;
let lastScannedCode = '';
let consecutiveNoQrTicks = 0;

// ---------- 1. DIAGNOSA & DETEKTOR NATIVE ----------
let nativeDetector = null;
if ('BarcodeDetector' in window) {
  try {
    nativeDetector = new BarcodeDetector({ formats: ['qr_code'] });
    console.log('[Trackly QR] Native BarcodeDetector API aktif dan didukung oleh browser.');
  } catch (e) {
    console.warn('[Trackly QR] BarcodeDetector inisialisasi gagal:', e);
  }
} else {
  console.log('[Trackly QR] BarcodeDetector tidak didukung browser, menggunakan mesin hybrid jsQR + Html5Qrcode.');
}

// ---------- 2. CAMERA STREAM HANDLING & CONTINUOUS SCAN ----------
async function startCamera() {
  const btnStart = document.getElementById('btn-start-cam');
  btnStart.disabled = true;
  updateCamStatus('Mengakses kamera...', 'scanning');

  const video = document.getElementById('camera-video');

  const constraintsList = [
    {
      video: {
        facingMode: { ideal: "environment" },
        width: { ideal: 1920, min: 1280 },
        height: { ideal: 1080, min: 720 },
        advanced: [{ focusMode: "continuous" }]
      }
    },
    {
      video: {
        facingMode: { ideal: "environment" },
        width: { ideal: 1280 },
        height: { ideal: 720 }
      }
    },
    {
      video: { facingMode: "environment" }
    },
    {
      video: true
    }
  ];

  let stream = null;
  for (const c of constraintsList) {
    try {
      stream = await navigator.mediaDevices.getUserMedia(c);
      if (stream) break;
    } catch (err) {
      // Coba constraint berikutnya
    }
  }

  if (!stream) {
    updateCamStatus('Kamera tidak dapat diakses atau izin ditolak. Silakan gunakan Upload File atau Input Manual.', 'error');
    btnStart.disabled = false;
    return;
  }

  videoStream = stream;
  video.srcObject = stream;
  video.style.display = 'block';

  // Aktifkan continuous autofocus jika didukung oleh perangkat
  const track = stream.getVideoTracks()[0];
  if (track && track.applyConstraints) {
    try {
      await track.applyConstraints({
        advanced: [{ focusMode: 'continuous' }]
      });
    } catch (e) {}
  }

  video.onloadedmetadata = function() {
    video.play();
    streamActive = true;
    scanLocked = false;
    consecutiveNoQrTicks = 0;
    hideTips();

    document.getElementById('cam-off-controls').style.display = 'none';
    document.getElementById('cam-on-controls').style.display = 'flex';
    document.getElementById('scanner-overlay').style.display = 'block';

    const toggle = document.getElementById('auto-scan-toggle');
    if (toggle) toggle.checked = autoScanEnabled;

    updateCamStatus('● Kamera Aktif — Memindai...', 'scanning');

    // Jalankan loop scan requestAnimationFrame (~10-12 FPS)
    startScanningLoop();
  };
}

function stopCamera() {
  if (rafScanId) {
    cancelAnimationFrame(rafScanId);
    rafScanId = null;
  }
  if (videoStream) {
    videoStream.getTracks().forEach(track => track.stop());
    videoStream = null;
  }
  streamActive = false;
  scanLocked = false;
  hideTips();

  const video = document.getElementById('camera-video');
  video.style.display = 'none';
  video.srcObject = null;

  document.getElementById('cam-off-controls').style.display = 'flex';
  document.getElementById('btn-start-cam').disabled = false;
  document.getElementById('cam-on-controls').style.display = 'none';
  document.getElementById('scanner-overlay').style.display = 'none';
  updateCamStatus('Kamera dinonaktifkan', 'info');
}

function toggleAutoScan(enabled) {
  autoScanEnabled = enabled;
  console.log('[Trackly QR] Pindai Otomatis diatur ke:', enabled);
  if (enabled && streamActive && !rafScanId) {
    startScanningLoop();
  }
}

// Loop pemindaian berkelanjutan berbasis requestAnimationFrame (~10-12 FPS)
function startScanningLoop() {
  if (rafScanId) cancelAnimationFrame(rafScanId);
  let lastTime = 0;
  const FRAME_INTERVAL = 90; // ~11 FPS (efisien dan tidak membebani CPU)

  async function loop(time) {
    if (!streamActive) return;

    if (autoScanEnabled && !scanLocked && !isScanningBusy) {
      if (time - lastTime >= FRAME_INTERVAL) {
        lastTime = time;
        await scanLiveFrame();
      }
    }

    rafScanId = requestAnimationFrame(loop);
  }

  rafScanId = requestAnimationFrame(loop);
}

async function scanLiveFrame() {
  const video = document.getElementById('camera-video');
  if (!video || video.readyState < 2) return;

  const vw = video.videoWidth;
  const vh = video.videoHeight;
  if (!vw || !vh) return;

  isScanningBusy = true;

  try {
    // 1. Percobaan 1: Native BarcodeDetector langsung pada video (hardware accelerated, ~2ms)
    if (nativeDetector) {
      try {
        const barcodes = await nativeDetector.detect(video);
        if (barcodes && barcodes.length > 0 && barcodes[0].rawValue) {
          console.log('[Trackly QR] BarcodeDetector (Live Video) Sukses:', barcodes[0].rawValue);
          onQrScanned(barcodes[0].rawValue);
          return;
        }
      } catch (e) {}
    }

    // 2. Siapkan canvas snapshot frame penuh
    let canvas = document.getElementById('snapshot-canvas');
    if (!canvas) {
      canvas = document.createElement('canvas');
      canvas.id = 'snapshot-canvas';
      canvas.style.display = 'none';
      document.body.appendChild(canvas);
    }
    canvas.width = vw;
    canvas.height = vh;
    const ctx = canvas.getContext('2d');
    ctx.drawImage(video, 0, 0, vw, vh);

    // 3. Percobaan 2: jsQR pada seluruh frame (bukan hanya area kotak overlay)
    const fullImgData = ctx.getImageData(0, 0, vw, vh);
    let code = jsQR(fullImgData.data, vw, vh, { inversionAttempts: "both" });
    if (code && code.data) {
      console.log('[Trackly QR] jsQR (Live Full Frame) Sukses:', code.data);
      onQrScanned(code.data);
      return;
    }

    // 4. Percobaan 3: jsQR dengan Horizontal Mirror (sangat penting untuk webcam depan laptop)
    const mirCanvas = getTransformedCanvas(canvas, 0, true);
    const mirCtx = mirCanvas.getContext('2d');
    const mirData = mirCtx.getImageData(0, 0, mirCanvas.width, mirCanvas.height);
    let codeMir = jsQR(mirData.data, mirCanvas.width, mirCanvas.height, { inversionAttempts: "both" });
    if (codeMir && codeMir.data) {
      console.log('[Trackly QR] jsQR (Live Mirrored Frame) Sukses:', codeMir.data);
      onQrScanned(codeMir.data);
      return;
    }

    // 5. Percobaan 4: jsQR pada Crop Area Tengah
    const minDim = Math.min(vw, vh);
    const sz = Math.floor(minDim * 0.55);
    const cx = Math.floor((vw - sz) / 2);
    const cy = Math.floor((vh - sz) / 2);
    const centerData = ctx.getImageData(cx, cy, sz, sz);
    let codeCenter = jsQR(centerData.data, sz, sz, { inversionAttempts: "both" });
    if (codeCenter && codeCenter.data) {
      console.log('[Trackly QR] jsQR (Live Center Crop) Sukses:', codeCenter.data);
      onQrScanned(codeCenter.data);
      return;
    }

    // 6. Percobaan 5: jsQR pada Crop Atas-Tengah (khusus kartu berkas/layar HP dengan teks di bawah)
    const cyU = Math.max(0, cy - Math.floor(sz * 0.25));
    const upperData = ctx.getImageData(cx, cyU, sz, sz);
    let codeUpper = jsQR(upperData.data, sz, sz, { inversionAttempts: "both" });
    if (codeUpper && codeUpper.data) {
      console.log('[Trackly QR] jsQR (Live Upper Crop) Sukses:', codeUpper.data);
      onQrScanned(codeUpper.data);
      return;
    }

    // Tampilkan tips bantuan jika gagal terus selama 5 detik (~50 frame tick)
    consecutiveNoQrTicks++;
    if (consecutiveNoQrTicks > 50) {
      showTips();
    }

  } catch (err) {
    console.warn('[Trackly QR] Error saat live frame scan:', err);
  } finally {
    isScanningBusy = false;
  }
}

// ---------- 3. EXHAUSTIVE MULTI-STRATEGY DECODER ENGINE ----------
// Digunakan oleh tombol manual "Ambil Foto & Pindai" dan fitur "Upload File"
async function decodeAnyImage(source) {
  if (!source) return null;

  // 1. Jika input berupa File / Blob, coba langsung dengan Html5Qrcode.scanFile
  if (source instanceof File || source instanceof Blob) {
    try {
      const h5 = new Html5Qrcode("html5qr-hidden-container");
      const r = await h5.scanFile(source, false);
      if (r) {
        console.log('[Trackly QR] Html5Qrcode.scanFile Berhasil:', r);
        return r;
      }
    } catch (e) {}
  }

  // 2. Ubah source ke format Canvas resolusi tinggi
  let canvas;
  if (source instanceof HTMLCanvasElement) {
    canvas = source;
  } else if (source instanceof HTMLVideoElement) {
    canvas = document.createElement('canvas');
    canvas.width = source.videoWidth || 1280;
    canvas.height = source.videoHeight || 720;
    canvas.getContext('2d').drawImage(source, 0, 0, canvas.width, canvas.height);
  } else if (source instanceof HTMLImageElement) {
    canvas = document.createElement('canvas');
    canvas.width = source.naturalWidth || source.width;
    canvas.height = source.naturalHeight || source.height;
    canvas.getContext('2d').drawImage(source, 0, 0, canvas.width, canvas.height);
  } else if (source instanceof File || source instanceof Blob) {
    canvas = await fileToCanvas(source);
  }

  if (!canvas || !canvas.width || !canvas.height) return null;

  // 3. Jalankan BarcodeDetector jika didukung
  if (nativeDetector) {
    try {
      const barcodes = await nativeDetector.detect(canvas);
      if (barcodes && barcodes.length > 0 && barcodes[0].rawValue) {
        console.log('[Trackly QR] BarcodeDetector (Canvas) Berhasil:', barcodes[0].rawValue);
        return barcodes[0].rawValue;
      }
    } catch (e) {}
  }

  const w = canvas.width;
  const h = canvas.height;
  const minDim = Math.min(w, h);

  // 4. Kumpulan variasi pemotongan (Crops) dan pembesaran (Scaling):
  // (a) Frame penuh
  // (b) Crop tengah 75% & 55%
  // (c) Crop atas-tengah (Upper-Center)
  // (d) Versi di-scale 1.5x dan 2x
  const candidates = [
    { name: 'Frame Penuh', canvas: canvas }
  ];

  for (let ratio of [0.75, 0.55]) {
    const sz = Math.floor(minDim * ratio);
    const cx = Math.floor((w - sz) / 2);
    const cy = Math.floor((h - sz) / 2);
    const cc = document.createElement('canvas');
    cc.width = sz; cc.height = sz;
    cc.getContext('2d').drawImage(canvas, cx, cy, sz, sz, 0, 0, sz, sz);
    candidates.push({ name: `Crop Tengah ${Math.round(ratio * 100)}%`, canvas: cc });
  }

  // Crop Atas-Tengah
  const szU = Math.floor(minDim * 0.55);
  const cxU = Math.floor((w - szU) / 2);
  const cyU = Math.max(0, Math.floor((h - szU) / 2) - Math.floor(szU * 0.25));
  const cUpper = document.createElement('canvas');
  cUpper.width = szU; cUpper.height = szU;
  cUpper.getContext('2d').drawImage(canvas, cxU, cyU, szU, szU, 0, 0, szU, szU);
  candidates.push({ name: 'Crop Atas-Tengah', canvas: cUpper });

  // Versi di-scale 1.5x dan 2.0x (untuk QR kecil atau jarak jauh)
  for (let scale of [1.5, 2.0]) {
    const srcSz = Math.floor(minDim / scale);
    const srcX = Math.floor((w - srcSz) / 2);
    const srcY = Math.floor((h - srcSz) / 2);
    const dstSz = Math.floor(srcSz * scale);
    const cScale = document.createElement('canvas');
    cScale.width = dstSz; cScale.height = dstSz;
    cScale.getContext('2d').drawImage(canvas, srcX, srcY, srcSz, srcSz, 0, 0, dstSz, dstSz);
    candidates.push({ name: `Scale ${scale}x`, canvas: cScale });
  }

  // 5. Transformasi Orientasi & Cermin
  const transforms = [
    [0, false, 'Normal'],
    [0, true, 'Cermin Horizontal (Webcam Depan)'],
    [90, false, 'Rotasi 90°'],
    [90, true, 'Cermin + Rotasi 90°'],
    [180, false, 'Rotasi 180°'],
    [180, true, 'Cermin + Rotasi 180°'],
    [270, false, 'Rotasi 270°'],
    [270, true, 'Cermin + Rotasi 270°']
  ];

  // Eksekusi jsQR multi-percobaan
  for (const cand of candidates) {
    for (const [angle, mirror, desc] of transforms) {
      const tc = getTransformedCanvas(cand.canvas, angle, mirror);
      const tctx = tc.getContext('2d');
      const tdata = tctx.getImageData(0, 0, tc.width, tc.height);

      // (e) Inverted (attemptBoth / both)
      let c = jsQR(tdata.data, tc.width, tc.height, { inversionAttempts: "both" });
      if (c && c.data) {
        console.log(`[Trackly QR] jsQR Berhasil (${cand.name}, ${desc}):`, c.data);
        return c.data;
      }

      // (c) Grayscale + Peningkatan Kontras
      const boosted = boostContrast(tdata.data);
      let c2 = jsQR(boosted, tc.width, tc.height, { inversionAttempts: "both" });
      if (c2 && c2.data) {
        console.log(`[Trackly QR] jsQR Kontras Tinggi Berhasil (${cand.name}, ${desc}):`, c2.data);
        return c2.data;
      }
    }
  }

  // Fallback ke ZXing / Html5Qrcode
  try {
    const zres1 = await scanCanvasWithZXing(cUpper);
    if (zres1) {
      console.log('[Trackly QR] Fallback ZXing Berhasil (Crop Atas):', zres1);
      return zres1;
    }
    const zres2 = await scanCanvasWithZXing(candidates[1].canvas || canvas);
    if (zres2) {
      console.log('[Trackly QR] Fallback ZXing Berhasil (Crop Tengah):', zres2);
      return zres2;
    }
  } catch (e) {}

  console.log('[Trackly QR] Seluruh percobaan selesai: QR belum terbaca.');
  return null;
}

function getTransformedCanvas(src, angle, mirror) {
  const w = src.width, h = src.height;
  const out = document.createElement('canvas');
  out.width = (angle === 90 || angle === 270) ? h : w;
  out.height = (angle === 90 || angle === 270) ? w : h;
  const ctx = out.getContext('2d');
  ctx.save();
  ctx.translate(out.width / 2, out.height / 2);
  if (mirror) ctx.scale(-1, 1);
  if (angle !== 0) ctx.rotate((angle * Math.PI) / 180);
  ctx.drawImage(src, -w / 2, -h / 2);
  ctx.restore();
  return out;
}

function boostContrast(data) {
  const out = new Uint8ClampedArray(data.length);
  let min = 255, max = 0;
  for (let i = 0; i < data.length; i += 4) {
    const l = 0.299 * data[i] + 0.587 * data[i+1] + 0.114 * data[i+2];
    if (l < min) min = l;
    if (l > max) max = l;
  }
  const range = Math.max(1, max - min);
  for (let i = 0; i < data.length; i += 4) {
    const l = 0.299 * data[i] + 0.587 * data[i+1] + 0.114 * data[i+2];
    let v = ((l - min) / range) * 255;
    v = v < 128 ? Math.max(0, v - 40) : Math.min(255, v + 40);
    out[i] = v; out[i+1] = v; out[i+2] = v; out[i+3] = 255;
  }
  return out;
}

function fileToCanvas(file) {
  return new Promise((resolve) => {
    const reader = new FileReader();
    reader.onload = function(e) {
      const img = new Image();
      img.onload = function() {
        let iw = img.naturalWidth || img.width;
        let ih = img.naturalHeight || img.height;

        // Resize bila lebar/tinggi > 1600px (pertahankan rasio aspek)
        const MAX_DIM = 1600;
        if (iw > MAX_DIM || ih > MAX_DIM) {
          if (iw > ih) {
            ih = Math.round((ih * MAX_DIM) / iw);
            iw = MAX_DIM;
          } else {
            iw = Math.round((iw * MAX_DIM) / ih);
            ih = MAX_DIM;
          }
        }

        const c = document.createElement('canvas');
        c.width = iw;
        c.height = ih;
        const ctx = c.getContext('2d');
        ctx.drawImage(img, 0, 0, iw, ih);
        resolve(c);
      };
      img.onerror = () => resolve(null);
      img.src = e.target.result;
    };
    reader.onerror = () => resolve(null);
    reader.readAsDataURL(file);
  });
}

function scanCanvasWithZXing(canvas) {
  return new Promise((resolve) => {
    canvas.toBlob(async function(blob) {
      if (!blob) return resolve(null);
      const f = new File([blob], "t.png", { type: "image/png" });
      try {
        const h5 = new Html5Qrcode("html5qr-hidden-container");
        const res = await h5.scanFile(f, false);
        resolve(res);
      } catch (e) {
        resolve(null);
      }
    });
  });
}

// ---------- 4. MANUAL CAPTURE & SCAN (TOMBOL KLIK) ----------
async function captureAndScan(isSilent = false) {
  if (isScanningBusy || scanLocked) return;
  const video = document.getElementById('camera-video');
  if (!video || !streamActive || video.readyState < 2) {
    if (!isSilent) alert('Kamera belum siap. Mohon tunggu sesaat.');
    return;
  }

  isScanningBusy = true;
  const snapBtn = document.getElementById('btn-snap-photo');

  if (!isSilent) {
    if (snapBtn) {
      snapBtn.disabled = true;
      snapBtn.innerHTML = '⏳ Memindai QR Code...';
    }
    const flash = document.getElementById('shutter-flash');
    if (flash) {
      flash.style.opacity = '0.75';
      setTimeout(() => { flash.style.opacity = '0'; }, 120);
    }
    updateCamStatus('Memindai QR Code...', 'scanning');
  }

  try {
    const canvas = document.getElementById('snapshot-canvas');
    canvas.width = video.videoWidth || 1280;
    canvas.height = video.videoHeight || 720;
    const ctx = canvas.getContext('2d');
    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

    const result = await decodeAnyImage(canvas);
    if (result) {
      updateCamStatus('QR terdeteksi!', 'success');
      onQrScanned(result);
    } else if (!isSilent) {
      if (window.TracklyAudio) TracklyAudio.play('error');
      updateCamStatus('QR belum terbaca. Dekatkan posisi QR ke tengah kamera lalu klik lagi.', 'error');
      showTips();
      setTimeout(() => {
        if (streamActive && !scanLocked) {
          updateCamStatus('● Kamera Aktif — Klik "Ambil Foto & Pindai"', 'info');
        }
      }, 3500);
    }
  } finally {
    isScanningBusy = false;
    if (!isSilent && snapBtn) {
      snapBtn.disabled = false;
      snapBtn.innerHTML = '<svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3" stroke-width="2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/></svg> 📸 Ambil Foto &amp; Pindai QR Sekarang';
    }
  }
}

// ---------- 5. UPLOAD FILE GAMBAR QR ----------
async function scanQrFromFile(input) {
  if (!input.files || input.files.length === 0) return;
  const file = input.files[0];

  // Validasi tipe file (jpg, png, webp)
  const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
  if (!allowedTypes.includes(file.type) && !/\.(jpe?g|png|webp)$/i.test(file.name)) {
    if (window.TracklyAudio) TracklyAudio.play('error');
    alert('Format file tidak didukung. Silakan pilih file gambar JPG, PNG, atau WebP.');
    input.value = '';
    return;
  }

  // Validasi ukuran file (maksimal 5MB)
  const maxBytes = 5 * 1024 * 1024;
  if (file.size > maxBytes) {
    if (window.TracklyAudio) TracklyAudio.play('error');
    alert('Ukuran file gambar terlalu besar (' + (file.size / (1024*1024)).toFixed(1) + ' MB). Maksimal ukuran file adalah 5MB.');
    input.value = '';
    return;
  }

  updateCamStatus('Memproses file gambar QR...', 'scanning');

  try {
    const result = await decodeAnyImage(file);
    if (result) {
      updateCamStatus('QR terdeteksi pada file!', 'success');
      onQrScanned(result);
    } else {
      if (window.TracklyAudio) TracklyAudio.play('error');
      showError('QR tidak ditemukan di gambar, coba foto lebih dekat dan terang.');
      updateCamStatus('QR tidak terbaca pada file', 'error');
    }
  } catch (err) {
    if (window.TracklyAudio) TracklyAudio.play('error');
    showError('Gagal memproses gambar: ' + (err.message || 'Error'));
  } finally {
    input.value = '';
  }
}

// ---------- 6. NORMALISASI NO. RM & ALUR SCAN ----------
function extractNoRm(rawText) {
  if (!rawText) return '';
  let str = String(rawText).trim();

  // (a) JSON format: {"no_rm": "00-23-45"}
  if (str.startsWith('{') && str.endsWith('}')) {
    try {
      const obj = JSON.parse(str);
      if (obj.no_rm) return String(obj.no_rm).trim();
      if (obj.kode) return String(obj.kode).trim();
    } catch (e) {}
  }

  // (b) URL format: /scan/00-23-45 atau ?kode=... atau ?no_rm=...
  const urlMatch = str.match(/\/scan\/([^\/\?\#]+)/i) || str.match(/[?&](?:kode|no_rm)=([^&#]+)/i);
  if (urlMatch) {
    str = decodeURIComponent(urlMatch[1]).trim();
  }

  return str;
}

function onQrScanned(rawCode) {
  const norm = extractNoRm(rawCode);
  const now = Date.now();

  // Jeda (debounce) 2 detik agar QR yang sama tidak terbaca berulang
  if (norm === lastScannedCode && (now - lastScanTimestamp) < 2000) {
    return;
  }

  lastScannedCode = norm;
  lastScanTimestamp = now;
  scanLocked = true;
  hideTips();

  updateCamStatus('QR terdeteksi: ' + norm, 'success');
  handleQrResult(norm);
}

function handleQrResult(kode) {
  if (window.TracklyAudio) TracklyAudio.play('scan');

  fetch(`${BASE_URL}peminjaman/lookup-qr?kode=${encodeURIComponent(kode)}`)
    .then(r => r.json())
    .then(renderResult)
    .catch(() => {
      if (window.TracklyAudio) TracklyAudio.play('error');
      showError('Gagal menghubungi server. Periksa koneksi Anda.');
    });
}

function lookupByRm() {
  const noRm = document.getElementById('manual-rm-input').value.trim();
  if (!noRm) {
    document.getElementById('manual-error').textContent = 'Nomor RM tidak boleh kosong.';
    document.getElementById('manual-error').style.display = 'block';
    if (window.TracklyAudio) TracklyAudio.play('error');
    return;
  }
  document.getElementById('manual-error').style.display = 'none';

  fetch(`${BASE_URL}peminjaman/lookup-rm?no_rm=${encodeURIComponent(noRm)}`)
    .then(r => r.json())
    .then(renderResult)
    .catch(() => showError('Gagal menghubungi server. Coba lagi.'));
}
document.getElementById('manual-rm-input').addEventListener('keydown', e => { if (e.key === 'Enter') lookupByRm(); });

// ---------- 7. UI FEEDBACK & PANEL RENDER ----------
function renderResult(data) {
  hideAll();
  if (!data.found) {
    if (window.TracklyAudio) TracklyAudio.play('error');
    showError(data.error || 'Berkas tidak ditemukan.');
    return;
  }

  // Suara lonceng keberhasilan (menghormati toggle Suara Aktif)
  if (window.TracklyAudio) TracklyAudio.play('success');

  const b = data.berkas;
  if (b.status === 'tersedia') {
    document.getElementById('res-no-rm').textContent = 'No. RM ' + b.no_rm;
    document.getElementById('res-nama').textContent = b.nama_pasien;
    document.getElementById('res-rak').textContent = b.lokasi_rak;
    document.getElementById('form-berkas-id').value = b.id;

    // Batas tanggal pengembalian default
    const def = new Date();
    def.setDate(def.getDate() + DEFAULT_DURASI);
    document.getElementById('loan-batas').min = new Date().toISOString().split('T')[0];
    document.getElementById('loan-batas').value = def.toISOString().split('T')[0];
    document.getElementById('result-tersedia').style.display = 'block';
  } else {
    const loan = data.activeLoan;
    document.getElementById('rej-no-rm').textContent = 'No. RM ' + b.no_rm;
    document.getElementById('rej-unit').textContent = loan ? (loan.nama_unit + ' (' + loan.kode_unit + ')') : '—';
    document.getElementById('rej-tgl').textContent = loan ? formatDate(loan.tgl_pinjam) : '—';
    document.getElementById('rej-batas').textContent = loan ? formatDate(loan.batas_kembali) : '—';
    const isLate = loan && new Date(loan.batas_kembali) < new Date();
    document.getElementById('rej-badge').textContent = isLate ? 'TERLAMBAT' : 'DIPINJAM';
    document.getElementById('rej-badge').className = 'badge ' + (isLate ? 'badge-terlambat' : 'badge-dipinjam');
    document.getElementById('result-dipinjam').style.display = 'block';
  }
}

function showError(msg) {
  hideAll();
  document.getElementById('error-msg').textContent = msg;
  document.getElementById('result-error').style.display = 'block';
}

function hideAll() {
  ['result-empty','result-tersedia','result-dipinjam','result-error'].forEach(id => {
    document.getElementById(id).style.display = 'none';
  });
}

function resetScan() {
  scanLocked = false;
  consecutiveNoQrTicks = 0;
  hideTips();
  hideAll();
  document.getElementById('result-empty').style.display = 'block';
  document.getElementById('manual-rm-input').value = '';
  if (streamActive) {
    updateCamStatus('● Memindai...', 'scanning');
  }
}

function showTips() {
  const banner = document.getElementById('cam-tips-banner');
  if (banner && streamActive && !scanLocked) {
    banner.style.display = 'flex';
  }
}

function hideTips() {
  const banner = document.getElementById('cam-tips-banner');
  if (banner) {
    banner.style.display = 'none';
  }
}

function updateCamStatus(text, type = 'info') {
  const el = document.getElementById('cam-status');
  if (!el) return;

  let bg = 'rgba(15,23,42,0.88)';
  let extraStyle = '';
  if (type === 'success') {
    bg = 'rgba(16,185,129,0.95)';
  } else if (type === 'error') {
    bg = 'rgba(239,68,68,0.95)';
  } else if (type === 'scanning') {
    bg = 'rgba(2,132,199,0.95)';
    extraStyle = 'animation:pulse 1.2s infinite;';
  }

  el.innerHTML = `<span style="background:${bg}; color:#fff; font-size:0.75rem; padding:6px 16px; border-radius:var(--radius-full); backdrop-filter:blur(6px); font-weight:600; border:1px solid rgba(255,255,255,0.15); ${extraStyle}">${text}</span>`;
}

function formatDate(d) {
  if (!d) return '—';
  const dt = new Date(d.replace(' ', 'T'));
  return dt.toLocaleDateString('id-ID', {day:'2-digit', month:'short', year:'numeric'});
}
</script>

<?php echo $this->endSection(); ?>
