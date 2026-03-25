<?= $this->extend('Layout/index'); ?>
<?= $this->section('Dashboard'); ?>

<div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
    <div>
        <h3 class="fw-bold mb-3"><?= esc($title); ?></h3>
        <h6 class="op-7 mb-2">Edit konfigurasi untuk elemen penilaian: <b><?= esc($ElemenList['nama_elemen_penilaian']); ?></b></h6>
    </div>
    <div class="ms-md-auto py-2 py-md-0">
        <a href="<?= base_url('elemen-penilaian'); ?>" class="btn btn-secondary btn-round">
            <i class="fas fa-fw fa-chevron-left"></i> Kembali
        </a>
    </div>
</div>

<div class="card card-round">
    <div class="card-body">
        <form action="<?= base_url("elemen-penilaian/update/" . $ElemenList['id_elemen_penilaian']) ?>" method="post">
            <?= csrf_field(); ?>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Nama Elemen Penilaian <span class="text-danger">*</span></label>
                        <input type="text"
                            class="form-control <?= isset($errors['nama_elemen_penilaian']) ? 'is-invalid' : ''; ?>"
                            name="nama_elemen_penilaian"
                            value="<?= old('nama_elemen_penilaian', $ElemenList['nama_elemen_penilaian']); ?>">
                        <?php if (isset($errors['nama_elemen_penilaian'])): ?>
                            <div class="invalid-feedback"><?= esc($errors['nama_elemen_penilaian']); ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="col-sm-12 mt-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h3><b>Daftar Kategori Kegiatan</b> <span class="text-danger">*</span></h3>
                        <button type="button" class="btn btn-primary btn-rounded" data-bs-toggle="modal" data-bs-target="#modalElemenKategori">
                            <i class="fas fa-plus"></i> Tambah Kategori
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered" id="ElemenKategori">
                            <thead>
                                <tr>
                                    <th style="width: 80px;" class="text-center">No</th>
                                    <th>Nama Kategori Penilaian</th>
                                    <th style="width: 100px;" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="col-md-12 mt-4">
                    <hr>
                    <button type="submit" class="btn btn-success btn-rounded">
                        <i class="fas fa-save"></i> Perbarui Data Elemen
                    </button>
                    <a href="<?= base_url('elemen-penilaian'); ?>" class="btn btn-secondary btn-rounded">Batal</a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modalElemenKategori" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Kategori ke Elemen</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Pilih Kategori Kegiatan</label>
                            <select id="input_id_kategori" class="form-control">
                                <option value="">-- Pilih Kategori --</option>
                                <?php foreach ($KategoriList as $KL) : ?>
                                    <option value="<?= $KL['id_kategori_kegiatan']; ?>" data-nama="<?= $KL['nama_kategori_kegiatan']; ?>">
                                        <?= $KL['nama_kategori_kegiatan']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" id="btnSimpanMahasiswa">
                    <i class="fas fa-plus"></i> Tambahkan ke Daftar
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    window.addEventListener('load', function() {

        // Ambil data PHP dan masukkan ke variabel JS
        // Ini adalah cara "melooping" $KategoriElemen ke dalam array JavaScript
        const initialData = <?= json_encode($KategoriElemen) ?>;
        if (initialData && initialData.length > 0) {
            // Gunakan fungsi yang sudah kita buat di JS
            window.setInitialData(initialData);
        } else {
            window.renderTable(); // Tetap panggil untuk memunculkan pesan "Belum ada data"
        }
    });
</script>

<script src="<?= base_url('/assets/js/crud_pimpinan.js') ?>"></script>

<?= $this->endSection(); ?>