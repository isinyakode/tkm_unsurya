<?= $this->extend('Layout/index'); ?>
<?= $this->section('Dashboard'); ?>

<div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
    <div>
        <h3 class="fw-bold mb-3"><?= esc($title); ?></h3>
        <h6 class="op-7 mb-2">Manajemen jenis kegiatan untuk konfigurasi form pengajuan.</h6>
    </div>
    <div class="ms-md-auto py-2 py-md-0">
        <a href="<?= base_url('jenis-kegiatan/create'); ?>" class="btn btn-primary btn-round">
            <i class="fas fa-fw fa-plus"></i> Tambah Jenis Kegiatan
        </a>
    </div>
</div>

<div class="card card-round">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>#</th>
                        <th>Jenis Kegiatan</th>
                        <th>Peran Default</th>
                        <th>Color Icon</th>
                        <th>Show Anggota</th>
                        <th width="150">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (!empty($jenisList)): ?>
                        <?php $no = 1;
                        foreach ($jenisList as $jk): ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= esc($jk['jenis_kegiatan']); ?></td>
                                <td><?= esc($jk['peran_default']); ?></td>
                                <td>
                                    <?php $color = $jk['color_icon'] ?: 'secondary'; ?>
                                    <span class="badge bg-<?= esc($color); ?>">
                                        <?= esc($color); ?>
                                    </span>
                                </td>
                                <td><?= $jk['show_anggota'] ? 'Ya' : 'Tidak'; ?></td>
                                <td>
                                    <div class="row form-group mt-4">
                                        <div class="col">
                                            <div class="d-flex gap-2 justify-content-between">
                                                <a href="<?= base_url('jenis-kegiatan/edit/' . $jk['slug_jenis_kegiatan']); ?>" class="btn btn-warning btn-sm btn-round">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button"
                                                    class="btn btn-sm btn-danger btn-round btn-delete-jenis"
                                                    data-url="<?= base_url('jenis-kegiatan/delete/' . $jk['slug_jenis_kegiatan']); ?>"
                                                    data-nama="<?= esc($jk['jenis_kegiatan']); ?>">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center">Belum ada data jenis kegiatan.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Pastikan variabel didefinisikan sebagai null jika tidak ada session
    window.FLASH_SUCCESS = <?= session()->getFlashdata('success') ? json_encode(session()->getFlashdata('success')) : 'undefined' ?>;
    window.FLASH_ERROR = <?= session()->getFlashdata('error') ? json_encode(session()->getFlashdata('error')) : 'undefined' ?>;
    window.FLASH_VALIDATION = <?= session()->getFlashdata('validation') ? json_encode(session()->getFlashdata('validation')) : 'undefined' ?>;
</script>
<script src="<?= base_url('/assets/js/crud_pimpinan.js') ?>"></script>
<?= $this->endSection(); ?>