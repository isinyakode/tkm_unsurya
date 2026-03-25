<?= $this->extend('Layout/index'); ?>
<?= $this->section('Dashboard'); ?>
<div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
    <div>
        <h3 class="fw-bold mb-3"><?= $title; ?></h3>
        <h6 class="op-7 mb-2">All Activities Recorded in the System</h6>
    </div>
    <div class="ms-md-auto py-2 py-md-0">
        <a href="<?= base_url('jenis-pengajuan') ?>" class="btn btn-primary btn-round"><i class="fas fa-fw fa-plus"></i> Ajukan Kegiatan</a>
    </div>
</div>
<div class="row">
    <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-round">
            <div class="card-body ">
                <div class="row align-items-center">
                    <div class="col-icon">
                        <div class="icon-big text-center icon-primary bubble-shadow-small">
                            <i class="fas fa-user-check"></i>
                        </div>
                    </div>
                    <div class="col col-stats ms-3 ms-sm-0">
                        <div class="numbers">
                            <p class="card-category">Score Kegiatan</p>
                            <h4 class="card-title"><?= $score_kegiatan ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-round">
            <div class="card-body ">
                <div class="row align-items-center">
                    <div class="col-icon">
                        <div class="icon-big text-center icon-primary bubble-shadow-small">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                    <div class="col col-stats ms-3 ms-sm-0">
                        <div class="numbers">
                            <p class="card-category">Total Kegiatan</p>
                            <h4 class="card-title"><?= $total_kegiatan ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-round">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-icon">
                        <div class="icon-big text-center icon-info bubble-shadow-small">
                            <i class="fas fa-file-alt"></i>
                        </div>
                    </div>
                    <div class="col col-stats ms-3 ms-sm-0">
                        <div class="numbers">
                            <p class="card-category">Diajukan</p>
                            <h4 class="card-title"><?= $proses_kegiatan ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-round">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-icon">
                        <div class="icon-big text-center icon-warning bubble-shadow-small">
                            <i class="fas fa-sync"></i>
                        </div>
                    </div>
                    <div class="col col-stats ms-3 ms-sm-0">
                        <div class="numbers">
                            <p class="card-category">Revisi</p>
                            <h4 class="card-title"><?= $revisi_kegiatan ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-round">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-icon">
                        <div class="icon-big text-center icon-danger bubble-shadow-small">
                            <i class="fas fa-times"></i>
                        </div>
                    </div>
                    <div class="col col-stats ms-3 ms-sm-0">
                        <div class="numbers">
                            <p class="card-category">Ditolak</p>
                            <h4 class="card-title"><?= $tolak_kegiatan ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-round">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-icon">
                        <div class="icon-big text-center icon-success bubble-shadow-small">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                    <div class="col col-stats ms-3 ms-sm-0">
                        <div class="numbers">
                            <p class="card-category">Diverifikasi</p>
                            <h4 class="card-title"><?= $lolos_kegiatan ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card card-round">
            <div class="card-header">
                <div class="card-head-row card-tools-still-right">
                    <div class="card-title">Riwayat Pengajuan</div>
                    <div class="card-tools">
                        <div class="dropdown">
                            <a href="<?= base_url("Cetak-Kegiatan") ?>" class="btn btn-secondary btn-round"><i class="fas fa-fw fa-print"></i> Cetak</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table id="basic-datatables" class="display table table-striped table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th scope="col">No.</th>
                                <th scope="col" class="text-start">Judul Kegiatan</th>
                                <th scope="col" class="text-center">Status</th>
                                <th scope="col" class="text-center">Tanggal Pengajuan</th>
                                <th scope="col" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1;
                            foreach ($kegiatan as $k): ?>
                                <tr>
                                    <th scope="row"><?= $i++; ?></th>
                                    <td class="text-start"><?= esc($k['nama_kegiatan']); ?></td>
                                    <td class="text-center">
                                        <span class="badge badge-<?= $k['status_pengajuan'] == "Disetujui" ? "success" : ($k['status_pengajuan'] == "Ditolak" ? "danger" : "warning"); ?>">
                                            <?= esc($k['status_pengajuan'] ?? 'Completed'); ?>
                                        </span>
                                    </td>
                                    <td class="text-center"><?= date('d M Y H:i', strtotime($k['created_at'])); ?></td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <?php if ($k['status_pengajuan'] === "Ditolak" || $k['status_pengajuan'] === "Diajukan" || $k['status_pengajuan'] === "Disetujui" || $k['status_pengajuan'] === "Diverifikasi"): ?>
                                                <a href="<?= base_url("detail-pengajuan/" . $k['nim'] . "/" . $k['slug_kegiatan_mahasiswa']) ?>" class="btn btn-success btn-sm btn-round" title="Lihat">
                                                    <i class="fas fa-fw fa-eye"></i>
                                                </a>
                                            <?php else: ?>
                                                <a href="<?= base_url('edit-jenis-pengajuan/' . $k['slug_kegiatan_mahasiswa']) ?>" class="btn btn-warning btn-sm btn-round" title="Edit">
                                                    <i class="fas fa-fw fa-pen"></i>
                                                </a>
                                                <button class="btn btn-danger btn-md btn-round btn-delete-kegiatan" data-slug="<?= esc($k['slug_kegiatan_mahasiswa'] ?? $k['slug_kegiatan_mahasiswa'] ?? '') ?>">
                                                    <i class="fas fa-fw fa-trash"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    // Variable global untuk kepanitiaan.js
    window.BASE_URL = "<?= base_url() ?>";
    window.FLASH_ERROR = <?= json_encode(session()->getFlashdata('error') ?: null) ?>;
</script>
<script src="<?= base_url('/assets/js/kepanitiaan.js') ?>"></script>
<?= $this->endSection() ?>