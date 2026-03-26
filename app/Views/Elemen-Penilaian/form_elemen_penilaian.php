<?php /** @var \CodeIgniter\View\View $this */ ?>
<?php $this->extend('Layout/index'); ?>
<?php $this->section('Dashboard'); ?>

<div class="page-inner">
    <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
        <div>
            <h3 class="fw-bold mb-3">Tambah Elemen Penilaian</h3>
            <h6 class="op-7 mb-2">Konfigurasi elemen baru beserta kategori kegiatannya.</h6>
        </div>
    </div>

    <form action="<?= base_url('elemen-penilaian/save'); ?>" method="post">
        <?= csrf_field(); ?>
        <div class="row">
            <div class="col-md-12">
                <div class="card card-round">
                    <div class="card-header">
                        <div class="card-title">Informasi Utama</div>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="nama_elemen_penilaian">Nama Elemen Penilaian</label>
                            <input type="text"
                                name="nama_elemen_penilaian"
                                id="nama_elemen_penilaian"
                                class="form-control <?= (session('errors.nama_elemen_penilaian')) ? 'is-invalid' : ''; ?>"
                                placeholder="Contoh: Peran, Tingkat, atau Prestasi"
                                value="<?= old('nama_elemen_penilaian'); ?>" required>
                            <div class="invalid-feedback">
                                <?= session('errors.nama_elemen_penilaian'); ?>
                            </div>
                        </div>

                        <div class="separator-solid"></div>

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="fw-bold">Daftar Kategori</h4>
                            <button type="button" class="btn btn-primary btn-sm btn-round" data-bs-toggle="modal" data-bs-target="#modalElemenKategori">
                                <i class="fas fa-plus"></i> Pilih Kategori
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="ElemenKategori">
                                <thead class="thead-light">
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
                    <div class="card-footer text-end">
                        <a href="<?= base_url('elemen-penilaian'); ?>" class="btn btn-danger btn-round">Batal</a>
                        <button type="submit" class="btn btn-success btn-round">Simpan Elemen</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<div class="modal fade" id="modalElemenKategori" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Kategori ke Elemen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Pilih Kategori Kegiatan</label>
                    <select id="input_id_kategori" class="form-control">
                        <option value="">-- Pilih Kategori --</option>
                        <?php foreach ($allKategori as $kat) : ?>
                            <option value="<?= $kat['id_kategori_kegiatan']; ?>" data-nama="<?= $kat['nama_kategori_kegiatan']; ?>">
                                <?= $kat['nama_kategori_kegiatan']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" id="btnSimpanKategori" class="btn btn-primary">Tambahkan</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Inisialisasi tabel kosong saat halaman dimuat
    window.addEventListener('load', function() {
        window.renderTable();
    });
</script>

<script src="<?= base_url('/assets/js/crud_pimpinan.js') ?>"></script>

<?php $this->endSection(); ?>