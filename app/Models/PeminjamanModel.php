<?php

namespace App\Models;

use CodeIgniter\Model;

class PeminjamanModel extends Model
{
    protected $table            = 'peminjaman';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'berkas_id',
        'unit_id',
        'dicatat_oleh',
        'nama_peminjam',
        'tujuan',
        'tgl_pinjam',
        'batas_kembali',
        'tgl_kembali',
        'kondisi_kembali',
        'catatan',
        'status',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Update active loans whose deadline has passed to 'terlambat'
     */
    public function updateOverdueStatus(): int
    {
        $today = date('Y-m-d');
        return $this->where('status', 'dipinjam')
            ->where('batas_kembali <', $today)
            ->set(['status' => 'terlambat', 'updated_at' => date('Y-m-d H:i:s')])
            ->update();
    }

    /**
     * Get 7-day trend (Loans vs Returns)
     */
    public function get7DaysTrend(?int $unitId = null): array
    {
        $days = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-{$i} days"));
            $days[$date] = [
                'date'     => $date,
                'label'    => date('d M', strtotime($date)),
                'pinjam'   => 0,
                'kembali'  => 0,
            ];
        }

        // Query loans in last 7 days
        $startDate = date('Y-m-d 00:00:00', strtotime('-6 days'));
        $loanQuery = $this->select("DATE(tgl_pinjam) as tanggal, COUNT(*) as total")
            ->where('tgl_pinjam >=', $startDate)
            ->groupBy('DATE(tgl_pinjam)');
        if ($unitId) {
            $loanQuery->where('unit_id', $unitId);
        }
        $loans = $loanQuery->findAll();
        foreach ($loans as $l) {
            if (isset($days[$l['tanggal']])) {
                $days[$l['tanggal']]['pinjam'] = (int) $l['total'];
            }
        }

        // Query returns in last 7 days
        $returnQuery = $this->select("DATE(tgl_kembali) as tanggal, COUNT(*) as total")
            ->where('tgl_kembali IS NOT NULL')
            ->where('tgl_kembali >=', $startDate)
            ->groupBy('DATE(tgl_kembali)');
        if ($unitId) {
            $returnQuery->where('unit_id', $unitId);
        }
        $returns = $returnQuery->findAll();
        foreach ($returns as $r) {
            if (isset($days[$r['tanggal']])) {
                $days[$r['tanggal']]['kembali'] = (int) $r['total'];
            }
        }

        return array_values($days);
    }

    /**
     * Get Top Borrowing Units
     */
    public function getUnitLoanDistribution(int $limit = 6): array
    {
        return $this->select('units.nama_unit, units.kode_unit, COUNT(peminjaman.id) as total')
            ->join('units', 'units.id = peminjaman.unit_id')
            ->groupBy('peminjaman.unit_id')
            ->orderBy('total', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Get summary of units that currently have overdue loans
     */
    public function getOverdueByUnitSummary(): array
    {
        return $this->select('units.nama_unit, units.kode_unit, COUNT(peminjaman.id) as total_terlambat')
            ->join('units', 'units.id = peminjaman.unit_id')
            ->where('peminjaman.status', 'terlambat')
            ->groupBy('peminjaman.unit_id')
            ->orderBy('total_terlambat', 'DESC')
            ->findAll();
    }

    /**
     * Get recent transactions (loans & returns)
     */
    public function getRecentTransactions(int $limit = 6, ?int $unitId = null): array
    {
        $q = $this->select('peminjaman.*, berkas_rm.no_rm, berkas_rm.nama_pasien, units.nama_unit, users.nama as nama_petugas_catat')
            ->join('berkas_rm', 'berkas_rm.id = peminjaman.berkas_id')
            ->join('units', 'units.id = peminjaman.unit_id')
            ->join('users', 'users.id = peminjaman.dicatat_oleh', 'left');

        if ($unitId) {
            $q->where('peminjaman.unit_id', $unitId);
        }

        return $q->orderBy('peminjaman.id', 'DESC')->limit($limit)->findAll();
    }
}
