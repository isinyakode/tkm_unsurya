<?php

if (!function_exists('hitung_predikat_tkm')) {
    /**
     * Menghitung predikat TKM berdasarkan jenjang dan total kredit
     * Sesuai aturan: Sarjana (121+), Diploma (91+), Ekstensi (31+) untuk Istimewa
     */
    function hitung_predikat_tkm($jenjang, $total_kredit)
    {
        $total_kredit = (float) $total_kredit;

        // 1. Validasi awal: Jika di bawah standar minimum umum
        if ($total_kredit < 11) {
            return 'Belum Mencapai Predikat';
        }

        // 2. Mapping ambang batas predikat berdasarkan jenjang
        $rules = [
            'S1' => [121 => 'Istimewa (With Distinction)', 81 => 'Sangat Baik (Excellent)', 41 => 'Baik Sekali (Very Good)'],
            'D3' => [91  => 'Istimewa (With Distinction)', 61 => 'Sangat Baik (Excellent)', 31 => 'Baik Sekali (Very Good)'],
            'RPL' => [31  => 'Istimewa (With Distinction)', 21 => 'Sangat Baik (Excellent)', 16 => 'Baik Sekali (Very Good)'],
        ];

        // Jika jenjang tidak ditemukan dalam daftar rules
        if (!isset($rules[$jenjang])) {
            return 'Jenjang Tidak Dikenal';
        }

        // 3. Loop melalui ambang batas (dari yang tertinggi ke terendah)
        foreach ($rules[$jenjang] as $threshold => $label) {
            if ($total_kredit >= $threshold) {
                return $label;
            }
        }

        // 4. Default jika >= 11 tapi tidak mencapai threshold 'Baik Sekali'
        return 'Baik (Good)';
    }

    function get_status_badge($status)
    {
        $class = match ($status) {
            'Disetujui' => 'success',
            'Revisi'     => 'warning',
            'Diajukan'   => 'info',
            default      => 'secondary'
        };
        return '<span class="badge badge-' . $class . ' py-1 px-2">' . esc($status) . '</span>';
    }

    function kriteria_penilaian($kriteria_penilaian)
    {
        $kriteria = '';
        foreach ($kriteria_penilaian as $kp) {
            $kriteria .= '<span class="badge badge-info py-1 px-2">' . esc($kp['nama_elemen_penilaian']) . '</span> ';
        }

        return $kriteria;
    }
}
