<?php

namespace App\Controllers;

use App\Models\LogModel;
use CodeIgniter\Controller;

class LogController extends Controller
{
    protected LogModel $logModel;

    public function __construct()
    {
        $this->logModel = new LogModel();
    }

    /**
     * Display activity audit trail logs
     */
    public function index()
    {
        $keyword = trim((string) $this->request->getGet('cari'));

        $query = $this->logModel->select('log_aktivitas.*, users.nama as user_nama, users.username, users.role')
            ->join('users', 'users.id = log_aktivitas.user_id', 'left');

        if (! empty($keyword)) {
            $query = $query->groupStart()
                ->like('log_aktivitas.aksi', $keyword)
                ->orLike('log_aktivitas.keterangan', $keyword)
                ->orLike('users.nama', $keyword)
                ->orLike('users.username', $keyword)
            ->groupEnd();
        }

        $logs  = $query->orderBy('log_aktivitas.created_at', 'DESC')->paginate(20, 'logs');
        $pager = $this->logModel->pager;

        return view('log/index', [
            'title'   => 'Log Aktivitas Sistem — TRACKLY',
            'logs'    => $logs,
            'pager'   => $pager,
            'keyword' => $keyword,
        ]);
    }
}
