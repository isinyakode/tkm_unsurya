<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\KategoriKegiatanModel;

class KategoriKegiatan extends BaseController
{
    protected $KategoriKegiatanModel;
    protected $helpers = ['form', 'text', 'auth', 'auth_helper'];

    public function __construct()
    {
        $this->KategoriKegiatanModel = new KategoriKegiatanModel();
    }

    // INDEX
    public function index()
    {
        $data = [
            'title'         => 'Manajemen Kategori Kegiatan',
            'kategoriList'  => $this->KategoriKegiatanModel->findAll(),
        ];

        return view('Kategori-Kegiatan/index', $data);
    }

    // PROSES SIMPAN (CREATE)
    public function save()
    {
        $rules = [
            'nama_kategori_kegiatan' => [
                'rules'  => 'required|min_length[3]|is_unique[kategori_kegiatan.nama_kategori_kegiatan]',
                'errors' => [
                    'required'   => 'Nama kategori tidak boleh kosong.',
                    'min_length' => 'Nama kategori minimal 3 karakter.',
                    'is_unique'  => 'Nama kategori sudah ada.'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('error', $this->validator->getErrors())
                ->with('open_modal', 'tambah');
        }

        $namaKategori = $this->request->getPost('nama_kategori_kegiatan');
        $slug = url_title($namaKategori, '-', true);

        $this->KategoriKegiatanModel->insert((object)[
            'slug_kategori_kegiatan' => $slug,
            'nama_kategori_kegiatan' => $namaKategori,
            'user' => session()->get('nama') ?? 'System' // Fallback jika session kosong
        ]);

        return redirect()->to('/kategori-kegiatan')->with('success', 'Kategori berhasil ditambahkan.');
    }

    // PROSES Edit (UPDATE)
    public function update($slug = null)
    {
        if ($slug === null) return redirect()->back();

        // Cari data lama
        $kategoriLama = $this->KategoriKegiatanModel->getBySlug($slug);
        if (!$kategoriLama) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        // Validasi: Unique check harus ignore ID data yang sedang diedit
        $id = $kategoriLama['id_kategori_kegiatan'];
        $rules = [
            'nama_kategori_kegiatan' => "required|min_length[3]|is_unique[kategori_kegiatan.nama_kategori_kegiatan,id_kategori_kegiatan,{$id}]"
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('error', $this->validator->getErrors())
                ->with('open_modal', 'edit');
        }

        $namaBaru = $this->request->getPost('nama_kategori_kegiatan');

        $this->KategoriKegiatanModel->update($id, [
            'nama_kategori_kegiatan' => $namaBaru,
            'user' => session()->get('nama') ?? 'System',
            'slug_kategori_kegiatan' => url_title($namaBaru, '-', true)
        ]);

        return redirect()->to('/kategori-kegiatan')->with('success', 'Kategori berhasil diperbarui.');
    }

    // PROSES Hapus (DELETE)
    public function delete($slug = null)
    {
        if ($slug === null) {
            return redirect()->back()->with('error', 'Parameter slug tidak ditemukan.');
        }

        $kategori = $this->KategoriKegiatanModel->getBySlug($slug);

        if (!$kategori) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        $this->KategoriKegiatanModel->delete($kategori['id_kategori_kegiatan']);

        return redirect()->to('/kategori-kegiatan')->with('success', 'Kategori berhasil dihapus.');
    }
}
