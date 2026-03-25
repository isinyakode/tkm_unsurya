<?php

namespace App\Models;

use CodeIgniter\Model;

class KategoriKegiatanModel extends Model
{
    protected $table = 'kategori_kegiatan';
    protected $primaryKey = 'id_kategori_kegiatan';
    protected $allowedFields = ['nama_kategori_kegiatan', 'slug_kategori_kegiatan', 'user'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';
    protected $useSoftDeletes = true;

    public function getBySlug(string $slug): ?array
    {
        return $this->where('slug_kategori_kegiatan', $slug)->first();
    }

    public function getElemenPenilaian($nama_elemen = null)
    {
        if ($nama_elemen) {
            return $this->db->table('elemen_penilaian ep')
                ->select('kk.id_kategori_kegiatan, kk.nama_kategori_kegiatan, kk.slug_kategori_kegiatan, ep.nama_elemen_penilaian')
                ->join('elemen_kategori ek', 'ek.id_elemen_penilaian = ep.id_elemen_penilaian')
                ->join('kategori_kegiatan kk', 'kk.id_kategori_kegiatan = ek.id_kategori_kegiatan')
                ->where('ep.nama_elemen_penilaian', $nama_elemen)
                ->where('ep.deleted_at', NULL)
                ->get()
                ->getResultArray();
        }

        return $this->findall();
    }
}
