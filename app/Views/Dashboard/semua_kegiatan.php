<?php /** @var \CodeIgniter\View\View $this */ ?>
<?php $this->extend('Layout/index'); ?>
<?php $this->section('Dashboard'); ?>
<div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
    <div>
        <h3 class="fw-bold mb-3"><?= esc($title); ?></h3>
        <h6 class="op-7 mb-2">All Activities Recorded in the System</h6>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card card-round">
            <div class="card-header">
                <div class="card-head-row card-tools-still-right">
                    <div class="card-title">Semua Data Pengajuan</div>
                    <div class="card-tools">
                        <button id="btnBulkVerify" class="btn btn-success btn-round btn-sm" style="display:none;">
                            <i class="fas fa-check"></i> Setujui (<span id="countSelected">0</span>)
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="allkegiatan" class="display table table-striped table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th scope="col">No.</th>
                                <th scope="col" class="text-start">NIM</th>
                                <th scope="col" class="text-start">Nama Mahasiswa</th>
                                <th scope="col" class="text-start">Fakultas</th>
                                <th scope="col" class="text-start">Prodi</th>
                                <th scope="col" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1;
                            foreach ($kegiatan as $k): ?>
                                <tr>
                                    <th scope="row"><?= $i++; ?></th>
                                    <td class="text-start"><?= esc($k['nim']); ?></td>
                                    <td class="text-start"><?= esc($k['nama']); ?></td>
                                    <td class="text-start"><?= esc($k['fakultas']); ?></td>
                                    <td class="text-start"><?= esc($k['prodi']); ?></td>
                                    <td class="text-center">
                                        <a href="<?= base_url("riwayat-mahasiswa/" . $k['nim']) ?>" class="btn btn-success btn-sm btn-round" title="Lihat">
                                                    <i class="fas fa-fw fa-eye"></i>
                                                </a>
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
<?php $this->endSection() ?>