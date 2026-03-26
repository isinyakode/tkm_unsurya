<?php /** @var \CodeIgniter\View\View $this */ ?>
<?php $this->extend('Layout/index'); ?>
<?php $this->section('Dashboard'); ?>

<div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
    <div>
        <h3 class="fw-bold mb-3"><?= esc($title ?? 'Riwayat Pengajuan'); ?></h3>
        <h6 class="op-7 mb-2">All Activities Recorded in the System</h6>
    </div>
    <div class="ms-md-auto py-2 py-md-0">
        <a href="<?= base_url('jenis-pengajuan') ?>" class="btn btn-primary btn-round"><i class="fas fa-fw fa-plus"></i> Ajukan Kegiatan</a>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card card-round">
            <div class="card-header">
                <div class="card-head-row card-tools-still-right">
                    <div class="card-title">Riwayat Pengajuan</div>
                    <div class="card-tools">
                        <a href="<?= base_url("Cetak-Kegiatan") ?>" class="btn btn-secondary btn-round"><i class="fas fa-fw fa-print"></i> Cetak</a>
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
                                        <span class="badge badge-<?= ($k['status_pengajuan'] == "Disetujui" ? "success" : ($k['status_pengajuan'] == "Ditolak" ? "danger" : "warning")); ?>">
                                            <?= esc($k['status_pengajuan'] ?? 'Pending'); ?>
                                        </span>
                                    </td>
                                    <td class="text-center"><?= date('d M Y H:i', strtotime($k['created_at'])); ?></td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <?php if ($k['status_pengajuan'] === "Diajukan" || $k['status_pengajuan'] === "Disetujui" || $k['status_pengajuan'] === "Diverifikasi" || $k['status_pengajuan'] === "Ditolak"): ?>
                                                <a href="<?= base_url("detail-pengajuan/" . $k['nim'] . "/" . $k['slug_kegiatan_mahasiswa']) ?>" class="btn btn-success btn-sm btn-round" title="Lihat">
                                                    <i class="fas fa-fw fa-eye"></i>
                                                </a>
                                            <?php else: ?>
                                                <a href="<?= base_url('edit-jenis-pengajuan/' . $k['slug_kegiatan_mahasiswa']) ?>" class="btn btn-warning btn-sm btn-round" title="Edit">
                                                    <i class="fas fa-fw fa-pen"></i>
                                                </a>
                                                <button class="btn btn-danger btn-md btn-round btn-delete-kegiatan" data-nim="<?= esc($k['nim_pengaju'] ?? '') ?>" data-slug="<?= esc($k['slug_kegiatan_mahasiswa'] ?? '') ?>">
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
    window.FLASH_SUCCESS = <?= json_encode(session()->getFlashdata('success') ?: null) ?>;
    window.FLASH_ERROR = <?= json_encode(session()->getFlashdata('error') ?: null) ?>;
    window.FLASH_VALIDATION = <?= json_encode(session()->getFlashdata('errors') ?: null) ?>;
</script>
<script src="<?= base_url('/assets/js/kepanitiaan.js') ?>"></script>
<?php $this->endSection() ?>