<?php

namespace App\Controllers;

use App\Models\BerkasModel;
use App\Models\PeminjamanModel;
use App\Models\UnitModel;
use App\Models\LogModel;

class LaporanController extends BaseController
{
    protected BerkasModel $berkasModel;
    protected PeminjamanModel $peminjamanModel;
    protected UnitModel $unitModel;
    protected LogModel $logModel;

    public function __construct()
    {
        $this->berkasModel     = new BerkasModel();
        $this->peminjamanModel = new PeminjamanModel();
        $this->unitModel       = new UnitModel();
        $this->logModel        = new LogModel();
    }

    /**
     * Build base query based on filter parameters
     */
    private function buildFilterQuery(array $filters)
    {
        $session = session();
        $role    = $session->get('role');
        $userUnitId = $session->get('unit_id');

        $builder = $this->peminjamanModel
            ->select('peminjaman.*, berkas_rm.no_rm, berkas_rm.nama_pasien, berkas_rm.lokasi_rak, units.nama_unit, units.kode_unit, users.nama as nama_petugas_catat')
            ->join('berkas_rm', 'berkas_rm.id = peminjaman.berkas_id')
            ->join('units', 'units.id = peminjaman.unit_id')
            ->join('users', 'users.id = peminjaman.dicatat_oleh', 'left');

        // Petugas unit can only see their unit's data (BR-10 Privacy Rule)
        if ($role === 'petugas_unit' && $userUnitId) {
            $builder->where('peminjaman.unit_id', $userUnitId);
        } elseif (! empty($filters['unit_id'])) {
            $builder->where('peminjaman.unit_id', (int) $filters['unit_id']);
        }

        // Date range filter
        if (! empty($filters['tgl_mulai'])) {
            $builder->where('DATE(peminjaman.tgl_pinjam) >=', $filters['tgl_mulai']);
        }
        if (! empty($filters['tgl_selesai'])) {
            $builder->where('DATE(peminjaman.tgl_pinjam) <=', $filters['tgl_selesai']);
        }

        // Status filter
        if (! empty($filters['status'])) {
            $builder->where('peminjaman.status', $filters['status']);
        }

        // Kondisi kembali filter
        if (! empty($filters['kondisi_kembali'])) {
            $builder->where('peminjaman.kondisi_kembali', $filters['kondisi_kembali']);
        }

        return $builder;
    }

    /**
     * Calculate summary metrics from loans array
     */
    private function calculateMetrics(array $loans): array
    {
        $total = count($loans);
        $dikembalikan = 0;
        $dipinjam = 0;
        $terlambat = 0;
        $tepatWaktu = 0;
        $totalDurasiHari = 0;
        $durasiCount = 0;

        foreach ($loans as $row) {
            if ($row['status'] === 'dikembalikan') {
                $dikembalikan++;
                $tglPinjam = strtotime($row['tgl_pinjam']);
                $tglKembali = strtotime($row['tgl_kembali']);
                $batasKembali = strtotime($row['batas_kembali']);

                if ($tglKembali <= $batasKembali) {
                    $tepatWaktu++;
                }

                $durasi = max(1, (int) ceil(($tglKembali - $tglPinjam) / 86400));
                $totalDurasiHari += $durasi;
                $durasiCount++;
            } elseif ($row['status'] === 'terlambat') {
                $terlambat++;
            } elseif ($row['status'] === 'dipinjam') {
                $dipinjam++;
            }
        }

        $rataRataHari = $durasiCount > 0 ? round($totalDurasiHari / $durasiCount, 1) : 0;
        $persenTepatWaktu = $dikembalikan > 0 ? round(($tepatWaktu / $dikembalikan) * 100, 1) : 100;

        return [
            'total'             => $total,
            'dikembalikan'      => $dikembalikan,
            'dipinjam'          => $dipinjam,
            'terlambat'         => $terlambat,
            'tepat_waktu'       => $tepatWaktu,
            'rata_rata_hari'    => $rataRataHari,
            'persen_tepat_waktu'=> $persenTepatWaktu,
        ];
    }

    /**
     * Show main report page (FR-26)
     */
    public function index()
    {
        $tglMulai   = $this->request->getGet('tgl_mulai') ?? date('Y-m-d', strtotime('-30 days'));
        $tglSelesai = $this->request->getGet('tgl_selesai') ?? date('Y-m-d');
        $unitId     = $this->request->getGet('unit_id');
        $status     = $this->request->getGet('status');
        $kondisi    = $this->request->getGet('kondisi_kembali');

        $filters = [
            'tgl_mulai'       => $tglMulai,
            'tgl_selesai'     => $tglSelesai,
            'unit_id'         => $unitId,
            'status'          => $status,
            'kondisi_kembali' => $kondisi,
        ];

        $query = $this->buildFilterQuery($filters);
        $loans = $query->orderBy('peminjaman.tgl_pinjam', 'DESC')->findAll();

        $metrics = $this->calculateMetrics($loans);

        // Mask patient names if user is petugas_unit (BR-10)
        $role = session()->get('role');
        if ($role === 'petugas_unit') {
            foreach ($loans as &$row) {
                $row['nama_pasien'] = $this->maskPatientName($row['nama_pasien']);
            }
        }

        $units = $this->unitModel->where('is_active', 1)->orderBy('nama_unit', 'ASC')->findAll();

        return view('laporan/index', [
            'title'   => 'Laporan Peminjaman Berkas RM — TRACKLY',
            'loans'   => $loans,
            'metrics' => $metrics,
            'filters' => $filters,
            'units'   => $units,
        ]);
    }

    /**
     * Export report to Excel / CSV (FR-27)
     */
    public function exportExcel()
    {
        $filters = [
            'tgl_mulai'       => $this->request->getGet('tgl_mulai') ?? date('Y-m-d', strtotime('-30 days')),
            'tgl_selesai'     => $this->request->getGet('tgl_selesai') ?? date('Y-m-d'),
            'unit_id'         => $this->request->getGet('unit_id'),
            'status'          => $this->request->getGet('status'),
            'kondisi_kembali' => $this->request->getGet('kondisi_kembali'),
        ];

        $query = $this->buildFilterQuery($filters);
        $loans = $query->orderBy('peminjaman.tgl_pinjam', 'DESC')->findAll();

        $role = session()->get('role');
        if ($role === 'petugas_unit') {
            foreach ($loans as &$row) {
                $row['nama_pasien'] = $this->maskPatientName($row['nama_pasien']);
            }
        }

        $filename = 'Laporan_Peminjaman_Trackly_' . date('Ymd_His') . '.csv';

        // Log export activity (FR-28)
        $this->logModel->logActivity(session()->get('user_id'), 'laporan_export_excel', "Mengekspor laporan peminjaman ke format Excel/CSV ({$filters['tgl_mulai']} s/d {$filters['tgl_selesai']})");

        // Output CSV with UTF-8 BOM for seamless Microsoft Excel opening
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $out = fopen('php://output', 'w');
        // UTF-8 BOM
        fputs($out, "\xEF\xBB\xBF");

        // Title rows
        fputcsv($out, ['TRACKLY - SISTEM PELACAKAN BERKAS REKAM MEDIS']);
        fputcsv($out, ['LAPORAN SIRKULASI & PEMINJAMAN BERKAS REKAM MEDIS']);
        fputcsv($out, ['Periode:', $filters['tgl_mulai'] . ' s/d ' . $filters['tgl_selesai']]);
        fputcsv($out, ['Tanggal Unduh:', date('d/m/Y H:i:s') . ' WIB']);
        fputcsv($out, ['Diunduh Oleh:', session()->get('nama') . ' (' . session()->get('username') . ')']);
        fputcsv($out, []); // blank line

        // Table headers
        fputcsv($out, [
            'No.',
            'No. RM',
            'Nama Pasien',
            'Unit Peminjam',
            'Nama Petugas Peminjam',
            'Tujuan Peminjaman',
            'Tanggal Pinjam',
            'Batas Kembali',
            'Tanggal Kembali',
            'Status',
            'Kondisi Kembali',
            'Catatan Pengembalian',
            'Petugas Pencatat',
        ]);

        $no = 1;
        foreach ($loans as $row) {
            fputcsv($out, [
                $no++,
                $row['no_rm'],
                $row['nama_pasien'],
                $row['nama_unit'],
                $row['nama_peminjam'],
                $row['tujuan'],
                date('d/m/Y H:i', strtotime($row['tgl_pinjam'])),
                date('d/m/Y', strtotime($row['batas_kembali'])),
                $row['tgl_kembali'] ? date('d/m/Y H:i', strtotime($row['tgl_kembali'])) : '-',
                strtoupper($row['status']),
                $row['kondisi_kembali'] ? ($row['kondisi_kembali'] === 'lengkap' ? 'Lengkap' : 'Tidak Lengkap') : '-',
                $row['catatan'] ?? '-',
                $row['nama_petugas_catat'] ?? '-',
            ]);
        }

        fclose($out);
        exit;
    }

    /**
     * Export report to printable formal PDF layout (FR-27)
     */
    public function exportPdf()
    {
        $filters = [
            'tgl_mulai'       => $this->request->getGet('tgl_mulai') ?? date('Y-m-d', strtotime('-30 days')),
            'tgl_selesai'     => $this->request->getGet('tgl_selesai') ?? date('Y-m-d'),
            'unit_id'         => $this->request->getGet('unit_id'),
            'status'          => $this->request->getGet('status'),
            'kondisi_kembali' => $this->request->getGet('kondisi_kembali'),
        ];

        $query = $this->buildFilterQuery($filters);
        $loans = $query->orderBy('peminjaman.tgl_pinjam', 'ASC')->findAll();

        $metrics = $this->calculateMetrics($loans);

        $role = session()->get('role');
        if ($role === 'petugas_unit') {
            foreach ($loans as &$row) {
                $row['nama_pasien'] = $this->maskPatientName($row['nama_pasien']);
            }
        }

        $unitNama = 'Semua Unit';
        if (! empty($filters['unit_id'])) {
            $u = $this->unitModel->find($filters['unit_id']);
            if ($u) {
                $unitNama = $u['nama_unit'];
            }
        }

        // Log export activity (FR-28)
        $this->logModel->logActivity(session()->get('user_id'), 'laporan_export_pdf', "Membuka/Mencetak laporan PDF peminjaman ({$filters['tgl_mulai']} s/d {$filters['tgl_selesai']})");

        return view('laporan/print_pdf', [
            'title'    => 'Laporan Peminjaman Berkas Rekam Medis — TRACKLY',
            'loans'    => $loans,
            'metrics'  => $metrics,
            'filters'  => $filters,
            'unitNama' => $unitNama,
        ]);
    }

    /**
     * Unified router for export: /laporan/export?format=pdf|xlsx
     */
    public function export()
    {
        $format = strtolower((string) $this->request->getGet('format'));
        if ($format === 'pdf') {
            return $this->exportPdf();
        }
        return $this->exportExcel();
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
