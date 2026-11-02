<?php

namespace App\Controllers;

use App\Models\LogModel;
use App\Models\PeminjamanModel;
use App\Models\UnitModel;
use App\Models\UserModel;
use CodeIgniter\Controller;

class UnitController extends Controller
{
    protected UnitModel $unitModel;
    protected LogModel $logModel;
    protected UserModel $userModel;
    protected PeminjamanModel $peminjamanModel;

    public function __construct()
    {
        $this->unitModel       = new UnitModel();
        $this->logModel        = new LogModel();
        $this->userModel       = new UserModel();
        $this->peminjamanModel = new PeminjamanModel();
    }

    /**
     * Display list of units with search and pagination
     */
    public function index()
    {
        $keyword = trim((string) $this->request->getGet('cari'));
        $status  = $this->request->getGet('status'); // 'all', '1', '0'

        $query = $this->unitModel;

        if (! empty($keyword)) {
            $query = $query->groupStart()
                ->like('kode_unit', $keyword)
                ->orLike('nama_unit', $keyword)
            ->groupEnd();
        }

        if ($status !== null && $status !== '' && $status !== 'all') {
            $query = $query->where('is_active', (int) $status);
        }

        $units = $query->orderBy('nama_unit', 'ASC')->paginate(10, 'units');
        $pager = $this->unitModel->pager;

        // Count linked entities for each unit
        $unitStats = [];
        foreach ($units as $u) {
            $unitStats[$u['id']] = [
                'users_count' => $this->userModel->where('unit_id', $u['id'])->countAllResults(),
                'loans_count' => $this->peminjamanModel->where('unit_id', $u['id'])->countAllResults(),
            ];
        }

        return view('unit/index', [
            'title'     => 'Kelola Data Unit Peminjam — TRACKLY',
            'units'     => $units,
            'pager'     => $pager,
            'keyword'   => $keyword,
            'status'    => $status,
            'unitStats' => $unitStats,
        ]);
    }

    /**
     * Store new unit
     */
    public function store()
    {
        $kodeUnit = strtoupper(trim((string) $this->request->getPost('kode_unit')));
        $namaUnit = trim((string) $this->request->getPost('nama_unit'));
        $isActive = $this->request->getPost('is_active') ? 1 : 0;

        if (empty($kodeUnit) || empty($namaUnit)) {
            return redirect()->back()->withInput()->with('error', 'Kode unit dan nama unit wajib diisi.');
        }

        if (strlen($kodeUnit) > 10) {
            return redirect()->back()->withInput()->with('error', 'Kode unit maksimal 10 karakter.');
        }

        if (strlen($namaUnit) > 100) {
            return redirect()->back()->withInput()->with('error', 'Nama unit maksimal 100 karakter.');
        }

        // Check unique kode_unit
        if ($this->unitModel->where('kode_unit', $kodeUnit)->first()) {
            return redirect()->back()->withInput()->with('error', "Kode unit '{$kodeUnit}' sudah digunakan oleh unit lain.");
        }

        $this->unitModel->insert([
            'kode_unit' => $kodeUnit,
            'nama_unit' => $namaUnit,
            'is_active' => $isActive,
        ]);

        $this->logModel->logActivity(
            session()->get('user_id'),
            'tambah_unit',
            "Menambahkan master unit baru: {$namaUnit} ({$kodeUnit})"
        );

        return redirect()->to('/unit')->with('success', "Unit '{$namaUnit}' berhasil ditambahkan.");
    }

    /**
     * Update unit
     */
    public function update(int $id)
    {
        $unit = $this->unitModel->find($id);
        if (! $unit) {
            return redirect()->to('/unit')->with('error', 'Unit tidak ditemukan.');
        }

        $kodeUnit = strtoupper(trim((string) $this->request->getPost('kode_unit')));
        $namaUnit = trim((string) $this->request->getPost('nama_unit'));
        $isActive = $this->request->getPost('is_active') ? 1 : 0;

        if (empty($kodeUnit) || empty($namaUnit)) {
            return redirect()->back()->withInput()->with('error', 'Kode unit dan nama unit wajib diisi.');
        }

        // Check uniqueness excluding current ID
        $existing = $this->unitModel->where('kode_unit', $kodeUnit)->where('id !=', $id)->first();
        if ($existing) {
            return redirect()->back()->withInput()->with('error', "Kode unit '{$kodeUnit}' sudah dipakai oleh unit lain.");
        }

        $this->unitModel->update($id, [
            'kode_unit' => $kodeUnit,
            'nama_unit' => $namaUnit,
            'is_active' => $isActive,
        ]);

        $this->logModel->logActivity(
            session()->get('user_id'),
            'ubah_unit',
            "Memperbarui data unit: {$namaUnit} ({$kodeUnit})"
        );

        return redirect()->to('/unit')->with('success', "Data unit '{$namaUnit}' berhasil diperbarui.");
    }

    /**
     * Toggle active status
     */
    public function toggle(int $id)
    {
        $unit = $this->unitModel->find($id);
        if (! $unit) {
            return redirect()->to('/unit')->with('error', 'Unit tidak ditemukan.');
        }

        $newStatus = $unit['is_active'] ? 0 : 1;
        $this->unitModel->update($id, ['is_active' => $newStatus]);

        $statusText = $newStatus ? 'diaktifkan' : 'dinonaktifkan';
        $this->logModel->logActivity(
            session()->get('user_id'),
            'status_unit',
            "Mengubah status unit {$unit['nama_unit']} menjadi {$statusText}"
        );

        return redirect()->to('/unit')->with('success', "Unit '{$unit['nama_unit']}' berhasil {$statusText}.");
    }

    /**
     * Delete unit (only if safe)
     */
    public function delete(int $id)
    {
        $unit = $this->unitModel->find($id);
        if (! $unit) {
            return redirect()->to('/unit')->with('error', 'Unit tidak ditemukan.');
        }

        // Check if unit is associated with any loan
        $linkedLoans = $this->peminjamanModel->where('unit_id', $id)->countAllResults();
        if ($linkedLoans > 0) {
            return redirect()->to('/unit')->with('error', "Unit '{$unit['nama_unit']}' tidak dapat dihapus karena memiliki {$linkedLoans} riwayat transaksi peminjaman. Silakan nonaktifkan unit jika sudah tidak digunakan.");
        }

        // Check if unit has users
        $linkedUsers = $this->userModel->where('unit_id', $id)->countAllResults();
        if ($linkedUsers > 0) {
            return redirect()->to('/unit')->with('error', "Unit '{$unit['nama_unit']}' tidak dapat dihapus karena memiliki {$linkedUsers} akun pengguna terkait.");
        }

        $this->unitModel->delete($id);

        $this->logModel->logActivity(
            session()->get('user_id'),
            'hapus_unit',
            "Menghapus unit: {$unit['nama_unit']} ({$unit['kode_unit']})"
        );

        return redirect()->to('/unit')->with('success', "Unit '{$unit['nama_unit']}' berhasil dihapus.");
    }
}
