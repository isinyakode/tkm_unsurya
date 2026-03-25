<?php

namespace App\Controllers;

use App\Libraries\SsoService;

class Auth extends BaseController
{
    protected $ssoService;

    public function __construct()
    {
        $this->ssoService = new SsoService();
    }

    public function login()
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/dashboard');
        }
        return view('Auth/login');
    }

    public function attemptLogin()
    {
        $username = $this->request->getPost('nim');
        $password = $this->request->getPost('password');

        $loginData = $this->ssoService->loginSso($username, $password);
        // dd($loginData);
        if ($loginData) {
            // Ambil info detail mahasiswa dari dalam JWT access_token
            $profile = $this->ssoService->decodeToken($loginData['access_token']);
            // dd($profile);
            if ($profile) {
                $sessionData = [
                    'isLoggedIn'    => true,
                    'nim'           => $username,
                    'nama'          => $profile['nama'],
                    'email'         => $profile['email'],
                    'prodi'         => $profile['prodi'],
                    'fakultas'      => $profile['fakultas'],
                    'namakotalahir' => $profile['namakotalahir'] ?? $profile['tempatlahir'],
                    'tgllahir'      => $profile['tgllahir'] ?? $profile['tanggallahir'],
                    'jenjang'      => $profile['jenjang'] ?? 'admin',
                    'semester'      => $profile['semester'] ?? 1,
                    'otoritas'      => $profile['otoritas'],
                    'foto'          => "https://sia.unsurya.ac.id/tampil_foto.php?id=" . $profile['user_id'],
                    'access_token'  => $loginData['access_token'],
                    'refresh_token' => $loginData['refresh_token'],
                ];
                session()->set($sessionData);

                return redirect()->to('/dashboard');
            }
        }

        return redirect()->back()->with('error', 'NIM atau Password salah.');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }

    public function cdn_rum()
    {
        $json = $this->request->getJSON();
        if ($json) {
            log_message('info', 'RUM Analytics: ' . json_encode($json));
            return $this->response->setJSON(['status' => 'ok']);
        }
        return $this->response->setStatusCode(200);
    }
}
