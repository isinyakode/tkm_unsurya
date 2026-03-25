<?= $this->extend('Layout/index'); ?>
<?= $this->section('Dashboard'); ?>

<div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
    <div>
        <h3 class="fw-bold mb-3"><?= esc($title); ?></h3>
        <h6 class="op-7 mb-2">Edit konfigurasi: <b><?= esc($jenis['jenis_kegiatan']); ?></b></h6>
    </div>
    <div class="ms-md-auto py-2 py-md-0">
        <a href="<?= base_url('jenis-kegiatan'); ?>" class="btn btn-secondary btn-round">
            <i class="fas fa-fw fa-chevron-left"></i> Kembali
        </a>
    </div>
</div>

<div class="card card-round">
    <div class="card-body">
        <form action="<?= base_url("jenis-kegiatan/update/" . $jenis['slug_jenis_kegiatan']) ?>" method="post">
            <?= csrf_field(); ?>
            <div class="row">

                <div class="col-md-12">
                    <div class="form-group">
                        <label class="form-label">Color Input</label>
                        <div class="row gutters-xs">
                            <?php
                            $colors = ['dark', 'primary', 'secondary', 'info', 'success', 'danger', 'warning'];
                            // Ambil warna dari DB atau Old Input
                            $savedColor = old('color_icon', $jenis['color_icon'] ?? 'dark');
                            ?>

                            <?php foreach ($colors as $color): ?>
                                <div class="col-auto">
                                    <label class="colorinput">
                                        <input name="color_icon" type="radio" value="<?= $color ?>" class="colorinput-input"
                                            <?= ($savedColor == $color) ? 'checked' : '' ?> />
                                        <span class="colorinput-color bg-<?= ($color == 'dark' ? 'black' : $color) ?>"></span>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="nama_jenis_kegiatan">Nama Jenis Kegiatan <span class="text-danger">*</span></label>
                        <input type="text"
                            class="form-control <?= isset($errors['nama_jenis_kegiatan']) ? 'is-invalid' : ''; ?>"
                            id="nama_jenis_kegiatan"
                            name="nama_jenis_kegiatan"
                            value="<?= old('nama_jenis_kegiatan', $jenis['jenis_kegiatan']); ?>">
                        <?php if (isset($errors['nama_jenis_kegiatan'])): ?>
                            <div class="invalid-feedback"><?= esc($errors['nama_jenis_kegiatan']); ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="peran_mode">Mode Peran <span class="text-danger">*</span></label>
                        <select class="form-control <?= isset($errors['peran_mode']) ? 'is-invalid' : ''; ?>"
                            id="peran_mode"
                            name="peran_mode">
                            <?php
                            $selectedPeranMode = old('peran_mode', $jenis['peran_mode']);
                            ?>
                            <option value="none" <?= $selectedPeranMode === 'none' ? 'selected' : ''; ?>>Tidak ada field Peran</option>
                            <option value="fixed" <?= $selectedPeranMode === 'fixed' ? 'selected' : ''; ?>>Peran fixed (text)</option>
                            <option value="select_peran" <?= $selectedPeranMode === 'select_peran' ? 'selected' : ''; ?>>Dropdown Peran</option>
                            <option value="select_prestasi" <?= $selectedPeranMode === 'select_prestasi' ? 'selected' : ''; ?>>Dropdown Prestasi</option>
                        </select>
                        <?php if (isset($errors['peran_mode'])): ?>
                            <div class="invalid-feedback"><?= esc($errors['peran_mode']); ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="peran_default">Peran Default (untuk mode fixed)</label>
                        <input type="text"
                            class="form-control"
                            id="peran_default"
                            name="peran_default"
                            placeholder="Contoh: PESERTA atau KETUA"
                            value="<?= old('peran_default', $jenis['peran_default']); ?>">
                        <small class="form-text text-muted">Diabaikan jika Mode Peran bukan <b>fixed</b>.</small>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="label_jenis">Label Field "Jenis"</label>
                        <input type="text"
                            class="form-control"
                            id="label_jenis"
                            name="label_jenis"
                            placeholder="Contoh: Jenis Publikasi Ilmiah"
                            value="<?= old('label_jenis', $jenis['label_jenis']); ?>">
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group form-check">
                        <?php $checkedTanggal = old('show_tanggal', $jenis['show_tanggal']); ?>
                        <input type="checkbox" class="form-check-input" id="show_tanggal" name="show_tanggal" value="1"
                            <?= $checkedTanggal ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="show_tanggal">
                            Tampilkan Tanggal Mulai & Tanggal Selesai<br>
                        </label>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group form-check">
                        <?php $checkedAnggota = old('show_anggota', $jenis['show_anggota']); ?>
                        <input type="checkbox" class="form-check-input" id="show_anggota" name="show_anggota" value="1"
                            <?= $checkedAnggota ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="show_anggota">
                            Tampilkan Daftar Anggota (tabel & modal)
                        </label>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group form-check">
                        <?php $checkedLokasi = old('show_lokasi', $jenis['show_lokasi']); ?>
                        <input type="checkbox" class="form-check-input" id="show_lokasi" name="show_lokasi" value="1"
                            <?= $checkedLokasi ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="show_lokasi">
                            Tampilkan Lokasi Kegiatan
                        </label>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group form-check">
                        <?php $checkedDeskripsi = old('show_deskripsi', $jenis['show_deskripsi']); ?>
                        <input type="checkbox" class="form-check-input" id="show_deskripsi" name="show_deskripsi" value="1"
                            <?= $checkedDeskripsi ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="show_deskripsi">
                            Tampilkan Deskripsi Kegiatan
                        </label>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group">
                        <label for="deskripsi_jenis_kegiatan">Deskripsi Jenis Kegiatan</label>
                        <textarea class="form-control" id="deskripsi_jenis_kegiatan" name="deskripsi_jenis_kegiatan" rows="3"><?= old('deskripsi_jenis_kegiatan', $jenis['deskripsi_jenis_kegiatan']); ?></textarea>
                    </div>
                </div>

                <div class="mt-3 mb-2">
                    <div class="row form-group mt-4">
                        <div class="col-sm-12">
                            <div class="d-flex gap-2 justify-content-between">
                                <h3><b>Daftar Penilaian</b> <span class="text-danger">*</span></h3>
                                <button type="button" class="btn btn-primary btn-rounded" data-bs-toggle="modal" data-bs-target="#modalElemenKategori">
                                    <i class="fas fa-fw fa-plus"></i> Tambah Penilaian
                                </button>
                            </div>
                        </div>
                        <div class="col-sm-12 mt-3">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="ElemenKategori">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Elemen Penilaian</th>
                                            <th>Nama Kategori Penilaian</th>
                                            <th>Kredit Score</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Loop data elemen kategori yang diambil dari tabel kredit_penilaian -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group mt-4 text-end">
                        <button type="submit" class="btn btn-success btn-rounded">
                            <i class="fas fa-save"></i> Simpan Perubahan
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modalElemenKategori" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Tambah Item Penilaian</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group mb-3">
                    <label class="form-label">Elemen Penilaian</label>
                    <select id="input_id_elemen" class="form-select form-control">
                        <option value="">-- Pilih Elemen --</option>
                        <?php foreach ($ElemenList as $el): ?>
                            <option value="<?= $el['id_elemen_penilaian'] ?>" data-nama="<?= $el['nama_elemen_penilaian'] ?>">
                                <?= $el['nama_elemen_penilaian'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group mb-3">
                    <label class="form-label">Kategori Kegiatan</label>
                    <select id="input_id_kategori" class="form-select form-control" disabled>
                        <option value="">-- Pilih Elemen Terlebih Dahulu --</option>
                    </select>
                </div>

                <div class="form-group mb-3">
                    <label class="form-label">Bobot / Kredit Score</label>
                    <input type="number" id="input_kredit" class="form-control" placeholder="Contoh: 10" min="0" step="0.1">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btnSimpanPenilaian">Tambahkan</button>
            </div>
        </div>
    </div>
</div>
<script>
    // Inject data dari PHP ke Global Variable JS
    window.RELASI_ELEMEN_KATEGORI = <?= json_encode($relasi); ?>;

    // Data awal untuk mengisi tabel saat halaman edit dibuka
    const initialData = <?= json_encode($itemSaved); ?>;

    document.addEventListener('DOMContentLoaded', function() {
        if (initialData.length > 0) {
            window.penilaianData = initialData;
            window.renderTable();
        }
    });
</script>
<script src="<?= base_url('/assets/js/crud_pimpinan.js') ?>"></script>
<?= $this->endSection(); ?>