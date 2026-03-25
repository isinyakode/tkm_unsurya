<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\JenisElemenModel;
use App\Models\JenisKegiatanMahasiswaModel;
use App\Models\ElemenPenilaianModel;
use App\Models\KategoriKegiatanModel;
use App\Models\KegiatanMahasiswaModel;
use App\Models\KegiatanAnggotaModel;
use App\Models\KegiatanPenilaianModel;
use App\Models\KreditPenilaianModel;
use App\Models\LogsModel;
use App\Services\ActivityService;
use CodeIgniter\Exceptions\PageNotFoundException;

class Verifikasi extends BaseController
{
    protected $ActivityService;
    protected $JenisElemenModel;
    protected $JenisKegiatanMahasiswaModel;
    protected $ElemenPenilaianModel;
    protected $KategoriKegiatanMahasiswaModel;
    protected $KegiatanMahasiswaModel;
    protected $KegiatanAnggotaModel;
    protected $KegiatanPenilaianModel;
    protected $KreditPenilaianModel;
    protected $LogsModel;
    protected $session;
    protected $db;

    public function __construct()
    {
        $this->ActivityService = new ActivityService();
        $this->JenisElemenModel = new JenisElemenModel();
        $this->JenisKegiatanMahasiswaModel = new JenisKegiatanMahasiswaModel();
        $this->ElemenPenilaianModel = new ElemenPenilaianModel();
        $this->KategoriKegiatanMahasiswaModel = new KategoriKegiatanModel();
        $this->KegiatanMahasiswaModel = new KegiatanMahasiswaModel();
        $this->KegiatanAnggotaModel = new KegiatanAnggotaModel();
        $this->KegiatanPenilaianModel = new KegiatanPenilaianModel();
        $this->KreditPenilaianModel = new KreditPenilaianModel();
        $this->LogsModel = new LogsModel();
        $this->session = session();
    }

    public function index()
    {
        // Cukup panggil 'auth', CI4 otomatis mencari auth_helper.php
        helper(['auth_helper', 'auth']);

        $otoritas_aktif = "";

        $otoritas_aktif = get_otoritas_verifikasi();
        if (!$otoritas_aktif) {
            return redirect()->to('/Dashboard')->with('error', 'Akses ditolak.');
        }
        // dd($otoritas_aktif);
        $kegiatan = $this->KegiatanMahasiswaModel->getkegiatanverifikasi($otoritas_aktif);
        // dd($otoritas_aktif);
        $data = [
            'title' => "Verifikasi Dokumen",
            'kegiatan' => $kegiatan,
            'otoritas' => $otoritas_aktif,
            'title_aksi' => (is_warek()) ? "Persetujuan Wakil Rektor III" : "Verifikasi Biro Kemahasiswaan"
        ];

        return view('Verifikasi/index', $data);
    }

    public function detail_dokumen($slug, $nim)
    {
        helper(['cetak_helper', 'status_badge_helper']);
        // $otoritas = session()->get('otoritas') ?? 'KABIRO KEMAHASISWAAN';
        helper(['auth_helper', 'auth']);

        $otoritas_aktif = "";

        $otoritas_aktif = get_otoritas_verifikasi();
        if (!$otoritas_aktif) {
            return redirect()->to('/Dashboard')->with('error', 'Akses ditolak.');
        }
        // 1. Ambil data utama kegiatan JOIN dengan data anggota
        // Kita gunakan builder untuk fleksibilitas join antar tabel
        // 1. Ambil data utama kegiatan JOIN dengan data anggota
        // Kita gunakan function model getDetailPengajuan
        $kegiatan = $this->KegiatanMahasiswaModel->getDetailPengajuan($slug, $nim);
        if (!$kegiatan) {
            return redirect()->to('/pengajuan-kegiatan')->with('error', 'Data tidak ditemukan.');
        }
        $logs = $this->LogsModel->getLogs($kegiatan['id_kegiatan']);
        // ANGGOTA DAN PERAN
        $anggota = $this->KegiatanAnggotaModel->getAnggotaLain($kegiatan['id_kegiatan'], null);

        // 2. Siapkan data untuk dikirim ke View
        $data = [
            'title' => "Pratinjau & Verifikasi",
            'nama_kegiatan' => $kegiatan['nama_kegiatan'],
            'kegiatan' => $kegiatan,
            'anggota' => $anggota,
            'otoritas' => $otoritas_aktif,
            'logs' => $logs,
        ];

        return view('Verifikasi/detail_dokumen', $data);
    }

    public function proses_verifikasi()
    {
        $otoritas_aktif = get_otoritas_verifikasi();
        if (!$otoritas_aktif) {
            return redirect()->to('/Dashboard')->with('error', 'Akses ditolak.');
        }

        // Validasi input
        if (!$this->validate([
        'status_pengajuan' => 'required',
        'id_kegiatan' => 'required'
        ])) {
            return redirect()->back()->withInput()->with('error', 'Data tidak lengkap.');
        }

        $id_kegiatan = $this->request->getPost('id_kegiatan');
        $status_baru = $this->request->getPost('status_pengajuan');
        $catatan = $this->request->getPost('catatan');
        $verifikator = session()->get('nama');

        // 1. Ambil status lama untuk log
        $kegiatanLama = $this->KegiatanMahasiswaModel->find($id_kegiatan);
        $status_lama = $kegiatanLama['status_pengajuan'];

        $this->KegiatanMahasiswaModel->update($id_kegiatan, [
            'status_pengajuan' => $status_baru,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        $this->LogsModel->insert(
            (object)[
            'id_kegiatan' => $id_kegiatan,
            'status_lama' => $status_lama,
            'status_baru' => $status_baru,
            'catatan_verifikator' => $catatan,
            'verifikator' => $verifikator,
            'created_at' => date('Y-m-d H:i:s')
        ]
        );

        return redirect()->to('/verifikasi-dokumen')->with('success', 'Status berhasil diperbarui ke: ' . $status_baru);
    }

    public function SetujuiBatch()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $ids = $this->request->getPost('ids');

        if (empty($ids) || !is_array($ids)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Tidak ada data yang dipilih.'
            ]);
        }

        try {
            // Gunakan whereIn untuk mengupdate banyak ID sekaligus
            $this->KegiatanMahasiswaModel->whereIn('id_kegiatan', $ids)
                ->set(['status_pengajuan' => 'Disetujui', 'updated_at' => date('Y-m-d H:i:s')])
                ->update();

            // Opsional: Simpan log untuk setiap kegiatan (Bulk Insert ke Logs)
            foreach ($ids as $id) {
                $this->ActivityService->SaveLogs($id, 'Diajukan', 'Disetujui');
            }

            return $this->response->setJSON([
                'status' => 'success',
                'message' => count($ids) . ' pengajuan berhasil disetujui.',
                'token' => csrf_hash() // Kirim token baru
            ]);
        }
        catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
}
