<?php

namespace App\Controllers;

use App\Controllers\BaseController;
// use App\Models\JenisElemenModel;
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

class Pengajuan extends BaseController
{
    protected $ActivityService;
    // protected $JenisElemenModel;
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
        // $this->JenisElemenModel   = new JenisElemenModel();
        $this->JenisKegiatanMahasiswaModel = new JenisKegiatanMahasiswaModel();
        $this->ElemenPenilaianModel = new ElemenPenilaianModel();
        $this->KategoriKegiatanMahasiswaModel = new KategoriKegiatanModel();
        $this->KegiatanMahasiswaModel = new KegiatanMahasiswaModel();
        $this->KegiatanAnggotaModel = new KegiatanAnggotaModel();
        $this->KegiatanPenilaianModel = new KegiatanPenilaianModel();
        $this->KreditPenilaianModel = new KreditPenilaianModel();
        $this->LogsModel = new LogsModel();
        $this->session = session();
        helper(['auth_helper', 'auth', 'tkm_form_helper']);
    }

    public function form($slug)
    {
        $jenis_kegiatan = $this->JenisKegiatanMahasiswaModel->getslug($slug);
        if (!$jenis_kegiatan) {
            throw PageNotFoundException::forPageNotFound('Jenis kegiatan tidak ditemukan.');
        }
        // dd($jenis_kegiatan['id_jenis_kegiatan']);
        $namaJenis = $jenis_kegiatan['jenis_kegiatan'];
        $slugJenis = $jenis_kegiatan['slug_jenis_kegiatan'];
        // dd($slugJenis);
        $data = array_merge([
            'title' => "Formulir",
            'jenis_kegiatan' => $namaJenis,
            'slug_jenis_kegiatan' => $slugJenis,

            'tingkat' => $this->JenisKegiatanMahasiswaModel->getTingkatKegiatan($jenis_kegiatan['id_jenis_kegiatan']),
            'peran' => $this->JenisKegiatanMahasiswaModel->getPeranKegiatan($jenis_kegiatan['id_jenis_kegiatan']),
            'prestasi' => $this->JenisKegiatanMahasiswaModel->getPrestasiKegiatan($jenis_kegiatan['id_jenis_kegiatan']),
            'waktu' => $this->JenisKegiatanMahasiswaModel->getWaktuKegiatan($jenis_kegiatan['id_jenis_kegiatan']),
            'badan_hukum' => $this->JenisKegiatanMahasiswaModel->getBadanHukumKegiatan($jenis_kegiatan['id_jenis_kegiatan']),
            'omzet' => $this->JenisKegiatanMahasiswaModel->getOmzetKegiatan($jenis_kegiatan['id_jenis_kegiatan']),
            'jenis' => $this->JenisKegiatanMahasiswaModel->getJenisKegiatan($jenis_kegiatan['id_jenis_kegiatan']),
            'media_sosial' => $this->JenisKegiatanMahasiswaModel->getMedsosKegiatan($jenis_kegiatan['id_jenis_kegiatan']),
        ], get_jenis_kegiatan_config($jenis_kegiatan));
        // dd($data['jenis']);
        return view('Pengajuan/form_kegiatan', $data);
    }

    public function add_pengajuan_kegiatan($slug)
    {
        $db = \Config\Database::connect();
        // $idJenis = $this->request->getPost('id_jenis_kegiatan');

        $setup = $this->JenisKegiatanMahasiswaModel->getslug($slug);
        if (!$setup)
            return redirect()->back()->with('error', 'Jenis kegiatan tidak valid.');

        // 1. Validasi Dasar
        $rules = [
            'laporan' => [
                'rules' => 'uploaded[laporan]|max_size[laporan,2048]|ext_in[laporan,pdf]',
                'label' => 'File Laporan'
            ]
        ];

        // 2. Validasi Dinamis (Jika bukan PKKMB)
        // 2. Validasi Dinamis (Jika bukan PKKMB)
        $rules = array_merge($rules, get_dynamic_validation_rules($setup, $setup['id_jenis_kegiatan']));
        // dd($rules);
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', $this->validator->getErrors());
        }

        // // 3. Eksekusi
        // dd($this->request->getPost());
        $success = $this->ActivityService->submitActivity(
            $setup['id_jenis_kegiatan'],
            $this->request->getPost(),
            $this->request->getFile('laporan')
        );
        // dd($success);

        return $success
            ? redirect()->to('/pengajuan-kegiatan')->with('success', 'Data Berhasil Disimpan')
            : redirect()->back()->withInput()->with('error', 'Gagal menyimpan data ke database. Periksa log error.');
    }

    public function edit($slug)
    {
        helper(['cetak_helper', 'status_badge_helper']);
        $db = \Config\Database::connect();

        // 1. Ambil kegiatan utama
        $kegiatan = $this->KegiatanMahasiswaModel->getBySlug($slug);

        if (!$kegiatan) {
            throw new \RuntimeException("Data kegiatan tidak ditemukan.");
        }

        $idK = $kegiatan['id_kegiatan'];
        $idJenis = $kegiatan['id_jenis_kegiatan'];

        // 2. Ambil setting jenis kegiatan (WAJIB karena view memerlukan)
        $jenis_kegiatan = $this->JenisKegiatanMahasiswaModel->find($idJenis);

        // 3. Ambil elemen penilaian yang aktif untuk jenis kegiatan
        $elemenAktif = $db->table('jenis_elemen je')
            ->join('elemen_penilaian ep', 'je.id_elemen_penilaian = ep.id_elemen_penilaian')
            ->where('je.id_jenis_kegiatan', $idJenis)
            ->get()->getResultArray();

        // 4. Ambil penilaian lama
        $penilaianLama = $db->table('kegiatan_mahasiswa_penilaian kmp')
            ->join('kegiatan_mahasiswa km', 'km.id_kegiatan = kmp.id_kegiatan')
            ->join('jenis_kegiatan jk', 'jk.id_jenis_kegiatan = km.id_jenis_kegiatan')
            ->join('kredit_penilaian kp', 'kp.id_jenis_kegiatan = jk.id_jenis_kegiatan')
            ->join('elemen_penilaian ep', 'ep.id_elemen_penilaian = kp.id_elemen_penilaian')
            ->where('kmp.id_kegiatan', $idK)
            ->get()->getResultArray();

        $selectedValues = [];
        foreach ($penilaianLama as $p) {
            $selectedValues[$p['request_key']] = $p['id_kategori_kegiatan'];
            $namaJenis = $p['jenis_kegiatan'] ?? '';
        }

        // 5. Ambil pilihan kategori berdasarkan elemen
        $tingkat      = $this->JenisKegiatanMahasiswaModel->getTingkatKegiatan($idJenis);
        $peran        = $this->JenisKegiatanMahasiswaModel->getPeranKegiatan($idJenis);
        $prestasi     = $this->JenisKegiatanMahasiswaModel->getPrestasiKegiatan($idJenis);
        $waktu        = $this->JenisKegiatanMahasiswaModel->getWaktuKegiatan($idJenis);
        $badan_hukum  = $this->JenisKegiatanMahasiswaModel->getBadanHukumKegiatan($idJenis);
        $omzet        = $this->JenisKegiatanMahasiswaModel->getOmzetKegiatan($idJenis);
        $jenis_kategori = $this->JenisKegiatanMahasiswaModel->getJenisKegiatan($idJenis);
        $media_sosial = $this->JenisKegiatanMahasiswaModel->getMedsosKegiatan($idJenis);

        // 6. Ambil anggota lama
        $anggotaLama = $this->KegiatanAnggotaModel->getAnggotaLain($idK, "Ketua");
        $anggotaJson = json_encode($anggotaLama);

        // 7. Kirim ke view
        $data = array_merge([
            'title'            => "Update Formulir " . ($namaJenis ?? ''),
            'kegiatan'         => $kegiatan,
            'elemenAktif'      => $elemenAktif,
            'selectedValues'   => $selectedValues,

            // kategori untuk dropdown
            'tingkat'           => $tingkat,
            'peran'             => $peran,
            'prestasi'          => $prestasi,
            'waktu'             => $waktu,
            'badan_hukum'       => $badan_hukum,
            'omzet'             => $omzet,
            'jenis'             => $jenis_kategori,
            'media_sosial'      => $media_sosial,

            // anggota
            'anggota_lama'     => $anggotaLama,
            'anggotaJson'      => $anggotaJson,

            'logs' => $this->LogsModel->getLogs($idK),
        ], get_jenis_kegiatan_config($jenis_kegiatan));
        // dd($data);
        return view('Pengajuan/edit_kegiatan', $data);
    }

    public function update_pengajuan($slug)
    {
        // dd($this->request->getPost());
        $db = \Config\Database::connect();

        // 1. Cari data lama berdasarkan slug
        $kegiatan = $this->KegiatanMahasiswaModel->getBySlug($slug);
        // dd($kegiatan);
        if (!$kegiatan) {
            return redirect()->back()->with('error', 'Data kegiatan tidak ditemukan.');
        }

        $idJenis = $this->request->getPost('id_jenis_kegiatan');
        $setup = $this->JenisKegiatanMahasiswaModel->find($idJenis);
        // dd($setup);
        if (!$setup)
            return redirect()->back()->with('error', 'Jenis kegiatan tidak valid.');

        // 2. Validasi Dasar (Sama dengan ADD, tapi laporan jadi opsional/permit_empty)
        $rules = [
            'id_jenis_kegiatan' => 'required',
            'laporan' => [
                'rules' => 'permit_empty|max_size[laporan,2048]|ext_in[laporan,pdf]',
                'label' => 'File Laporan'
            ]
        ];

        // 3. Validasi Dinamis (Logika yang sama dengan fungsi ADD Anda)
        $rules = array_merge($rules, get_dynamic_validation_rules($setup, $idJenis));

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // 4. Eksekusi melalui Service
        // Kita kirimkan ID Kegiatan dan data lama untuk handle file
        // dd($this->request->getPost());
        $success = $this->ActivityService->updateActivity(
            $kegiatan['id_kegiatan'],
            $this->request->getPost(),
            $this->request->getFile('laporan'),
            $kegiatan
        );

        // dd($kegiatan['id_kegiatan'], $this->request->getPost(), $this->request->getFile('laporan'), $kegiatan);

        return $success
            ? redirect()->to('/pengajuan-kegiatan')->with('success', 'Data Berhasil Diperbarui')
            : redirect()->back()->withInput()->with('error', 'Gagal memperbarui data. Periksa log error.');
    }

    public function delete($slug = null)
    {
        $kegiatan = $this->KegiatanMahasiswaModel->getBySlug($slug);
        $success = $this->ActivityService->softDeleteActivity($kegiatan['id_kegiatan']);

        if ($success) {
            return redirect()->to('/pengajuan-kegiatan')->with('success', 'Pengajuan berhasil dihapus dan file telah dibersihkan.');
        }
        else {
            return redirect()->back()->with('error', 'Gagal menghapus data.');
        }
    }
}
