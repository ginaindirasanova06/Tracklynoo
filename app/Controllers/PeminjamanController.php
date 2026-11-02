<?php

namespace App\Controllers;

use App\Models\BerkasModel;
use App\Models\PeminjamanModel;
use App\Models\UnitModel;
use App\Models\LogModel;
use App\Models\SettingModel;

class PeminjamanController extends BaseController
{
    protected BerkasModel $berkasModel;
    protected PeminjamanModel $peminjamanModel;
    protected UnitModel $unitModel;
    protected LogModel $logModel;
    protected SettingModel $settingModel;

    public function __construct()
    {
        $this->berkasModel     = new BerkasModel();
        $this->peminjamanModel = new PeminjamanModel();
        $this->unitModel       = new UnitModel();
        $this->logModel        = new LogModel();
        $this->settingModel    = new SettingModel();
    }

    public function scan()
    {
        $units = $this->unitModel->where('is_active', 1)->orderBy('nama_unit', 'ASC')->findAll();
        $durasi = (int) ($this->settingModel->getVal('durasi_peminjaman') ?? 2);
        return view('peminjaman/scan', [
            'title'  => 'Scanner Peminjaman — TRACKLY',
            'units'  => $units,
            'durasi' => $durasi,
        ]);
    }

    public function lookupQr()
    {
        $kodeQr = trim((string) $this->request->getGet('kode'));
        if (preg_match('/\/scan\/([^\/\?\#]+)/', $kodeQr, $matches)) {
            $kodeQr = urldecode($matches[1]);
        }
        if (empty($kodeQr)) {
            return $this->response->setJSON(['found' => false, 'error' => 'Kode QR tidak valid.']);
        }
        $berkas = $this->berkasModel->findByQr($kodeQr);
        if (! $berkas) {
            $berkas = $this->berkasModel->findByNoRm($kodeQr);
        }
        if (! $berkas) {
            $preview = strlen($kodeQr) > 40 ? substr($kodeQr, 0, 37) . '...' : $kodeQr;
            return $this->response->setJSON([
                'found'        => false,
                'scanned_code' => $kodeQr,
                'error'        => "QR Code terbaca ('{$preview}'), namun berkas ini tidak terdaftar di database TRACKLY. Silakan gunakan No. RM berkas atau daftarkan terlebih dahulu."
            ]);
        }
        $activeLoan = null;
        if ($berkas['status'] !== 'tersedia') {
            $activeLoan = $this->peminjamanModel
                ->select('peminjaman.*, units.nama_unit, units.kode_unit')
                ->join('units', 'units.id = peminjaman.unit_id')
                ->where('peminjaman.berkas_id', $berkas['id'])
                ->whereIn('peminjaman.status', ['dipinjam', 'terlambat'])
                ->first();
        }
        return $this->response->setJSON([
            'found'      => true,
            'berkas'     => [
                'id'          => $berkas['id'],
                'no_rm'       => $berkas['no_rm'],
                'nama_pasien' => $berkas['nama_pasien'],
                'lokasi_rak'  => $berkas['lokasi_rak'],
                'tahun_buka'  => $berkas['tahun_buka'],
                'status'      => $berkas['status'],
                'kode_qr'     => $berkas['kode_qr'],
            ],
            'activeLoan' => $activeLoan,
        ]);
    }

    public function lookupRm()
    {
        $noRm = trim((string) $this->request->getGet('no_rm'));
        if (preg_match('/\/scan\/([^\/\?\#]+)/', $noRm, $matches)) {
            $noRm = urldecode($matches[1]);
        }
        if (empty($noRm)) {
            return $this->response->setJSON(['found' => false, 'error' => 'Nomor RM tidak boleh kosong.']);
        }
        $berkas = $this->berkasModel->findByNoRm($noRm);
        if (! $berkas) {
            $berkas = $this->berkasModel->findByQr($noRm);
        }
        if (! $berkas) {
            return $this->response->setJSON(['found' => false, 'error' => "Berkas dengan No. RM '{$noRm}' tidak ditemukan dalam sistem."]);
        }
        $activeLoan = null;
        if ($berkas['status'] !== 'tersedia') {
            $activeLoan = $this->peminjamanModel
                ->select('peminjaman.*, units.nama_unit, units.kode_unit')
                ->join('units', 'units.id = peminjaman.unit_id')
                ->where('peminjaman.berkas_id', $berkas['id'])
                ->whereIn('peminjaman.status', ['dipinjam', 'terlambat'])
                ->first();
        }
        return $this->response->setJSON([
            'found'      => true,
            'berkas'     => [
                'id'          => $berkas['id'],
                'no_rm'       => $berkas['no_rm'],
                'nama_pasien' => $berkas['nama_pasien'],
                'lokasi_rak'  => $berkas['lokasi_rak'],
                'tahun_buka'  => $berkas['tahun_buka'],
                'status'      => $berkas['status'],
                'kode_qr'     => $berkas['kode_qr'],
            ],
            'activeLoan' => $activeLoan,
        ]);
    }

    public function store()
    {
        $berkasId     = (int) $this->request->getPost('berkas_id');
        $unitId       = (int) $this->request->getPost('unit_id');
        $namaPeminjam = trim((string) $this->request->getPost('nama_peminjam'));
        $tujuan       = trim((string) $this->request->getPost('tujuan'));
        $batasKembali = trim((string) $this->request->getPost('batas_kembali'));

        if (! $berkasId || ! $unitId || empty($namaPeminjam) || empty($tujuan) || empty($batasKembali)) {
            return redirect()->to('/peminjaman/scan')->with('error', 'Semua bidang wajib diisi.');
        }

        $berkas = $this->berkasModel->find($berkasId);
        if (! $berkas) {
            return redirect()->to('/peminjaman/scan')->with('error', 'Berkas tidak ditemukan.');
        }

        if ($berkas['status'] !== 'tersedia') {
            return redirect()->to('/peminjaman/scan')->with('error', "Berkas No. RM '{$berkas['no_rm']}' sedang dipinjam dan tidak dapat dipinjam ulang.");
        }

        $unit = $this->unitModel->find($unitId);
        if (! $unit || ! $unit['is_active']) {
            return redirect()->to('/peminjaman/scan')->with('error', 'Unit peminjam tidak valid atau tidak aktif.');
        }

        $validTujuan = ['Pelayanan pasien', 'Kontrol/Poli', 'Klaim/Asuransi', 'Audit/Akreditasi', 'Penelitian/Pendidikan', 'Lainnya'];
        if (! in_array($tujuan, $validTujuan)) {
            return redirect()->to('/peminjaman/scan')->with('error', 'Tujuan peminjaman tidak valid.');
        }

        $today = date('Y-m-d');
        if ($batasKembali < $today) {
            return redirect()->to('/peminjaman/scan')->with('error', 'Batas tanggal kembali tidak boleh sebelum hari ini.');
        }

        $this->peminjamanModel->insert([
            'berkas_id'    => $berkasId,
            'unit_id'      => $unitId,
            'dicatat_oleh' => session()->get('user_id'),
            'nama_peminjam' => $namaPeminjam,
            'tujuan'       => $tujuan,
            'tgl_pinjam'   => date('Y-m-d H:i:s'),
            'batas_kembali' => $batasKembali,
            'status'       => 'dipinjam',
        ]);

        $this->berkasModel->update($berkasId, ['status' => 'dipinjam']);

        $this->logModel->logActivity(
            session()->get('user_id'),
            'pinjam_berkas',
            "Mencatat peminjaman berkas No. RM {$berkas['no_rm']} oleh {$namaPeminjam} (Unit: {$unit['nama_unit']}, Tujuan: {$tujuan})"
        );

        return redirect()->to('/peminjaman/scan')->with('success', "Peminjaman berkas No. RM '{$berkas['no_rm']}' a/n {$berkas['nama_pasien']} berhasil dicatat.");
    }

    public function riwayat()
    {
        $this->peminjamanModel->updateOverdueStatus();

        $keyword = trim((string) $this->request->getGet('cari'));
        $unitId  = $this->request->getGet('unit_id');
        $status  = $this->request->getGet('status');
        $dari    = $this->request->getGet('dari');
        $sampai  = $this->request->getGet('sampai');

        $query = $this->peminjamanModel
            ->select('peminjaman.*, berkas_rm.no_rm, berkas_rm.nama_pasien, units.nama_unit, units.kode_unit, users.nama as nama_petugas')
            ->join('berkas_rm', 'berkas_rm.id = peminjaman.berkas_id')
            ->join('units', 'units.id = peminjaman.unit_id')
            ->join('users', 'users.id = peminjaman.dicatat_oleh');

        if (! empty($keyword)) {
            $query->groupStart()
                ->like('berkas_rm.no_rm', $keyword)
                ->orLike('berkas_rm.nama_pasien', $keyword)
                ->orLike('peminjaman.nama_peminjam', $keyword)
                ->groupEnd();
        }
        if (! empty($unitId)) { $query->where('peminjaman.unit_id', $unitId); }
        if (! empty($status) && $status !== 'all') { $query->where('peminjaman.status', $status); }
        if (! empty($dari)) { $query->where('DATE(peminjaman.tgl_pinjam) >=', $dari); }
        if (! empty($sampai)) { $query->where('DATE(peminjaman.tgl_pinjam) <=', $sampai); }

        $peminjaman = $query->orderBy('peminjaman.tgl_pinjam', 'DESC')->paginate(15, 'peminjaman');
        $pager      = $this->peminjamanModel->pager;
        $units      = $this->unitModel->where('is_active', 1)->findAll();

        return view('peminjaman/riwayat', [
            'title'      => 'Riwayat Peminjaman — TRACKLY',
            'peminjaman' => $peminjaman,
            'pager'      => $pager,
            'units'      => $units,
            'keyword'    => $keyword,
            'unitId'     => $unitId,
            'status'     => $status,
            'dari'       => $dari,
            'sampai'     => $sampai,
            'userRole'   => session()->get('role'),
        ]);
    }

    public function belumKembali()
    {
        $this->peminjamanModel->updateOverdueStatus();

        $unitId = $this->request->getGet('unit_id');
        $role   = session()->get('role');

        $query = $this->peminjamanModel
            ->select('peminjaman.*, berkas_rm.no_rm, berkas_rm.nama_pasien, berkas_rm.lokasi_rak, units.nama_unit, units.kode_unit, users.nama as nama_petugas')
            ->join('berkas_rm', 'berkas_rm.id = peminjaman.berkas_id')
            ->join('units', 'units.id = peminjaman.unit_id')
            ->join('users', 'users.id = peminjaman.dicatat_oleh')
            ->whereIn('peminjaman.status', ['dipinjam', 'terlambat']);

        if (! empty($unitId)) { $query->where('peminjaman.unit_id', $unitId); }
        if ($role === 'petugas_unit') { $query->where('peminjaman.unit_id', session()->get('unit_id')); }

        $peminjaman = $query->orderBy('peminjaman.batas_kembali', 'ASC')->findAll();
        $units      = $this->unitModel->where('is_active', 1)->findAll();

        return view('peminjaman/belum_kembali', [
            'title'      => 'Berkas Belum Kembali — TRACKLY',
            'peminjaman' => $peminjaman,
            'units'      => $units,
            'unitId'     => $unitId,
            'userRole'   => $role,
        ]);
    }

    /**
     * Export unreturned loans list to Excel/CSV (FR-27 & Wireframe Gambar 7)
     */
    public function exportExcelBelumKembali()
    {
        $this->peminjamanModel->updateOverdueStatus();

        $unitId = $this->request->getGet('unit_id');
        $role   = session()->get('role');

        $query = $this->peminjamanModel
            ->select('peminjaman.*, berkas_rm.no_rm, berkas_rm.nama_pasien, berkas_rm.lokasi_rak, units.nama_unit, units.kode_unit, users.nama as nama_petugas')
            ->join('berkas_rm', 'berkas_rm.id = peminjaman.berkas_id')
            ->join('units', 'units.id = peminjaman.unit_id')
            ->join('users', 'users.id = peminjaman.dicatat_oleh', 'left')
            ->whereIn('peminjaman.status', ['dipinjam', 'terlambat']);

        if (! empty($unitId)) {
            $query->where('peminjaman.unit_id', $unitId);
        }
        if ($role === 'petugas_unit') {
            $query->where('peminjaman.unit_id', session()->get('unit_id'));
        }

        $peminjaman = $query->orderBy('peminjaman.batas_kembali', 'ASC')->findAll();

        $filename = 'Berkas_Belum_Kembali_Trackly_' . date('Ymd_His') . '.csv';

        // Log export activity
        $this->logModel->logActivity(session()->get('user_id'), 'belum_kembali_export_excel', 'Mengekspor daftar berkas belum kembali ke Excel/CSV');

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $out = fopen('php://output', 'w');
        fputs($out, "\xEF\xBB\xBF");

        fputcsv($out, ['TRACKLY - DAFTAR BERKAS REKAM MEDIS BELUM KEMBALI']);
        fputcsv($out, ['Tanggal Unduh:', date('d/m/Y H:i:s') . ' WIB']);
        fputcsv($out, ['Diunduh Oleh:', session()->get('nama') . ' (' . session()->get('username') . ')']);
        fputcsv($out, []);

        fputcsv($out, [
            'No.',
            'No. RM',
            'Nama Pasien',
            'Lokasi Rak',
            'Unit Peminjam',
            'Nama Peminjam',
            'Tujuan Peminjaman',
            'Tanggal Pinjam',
            'Batas Kembali',
            'Lama Pinjam (Hari)',
            'Status',
        ]);

        $no = 1;
        $now = time();
        foreach ($peminjaman as $row) {
            $tglPinjam = strtotime($row['tgl_pinjam']);
            $lamaHari = max(1, (int) ceil(($now - $tglPinjam) / 86400));
            $isOverdue = ($row['status'] === 'terlambat' || $row['batas_kembali'] < date('Y-m-d'));

            $namaPasien = $row['nama_pasien'];
            if ($role === 'petugas_unit') {
                $words = explode(' ', trim($namaPasien));
                $masked = [];
                foreach ($words as $w) {
                    $masked[] = mb_strlen($w) > 1 ? mb_substr($w, 0, 1) . '***' : $w;
                }
                $namaPasien = implode(' ', $masked);
            }

            fputcsv($out, [
                $no++,
                $row['no_rm'],
                $namaPasien,
                $row['lokasi_rak'],
                $row['nama_unit'],
                $row['nama_peminjam'],
                $row['tujuan'],
                date('d/m/Y H:i', $tglPinjam),
                date('d/m/Y', strtotime($row['batas_kembali'])),
                $lamaHari . ' hari',
                $isOverdue ? 'TERLAMBAT' : 'DIPINJAM',
            ]);
        }

        fclose($out);
        exit;
    }

    /**
     * Extend loan due date (FR-18)
     */
    public function perpanjang(int $id)
    {
        $batasBaru = trim((string) $this->request->getPost('batas_kembali_baru'));
        $alasan    = trim((string) $this->request->getPost('alasan'));

        if (empty($batasBaru) || empty($alasan)) {
            return redirect()->back()->with('error', 'Batas kembali baru dan alasan perpanjangan wajib diisi.');
        }

        $loan = $this->peminjamanModel->find($id);
        if (! $loan || ! in_array($loan['status'], ['dipinjam', 'terlambat'])) {
            return redirect()->back()->with('error', 'Data peminjaman tidak aktif atau tidak ditemukan.');
        }

        if ($batasBaru <= $loan['batas_kembali']) {
            return redirect()->back()->with('error', 'Batas kembali baru harus lebih lambat dari batas kembali sebelumnya.');
        }

        $berkas = $this->berkasModel->find($loan['berkas_id']);
        $newStatus = ($batasBaru >= date('Y-m-d')) ? 'dipinjam' : 'terlambat';

        $this->peminjamanModel->update($id, [
            'batas_kembali' => $batasBaru,
            'status'        => $newStatus,
            'catatan'       => ($loan['catatan'] ? $loan['catatan'] . ' | ' : '') . "Perpanjangan s/d {$batasBaru}: {$alasan}",
        ]);

        $this->logModel->logActivity(
            session()->get('user_id'),
            'perpanjang_peminjaman',
            "Memperpanjang peminjaman berkas No. RM {$berkas['no_rm']} s/d {$batasBaru}. Alasan: {$alasan}"
        );

        return redirect()->back()->with('success', "Masa peminjaman berkas No. RM '{$berkas['no_rm']}' berhasil diperpanjang hingga " . date('d/m/Y', strtotime($batasBaru)) . ".");
    }

    /**
     * Transaction correction by Admin with reason logged (FR-19)
     */
    public function koreksi(int $id)
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->back()->with('error', 'Hanya Administrator yang berhak mengoreksi data transaksi.');
        }

        $namaPeminjam = trim((string) $this->request->getPost('nama_peminjam'));
        $tujuan       = trim((string) $this->request->getPost('tujuan'));
        $alasan       = trim((string) $this->request->getPost('alasan_koreksi'));

        if (empty($namaPeminjam) || empty($tujuan) || empty($alasan)) {
            return redirect()->back()->with('error', 'Nama peminjam, tujuan, dan alasan koreksi wajib diisi.');
        }

        $loan = $this->peminjamanModel->find($id);
        if (! $loan) {
            return redirect()->back()->with('error', 'Data peminjaman tidak ditemukan.');
        }

        $berkas = $this->berkasModel->find($loan['berkas_id']);

        $this->peminjamanModel->update($id, [
            'nama_peminjam' => $namaPeminjam,
            'tujuan'        => $tujuan,
            'catatan'       => ($loan['catatan'] ? $loan['catatan'] . ' | ' : '') . "Koreksi Admin: {$alasan}",
        ]);

        $this->logModel->logActivity(
            session()->get('user_id'),
            'koreksi_transaksi',
            "Koreksi transaksi peminjaman ID #{$id} (No. RM {$berkas['no_rm']}). Alasan: {$alasan}"
        );

        return redirect()->back()->with('success', 'Data transaksi berhasil dikoreksi.');
    }
}



