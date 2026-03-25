<?php

function countscorekegiatan($all_kegiatan)
{
    return array_sum(array_column($all_kegiatan, 'total_kredit'));
}

function counttotalkegiatan($all_kegiatan)
{
    $total = 0;
    foreach ($all_kegiatan as $kegiatan) {
        $total++;
    }
    return $total;
}

function countproseskegiatan($all_kegiatan)
{
    $total = 0;
    foreach ($all_kegiatan as $kegiatan) {
        if ($kegiatan['status_pengajuan'] == 'Diajukan') {
            $total++;
        }
    }
    return $total;
}

function countrevisikegiatan($all_kegiatan)
{
    $total = 0;
    foreach ($all_kegiatan as $kegiatan) {
        if ($kegiatan['status_pengajuan'] == 'Revisi') {
            $total++;
        }
    }
    return $total;
}

function counttolakkegiatan($all_kegiatan)
{
    $total = 0;
    foreach ($all_kegiatan as $kegiatan) {
        if ($kegiatan['status_pengajuan'] == 'Ditolak') {
            $total++;
        }
    }
    return $total;
}

function countloloskegiatan($all_kegiatan)
{
    $total = 0;
    foreach ($all_kegiatan as $kegiatan) {
        if ($kegiatan['status_pengajuan'] == 'Disetujui') {
            $total++;
        }
    }
    return $total;
}

function check_jenjang($jenjang, $kumulatif_poin)
{
    // Konfigurasi ambang batas berdasarkan tabel
    $config = [
        'S1'  => ['min' => 41, 'max' => 160],
        'D3'  => ['min' => 31, 'max' => 120],
        'Ekstensi' => ['min' => 21, 'max' => 40],
    ];

    // Jika jenjang tidak ditemukan di tabel, anggap sebagai Ekstensi (default sesuai kode awal Anda)
    $threshold = $config[$jenjang] ?? $config['Ekstensi'];

    // 1. Jika di bawah minimal, kembalikan null
    if ($kumulatif_poin < $threshold['min']) {
        return null;
    }

    // 2. Jika di dalam rentang (min s/d max), kembalikan true. Jika melebihi max, kembalikan false.
    return $kumulatif_poin <= $threshold['max'];
}
