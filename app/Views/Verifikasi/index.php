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
                                <th>No.</th>
                                <th>NIM</th>
                                <th>Nama</th>
                                <th>Judul Kegiatan</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i=1; foreach ($kegiatan as $k): ?>
                                <tr>
                                    <td><?= $i++;?></td>
                                    <td><?= $k['nim'];?></td>
                                    <td><?=$k['nama'];?></td>
                                    <td><?= esc($k['nama_kegiatan']); ?></td>
                                    <td><span class="badge badge-info"><?= esc($k['status_pengajuan']); ?></span></td>
                                    <td><?= date('d M Y H:i', strtotime($k['created_at'])); ?></td>
                                    <td>
                                        <a href="<?= base_url('verifikasi-dokumen/' . $k['slug_kegiatan_mahasiswa'].'/'.$k['nim']) ?>" class="btn btn-primary btn-sm">Lihat</a>
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
    window.BASE_URL = '<?= base_url() ?>';
    window.FLASH_SUCCESS = <?= json_encode(session()->getFlashdata('success') ?: null) ?>;
    window.FLASH_ERROR = <?= json_encode(session()->getFlashdata('error') ?: null) ?>;
    window.FLASH_VALIDATION = <?= json_encode(session()->getFlashdata('errors') ?: null) ?>;
</script>
<script src="<?= base_url('/assets/js/kepanitiaan.js') ?>"></script>
<?php $this->endSection() ?>