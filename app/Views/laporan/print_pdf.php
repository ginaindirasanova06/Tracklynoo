<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= esc($title) ?></title>
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: 'Times New Roman', Times, serif;
      background: #f8fafc;
      color: #0f172a;
      line-height: 1.4;
      font-size: 11pt;
    }

    /* Screen Action Bar */
    .screen-bar {
      position: fixed;
      top: 0; left: 0; right: 0;
      background: #0f172a;
      color: #fff;
      padding: 12px 24px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 2px 10px rgba(0,0,0,0.25);
      z-index: 1000;
      font-family: 'Segoe UI', Arial, sans-serif;
    }
    .btn-action {
      background: #0284c7;
      color: #fff;
      border: none;
      padding: 8px 18px;
      border-radius: 6px;
      font-weight: 600;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      font-size: 0.9rem;
      text-decoration: none;
    }
    .btn-action:hover { background: #0369a1; }
    .btn-secondary {
      background: rgba(255,255,255,0.15);
      border: 1px solid rgba(255,255,255,0.25);
    }
    .btn-secondary:hover { background: rgba(255,255,255,0.25); }

    /* Paper Container */
    .paper {
      max-width: 1050px;
      margin: 70px auto 40px auto;
      background: #fff;
      padding: 36px 48px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.08);
      border-radius: 4px;
    }

    /* Official Hospital Header (Kop Surat) */
    .kop-header {
      display: flex;
      align-items: center;
      gap: 20px;
      padding-bottom: 14px;
      border-bottom: 3px double #0f172a;
      margin-bottom: 20px;
    }
    .kop-logo {
      width: 72px;
      height: 72px;
      border: 2px solid #0284c7;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'Segoe UI', Arial, sans-serif;
      font-weight: 900;
      font-size: 18pt;
      color: #0284c7;
      letter-spacing: -1px;
      flex-shrink: 0;
    }
    .kop-text {
      flex: 1;
      text-align: center;
    }
    .kop-text h2 {
      font-size: 15pt;
      font-weight: 900;
      letter-spacing: 0.8px;
      text-transform: uppercase;
      color: #0f172a;
      margin-bottom: 2px;
    }
    .kop-text h3 {
      font-size: 13pt;
      font-weight: 700;
      text-transform: uppercase;
      color: #0284c7;
      margin-bottom: 3px;
    }
    .kop-text p {
      font-size: 9.5pt;
      color: #334155;
      margin: 0;
    }

    /* Report Title */
    .report-title-box {
      text-align: center;
      margin-bottom: 24px;
    }
    .report-title {
      font-size: 14pt;
      font-weight: 800;
      text-decoration: underline;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      margin-bottom: 4px;
    }
    .report-subtitle {
      font-size: 10pt;
      font-style: italic;
      color: #475569;
    }

    /* Metadata Info */
    .meta-table {
      width: 100%;
      margin-bottom: 16px;
      font-size: 10pt;
      border-collapse: collapse;
    }
    .meta-table td {
      padding: 3px 6px;
      vertical-align: top;
    }

    /* KPI Summary in PDF */
    .summary-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 12px;
      margin-bottom: 20px;
    }
    .summary-box {
      border: 1px solid #cbd5e1;
      background: #f8fafc;
      padding: 8px 12px;
      border-radius: 4px;
      text-align: center;
    }
    .summary-box .val {
      font-size: 14pt;
      font-weight: 800;
      color: #0284c7;
    }
    .summary-box .lbl {
      font-size: 8.5pt;
      text-transform: uppercase;
      font-weight: 700;
      color: #475569;
    }

    /* Transaction Table */
    .data-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 32px;
      font-size: 9pt;
    }
    .data-table th, .data-table td {
      border: 1px solid #64748b;
      padding: 6px 8px;
    }
    .data-table th {
      background: #f1f5f9;
      font-weight: 800;
      text-align: center;
      text-transform: uppercase;
      font-size: 8.5pt;
    }

    /* Signature Section */
    .sign-section {
      display: flex;
      justify-content: space-between;
      margin-top: 24px;
      page-break-inside: avoid;
      font-size: 10pt;
    }
    .sign-box {
      width: 250px;
      text-align: center;
    }

    /* Print Styles */
    @media print {
      body { background: #fff; font-size: 9.5pt; }
      .screen-bar { display: none !important; }
      .paper {
        margin: 0 !important;
        padding: 0 !important;
        box-shadow: none !important;
        max-width: 100% !important;
      }
      @page {
        size: A4 landscape;
        margin: 10mm 12mm 10mm 12mm;
      }
    }
  </style>
</head>
<body>

<!-- Screen Action Bar -->
<div class="screen-bar">
  <div style="font-weight:700; font-size:1rem; display:flex; align-items:center; gap:8px;">
    <span>TRACKLY &bull; Pratinjau Cetak Laporan Peminjaman</span>
  </div>
  <div style="display:flex; gap:10px;">
    <button onclick="window.print()" class="btn-action">
      <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
      Cetak / Simpan PDF
    </button>
    <button onclick="window.close()" class="btn-action btn-secondary">
      Tutup
    </button>
  </div>
</div>

<div class="paper">
  <!-- Kop Surat -->
  <div class="kop-header">
    <div class="kop-logo">TRC</div>
    <div class="kop-text">
      <h2>RUMAH SAKIT UMUM DAERAH SEHAT SEJAHTERA</h2>
      <h3>INSTALASI REKAM MEDIS & INFORMASI KESEHATAN</h3>
      <p>Jl. Kesehatan No. 10, Jakarta Selatan &bull; Telp: (021) 7891011 &bull; Email: rekam.medis@rsud-sehat.go.id</p>
    </div>
  </div>

  <!-- Title Box -->
  <div class="report-title-box">
    <div class="report-title">LAPORAN PEMINJAMAN & SIRKULASI BERKAS REKAM MEDIS</div>
    <div class="report-subtitle">Sistem Pelacakan Berkas Rekam Medis Berbasis QR Code (TRACKLY)</div>
  </div>

  <!-- Metadata -->
  <table class="meta-table">
    <tr>
      <td style="width:15%; font-weight:700;">Periode Laporan</td>
      <td style="width:35%;">: <?= date('d/m/Y', strtotime($filters['tgl_mulai'])) ?> s/d <?= date('d/m/Y', strtotime($filters['tgl_selesai'])) ?></td>
      <td style="width:15%; font-weight:700;">Tanggal Cetak</td>
      <td style="width:35%;">: <?= date('d F Y, H:i') ?> WIB</td>
    </tr>
    <tr>
      <td style="font-weight:700;">Unit Filter</td>
      <td>: <?= esc($unitNama) ?></td>
      <td style="font-weight:700;">Petugas Pelapor</td>
      <td>: <?= esc(session()->get('nama')) ?> (<?= esc(session()->get('username')) ?>)</td>
    </tr>
    <tr>
      <td style="font-weight:700;">Status Transaksi</td>
      <td>: <?= !empty($filters['status']) ? strtoupper($filters['status']) : 'Semua Status' ?></td>
      <td style="font-weight:700;">Kondisi Berkas</td>
      <td>: <?= !empty($filters['kondisi_kembali']) ? ucfirst($filters['kondisi_kembali']) : 'Semua Kondisi' ?></td>
    </tr>
  </table>

  <!-- KPI Metrics -->
  <div class="summary-grid">
    <div class="summary-box">
      <div class="val"><?= number_format($metrics['total']) ?></div>
      <div class="lbl">Total Transaksi</div>
    </div>
    <div class="summary-box">
      <div class="val" style="color:#10b981;"><?= number_format($metrics['dikembalikan']) ?></div>
      <div class="lbl">Sudah Dikembalikan</div>
    </div>
    <div class="summary-box">
      <div class="val" style="color:<?= $metrics['terlambat'] > 0 ? '#ef4444' : '#0284c7' ?>;">
        <?= number_format($metrics['dipinjam'] + $metrics['terlambat']) ?>
      </div>
      <div class="lbl">Belum Kembali (<?= $metrics['terlambat'] ?> Terlambat)</div>
    </div>
    <div class="summary-box">
      <div class="val" style="color:#f59e0b;"><?= $metrics['persen_tepat_waktu'] ?>%</div>
      <div class="lbl">Kepatuhan Waktu</div>
    </div>
  </div>

  <!-- Data Table -->
  <table class="data-table">
    <thead>
      <tr>
        <th style="width:30px;">No.</th>
        <th style="width:75px;">No. RM</th>
        <th>Nama Pasien</th>
        <th>Unit Peminjam</th>
        <th>Peminjam</th>
        <th>Tujuan</th>
        <th style="width:90px;">Tgl Pinjam</th>
        <th style="width:80px;">Batas Kembali</th>
        <th style="width:90px;">Tgl Kembali</th>
        <th style="width:70px;">Kondisi</th>
        <th style="width:75px;">Status</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($loans)): ?>
        <tr>
          <td colspan="11" style="text-align:center; padding:20px; font-style:italic;">
            Tidak ada transaksi peminjaman yang sesuai dengan filter periode ini.
          </td>
        </tr>
      <?php else: ?>
        <?php $no = 1; foreach ($loans as $row): ?>
          <tr>
            <td style="text-align:center;"><?= $no++ ?></td>
            <td style="font-family:'Courier New', monospace; font-weight:700; text-align:center;"><?= esc($row['no_rm']) ?></td>
            <td><?= esc($row['nama_pasien']) ?></td>
            <td><?= esc($row['nama_unit']) ?></td>
            <td><?= esc($row['nama_peminjam']) ?></td>
            <td><?= esc($row['tujuan']) ?></td>
            <td style="text-align:center;"><?= date('d/m/Y H:i', strtotime($row['tgl_pinjam'])) ?></td>
            <td style="text-align:center;"><?= date('d/m/Y', strtotime($row['batas_kembali'])) ?></td>
            <td style="text-align:center;"><?= $row['tgl_kembali'] ? date('d/m/Y H:i', strtotime($row['tgl_kembali'])) : '-' ?></td>
            <td style="text-align:center;">
              <?= $row['kondisi_kembali'] ? ($row['kondisi_kembali'] === 'lengkap' ? 'Lengkap' : 'Tidak Lengkap') : '-' ?>
            </td>
            <td style="text-align:center; font-weight:700;">
              <?= strtoupper($row['status']) ?>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>

  <!-- Signature -->
  <div class="sign-section">
    <div class="sign-box">
      <div>Petugas Pengelola Rekam Medis,</div>
      <div style="height:60px;"></div>
      <div style="font-weight:700; text-decoration:underline;"><?= esc(session()->get('nama')) ?></div>
      <div style="font-size:9pt; color:#475569;">NIP. 19890412 201402 2 003</div>
    </div>
    <div class="sign-box">
      <div>Mengetahui,<br>Kepala Instalasi Rekam Medis</div>
      <div style="height:60px;"></div>
      <div style="font-weight:700; text-decoration:underline;">dr. H. Hendra Wijaya, Sp.PK</div>
      <div style="font-size:9pt; color:#475569;">NIP. 19780516 200501 1 002</div>
    </div>
  </div>
</div>

</body>
</html>
