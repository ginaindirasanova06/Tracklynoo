<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= esc($title) ?></title>
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: 'Segoe UI', Arial, sans-serif;
      background: #f1f5f9;
      color: #1e293b;
    }

    /* ====== SCREEN CONTROLS (hidden when printing) ====== */
    .print-controls {
      position: fixed;
      top: 0; left: 0; right: 0;
      background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
      color: #fff;
      padding: 14px 24px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 16px;
      z-index: 1000;
      box-shadow: 0 4px 16px rgba(15,23,42,0.25);
    }
    .print-controls h1 { font-size: 1.05rem; font-weight: 700; display: flex; align-items: center; gap: 8px; }
    .print-controls .meta { font-size: 0.82rem; color: #94a3b8; margin-top: 2px; }
    .print-controls .btn-group { display: flex; gap: 10px; align-items: center; }
    .btn-print {
      background: linear-gradient(135deg, #0ea5e9, #0284c7);
      color: white; border: none; padding: 10px 22px;
      border-radius: 8px; font-weight: 700; font-size: 0.9rem;
      cursor: pointer; display: flex; align-items: center; gap: 8px;
      box-shadow: 0 4px 12px rgba(14,165,233,0.35);
      transition: all 0.2s ease;
    }
    .btn-print:hover { transform: translateY(-1px); box-shadow: 0 6px 16px rgba(14,165,233,0.45); }
    .btn-back {
      background: rgba(255,255,255,0.1);
      color: white; border: 1px solid rgba(255,255,255,0.2);
      padding: 10px 18px; border-radius: 8px;
      font-weight: 600; font-size: 0.9rem;
      cursor: pointer; text-decoration: none;
      display: inline-flex; align-items: center; gap: 6px;
      transition: background 0.2s;
    }
    .btn-back:hover { background: rgba(255,255,255,0.18); }

    /* ====== LABELS GRID ====== */
    .labels-wrapper {
      margin-top: 76px;
      padding: 24px;
    }

    .labels-grid {
      display: flex;
      flex-wrap: wrap;
      gap: 16px;
      justify-content: flex-start;
    }

    /* ====== INDIVIDUAL LABEL ====== */
    .label-card {
      width: 125px;
      min-height: 145px;
      background: #ffffff;
      border: 1px solid #e2e8f0;
      border-radius: 10px;
      padding: 10px 8px;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 6px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.06);
      cursor: pointer;
      transition: all 0.2s ease;
      page-break-inside: avoid;
      break-inside: avoid;
      text-align: center;
    }
    .label-card:hover {
      box-shadow: 0 6px 16px rgba(14,165,233,0.18);
      border-color: #38bdf8;
    }
    .label-card.selected {
      border: 2px solid #0284c7;
      box-shadow: 0 0 0 3px rgba(2,132,199,0.25);
    }

    .label-qr-area {
      width: 96px;
      height: 96px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto;
      background: #ffffff;
      border-radius: 6px;
      overflow: hidden;
    }
    .label-qr-area canvas,
    .label-qr-area img {
      width: 96px !important;
      height: 96px !important;
      max-width: 100% !important;
      max-height: 100% !important;
      display: block !important;
      margin: 0 auto !important;
      image-rendering: pixelated;
      image-rendering: crisp-edges;
    }

    .label-no-rm {
      font-size: 9pt;
      font-weight: 800;
      font-family: 'Courier New', monospace, Consolas;
      letter-spacing: 0.5px;
      color: #0f172a;
      word-break: break-all;
      line-height: 1.2;
    }

    .label-nama {
      font-size: 6.5pt;
      font-weight: 600;
      color: #334155;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      max-width: 100%;
      line-height: 1.2;
    }

    .label-lokasi {
      font-size: 5.5pt;
      color: #64748b;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      max-width: 100%;
    }

    .trackly-badge {
      font-size: 5.5pt;
      text-transform: uppercase;
      letter-spacing: 0.8px;
      color: #94a3b8;
      border-top: 1px solid #f1f5f9;
      padding-top: 4px;
      width: 100%;
      text-align: center;
      font-weight: 700;
    }

    /* ====== PRINT STYLES ====== */
    @media print {
      @page {
        margin: 8mm;
        size: auto;
      }

      body {
        background: #ffffff !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
        color-adjust: exact !important;
      }

      .print-controls {
        display: none !important;
      }

      .labels-wrapper {
        margin-top: 0 !important;
        padding: 0 !important;
      }

      .labels-grid {
        gap: 5mm !important;
        display: flex !important;
        flex-wrap: wrap !important;
        justify-content: flex-start !important;
      }

      .label-card {
        width: 36mm !important;
        min-height: 48mm !important;
        padding: 2.5mm 2mm !important;
        box-shadow: none !important;
        border: 0.8pt solid #64748b !important;
        border-radius: 2mm !important;
        page-break-inside: avoid !important;
        break-inside: avoid !important;
        cursor: default !important;
        background: #ffffff !important;
      }

      .label-card:hover { box-shadow: none !important; }
      .label-card.selected { border: 0.8pt solid #64748b !important; box-shadow: none !important; }

      .label-qr-area {
        width: 30mm !important;
        height: 30mm !important;
        padding: 2mm !important; /* Quiet zone (margin putih minimal 4 modul) */
        box-sizing: border-box !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        margin: 0 auto !important;
        background: #ffffff !important;
      }
      .label-qr-area canvas,
      .label-qr-area img {
        width: 26mm !important;
        height: 26mm !important;
        display: block !important;
        margin: 0 auto !important;
        image-rendering: pixelated !important;
      }
      .label-no-rm {
        font-size: 8pt !important;
        font-weight: 800 !important;
        color: #000000 !important;
        margin-top: 1mm !important;
        line-height: 1.2 !important;
      }
      .label-nama {
        font-size: 5.5pt !important;
        font-weight: 600 !important;
        color: #1e293b !important;
        line-height: 1.2 !important;
      }
      .label-lokasi {
        font-size: 4.8pt !important;
        color: #475569 !important;
      }
      .trackly-badge {
        font-size: 4.8pt !important;
        color: #64748b !important;
        border-top: 0.4pt solid #cbd5e1 !important;
        padding-top: 1mm !important;
      }

      .label-count-badge { display: none !important; }
    }
  </style>
</head>
<body>

<!-- Print Controls (screen only) -->
<div class="print-controls">
  <div>
    <h1>
      <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
      Cetak Label QR Code Rekam Medis
    </h1>
    <div class="meta">
      <?= count($berkas) ?> label siap dicetak &bull; QR Code terverifikasi untuk scanner peminjaman &amp; pengembalian
    </div>
  </div>
  <div class="btn-group">
    <a href="javascript:history.back()" class="btn-back">
      <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
      Kembali
    </a>
    <button onclick="doPrint()" class="btn-print">
      <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
      Cetak Sekarang
    </button>
  </div>
</div>

<!-- Labels Grid -->
<div class="labels-wrapper">
  <div class="labels-grid" id="labels-grid">
    <?php foreach ($berkas as $b): ?>
    <div class="label-card" id="label-<?= $b['id'] ?>" onclick="toggleSelect(<?= $b['id'] ?>)">
      <div class="label-qr-area" id="qr-<?= $b['id'] ?>"></div>
      <div class="label-no-rm"><?= esc($b['no_rm']) ?></div>
      <div class="label-nama"><?= esc(mb_substr($b['nama_pasien'], 0, 20)) ?><?= mb_strlen($b['nama_pasien']) > 20 ? '…' : '' ?></div>
      <?php if (!empty($b['lokasi_rak'])): ?>
      <div class="label-lokasi">Rak: <?= esc($b['lokasi_rak']) ?></div>
      <?php endif; ?>
      <div class="trackly-badge">TRACKLY &bull; <?= esc($b['tahun_buka'] ?? date('Y')) ?></div>
    </div>
    <?php endforeach; ?>
  </div>
</div>

<!-- Local Bulletproof QR Code Generator -->
<script src="<?= base_url('assets/js/qrcode.min.js') ?>"></script>
<script>
const BASE_URL = '<?= base_url() ?>';
const berkasData = <?= json_encode(array_map(static function($b) {
    return [
        'id'       => (int) $b['id'],
        'no_rm'    => (string) $b['no_rm'],
        'kode_qr'  => (string) $b['kode_qr'],
    ];
}, $berkas)) ?>;

// Generate QR codes for each medical record label
berkasData.forEach(function(b) {
  const container = document.getElementById('qr-' + b.id);
  if (!container) return;

  // Use No. RM for ultra-sharp, large-module (21x21 Version 1) QR codes
  // Ultra-high legibility on laptop webcams, phone screens, and physical printouts
  const qrContent = b.no_rm || b.kode_qr;

  try {
    new QRCode(container, {
      text: qrContent,
      width: 140,
      height: 140,
      colorDark: '#0f172a',
      colorLight: '#ffffff',
      correctLevel: QRCode.CorrectLevel.M
    });
  } catch (err) {
    console.error('Gagal generate QR Code untuk No. RM ' + b.no_rm, err);
  }
});

function doPrint() {
  // Brief delay to ensure all canvas to image conversions are fully rendered
  setTimeout(function() {
    window.print();
  }, 100);
}

function toggleSelect(id) {
  const card = document.getElementById('label-' + id);
  if (card) card.classList.toggle('selected');
}
</script>

</body>
</html>
