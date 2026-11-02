<?php

namespace App\Controllers;

use App\Models\BerkasModel;
use App\Models\PeminjamanModel;
use App\Models\UnitModel;
use App\Models\UserModel;

class DashboardController extends BaseController
{
    protected BerkasModel $berkasModel;
    protected PeminjamanModel $peminjamanModel;
    protected UnitModel $unitModel;
    protected UserModel $userModel;

    public function __construct()
    {
        $this->berkasModel     = new BerkasModel();
        $this->peminjamanModel = new PeminjamanModel();
        $this->unitModel       = new UnitModel();
        $this->userModel       = new UserModel();
    }

    public function index()
    {
        $session = session();
        $role    = $session->get('role');
        $unitId  = $session->get('unit_id');

        // Automatically update overdue status (AC-05, BR-04)
        $this->peminjamanModel->updateOverdueStatus();

        $today = date('Y-m-d');

        // Query Stats (FR-24)
        $totalBerkas       = $this->berkasModel->countAllResults();
        $sedangDipinjam    = $this->peminjamanModel->where('status', 'dipinjam')->countAllResults();
        $totalTerlambat    = $this->peminjamanModel->where('status', 'terlambat')->countAllResults();
        $pinjamHariIni     = $this->peminjamanModel->like('tgl_pinjam', $today)->countAllResults();

        // Unit specific stats for Petugas Unit
        $unitStats = null;
        if ($role === 'petugas_unit' && $unitId) {
            $unitStats = [
                'unit_dipinjam'  => $this->peminjamanModel->where('unit_id', $unitId)->where('status', 'dipinjam')->countAllResults(),
                'unit_terlambat' => $this->peminjamanModel->where('unit_id', $unitId)->where('status', 'terlambat')->countAllResults(),
            ];
        }

        // Overdue loans query (FR-25 Alert & Table)
        $overdueQuery = $this->peminjamanModel->select('peminjaman.*, berkas_rm.no_rm, berkas_rm.nama_pasien, berkas_rm.lokasi_rak, units.nama_unit, units.kode_unit')
            ->join('berkas_rm', 'berkas_rm.id = peminjaman.berkas_id')
            ->join('units', 'units.id = peminjaman.unit_id')
            ->where('peminjaman.status', 'terlambat');

        if ($role === 'petugas_unit' && $unitId) {
            $overdueQuery->where('peminjaman.unit_id', $unitId);
        }

        $daftarTerlambat = $overdueQuery->orderBy('peminjaman.batas_kembali', 'ASC')->limit(6)->findAll();

        // Calculate days overdue
        $now = time();
        foreach ($daftarTerlambat as &$row) {
            $batasTime = strtotime($row['batas_kembali']);
            $row['hari_terlambat'] = max(1, (int) floor(($now - $batasTime) / 86400));
            if ($role === 'petugas_unit') {
                $row['nama_pasien'] = $this->maskPatientName($row['nama_pasien']);
            }
        }

        // Summary of units with overdue files (FR-25)
        $overdueSummary = $this->peminjamanModel->getOverdueByUnitSummary();

        // 7-day trend (FR-24)
        $trend7Hari = $this->peminjamanModel->get7DaysTrend($role === 'petugas_unit' ? $unitId : null);

        // Top borrowing units distribution
        $unitDistribution = $this->peminjamanModel->getUnitLoanDistribution(5);

        // Recent 5 transactions
        $transaksiTerbaru = $this->peminjamanModel->getRecentTransactions(5, $role === 'petugas_unit' ? $unitId : null);
        if ($role === 'petugas_unit') {
            foreach ($transaksiTerbaru as &$tr) {
                $tr['nama_pasien'] = $this->maskPatientName($tr['nama_pasien']);
            }
        }

        return view('dashboard/index', [
            'totalBerkas'       => $totalBerkas,
            'sedangDipinjam'    => $sedangDipinjam,
            'totalTerlambat'    => $totalTerlambat,
            'pinjamHariIni'     => $pinjamHariIni,
            'unitStats'         => $unitStats,
            'daftarTerlambat'   => $daftarTerlambat,
            'overdueSummary'    => $overdueSummary,
            'trend7Hari'        => $trend7Hari,
            'unitDistribution'  => $unitDistribution,
            'transaksiTerbaru'  => $transaksiTerbaru,
            'user'              => [
                'nama'     => $session->get('nama'),
                'username' => $session->get('username'),
                'role'     => $role,
            ],
        ]);
    }

    /**
     * Mask patient name for privacy (BR-10)
     */
    private function maskPatientName(string $name): string
    {
        $words = explode(' ', trim($name));
        $masked = [];
        foreach ($words as $w) {
            if (mb_strlen($w) > 1) {
                $masked[] = mb_substr($w, 0, 1) . '***';
            } else {
                $masked[] = $w;
            }
        }
        return implode(' ', $masked);
    }
}
