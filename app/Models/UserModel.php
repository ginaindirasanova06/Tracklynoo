<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nama',
        'username',
        'email',
        'password',
        'role',
        'unit_id',
        'is_active',
        'failed_login_count',
        'locked_until',
        'must_change_password',
        'password_changed_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Find user by username or email
     */
    public function findByUsernameOrEmail(string $identifier)
    {
        return $this->select('users.*, units.nama_unit, units.kode_unit')
            ->join('units', 'units.id = users.unit_id', 'left')
            ->groupStart()
                ->where('users.username', $identifier)
                ->orWhere('users.email', $identifier)
            ->groupEnd()
            ->first();
    }

    /**
     * Check if user account is locked
     */
    public function isAccountLocked(array $user): bool
    {
        if (empty($user['locked_until'])) {
            return false;
        }

        return strtotime($user['locked_until']) > time();
    }

    /**
     * Increment failed login attempts. Lock if >= 5 attempts.
     */
    public function handleFailedLogin(int $userId): int
    {
        $user = $this->find($userId);
        if (! $user) {
            return 0;
        }

        $newCount = ((int) $user['failed_login_count']) + 1;
        $data = ['failed_login_count' => $newCount];

        if ($newCount >= 5) {
            // Lock for 15 minutes
            $data['locked_until'] = date('Y-m-d H:i:s', strtotime('+15 minutes'));
        }

        $this->update($userId, $data);
        return $newCount;
    }

    /**
     * Reset failed login counter on successful authentication
     */
    public function resetFailedLogin(int $userId): void
    {
        $this->update($userId, [
            'failed_login_count' => 0,
            'locked_until'       => null,
        ]);
    }
}
