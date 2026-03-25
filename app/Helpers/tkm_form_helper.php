<?php

if (!function_exists('get_jenis_kegiatan_config')) {
    function get_jenis_kegiatan_config(array $jenis_kegiatan)
    {
        return [
            'peranMode'        => $jenis_kegiatan['peran_mode']    ?? 'fixed',
            'peranFixedValue'  => $jenis_kegiatan['peran_default'] ?? 'KETUA',
            'peranFixedId'     => $jenis_kegiatan['peran_default_id'] ?? null,
            'labelJenis'       => $jenis_kegiatan['label_jenis']   ?? null,
            'showTanggal'      => (bool)($jenis_kegiatan['show_tanggal'] ?? 1),
            'showAnggota'      => (bool)($jenis_kegiatan['show_anggota'] ?? 1),
            'showNamaKegiatan' => (bool)($jenis_kegiatan['show_nama_kegiatan'] ?? 1),
            'showDeskripsi'    => (bool)($jenis_kegiatan['show_deskripsi'] ?? 1),
            'showLokasi'       => (bool)($jenis_kegiatan['show_lokasi'] ?? 1),
        ];
    }
}

if (!function_exists('get_dynamic_validation_rules')) {
    function get_dynamic_validation_rules(array $setup, $idJenis)
    {
        $rules = [];
        $db = \Config\Database::connect();
        
        if ($idJenis != 1) { // Jika bukan PKKMB
            if (($setup['show_nama_kegiatan'] ?? 0) == 1) $rules['nama_kegiatan'] = 'required|min_length[5]';
            if (($setup['show_deskripsi'] ?? 0) == 1)     $rules['deskripsi_kegiatan'] = 'required';
            if (($setup['show_lokasi'] ?? 0) == 1)        $rules['lokasi_kegiatan'] = 'required';
            if (($setup['show_tanggal_mulai'] ?? 0) == 1)  $rules['tanggal_mulai'] = 'required|valid_date';
            if (($setup['show_tanggal_selesai'] ?? 0) == 1) $rules['tanggal_selesai'] = 'required|valid_date';

            // Ambil Elemen Penilaian (Tingkat, Omzet, dsb)
            $elemenAktif = $db->table('jenis_elemen')
                ->join('elemen_penilaian', 'jenis_elemen.id_elemen_penilaian = elemen_penilaian.id_elemen_penilaian')
                ->where('id_jenis_kegiatan', $idJenis)->get()->getResultArray();

            foreach ($elemenAktif as $el) {
                // Menggunakan request_key dari database
                $rules[$el['request_key']] = [
                    'rules' => 'required',
                    'label' => $el['nama_elemen_penilaian']
                ];
            }
        }
        return $rules;
    }
}

if (!function_exists('render_status_verifikasi_select')) {
    function render_status_verifikasi_select($status_saat_ini, $otoritas)
    {
        $html = '<div class="form-group p-0 mb-3">
                    <label class="fw-bold mb-1">Status Verifikasi</label>
                    <select name="status_pengajuan" class="form-select border-primary" required>
                        <option value="" ' . ($status_saat_ini == '' ? 'selected' : '') . '>-- Pilih Status --</option>
                        <option value="Ditolak" ' . ($status_saat_ini == 'Ditolak' ? 'selected' : '') . '>Tolak (Reject)</option>
                        <option value="Revisi" ' . ($status_saat_ini == 'Revisi' ? 'selected' : '') . '>Revisi (Revision)</option>';
        
        if ($otoritas === "WAREK III" || $otoritas === "Kabiro Mahasiswa") {
            $html .= '<option value="Disetujui" ' . ($status_saat_ini == 'Disetujui' ? 'selected' : '') . '> Disetujui (Accept) </option>';
        } elseif ($otoritas === "ORMAWA DAN ALUMNI") {
            $html .= '<option value="Ditinjau" ' . ($status_saat_ini == 'Ditinjau' ? 'selected' : '') . '> Ditinjau (Review) </option>';
        } elseif ($otoritas === "KETUA PROGRAM STUDI") {
            $html .= '<option value="Diverifikasi" ' . ($status_saat_ini == 'Diverifikasi' ? 'selected' : '') . '> Diverifikasi (Verify) </option>';
        }

        $html .= '</select>
                </div>';

        return $html;
    }
}
