<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class TracklySeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');

        // 1. Seed Units
        $units = [
            ['kode_unit' => 'P-PD',  'nama_unit' => 'Poli Penyakit Dalam', 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['kode_unit' => 'P-ANK', 'nama_unit' => 'Poli Anak',           'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['kode_unit' => 'P-BDH', 'nama_unit' => 'Poli Bedah',          'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['kode_unit' => 'P-GGI', 'nama_unit' => 'Poli Gigi & Mulut',   'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['kode_unit' => 'IGD',   'nama_unit' => 'Instalasi Gawat Darurat (IGD)', 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['kode_unit' => 'RI-MLT','nama_unit' => 'Rawat Inap Melati',   'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['kode_unit' => 'RI-MWR','nama_unit' => 'Rawat Inap Mawar',    'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['kode_unit' => 'VK',    'nama_unit' => 'Ruang Bersalin / VK',  'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['kode_unit' => 'ICU',   'nama_unit' => 'Intensive Care Unit (ICU)', 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
        ];

        $this->db->table('units')->insertBatch($units);

        // Get Poli Anak ID
        $poliAnak = $this->db->table('units')->where('kode_unit', 'P-ANK')->get()->getRowArray();
        $poliAnakId = $poliAnak ? $poliAnak['id'] : null;

        // 2. Seed Users
        // Default password for all: password123
        $hashedPassword = password_hash('password123', PASSWORD_BCRYPT);

        $users = [
            [
                'nama'                  => 'Administrator Trackly',
                'username'              => 'admin',
                'email'                 => 'admin@trackly.local',
                'password'              => $hashedPassword,
                'role'                  => 'admin',
                'unit_id'               => null,
                'is_active'             => 1,
                'failed_login_count'    => 0,
                'locked_until'          => null,
                'must_change_password'  => 0,
                'password_changed_at'   => $now,
                'created_at'            => $now,
                'updated_at'            => $now,
            ],
            [
                'nama'                  => 'Ibu Sari (Petugas Filing)',
                'username'              => 'petugas_rm',
                'email'                 => 'petugas.rm@trackly.local',
                'password'              => $hashedPassword,
                'role'                  => 'petugas_rm',
                'unit_id'               => null,
                'is_active'             => 1,
                'failed_login_count'    => 0,
                'locked_until'          => null,
                'must_change_password'  => 0,
                'password_changed_at'   => $now,
                'created_at'            => $now,
                'updated_at'            => $now,
            ],
            [
                'nama'                  => 'Ns. Rina (Perawat Poli Anak)',
                'username'              => 'petugas_unit',
                'email'                 => 'petugas.unit@trackly.local',
                'password'              => $hashedPassword,
                'role'                  => 'petugas_unit',
                'unit_id'               => $poliAnakId,
                'is_active'             => 1,
                'failed_login_count'    => 0,
                'locked_until'          => null,
                'must_change_password'  => 0,
                'password_changed_at'   => $now,
                'created_at'            => $now,
                'updated_at'            => $now,
            ],
            [
                'nama'                  => 'Pak Hendra (Kepala Rekam Medis)',
                'username'              => 'kepala',
                'email'                 => 'kepala.rm@trackly.local',
                'password'              => $hashedPassword,
                'role'                  => 'kepala',
                'unit_id'               => null,
                'is_active'             => 1,
                'failed_login_count'    => 0,
                'locked_until'          => null,
                'must_change_password'  => 0,
                'password_changed_at'   => $now,
                'created_at'            => $now,
                'updated_at'            => $now,
            ],
        ];

        $this->db->table('users')->insertBatch($users);

        // 3. Seed Initial Settings
        $settings = [
            [
                'setting_key'   => 'durasi_standar_hari',
                'setting_value' => '2',
                'keterangan'    => 'Durasi standar peminjaman berkas rekam medis dalam hari',
                'updated_at'    => $now,
            ],
            [
                'setting_key'   => 'nama_instansi',
                'setting_value' => 'RSUD Sehat Sentosa',
                'keterangan'    => 'Nama fasilitas pelayanan kesehatan',
                'updated_at'    => $now,
            ],
            [
                'setting_key'   => 'maks_percobaan_otp',
                'setting_value' => '3',
                'keterangan'    => 'Batas maksimal kesalahan input kode OTP sebelum hangus',
                'updated_at'    => $now,
            ],
            [
                'setting_key'   => 'masa_berlaku_otp_menit',
                'setting_value' => '5',
                'keterangan'    => 'Masa berlaku kode OTP dalam menit',
                'updated_at'    => $now,
            ],
            [
                'setting_key'   => 'waktu_penguncian_menit',
                'setting_value' => '15',
                'keterangan'    => 'Durasi penguncian akun setelah 5 kali salah password dalam menit',
                'updated_at'    => $now,
            ],
        ];

        $this->db->table('settings')->insertBatch($settings);

        // 4. Seed Berkas RM dummy
        $berkas = [
            [
                'no_rm'       => '00-12-34',
                'nama_pasien' => 'Ahmad Subarjo',
                'tahun_buka'  => 2021,
                'lokasi_rak'  => 'Rak A-01',
                'kode_qr'     => 'trc-a1b2c3d4-e5f6-7890-1111-222233334444',
                'status'      => 'tersedia',
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'no_rm'       => '00-45-67',
                'nama_pasien' => 'Siti Aminah',
                'tahun_buka'  => 2022,
                'lokasi_rak'  => 'Rak A-02',
                'kode_qr'     => 'trc-b2c3d4e5-f6a7-8901-2222-333344445555',
                'status'      => 'tersedia',
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'no_rm'       => '00-78-90',
                'nama_pasien' => 'Bambang Pamungkas',
                'tahun_buka'  => 2020,
                'lokasi_rak'  => 'Rak B-01',
                'kode_qr'     => 'trc-c3d4e5f6-a7b8-9012-3333-444455556666',
                'status'      => 'tersedia',
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'no_rm'       => '00-23-45',
                'nama_pasien' => 'Dewi Sartika',
                'tahun_buka'  => 2023,
                'lokasi_rak'  => 'Rak B-03',
                'kode_qr'     => 'trc-d4e5f6a7-b8c9-0123-4444-555566667777',
                'status'      => 'tersedia',
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'no_rm'       => '00-34-56',
                'nama_pasien' => 'Eko Prasetyo',
                'tahun_buka'  => 2024,
                'lokasi_rak'  => 'Rak C-02',
                'kode_qr'     => 'trc-e5f6a7b8-c9d0-1234-5555-666677778888',
                'status'      => 'tersedia',
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
        ];

        $this->db->table('berkas_rm')->insertBatch($berkas);
    }
}
