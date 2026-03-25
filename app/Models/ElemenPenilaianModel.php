<?php

namespace App\Models;

use CodeIgniter\Model;

class ElemenPenilaianModel extends Model
{
    protected $table      = 'elemen_penilaian';
    protected $primaryKey = 'id_elemen_penilaian';
    protected $allowedFields = ['nama_elemen_penilaian', 'request_key'];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $useSoftDeletes = true;
    protected $deletedField   = 'deleted_at';

    public function getElemenPenilaian($id_elemen_penilaian = null, $nama_elemen = null)
    {
        if ($id_elemen_penilaian) {
            return $this->db->table('elemen_penilaian ep')
                ->select('ep.*, kk.*, ek.*')
                ->join('kategori_kegiatan kk', 'kk.id_kategori_kegiatan = ek.id_kategori_kegiatan')
                ->join('elemen_kategori ek', 'ek.id_elemen_penilaian = ep.id_elemen_penilaian')
                ->where(['ep.id_elemen_penilaian' => $id_elemen_penilaian])
                ->where(['ep.deleted_at' => NULL])
                ->get()
                ->getResultArray();
        }

        if ($nama_elemen) {
            return $this->db->table('elemen_penilaian ep')
                ->select('ep.*')
                ->where(['ep.nama_elemen_penilaian' => $nama_elemen])
                ->where(['ep.deleted_at' => NULL])
                ->get()
                ->getRowArray();
        }

        return $this->findall();
    }
}
