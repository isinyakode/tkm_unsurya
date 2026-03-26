<?php /** @var \CodeIgniter\View\View $this */ ?>
<?php $this->extend('Layout/index'); ?>
<?php $this->section('Dashboard'); ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
    <div>
        <h3 class="fw-bold mb-3"><?= esc($title); ?></h3>
        <h6 class="op-7 mb-2">Manajemen kategori kegiatan untuk konfigurasi form pengajuan.</h6>
    </div>
    <div class="ms-md-auto py-2 py-md-0">
        <button type="button" class="btn btn-primary btn-round" data-bs-toggle="modal" data-bs-target="#modalTambahKategori">
            <i class="fas fa-fw fa-plus"></i> Tambah Kategori Kegiatan
        </button>
    </div>
</div>

<div class="card card-round">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="thead-light">
                    <tr>
                        <th class="text-center">No.</th>
                        <th>Kategori Kegiatan</th>
                        <th class="text-center" width="150">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($kategoriList)): ?>
                        <?php $no = 1;
                        foreach ($kategoriList as $kL): ?>
                            <tr>
                                <td class="text-center"><?= $no++; ?></td>
                                <td><?= esc($kL['nama_kategori_kegiatan']); ?></td>
                                <td class="text-center">
                                    <div class="d-flex gap-2 justify-content-center">
                                        <button type="button"
                                            class="btn btn-sm btn-round btn-warning btn-edit-kategori"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalEditKategori"
                                            data-slug="<?= $kL['slug_kategori_kegiatan']; ?>"
                                            data-nama="<?= esc($kL['nama_kategori_kegiatan']); ?>">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        <button type="button"
                                            class="btn btn-sm btn-danger btn-delete-kategori btn-round"
                                            data-url="<?= base_url('kategori-kegiatan/delete/' . $kL['slug_kategori_kegiatan']); ?>"
                                            data-nama="<?= esc($kL['nama_kategori_kegiatan']); ?>">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="text-center">Belum ada data kategori.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTambahKategori" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Kategori Kegiatan Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url("kategori-kegiatan/save") ?>" method="post">
                <?= csrf_field(); ?>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Kategori Kegiatan</label>
                        <input type="text" name="nama_kategori_kegiatan" class="form-control" required placeholder="Contoh: Pengabdian Masyarakat">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditKategori" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Kategori Kegiatan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="" method="post" id="formEditKategori">
                <?= csrf_field(); ?>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Kategori Kegiatan</label>
                        <input type="text" name="nama_kategori_kegiatan" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    window.FLASH_SUCCESS = <?= json_encode(session()->getFlashdata('success')) ?>;
    window.FLASH_ERROR = <?= json_encode(session()->getFlashdata('error')) ?>;
</script>

<script src="<?= base_url('/assets/js/crud_pimpinan.js') ?>"></script>

<?php $this->endSection(); ?>