<?php

namespace App\Controllers;

use App\Models\LogModel;
use App\Models\SettingModel;
use CodeIgniter\Controller;

class PengaturanController extends Controller
{
    protected SettingModel $settingModel;
    protected LogModel $logModel;

    public function __construct()
    {
        $this->settingModel = new SettingModel();
        $this->logModel     = new LogModel();
    }

    /**
     * Display system settings form
     */
    public function index()
    {
        $settings = [
            'durasi_standar_hari'    => $this->settingModel->getVal('durasi_standar_hari', 2),
            'nama_instansi'          => $this->settingModel->getVal('nama_instansi', 'RSUD Sehat Sentosa'),
            'maks_percobaan_otp'     => $this->settingModel->getVal('maks_percobaan_otp', 3),
            'masa_berlaku_otp_menit' => $this->settingModel->getVal('masa_berlaku_otp_menit', 5),
            'waktu_penguncian_menit' => $this->settingModel->getVal('waktu_penguncian_menit', 15),
        ];

        return view('pengaturan/index', [
            'title'    => 'Pengaturan Sistem — TRACKLY',
            'settings' => $settings,
        ]);
    }

    /**
     * Update system settings
     */
    public function update()
    {
        $durasiHari = (int) $this->request->getPost('durasi_standar_hari');
        $namaInst   = trim((string) $this->request->getPost('nama_instansi'));
        $maksOtp    = (int) $this->request->getPost('maks_percobaan_otp');
        $masaOtp    = (int) $this->request->getPost('masa_berlaku_otp_menit');
        $lockMins   = (int) $this->request->getPost('waktu_penguncian_menit');

        if ($durasiHari < 1 || $durasiHari > 30) {
            return redirect()->back()->withInput()->with('error', 'Durasi standar peminjaman harus antara 1 sampai 30 hari.');
        }

        if (empty($namaInst)) {
            return redirect()->back()->withInput()->with('error', 'Nama fasilitas pelayanan kesehatan tidak boleh kosong.');
        }

        if ($maksOtp < 1 || $maksOtp > 5) {
            return redirect()->back()->withInput()->with('error', 'Maksimal percobaan OTP harus antara 1 sampai 5 kali.');
        }

        if ($masaOtp < 1 || $masaOtp > 15) {
            return redirect()->back()->withInput()->with('error', 'Masa berlaku OTP harus antara 1 sampai 15 menit.');
        }

        if ($lockMins < 5 || $lockMins > 60) {
            return redirect()->back()->withInput()->with('error', 'Waktu penguncian akun harus antara 5 sampai 60 menit.');
        }

        $this->settingModel->setVal('durasi_standar_hari', (string) $durasiHari, 'Durasi standar peminjaman berkas rekam medis dalam hari');
        $this->settingModel->setVal('nama_instansi', $namaInst, 'Nama fasilitas pelayanan kesehatan');
        $this->settingModel->setVal('maks_percobaan_otp', (string) $maksOtp, 'Batas maksimal kesalahan input kode OTP');
        $this->settingModel->setVal('masa_berlaku_otp_menit', (string) $masaOtp, 'Masa berlaku kode OTP dalam menit');
        $this->settingModel->setVal('waktu_penguncian_menit', (string) $lockMins, 'Durasi penguncian akun setelah 5 kali salah password');

        $this->logModel->logActivity(
            session()->get('user_id'),
            'ubah_pengaturan',
            "Memperbarui parameter pengaturan sistem (Durasi pinjam: {$durasiHari} hari, Instansi: {$namaInst})"
        );

        return redirect()->to('/pengaturan')->with('success', 'Pengaturan sistem berhasil diperbarui dan diterapkan ke seluruh modul.');
    }
}
