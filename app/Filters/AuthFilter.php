<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        if (! $session->get('is_logged_in')) {
            return redirect()->to('/login')->with('error', 'Silakan masuk ke akun Anda terlebih dahulu.');
        }

        // Check if user is required to change password
        // Use uri_string() and raw path to safely detect subfolder deployments (e.g. /Trackly/profil/ubah-sandi)
        $uriString = trim(uri_string(), '/');
        $rawPath   = service('uri')->getPath();

        $isUbahSandi = str_contains($uriString, 'profil/ubah-sandi') || str_contains($rawPath, 'profil/ubah-sandi');
        $isLogout    = str_contains($uriString, 'logout') || str_contains($rawPath, 'logout');

        if ($session->get('must_change_password') && ! $isUbahSandi && ! $isLogout) {
            return redirect()->to(base_url('profil/ubah-sandi'))->with('warning', 'Demi keamanan, Anda wajib mengganti kata sandi sebelum melanjutkan.');
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}
