<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// Root redirection
$routes->get('/', static function () {
    if (session()->get('is_logged_in')) {
        return redirect()->to('/dashboard');
    }
    return redirect()->to('/login');
});

// Authentication Routes (Public)
$routes->get('/login', 'AuthController::login');
$routes->post('/login', 'AuthController::processLogin');
$routes->get('/login/otp', 'AuthController::showOtp');
$routes->post('/login/otp', 'AuthController::verifyOtp');
$routes->post('/login/otp/kirim-ulang', 'AuthController::resendOtp');

// Password Recovery (Public)
$routes->get('/lupa-sandi', 'AuthController::forgotPassword');
$routes->post('/lupa-sandi', 'AuthController::processForgotPassword');
$routes->get('/lupa-sandi/otp', 'AuthController::showResetOtp');
$routes->post('/lupa-sandi/otp', 'AuthController::verifyResetOtp');
$routes->get('/reset-sandi', 'AuthController::showNewPassword');
$routes->post('/reset-sandi', 'AuthController::saveNewPassword');

// Logout
$routes->get('/logout', 'AuthController::logout');
$routes->post('/logout', 'AuthController::logout');

// Authenticated Routes (Protected by AuthFilter)
$routes->group('', ['filter' => 'auth'], static function ($routes) {
    // Dashboard (All roles)
    $routes->get('/dashboard', 'DashboardController::index');

    // Profile & Password Change (All roles)
    $routes->get('/profil/ubah-sandi', 'AuthController::showChangePassword');
    $routes->post('/profil/ubah-sandi', 'AuthController::processChangePassword');

    // Master Data Berkas RM (Admin, Petugas RM, Kepala)
    $routes->get('/berkas', 'BerkasController::index', ['filter' => 'role:admin,petugas_rm,kepala']);
    $routes->get('/berkas/template-import', 'BerkasController::downloadTemplate', ['filter' => 'role:admin,petugas_rm']);
    $routes->post('/berkas/import', 'BerkasController::importCsv', ['filter' => 'role:admin,petugas_rm']);
    $routes->get('/berkas/(:num)', 'BerkasController::show/$1', ['filter' => 'role:admin,petugas_rm,kepala']);
    $routes->post('/berkas', 'BerkasController::store', ['filter' => 'role:admin,petugas_rm']);
    $routes->post('/berkas/update/(:num)', 'BerkasController::update/$1', ['filter' => 'role:admin,petugas_rm']);
    $routes->post('/berkas/delete/(:num)', 'BerkasController::delete/$1', ['filter' => 'role:admin,petugas_rm']);

    // Master Data Unit (Admin, Petugas RM)
    $routes->get('/unit', 'UnitController::index', ['filter' => 'role:admin,petugas_rm']);
    $routes->post('/unit', 'UnitController::store', ['filter' => 'role:admin,petugas_rm']);
    $routes->post('/unit/update/(:num)', 'UnitController::update/$1', ['filter' => 'role:admin,petugas_rm']);
    $routes->post('/unit/toggle/(:num)', 'UnitController::toggle/$1', ['filter' => 'role:admin,petugas_rm']);
    $routes->post('/unit/delete/(:num)', 'UnitController::delete/$1', ['filter' => 'role:admin,petugas_rm']);

    // Manajemen Pengguna (Admin only)
    $routes->get('/pengguna', 'PenggunaController::index', ['filter' => 'role:admin']);
    $routes->post('/pengguna', 'PenggunaController::store', ['filter' => 'role:admin']);
    $routes->post('/pengguna/update/(:num)', 'PenggunaController::update/$1', ['filter' => 'role:admin']);
    $routes->post('/pengguna/reset-sandi/(:num)', 'PenggunaController::resetSandi/$1', ['filter' => 'role:admin']);
    $routes->post('/pengguna/unlock/(:num)', 'PenggunaController::unlock/$1', ['filter' => 'role:admin']);
    $routes->post('/pengguna/toggle/(:num)', 'PenggunaController::toggle/$1', ['filter' => 'role:admin']);
    $routes->post('/pengguna/delete/(:num)', 'PenggunaController::delete/$1', ['filter' => 'role:admin']);

    // Pengaturan Sistem (Admin only)
    $routes->get('/pengaturan', 'PengaturanController::index', ['filter' => 'role:admin']);
    $routes->post('/pengaturan', 'PengaturanController::update', ['filter' => 'role:admin']);

    // Log Aktivitas (Admin, Kepala)
    $routes->get('/log', 'LogController::index', ['filter' => 'role:admin,kepala']);

    // Cetak & Regenerate QR Code (Admin, Petugas RM)
    $routes->get('/berkas/print-qr/(:num)', 'BerkasController::printQr/$1', ['filter' => 'role:admin,petugas_rm']);
    $routes->get('/berkas/print-qr-massal', 'BerkasController::printQrMassal', ['filter' => 'role:admin,petugas_rm']);
    $routes->post('/berkas/regenerate-qr/(:num)', 'BerkasController::regenerateQr/$1', ['filter' => 'role:admin']);

    // Sirkulasi Berkas — Peminjaman (Admin, Petugas RM)
    $routes->get('/peminjaman/scan', 'PeminjamanController::scan', ['filter' => 'role:admin,petugas_rm']);
    $routes->get('/peminjaman/lookup-qr', 'PeminjamanController::lookupQr', ['filter' => 'role:admin,petugas_rm']);
    $routes->get('/peminjaman/lookup-rm', 'PeminjamanController::lookupRm', ['filter' => 'role:admin,petugas_rm']);
    $routes->post('/peminjaman', 'PeminjamanController::store', ['filter' => 'role:admin,petugas_rm']);
    $routes->post('/peminjaman/perpanjang/(:num)', 'PeminjamanController::perpanjang/$1', ['filter' => 'role:admin,petugas_rm']);
    $routes->post('/peminjaman/koreksi/(:num)', 'PeminjamanController::koreksi/$1', ['filter' => 'role:admin']);

    // Sirkulasi Berkas — Pengembalian (Admin, Petugas RM)
    $routes->get('/pengembalian/scan', 'PengembalianController::scan', ['filter' => 'role:admin,petugas_rm']);
    $routes->get('/pengembalian/lookup-qr', 'PengembalianController::lookupQr', ['filter' => 'role:admin,petugas_rm']);
    $routes->get('/pengembalian/lookup-rm', 'PengembalianController::lookupRm', ['filter' => 'role:admin,petugas_rm']);
    $routes->post('/pengembalian', 'PengembalianController::store', ['filter' => 'role:admin,petugas_rm']);

    // Monitoring & Ekspor Belum Kembali
    $routes->get('/belum-kembali', 'PeminjamanController::belumKembali');
    $routes->get('/belum-kembali/export-excel', 'PeminjamanController::exportExcelBelumKembali');
    $routes->get('/riwayat', 'PeminjamanController::riwayat', ['filter' => 'role:admin,petugas_rm,kepala']);

    // Laporan & Ekspor (Admin, Petugas RM, Kepala - FR-26 & FR-27)
    $routes->get('/laporan', 'LaporanController::index', ['filter' => 'role:admin,petugas_rm,kepala']);
    $routes->get('/laporan/export', 'LaporanController::export', ['filter' => 'role:admin,petugas_rm,kepala']);
    $routes->get('/laporan/export-excel', 'LaporanController::exportExcel', ['filter' => 'role:admin,petugas_rm,kepala']);
    $routes->get('/laporan/export-pdf', 'LaporanController::exportPdf', ['filter' => 'role:admin,petugas_rm,kepala']);
});

// Public QR Scan Redirect (scan with phone camera)
$routes->get('/scan/(:segment)', 'BerkasController::scanRedirect/$1');
