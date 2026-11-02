<?php

namespace App\Models;

use CodeIgniter\Model;

class SettingModel extends Model
{
    protected $table            = 'settings';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'setting_key',
        'setting_value',
        'keterangan',
        'updated_at',
    ];

    /**
     * Get a setting value by its key with fallback default
     */
    public function getVal(string $key, $default = null)
    {
        $row = $this->where('setting_key', $key)->first();
        return $row ? $row['setting_value'] : $default;
    }

    /**
     * Set a setting value
     */
    public function setVal(string $key, $value, ?string $keterangan = null): void
    {
        $existing = $this->where('setting_key', $key)->first();
        if ($existing) {
            $this->update($existing['id'], [
                'setting_value' => $value,
                'updated_at'    => date('Y-m-d H:i:s'),
            ]);
        } else {
            $this->insert([
                'setting_key'   => $key,
                'setting_value' => $value,
                'keterangan'    => $keterangan,
                'updated_at'    => date('Y-m-d H:i:s'),
            ]);
        }
    }
}
