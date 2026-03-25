<?php

namespace App\Models;

use CodeIgniter\Model;

class LogsModel extends Model
{
    protected $table      = 'log_verifikasi_kegiatan';
    protected $primaryKey = 'id_log';

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'id_kegiatan',
        'status_lama',
        'status_baru',
        'catatan_verifikator',
        'verifikator',
    ];

    public function getLogs($id_kegiatan)
    {
        return $this->where(['id_kegiatan' => $id_kegiatan])->orderBy('created_at', 'DESC')->get()->getResultArray();
    }
}
