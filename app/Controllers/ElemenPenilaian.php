<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ElemenPenilaianModel;
use App\Models\ElemenKategoriModel;
use App\Models\KategoriKegiatanModel;

class ElemenPenilaian extends BaseController
{
    protected $ElemenPenilaianModel;
    protected $ElemenKategoriModel;
    protected $KategoriKegiatanModel;
    protected $helpers = ['form', 'text', 'auth', 'auth_helper'];

    public function __construct()
    {
        $this->ElemenPenilaianModel = new ElemenPenilaianModel();
        $this->ElemenKategoriModel = new ElemenKategoriModel();
        $this->KategoriKegiatanModel = new KategoriKegiatanModel();
    }

    // INDEX
    public function index()
    {
        $data = [
            'title'         => 'Manajemen Elemen Penilaian',
            'elemenList'     => $this->ElemenPenilaianModel->getElemenPenilaian(),
            'kategoriList'     => $this->KategoriKegiatanModel->findAll(),
        ];

        return view('Elemen-Penilaian/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Tambah Elemen Penilaian',
            'allKategori' => $this->KategoriKegiatanModel->findAll(),
            'KategoriElemen' => [] // Kosong karena ini halaman baru (create)
        ];

        return view('Elemen-Penilaian/form_elemen_penilaian', $data);
    }

    public function save()
    {
        $rules = [
            'nama_elemen_penilaian' => 'required|is_unique[elemen_penilaian.nama_elemen_penilaian]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $db = \Config\Database::connect();
        $db->transStart(); // Gunakan transaksi agar data sinkron

        // 1. Simpan ke elemen_penilaian
        $this->ElemenPenilaianModel->save((object)[
            'nama_elemen_penilaian' => $this->request->getPost('nama_elemen_penilaian')
        ]);

        $idElemen = $this->ElemenPenilaianModel->getInsertID();

        // 2. Simpan relasi ke elemen_kategori (Pivot Table)
        $penilaian = $this->request->getPost('penilaian'); // Ambil array dari JS
        if ($penilaian) {
            foreach ($penilaian as $p) {
                $db->table('elemen_kategori')->insert([
                    'id_elemen_penilaian' => $idElemen,
                    'id_kategori_kegiatan' => $p['id_kategori']
                ]);
            }
        }

        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            return redirect()->back()->with('error', 'Gagal menyimpan data.');
        }

        return redirect()->to('/elemen-penilaian')->with('success', 'Elemen Penilaian berhasil dibuat.');
    }

    // EDIT
    public function edit($nama_elemen)
    {
        $data = [
            'title'         => 'Edit Elemen Penilaian',
            'ElemenList'     => $this->ElemenPenilaianModel->getElemenPenilaian(null, $nama_elemen),
            'KategoriElemen'     => $this->KategoriKegiatanModel->getElemenPenilaian($nama_elemen),
            'KategoriList'     => $this->KategoriKegiatanModel->findAll(),
        ];
        // dd($data['KategoriList']);

        return view('Elemen-Penilaian/edit_elemen_penilaian', $data);
    }

    // PROSES Edit (UPDATE)
    public function update($id)
    {
        // 1. Validasi Nama (Sama seperti sebelumnya)
        $rules = [
            'nama_elemen_penilaian' => "required"
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // 2. Ambil data kategori dari form
        $penilaianForm = $this->request->getPost('penilaian') ?? [];

        // Ambil hanya ID kategorinya saja dari form untuk memudahkan perbandingan
        $idKategoriBaru = array_column($penilaianForm, 'id_kategori_kegiatan');

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // A. Update Nama Elemen
            $this->ElemenPenilaianModel->update($id, [
                'nama_elemen_penilaian' => $this->request->getPost('nama_elemen_penilaian')
            ]);

            // B. Sinkronisasi Detail Kategori (Cek satu per satu)

            // 1. Ambil data kategori yang saat ini ada di Database
            $currentData = $this->ElemenKategoriModel->getByElemenPenilaian($id);
            $idKategoriLama = array_column($currentData, 'id_kategori_kegiatan');

            // 2. Tentukan mana yang harus DIHAPUS 
            // (Ada di database, tapi tidak ada di form baru)
            $idUntukDihapus = array_diff($idKategoriLama, $idKategoriBaru);
            if (!empty($idUntukDihapus)) {
                $this->ElemenKategoriModel
                    ->where('id_elemen_penilaian', $id)
                    ->whereIn('id_kategori_kegiatan', $idUntukDihapus)
                    ->delete();
            }

            // 3. Tentukan mana yang harus DITAMBAH 
            // (Ada di form baru, tapi belum ada di database)
            $idUntukDitambah = array_diff($idKategoriBaru, $idKategoriLama);
            if (!empty($idUntukDitambah)) {
                $dataInsert = [];
                foreach ($idUntukDitambah as $idKat) {
                    $dataInsert[] = [
                        'id_elemen_penilaian'  => $id,
                        'id_kategori_kegiatan' => $idKat
                    ];
                }
                $this->ElemenKategoriModel->insertBatch($dataInsert);
            }

            $db->transComplete();
            return redirect()->to(base_url('elemen-penilaian'))->with('success', 'Data berhasil disinkronisasi.');
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }
    // PROSES Hapus (DELETE)
    public function delete($id)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        // 1. Hapus relasi di tabel jembatan terlebih dahulu
        $db->table('elemen_kategori')->where('id_elemen_penilaian', $id)->delete();

        // 2. Hapus data utama
        $db->table('elemen_penilaian')->where('id_elemen_penilaian', $id)->delete();

        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            return redirect()->back()->with('error', 'Gagal menghapus elemen penilaian.');
        }

        return redirect()->to('/elemen-penilaian')->with('success', 'Elemen penilaian berhasil dihapus.');
    }
}
