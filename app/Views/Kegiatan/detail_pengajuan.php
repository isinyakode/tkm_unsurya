<?php /** @var \CodeIgniter\View\View $this */ ?>
<?php $this->extend('Layout/index'); ?>
<?php $this->section('Dashboard'); ?>

<div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
    <div>
        <h3 class="fw-bold mb-3"><?= esc($title ?? 'Riwayat Pengajuan') . " : " . esc($jenis_pengajuan); ?></h3>
        <h6 class="op-7 mb-2">All Activities Recorded in the System</h6>
    </div>
    <div class="ms-md-auto py-2 py-md-0">
        <a href="<?= previous_url() ?>" class="btn btn-primary btn-round"><i class="fas fa-fw fa-chevron-left"></i> Kembali</a>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card card-round">
            <div class="card-header">
                <div class="card-head-row card-tools-still-right">
                    <div class="card-title">Nama Kegiatan : <?= $kegiatan['nama_kegiatan']; ?></div>
                    <div class="card-tools">
                        <span class="btn btn-round <?= $peran == 'Ketua' ? 'btn-primary' : 'btn-info' ?> fs-6">
                            <i class="fas <?= $peran == 'Ketua' ? 'fa-user-tie' : 'fa-users' ?>"></i> <?= $peran; ?>
                        </span>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex justify-content-between">
                            <div class="mb-3">
                                <small class="text-muted">
                                    Nama Pengaju : <?= $kegiatan['nama']; ?><br>
                                    Peran Pengaju : <?= $kegiatan['peran']; ?><br>
                                    Jenis Kegiatan : <?= $kegiatan['jenis_kegiatan']; ?><br>
                                    Kriteria Penilaian : <?= kriteria_penilaian($kriteria_penilaian); ?><br>
                                    Dibuat pada: <?= date('d M Y H:i', strtotime($kegiatan['created_at'])); ?><br>
                                    Update terakhir: <?= date('d M Y H:i', strtotime($kegiatan['updated_at'])); ?>
                                </small>
                            </div>
                            <?php if ($peran == 'Ketua' && $kegiatan['status_pengajuan'] != 'Ditolak' && $kegiatan['status_pengajuan'] != 'Disetujui' && $kegiatan['status_pengajuan'] != 'Diajukan' && $kegiatan['status_pengajuan'] != "Diverifikasi"): ?>
                                <div class="text-end">
                                    <a href="<?= base_url('edit-jenis-pengajuan/' . $kegiatan['slug_kegiatan_mahasiswa']) ?>" class="btn btn-round btn-warning">
                                        <i class="fas fw fa-edit"></i> Edit Pengajuan
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="p-3 border rounded">
                            <small class="text-muted">Semester Pelaporan</small>
                            <p class="mb-0 fw-bold">Semester <?= $kegiatan['semester']; ?></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 border rounded">
                            <small class="text-muted">Kontribusi Poin</small>
                            <p class="mb-0 fw-bold"><?= $kegiatan['total_kredit']; ?> Kredit</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card card-round">
            <div class="card-header">
                <div class="card-head-row card-tools-still-right">
                    <div class="card-title">Anggota</div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="tabelMahasiswa">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Peran</th>
                                        <th>NIM</th>
                                        <th>Nama</th>
                                        <th>Fakultas</th>
                                        <th>Prodi</th>
                                        <th>Jenjang</th>
                                        <th>Semester</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i = 1;
                                    foreach ($anggota as $agt): ?>
                                        <tr>
                                            <td><?= $i++; ?></td>
                                            <td><?= $agt['peran'] ?></td>
                                            <td><?= $agt['nim'] ?></td>
                                            <td><?= $agt['nama'] ?></td>
                                            <td><?= $agt['fakultas'] ?></td>
                                            <td><?= $agt['prodi'] ?></td>
                                            <td><?= $agt['jenjang'] ?></td>
                                            <td><?= $agt['semester'] ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card card-round">
            <div class="card-body">

                <div class="col-lg-12">
                    <div class="card card-round shadow-sm border-0 h-100">
                        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                            <div class="card-title text-white"><i class="fas fa-file-pdf me-2"></i> Pratinjau Dokumen</div>
                            <?php if ($kegiatan['file_laporan']): ?>
                                <a href="<?= base_url('uploads/tkm/' . $kegiatan['nim_pengaju'] . '/' . $kegiatan['slug_kegiatan_mahasiswa'] . '/' . $kegiatan['file_laporan']); ?>" target="_blank" class="btn btn-sm btn-light">
                                    <i class="fas fa-external-link-alt"></i> Layar Penuh
                                </a>
                            <?php endif; ?>
                        </div>
                        <div class="card-body p-0 bg-secondary">
                            <?php if ($kegiatan['file_laporan']):
                                $file_url = base_url('uploads/tkm/' . $kegiatan['nim_pengaju'] . '/' . $kegiatan['slug_kegiatan_mahasiswa'] . '/' . $kegiatan['file_laporan']);
                            ?>
                                <object data="<?= $file_url; ?>" type="application/pdf" width="100%" height="800px">
                                    <iframe src="<?= $file_url; ?>" width="100%" height="800px" style="border: none;">
                                        <div class="alert alert-warning m-3">
                                            Browser Anda tidak mendukung pratinjau PDF.
                                            <a href="<?= $file_url; ?>" class="fw-bold">Unduh Dokumen di sini.</a>
                                        </div>
                                    </iframe>
                                </object>
                            <?php else: ?>
                                <div class="h-100 d-flex align-items-center justify-content-center flex-column text-white opacity-50">
                                    <i class="fas fa-file-excel fa-5x mb-3"></i>
                                    <h5>Tidak ada file laporan yang diunggah.</h5>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <?php if ($peran == 'Ketua' && $kegiatan['status_pengajuan'] != 'Ditolak' && $kegiatan['status_pengajuan'] != 'Disetujui' && $kegiatan['status_pengajuan'] != 'Diajukan' && $kegiatan['status_pengajuan'] != "Diverifikasi"): ?>
                        <div class="mt-4">
                            <a href="<?= base_url('edit-jenis-pengajuan/' . $kegiatan['slug_kegiatan_mahasiswa']) ?>" class="btn btn-warning w-100">
                                <i class="fas fa-edit"></i> Edit Pengajuan
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <!-- Logs -->
        <div class="card card-round shadow-sm">
            <div class="card-header">
                <div class="card-title">Track Record Verifikasi</div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="py-2 small">Waktu</th>
                                <th class="py-2 small">Status</th>
                                <th class="py-2 small">Catatan</th>
                                <th class="py-2 small">Verifikator</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($logs): foreach ($logs as $l): ?>
                                    <tr>
                                        <td class="py-2 small text-nowrap"><?= date('d/m/y H:i', strtotime($l['created_at'])) ?></td>
                                        <td class="py-2">
                                            <?php echo get_status_badge($l['status_baru']); ?>
                                        </td>
                                        <td class="py-2 small text-muted italic"><?= esc($l['catatan_verifikator']) ?: '-' ?></td>
                                        <td class="py-2 small"><?= esc($l['verifikator']) ?: '-' ?></td>
                                    </tr>
                                <?php endforeach;
                            else: ?>
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted small">Belum ada riwayat verifikasi.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->endSection(); ?>