<?php

namespace App\Controllers;

use App\Models\BerkasModel;
use App\Models\LogModel;
use App\Models\PeminjamanModel;

class BerkasController extends BaseController
{
    protected BerkasModel $berkasModel;
    protected PeminjamanModel $peminjamanModel;
    protected LogModel $logModel;

    public function __construct()
    {
        $this->berkasModel     = new BerkasModel();
        $this->peminjamanModel = new PeminjamanModel();
        $this->logModel        = new LogModel();
    }

    /**
     * List medical records with search and filter
     */
    public function index()
    {
        $keyword = trim((string) $this->request->getGet('cari'));
        $status  = $this->request->getGet('status');

        $query = $this->berkasModel;

        if (! empty($keyword)) {
            $query = $query->groupStart()
                ->like('no_rm', $keyword)
                ->orLike('nama_pasien', $keyword)
                ->orLike('lokasi_rak', $keyword)
            ->groupEnd();
        }

        if (! empty($status) && $status !== 'all') {
            $query = $query->where('status', $status);
        }

        $berkas = $query->orderBy('no_rm', 'ASC')->paginate(10, 'berkas');
        $pager  = $this->berkasModel->pager;

        // If file is borrowed, fetch active loan info for quick display
        $activeLoans = [];
        foreach ($berkas as $b) {
            if ($b['status'] === 'dipinjam') {
                $loan = $this->peminjamanModel->select('peminjaman.*, units.nama_unit')
                    ->join('units', 'units.id = peminjaman.unit_id')
                    ->where('peminjaman.berkas_id', $b['id'])
                    ->whereIn('peminjaman.status', ['dipinjam', 'terlambat'])
                    ->first();
                if ($loan) {
                    $activeLoans[$b['id']] = $loan;
                }
            }
        }

        return view('berkas/index', [
            'title'       => 'Data Berkas Rekam Medis — TRACKLY',
            'berkas'      => $berkas,
            'pager'       => $pager,
            'keyword'     => $keyword,
            'status'      => $status,
            'activeLoans' => $activeLoans,
            'userRole'    => session()->get('role'),
        ]);
    }

    /**
     * Store new medical record file
     */
    public function store()
    {
        $noRm       = trim((string) $this->request->getPost('no_rm'));
        $namaPasien = trim((string) $this->request->getPost('nama_pasien'));
        $tahunBuka  = (int) $this->request->getPost('tahun_buka');
        $lokasiRak  = trim((string) $this->request->getPost('lokasi_rak'));

        // Basic validations (Section 11.2)
        if (empty($noRm) || empty($namaPasien) || empty($tahunBuka) || empty($lokasiRak)) {
            return redirect()->back()->withInput()->with('error', 'Semua bidang wajib diisi.');
        }

        // Format No. RM: only numbers and hyphens
        if (! preg_match('/^[0-9\-]+$/', $noRm)) {
            return redirect()->back()->withInput()->with('error', 'Nomor Rekam Medis hanya boleh memuat angka dan tanda hubung (misal: 00-12-34).');
        }

        if (strlen($noRm) > 20) {
            return redirect()->back()->withInput()->with('error', 'Nomor Rekam Medis maksimal 20 karakter.');
        }

        // Check unique No. RM
        if ($this->berkasModel->findByNoRm($noRm)) {
            return redirect()->back()->withInput()->with('error', "Nomor RM '{$noRm}' sudah terdaftar dalam sistem.");
        }

        $currentYear = (int) date('Y');
        if ($tahunBuka < 1900 || $tahunBuka > $currentYear) {
            return redirect()->back()->withInput()->with('error', "Tahun buka berkas harus 4 digit angka dan tidak boleh melebihi tahun berjalan ({$currentYear}).");
        }

        if (strlen($lokasiRak) > 30) {
            return redirect()->back()->withInput()->with('error', 'Lokasi rak penyimpanan maksimal 30 karakter.');
        }

        // Generate unique 36-char QR code token (UUID v4 format)
        $kodeQr = sprintf(
            'trc-%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            random_int(0, 0xffff), random_int(0, 0xffff),
            random_int(0, 0xffff),
            random_int(0, 0x0fff) | 0x4000,
            random_int(0, 0x3fff) | 0x8000,
            random_int(0, 0xffff), random_int(0, 0xffff), random_int(0, 0xffff)
        );

        $this->berkasModel->insert([
            'no_rm'       => $noRm,
            'nama_pasien' => $namaPasien,
            'tahun_buka'  => $tahunBuka,
            'lokasi_rak'  => $lokasiRak,
            'kode_qr'     => $kodeQr,
            'status'      => 'tersedia',
        ]);

        $this->logModel->logActivity(
            session()->get('user_id'),
            'tambah_berkas',
            "Registrasi berkas rekam medis baru: No. RM {$noRm} a/n {$namaPasien}"
        );

        return redirect()->to('/berkas')->with('success', "Berkas RM '{$noRm}' a/n {$namaPasien} berhasil didaftarkan.");
    }

    /**
     * Show detail of a single medical record file
     */
    public function show(int $id)
    {
        $berkas = $this->berkasModel->find($id);
        if (! $berkas) {
            return redirect()->to('/berkas')->with('error', 'Berkas rekam medis tidak ditemukan.');
        }

        // Active loan if currently borrowed
        $activeLoan = null;
        if ($berkas['status'] === 'dipinjam') {
            $activeLoan = $this->peminjamanModel->select('peminjaman.*, units.nama_unit, units.kode_unit, users.nama as nama_petugas')
                ->join('units', 'units.id = peminjaman.unit_id')
                ->join('users', 'users.id = peminjaman.dicatat_oleh')
                ->where('peminjaman.berkas_id', $id)
                ->whereIn('peminjaman.status', ['dipinjam', 'terlambat'])
                ->first();
        }

        // Recent borrowing history of this file
        $history = $this->peminjamanModel->select('peminjaman.*, units.nama_unit, users.nama as nama_petugas')
            ->join('units', 'units.id = peminjaman.unit_id')
            ->join('users', 'users.id = peminjaman.dicatat_oleh')
            ->where('peminjaman.berkas_id', $id)
            ->orderBy('peminjaman.tgl_pinjam', 'DESC')
            ->limit(10)
            ->findAll();

        return view('berkas/show', [
            'title'      => "Detail Berkas RM {$berkas['no_rm']} — TRACKLY",
            'berkas'     => $berkas,
            'activeLoan' => $activeLoan,
            'history'    => $history,
            'userRole'   => session()->get('role'),
        ]);
    }

    /**
     * Update medical record file
     */
    public function update(int $id)
    {
        $berkas = $this->berkasModel->find($id);
        if (! $berkas) {
            return redirect()->to('/berkas')->with('error', 'Berkas rekam medis tidak ditemukan.');
        }

        $noRm       = trim((string) $this->request->getPost('no_rm'));
        $namaPasien = trim((string) $this->request->getPost('nama_pasien'));
        $tahunBuka  = (int) $this->request->getPost('tahun_buka');
        $lokasiRak  = trim((string) $this->request->getPost('lokasi_rak'));

        if (empty($namaPasien) || empty($tahunBuka) || empty($lokasiRak)) {
            return redirect()->back()->withInput()->with('error', 'Nama pasien, tahun buka, dan lokasi rak wajib diisi.');
        }

        $currentYear = (int) date('Y');
        if ($tahunBuka < 1900 || $tahunBuka > $currentYear) {
            return redirect()->back()->withInput()->with('error', "Tahun buka tidak boleh melebihi {$currentYear}.");
        }

        $updateData = [
            'nama_pasien' => $namaPasien,
            'tahun_buka'  => $tahunBuka,
            'lokasi_rak'  => $lokasiRak,
        ];

        // If no_rm is provided and changed, validate and update it
        if (! empty($noRm) && $noRm !== $berkas['no_rm']) {
            if (! preg_match('/^[0-9\-]+$/', $noRm)) {
                return redirect()->back()->withInput()->with('error', 'Nomor Rekam Medis hanya boleh memuat angka dan tanda hubung.');
            }
            if (strlen($noRm) > 20) {
                return redirect()->back()->withInput()->with('error', 'Nomor Rekam Medis maksimal 20 karakter.');
            }
            $existing = $this->berkasModel->findByNoRm($noRm);
            if ($existing && (int)$existing['id'] !== $id) {
                return redirect()->back()->withInput()->with('error', "Nomor RM '{$noRm}' sudah digunakan oleh berkas lain.");
            }
            $updateData['no_rm'] = $noRm;
        }

        $this->berkasModel->update($id, $updateData);

        $finalNoRm = $updateData['no_rm'] ?? $berkas['no_rm'];
        $this->logModel->logActivity(
            session()->get('user_id'),
            'ubah_berkas',
            "Memperbarui data berkas No. RM {$finalNoRm} a/n {$namaPasien}"
        );

        return redirect()->back()->with('success', "Data berkas RM '{$finalNoRm}' a/n {$namaPasien} berhasil diperbarui.");
    }

    /**
     * Delete medical record file
     */
    public function delete(int $id)
    {
        $berkas = $this->berkasModel->find($id);
        if (! $berkas) {
            return redirect()->to('/berkas')->with('error', 'Berkas rekam medis tidak ditemukan.');
        }

        if ($berkas['status'] === 'dipinjam') {
            return redirect()->to('/berkas')->with('error', "Berkas No. RM '{$berkas['no_rm']}' sedang dipinjam dan tidak dapat dihapus.");
        }

        $loanCount = $this->peminjamanModel->where('berkas_id', $id)->countAllResults();
        if ($loanCount > 0) {
            return redirect()->to('/berkas')->with('error', "Berkas No. RM '{$berkas['no_rm']}' memiliki {$loanCount} riwayat transaksi peminjaman (audit trail) sehingga tidak dapat dihapus.");
        }

        $this->berkasModel->delete($id);

        $this->logModel->logActivity(
            session()->get('user_id'),
            'hapus_berkas',
            "Menghapus berkas No. RM {$berkas['no_rm']} a/n {$berkas['nama_pasien']}"
        );

        return redirect()->to('/berkas')->with('success', "Berkas RM {$berkas['no_rm']} berhasil dihapus.");
    }

    /**
     * Print QR label for single medical record (FR-07)
     */
    public function printQr(int $id)
    {
        $berkas = $this->berkasModel->find($id);
        if (! $berkas) {
            return redirect()->to('/berkas')->with('error', 'Berkas rekam medis tidak ditemukan.');
        }

        return view('berkas/print_qr', [
            'title'  => "Cetak Label QR — No. RM {$berkas['no_rm']}",
            'berkas' => [$berkas],
            'mode'   => 'satuan',
        ]);
    }

    /**
     * Print QR labels for multiple/all medical records (FR-07)
     * Accepts comma-separated IDs or 'all'
     */
    public function printQrMassal()
    {
        $ids    = $this->request->getGet('ids');
        $status = $this->request->getGet('status');

        if ($ids === 'all') {
            $query = $this->berkasModel;
            if (! empty($status) && $status !== 'all') {
                $query = $query->where('status', $status);
            }
            $berkas = $query->orderBy('no_rm', 'ASC')->findAll();
        } elseif (! empty($ids)) {
            $idArr  = array_filter(array_map('intval', explode(',', $ids)));
            $berkas = $this->berkasModel->whereIn('id', $idArr)->orderBy('no_rm', 'ASC')->findAll();
        } else {
            return redirect()->to('/berkas')->with('error', 'Pilih berkas yang akan dicetak terlebih dahulu.');
        }

        if (empty($berkas)) {
            return redirect()->to('/berkas')->with('error', 'Tidak ada berkas yang ditemukan untuk dicetak.');
        }

        return view('berkas/print_qr', [
            'title'  => 'Cetak Label QR Massal — TRACKLY',
            'berkas' => $berkas,
            'mode'   => 'massal',
        ]);
    }

    /**
     * Regenerate QR code for a medical record (FR-10 - Admin only)
     */
    public function regenerateQr(int $id)
    {
        $berkas = $this->berkasModel->find($id);
        if (! $berkas) {
            return redirect()->to('/berkas')->with('error', 'Berkas rekam medis tidak ditemukan.');
        }

        // Generate new unique QR token
        $kodeQrBaru = sprintf(
            'trc-%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            random_int(0, 0xffff), random_int(0, 0xffff),
            random_int(0, 0xffff),
            random_int(0, 0x0fff) | 0x4000,
            random_int(0, 0x3fff) | 0x8000,
            random_int(0, 0xffff), random_int(0, 0xffff), random_int(0, 0xffff)
        );

        $this->berkasModel->update($id, ['kode_qr' => $kodeQrBaru]);

        $this->logModel->logActivity(
            session()->get('user_id'),
            'regenerate_qr',
            "Membuat ulang QR Code berkas No. RM {$berkas['no_rm']} — kode lama tidak berlaku."
        );

        return redirect()->to('/berkas/' . $id)->with('success', "QR Code berkas No. RM '{$berkas['no_rm']}' berhasil dibuat ulang. Kode lama tidak berlaku lagi.");
    }

    /**
     * Public scan redirect — when QR is scanned with camera app
     */
    public function scanRedirect(string $kodeQr)
    {
        if (! session()->get('is_logged_in')) {
            session()->setFlashdata('info', 'Silakan login untuk melihat informasi berkas.');
            return redirect()->to('/login');
        }
        $berkas = $this->berkasModel->findByQr($kodeQr);
        if (! $berkas) {
            return redirect()->to('/berkas')->with('error', 'QR Code tidak dikenali atau sudah tidak berlaku.');
        }
        return redirect()->to('/berkas/' . $berkas['id']);
    }

    /**
     * Download CSV template for bulk medical record import (FR-09)
     */
    public function downloadTemplate()
    {
        $filename = 'template_impor_berkas_rm.csv';

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $out = fopen('php://output', 'w');
        // UTF-8 BOM
        fputs($out, "\xEF\xBB\xBF");

        // Headers
        fputcsv($out, ['no_rm', 'nama_pasien', 'tahun_buka', 'lokasi_rak']);

        // Sample rows
        fputcsv($out, ['00-90-01', 'Siti Rahmawati', '2023', 'Rak A-01']);
        fputcsv($out, ['00-90-02', 'Bambang Kusuma', '2022', 'Rak A-02']);
        fputcsv($out, ['00-90-03', 'Dewi Lestari', '2024', 'Rak B-01']);

        fclose($out);
        exit;
    }

    /**
     * Process bulk medical record import from CSV/Excel (FR-09)
     */
    public function importCsv()
    {
        $file = $this->request->getFile('file_csv');

        if (! $file || ! $file->isValid()) {
            return redirect()->to('/berkas')->with('error', 'Silakan pilih file CSV/Excel yang valid untuk diunggah.');
        }

        $ext = strtolower($file->getClientExtension());
        if (! in_array($ext, ['csv', 'txt'])) {
            return redirect()->to('/berkas')->with('error', 'Format file tidak didukung. Harap unggah file berekstensi .csv');
        }

        $filepath = $file->getTempName();
        $handle   = fopen($filepath, 'r');
        if (! $handle) {
            return redirect()->to('/berkas')->with('error', 'Gagal membaca file yang diunggah.');
        }

        // Read first line to detect BOM and delimiter
        $firstLine = fgets($handle);
        // Remove BOM if present
        $firstLine = preg_replace('/^\xEF\xBB\xBF/', '', $firstLine);
        $delimiter = strpos($firstLine, ';') !== false ? ';' : ',';

        // Parse header
        $header = str_getcsv(trim($firstLine), $delimiter);
        $header = array_map(fn($h) => strtolower(trim($h)), $header);

        $colNoRm   = array_search('no_rm', $header);
        $colNama   = array_search('nama_pasien', $header);
        $colTahun  = array_search('tahun_buka', $header);
        $colRak    = array_search('lokasi_rak', $header);

        if ($colNoRm === false || $colNama === false || $colTahun === false || $colRak === false) {
            fclose($handle);
            return redirect()->to('/berkas')->with('error', 'Format header file CSV tidak sesuai. Kolom wajib: no_rm, nama_pasien, tahun_buka, lokasi_rak.');
        }

        $successCount = 0;
        $skipCount    = 0;
        $errors       = [];
        $rowNum       = 1;
        $batchNoRm    = [];

        $currentYear = (int) date('Y');

        while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
            $rowNum++;

            // Check empty line
            if (empty(array_filter($row))) {
                continue;
            }

            $noRm       = trim($row[$colNoRm] ?? '');
            $namaPasien = trim($row[$colNama] ?? '');
            $tahunBuka  = (int) trim($row[$colTahun] ?? '');
            $lokasiRak  = trim($row[$colRak] ?? '');

            // Validations
            if (empty($noRm) || empty($namaPasien) || empty($lokasiRak)) {
                $skipCount++;
                $errors[] = "Baris {$rowNum}: Data tidak lengkap (No. RM, Nama, atau Lokasi Rak kosong).";
                continue;
            }

            if ($tahunBuka < 1900 || $tahunBuka > ($currentYear + 1)) {
                $skipCount++;
                $errors[] = "Baris {$rowNum} (RM: {$noRm}): Tahun buka tidak valid ({$tahunBuka}).";
                continue;
            }

            // Check duplicate in current batch
            if (in_array($noRm, $batchNoRm)) {
                $skipCount++;
                $errors[] = "Baris {$rowNum}: No. RM '{$noRm}' duplikat dalam file yang diunggah.";
                continue;
            }

            // Check duplicate in database
            $existing = $this->berkasModel->findByNoRm($noRm);
            if ($existing) {
                $skipCount++;
                $errors[] = "Baris {$rowNum}: No. RM '{$noRm}' sudah terdaftar dalam sistem.";
                continue;
            }

            // Generate unique QR code
            $kodeQr = sprintf(
                'trc-%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                random_int(0, 0xffff), random_int(0, 0xffff),
                random_int(0, 0xffff),
                random_int(0, 0x0fff) | 0x4000,
                random_int(0, 0x3fff) | 0x8000,
                random_int(0, 0xffff), random_int(0, 0xffff), random_int(0, 0xffff)
            );

            $this->berkasModel->insert([
                'no_rm'       => $noRm,
                'nama_pasien' => $namaPasien,
                'tahun_buka'  => $tahunBuka,
                'lokasi_rak'  => $lokasiRak,
                'kode_qr'     => $kodeQr,
                'status'      => 'tersedia',
            ]);

            $batchNoRm[] = $noRm;
            $successCount++;
        }

        fclose($handle);

        // Audit log (FR-28)
        $this->logModel->logActivity(
            session()->get('user_id'),
            'impor_berkas',
            "Mengimpor berkas rekam medis massal dari CSV: {$successCount} berhasil, {$skipCount} dilewati."
        );

        $msg = "Impor selesai: <strong>{$successCount} berkas</strong> berhasil ditambahkan.";
        if ($skipCount > 0) {
            $msg .= " Sebanyak {$skipCount} baris dilewati/gagal.";
        }

        if (! empty($errors) && count($errors) <= 5) {
            session()->setFlashdata('import_warnings', $errors);
        }

        return redirect()->to('/berkas')->with('success', $msg);
    }
}

