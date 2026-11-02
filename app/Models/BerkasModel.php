<?php

namespace App\Models;

use CodeIgniter\Model;

class BerkasModel extends Model
{
    protected $table            = 'berkas_rm';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'no_rm',
        'nama_pasien',
        'tahun_buka',
        'lokasi_rak',
        'kode_qr',
        'status',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Find file by QR code token
     */
    public function findByQr(string $qrCode)
    {
        return $this->where('kode_qr', $qrCode)->first();
    }

    /**
     * Find file by No. RM
     */
    public function findByNoRm(string $noRm)
    {
        return $this->where('no_rm', $noRm)->first();
    }
}
