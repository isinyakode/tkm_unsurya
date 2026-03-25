<?php

namespace App\Models;

use CodeIgniter\Model;

class ElemenKategoriModel extends Model
{
    protected $table      = 'elemen_kategori';
    protected $primaryKey = 'id_elemen_kategori';
    protected $allowedFields = ['id_elemen_penilaian', 'id_kategori_kegiatan'];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getByElemenPenilaian($id_elemen)
    {
        return $this->where('id_elemen_penilaian', $id_elemen)->findAll();
    }

    public function get_elemen_kategori($id_elemen)
    {
        $builder = $this->db->table('elemen_kategori ek');
        $builder->select('kk.id_kategori_kegiatan, kk.nama_kategori_kegiatan');
        $builder->join('kategori_kegiatan kk', 'ek.id_kategori_kegiatan = kk.id_kategori_kegiatan');
        $builder->where('ek.id_elemen_penilaian', $id_elemen);
        $query = $builder->get();
        return $query->getResultArray();
    }

    public function getRelasiKategori()
    {
        return $this->db->table('elemen_kategori ek')
            ->select('ek.id_elemen_penilaian, ek.id_kategori_kegiatan, k.nama_kategori_kegiatan')
            ->join('kategori_kegiatan k', 'k.id_kategori_kegiatan = ek.id_kategori_kegiatan')
            ->get()->getResultArray();
    }
}
