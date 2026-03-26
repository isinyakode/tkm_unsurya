<?php /** @var \CodeIgniter\View\View $this */ ?>
<?php $this->extend('Layout/index'); ?>
<?php $this->section('Dashboard'); ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
    <div>
        <h3 class="fw-bold mb-3"><?= esc($title); ?></h3>
        <h6 class="op-7 mb-2">Manajemen elemen penilaian untuk konfigurasi form pengajuan.</h6>
    </div>
    <div class="ms-md-auto py-2 py-md-0">
        <a href="<?= base_url("elemen-penilaian/create") ?>" type="button" class="btn btn-primary btn-round">
            <i class="fas fa-fw fa-plus"></i> Tambah elemen penilaian
        </a>
    </div>
</div>

<div class="card card-round">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="thead-light">
                    <tr>
                        <th class="text-center">No.</th>
                        <th>elemen penilaian</th>
                        <th class="text-center" width="150">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($elemenList)): ?>
                        <?php $no = 1;
                        foreach ($elemenList as $eL): ?>
                            <tr>
                                <td class="text-center"><?= $no++; ?></td>
                                <td><?= esc($eL['nama_elemen_penilaian']); ?></td>
                                <td class="text-center">
                                    <div class="d-flex gap-2 justify-content-center">
                                        <a href="<?= base_url("elemen-penilaian/edit/" . esc($eL['nama_elemen_penilaian'])) ?>"
                                            class="btn btn-sm btn-round btn-warning btn-edit-elemen">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <button type="button"
                                            class="btn btn-sm btn-round btn-danger btn-delete-data"
                                            data-nama="<?= esc($eL['nama_elemen_penilaian']); ?>"
                                            data-url="<?= base_url('elemen-penilaian/delete/' . $eL['id_elemen_penilaian']); ?>">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="text-center">Belum ada data elemen.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
    // Menyimpan data flash kedalam window agar terbaca oleh crud_pimpinan.js
    window.FLASH_SUCCESS = <?= json_encode(session()->getFlashdata('success')) ?>;
    window.FLASH_ERROR = <?= json_encode(session()->getFlashdata('error')) ?>;
</script>
<script src="<?= base_url('/assets/js/crud_pimpinan.js') ?>"></script>
<?php $this->endSection(); ?>