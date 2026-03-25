<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// AUTH
$routes->get('/', 'Auth::login');
$routes->get('login', 'Auth::login');
$routes->post('login/attempt', 'Auth::attemptLogin');
$routes->get('logout', 'Auth::logout');

// Route khusus RUM
$routes->post('cdn-cgi/rum', 'Auth::cdn_rum');

// Contoh Group Dashboard yang diproteksi
// $routes->group('dashboard', ['filter' => 'auth'], function ($routes) {
//     });

// DASHBOARD
$routes->group('', ['filter' => 'auth'], function ($routes) {
    $routes->get('dashboard', 'Dashboard::index');
    $routes->get('Dashboard', 'Dashboard::index');
    $routes->get('detail-point-mahasiswa/(:num)', 'Dashboard::detail_point_mahasiswa/$1');

    $routes->get('riwayat-mahasiswa/(:num)', 'Dashboard::riwayat_mahasiswa/$1');
    $routes->get('pengajuan-kegiatan', 'Kegiatan::index');
    $routes->get('jenis-pengajuan', 'Kegiatan::jenis_pengajuan');

    $routes->get('detail-pengajuan/(:segment)/(:segment)', 'Kegiatan::detail_pengajuan/$1/$2');

    // PENGAJUAN
    $routes->get('jenis-pengajuan/(:any)', 'Pengajuan::form/$1');
    $routes->post('Pengajuan-Kegiatan/(:any)', 'Pengajuan::add_pengajuan_kegiatan/$1');

    $routes->get('edit-jenis-pengajuan/(:any)', 'Pengajuan::edit/$1');
    $routes->post('Delete-Pengajuan-Kegiatan/(:any)', 'Pengajuan::delete/$1');
    $routes->post('Edit-Pengajuan-Kegiatan/(:any)', 'Pengajuan::update_pengajuan/$1');

    // PENCARIAN
    $routes->get('carimhs', 'Search::getMahasiswa');

    // CETAK
    $routes->get('Cetak-Kegiatan', 'CetakKegiatan::cetak_dan_merge'); // atau method DELETE kalau mau RESTful
    $routes->post('Cetak-Semua-Kegiatan', 'CetakKegiatan::cetak_semua_kegiatan'); // atau method DELETE kalau mau RESTful

    // Karomahassiwa
    $routes->get('verifikasi-dokumen', 'Verifikasi::index');
    $routes->get('verifikasi-dokumen/(:segment)/(:segment)', 'Verifikasi::detail_dokumen/$1/$2');
    $routes->post('verifikasi-dokumen', 'Verifikasi::proses_verifikasi');

    // Karomahassiwa - Jenis_Kegiatan
    $routes->get('jenis-kegiatan', 'JenisKegiatan::index');
    $routes->get('jenis-kegiatan/create', 'JenisKegiatan::create');
    $routes->post('jenis-kegiatan/store', 'JenisKegiatan::store');
    $routes->get('jenis-kegiatan/edit/(:segment)', 'JenisKegiatan::edit/$1');
    $routes->post('jenis-kegiatan/update/(:segment)', 'JenisKegiatan::update/$1');
    $routes->get('jenis-kegiatan/delete/(:segment)', 'JenisKegiatan::delete/$1'); // atau method DELETE kalau mau RESTful

    // Karomahassiwa - Elemen_Penilaian
    $routes->get('elemen-penilaian', 'ElemenPenilaian::index');
    $routes->get('elemen-penilaian/create', 'ElemenPenilaian::create');
    $routes->post('elemen-penilaian/save', 'ElemenPenilaian::save');
    $routes->get('elemen-penilaian/edit/(:segment)', 'ElemenPenilaian::edit/$1');
    $routes->post('elemen-penilaian/update/(:segment)', 'ElemenPenilaian::update/$1');
    $routes->get('elemen-penilaian/delete/(:segment)', 'ElemenPenilaian::delete/$1'); // atau method DELETE kalau mau RESTful

    // Karomahassiwa - Kategori_Kegiatan
    $routes->get('kategori-kegiatan', 'KategoriKegiatan::index');
    $routes->post('kategori-kegiatan/save', 'KategoriKegiatan::save');
    $routes->post('kategori-kegiatan/update/(:segment)', 'KategoriKegiatan::update/$1');
    $routes->get('kategori-kegiatan/delete/(:segment)', 'KategoriKegiatan::delete/$1'); // atau method DELETE kalau mau RESTful

    // WAREK III
    $routes->post('setujui-batch', 'Verifikasi::SetujuiBatch');
    $routes->get('semua-tkm', 'Dashboard::All_Kegiatan');
});
