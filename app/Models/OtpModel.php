<?php

namespace App\Models;

use CodeIgniter\Model;

class OtpModel extends Model
{
    protected $table            = 'otp_codes';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'tujuan',
        'kode_hash',
        'kadaluarsa_pada',
        'percobaan',
        'jumlah_kirim',
        'digunakan_pada',
        'ip_address',
        'token_reset',
        'token_kadaluarsa_pada',
        'created_at',
    ];

    /**
     * Generate new OTP for a user
     * Returns raw 6-digit code (for sending to email / dev banner)
     */
    public function generateOtp(int $userId, string $tujuan = 'login', string $ipAddress = ''): string
    {
        // 6 digits secure code: 100000 - 999999
        $rawCode = (string) random_int(100000, 999999);
        $hashedCode = password_hash($rawCode, PASSWORD_BCRYPT);
        $expiresAt = date('Y-m-d H:i:s', strtotime('+5 minutes'));

        // Check if there is already an active unused OTP to count resends
        $existing = $this->where('user_id', $userId)
            ->where('tujuan', $tujuan)
            ->where('digunakan_pada IS NULL')
            ->where('kadaluarsa_pada >', date('Y-m-d H:i:s'))
            ->orderBy('id', 'DESC')
            ->first();

        $jumlahKirim = 1;
        if ($existing) {
            $jumlahKirim = ((int) $existing['jumlah_kirim']) + 1;
            // Mark older one as expired
            $this->update($existing['id'], ['kadaluarsa_pada' => date('Y-m-d H:i:s')]);
        }

        $this->insert([
            'user_id'         => $userId,
            'tujuan'          => $tujuan,
            'kode_hash'       => $hashedCode,
            'kadaluarsa_pada' => $expiresAt,
            'percobaan'       => 0,
            'jumlah_kirim'    => $jumlahKirim,
            'ip_address'      => $ipAddress,
            'created_at'      => date('Y-m-d H:i:s'),
        ]);

        return $rawCode;
    }

    /**
     * Get latest active OTP entry for user
     */
    public function getActiveOtp(int $userId, string $tujuan = 'login')
    {
        return $this->where('user_id', $userId)
            ->where('tujuan', $tujuan)
            ->where('digunakan_pada IS NULL')
            ->orderBy('id', 'DESC')
            ->first();
    }

    /**
     * Verify OTP submission
     * Returns array: ['valid' => bool, 'reason' => string, 'remaining' => int]
     */
    public function verifyOtp(int $userId, string $rawCode, string $tujuan = 'login'): array
    {
        $otp = $this->getActiveOtp($userId, $tujuan);

        if (! $otp) {
            return ['valid' => false, 'reason' => 'expired_or_none', 'remaining' => 0];
        }

        // Check expiry
        if (strtotime($otp['kadaluarsa_pada']) < time()) {
            return ['valid' => false, 'reason' => 'expired', 'remaining' => 0];
        }

        // Check max attempts (3)
        $percobaan = (int) $otp['percobaan'];
        if ($percobaan >= 3) {
            return ['valid' => false, 'reason' => 'max_attempts_exceeded', 'remaining' => 0];
        }

        // Check password_verify
        if (password_verify($rawCode, $otp['kode_hash'])) {
            // Valid! Mark used
            $this->update($otp['id'], [
                'digunakan_pada' => date('Y-m-d H:i:s'),
            ]);

            return ['valid' => true, 'otp_id' => $otp['id']];
        }

        // Wrong OTP code
        $percobaan++;
        $remaining = max(0, 3 - $percobaan);

        $updateData = ['percobaan' => $percobaan];
        if ($remaining <= 0) {
            // Expire immediately
            $updateData['kadaluarsa_pada'] = date('Y-m-d H:i:s');
        }

        $this->update($otp['id'], $updateData);

        return ['valid' => false, 'reason' => 'incorrect', 'remaining' => $remaining];
    }

    /**
     * Create a reset token after OTP is verified for forgot password
     */
    public function issueResetToken(int $otpId): string
    {
        $token = bin2hex(random_bytes(32)); // 64 char random token
        $expiresAt = date('Y-m-d H:i:s', strtotime('+10 minutes'));

        $this->update($otpId, [
            'token_reset'           => $token,
            'token_kadaluarsa_pada' => $expiresAt,
        ]);

        return $token;
    }

    /**
     * Validate reset token
     */
    public function validateResetToken(string $token)
    {
        return $this->where('token_reset', $token)
            ->where('token_kadaluarsa_pada >', date('Y-m-d H:i:s'))
            ->first();
    }

    /**
     * Invalidate reset token after password changed
     */
    public function invalidateResetToken(string $token): void
    {
        $this->where('token_reset', $token)->set([
            'token_kadaluarsa_pada' => date('Y-m-d H:i:s'),
            'token_reset'           => null,
        ])->update();
    }
}
