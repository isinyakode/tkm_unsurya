<?php /** @var \CodeIgniter\View\View $this */ ?>
<!-- Sidebar -->
<div class="sidebar" data-background-color="dark">
    <div class="sidebar-logo">
        <!-- Logo Header -->
        <div class="logo-header" data-background-color="dark">

            <a href="<?= base_url("/") ?>" class="logo">
                <img src="<?= base_url('/') ?>assets/img/LOGO_TKM.png" alt="navbar brand" class="navbar-brand" height="30">
            </a>
            <div class="nav-toggle">
                <button class="btn btn-toggle toggle-sidebar">
                    <i class="gg-menu-right"></i>
                </button>
                <button class="btn btn-toggle sidenav-toggler">
                    <i class="gg-menu-left"></i>
                </button>
            </div>
            <button class="topbar-toggler more">
                <i class="gg-more-vertical-alt"></i>
            </button>

        </div>
        <!-- End Logo Header -->
    </div>
    <div class="sidebar-wrapper scrollbar scrollbar-inner">
        <div class="sidebar-content">
            <ul class="nav nav-secondary">
                <li class="nav-item <?= ($title == "Dashboard" ? "active" : "") ?>">
                    <a href="<?= base_url('/') ?>">
                        <i class="fas fa-home"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <!-- MAHASISWA -->
                <?php if (is_mahasiswa() || is_kaplti()): ?>
                    <li class="nav-section">
                        <span class="sidebar-mini-icon">
                            <i class="fa fa-ellipsis-h"></i>
                        </span>
                        <h4 class="text-section">MAHASISWA</h4>
                    </li>
                    <li class="nav-item <?= ($title == "Kegiatan" || $title == "Jenis Pengajuan" || $title == "Detail Pengajuan" || $title == "Formulir" ? "active" : "") ?>">
                        <a href="<?= base_url('pengajuan-kegiatan') ?>">
                            <i class="fas fa-file-alt"></i>
                            <p>Pengajuan Kegiatan</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a target="_blank" href="<?= base_url("assets/pedoman/Pedoman-TKM-Unsurya-2025.pdf") ?>">
                            <i class="fas fa-file-pdf"></i>
                            <p>Pedoman Pengajuan</p>
                        </a>
                    </li>
                <?php endif; ?>

                <!-- BIRO KEMAHASISWAAN -->
                <?php if (is_kabiro() || is_kabag() || is_kaplti()): ?>
                    <?php
                    $is_pengaturan_active = in_array($title, [
                        "Manajemen Jenis Kegiatan",
                        "Tambah Jenis Kegiatan",
                        "Edit Jenis Kegiatan",
                        "Manajemen Elemen Penilaian",
                        "Tambah Elemen Penilaian",
                        "Edit Elemen Penilaian",
                        "Manajemen Kategori Kegiatan"
                    ]);
                    ?>
                    <!-- BIRO KEMAHASISWAAN -->
                    <li class="nav-section">
                        <span class="sidebar-mini-icon">
                            <i class="fa fa-ellipsis-h"></i>
                        </span>
                        <h4 class="text-section">BIRO KEMAHASISWAAN</h4>
                    </li>
                    <li class="nav-item <?= ($title == "Verifikasi Dokumen" || $title == "Pratinjau & Verifikasi" ? "active" : "") ?>">
                        <a href="<?= base_url("verifikasi-dokumen") ?>">
                            <i class="fas fa-check-double"></i>
                            <p>Verifikasi Dokumen</p>
                        </a>
                    </li>
                    <li class="nav-item <?= ($title == "Semua Kegiatan Mahasiswa" || $title == "Riwayat Mahasiswa" || $title == "Detail Pengajuan" ? "active" : "") ?>">
                        <a href="<?= base_url("semua-tkm") ?>">
                            <i class="fas fa-check-double"></i>
                            <p>Data Kegiatan</p>
                        </a>
                    </li>
                    <li class="nav-item <?= $is_pengaturan_active ? 'active submenu' : '' ?>">
                        <a data-bs-toggle="collapse"
                            href="#pengaturan_kegiatan"
                            aria-expanded="<?= $is_pengaturan_active ? 'true' : 'false' ?>"
                            class="<?= $is_pengaturan_active ? '' : 'collapsed' ?>">
                            <i class="fas fa-cogs"></i>
                            <p>Pengaturan</p>
                            <span class="caret"></span>
                        </a>

                        <div class="collapse <?= $is_pengaturan_active ? 'show' : '' ?>" id="pengaturan_kegiatan">
                            <ul class="nav nav-collapse">
                                <li class="<?= ($title == "Manajemen Jenis Kegiatan" || $title == "Tambah Jenis Kegiatan" || $title == "Edit Jenis Kegiatan" ? "active" : "") ?>">
                                    <a href="<?= base_url('jenis-kegiatan') ?>">
                                        <span class="sub-item">Jenis Kegiatan</span>
                                    </a>
                                </li>
                                <li class="<?= ($title == "Manajemen Elemen Penilaian" || $title == "Tambah Elemen Penilaian" || $title == "Edit Elemen Penilaian" ? "active" : "") ?>">
                                    <a href="<?= base_url('elemen-penilaian') ?>">
                                        <span class="sub-item">Elemen Kegiatan</span>
                                    </a>
                                </li>
                                <li class="<?= ($title == "Manajemen Kategori Kegiatan" || $title == "Tambah Kategori Kegiatan" || $title == "Edit Kategori Kegiatan" ? "active" : "") ?>">
                                    <a href="<?= base_url('kategori-kegiatan') ?>">
                                        <span class="sub-item">Kategori Kegiatan</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                <?php endif; ?>

                  <!-- KAPRODI -->
                <?php if (is_kaprodi() || is_kaplti()): ?>
                    <?php $is_pengaturan_active = in_array($title, [
                        "Manajemen Jenis Kegiatan",
                        "Tambah Jenis Kegiatan",
                        "Edit Jenis Kegiatan",
                        "Manajemen Elemen Penilaian",
                        "Tambah Elemen Penilaian",
                        "Edit Elemen Penilaian",
                        "Manajemen Kategori Kegiatan"
                    ]);
                    ?>
                    
                    <li class="nav-section">
                        <span class="sidebar-mini-icon">
                            <i class="fa fa-ellipsis-h"></i>
                        </span>
                        <h4 class="text-section">KAPRODI</h4>
                    </li>
                    <li class="nav-item <?= ($title == "Verifikasi Dokumen" || $title == "Pratinjau & Verifikasi" ? "active" : "") ?>">
                        <a href="<?= base_url("verifikasi-dokumen") ?>">
                            <i class="fas fa-check-double"></i>
                            <p>Verifikasi Dokumen</p>
                        </a>
                    </li>
                    <li class="nav-item <?= ($title == "Semua Kegiatan Mahasiswa" || $title == "Riwayat Mahasiswa" || $title == "Detail Pengajuan" ? "active" : "") ?>">
                        <a href="<?= base_url("semua-tkm") ?>">
                            <i class="fas fa-check-double"></i>
                            <p>Data Kegiatan</p>
                        </a>
                    </li>
                    <li class="nav-item <?= $is_pengaturan_active ? 'active submenu' : '' ?>">
                        <a data-bs-toggle="collapse"
                            href="#pengaturan_kegiatan"
                            aria-expanded="<?= $is_pengaturan_active ? 'true' : 'false' ?>"
                            class="<?= $is_pengaturan_active ? '' : 'collapsed' ?>">
                            <i class="fas fa-cogs"></i>
                            <p>Pengaturan</p>
                            <span class="caret"></span>
                        </a>

                        <div class="collapse <?= $is_pengaturan_active ? 'show' : '' ?>" id="pengaturan_kegiatan">
                            <ul class="nav nav-collapse">
                                <li class="<?= ($title == "Manajemen Jenis Kegiatan" || $title == "Tambah Jenis Kegiatan" || $title == "Edit Jenis Kegiatan" ? "active" : "") ?>">
                                    <a href="<?= base_url('jenis-kegiatan') ?>">
                                        <span class="sub-item">Jenis Kegiatan</span>
                                    </a>
                                </li>
                                <li class="<?= ($title == "Manajemen Elemen Penilaian" || $title == "Tambah Elemen Penilaian" || $title == "Edit Elemen Penilaian" ? "active" : "") ?>">
                                    <a href="<?= base_url('elemen-penilaian') ?>">
                                        <span class="sub-item">Elemen Kegiatan</span>
                                    </a>
                                </li>
                                <li class="<?= ($title == "Manajemen Kategori Kegiatan" || $title == "Tambah Kategori Kegiatan" || $title == "Edit Kategori Kegiatan" ? "active" : "") ?>">
                                    <a href="<?= base_url('kategori-kegiatan') ?>">
                                        <span class="sub-item">Kategori Kegiatan</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                <?php endif; ?>
                
                 <!-- WAREK III -->
                <?php if (is_warek() || is_kaplti()): ?>
                    <!-- WAREK III -->
                    <li class="nav-section">
                        <span class="sidebar-mini-icon">
                            <i class="fa fa-ellipsis-h"></i>
                        </span>
                        <h4 class="text-section">PIMPINAN</h4>
                    </li>
                    <li class="nav-item">
                        <a href="<?= base_url("verifikasi-dokumen") ?>">
                            <i class="fas fa-check-double"></i>
                            <p>Verifikasi Dokumen</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= base_url("semua-tkm") ?>">
                            <i class="fas fa-check-double"></i>
                            <p>Data Kegiatan</p>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</div>
<!-- End Sidebar -->