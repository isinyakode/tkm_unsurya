<?php

namespace App\Models;

use CodeIgniter\Model;

class KegiatanMahasiswaModel extends Model
{
    protected $table = 'kegiatan_mahasiswa';
    protected $primaryKey = 'id_kegiatan';

    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $deletedField   = 'deleted_at';

    protected $allowedFields = [
        'tipe_kegiatan',
        'nim_pengaju',
        'id_jenis_kegiatan',
        'id_elemen_penilaian',
        'jenis_kegiatan',
        'slug_kegiatan_mahasiswa',
        'nama_kegiatan',
        'tanggal_mulai',
        'tanggal_selesai',
        'deskripsi_kegiatan',
        'lokasi_kegiatan',
        'status_pengajuan',
        'file_laporan',
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getnim(string $nim_pengaju): ?array
    {
        return $this->where('nim_pengaju', $nim_pengaju)
            ->where('id_jenis_kegiatan =', 1)
            ->where('deleted_at', null)
            ->get()->getRowArray();
    }

    public function getBySlug(string $slug, ?string $nim = null): ?array
    {
        $builder = $this->where('slug_kegiatan_mahasiswa', $slug);

        if (!empty($nim)) {
            $builder->where('nim_pengaju', $nim);
        }

        return $builder->first();
    }

    public function getallkegiatan($nim_pengaju = null): ?array
    {
        return $this->db
            ->table('kegiatan_mahasiswa km')
            ->select('km.*, kma.*')
            ->join('kegiatan_mahasiswa_anggota kma', 'kma.id_kegiatan = km.id_kegiatan')
            ->where('kma.nim', $nim_pengaju)
            ->where('km.deleted_at', null)

            ->orderBy('kma.semester', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function getDetailPengajuan($slug = null, $nim = null): ?array
    {
        if ($nim && $slug) {
            return $this->db->table('kegiatan_mahasiswa')
                ->select('kegiatan_mahasiswa.*, 
                      kegiatan_mahasiswa_anggota.*')
                ->join('kegiatan_mahasiswa_anggota', 'kegiatan_mahasiswa_anggota.id_kegiatan = kegiatan_mahasiswa.id_kegiatan')
                ->where('kegiatan_mahasiswa.slug_kegiatan_mahasiswa', $slug)
                ->where('kegiatan_mahasiswa_anggota.nim', $nim)
                ->where('deleted_at', null)
                ->get()->getRowArray();
        }

        return $this->db->table('kegiatan_mahasiswa')
            ->select('kegiatan_mahasiswa.*, 
                      kegiatan_mahasiswa_anggota.*')
            ->join('kegiatan_mahasiswa_anggota', 'kegiatan_mahasiswa_anggota.id_kegiatan = kegiatan_mahasiswa.id_kegiatan')
            ->where('kegiatan_mahasiswa.slug_kegiatan_mahasiswa', $slug)
            ->where('kegiatan_mahasiswa_anggota.nim', $nim)
            ->where('deleted_at', null)
            ->get()->getResultArray();
    }

    public function getKriteriaPenilaian($id_jenis_kegiatan)
    {
        return $this->db->table('kredit_penilaian kp')
            ->select('ep.id_elemen_penilaian, ep.nama_elemen_penilaian, ep.request_key')
            ->join('elemen_penilaian ep', 'ep.id_elemen_penilaian = kp.id_elemen_penilaian')
            ->where('kp.id_jenis_kegiatan', $id_jenis_kegiatan)
            ->groupBy('kp.id_elemen_penilaian')
            ->orderBy('ep.nama_elemen_penilaian', 'ASC')
            ->get()->getResultArray();
    }

    public function getKegiatan($nim, $status = null)
    {
        if ($status) {
            return $this->db->table('kegiatan_mahasiswa')
                ->select('kegiatan_mahasiswa.*, 
                  kegiatan_mahasiswa_anggota.*')
                ->join('kegiatan_mahasiswa_anggota', 'kegiatan_mahasiswa_anggota.id_kegiatan = kegiatan_mahasiswa.id_kegiatan')
                ->where('kegiatan_mahasiswa.status_pengajuan', $status)
                ->where('kegiatan_mahasiswa_anggota.nim', $nim)
                ->where('kegiatan_mahasiswa.deleted_at', null)
                ->get()->getResultArray();
        }

        return $this->db->table('kegiatan_mahasiswa')
            ->select('kegiatan_mahasiswa.*, 
                  kegiatan_mahasiswa_anggota.*')
            ->join('kegiatan_mahasiswa_anggota', 'kegiatan_mahasiswa_anggota.id_kegiatan = kegiatan_mahasiswa.id_kegiatan')
            ->where('kegiatan_mahasiswa_anggota.nim', $nim)
            ->where('kegiatan_mahasiswa.deleted_at', null)
            ->get()->getResultArray();
    }

    public function getallKegiatanApprove($nim = null)
    {
        if ($nim) {
            return $this->db->table('kegiatan_mahasiswa')
                ->select('kegiatan_mahasiswa.*, 
                      kegiatan_mahasiswa_anggota.*')
                ->join('kegiatan_mahasiswa_anggota', 'kegiatan_mahasiswa_anggota.id_kegiatan = kegiatan_mahasiswa.id_kegiatan')
                ->where('kegiatan_mahasiswa.status_pengajuan', 'Disetujui')
                ->where('kegiatan_mahasiswa_anggota.nim', $nim)
                ->where('kegiatan_mahasiswa.deleted_at', null)
                ->get()->getResultArray();
        }

        return $this->db->table('kegiatan_mahasiswa')
            ->select('kegiatan_mahasiswa.*, 
                  kegiatan_mahasiswa_anggota.*')
            ->join('kegiatan_mahasiswa_anggota', 'kegiatan_mahasiswa_anggota.id_kegiatan = kegiatan_mahasiswa.id_kegiatan')
            ->where('kegiatan_mahasiswa.status_pengajuan', 'Disetujui')
            ->where('kegiatan_mahasiswa.deleted_at', null)
            ->groupBy('kegiatan_mahasiswa_anggota.nim')
            ->get()->getResultArray();
    }

    public function getAllGroupedByNim(): array
    {
        return $this->db->table('kegiatan_mahasiswa')
            ->select('kegiatan_mahasiswa.*, kegiatan_mahasiswa_anggota.*')
            ->join('kegiatan_mahasiswa_anggota', 'kegiatan_mahasiswa_anggota.id_kegiatan = kegiatan_mahasiswa.id_kegiatan')
            ->where('kegiatan_mahasiswa.deleted_at', null)
            ->groupBy('kegiatan_mahasiswa_anggota.nim')
            ->get()->getResultArray();
    }

    public function getkegiatanverifikasi($otoritas, $prodi = null)
    {
        if ($otoritas == "KABIRO KEMAHASISWAAN" || $otoritas == "WAREK III") {
            return $this->join('kegiatan_mahasiswa_anggota', 'kegiatan_mahasiswa_anggota.id_kegiatan = kegiatan_mahasiswa.id_kegiatan')->where('status_pengajuan', 'Diverifikasi')->findAll();
        }
        elseif ($otoritas == "KETUA PROGRAM STUDI") {
            return $this->join('kegiatan_mahasiswa_anggota', 'kegiatan_mahasiswa_anggota.id_kegiatan = kegiatan_mahasiswa.id_kegiatan')->where(['status_pengajuan'=> 'Ditinjau', 'peran'=> 'Ketua', 'prodi' => $prodi])->findAll();
        }
        return $this->join('kegiatan_mahasiswa_anggota', 'kegiatan_mahasiswa_anggota.id_kegiatan = kegiatan_mahasiswa.id_kegiatan')->where(['status_pengajuan'=> 'Diajukan', 'peran'=> 'Ketua'])->findAll();
    }

    // PIMPINAN DASHBOARD CHART DATA
    public function chartdata()
    {
        if ($cached = cache('chart_data_all')) return $cached;
        $data = $this->table('kegiatan_mahasiswa')
            ->select('id_jenis_kegiatan')
            ->select("SUM(CASE WHEN MONTH(created_at) IN (9,10,11,12,1,2) THEN 1 ELSE 0 END) as ganjil")
            ->select("SUM(CASE WHEN MONTH(created_at) NOT IN (9,10,11,12,1,2) THEN 1 ELSE 0 END) as genap")
            ->where('deleted_at', null)
            ->groupBy('id_jenis_kegiatan')
            ->get()->getResultArray();
        cache()->save('chart_data_all', $data, 3600);
        return $data;
    }

    public function chartmouthlydata()
    {
        if ($cached = cache('chart_monthly_data')) return $cached;
        $data = $this->table('kegiatan_mahasiswa')
            ->select("MONTH(created_at) as bulan, status_pengajuan, COUNT(*) as total")
            ->where('deleted_at', null)
            ->groupBy("MONTH(created_at), status_pengajuan")
            ->orderBy("MONTH(created_at)", "ASC")
            ->get()
            ->getResultArray();
        cache()->save('chart_monthly_data', $data, 3600);
        return $data;
    }

    public function chartfakultas()
    {
        if ($cached = cache('chart_fakultas_data')) return $cached;
        $data = $this->table('kegiatan_mahasiswa')
            ->select('kma.fakultas')
            ->select("COUNT(CASE WHEN MONTH(kegiatan_mahasiswa.created_at) IN (9,10,11,12,1,2) THEN kegiatan_mahasiswa.id_kegiatan END) as total_ganjil")
            ->select("COUNT(CASE WHEN MONTH(kegiatan_mahasiswa.created_at) IN (3,4,5,6,7,8) THEN kegiatan_mahasiswa.id_kegiatan END) as total_genap")
            ->join('kegiatan_mahasiswa_anggota kma', 'kma.id_kegiatan = kegiatan_mahasiswa.id_kegiatan')
            ->where('kegiatan_mahasiswa.status_pengajuan', 'Disetujui')
            ->where('kegiatan_mahasiswa.deleted_at', null)
            ->groupBy('kma.fakultas')
            ->get()->getResultArray();
        cache()->save('chart_fakultas_data', $data, 3600);
        return $data;
    }

    public function chartprodi()
    {
        if ($cached = cache('chart_prodi_data')) return $cached;
        $data = $this->table('kegiatan_mahasiswa')
            ->select('kma.prodi')
            ->select("COUNT(CASE WHEN MONTH(kegiatan_mahasiswa.created_at) IN (9,10,11,12,1,2) THEN kegiatan_mahasiswa.id_kegiatan END) as total_ganjil")
            ->select("COUNT(CASE WHEN MONTH(kegiatan_mahasiswa.created_at) IN (3,4,5,6,7,8) THEN kegiatan_mahasiswa.id_kegiatan END) as total_genap")
            ->join('kegiatan_mahasiswa_anggota kma', 'kma.id_kegiatan = kegiatan_mahasiswa.id_kegiatan')
            ->where('kegiatan_mahasiswa.status_pengajuan', 'Disetujui')
            ->where('kegiatan_mahasiswa.deleted_at', null)
            ->groupBy('kma.prodi')
            ->get()->getResultArray();
        cache()->save('chart_prodi_data', $data, 3600);
        return $data;
    }

    public function getPointMahasiswa($nim = null)
    {
        if ($nim) {
            $cacheKey = 'leaderboard_nim_'.$nim;
            if ($cached = cache($cacheKey)) return $cached;
            $data = $this->db->table('kegiatan_mahasiswa_anggota kma')
                ->select('kma.*, km.*')
                ->select('SUM(kma.total_kredit) as total_point')
                ->join('kegiatan_mahasiswa km', 'km.id_kegiatan = kma.id_kegiatan')
                ->where('km.status_pengajuan', 'Disetujui')
                ->where('kma.nim', $nim)
                ->where('km.deleted_at', null)
                ->groupBy('kma.nim')
                ->orderBy('total_point', 'DESC')
                ->limit(10)
                ->get()->getResultArray();
            cache()->save($cacheKey, $data, 3600);
            return $data;
        }
        if ($cached = cache('leaderboard_all')) return $cached;
        $data = $this->db->table('kegiatan_mahasiswa_anggota kma')
            ->select('kma.nim, kma.nama, kma.prodi, kma.fakultas')
            ->select('SUM(kma.total_kredit) as total_point')
            ->join('kegiatan_mahasiswa km', 'km.id_kegiatan = kma.id_kegiatan')
            ->where('km.status_pengajuan', 'Disetujui')
            ->where('km.deleted_at', null)
            ->groupBy('kma.nim')
            ->orderBy('total_point', 'DESC')
            ->limit(10)
            ->get()->getResultArray();
        cache()->save('leaderboard_all', $data, 3600);
        return $data;
    }
}
