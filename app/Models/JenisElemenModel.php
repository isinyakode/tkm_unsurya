<?php

namespace App\Models;

use CodeIgniter\Model;

class JenisElemenModel extends Model
{
    protected $table            = 'jenis_elemen';
    protected $primaryKey       = 'id_jenis_elemen';
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;

    protected $allowedFields    = [
        'jenis_elemen',
        'id_jenis_kegiatan',
        'id_elemen_penilaian',
    ];

    protected $useTimestamps    = true;
}
