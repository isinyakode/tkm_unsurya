<?php

namespace App\Models;

use CodeIgniter\Model;

class KreditPenilaianModel extends Model
{
    protected $table      = 'kredit_penilaian';
    protected $primaryKey = 'id_kredit_score';

    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'id_jenis_kegiatan',
        'id_elemen_kategori',
        'id_elemen_penilaian',
        'id_kategori_kegiatan',
        'kredit_score',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    public function getItemTersimpan($id_jenis_kegiatan)
    {
        return $this->db->table('kredit_penilaian kp')
            ->select('kp.id_elemen_penilaian as id_elemen, ep.nama_elemen_penilaian as nama_elemen, 
                  kp.id_kategori_kegiatan as id_kategori, kk.nama_kategori_kegiatan as nama_kategori, 
                  kp.kredit_score as kredit')
            ->join('elemen_penilaian ep', 'ep.id_elemen_penilaian = kp.id_elemen_penilaian')
            ->join('kategori_kegiatan kk', 'kk.id_kategori_kegiatan = kp.id_kategori_kegiatan')
            ->where('kp.id_jenis_kegiatan', $id_jenis_kegiatan)
            ->get()->getResultArray();
    }

    public function deleteByJenisKegiatan($id_jenis_kegiatan)
    {
        return $this->where('id_jenis_kegiatan', $id_jenis_kegiatan)->delete();
    }
}
