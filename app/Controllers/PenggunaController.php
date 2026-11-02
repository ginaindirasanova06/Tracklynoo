<?php

namespace App\Controllers;

use App\Models\LogModel;
use App\Models\PeminjamanModel;
use App\Models\UnitModel;
use App\Models\UserModel;
use CodeIgniter\Controller;

class PenggunaController extends Controller
{
    protected UserModel $userModel;
    protected UnitModel $unitModel;
    protected LogModel $logModel;
    protected PeminjamanModel $peminjamanModel;

    public function __construct()
    {
        $this->userModel       = new UserModel();
        $this->unitModel       = new UnitModel();
        $this->logModel        = new LogModel();
        $this->peminjamanModel = new PeminjamanModel();
    }

    /**
     * List all users with search, role filter, and unit relation
     */
    public function index()
    {
        $keyword = trim((string) $this->request->getGet('cari'));
        $role    = $this->request->getGet('role');
        $status  = $this->request->getGet('status');

        $query = $this->userModel->select('users.*, units.nama_unit, units.kode_unit')
            ->join('units', 'units.id = users.unit_id', 'left');

        if (! empty($keyword)) {
            $query = $query->groupStart()
                ->like('users.nama', $keyword)
                ->orLike('users.username', $keyword)
                ->orLike('users.email', $keyword)
            ->groupEnd();
        }

        if (! empty($role) && $role !== 'all') {
            $query = $query->where('users.role', $role);
        }

        if ($status !== null && $status !== '' && $status !== 'all') {
            $query = $query->where('users.is_active', (int) $status);
        }

        $users = $query->orderBy('users.id', 'ASC')->paginate(10, 'users');
        $pager = $this->userModel->pager;

        $units = $this->unitModel->where('is_active', 1)->orderBy('nama_unit', 'ASC')->findAll();

        return view('pengguna/index', [
            'title'   => 'Manajemen Pengguna — TRACKLY',
            'users'   => $users,
            'pager'   => $pager,
            'units'   => $units,
            'keyword' => $keyword,
            'role'    => $role,
            'status'  => $status,
        ]);
    }

    /**
     * Store new user
     */
    public function store()
    {
        $nama     = trim((string) $this->request->getPost('nama'));
        $username = strtolower(trim((string) $this->request->getPost('username')));
        $email    = strtolower(trim((string) $this->request->getPost('email')));
        $password = (string) $this->request->getPost('password');
        $role     = (string) $this->request->getPost('role');
        $unitId   = $this->request->getPost('unit_id') ? (int) $this->request->getPost('unit_id') : null;
        $isActive = $this->request->getPost('is_active') ? 1 : 0;

        // Basic validations
        if (empty($nama) || empty($username) || empty($email) || empty($password) || empty($role)) {
            return redirect()->back()->withInput()->with('error', 'Semua bidang bertanda bintang wajib diisi.');
        }

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return redirect()->back()->withInput()->with('error', 'Format alamat email tidak valid.');
        }

        // Role 'petugas_unit' must have unit_id (Section 11.5)
        if ($role === 'petugas_unit' && empty($unitId)) {
            return redirect()->back()->withInput()->with('error', 'Petugas Unit wajib memilih Unit asal peminjam.');
        }

        // Check password requirements (min 8 chars, letter + number)
        if (strlen($password) < 8 || ! preg_match('/[A-Za-z]/', $password) || ! preg_match('/[0-9]/', $password)) {
            return redirect()->back()->withInput()->with('error', 'Kata sandi minimal 8 karakter dan harus memuat perpaduan huruf dan angka.');
        }

        // Check unique username
        if ($this->userModel->where('username', $username)->first()) {
            return redirect()->back()->withInput()->with('error', "Username '{$username}' sudah digunakan.");
        }

        // Check unique email
        if ($this->userModel->where('email', $email)->first()) {
            return redirect()->back()->withInput()->with('error', "Email '{$email}' sudah terdaftar.");
        }

        $this->userModel->insert([
            'nama'                 => $nama,
            'username'             => $username,
            'email'                => $email,
            'password'             => password_hash($password, PASSWORD_BCRYPT),
            'role'                 => $role,
            'unit_id'              => $role === 'petugas_unit' ? $unitId : null,
            'is_active'            => $isActive,
            'failed_login_count'   => 0,
            'locked_until'         => null,
            'must_change_password' => 0, // Admin has set the password; user can log in directly
            'password_changed_at'  => date('Y-m-d H:i:s'),
        ]);

        $this->logModel->logActivity(
            session()->get('user_id'),
            'tambah_pengguna',
            "Membuat akun pengguna baru: {$nama} ({$username}) dengan peran {$role}"
        );

        return redirect()->to(base_url('pengguna'))->with('success', "Pengguna '{$nama}' ({$username}) berhasil didaftarkan dan dapat langsung digunakan untuk login.");
    }

    /**
     * Update user details
     */
    public function update(int $id)
    {
        $user = $this->userModel->find($id);
        if (! $user) {
            return redirect()->to('/pengguna')->with('error', 'Pengguna tidak ditemukan.');
        }

        $nama     = trim((string) $this->request->getPost('nama'));
        $email    = strtolower(trim((string) $this->request->getPost('email')));
        $role     = (string) $this->request->getPost('role');
        $unitId   = $this->request->getPost('unit_id') ? (int) $this->request->getPost('unit_id') : null;
        $isActive = $this->request->getPost('is_active') ? 1 : 0;

        if (empty($nama) || empty($email) || empty($role)) {
            return redirect()->back()->withInput()->with('error', 'Nama, email, dan peran wajib diisi.');
        }

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return redirect()->back()->withInput()->with('error', 'Format email tidak valid.');
        }

        if ($role === 'petugas_unit' && empty($unitId)) {
            return redirect()->back()->withInput()->with('error', 'Petugas Unit wajib memilih Unit asal peminjam.');
        }

        // Check unique email excluding current ID
        $existing = $this->userModel->where('email', $email)->where('id !=', $id)->first();
        if ($existing) {
            return redirect()->back()->withInput()->with('error', "Email '{$email}' sudah dipakai oleh akun lain.");
        }

        // Protect against deactivating the currently logged in admin
        if ($id === (int) session()->get('user_id') && ! $isActive) {
            return redirect()->back()->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
        }

        $this->userModel->update($id, [
            'nama'      => $nama,
            'email'     => $email,
            'role'      => $role,
            'unit_id'   => $role === 'petugas_unit' ? $unitId : null,
            'is_active' => $isActive,
        ]);

        $this->logModel->logActivity(
            session()->get('user_id'),
            'ubah_pengguna',
            "Memperbarui profil pengguna {$user['username']}"
        );

        return redirect()->to('/pengguna')->with('success', "Data pengguna '{$user['username']}' berhasil diperbarui.");
    }

    /**
     * Reset password by Admin (PRD Section 7.7, 1010)
     * Generates temporary password and sets must_change_password = 1
     */
    public function resetSandi(int $id)
    {
        $user = $this->userModel->find($id);
        if (! $user) {
            return redirect()->to('/pengguna')->with('error', 'Pengguna tidak ditemukan.');
        }

        // Generate readable temporary password: e.g. Trackly! + 4 random digits
        $tempPassword = 'Trk' . random_int(1000, 9999) . '!';

        $this->userModel->update($id, [
            'password'             => password_hash($tempPassword, PASSWORD_BCRYPT),
            'must_change_password' => 1,
            'failed_login_count'   => 0,
            'locked_until'         => null,
            'password_changed_at'  => date('Y-m-d H:i:s'),
        ]);

        $this->logModel->logActivity(
            session()->get('user_id'),
            'admin_reset_sandi',
            "Admin mereset kata sandi pengguna {$user['username']}. Sandi sementara dibuat."
        );

        return redirect()->to('/pengguna')->with('success_reset_info', [
            'username' => $user['username'],
            'nama'     => $user['nama'],
            'password' => $tempPassword,
        ]);
    }

    /**
     * Unlock account manually
     */
    public function unlock(int $id)
    {
        $user = $this->userModel->find($id);
        if (! $user) {
            return redirect()->to('/pengguna')->with('error', 'Pengguna tidak ditemukan.');
        }

        $this->userModel->update($id, [
            'locked_until'       => null,
            'failed_login_count' => 0,
        ]);

        $this->logModel->logActivity(
            session()->get('user_id'),
            'buka_kunci_akun',
            "Admin membuka kunci akun {$user['username']}"
        );

        return redirect()->to('/pengguna')->with('success', "Akun '{$user['username']}' berhasil dibuka kuncinya.");
    }

    /**
     * Toggle active/inactive
     */
    public function toggle(int $id)
    {
        if ($id === (int) session()->get('user_id')) {
            return redirect()->to('/pengguna')->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
        }

        $user = $this->userModel->find($id);
        if (! $user) {
            return redirect()->to('/pengguna')->with('error', 'Pengguna tidak ditemukan.');
        }

        $newStatus = $user['is_active'] ? 0 : 1;
        $this->userModel->update($id, ['is_active' => $newStatus]);

        $statusText = $newStatus ? 'diaktifkan' : 'dinonaktifkan';
        $this->logModel->logActivity(
            session()->get('user_id'),
            'status_pengguna',
            "Mengubah status akun {$user['username']} menjadi {$statusText}"
        );

        return redirect()->to('/pengguna')->with('success', "Akun '{$user['username']}' berhasil {$statusText}.");
    }

    /**
     * Delete user (only if no transactions linked)
     */
    public function delete(int $id)
    {
        if ($id === (int) session()->get('user_id')) {
            return redirect()->to('/pengguna')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user = $this->userModel->find($id);
        if (! $user) {
            return redirect()->to('/pengguna')->with('error', 'Pengguna tidak ditemukan.');
        }

        // Check if user has recorded loans
        $recordedLoans = $this->peminjamanModel->where('dicatat_oleh', $id)->countAllResults();
        if ($recordedLoans > 0) {
            return redirect()->to('/pengguna')->with('error', "Pengguna '{$user['nama']}' tidak dapat dihapus karena tercatat mencatat {$recordedLoans} transaksi peminjaman (audit trail). Silakan nonaktifkan akun sebagai alternatif.");
        }

        $this->userModel->delete($id);

        $this->logModel->logActivity(
            session()->get('user_id'),
            'hapus_pengguna',
            "Menghapus akun pengguna: {$user['username']} ({$user['nama']})"
        );

        return redirect()->to('/pengguna')->with('success', "Pengguna '{$user['username']}' berhasil dihapus.");
    }
}
