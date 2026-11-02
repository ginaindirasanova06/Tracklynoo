<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        if (! $session->get('is_logged_in')) {
            return redirect()->to('/login')->with('error', 'Silakan masuk ke akun Anda terlebih dahulu.');
        }

        $userRole = $session->get('role');

        if (! empty($arguments)) {
            if (! in_array($userRole, $arguments, true)) {
                return redirect()->to('/dashboard')->with('error', 'Akses ditolak: Peran Anda (' . esc($userRole) . ') tidak berwenang mengakses halaman ini.');
            }
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}
