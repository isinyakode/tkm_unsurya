<?php

namespace App\Models;

use CodeIgniter\Model;

class JenisKegiatanMahasiswaModel extends Model
{
    protected $table            = 'jenis_kegiatan';
    protected $primaryKey       = 'id_jenis_kegiatan';
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields    = [
        'jenis_kegiatan',
        'slug_jenis_kegiatan',
        'peran_mode',
        'peran_default',
        'label_jenis',
        'show_tanggal',
        'show_anggota',
        'color_icon',
        'deskripsi_jenis_kegiatan',
    ];

    protected $useTimestamps    = false;
    protected $deletedField   = 'deleted_at';

    public function getslug($slug)
    {
        return $this->where(['slug_jenis_kegiatan' => $slug])->first();
    }

    public function getNonPkkmb(): array
    {
        return $this->where(['id_jenis_kegiatan !=' => 1])->findAll();
    }

    public function getTingkatKegiatan($id_jenis_kegiatan)
    {
        return $this->db->table('kredit_penilaian kp')
            ->select('kk.*, kp.*')
            ->join('kategori_kegiatan kk', 'kk.id_kategori_kegiatan = kp.id_kategori_kegiatan')
            ->join('elemen_penilaian ep', 'ep.id_elemen_penilaian = kp.id_elemen_penilaian')
            ->getwhere(['kp.id_jenis_kegiatan' => $id_jenis_kegiatan, 'ep.nama_elemen_penilaian' => 'Tingkat'])
            ->getResultArray();
    }

    public function getPeranKegiatan($id_jenis_kegiatan)
    {
        return $this->db->table('kredit_penilaian kp')
            ->select('kk.id_kategori_kegiatan, kk.nama_kategori_kegiatan, kk.nama_kategori_kegiatan, kp.kredit_score')
            ->join('kategori_kegiatan kk', 'kk.id_kategori_kegiatan = kp.id_kategori_kegiatan')
            ->join('elemen_penilaian ep', 'ep.id_elemen_penilaian = kp.id_elemen_penilaian')
            ->getwhere(['kp.id_jenis_kegiatan' => $id_jenis_kegiatan, 'ep.nama_elemen_penilaian' => 'Peran', 'kk.nama_kategori_kegiatan !=' => 'Ketua'])
            ->getResultArray();
    }

    public function getWaktuKegiatan($id_jenis_kegiatan)
    {
        return $this->db->table('kredit_penilaian kp')
            ->select('kk.id_kategori_kegiatan, kk.nama_kategori_kegiatan, kp.kredit_score')
            ->join('kategori_kegiatan kk', 'kk.id_kategori_kegiatan = kp.id_kategori_kegiatan')
            ->join('elemen_penilaian ep', 'ep.id_elemen_penilaian = kp.id_elemen_penilaian')
            ->getwhere(['kp.id_jenis_kegiatan' => $id_jenis_kegiatan, 'ep.nama_elemen_penilaian' => 'Waktu'])
            ->getResultArray();
    }

    public function getPrestasiKegiatan($id_jenis_kegiatan)
    {
        return $this->db->table('kredit_penilaian kp')
            ->select('kk.id_kategori_kegiatan, kk.nama_kategori_kegiatan, kp.kredit_score')
            ->join('kategori_kegiatan kk', 'kk.id_kategori_kegiatan = kp.id_kategori_kegiatan')
            ->join('elemen_penilaian ep', 'ep.id_elemen_penilaian = kp.id_elemen_penilaian')
            ->getwhere(['kp.id_jenis_kegiatan' => $id_jenis_kegiatan, 'ep.nama_elemen_penilaian' => 'Prestasi'])
            ->getResultArray();
    }

    public function getBadanHukumKegiatan($id_jenis_kegiatan)
    {
        return $this->db->table('kredit_penilaian kp')
            ->select('kk.id_kategori_kegiatan, kk.nama_kategori_kegiatan, kp.kredit_score')
            ->join('kategori_kegiatan kk', 'kk.id_kategori_kegiatan = kp.id_kategori_kegiatan')
            ->join('elemen_penilaian ep', 'ep.id_elemen_penilaian = kp.id_elemen_penilaian')
            ->getwhere(['kp.id_jenis_kegiatan' => $id_jenis_kegiatan, 'ep.nama_elemen_penilaian' => 'Badan Hukum'])
            ->getResultArray();
    }

    public function getOmzetKegiatan($id_jenis_kegiatan)
    {
        return $this->db->table('kredit_penilaian kp')
            ->select('kk.id_kategori_kegiatan, kk.nama_kategori_kegiatan, kp.kredit_score')
            ->join('kategori_kegiatan kk', 'kk.id_kategori_kegiatan = kp.id_kategori_kegiatan')
            ->join('elemen_penilaian ep', 'ep.id_elemen_penilaian = kp.id_elemen_penilaian')
            ->getwhere(['kp.id_jenis_kegiatan' => $id_jenis_kegiatan, 'ep.nama_elemen_penilaian' => 'Omzet Pertahun'])
            ->getResultArray();
    }

    public function getJenisKegiatan($id_jenis_kegiatan)
    {
        return $this->db->table('kredit_penilaian kp')
            ->select('kk.id_kategori_kegiatan, kk.nama_kategori_kegiatan, kp.kredit_score')
            ->join('kategori_kegiatan kk', 'kk.id_kategori_kegiatan = kp.id_kategori_kegiatan')
            ->join('elemen_penilaian ep', 'ep.id_elemen_penilaian = kp.id_elemen_penilaian')
            ->getwhere(['kp.id_jenis_kegiatan' => $id_jenis_kegiatan, 'ep.nama_elemen_penilaian' => 'Jenis'])
            ->getResultArray();
    }

    public function getMedsosKegiatan($id_jenis_kegiatan)
    {
        return $this->db->table('kredit_penilaian kp')
            ->select('kk.id_kategori_kegiatan, kk.nama_kategori_kegiatan, kp.kredit_score')
            ->join('kategori_kegiatan kk', 'kk.id_kategori_kegiatan = kp.id_kategori_kegiatan')
            ->join('elemen_penilaian ep', 'ep.id_elemen_penilaian = kp.id_elemen_penilaian')
            ->getwhere(['kp.id_jenis_kegiatan' => $id_jenis_kegiatan, 'ep.nama_elemen_penilaian' => 'Viewers'])
            ->getResultArray();
    }
}
