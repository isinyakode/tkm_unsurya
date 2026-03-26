<?php

namespace App\Services;

use setasign\Fpdi\Tcpdf\Fpdi;

class CetakPdfService
{
    public function generateSkpi($mhs, $allKegiatan, $total_kredit, $jenjang)
    {
        helper(['cetak_helper', 'tkm']);
        
        $pdf = new class('P', 'mm', 'A4', true, 'UTF-8', false) extends Fpdi {
            public function Footer()
            {
                $this->SetY(-15);
                $this->SetFont('helvetica', 'I', 8);
                $this->Cell(0, 10, 'Halaman ' . $this->getAliasNumPage() . ' | Dicetak pada: ' . date('d/M/Y H:i'), 0, 0, 'R');
            }
        };
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(true);
        $pdf->SetMargins(20, 15, 20);

        // BAGIAN 1: LAMPIRAN 3
        $pdf->AddPage();
        $logoPath = FCPATH . 'assets/img/kop_surat.jpeg';
        if (file_exists($logoPath)) {
            $pdf->Image($logoPath, 0, 0, 210, 0, 'JPEG');
        }
        $pdf->SetY(55);
        $pdf->SetFont('times', 'B', 12);
        $pdf->Cell(0, 5, 'TRANSKRIP KEGIATAN MAHASISWA', 0, 1, 'C');
        $pdf->SetFont('times', 'I', 11);
        $pdf->Cell(0, 5, 'STUDENT ACTIVITY TRANSCRIPT', 0, 1, 'C');
        $pdf->Ln(2);
        $pdf->SetFont('times', '', 10);
        $pdf->Cell(0, 5, 'No: WR-III / TKM / ' . str_repeat(' ', 8) . '/' . str_repeat(' ', 8) . '/ ' . date('Y'), 0, 1, 'C');
        $pdf->Ln(8);

        // Identitas
        $pdf->SetFont('times', 'B', 10);
        $pdf->Cell(75, 5, 'Nama/ Name', 0, 0); $pdf->Cell(5, 5, ':', 0, 0);
        $pdf->SetFont('times', '', 10); $pdf->Cell(0, 5, $mhs['nama'], 0, 1);
        
        $pdf->SetFont('times', 'B', 10);
        $pdf->Cell(75, 5, 'Nomor Induk Mahasiswa/ Student\'s ID', 0, 0); $pdf->Cell(5, 5, ':', 0, 0);
        $pdf->SetFont('times', '', 10); $pdf->Cell(0, 5, $mhs['nim'], 0, 1);
        
        $pdf->SetFont('times', 'B', 10);
        $pdf->Cell(75, 5, 'Program Studi / Study Program', 0, 0); $pdf->Cell(5, 5, ':', 0, 0);
        $pdf->SetFont('times', '', 10); $pdf->Cell(0, 5, $mhs['prodi'], 0, 1);
        
        $pdf->SetFont('times', 'B', 10);
        $pdf->Cell(75, 5, 'Fakultas / Faculty', 0, 0); $pdf->Cell(5, 5, ':', 0, 0);
        $pdf->SetFont('times', '', 10); $pdf->Cell(0, 5, $mhs['fakultas'], 0, 1);
        
        $pdf->SetFont('times', 'B', 10);
        $pdf->Cell(75, 5, 'Tempat & Tanggal Lahir/ Place & Date of Birth', 0, 0); $pdf->Cell(5, 5, ':', 0, 0);
        $pdf->SetFont('times', '', 10); $pdf->Cell(0, 5, $mhs['tempat_lahir'] . ', ' . date('d M Y', strtotime($mhs['tanggal_lahir'])), 0, 1);
        $pdf->Ln(5);

        // Tabel Transkrip
        $pdf->SetFont('times', 'B', 9);
        $pdf->SetFillColor(235, 235, 235);
        $pdf->Cell(10, 10, 'No.', 1, 0, 'C', true);
        $pdf->Cell(20, 10, "Tahun/\nYear", 1, 0, 'C', true);
        $pdf->Cell(50, 10, "Kegiatan/\nActivity", 1, 0, 'C', true);
        $pdf->Cell(65, 10, "Deskripsi/\nDescription", 1, 0, 'C', true);
        $pdf->Cell(25, 10, "Kredit/\nCredit", 1, 1, 'C', true);

        $pdf->SetFont('times', '', 9);
        $no = 1;
        foreach ($allKegiatan as $k) {
            $h_baris = max(10, $pdf->getStringHeight(65, $k['deskripsi_kegiatan']));
            if ($pdf->GetY() + $h_baris > $pdf->getPageHeight() - 30) $pdf->AddPage();
            $pdf->MultiCell(10, $h_baris, $no++, 1, 'C', 0, 0, '', '', true, 0, false, true, $h_baris, 'M');
            $pdf->MultiCell(20, $h_baris, date('Y', strtotime($k['tanggal_mulai'])), 1, 'C', 0, 0, '', '', true, 0, false, true, $h_baris, 'M');
            $pdf->MultiCell(50, $h_baris, $k['nama_kegiatan'], 1, 'L', 0, 0, '', '', true, 0, false, true, $h_baris, 'M');
            $pdf->MultiCell(65, $h_baris, $k['deskripsi_kegiatan'], 1, 'L', 0, 0, '', '', true, 0, false, true, $h_baris, 'M');
            $pdf->MultiCell(25, $h_baris, $k['total_kredit'], 1, 'C', 0, 1, '', '', true, 0, false, true, $h_baris, 'M');
        }

        $hasil_predikat = hitung_predikat_tkm($jenjang, $total_kredit);
        $pdf->Ln(5);
        $pdf->SetFont('times', 'B', 10);
        $pdf->Cell(100, 5, 'Pencapaian Kredit Kumulatif/ Cumulative Scores:', 0, 0, 'L');
        $pdf->Cell(0, 5, $total_kredit, 0, 1, 'L');
        $pdf->SetFont('times', '', 10);
        $pdf->Cell(20, 5, 'Predikat: ', 0, 0, 'L');
        $pdf->SetFont('times', 'B', 10);
        $pdf->Cell(0, 5, $hasil_predikat, 0, 1, 'L');

        $this->tambahFooterTandaTangan($pdf);

        // BAGIAN 2: LAMPIRAN 2
        $kegiatanPerSemester = [];
        foreach ($allKegiatan as $k) {
            $kegiatanPerSemester[$k['semester']][] = $k;
        }
        ksort($kegiatanPerSemester);
        $pdf->AddPage();
        $pdf->SetFont('times', 'B', 12);
        $pdf->Cell(0, 5, 'TRANSKRIP KEGIATAN KUMULATIF', 0, 1, 'C');
        $pdf->Ln(10);

        $this->tambahTabelLampiran2($pdf, $kegiatanPerSemester);
        $pdf->SetFont('times', 'B', 10);
        $pdf->Cell(145, 8, 'TOTAL KREDIT', 1, 0, 'C', true);
        $pdf->Cell(25, 8, $total_kredit, 1, 1, 'C', true);

        // BAGIAN 3: LAMPIRAN 1
        $pdf->AddPage();
        $pdf->SetFont('times', 'B', 12);
        $pdf->Cell(0, 5, 'TRANSKRIP KEGIATAN MAHASISWA PERSEMESTER', 0, 1, 'C');
        $pdf->Ln(10);

        $this->tambahTabelDetail($pdf, $allKegiatan);

        return $pdf->Output('Transkrip_Lengkap_' . $mhs['nim'] . '.pdf', 'S');
    }

    public function generateLaporanPimpinan($postData, $topMahasiswa, $sessionData)
    {
        $pdf = new Fpdi('L', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetAutoPageBreak(true, 25);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(10, 10, 10);

        $periodes = ['GANJIL', 'GENAP'];
        foreach ($periodes as $p) {
            $prefix = strtolower($p);
            $laporan = [
                ['judul' => '1. SEBARAN JENIS KEGIATAN', 'img' => $postData[$prefix . '_jenis'] ?? ''],
                ['judul' => '2. TREN BULANAN KEGIATAN', 'img' => $postData[$prefix . '_bulan'] ?? ''],
                ['judul' => '3. TOTAL KEGIATAN PER FAKULTAS', 'img' => $postData[$prefix . '_fakultas'] ?? ''],
                ['judul' => '4. TOTAL KEGIATAN PER PROGRAM STUDI', 'img' => $postData[$prefix . '_prodi'] ?? '']
            ];

            foreach ($laporan as $item) {
                $pdf->AddPage();
                $pdf->SetFont('helvetica', 'B', 18);
                $pdf->Cell(0, 10, 'LAPORAN STATISTIK KEGIATAN MAHASISWA', 0, 1, 'C');
                $pdf->SetFont('helvetica', 'B', 14);
                $pdf->Cell(0, 7, 'PERIODE ' . $p . ' - ' . $item['judul'], 0, 1, 'C');
                $pdf->SetLineWidth(0.8);
                $pdf->Line(15, $pdf->GetY() + 3, 282, $pdf->GetY() + 3);

                if (!empty($item['img'])) {
                    $imgData = base64_decode(str_replace('data:image/jpeg;base64,', '', $item['img']));
                    $pdf->Image('@' . $imgData, 18, $pdf->GetY() + 5, 260, 125, 'JPG');
                }
                $this->renderFooter($pdf);
            }
        }

        $pdf->AddPage();
        $pdf->SetFont('helvetica', 'B', 18);
        $pdf->Cell(0, 10, 'DAFTAR 10 MAHASISWA DENGAN POIN TERTINGGI', 0, 1, 'C');
        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->Cell(0, 7, 'SELURUH PERIODE TAHUN AKADEMIK', 0, 1, 'C');
        $pdf->SetLineWidth(0.8);
        $pdf->Line(15, $pdf->GetY() + 3, 282, $pdf->GetY() + 3);
        $pdf->Ln(15);

        $pdf->SetFillColor(44, 62, 80);
        $pdf->SetTextColor(255);
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(15, 12, 'No', 1, 0, 'C', true);
        $pdf->Cell(40, 12, 'NIM', 1, 0, 'C', true);
        $pdf->Cell(105, 12, 'Nama Mahasiswa', 1, 0, 'C', true);
        $pdf->Cell(75, 12, 'Program Studi', 1, 0, 'C', true);
        $pdf->Cell(32, 12, 'Total Point', 1, 1, 'C', true);

        $pdf->SetTextColor(0);
        $pdf->SetFont('helvetica', '', 11);
        $no = 1;
        $fill = false;
        foreach ($topMahasiswa as $mhs) {
            $pdf->SetFillColor(240, 240, 240);
            $pdf->Cell(15, 10, $no++, 1, 0, 'C', $fill);
            $pdf->Cell(40, 10, $mhs['nim'], 1, 0, 'C', $fill);
            $pdf->Cell(105, 10, '  ' . $mhs['nama'], 1, 0, 'L', $fill);
            $pdf->Cell(75, 10, '  ' . $mhs['fakultas'], 1, 0, 'L', $fill);
            $pdf->Cell(75, 10, '  ' . $mhs['prodi'], 1, 0, 'L', $fill);
            $pdf->Cell(32, 10, $mhs['total_point'], 1, 1, 'C', $fill);
            $fill = !$fill;
        }

        $pdf->Ln(15);
        $pdf->SetFont('helvetica', '', 12);
        $pdf->SetX(200);
        $pdf->Cell(70, 7, 'Bekasi, ' . date('d F Y'), 0, 1, 'C');
        $pdf->SetX(200);
        $pdf->Cell(70, 7, 'Mengetahui,', 0, 1, 'C');
        $pdf->SetX(200);
        if ($sessionData['is_warek']) {
            $pdf->Cell(70, 7, 'Warek III', 0, 1, 'C');
        } elseif ($sessionData['is_kabiro']) {
            $pdf->Cell(70, 7, 'Kabiro Kemahasiswaan', 0, 1, 'C');
        } elseif ($sessionData['is_kabag']) {
            $pdf->Cell(70, 7, 'Kabag Kreativitas & Ormawa', 0, 1, 'C');
        }
        $pdf->Ln(20);
        $pdf->SetX(200);
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(70, 7, '( ' . $sessionData['nama'] . ' )', 0, 1, 'C');
        $pdf->SetX(200);
        $pdf->Cell(70, 7, 'NIP. ' . $sessionData['nim'], 0, 1, 'C');

        $this->renderFooter($pdf);
        return $pdf->Output('Laporan_Kegiatan_Tahunan.pdf', 'S');
    }

    private function tambahFooterTandaTangan($pdf)
    {
        $currY = $pdf->GetY() + 10;
        $pdf->SetXY(20, $currY);
        $pdf->SetFont('times', '', 10);
        $pdf->Cell(80, 5, 'Mengetahui,', 0, 1, 'C');
        $pdf->Cell(80, 5, 'Wakil Rektor III', 0, 1, 'C');
        $pdf->Ln(20);
        $pdf->Cell(80, 5, '( Dr. Agus Purwo Wicaksono, SE., MM., MA., CIPA )', 0, 1, 'C');
        
        $pdf->SetXY(110, $currY);
        $pdf->SetFont('times', '', 10);
        $pdf->Cell(80, 5, 'Jakarta, ' . str_repeat(' ', 10) . date('F Y'), 0, 1, 'C');
        $pdf->SetX(110);
        $pdf->Cell(80, 5, 'Kepala Biro Kemahasiswaan', 0, 1, 'C');
        $pdf->Ln(20);
        $pdf->SetX(110);
        $pdf->Cell(80, 5, '( Drs. Agus Suharto, M.Si )', 0, 1, 'C');
    }

    private function tambahTabelDetail($pdf, $allKegiatan)
    {
        $pdf->SetFont('times', 'B', 10);
        $pdf->SetFillColor(235, 235, 235);
        $pdf->Cell(10, 10, 'No.', 1, 0, 'C', true);
        $pdf->Cell(20, 10, 'Tahun', 1, 0, 'C', true);
        $pdf->Cell(50, 10, 'Nama Kegiatan', 1, 0, 'C', true);
        $pdf->Cell(65, 10, 'Deskripsi Kegiatan', 1, 0, 'C', true);
        $pdf->Cell(25, 10, 'Kredit', 1, 1, 'C', true);

        $pdf->SetFont('times', '', 9);
        $no = 1;

        foreach ($allKegiatan as $k) {
            $w_deskripsi = 65;
            $deskripsi = $k['deskripsi_kegiatan'];
            $h_baris = $pdf->getStringHeight($w_deskripsi, $deskripsi);
            if ($h_baris < 10) $h_baris = 10;

            if ($pdf->GetY() + $h_baris > $pdf->getPageHeight() - 10) {
                $pdf->AddPage();
                $pdf->SetFont('times', 'B', 10);
                $pdf->Cell(10, 10, 'No.', 1, 0, 'C', true);
                $pdf->Cell(20, 10, 'Tahun', 1, 0, 'C', true);
                $pdf->Cell(50, 10, 'Nama Kegiatan', 1, 0, 'C', true);
                $pdf->Cell(65, 10, 'Deskripsi Kegiatan', 1, 0, 'C', true);
                $pdf->Cell(25, 10, 'Kredit', 1, 1, 'C', true);
                $pdf->SetFont('times', '', 9);
            }

            $pdf->MultiCell(10, $h_baris, $no++, 1, 'C', 0, 0, '', '', true, 0, false, true, $h_baris, 'M');
            $pdf->MultiCell(20, $h_baris, date('Y', strtotime($k['tanggal_mulai'])), 1, 'C', 0, 0, '', '', true, 0, false, true, $h_baris, 'M');
            $pdf->MultiCell(50, $h_baris, $k['nama_kegiatan'], 1, 'L', 0, 0, '', '', true, 0, false, true, $h_baris, 'M');
            $pdf->MultiCell($w_deskripsi, $h_baris, $deskripsi, 1, 'L', 0, 0, '', '', true, 0, false, true, $h_baris, 'M');
            $pdf->MultiCell(25, $h_baris, $k['total_kredit'], 1, 'C', 0, 1, '', '', true, 0, false, true, $h_baris, 'M');
        }
    }

    private function tambahTabelLampiran2($pdf, $kegiatanPerSemester)
    {
        $pdf->SetFont('times', 'B', 10);
        $pdf->SetFillColor(235, 235, 235);
        $pdf->Cell(10, 8, 'No', 1, 0, 'C', true);
        $pdf->Cell(70, 8, 'Kegiatan', 1, 0, 'C', true);
        $pdf->Cell(65, 8, 'Deskripsi', 1, 0, 'C', true);
        $pdf->Cell(25, 8, 'Kredit', 1, 1, 'C', true);

        $romawi = [1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII'];

        foreach ($kegiatanPerSemester as $smt => $daftarKegiatan) {
            $pdf->SetFont('times', 'B', 10);
            $pdf->SetFillColor(255, 255, 255);
            $pdf->Cell(170, 7, 'Semester ' . ($romawi[$smt] ?? $smt), 1, 1, 'C', true);

            $pdf->SetFont('times', '', 9);
            $no = 1;
            foreach ($daftarKegiatan as $k) {
                $h_baris = max(10, $pdf->getStringHeight(65, $k['deskripsi_kegiatan']));
                if ($pdf->GetY() + $h_baris > $pdf->getPageHeight() - 15) {
                    $pdf->AddPage();
                }
                $pdf->MultiCell(10, $h_baris, $no++, 1, 'C', 0, 0, '', '', true, 0, false, true, $h_baris, 'M');
                $pdf->MultiCell(70, $h_baris, $k['nama_kegiatan'], 1, 'L', 0, 0, '', '', true, 0, false, true, $h_baris, 'M');
                $pdf->MultiCell(65, $h_baris, $k['deskripsi_kegiatan'], 1, 'L', 0, 0, '', '', true, 0, false, true, $h_baris, 'M');
                $pdf->MultiCell(25, $h_baris, $k['total_kredit'], 1, 'C', 0, 1, '', '', true, 0, false, true, $h_baris, 'M');
            }
        }
    }

    private function renderFooter($pdf)
    {
        $pdf->SetY(-35);
        $pdf->SetFont('helvetica', 'I', 8);
        $pdf->Cell(0, 10, 'Halaman ' . $pdf->getAliasNumPage() . ' | Dicetak pada: ' . date('d/M/Y H:i'), 0, 0, 'R');
    }
}
