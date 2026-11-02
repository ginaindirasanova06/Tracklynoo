<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<!-- Hero Welcome -->
<div class="hero-welcome">
  <div>
    <div style="display:flex; align-items:center; gap:8px; margin-bottom:6px;">
      <span class="badge" style="background:linear-gradient(135deg, #e0f2fe, #fdf2f8); color:var(--primary); font-weight:800; font-size:0.75rem; border:1px solid #bae6fd;">
        <?= strtoupper($user['role']) ?>
      </span>
      <span style="font-size:0.8rem; color:var(--text-muted);">&bull;</span>
      <span style="font-size:0.85rem; color:var(--text-secondary); font-weight:600;"><?= date('l, d F Y') ?></span>
    </div>
    <h2 class="hero-title">Halo, <?= esc($user['nama']) ?>!</h2>
    <p class="hero-desc">
      Selamat datang di <strong>TRACKLY</strong>. <em>Track Every File, Improve Every Process.</em> Sistem pemantauan sirkulasi dan pelacakan berkas rekam medis berbasis QR Code.
    </p>
  </div>
  <div style="text-align:right;">
    <div style="font-size:0.75rem; color:#94a3b8; text-transform:uppercase; letter-spacing:0.8px; font-weight:700;">Waktu Sistem</div>
    <div style="font-size:1.35rem; font-weight:800; color:var(--primary); font-family:var(--font-mono); margin-top:2px;">
      <?= date('H:i') ?> <span style="font-size:0.85rem; color:var(--text-muted); font-weight:600;">WIB</span>
    </div>
    <div style="display:flex; gap:8px; margin-top:10px; justify-content:flex-end;">
      <?php if (in_array($user['role'], ['admin', 'petugas_rm'])): ?>
      <a href="<?= base_url('peminjaman/scan') ?>" class="btn btn-primary btn-sm" style="font-size:0.8rem;">
        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
        Scan Pinjam
      </a>
      <a href="<?= base_url('pengembalian/scan') ?>" class="btn btn-pink btn-sm" style="font-size:0.8rem;">
        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
        Scan Kembali
      </a>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- FR-25: Smart Overdue Notification Banner -->
<?php if ($totalTerlambat > 0): ?>
<div class="card" style="margin-bottom:24px; border:2px solid #fecdd3; background:linear-gradient(135deg, #fff1f2, #ffffff); box-shadow:0 8px 24px rgba(244,63,94,0.08);">
  <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:16px; flex-wrap:wrap;">
    <div style="display:flex; gap:14px; align-items:flex-start;">
      <div style="width:46px; height:46px; background:#ffe4e6; border-radius:50%; display:flex; align-items:center; justify-content:center; flex-shrink:0; color:#e11d48;">
        <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
      </div>
      <div>
        <div style="display:flex; align-items:center; gap:8px;">
          <h4 style="margin:0; font-size:1.02rem; color:#9f1239; font-weight:800;">Perhatian: <?= $totalTerlambat ?> Berkas Melewati Batas Pengembalian!</h4>
          <span class="badge badge-terlambat" style="animation:pulse 2s infinite;">TERLAMBAT</span>
        </div>
        <p style="margin:4px 0 0 0; font-size:0.875rem; color:#881337;">
          Segera lakukan tindak lanjut sirkulasi untuk menjaga tata kelola rekam medis tertib.
          <?php if (!empty($overdueSummary)): ?>
            <span style="font-weight:700; color:#be123c;">Unit menunggak: </span>
            <?php foreach (array_slice($overdueSummary, 0, 3) as $sum): ?>
              <span style="background:rgba(244,63,94,0.1); padding:2px 8px; border-radius:12px; font-size:0.78rem; font-weight:700; margin-right:4px;">
                <?= esc($sum['nama_unit']) ?> (<?= $sum['total_terlambat'] ?>)
              </span>
            <?php endforeach; ?>
          <?php endif; ?>
        </p>
      </div>
    </div>
    <div style="display:flex; gap:8px; align-items:center; margin-left:auto;">
      <a href="<?= base_url('belum-kembali?status=terlambat') ?>" class="btn btn-danger btn-sm" style="font-size:0.82rem;">
        Lihat Berkas Terlambat &rarr;
      </a>
    </div>
  </div>
</div>
<?php endif; ?>

<!-- 4 Stat Cards with Counter Animation (FR-24 & Modern Pastel Theme) -->
<div class="stats-grid">
  <div class="stat-card">
    <div class="stat-icon blue">
      <svg width="26" height="26" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
    </div>
    <div class="stat-info">
      <span class="stat-label">Total Berkas RM</span>
      <span class="stat-value"><?= number_format($totalBerkas) ?></span>
    </div>
  </div>

  <div class="stat-card">
    <div class="stat-icon amber">
      <svg width="26" height="26" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
    </div>
    <div class="stat-info">
      <span class="stat-label">Sedang Dipinjam</span>
      <span class="stat-value"><?= number_format($sedangDipinjam) ?></span>
    </div>
  </div>

  <div class="stat-card" style="<?= $totalTerlambat > 0 ? 'border-color:#fecdd3; background:#fff1f2;' : '' ?>">
    <div class="stat-icon red">
      <svg width="26" height="26" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
    </div>
    <div class="stat-info">
      <span class="stat-label">Terlambat Kembali</span>
      <span class="stat-value" style="<?= $totalTerlambat > 0 ? 'color:#e11d48;' : '' ?>">
        <?= number_format($totalTerlambat) ?>
      </span>
    </div>
  </div>

  <div class="stat-card">
    <div class="stat-icon pink">
      <svg width="26" height="26" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
    </div>
    <div class="stat-info">
      <span class="stat-label">Pinjam Hari Ini</span>
      <span class="stat-value"><?= number_format($pinjamHariIni) ?></span>
    </div>
  </div>
</div>

<!-- Charts Section: Tren 7 Hari (Baby Blue vs Pastel Pink) & Distribusi Unit -->
<div style="display:grid; grid-template-columns: 2fr 1.1fr; gap:24px; margin-bottom:28px;">
  <!-- Tren 7 Hari Terakhir -->
  <div class="card">
    <div class="card-header" style="margin-bottom:16px;">
      <div>
        <h3 class="card-title" style="margin:0 0 4px 0;">Grafik Sirkulasi 7 Hari Terakhir</h3>
        <p style="font-size:0.82rem; color:var(--text-secondary); margin:0;">Perbandingan transaksi peminjaman baru dan pengembalian harian</p>
      </div>
      <span style="font-size:0.75rem; background:linear-gradient(135deg, #e0f2fe, #fdf2f8); color:var(--primary); padding:4px 12px; border-radius:var(--radius-full); font-weight:700; border:1px solid #bae6fd;">
        7 Hari Terakhir
      </span>
    </div>
    <div style="height:260px; position:relative;">
      <canvas id="trendChart"></canvas>
    </div>
  </div>

  <!-- Distribusi Peminjaman per Unit -->
  <div class="card">
    <div class="card-header" style="margin-bottom:16px;">
      <div>
        <h3 class="card-title" style="margin:0 0 4px 0;">Peminjaman per Unit</h3>
        <p style="font-size:0.82rem; color:var(--text-secondary); margin:0;">Proporsi unit peminjam teraktif</p>
      </div>
    </div>
    <div style="height:260px; position:relative; display:flex; align-items:center; justify-content:center;">
      <?php if (empty($unitDistribution)): ?>
        <div style="text-align:center; color:var(--text-muted); font-size:0.85rem;">
          Belum ada transaksi peminjaman tercatat.
        </div>
      <?php else: ?>
        <canvas id="unitDistChart"></canvas>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- Bottom Section: Berkas Terlambat & Aktivitas Terkini -->
<div style="display:grid; grid-template-columns: 1.8fr 1.2fr; gap:24px;">
  <!-- Berkas Terlambat Terbaru -->
  <div class="card">
    <div class="card-header">
      <div>
        <h3 class="card-title">Berkas Terlambat Kembali</h3>
        <p style="font-size:0.82rem; color:var(--text-secondary); margin:0;">Prioritas penarikan dan koordinasi ke ruangan</p>
      </div>
      <a href="<?= base_url('belum-kembali') ?>" class="btn btn-secondary btn-sm" style="font-size:0.8rem;">Lihat Semua &rarr;</a>
    </div>

    <div class="table-responsive">
      <table class="table">
        <thead>
          <tr>
            <th>No. RM</th>
            <th>Pasien</th>
            <th>Unit</th>
            <th>Batas Kembali</th>
            <th>Terlambat</th>
            <?php if (in_array($user['role'], ['admin', 'petugas_rm'])): ?>
            <th style="text-align:right;">Aksi</th>
            <?php endif; ?>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($daftarTerlambat)): ?>
            <tr>
              <td colspan="<?= in_array($user['role'], ['admin', 'petugas_rm']) ? '6' : '5' ?>" style="text-align:center; padding:36px; color:var(--text-muted);">
                <div style="width:48px; height:48px; background:#ecfdf5; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 10px; color:#10b981;">
                  <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <div style="font-weight:700; color:var(--text-primary); margin-bottom:2px;">Tertib Sirkulasi</div>
                <div style="font-size:0.82rem;">Tidak ada berkas rekam medis yang terlambat saat ini.</div>
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($daftarTerlambat as $row): ?>
              <tr>
                <td>
                  <a href="<?= base_url('berkas/' . $row['berkas_id']) ?>" style="font-weight:800; color:var(--primary); font-family:var(--font-mono); text-decoration:none;">
                    <?= esc($row['no_rm']) ?>
                  </a>
                </td>
                <td style="font-weight:600;"><?= esc($row['nama_pasien']) ?></td>
                <td>
                  <span style="font-size:0.82rem; background:#f1f5f9; padding:2px 8px; border-radius:6px; font-weight:600; color:var(--text-secondary);">
                    <?= esc($row['nama_unit']) ?>
                  </span>
                </td>
                <td style="font-size:0.85rem;"><?= date('d/m/Y', strtotime($row['batas_kembali'])) ?></td>
                <td>
                  <span class="badge badge-terlambat" style="font-size:0.75rem;">
                    <?= $row['hari_terlambat'] ?> hari
                  </span>
                </td>
                <?php if (in_array($user['role'], ['admin', 'petugas_rm'])): ?>
                <td style="text-align:right;">
                  <a href="<?= base_url('pengembalian/scan') ?>" class="btn btn-pink btn-sm" style="padding:4px 10px; font-size:0.75rem;" title="Scan Pengembalian">
                    Kembalikan
                  </a>
                </td>
                <?php endif; ?>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Aktivitas Transaksi Terkini -->
  <div class="card">
    <div class="card-header">
      <div>
        <h3 class="card-title">Aktivitas Terkini</h3>
        <p style="font-size:0.82rem; color:var(--text-secondary); margin:0;">Transaksi peminjaman & pengembalian terakhir</p>
      </div>
      <a href="<?= base_url('riwayat') ?>" class="btn btn-secondary btn-sm" style="font-size:0.8rem;">Riwayat &rarr;</a>
    </div>

    <div style="display:flex; flex-direction:column; gap:12px;">
      <?php if (empty($transaksiTerbaru)): ?>
        <div style="text-align:center; padding:32px; color:var(--text-muted); font-size:0.85rem;">
          Belum ada aktivitas transaksi hari ini.
        </div>
      <?php else: ?>
        <?php foreach ($transaksiTerbaru as $trx): ?>
          <div style="display:flex; align-items:flex-start; gap:12px; padding:12px 14px; border:1px solid var(--border-light); border-radius:var(--radius-md); background:#ffffff; box-shadow:var(--shadow-xs); transition:var(--transition);" onmouseover="this.style.transform='translateX(4px)'" onmouseout="this.style.transform='translateX(0)'">
            <div style="width:36px; height:36px; border-radius:50%; display:flex; align-items:center; justify-content:center; flex-shrink:0; <?= $trx['status'] === 'dikembalikan' ? 'background:#ecfdf5; color:#10b981;' : ($trx['status'] === 'terlambat' ? 'background:#fff1f2; color:#e11d48;' : 'background:#e0f2fe; color:#0284c7;') ?>">
              <?php if ($trx['status'] === 'dikembalikan'): ?>
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
              <?php elseif ($trx['status'] === 'terlambat'): ?>
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
              <?php else: ?>
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
              <?php endif; ?>
            </div>
            <div style="flex:1; min-width:0;">
              <div style="display:flex; justify-content:space-between; align-items:center;">
                <strong style="font-size:0.875rem; font-family:var(--font-mono); color:var(--text-primary);"><?= esc($trx['no_rm']) ?></strong>
                <span style="font-size:0.72rem; color:var(--text-muted); font-weight:600;"><?= date('H:i', strtotime($trx['updated_at'] ?? $trx['tgl_pinjam'])) ?> WIB</span>
              </div>
              <div style="font-size:0.82rem; color:var(--text-secondary); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                <?= esc($trx['nama_pasien']) ?> &bull; <?= esc($trx['nama_unit']) ?>
              </div>
              <div style="font-size:0.75rem; color:var(--text-muted); margin-top:2px;">
                Oleh: <?= esc($trx['nama_peminjam'] ?? $trx['nama_petugas_catat'] ?? 'Petugas') ?>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  // 1. Tren 7 Hari Chart (Soft Blue vs Pastel Pink)
  const trendData = <?= json_encode($trend7Hari ?? []) ?>;
  const trendLabels = trendData.map(d => d.label);
  const pinjamValues = trendData.map(d => d.pinjam);
  const kembaliValues = trendData.map(d => d.kembali);

  const ctxTrend = document.getElementById('trendChart');
  if (ctxTrend) {
    new Chart(ctxTrend.getContext('2d'), {
      type: 'bar',
      data: {
        labels: trendLabels,
        datasets: [
          {
            label: 'Peminjaman',
            data: pinjamValues,
            backgroundColor: '#38bdf8',
            hoverBackgroundColor: '#0284c7',
            borderRadius: 8,
            barPercentage: 0.6,
          },
          {
            label: 'Pengembalian',
            data: kembaliValues,
            backgroundColor: '#f472b6',
            hoverBackgroundColor: '#ec4899',
            borderRadius: 8,
            barPercentage: 0.6,
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'top',
            labels: {
              boxWidth: 12,
              font: { family: 'Plus Jakarta Sans', size: 12, weight: '700' }
            }
          },
          tooltip: {
            backgroundColor: '#0f172a',
            padding: 10,
            cornerRadius: 10,
            titleFont: { family: 'Plus Jakarta Sans', weight: '700' },
            bodyFont: { family: 'Plus Jakarta Sans' }
          }
        },
        scales: {
          x: {
            grid: { display: false },
            ticks: { font: { family: 'Plus Jakarta Sans', size: 11, weight: '600' } }
          },
          y: {
            beginAtZero: true,
            ticks: {
              stepSize: 1,
              font: { family: 'Plus Jakarta Sans', size: 11 }
            },
            grid: { color: '#f1f5f9' }
          }
        }
      }
    });
  }

  // 2. Unit Distribution Chart (Pastel Healthcare Palette)
  const unitData = <?= json_encode($unitDistribution ?? []) ?>;
  const ctxUnit = document.getElementById('unitDistChart');
  if (ctxUnit && unitData.length > 0) {
    const unitLabels = unitData.map(u => u.kode_unit || u.nama_unit);
    const unitCounts = unitData.map(u => parseInt(u.total, 10));
    const palette = ['#38bdf8', '#f472b6', '#a855f7', '#34d399', '#fbbf24', '#f87171'];

    new Chart(ctxUnit.getContext('2d'), {
      type: 'doughnut',
      data: {
        labels: unitLabels,
        datasets: [{
          data: unitCounts,
          backgroundColor: palette.slice(0, unitLabels.length),
          borderWidth: 3,
          borderColor: '#ffffff',
          hoverOffset: 6
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '70%',
        plugins: {
          legend: {
            position: 'bottom',
            labels: {
              boxWidth: 10,
              font: { family: 'Plus Jakarta Sans', size: 11, weight: '600' }
            }
          },
          tooltip: {
            backgroundColor: '#0f172a',
            padding: 10,
            cornerRadius: 10,
            callbacks: {
              label: function(ctx) {
                return ` ${ctx.label}: ${ctx.parsed} berkas`;
              }
            }
          }
        }
      }
    });
  }
});
</script>
<?= $this->endSection() ?>
