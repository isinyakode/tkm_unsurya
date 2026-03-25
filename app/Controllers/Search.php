<?php

namespace App\Controllers;

use App\Libraries\SsoService;

class Search extends BaseController
{
    protected $sso;

    public function __construct()
    {
        $this->sso = new SsoService();
    }

    public function getMahasiswa()
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setStatusCode(401)->setJSON(['message' => 'Unauthorized']);
        }

        $nim = $this->request->getGet('nim');
        if($nim === session()->get('nim')){
              return $this->response->setJSON(['status' => false, 'message' => 'NIM Ini adalah ketua']);
        }
        // Ambil token dari session di sini
        $token = session()->get('refresh_token');

        if (!$nim) {
            return $this->response->setJSON(['status' => false, 'message' => 'NIM kosong']);
        }

        // Oper NIM dan Token ke library
        $result = $this->sso->getMahasiswaByNIM($nim, $token);

        // Jika di dd($result) masih null, lakukan pengecekan ini:
        if ($result === null) {
            // Cek apakah tokennya benar-benar ada
            // dd(['nim' => $nim, 'token_in_session' => $token]); 

            return $this->response->setJSON([
                'status' => false,
                'message' => 'Data tidak ditemukan atau API bermasalah'
            ]);
        }

        return $this->response->setJSON([
            'status' => true,
            'mahasiswa' => [
                'nim'      => $result['user_id'] ?? ($result['nim'] ?? '-'),
                'nama'     => $result['nama'] ?? '-',
                'namakotalahir'     => $result['namakotalahir'] ?? '-',
                'tgllahir'     => $result['tgllahir'] ?? '-',
                'prodi'    => $result['prodi'] ?? '-',
                'fakultas' => $result['fakultas'] ?? '-',
                'semester' => $result['semester'] ?? '-',
                'jenjang'  => $result['jenjang'] ?? '-',
            ]
        ]);
    }
}
