<?php

namespace App\Services;

use App\Models\KegiatanMahasiswaModel;
use App\Models\KegiatanPenilaianModel;
use App\Models\KegiatanAnggotaModel;
use App\Models\LogsModel;
use Config\Database;

class ActivityService
{
    protected $db;
    protected $kegiatanModel;
    protected $penilaianModel;
    protected $anggotaModel;
    protected $LogsModel;

    public function __construct()
    {
        $this->db = Database::connect();
        $this->kegiatanModel  = new KegiatanMahasiswaModel();
        $this->penilaianModel = new KegiatanPenilaianModel();
        $this->anggotaModel   = new KegiatanAnggotaModel();
        $this->LogsModel   = new LogsModel();
    }

    // ================ BARU =========================
    public function submitActivity($idJenis, array $formData, $file)
    {
        $data_mhs = [
            'nim'           => session()->get('nim'),
            'tempat_lahir'  => session()->get('namakotalahir'),
            'tanggal_lahir' => session()->get('tgllahir'),
            'nama'          => session()->get('nama'),
            'fakultas'      => session()->get('fakultas'),
            'prodi'         => session()->get('prodi'),
            'jenjang'       => session()->get('jenjang'),
            'semester'      => session()->get('semester'),
        ];
        // dd($data_mhs);

        $namaKegiatan = ($idJenis == 1) ? "kegiatan-pkkmb" : $formData['nama_kegiatan'];
        $slug = url_title($namaKegiatan, '-', true);
        $fileName = $this->uploadLaporan($file, $data_mhs['nim'], $slug);
        // dd($data_mhs);
        $this->db->transStart();

        $jenisRow = $this->db->table('jenis_kegiatan')->where('id_jenis_kegiatan', $idJenis)->get()->getRow();

        // Simpan Data Utama (Tanpa id_elemen_penilaian sesuai instruksi Anda)
        $idKegiatan = $this->kegiatanModel->insert((object)[
            'id_jenis_kegiatan'       => $idJenis,
            'jenis_kegiatan'          => $jenisRow->jenis_kegiatan,
            'nim_pengaju'             => $data_mhs['nim'],
            'nama_kegiatan'           => $namaKegiatan,
            'slug_kegiatan_mahasiswa' => $slug,
            'deskripsi_kegiatan'      => $formData['deskripsi_kegiatan'] ?? ($idJenis == 1 ? "Laporan PKKMB" : "-"),
            'lokasi_kegiatan'         => $formData['lokasi_kegiatan'] ?? "Universitas Dirgantara Marsekal Suryadarma",
            'tanggal_mulai'           => $formData['tanggal_mulai'] ?? date('Y-m-d'),
            'tanggal_selesai'         => $formData['tanggal_selesai'] ?? date('Y-m-d'),
            'file_laporan'            => $fileName,
            'status_pengajuan'        => 'Diajukan',
            'created_at'              => date('Y-m-d H:i:s')
        ]);
        // dd($idKegiatan);
        // Hitung rincian skor (Tingkat & Peran)
        $scoreSummary = $this->calculateAndSaveScores($idKegiatan, $idJenis, $formData);

        // Simpan Anggota dan Rincian Penilaian ke tabel masing-masing
        $this->saveMembersBatch($idKegiatan, $idJenis, $formData, $scoreSummary, $data_mhs);

        $this->SaveLogs($idKegiatan, null, "Diajukan", session()->get('nama'));
        $this->db->transCommit();
        return true;
    }

    private function uploadLaporan($file, $nim, $slug)
    {
        $newName = $slug . '_' . time() . '.' . $file->getExtension();
        $path    = 'uploads/tkm/' . $nim . '/' . $slug . '/';

        if (!is_dir($path)) mkdir($path, 0777, true);
        $file->move($path, $newName);

        return $newName;
    }

    // private function calculateAndSaveScores($idKegiatan, $idJenis, $formData)
    // {
    //     $res = [
    //         'shared_score' => 0,
    //         'peran_ketua'  => ['nama_peran' => 'Ketua', 'kredit_score' => 0]
    //     ];

    //     $semuaElemen = $this->db->table('elemen_penilaian')->get()->getResultArray();
    //     $dataPenilaianBatch = []; // Array penampung untuk insertBatch

    //     foreach ($semuaElemen as $elemen) {
    //         $key = $elemen['request_key'];
    //         $idKategori = $formData[$key] ?? null;

    //         if ($idKategori) {
    //             $dataPenilaian = $this->db->table('kredit_penilaian kp')
    //                 ->select('kp.kredit_score, ep.nama_elemen_penilaian')
    //                 ->join('elemen_penilaian ep', 'ep.id_elemen_penilaian = kp.id_elemen_penilaian')
    //                 ->where([
    //                     'kp.id_jenis_kegiatan'    => $idJenis,
    //                     'kp.id_kategori_kegiatan' => $idKategori,
    //                     'kp.id_elemen_penilaian'  => $elemen['id_elemen_penilaian']
    //                 ])
    //                 ->get()->getResultArray();
    //             // dd($dataPenilaian);
    //             foreach ($dataPenilaian as $row) {
    //                 // if ($row['nama_elemen_penilaian'] !== 'Peran') {
    //                 $res['shared_score'] += (float)$row['kredit_score'];

    //                 // Siapkan data untuk batch
    //                 $dataPenilaianBatch[] = [
    //                     'id_kegiatan'      => $idKegiatan,
    //                     'elemen_penilaian' => $row['nama_elemen_penilaian'],
    //                     'kredit_score'     => (float)$row['kredit_score'],
    //                     'created_at'       => date('Y-m-d H:i:s'),
    //                     'updated_at'       => date('Y-m-d H:i:s')
    //                 ];
    //                 // }
    //             }
    //         }
    //     }

    //     // Eksekusi insertBatch jika ada data yang terkumpul
    //     if (!empty($dataPenilaianBatch)) {
    //         $this->db->table('kegiatan_mahasiswa_penilaian')->insertBatch($dataPenilaianBatch);
    //     }

    //     // 2. Ambil Skor Peran (Ketua/Peserta)
    //     $peranKategori = ($idJenis == 1) ? 'Peserta' : 'Ketua';
    //     $dataPeran = $this->db->table('kredit_penilaian kp')
    //         ->join('kategori_kegiatan kk', 'kp.id_kategori_kegiatan = kk.id_kategori_kegiatan')
    //         ->join('elemen_penilaian ep', 'ep.id_elemen_penilaian = kp.id_elemen_penilaian')
    //         ->where([
    //             'kp.id_jenis_kegiatan'      => $idJenis,
    //             'ep.nama_elemen_penilaian'  => 'Peran',
    //             'kk.nama_kategori_kegiatan' => $peranKategori
    //         ])->get()->getRow();

    //     if ($dataPeran) {
    //         $res['peran_ketua']['nama_peran']   = $dataPeran->nama_kategori_kegiatan;
    //         $res['peran_ketua']['kredit_score'] = (float)$dataPeran->kredit_score;
    //     }

    //     return $res;
    // }

    // BARU
    private function calculateAndSaveScores($idKegiatan, $idJenis, $formData)
    {
        $res = [
            'shared_score' => 0,
            'peran_ketua'  => ['nama_peran' => 'Ketua', 'kredit_score' => 0]
        ];

        // 1. Ambil Master Data Elemen
        $semuaElemen = $this->db->table('elemen_penilaian')->get()->getResultArray();

        // 2. Eager Loading: Ambil SEMUA aturan skor sekaligus untuk jenis kegiatan ini
        // Kita join ke elemen_penilaian untuk mendapatkan 'nama_elemen' yang berlaku SAAT INI
        $aturanKredit = $this->db->table('kredit_penilaian kp')
            ->select('kp.id_elemen_penilaian, kp.id_kategori_kegiatan, kp.kredit_score, ep.nama_elemen_penilaian')
            ->join('elemen_penilaian ep', 'ep.id_elemen_penilaian = kp.id_elemen_penilaian')
            ->where('kp.id_jenis_kegiatan', $idJenis)
            ->get()->getResultArray();

        // Mapping agar pencarian cepat tanpa query berulang
        // Struktur: $map[id_elemen][id_kategori] = ['score' => 10, 'nama' => 'Tingkat']
        $mapKredit = [];
        foreach ($aturanKredit as $ak) {
            $mapKredit[$ak['id_elemen_penilaian']][$ak['id_kategori_kegiatan']] = [
                'score' => (float)$ak['kredit_score'],
                'nama'  => $ak['nama_elemen_penilaian'] // Kita ambil namanya dari sini
            ];
        }

        $dataPenilaianBatch = [];
        $timestamp = date('Y-m-d H:i:s');

        // 3. Loop Logic (Di memory PHP, bukan DB)
        foreach ($semuaElemen as $elemen) {
            $key = $elemen['request_key'];

            // Skip 'Peran' karena dihitung terpisah, dan pastikan user mengisi input
            if ($elemen['nama_elemen_penilaian'] !== 'Peran' && !empty($formData[$key])) {

                $idElemen   = $elemen['id_elemen_penilaian'];
                $idKategori = $formData[$key];

                // Cek apakah kombinasi elemen & kategori ini punya skor
                if (isset($mapKredit[$idElemen][$idKategori])) {
                    $dataAturan = $mapKredit[$idElemen][$idKategori];

                    $res['shared_score'] += $dataAturan['score'];

                    $dataPenilaianBatch[] = [
                        'id_kegiatan'      => $idKegiatan,
                        // SNAPSHOT: Kita simpan Teks-nya, bukan ID-nya.
                        // Jika besok master berubah, data ini AMAN tidak berubah.
                        'elemen_penilaian' => $dataAturan['nama'],
                        'kredit_score'     => $dataAturan['score'],
                        'created_at'       => $timestamp,
                        'updated_at'       => $timestamp
                    ];
                }
            }
        }

        // 4. Insert Batch (Hanya 1 Query Insert)
        if (!empty($dataPenilaianBatch)) {
            // Pastikan nama kolom di DB adalah 'elemen_penilaian' (VARCHAR)
            $this->db->table('kegiatan_mahasiswa_penilaian')->insertBatch($dataPenilaianBatch);
        }

        // 5. Logika Peran (Snapshot juga)
        $peranKategori = ($idJenis == 1 || $idJenis == 14) ? 'Peserta' : 'Ketua';

        // Query spesifik untuk Peran
        $dataPeran = $this->db->table('kredit_penilaian kp')
            ->join('kategori_kegiatan kk', 'kp.id_kategori_kegiatan = kk.id_kategori_kegiatan')
            ->join('elemen_penilaian ep', 'ep.id_elemen_penilaian = kp.id_elemen_penilaian')
            ->where([
                'kp.id_jenis_kegiatan'      => $idJenis,
                'ep.nama_elemen_penilaian'  => 'Peran',
                'kk.nama_kategori_kegiatan' => $peranKategori
            ])->get()->getRow();

        if ($dataPeran) {
            $res['peran_ketua']['nama_peran']   = $dataPeran->nama_kategori_kegiatan;
            $res['peran_ketua']['kredit_score'] = (float)$dataPeran->kredit_score;
        }

        return $res;
    }

    private function saveMembersBatch($idK, $idJ, $formData, $scores, $data_mhs)
    {
        // dd($data_mhs);
        // --- 1. SIMPAN KETUA ---
        $this->anggotaModel->insert((object)[
            'id_kegiatan'   => $idK,
            'nim'           => $data_mhs['nim'],
            'nama'          => $data_mhs['nama'],
            'peran'         => $scores['peran_ketua']['nama_peran'],
            'kredit_score'  => $scores['peran_ketua']['kredit_score'],
            'total_kredit'  => $scores['peran_ketua']['kredit_score'] + $scores['shared_score'],
            'tempat_lahir'  => $data_mhs['tempat_lahir'],
            'tanggal_lahir' => date('Y-m-d', strtotime($data_mhs['tanggal_lahir'])),
            'fakultas'      => $data_mhs['fakultas'],
            'prodi'         => $data_mhs['prodi'],
            'jenjang'       => $data_mhs['jenjang'],
            'semester'      => $data_mhs['semester'],
            'created_at'    => date('Y-m-d H:i:s')
        ]);

        // --- 2. SIMPAN ANGGOTA ---
        if ($idJ != 1 && !empty($formData['anggotaData'])) {
            $anggotaArr = json_decode($formData['anggotaData'], true);
            foreach ($anggotaArr as $agt) {
                $dataMaster = $this->db->table('kredit_penilaian kp')
                    ->join('elemen_penilaian ep', 'ep.id_elemen_penilaian = kp.id_elemen_penilaian')
                    ->join('kategori_kegiatan kk', 'kk.id_kategori_kegiatan = kp.id_kategori_kegiatan')
                    ->where(['kp.id_jenis_kegiatan' => $idJ, 'kk.nama_kategori_kegiatan' => $agt['peran']])
                    ->get()->getRow();
                // dd($dataMaster);

                $skorPeran = $dataMaster ? (float)$dataMaster->kredit_score : 0;

                $this->anggotaModel->insert((object)[
                    'id_kegiatan'  => $idK,
                    'nim'          => $agt['nim'],
                    'nama'         => $agt['nama'],
                    'peran'        => $agt['peran'],
                    'kredit_score' => $skorPeran,
                    'total_kredit' => $skorPeran + $scores['shared_score'],
                    'tempat_lahir' => $agt['tmpt_lhr'] ?? '-',
                    'tanggal_lahir' => date('Y-m-d', strtotime($agt['tgl_lhr'] ?? date('Y-m-d'))),
                    'fakultas'      => $agt['fakultas'],
                    'prodi'         => $agt['prodi'],
                    'jenjang'       => $agt['jenjang'],
                    'semester'      => $agt['semester'],
                    'created_at'   => date('Y-m-d H:i:s')
                ]);
            }
        }
    }

    // private function insertPenilaianRecord($idK, $idA, $data)
    // {
    //     if (!$data) return;
    //     $this->penilaianModel->insert([
    //         'id_kegiatan'          => $idK,
    //         'id_anggota'           => $idA,
    //         'id_elemen_penilaian'  => $data['id_elemen_penilaian'],
    //         'id_kategori_kegiatan' => $data['id_kategori_kegiatan'],
    //         'kredit_score'         => $data['kredit_score']
    //     ]);
    // }
    // ================ BARU =========================

    // UPDATE
    // public function updateActivity($id_kegiatan, $data, $fileLaporan = null)
    // {
    //     $kegiatanLama = $this->kegiatanModel->where('id_kegiatan', $id_kegiatan)->first();
    //     if (!$kegiatanLama) return ['status' => false, 'message' => 'Data tidak ditemukan.'];

    //     $this->db->transStart();
    //     try {
    //         // 1. Handle File
    //         $namaFileSimpan = $kegiatanLama['file_laporan'];
    //         if ($fileLaporan && $fileLaporan->isValid() && !$fileLaporan->hasMoved()) {
    //             $namaFileSimpan = $fileLaporan->getRandomName();
    //             $dirPath = 'uploads/tkm/' . $kegiatanLama['nim_pengaju'] . '/' . $kegiatanLama['slug_kegiatan_mahasiswa'];

    //             if (!is_dir($dirPath)) mkdir($dirPath, 0777, true);
    //             $fileLaporan->move($dirPath, $namaFileSimpan);

    //             if (!empty($kegiatanLama['file_laporan']) && file_exists($dirPath . '/' . $kegiatanLama['file_laporan'])) {
    //                 @unlink($dirPath . '/' . $kegiatanLama['file_laporan']);
    //             }
    //         }

    //         // 2. Update Tabel Utama
    //         $this->kegiatanModel->update($id_kegiatan, [
    //             'nama_kegiatan'      => $data['nama_kegiatan'],
    //             'tanggal_mulai'      => $data['tanggal_mulai'],
    //             'tanggal_selesai'    => $data['tanggal_selesai'],
    //             'deskripsi_kegiatan' => $data['deskripsi_kegiatan'],
    //             'lokasi_kegiatan'    => $data['lokasi_kegiatan'],
    //             'file_laporan'       => $namaFileSimpan,
    //             'status_pengajuan'   => 'Diajukan',
    //             'updated_at'         => date('Y-m-d H:i:s')
    //         ]);

    //         // 3. NORMALISASI PENILAIAN (Tingkat)
    //         $this->db->table('kegiatan_mahasiswa_penilaian')->where('id_kegiatan', $id_kegiatan)->delete();
    //         $scoreSummary = $this->calculateAndSaveScores($id_kegiatan, $kegiatanLama['id_jenis_kegiatan'], $data);

    //         // 4. SINKRONISASI ANGGOTA
    //         // Hapus anggota lama (Direct Delete karena deleted_at sudah dihapus)
    //         $this->db->table('kegiatan_mahasiswa_anggota')->where('id_kegiatan', $id_kegiatan)->delete();

    //         $data_mhs = [
    //             'nim'           => session()->get('nim'),
    //             'tempat_lahir'  => session()->get('namakotalahir'),
    //             'tanggal_lahir' => session()->get('tgllahir'),
    //             'nama'          => session()->get('nama'),
    //             'fakultas'      => session()->get('fakultas'),
    //             'prodi'         => session()->get('prodi'),
    //             'jenjang'       => session()->get('jenjang'),
    //             'semester'      => session()->get('semester'),
    //         ];

    //         $this->saveMembersBatch($id_kegiatan, $kegiatanLama['id_jenis_kegiatan'], $data, $scoreSummary, $data_mhs);

    //         // 5. Simpan Log
    //         $this->SaveLogs($id_kegiatan, "Revisi", 'Diajukan', session()->get('nama'));

    //         $this->db->transComplete();
    //         return ['status' => true];
    //     } catch (\Exception $e) {
    //         $this->db->transRollback();
    //         return ['status' => false, 'message' => $e->getMessage()];
    //     }
    // }
    
    // BARU
    public function updateActivity($id_kegiatan, $data, $fileLaporan = null)
    {
        // 1. Ambil data lama untuk referensi NIM, Slug, dan Jenis Kegiatan
        $kegiatanLama = $this->kegiatanModel->where('id_kegiatan', $id_kegiatan)->first();
        if (!$kegiatanLama) return ['status' => false, 'message' => 'Data tidak ditemukan.'];
    
        $this->db->transStart();
        try {
            // 2. Handle File (Menggunakan helper uploadLaporan yang Anda punya)
            $namaFileSimpan = $kegiatanLama['file_laporan'];
            
            if ($fileLaporan && $fileLaporan->isValid() && !$fileLaporan->hasMoved()) {
                // Gunakan fungsi uploadLaporan private Anda agar logic penamaan file konsisten
                $namaFileSimpan = $this->uploadLaporan(
                    $fileLaporan, 
                    $kegiatanLama['nim_pengaju'], 
                    $kegiatanLama['slug_kegiatan_mahasiswa']
                );
    
                // Hapus file lama jika ada
                $dirPath = 'uploads/tkm/' . $kegiatanLama['nim_pengaju'] . '/' . $kegiatanLama['slug_kegiatan_mahasiswa'];
                if (!empty($kegiatanLama['file_laporan']) && file_exists($dirPath . '/' . $kegiatanLama['file_laporan'])) {
                    @unlink($dirPath . '/' . $kegiatanLama['file_laporan']);
                }
            }
    
            // 3. Update Tabel Utama
            $this->kegiatanModel->update($id_kegiatan, [
                'nama_kegiatan'      => $data['nama_kegiatan'],
                'tanggal_mulai'      => $data['tanggal_mulai'],
                'tanggal_selesai'    => $data['tanggal_selesai'],
                'deskripsi_kegiatan' => $data['deskripsi_kegiatan'],
                'lokasi_kegiatan'    => $data['lokasi_kegiatan'],
                'file_laporan'       => $namaFileSimpan,
                'status_pengajuan'   => 'Diajukan', // Status kembali ke awal jika direvisi
                'updated_at'         => date('Y-m-d H:i:s')
            ]);
    
            // 4. NORMALISASI PENILAIAN
            // Hapus detail penilaian lama, lalu hitung ulang berdasarkan input baru ($data)
            $this->db->table('kegiatan_mahasiswa_penilaian')->where('id_kegiatan', $id_kegiatan)->delete();
            $scoreSummary = $this->calculateAndSaveScores($id_kegiatan, $kegiatanLama['id_jenis_kegiatan'], $data);
    
            // 5. SINKRONISASI ANGGOTA
            // Hapus semua anggota lama (termasuk ketua di tabel anggota)
            $this->db->table('kegiatan_mahasiswa_anggota')->where('id_kegiatan', $id_kegiatan)->delete();
    
            // Siapkan data ketua dari Session (Sesuai dengan struktur saveMembersBatch Anda)
            $data_mhs = [
                'nim'           => session()->get('nim'),
                'nama'          => session()->get('nama'),
                'tempat_lahir'  => session()->get('namakotalahir'),
                'tanggal_lahir' => session()->get('tgllahir'),
                'fakultas'      => session()->get('fakultas'),
                'prodi'         => session()->get('prodi'),
                'jenjang'       => session()->get('jenjang'),
                'semester'      => session()->get('semester'),
            ];
    
            // Masukkan kembali anggota baru menggunakan fungsi Batch Anda
            // Fungsi ini akan menangani insert Ketua dan Anggota (dari $data['anggotaData'])
            $this->saveMembersBatch(
                $id_kegiatan, 
                $kegiatanLama['id_jenis_kegiatan'], 
                $data, 
                $scoreSummary, 
                $data_mhs
            );
    
            // 6. Simpan Log
            $this->SaveLogs($id_kegiatan, "Revisi Data", 'Diajukan', session()->get('nama'));
    
            $this->db->transComplete();
    
            if ($this->db->transStatus() === false) {
                return ['status' => false, 'message' => 'Gagal menyelesaikan transaksi database.'];
            }
    
            return ['status' => true];
    
        } catch (\Exception $e) {
            $this->db->transRollback();
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    // DELETE
    public function softDeleteActivity(array $kegiatan)
    {
        $idK = $kegiatan['id_kegiatan'];

        try {
            // 1. Hapus File Fisik
            $nim  = $kegiatan['nim_pengaju'];
            $slug = $kegiatan['slug_kegiatan_mahasiswa'];
            $pathFolder = FCPATH . 'uploads/tkm/' . $nim . '/' . $slug . '/';
            $fullPathFile = $pathFolder . ($kegiatan['file_laporan'] ?? '');

            if (!empty($kegiatan['file_laporan']) && file_exists($fullPathFile)) {
                @unlink($fullPathFile);
            }

            // 2. Hapus folder jika kosong
            if (is_dir($pathFolder) && count(scandir($pathFolder)) == 2) {
                @rmdir($pathFolder);
            }

            // 3. Eksekusi Soft Delete
            // 3. Eksekusi Soft Delete menggunakan Model
            $model = new \App\Models\KegiatanMahasiswaModel();
            $result = $model->delete($idK);

            if ($result) {
                return true;
            }

            $errorMsg = '[SoftDelete] Update affected 0 rows untuk ID ' . $idK;
            log_message('error', $errorMsg);
            return $errorMsg;
        } catch (\Exception $e) {
            $errorMsg = '[SoftDelete Error] ' . $e->getMessage();
            log_message('error', $errorMsg);
            return $errorMsg;
        }
    }

    // SAVE LOGS
    public function SaveLogs($idKegiatan, $status_lama = null, $status_baru = null, $verifikator = null)
    {
        // Pastikan $this->LogsModel sudah diinisialisasi
        return $this->LogsModel->insert((object)[
            'id_kegiatan'         => $idKegiatan,
            'status_lama'         => $status_lama ?: "Revisi",
            'status_baru'         => $status_baru ?: "Diajukan",
            'catatan_verifikator' => "Diajukan oleh Ketua",
            'verifikator'         => $verifikator,
            'created_at'          => date('Y-m-d H:i:s')
        ]);
    }
}
