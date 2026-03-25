<?= $this->extend('Layout/index'); ?>
<?= $this->section('Dashboard'); ?>
<div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
    <div>
        <h3 class="fw-bold mb-3"><?= $title; ?></h3>
        <h6 class="op-7 mb-2">All Activities Recorded in the System</h6>
    </div>
    <div class="ms-md-auto py-2 py-md-0">
        <a href="<?= base_url('pengajuan-kegiatan') ?>" class="btn btn-primary btn-round"><i class="fas fa-fw fa-chevron-left"></i> Kembali</a>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card card-round">
            <div class="card-body p-0">
                <div class="row p-3">
                    <?php foreach ($jenis_kegiatan as $jk): ?>
                        <a href="<?= base_url('jenis-pengajuan/' . $jk['slug_jenis_kegiatan']) ?>" class="col-sm-6 col-md-3">
                            <div class="card card-stats card-round">
                                <div class="card-body ">
                                    <div class="row align-items-center">
                                        <div class="col-icon">
                                            <div class="icon-big text-center icon-<?= $jk['color_icon']; ?> bubble-shadow-small">
                                                <i class="fas fa-users"></i>
                                            </div>
                                        </div>
                                        <div class="col col-stats ms-3 ms-sm-0">
                                            <div class="numbers">
                                                <p class="card-category"><b class="text-bold text-dark"><?= $jk['jenis_kegiatan']; ?></b></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>