<?php

namespace App\Controllers;

use App\Models\LogModel;
use App\Models\OtpModel;
use App\Models\UserModel;
use CodeIgniter\Controller;

class AuthController extends Controller
{
    protected $helpers = ['url', 'form'];

    protected UserModel $userModel;
    protected OtpModel $otpModel;
    protected LogModel $logModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->otpModel  = new OtpModel();
        $this->logModel  = new LogModel();
    }

    /**
     * Mask email address for privacy (e.g. s*****@gmail.com)
     */
    private function maskEmail(string $email): string
    {
        $parts = explode('@', $email);
        if (count($parts) !== 2) {
            return '******@email.com';
        }
        $name   = $parts[0];
        $domain = $parts[1];

        if (strlen($name) <= 2) {
            $maskedName = substr($name, 0, 1) . '*****';
        } else {
            $maskedName = substr($name, 0, 1) . str_repeat('*', max(3, strlen($name) - 2)) . substr($name, -1);
        }

        return $maskedName . '@' . $domain;
    }

    /**
     * Send OTP email via CodeIgniter Email service if SMTP is configured.
     * Wrapped in try-catch so network or credential failures will never block login.
     */
    protected function sendOtpEmail(string $recipientEmail, string $recipientName, string $otpCode, string $purpose = 'login'): bool
    {
        try {
            $config = config('Email');
            // If SMTPHost is not configured, skip attempting network send to avoid delay
            if (empty($config->SMTPHost) && empty(env('email.SMTPHost'))) {
                log_message('info', "SMTP host not configured. Simulated OTP for {$recipientEmail}: {$otpCode}");
                return false;
            }

            $email = \Config\Services::email();
            $email->setTo($recipientEmail);
            $subject = $purpose === 'login'
                ? 'Kode OTP Masuk Akun - TRACKLY'
                : 'Kode Verifikasi Reset Sandi - TRACKLY';
            $email->setSubject($subject);

            $actionText = $purpose === 'login'
                ? 'masuk ke sistem TRACKLY'
                : 'melakukan pemulihan kata sandi akun Anda';

            $message = "
            <div style='font-family: -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, Helvetica, Arial, sans-serif; max-width: 560px; margin: 0 auto; padding: 24px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px;'>
                <div style='text-align: center; margin-bottom: 24px; padding-bottom: 16px; border-bottom: 1px solid #f1f5f9;'>
                    <h2 style='color: #0284c7; margin: 0; font-size: 24px; font-weight: 800; letter-spacing: -0.5px;'>TRACKLY</h2>
                    <p style='color: #64748b; font-size: 13px; margin: 4px 0 0;'>Sistem Pelacakan Berkas Rekam Medis</p>
                </div>
                <p style='font-size: 15px; color: #1e293b; margin: 0 0 12px;'>Halo <strong>" . esc($recipientName) . "</strong>,</p>
                <p style='font-size: 14px; color: #475569; line-height: 1.6; margin: 0 0 20px;'>
                    Berikut adalah kode verifikasi 6 digit Anda untuk {$actionText}:
                </p>
                <div style='text-align: center; margin: 28px 0;'>
                    <span style='display: inline-block; font-size: 32px; font-weight: 800; letter-spacing: 8px; color: #0284c7; background: #f0f9ff; padding: 14px 28px; border-radius: 10px; border: 1px solid #bae6fd; font-family: monospace;'>
                        {$otpCode}
                    </span>
                </div>
                <p style='font-size: 13px; color: #64748b; margin: 0 0 8px; text-align: center;'>
                    Masa berlaku kode ini adalah <strong>5 menit</strong>.
                </p>
                <p style='font-size: 12px; color: #94a3b8; text-align: center; margin: 0;'>
                    Jangan berikan kode ini kepada pihak mana pun demi menjaga kerahasiaan berkas rekam medis.
                </p>
                <hr style='border: none; border-top: 1px solid #f1f5f9; margin: 24px 0;'>
                <p style='font-size: 11px; color: #94a3b8; text-align: center; margin: 0;'>
                    Email ini dikirim otomatis oleh sistem TRACKLY. Jika Anda tidak merasa melakukan permintaan ini, segera hubungi Administrator.
                </p>
            </div>";

            $email->setMessage($message);
            $sent = $email->send(false);
            if (! $sent) {
                log_message('warning', 'Gagal kirim email OTP: ' . $email->printDebugger(['headers']));
            }
            return (bool) $sent;
        } catch (\Throwable $e) {
            log_message('error', 'Exception sending OTP email: ' . $e->getMessage());
            return false;
        }
    }


    /**
     * Show login form
     */
    public function login()
    {
        if (session()->get('is_logged_in')) {
            return redirect()->to('/dashboard');
        }

        return view('auth/login');
    }

    /**
     * Handle Step 1 login: Username & Password verification
     */
    public function processLogin()
    {
        $username = trim((string) $this->request->getPost('username'));
        $password = (string) $this->request->getPost('password');

        if (empty($username) || empty($password)) {
            return redirect()->back()->withInput()->with('error', 'Username dan password wajib diisi.');
        }

        $user = $this->userModel->findByUsernameOrEmail($username);

        // Anti-enumeration: neutral error message if user not found
        if (! $user) {
            return redirect()->back()->withInput()->with('error', 'Username atau password salah.');
        }

        // Check if account is active
        if (! (bool) $user['is_active']) {
            return redirect()->back()->withInput()->with('error', 'Akun Anda dinonaktifkan oleh Administrator.');
        }

        // Check if account is locked
        if ($this->userModel->isAccountLocked($user)) {
            $lockedUntil = strtotime($user['locked_until']);
            $remainingMins = max(1, (int) ceil(($lockedUntil - time()) / 60));
            return redirect()->back()->withInput()->with('error', "Akun dikunci sementara karena terlalu banyak percobaan gagal. Coba lagi dalam {$remainingMins} menit.");
        }

        // Verify password
        if (! password_verify($password, $user['password'])) {
            $failedCount = $this->userModel->handleFailedLogin($user['id']);
            $this->logModel->logActivity($user['id'], 'login_gagal', "Percobaan login gagal untuk user {$user['username']} (gagal ke-{$failedCount})");

            if ($failedCount >= 5) {
                return redirect()->back()->withInput()->with('error', 'Akun dikunci sementara karena terlalu banyak percobaan gagal. Coba lagi dalam 15 menit.');
            }

            return redirect()->back()->withInput()->with('error', 'Username atau password salah.');
        }

        // Reset failed login counter on valid password
        $this->userModel->resetFailedLogin($user['id']);

        // Generate 6-digit OTP
        $ip = $this->request->getIPAddress();
        $rawOtp = $this->otpModel->generateOtp($user['id'], 'login', $ip);

        // Attempt sending email (if SMTP is configured)
        $emailSent = $this->sendOtpEmail($user['email'], $user['nama'], $rawOtp, 'login');

        // Save temporary session state for Step 2 OTP verification
        session()->set([
            'temp_user_id'       => $user['id'],
            'temp_user_email'    => $user['email'],
            'temp_user_nama'     => $user['nama'],
            'temp_masked_email'  => $this->maskEmail($user['email']),
            'temp_otp_sent_at'   => time(),
            'temp_otp_target'    => 'login',
            'temp_dev_otp'       => $rawOtp,
            'temp_email_sent'    => $emailSent,
        ]);

        // Provide simulated dev OTP for effortless testing
        session()->setFlashdata('dev_otp_code', $rawOtp);
        $this->logModel->logActivity($user['id'], 'otp_dikirim', "OTP login dibuat untuk {$user['email']} (email sent: " . ($emailSent ? 'yes' : 'no/local_mode') . ")");

        $infoMsg = $emailSent 
            ? 'Kode OTP 6-digit telah dikirimkan ke email Anda (' . $this->maskEmail($user['email']) . ').'
            : 'Kredensial valid. Kode OTP verifikasi dapat dilihat langsung pada layar di bawah.';

        return redirect()->to(base_url('login/otp'))->with('success', $infoMsg);
    }

    /**
     * Show Step 2 OTP verification form
     */
    public function showOtp()
    {
        if (session()->get('is_logged_in')) {
            return redirect()->to(base_url('dashboard'));
        }

        $userId = session()->get('temp_user_id');
        if (! $userId) {
            return redirect()->to(base_url('login'))->with('error', 'Sesi verifikasi tidak ditemukan. Silakan login kembali.');
        }

        return view('auth/otp', [
            'userEmail'   => session()->get('temp_user_email'),
            'maskedEmail' => session()->get('temp_masked_email'),
            'devOtpCode'  => session()->get('temp_dev_otp') ?? session()->getFlashdata('dev_otp_code'),
            'emailSent'   => session()->get('temp_email_sent') ?? false,
        ]);
    }

    /**
     * Handle Step 2 OTP verification
     */
    public function verifyOtp()
    {
        $userId = session()->get('temp_user_id');
        if (! $userId) {
            return redirect()->to(base_url('login'))->with('error', 'Sesi telah kedaluwarsa. Silakan login kembali.');
        }

        // Assemble 6-digit code
        $otpPost = $this->request->getPost('kode_otp');
        if (is_array($otpPost)) {
            $code = implode('', $otpPost);
        } else {
            $code = trim((string) $otpPost);
        }

        if (strlen($code) !== 6 || ! ctype_digit($code)) {
            return redirect()->back()->with('error', 'Kode OTP harus berupa 6 digit angka.');
        }

        $result = $this->otpModel->verifyOtp($userId, $code, 'login');

        if (! $result['valid']) {
            if ($result['reason'] === 'max_attempts_exceeded') {
                session()->remove(['temp_user_id', 'temp_user_email', 'temp_user_nama', 'temp_masked_email', 'temp_otp_sent_at', 'temp_otp_target', 'temp_dev_otp', 'temp_email_sent']);
                $this->logModel->logActivity($userId, 'otp_hangus', 'Percobaan OTP habis (3x salah). Sesi dibatalkan.');
                return redirect()->to(base_url('login'))->with('error', 'Percobaan OTP habis. Silakan login ulang.');
            }

            if ($result['reason'] === 'expired') {
                return redirect()->back()->with('error', 'Kode OTP sudah kedaluwarsa. Silakan kirim ulang kode.');
            }

            $remaining = $result['remaining'];
            return redirect()->back()->with('error', "Kode OTP salah. Sisa percobaan: {$remaining} kali.");
        }

        // OTP Valid! Fetch complete user record
        $user = $this->userModel->find($userId);

        // Clean temporary session
        session()->remove(['temp_user_id', 'temp_user_email', 'temp_user_nama', 'temp_masked_email', 'temp_otp_sent_at', 'temp_otp_target', 'temp_dev_otp', 'temp_email_sent']);

        // Set complete authenticated session
        session()->set([
            'is_logged_in'         => true,
            'user_id'              => $user['id'],
            'nama'                 => $user['nama'],
            'username'             => $user['username'],
            'email'                => $user['email'],
            'role'                 => $user['role'],
            'unit_id'              => $user['unit_id'],
            'must_change_password' => (bool) $user['must_change_password'],
            'login_time'           => time(),
        ]);

        $this->logModel->logActivity($user['id'], 'login_sukses', "Pengguna {$user['username']} ({$user['role']}) berhasil login melalui verifikasi OTP");

        if ((bool) $user['must_change_password']) {
            return redirect()->to(base_url('profil/ubah-sandi'))->with('warning', 'Anda wajib mengganti kata sandi sementara Anda saat pertama kali login.');
        }

        return redirect()->to(base_url('dashboard'))->with('success', 'Selamat datang di TRACKLY, ' . esc($user['nama']) . '!');
    }

    /**
     * Resend OTP code with 60-second cooldown
     */
    public function resendOtp()
    {
        $userId = session()->get('temp_user_id');
        if (! $userId) {
            return redirect()->to(base_url('login'))->with('error', 'Sesi tidak ditemukan. Silakan login ulang.');
        }

        $lastSent = session()->get('temp_otp_sent_at') ?? 0;
        $elapsed  = time() - $lastSent;

        if ($elapsed < 60) {
            $wait = 60 - $elapsed;
            return redirect()->to(base_url('login/otp'))->with('error', "Tunggu {$wait} detik sebelum mengirim ulang kode OTP.");
        }

        $activeOtp = $this->otpModel->getActiveOtp($userId, 'login');
        if ($activeOtp && ((int) $activeOtp['jumlah_kirim']) >= 3) {
            session()->remove(['temp_user_id', 'temp_user_email', 'temp_user_nama', 'temp_masked_email', 'temp_otp_sent_at', 'temp_otp_target', 'temp_dev_otp', 'temp_email_sent']);
            return redirect()->to(base_url('login'))->with('error', 'Batas maksimal pengiriman ulang OTP tercapai. Silakan login kembali.');
        }

        $ip = $this->request->getIPAddress();
        $rawOtp = $this->otpModel->generateOtp($userId, 'login', $ip);

        $user = $this->userModel->find($userId);
        $emailSent = false;
        if ($user) {
            $emailSent = $this->sendOtpEmail($user['email'], $user['nama'], $rawOtp, 'login');
        }

        session()->set('temp_otp_sent_at', time());
        session()->set('temp_dev_otp', $rawOtp);
        session()->set('temp_email_sent', $emailSent);
        session()->setFlashdata('dev_otp_code', $rawOtp);

        $this->logModel->logActivity($userId, 'otp_kirim_ulang', 'Pengiriman ulang OTP login berhasil diproses.');

        $msg = $emailSent
            ? 'Kode OTP baru telah berhasil dikirimkan ke email Anda.'
            : 'Kode OTP baru telah berhasil dibuat dan ditampilkan di bawah.';

        return redirect()->to(base_url('login/otp'))->with('success', $msg);
    }

    /**
     * Show Forgot Password form
     */
    public function forgotPassword()
    {
        if (session()->get('is_logged_in')) {
            return redirect()->to('/dashboard');
        }

        return view('auth/forgot_password');
    }

    /**
     * Process Forgot Password request (Anti-enumeration: neutral response)
     */
    public function processForgotPassword()
    {
        $identifier = trim((string) $this->request->getPost('username_or_email'));

        if (empty($identifier)) {
            return redirect()->back()->withInput()->with('error', 'Silakan masukkan username atau email Anda.');
        }

        $user = $this->userModel->findByUsernameOrEmail($identifier);

        if ($user && (bool) $user['is_active']) {
            $ip = $this->request->getIPAddress();
            $rawOtp = $this->otpModel->generateOtp($user['id'], 'reset_sandi', $ip);

            $emailSent = $this->sendOtpEmail($user['email'], $user['nama'], $rawOtp, 'reset_sandi');

            session()->set([
                'temp_reset_user_id'      => $user['id'],
                'temp_reset_masked_email' => $this->maskEmail($user['email']),
                'temp_reset_user_email'   => $user['email'],
                'temp_reset_sent_at'      => time(),
                'temp_reset_dev_otp'      => $rawOtp,
                'temp_reset_email_sent'   => $emailSent,
            ]);

            session()->setFlashdata('dev_otp_code', $rawOtp);
            $this->logModel->logActivity($user['id'], 'lupa_sandi_otp', "Kode OTP reset sandi diproses untuk {$user['email']} (email sent: " . ($emailSent ? 'yes' : 'no/local_mode') . ")");
        }

        // Always show identical neutral feedback
        return redirect()->to(base_url('lupa-sandi/otp'))->with('info', 'Jika akun terdaftar, kode verifikasi 6 digit telah diproses untuk dikirim ke email Anda.');
    }

    /**
     * Show Forgot Password OTP verification form
     */
    public function showResetOtp()
    {
        $userId = session()->get('temp_reset_user_id');
        if (! $userId) {
            return redirect()->to(base_url('lupa-sandi'))->with('error', 'Sesi pemulihan akun tidak ditemukan.');
        }

        return view('auth/reset_otp', [
            'maskedEmail' => session()->get('temp_reset_masked_email'),
            'userEmail'   => session()->get('temp_reset_user_email'),
            'devOtpCode'  => session()->get('temp_reset_dev_otp') ?? session()->getFlashdata('dev_otp_code'),
            'emailSent'   => session()->get('temp_reset_email_sent') ?? false,
        ]);
    }

    /**
     * Verify Forgot Password OTP and issue a reset token
     */
    public function verifyResetOtp()
    {
        $userId = session()->get('temp_reset_user_id');
        if (! $userId) {
            return redirect()->to(base_url('lupa-sandi'))->with('error', 'Sesi telah kedaluwarsa.');
        }

        $otpPost = $this->request->getPost('kode_otp');
        $code = is_array($otpPost) ? implode('', $otpPost) : trim((string) $otpPost);

        if (strlen($code) !== 6 || ! ctype_digit($code)) {
            return redirect()->back()->with('error', 'Kode OTP harus berupa 6 digit angka.');
        }

        $result = $this->otpModel->verifyOtp($userId, $code, 'reset_sandi');

        if (! $result['valid']) {
            if ($result['reason'] === 'max_attempts_exceeded') {
                session()->remove(['temp_reset_user_id', 'temp_reset_masked_email', 'temp_reset_user_email', 'temp_reset_sent_at', 'temp_reset_dev_otp', 'temp_reset_email_sent']);
                return redirect()->to(base_url('lupa-sandi'))->with('error', 'Percobaan OTP habis. Silakan ulangi permintaan lupa sandi.');
            }
            if ($result['reason'] === 'expired') {
                return redirect()->back()->with('error', 'Kode OTP sudah kedaluwarsa.');
            }
            return redirect()->back()->with('error', "Kode OTP salah. Sisa percobaan: {$result['remaining']} kali.");
        }

        // Issue 10-minute reset token
        $resetToken = $this->otpModel->issueResetToken($result['otp_id']);

        session()->remove(['temp_reset_user_id', 'temp_reset_masked_email', 'temp_reset_user_email', 'temp_reset_sent_at', 'temp_reset_dev_otp', 'temp_reset_email_sent']);

        return redirect()->to(base_url('reset-sandi?token=' . urlencode($resetToken)))->with('success', 'Verifikasi berhasil. Silakan buat kata sandi baru Anda.');
    }

    /**
     * Show Create New Password form
     */
    public function showNewPassword()
    {
        $token = (string) $this->request->getGet('token');

        if (empty($token)) {
            return redirect()->to(base_url('lupa-sandi'))->with('error', 'Token reset tidak ditemukan.');
        }

        $otpRecord = $this->otpModel->validateResetToken($token);
        if (! $otpRecord) {
            return redirect()->to(base_url('lupa-sandi'))->with('error', 'Token reset tidak valid atau sudah kedaluwarsa.');
        }

        return view('auth/new_password', ['token' => $token]);
    }

    /**
     * Save new password from reset flow
     */
    public function saveNewPassword()
    {
        $token           = (string) $this->request->getPost('token');
        $passwordBaru    = (string) $this->request->getPost('password_baru');
        $konfirmasiSandi = (string) $this->request->getPost('konfirmasi_password');

        $otpRecord = $this->otpModel->validateResetToken($token);
        if (! $otpRecord) {
            return redirect()->to(base_url('lupa-sandi'))->with('error', 'Token reset tidak valid atau sudah kedaluwarsa.');
        }

        $userId = (int) $otpRecord['user_id'];
        $user   = $this->userModel->find($userId);

        if (! $user) {
            return redirect()->to(base_url('login'))->with('error', 'Pengguna tidak ditemukan.');
        }

        // Validation: Min 8 chars, must contain letters and numbers
        if (strlen($passwordBaru) < 8 || ! preg_match('/[A-Za-z]/', $passwordBaru) || ! preg_match('/[0-9]/', $passwordBaru)) {
            return redirect()->back()->with('error', 'Kata sandi minimal 8 karakter dan harus mengandung kombinasi huruf dan angka.');
        }

        if ($passwordBaru !== $konfirmasiSandi) {
            return redirect()->back()->with('error', 'Konfirmasi kata sandi tidak cocok.');
        }

        // Cannot be the same as current password
        if (password_verify($passwordBaru, $user['password'])) {
            return redirect()->back()->with('error', 'Kata sandi baru tidak boleh sama dengan kata sandi saat ini.');
        }

        // Update password
        $this->userModel->update($userId, [
            'password'             => password_hash($passwordBaru, PASSWORD_BCRYPT),
            'must_change_password' => 0,
            'password_changed_at'  => date('Y-m-d H:i:s'),
            'failed_login_count'   => 0,
            'locked_until'         => null,
        ]);

        $this->otpModel->invalidateResetToken($token);
        $this->logModel->logActivity($userId, 'reset_sandi_sukses', "Pengguna {$user['username']} berhasil mengubah kata sandi via Lupa Sandi.");

        return redirect()->to(base_url('login'))->with('success', 'Sandi berhasil diubah. Silakan masuk menggunakan sandi baru Anda.');
    }

    /**
     * Show change password form for authenticated user
     */
    public function showChangePassword()
    {
        return view('auth/change_password');
    }

    /**
     * Process change password for authenticated user
     */
    public function processChangePassword()
    {
        $userId       = session()->get('user_id');
        $sandiLama    = (string) $this->request->getPost('sandi_lama');
        $sandiBaru    = (string) $this->request->getPost('sandi_baru');
        $konfirmasi   = (string) $this->request->getPost('konfirmasi_sandi');

        $user = $this->userModel->find($userId);
        if (! $user) {
            return redirect()->to(base_url('login'));
        }

        // If not mandatory first change, verify old password
        if (! password_verify($sandiLama, $user['password'])) {
            return redirect()->back()->with('error', 'Kata sandi saat ini salah.');
        }

        if (strlen($sandiBaru) < 8 || ! preg_match('/[A-Za-z]/', $sandiBaru) || ! preg_match('/[0-9]/', $sandiBaru)) {
            return redirect()->back()->with('error', 'Kata sandi baru minimal 8 karakter dan harus memuat huruf dan angka.');
        }

        if ($sandiBaru !== $konfirmasi) {
            return redirect()->back()->with('error', 'Konfirmasi kata sandi baru tidak cocok.');
        }

        if (password_verify($sandiBaru, $user['password'])) {
            return redirect()->back()->with('error', 'Kata sandi baru tidak boleh sama dengan kata sandi saat ini.');
        }

        $this->userModel->update($userId, [
            'password'             => password_hash($sandiBaru, PASSWORD_BCRYPT),
            'must_change_password' => 0,
            'password_changed_at'  => date('Y-m-d H:i:s'),
        ]);

        session()->set('must_change_password', false);
        $this->logModel->logActivity($userId, 'ubah_sandi_sukses', "Pengguna {$user['username']} berhasil memperbarui kata sandi profil.");

        return redirect()->to(base_url('dashboard'))->with('success', 'Kata sandi berhasil diperbarui.');
    }

    /**
     * Logout
     */
    public function logout()
    {
        $userId = session()->get('user_id');
        if ($userId) {
            $this->logModel->logActivity($userId, 'logout', 'Pengguna keluar dari aplikasi');
        }

        session()->destroy();
        return redirect()->to(base_url('login'))->with('info', 'Anda telah berhasil keluar dari sistem.');
    }
}
