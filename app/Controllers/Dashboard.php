<?php

namespace App\Controllers;

use App\Models\KegiatanMahasiswaModel;
use App\Models\JenisKegiatanMahasiswaModel;

class Dashboard extends BaseController
{
    protected $KegiatanMahasiswaModel;
    protected $JenisKegiatanMahasiswaModel;
    protected $session;
    public function __construct()
    {
        $this->KegiatanMahasiswaModel = new KegiatanMahasiswaModel();
        $this->JenisKegiatanMahasiswaModel = new JenisKegiatanMahasiswaModel();
        $this->session = session();
        helper(['auth_helper', 'auth', 'count_helper', 'cetak_helper']);
    }

    public function index()
    {
        $nim = $this->session->get('nim');

        $all_kegiatan = $this->KegiatanMahasiswaModel->getKegiatan($nim);
        
        $all_kegiatan_disetujui = $this->KegiatanMahasiswaModel->getKegiatan($nim, 'Diajukan');

        $score_kegiatan = countscorekegiatan($all_kegiatan_disetujui);
        $total_kegiatan = counttotalkegiatan($all_kegiatan);
        $tolak_kegiatan = counttolakkegiatan($all_kegiatan);
        $proses_kegiatan = countproseskegiatan($all_kegiatan);
        $revisi_kegiatan = countrevisikegiatan($all_kegiatan);
        $lolos_kegiatan = countloloskegiatan($all_kegiatan);
        $data = [
            'title' => "Dashboard",
            'score_kegiatan' => $score_kegiatan,
            'total_kegiatan' => $total_kegiatan,
            'proses_kegiatan' => $proses_kegiatan,
            'revisi_kegiatan' => $revisi_kegiatan,
            'tolak_kegiatan' => $tolak_kegiatan,
            'lolos_kegiatan' => $lolos_kegiatan,
            'kegiatan' => $all_kegiatan,
        ];
        if (is_warek() || is_kabiro() || is_kabag()) {
            return $this->pimpinan();
        }
        return view('Dashboard/index', $data);
    }

    public function pimpinan()
    {
        // --- CHART 1: Agregasi per Jenis Kegiatan ---
        $statsQuery = $this->KegiatanMahasiswaModel->chartdata();
        $statsMap = array_column($statsQuery, null, 'id_jenis_kegiatan');
        $jenis_kegiatan = $this->JenisKegiatanMahasiswaModel->findAll();

        $labels = [];
        $dataGanjil = [];
        $dataGenap = [];
        $colors = [];

        foreach ($jenis_kegiatan as $index => $jk) {
            $labels[] = $jk['jenis_kegiatan'];
            $colors[] = "hsla(" . ($index * (360 / max(1, count($jenis_kegiatan)))) . ", 70%, 50%, 0.8)";

            $id = $jk['id_jenis_kegiatan'];
            $dataGanjil[] = isset($statsMap[$id]) ? (int)$statsMap[$id]['ganjil'] : 0;
            $dataGenap[]  = isset($statsMap[$id]) ? (int)$statsMap[$id]['genap'] : 0;
        }

        // --- CHART 2: Data Bulanan (Status-based) ---
        $monthlyQuery = $this->KegiatanMahasiswaModel->chartmouthlydata();

        // Inisialisasi array 12 bulan (index 0-11 untuk JavaScript agar aman)
        $mDisetujui = array_fill(0, 12, 0);
        $mDitolak   = array_fill(0, 12, 0);
        $mPending   = array_fill(0, 12, 0);
        $monthlyRaw = array_fill(0, 12, 0); // Total gabungan per bulan

        foreach ($monthlyQuery as $row) {
            $bulanIndex = (int)$row['bulan'] - 1;
            $status = $row['status_pengajuan'];
            $total = (int)$row['total'];

            // Hitung Total Gabungan (untuk monthly_raw jika masih digunakan grafik lain)
            $monthlyRaw[$bulanIndex] += $total;

            // Pisah berdasarkan status
            if ($status === "Disetujui") {
                $mDisetujui[$bulanIndex] = $total;
            } elseif ($status === "Ditolak") {
                $mDitolak[$bulanIndex] = $total;
            } else {
                // Gabungan Diajukan, Verifikasi, Revisi, dll
                $mPending[$bulanIndex] += $total;
            }
        }

        // --- CHART 3: Data Status per Prodi ---
        $prodiQuery = $this->KegiatanMahasiswaModel->chartprodi();
        $dataProdi = [
            'labels' => array_column($prodiQuery, 'prodi'),
            'ganjil' => array_map('intval', array_column($prodiQuery, 'total_ganjil')),
            'genap'  => array_map('intval', array_column($prodiQuery, 'total_genap')),
        ];

        $fakultasQuery = $this->KegiatanMahasiswaModel->chartfakultas();
        // dd($fakultasQuery);
        $dataFakultas = [
            'labels' => array_column($fakultasQuery, 'fakultas'),
            'ganjil' => array_map('intval', array_column($fakultasQuery, 'total_ganjil')),
            'genap'  => array_map('intval', array_column($fakultasQuery, 'total_genap')),
        ];

        // --- Data Point Mahasiswa ---
        $dataPoint = $this->KegiatanMahasiswaModel->getPointMahasiswa();

        $data = [
            'title'             => "Dashboard",
            'chart_labels'      => $labels,
            'chart_ganjil'      => $dataGanjil,
            'chart_genap'       => $dataGenap,
            'chart_colors'      => $colors,
            'monthly_raw'       => $monthlyRaw,
            'chart_fakultas'    => $dataFakultas,
            'chart_prodi'       => $dataProdi,
            'data_point'        => $dataPoint,
            'monthly_disetujui' => $mDisetujui,
            'monthly_ditolak'   => $mDitolak,
            'monthly_pending'   => $mPending,
        ];

        return view('Dashboard/pimpinan', $data);
    }

    public function riwayat_mahasiswa($nim)
    {
        // $all_kegiatan = $this->KegiatanMahasiswaModel->getallKegiatanApprove($nim);
        $all_kegiatan = $this->KegiatanMahasiswaModel->getKegiatan($nim);
        // dd($all_kegiatan);
        $mhs = [
            'nim' => $all_kegiatan[0]['nim'],
            'nama' => $all_kegiatan[0]['nama'],
            'fakultas' => $all_kegiatan[0]['fakultas'],
            'prodi' => $all_kegiatan[0]['prodi'],
            'jenjang' => $all_kegiatan[0]['jenjang'],
        ];
        $total_kegiatan = countscorekegiatan($all_kegiatan);
        $predikat = hitung_predikat_tkm($mhs['jenjang'], $total_kegiatan);
        $data = [
            'title' => "Riwayat Mahasiswa",
            'mhs' => $mhs,
            'kegiatan' => $all_kegiatan,
            'total_poin' => $total_kegiatan,
            'predikat' => $predikat,
        ];
        // dd($data['total']);
        return view('Dashboard/riwayat_mahasiswa', $data);
    }
    
    public function detail_point_mahasiswa($nim)
    {
        // $all_kegiatan = $this->KegiatanMahasiswaModel->getallKegiatanApprove($nim);
        $all_kegiatan = $this->KegiatanMahasiswaModel->getallKegiatanApprove($nim);
        // dd($all_kegiatan);
        $mhs = [
            'nim' => $all_kegiatan[0]['nim'],
            'nama' => $all_kegiatan[0]['nama'],
            'fakultas' => $all_kegiatan[0]['fakultas'],
            'prodi' => $all_kegiatan[0]['prodi'],
            'jenjang' => $all_kegiatan[0]['jenjang'],
        ];
        $total_kegiatan = countscorekegiatan($all_kegiatan);
        $predikat = hitung_predikat_tkm($mhs['jenjang'], $total_kegiatan);
        $data = [
            'title' => "Riwayat Mahasiswa",
            'mhs' => $mhs,
            'kegiatan' => $all_kegiatan,
            'total_poin' => $total_kegiatan,
            'predikat' => $predikat,
        ];
        // dd($data['total']);
        return view('Dashboard/detail_point_mahasiswa', $data);
    }

    public function All_Kegiatan()
    {
        $all_kegiatan = $this->KegiatanMahasiswaModel->getAllGroupedByNim();
        // dd($all_kegiatan);
        $total_kegiatan = countscorekegiatan($all_kegiatan);
        $data = [
            'title' => "Semua Kegiatan Mahasiswa",
            'kegiatan' => $all_kegiatan,
            'total_poin' => $total_kegiatan,
        ];
        // dd($data['total']);
        return view('Dashboard/semua_kegiatan', $data);
    }
    
    
}
