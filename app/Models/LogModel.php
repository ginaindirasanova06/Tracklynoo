<?php

namespace App\Models;

use CodeIgniter\Model;

class LogModel extends Model
{
    protected $table            = 'log_aktivitas';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'aksi',
        'keterangan',
        'ip_address',
        'created_at',
    ];

    /**
     * Record an audit activity
     */
    public function logActivity(?int $userId, string $aksi, string $keterangan, ?string $ipAddress = null): void
    {
        $this->insert([
            'user_id'    => $userId,
            'aksi'       => $aksi,
            'keterangan' => $keterangan,
            'ip_address' => $ipAddress ?? service('request')->getIPAddress(),
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Get logs with joined user details
     */
    public function getLogsWithUsers(int $limit = 100)
    {
        return $this->select('log_aktivitas.*, users.nama as user_nama, users.username, users.role')
            ->join('users', 'users.id = log_aktivitas.user_id', 'left')
            ->orderBy('log_aktivitas.created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }
}
