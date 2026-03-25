<?php

namespace App\Models;

use CodeIgniter\Model;

class KegiatanAnggotaModel extends Model
{
    protected $table      = 'kegiatan_mahasiswa_anggota';
    protected $primaryKey = 'id_anggota';

    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'id_kegiatan',
        'peran',
        'nim',
        'nama',
        'tempat_lahir',
        'tanggal_lahir',
        'fakultas',
        'prodi',
        'jenjang',
        'semester',
        'kredit_score',
        'total_kredit',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    public function getAnggotaLain($id_kegiatan, $exclude_peran = 'Ketua'): array
    {
        $builder = $this->where('id_kegiatan', $id_kegiatan);
        if ($exclude_peran !== null) {
            $builder->where('peran !=', $exclude_peran);
        }
        return $builder->get()->getResultArray();
    }

    public function getByNim($nim): ?array
    {
        return $this->where('nim', $nim)->get()->getRowArray();
    }
}
