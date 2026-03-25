<?php

namespace App\Models;

use CodeIgniter\Model;

class KegiatanPenilaianModel extends Model
{
    protected $table      = 'kegiatan_mahasiswa_penilaian';
    protected $primaryKey = 'id_kegiatan_mahasiswa_penilaian';

    protected $returnType     = 'array';

    protected $allowedFields = [
        'id_kegiatan',
        'elemen_penilaian',
        'kredit_score',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
