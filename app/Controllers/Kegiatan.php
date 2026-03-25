<?php

namespace App\Controllers;

use App\Models\JenisKegiatanMahasiswaModel;
use App\Models\KategoriKegiatanModel;
use App\Models\KegiatanMahasiswaModel;
use App\Models\LogsModel;
use App\Models\KegiatanAnggotaModel;

class Kegiatan extends BaseController
{
    protected $JenisKegiatanMahasiswaModel;
    protected $KategoriKegiatanModel;
    protected $KegiatanMahasiswaModel;
    protected $LogsModel;
    protected $KegiatanAnggotaModel;
    protected $session;
    public function __construct()
    {
        $this->JenisKegiatanMahasiswaModel = new JenisKegiatanMahasiswaModel();
        $this->KategoriKegiatanModel = new KategoriKegiatanModel();
        $this->KegiatanMahasiswaModel = new KegiatanMahasiswaModel();
        $this->LogsModel = new LogsModel();
        $this->KegiatanAnggotaModel = new KegiatanAnggotaModel();
        $this->session = session();
        helper(['auth_helper', 'auth', 'count_helper', 'cetak_helper']);
    }

    public function index()
    {
        // dd($this->session->get());
        $nim_pengaju = $this->session->get('nim');
        $kegiatan = $this->KegiatanMahasiswaModel->getKegiatan($nim_pengaju);
        $data = [
            'title' => "Kegiatan",
            'kegiatan' => $kegiatan,
        ];
        return view('Kegiatan/index', $data);
    }

    public function jenis_pengajuan()
    {
        // dd($this->session->get());
        $nim_pengaju = $this->session->get('nim');
        $jenis_kegiatan = $this->JenisKegiatanMahasiswaModel->findAll();
        $cek_pkkmb = $this->KegiatanMahasiswaModel->getnim($nim_pengaju);
        if ($cek_pkkmb) {
            $jenis_kegiatan = $this->JenisKegiatanMahasiswaModel->getNonPkkmb();
        }
        $data = [
            'title' => "Jenis Pengajuan",
            'jenis_kegiatan' => $jenis_kegiatan,
        ];
        return view('Kegiatan/jenis_pengajuan', $data);
    }

    public function detail_pengajuan($nim = null, $slug = null)
    {
        $data_kegiatan = $this->KegiatanMahasiswaModel->getDetailPengajuan($slug, $nim);
        // dd($data_kegiatan);
        if (!$data_kegiatan) {
            // Jika data tidak ada atau NIM tidak terdaftar di kegiatan tersebut
            return redirect()->to('/Dashboard')->with('error', 'Data Tidak Ditemukan!');
        }
        // dd($data_kegiatan);
        $mhs = [
            'nama'          => $data_kegiatan['nama'],
            'peran'         => $data_kegiatan['peran'],
            'nim'           => $data_kegiatan['nim'],
            'prodi'         => $data_kegiatan['prodi'],
            'fakultas'      => $data_kegiatan['fakultas'],
            'jenjang'       => $data_kegiatan['jenjang'],
            'tempat_lahir'  => $data_kegiatan['tempat_lahir'],
            'tanggal_lahir' => $data_kegiatan['tanggal_lahir'],
            'semester'      => $data_kegiatan['semester'],
        ];
        // dd($mhs);

        // Ambil data detail dengan verifikasi akses NIM
        $pengajuan = $this->KegiatanMahasiswaModel->getDetailPengajuan($slug, $mhs['nim']);
        // dd($pengajuan);
        if (!$pengajuan) {
            // Jika data tidak ada atau NIM tidak terdaftar di kegiatan tersebut
            return redirect()->to('/Dashboard')->with('error', 'Anda tidak memiliki akses ke detail pengajuan ini.');
        }

        // dd($data_kegiatan['id_jenis_kegiatan']);
        // Ambil data pengajuan berdasarkan slug
        $kriteria_penilaian = $this->KegiatanMahasiswaModel->getKriteriaPenilaian($data_kegiatan['id_jenis_kegiatan']);
        // dd($kriteria_penilaian);
        if (!$kriteria_penilaian) {
            return redirect()->to('/pengajuan-kegiatan')->with('error', 'Data pengajuan tidak ditemukan.');
        }
        // ANGGOTA DAN PERAN
        $anggota = $this->KegiatanAnggotaModel->getAnggotaLain($pengajuan['id_kegiatan'], $mhs['peran']);

        $data = [
            'title'    => 'Detail Pengajuan',
            'jenis_pengajuan'    => $pengajuan['nama_kegiatan'],
            'logs' => $this->LogsModel->getLogs($pengajuan['id_kegiatan']),
            'kegiatan' => $pengajuan,
            'anggota'    => $anggota,
            'peran'    => $pengajuan['peran'],
            'kriteria_penilaian'    => $kriteria_penilaian,
            'file_url' => base_url('uploads/tkm/' . $pengajuan['nim_pengaju'] . '/' . $slug . '/' . $pengajuan['file_laporan'])
        ];
        // dd($data['kriteria_penilaian']);
        return view('Kegiatan/detail_pengajuan', $data);
    }
}
