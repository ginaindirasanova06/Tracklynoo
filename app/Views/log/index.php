<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; flex-wrap:wrap; gap:16px;">
  <div>
    <h2 style="font-size:1.5rem; font-weight:800; color:var(--text-primary); display:flex; align-items:center; gap:10px;">
      <span style="display:inline-flex; align-items:center; justify-content:center; width:38px; height:38px; border-radius:12px; background:linear-gradient(135deg, rgba(56,189,248,0.18), rgba(244,114,182,0.15)); color:var(--primary);">
        <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
      </span>
      Log Aktivitas Sistem (Audit Trail)
    </h2>
    <p style="font-size:0.875rem; color:var(--text-secondary); margin-top:4px;">
      Catatan kronologis seluruh interaksi pengguna, perubahan data, dan transaksi sirkulasi berkas RM
    </p>
  </div>
</div>

<!-- Search Card -->
<div class="card" style="margin-bottom:24px; padding:16px 20px;">
  <form method="get" action="<?= base_url('log') ?>" style="display:flex; gap:12px; align-items:center; flex-wrap:wrap;">
    <div style="flex:1; min-width:260px;">
      <input type="text" name="cari" class="form-control" placeholder="Cari aksi, keterangan log, atau nama pengguna..." value="<?= esc($keyword ?? '') ?>">
    </div>
    <button type="submit" class="btn btn-primary btn-sm" style="width:auto; padding:10px 18px; display:inline-flex; align-items:center; gap:6px;">
      <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
      Cari Log
    </button>
    <?php if (! empty($keyword)): ?>
      <a href="<?= base_url('log') ?>" class="btn btn-secondary btn-sm" style="width:auto; padding:10px 14px; color:#ef4444;">
        Reset
      </a>
    <?php endif; ?>
  </form>
</div>

<!-- Table of Activity Logs -->
<div class="card" style="padding:0; overflow:hidden;">
  <div class="table-responsive">
    <table class="table">
      <thead>
        <tr>
          <th style="width:170px;">Waktu Kejadian</th>
          <th style="width:190px;">Pengguna</th>
          <th style="width:150px;">Aksi</th>
          <th>Keterangan Aktivitas</th>
          <th style="width:140px;">Alamat IP</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($logs)): ?>
          <tr>
            <td colspan="5" style="text-align:center; padding:48px; color:var(--text-muted);">
              <div style="width:64px; height:64px; background:var(--bg-secondary); border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 12px;">
                <svg width="32" height="32" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="opacity:0.4;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
              </div>
              Belum ada log aktivitas yang tercatat.
            </td>
          </tr>
        <?php else: ?>
          <?php foreach ($logs as $l): ?>
            <tr>
              <td style="font-size:0.82rem; color:var(--text-secondary); font-family:var(--font-mono);">
                <?= date('d/m/Y H:i:s', strtotime($l['created_at'])) ?>
              </td>
              <td>
                <?php if ($l['user_nama']): ?>
                  <div style="font-weight:700; color:var(--text-primary); font-size:0.88rem;"><?= esc($l['user_nama']) ?></div>
                  <span class="role-badge role-<?= esc($l['role']) ?>" style="font-size:0.68rem; padding:1px 7px; margin-top:2px;">
                    <?= esc($l['username']) ?>
                  </span>
                <?php else: ?>
                  <span style="color:var(--text-muted); font-size:0.82rem;">Sistem Otomatis</span>
                <?php endif; ?>
              </td>
              <td>
                <span style="font-family:var(--font-mono); font-size:0.75rem; background:var(--bg-secondary); padding:4px 8px; border-radius:6px; font-weight:700; color:var(--primary); border:1px solid var(--border);">
                  <?= esc($l['aksi']) ?>
                </span>
              </td>
              <td style="font-size:0.85rem; color:var(--text-primary);">
                <?= esc($l['keterangan']) ?>
              </td>
              <td style="font-size:0.8rem; font-family:var(--font-mono); color:var(--text-secondary);">
                <?= esc($l['ip_address'] ?? '127.0.0.1') ?>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <?php if ($pager): ?>
    <div style="padding:16px 20px; border-top:1px solid var(--border); display:flex; justify-content:center;">
      <?= $pager->links('logs', 'default_full') ?>
    </div>
  <?php endif; ?>
</div>
<?= $this->endSection() ?>

