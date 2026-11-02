<?php

namespace App\Controllers;

use App\Models\BerkasModel;
use App\Models\PeminjamanModel;
use App\Models\UnitModel;
use App\Models\LogModel;

class PengembalianController extends BaseController
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

    public function scan()
    {
        return view('pengembalian/scan', [
            'title' => 'Scanner Pengembalian � TRACKLY',
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
                'error'        => "QR Code terbaca ('{$preview}'), namun berkas ini tidak terdaftar di database TRACKLY. Silakan gunakan No. RM berkas."
            ]);
        }
        return $this->buildReturnPayload($berkas);
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
            return $this->response->setJSON(['found' => false, 'error' => "Berkas dengan No. RM '{$noRm}' tidak ditemukan."]);
        }
        return $this->buildReturnPayload($berkas);
    }

    private function buildReturnPayload(array $berkas)
    {
        $activeLoan = null;
        if (in_array($berkas['status'], ['dipinjam', 'terlambat'])) {
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
                'status'      => $berkas['status'],
            ],
            'activeLoan' => $activeLoan,
        ]);
    }

    public function store()
    {
        $peminjamanId   = (int) $this->request->getPost('peminjaman_id');
        $kondisiKembali = trim((string) $this->request->getPost('kondisi_kembali'));
        $catatan        = trim((string) $this->request->getPost('catatan'));

        if (! $peminjamanId || empty($kondisiKembali)) {
            return redirect()->to('/pengembalian/scan')->with('error', 'Data pengembalian tidak lengkap.');
        }

        $peminjaman = $this->peminjamanModel->find($peminjamanId);
        if (! $peminjaman) {
            return redirect()->to('/pengembalian/scan')->with('error', 'Data peminjaman tidak ditemukan.');
        }

        if (! in_array($peminjaman['status'], ['dipinjam', 'terlambat'])) {
            return redirect()->to('/pengembalian/scan')->with('error', 'Berkas ini tidak sedang dipinjam.');
        }

        $validKondisi = ['lengkap', 'tidak_lengkap'];
        if (! in_array($kondisiKembali, $validKondisi)) {
            return redirect()->to('/pengembalian/scan')->with('error', 'Kondisi kembali tidak valid.');
        }

        // BR-05: catatan wajib jika tidak lengkap
        if ($kondisiKembali === 'tidak_lengkap' && empty($catatan)) {
            return redirect()->to('/pengembalian/scan')->with('error', 'Catatan wajib diisi jika kondisi berkas tidak lengkap.');
        }

        $berkas = $this->berkasModel->find($peminjaman['berkas_id']);

        // Update peminjaman
        $this->peminjamanModel->update($peminjamanId, [
            'tgl_kembali'    => date('Y-m-d H:i:s'),
            'kondisi_kembali' => $kondisiKembali,
            'catatan'        => $catatan ?: null,
            'status'         => 'dikembalikan',
        ]);

        // Update status berkas menjadi tersedia (FR-17)
        $this->berkasModel->update($peminjaman['berkas_id'], ['status' => 'tersedia']);

        $kondisiLabel = $kondisiKembali === 'lengkap' ? 'Lengkap' : "Tidak Lengkap ({$catatan})";
        $this->logModel->logActivity(
            session()->get('user_id'),
            'kembali_berkas',
            "Mencatat pengembalian berkas No. RM {$berkas['no_rm']} a/n {$berkas['nama_pasien']}, kondisi: {$kondisiLabel}"
        );

        return redirect()->to('/pengembalian/scan')->with('success', "Pengembalian berkas No. RM '{$berkas['no_rm']}' berhasil dicatat.");
    }
}
