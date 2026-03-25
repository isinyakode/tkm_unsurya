<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\JenisKegiatanMahasiswaModel; // Sesuaikan jika nama modelnya JenisKegiatanModel
use App\Models\ElemenPenilaianModel;
use App\Models\KategoriKegiatanModel;
use App\Models\KreditPenilaianModel;
use App\Models\ElemenKategoriModel;

class JenisKegiatan extends BaseController
{
    protected $JenisKegiatanMahasiswaModel;
    protected $ElemenPenilaianModel;
    protected $ElemenKategoriModel;
    protected $KategoriKegiatanModel;
    protected $KreditPenilaianModel;
    protected $session;
    protected $helpers = ['form', 'text', 'auth', 'auth_helper'];

    public function __construct()
    {
        $this->JenisKegiatanMahasiswaModel = new JenisKegiatanMahasiswaModel();
        $this->ElemenKategoriModel = new ElemenKategoriModel();
        $this->ElemenPenilaianModel = new ElemenPenilaianModel();
        $this->KategoriKegiatanModel = new KategoriKegiatanModel();
        $this->KreditPenilaianModel = new KreditPenilaianModel();
        $this->session = session();
        helper('form', 'url');
    }

    // LIST / INDEX
    public function index()
    {
        $nim = $this->session->get('nim');
        if ($nim == null) {
            return redirect()->to('Dashboard');
        }

        $data = [
            'title'         => 'Manajemen Jenis Kegiatan',
            'jenisList'     => $this->JenisKegiatanMahasiswaModel->findAll(),
        ];

        return view('Jenis-Kegiatan/index', $data);
    }

    // public function getkategori($id_elemen)
    // {
    //     $get_kategori = $this->ElemenKategoriModel->get_elemen_kategori($id_elemen);

    //     return $this->response->setJSON($get_kategori);
    // }

    // FORM TAMBAH
    public function create()
    {
        $data = [
            'title' => 'Tambah Jenis Kegiatan',
            'ElemenList'     => $this->ElemenPenilaianModel->findAll(),
            'relasiList' => $this->ElemenKategoriModel
                ->select('elemen_kategori.id_elemen_penilaian, kategori_kegiatan.id_kategori_kegiatan, kategori_kegiatan.nama_kategori_kegiatan')
                ->join('kategori_kegiatan', 'kategori_kegiatan.id_kategori_kegiatan = elemen_kategori.id_kategori_kegiatan')
                ->findAll(),
        ];

        return view('Jenis-Kegiatan/form_jenis_kegiatan', $data);
    }

    // PROSES SIMPAN (CREATE)
    public function store()
    {
        // 1. ATURAN VALIDASI
        $rules = [
            'nama_jenis_kegiatan' => [
                'rules'  => 'required|min_length[3]|is_unique[jenis_kegiatan.jenis_kegiatan]',
                'errors' => [
                    'required'   => 'Nama Jenis Kegiatan wajib diisi.',
                    'min_length' => 'Nama terlalu pendek.',
                    'is_unique'  => 'Nama Jenis Kegiatan ini sudah terdaftar.'
                ]
            ],
            'peran_mode' => [
                'rules'  => 'required',
                'errors' => ['required' => 'Mode Peran harus dipilih.']
            ],
            'penilaian' => [
                'rules'  => 'required',
                'errors' => ['required' => 'Anda harus menambahkan minimal satu item penilaian.']
            ]
        ];

        // 2. JALANKAN VALIDASI
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator->getErrors());
        }

        // 3. PERSIAPAN DATA UTAMA (Tabel jenis_kegiatan)
        $namaJenis = $this->request->getPost('nama_jenis_kegiatan');

        $dataJenis = [
            'jenis_kegiatan'           => $namaJenis,
            'slug_jenis_kegiatan'      => url_title($namaJenis, '-', true),
            'deskripsi_jenis_kegiatan' => $this->request->getPost('deskripsi_jenis_kegiatan'),
            'color_icon'               => $this->request->getPost('color_icon') ?: 'dark',
            'peran_mode'               => $this->request->getPost('peran_mode'),
            'peran_default'            => $this->request->getPost('peran_default'),
            'label_jenis'              => $this->request->getPost('label_jenis'),

            // Handle Checkbox (Value 1 jika dicentang, 0 jika tidak)
            'show_tanggal'   => $this->request->getPost('show_tanggal') ?? 0,
            'show_anggota'   => $this->request->getPost('show_anggota') ?? 0,
            'show_lokasi'    => $this->request->getPost('show_lokasi') ?? 0,
            'show_deskripsi' => $this->request->getPost('show_deskripsi') ?? 0,
        ];

        // 4. PROSES TRANSAKSI DATABASE
        $db = \Config\Database::connect();
        $db->transStart(); // Mulai Transaksi

        try {
            // A. Simpan Jenis Kegiatan (Parent)
            $this->JenisKegiatanMahasiswaModel->insert((object)$dataJenis);
            $idJenisBaru = $this->JenisKegiatanMahasiswaModel->getInsertID();

            // B. Simpan Detail Penilaian (Tabel kredit_penilaian)
            $penilaianInput = $this->request->getPost('penilaian');

            if ($penilaianInput && is_array($penilaianInput)) {
                $dataKredit = [];

                foreach ($penilaianInput as $p) {
                    if (!empty($p['id_elemen']) && !empty($p['id_kategori'])) {

                        $relasi = $this->ElemenKategoriModel
                            ->where('id_elemen_penilaian', $p['id_elemen'])
                            ->where('id_kategori_kegiatan', $p['id_kategori'])
                            ->first();

                        // Hanya simpan jika relasi ditemukan di database
                        if ($relasi) {
                            $dataKredit[] = [
                                'id_jenis_kegiatan'    => $idJenisBaru,
                                'id_elemen_kategori'   => $relasi['id_elemen_kategori'],
                                'id_elemen_penilaian'  => $p['id_elemen'],
                                'id_kategori_kegiatan' => $p['id_kategori'],
                                'kredit_score'         => $p['kredit'] ?? 0
                            ];
                        }
                    }
                }

                // Insert Batch jika ada data valid
                if (count($dataKredit) > 0) {
                    $this->KreditPenilaianModel->insertBatch($dataKredit);
                }
            }

            $db->transComplete(); // Selesai Transaksi

            if ($db->transStatus() === false) {
                // Jika database menolak (misal duplicate entry atau foreign key error)
                throw new \Exception('Gagal menyimpan data ke database.');
            }

            return redirect()->to('/jenis-kegiatan')->with('success', 'Jenis kegiatan berhasil ditambahkan!');
        } catch (\Exception $e) {
            // $db->transRollback(); // Opsional, transComplete otomatis handle rollback
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    public function edit($slug)
    {
        // 1. Ambil data utama jenis kegiatan
        $jenis = $this->JenisKegiatanMahasiswaModel->getslug($slug);

        // 2. Ambil semua elemen untuk dropdown modal
        $elemenList = $this->ElemenPenilaianModel->findAll();

        // 3. Ambil data relasi elemen_kategori untuk filter dropdown dinamis di JS
        // Kita join agar JS tahu elemen A punya kategori apa saja
        $db = \Config\Database::connect();
        $relasi = $this->ElemenKategoriModel->getRelasiKategori();

        // 4. Ambil data yang SUDAH TERSIMPAN untuk jenis kegiatan ini (Item Penilaian)
        // Asumsi ada tabel 'kredit_penilaian' yang menghubungkan jenis_kegiatan dengan elemen_kategori
        $itemTersimpan = $this->KreditPenilaianModel->getItemTersimpan($jenis['id_jenis_kegiatan']);

        $data = [
            'title'      => 'Edit Jenis Kegiatan',
            'jenis'      => $jenis,
            'ElemenList' => $elemenList,
            'relasi'     => $relasi,
            'itemSaved'  => $itemTersimpan
        ];

        return view('Jenis-Kegiatan/edit_jenis_kegiatan', $data);
    }

    public function update($slug)
    {
        $jenis_kegiatan = $this->JenisKegiatanMahasiswaModel->getslug($slug);
        // 1. Update data utama Jenis Kegiatan
        $dataUpdate = [
            'id_jenis_kegiatan' => $jenis_kegiatan['id_jenis_kegiatan'],
            'jenis_kegiatan'    => $this->request->getPost('nama_jenis_kegiatan'),
            'color_icon'        => $this->request->getPost('color_icon'),
            'peran_mode'        => $this->request->getPost('peran_mode'),
            'peran_default'     => $this->request->getPost('peran_default'),
            'label_jenis'       => $this->request->getPost('label_jenis'),
            'show_tanggal'      => $this->request->getPost('show_tanggal') ?? 0,
            'show_anggota'      => $this->request->getPost('show_anggota') ?? 0,
            'show_lokasi'       => $this->request->getPost('show_lokasi') ?? 0,
            'show_deskripsi'    => $this->request->getPost('show_deskripsi') ?? 0,
            'deskripsi_jenis_kegiatan' => $this->request->getPost('deskripsi_jenis_kegiatan'),
        ];

        $this->JenisKegiatanMahasiswaModel->save((object)$dataUpdate);

        // 2. Update Item Penilaian (Tabel Relasi)
        $db = \Config\Database::connect();
        $penilaian = $this->request->getPost('penilaian');

        // Hapus data lama, lalu masukkan yang baru (Sync)
        $this->KreditPenilaianModel->deleteByJenisKegiatan($jenis_kegiatan['id_jenis_kegiatan']);

        if ($penilaian) {
            foreach ($penilaian as $p) {
                $this->KreditPenilaianModel->insert((object)[
                    'id_jenis_kegiatan'    => $jenis_kegiatan['id_jenis_kegiatan'],
                    'id_elemen_penilaian'  => $p['id_elemen'],
                    'id_kategori_kegiatan' => $p['id_kategori'],
                    'kredit_score'         => $p['kredit']
                ]);
            }
        }

        return redirect()->to('/jenis-kegiatan')->with('success', 'Data berhasil diperbarui');
    }

    // HAPUS
    public function delete($slug = null)
    {
        if (!$slug) return redirect()->back();

        $jenis = $this->JenisKegiatanMahasiswaModel->getslug($slug);

        if ($jenis) {
            // Soft delete otomatis karena property $useSoftDeletes = true di Model
            $this->JenisKegiatanMahasiswaModel->delete($jenis['id_jenis_kegiatan']);

            return redirect()->to('/jenis-kegiatan')->with('success', 'Jenis kegiatan berhasil dihapus.');
        }

        return redirect()->back()->with('error', 'Data tidak ditemukan.');
    }
}
