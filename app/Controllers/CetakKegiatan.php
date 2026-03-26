<?php

namespace App\Controllers;

use App\Models\KegiatanMahasiswaModel;
use App\Models\KegiatanAnggotaModel;
use App\Services\ActivityService;
use App\Services\CetakPdfService;

class CetakKegiatan extends BaseController
{
    protected $KegiatanMahasiswaModel;
    protected $KegiatanAnggotaModel;
    protected $ActivityService;
    protected $CetakPdfService;
    protected $session;

    public function __construct()
    {
        $this->KegiatanMahasiswaModel = new KegiatanMahasiswaModel();
        $this->KegiatanAnggotaModel = new KegiatanAnggotaModel();
        $this->ActivityService = new ActivityService();
        $this->CetakPdfService = new CetakPdfService();
        $this->session = session();
        helper('count_helper');
    }

    // CETAK SKPI
    public function cetak_skpi()
    {
        $nimSession = session()->get('nim');
        if (!$nimSession) {
            return redirect()->to('/login');
        }

        $mhs = $this->KegiatanAnggotaModel->getByNim($nimSession);
        if (!$mhs) {
            return redirect()->to('/Dashboard')->with('error', 'Belum ada kegiatan');
        }

        $allKegiatan = $this->KegiatanMahasiswaModel->getallKegiatanApprove($mhs['nim']);
        
        $total_kredit = 0;
        foreach ($allKegiatan as $k) {
            $total_kredit += (float)$k['total_kredit'];
        }

        $pdfOutput = $this->CetakPdfService->generateSkpi($mhs, $allKegiatan, $total_kredit, session()->get('jenjang'));

        return $this->response->setHeader('Content-Type', 'application/pdf')
            ->setBody($pdfOutput);
    }

    // CETAK TKM
    public function cetak_dan_merge()
    {
        // Fitur ini di-route sama dengan SKPI pada implementasi aslinya
        return $this->cetak_skpi();
    }

    // CETAK PIMPINAN
    public function cetak_semua_kegiatan()
    {
        helper(['auth_helper', 'auth']);
        if (is_warek() == false && is_kabiro() == false && is_kabag() == false) {
            return redirect()->to('/Dashboard')->with('error', 'Akses ditolak.');
        }

        if ($this->request->getMethod() !== 'POST') {
            return redirect()->to(base_url('/Dashboard'));
        }

        $topMahasiswa = $this->KegiatanMahasiswaModel->getPointMahasiswa();
        $postData = $this->request->getPost();
        
        $sessionData = [
            'nama' => session()->get('nama'),
            'nim'  => session()->get('nim'),
            'is_warek' => is_warek(),
            'is_kabiro' => is_kabiro(),
            'is_kabag' => is_kabag()
        ];

        $pdfOutput = $this->CetakPdfService->generateLaporanPimpinan($postData, $topMahasiswa, $sessionData);

        return $this->response->setHeader('Content-Type', 'application/pdf')
            ->setBody($pdfOutput);
    }
}
