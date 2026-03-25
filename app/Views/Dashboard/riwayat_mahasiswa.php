<?= $this->extend('Layout/index'); ?>
<?= $this->section('Dashboard'); ?>
<div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
    <div>
        <h3 class="fw-bold mb-3"><?= $title; ?></h3>
        <h6 class="op-7 mb-2">NIM : <?= $mhs['nim'] ?></h6>
        <h6 class="op-7 mb-2">Nama : <?= $mhs['nama'] ?></h6>
        <h6 class="op-7 mb-2">Fakultas : <?= $mhs['fakultas'] ?></h6>
        <h6 class="op-7 mb-2">Prodi : <?= $mhs['prodi'] ?></h6>
        <h6 class="op-7 mb-2">Total Poin : <?= $total_poin ?></h6>
        <h6 class="op-7 mb-2">Predikat : <?= $predikat ?></h6>
    </div>
    <div class="ms-md-auto py-2 py-md-0">
        <a href="<?= base_url('/semua-tkm') ?>" class="btn btn-primary btn-round"><i class="fas fa-fw fa-chevron-left"></i> Kembali</a>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card card-round">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table id="basic-datatables" class="display table table-striped table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th scope="col">No.</th>
                                <th scope="col" class="text-start">Judul Kegiatan</th>
                                <th scope="col" class="text-center">Status</th>
                                <th scope="col" class="text-center">Poin</th>
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
                                        <span class="badge badge-<?= $k['status_pengajuan'] == "Disetujui" ? "success" : "warning"; ?>"><?= esc($k['status_pengajuan'] ?? 'Completed'); ?></span>
                                    </td>
                                    <td class="text-center"><?= date('d M Y H:i', strtotime($k['created_at'])); ?></td>
                                    <td class="text-center"><?= $k['total_kredit']; ?></td>
                                    <td class="text-center">
                                        <a href="<?= base_url("detail-pengajuan/" . $k['nim']."/".$k['slug_kegiatan_mahasiswa']) ?>" class="btn btn-success btn-sm btn-round" title="Lihat">
                                                    <i class="fas fa-fw fa-eye"></i>
                                                </a>

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