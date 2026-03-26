<?php /** @var \CodeIgniter\View\View $this */ ?>
<?php $this->extend('Layout/index'); ?>
<?php $this->section('Dashboard'); ?>

<div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
    <div>
        <h3 class="fw-bold mb-3"><?= esc($title); ?></h3>
        <h6 class="op-7 mb-2">Atur konfigurasi jenis kegiatan untuk form pengajuan.</h6>
    </div>
    <div class="ms-md-auto py-2 py-md-0">
        <a href="<?= base_url('jenis-kegiatan'); ?>" class="btn btn-secondary btn-round">
            <i class="fas fa-fw fa-chevron-left"></i> Kembali
        </a>
    </div>
</div>

<div class="card card-round">
    <div class="card-body">
        <form action="<?= base_url("jenis-kegiatan/store") ?>" method="post">
            <?= csrf_field(); ?>
            <div class="row">
                <!-- Nama Jenis Kegiatan -->
                <!-- Color Icon (Bootstrap Color) -->
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="form-label">Color Input</label>
                        <div class="row gutters-xs">

                            <div class="col-auto">
                                <label class="colorinput">
                                    <input
                                        name="color_icon"
                                        type="radio"
                                        value="dark"
                                        class="colorinput-input" checked />
                                    <span class="colorinput-color bg-black"></span>
                                </label>
                            </div>

                            <div class="col-auto">
                                <label class="colorinput">
                                    <input
                                        name="color_icon"
                                        type="radio"
                                        value="primary"
                                        class="colorinput-input" />
                                    <span class="colorinput-color bg-primary"></span>
                                </label>
                            </div>

                            <div class="col-auto">
                                <label class="colorinput">
                                    <input
                                        name="color_icon"
                                        type="radio"
                                        value="secondary"
                                        class="colorinput-input" />
                                    <span class="colorinput-color bg-secondary"></span>
                                </label>
                            </div>

                            <div class="col-auto">
                                <label class="colorinput">
                                    <input
                                        name="color_icon"
                                        type="radio"
                                        value="info"
                                        class="colorinput-input" />
                                    <span class="colorinput-color bg-info"></span>
                                </label>
                            </div>

                            <div class="col-auto">
                                <label class="colorinput">
                                    <input
                                        name="color_icon"
                                        type="radio"
                                        value="success"
                                        class="colorinput-input" />
                                    <span class="colorinput-color bg-success"></span>
                                </label>
                            </div>

                            <div class="col-auto">
                                <label class="colorinput">
                                    <input
                                        name="color_icon"
                                        type="radio"
                                        value="danger"
                                        class="colorinput-input" />
                                    <span class="colorinput-color bg-danger"></span>
                                </label>
                            </div>

                            <div class="col-auto">
                                <label class="colorinput">
                                    <input
                                        name="color_icon"
                                        type="radio"
                                        value="warning"
                                        class="colorinput-input" />
                                    <span class="colorinput-color bg-warning"></span>
                                </label>
                            </div>
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
                            value="<?= old('nama_jenis_kegiatan', $jenis['nama_jenis_kegiatan'] ?? ''); ?>">
                        <?php if (isset($errors['nama_jenis_kegiatan'])): ?>
                            <div class="invalid-feedback">
                                <?= esc($errors['nama_jenis_kegiatan']); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Peran Mode -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="peran_mode">Mode Peran <span class="text-danger">*</span></label>
                        <select class="form-control <?= isset($errors['peran_mode']) ? 'is-invalid' : ''; ?>"
                            id="peran_mode"
                            name="peran_mode">
                            <?php
                            $selectedPeranMode = old('peran_mode', $jenis['peran_mode'] ?? 'fixed');
                            ?>
                            <option value="none" <?= $selectedPeranMode === 'none' ? 'selected' : ''; ?>>Tidak ada field Peran</option>
                            <option value="fixed" <?= $selectedPeranMode === 'fixed' ? 'selected' : ''; ?>>Peran fixed (text)</option>
                            <option value="select_peran" <?= $selectedPeranMode === 'select_peran' ? 'selected' : ''; ?>>Dropdown Peran</option>
                            <option value="select_prestasi" <?= $selectedPeranMode === 'select_prestasi' ? 'selected' : ''; ?>>Dropdown Prestasi</option>
                        </select>
                        <?php if (isset($errors['peran_mode'])): ?>
                            <div class="invalid-feedback">
                                <?= esc($errors['peran_mode']); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Peran Default -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="peran_default">Peran Default (untuk mode fixed)</label>
                        <input type="text"
                            class="form-control"
                            id="peran_default"
                            name="peran_default"
                            placeholder="Contoh: PESERTA atau KETUA"
                            value="<?= old('peran_default', $jenis['peran_default'] ?? ''); ?>">
                        <small class="form-text text-muted">
                            Diabaikan jika Mode Peran bukan <b>fixed</b>.
                        </small>
                    </div>
                </div>

                <!-- Label Jenis -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="label_jenis">Label Field "Jenis"</label>
                        <input type="text"
                            class="form-control"
                            id="label_jenis"
                            name="label_jenis"
                            placeholder="Contoh: Jenis Publikasi Ilmiah, Jenis Rekognisi, dll."
                            value="<?= old('label_jenis', $jenis['label_jenis'] ?? ''); ?>">
                        <small class="form-text text-muted">
                            Jika diisi, form akan menampilkan dropdown untuk <b>Jenis</b> dengan label ini.
                        </small>
                    </div>
                </div>

                <!-- Show Tanggal -->
                <div class="col-md-6">
                    <div class="form-group form-check">
                        <?php
                        $checkedTanggal = old('show_tanggal', $jenis['show_tanggal'] ?? 1);
                        ?>
                        <input type="checkbox"
                            class="form-check-input"
                            id="show_tanggal"
                            name="show_tanggal"
                            value="1"
                            <?= $checkedTanggal ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="show_tanggal">
                            Tampilkan Tanggal Mulai & Tanggal Selesai<br>
                            <small class="form-text text-muted">
                                <span class="text-danger">*</span> Centang jika sifat pengajuan rentang waktu
                            </small>
                        </label>
                    </div>
                </div>

                <!-- Show Anggota -->
                <div class="col-md-6">
                    <div class="form-group form-check">
                        <?php
                        $checkedAnggota = old('show_anggota', $jenis['show_anggota'] ?? 1);
                        ?>
                        <input type="checkbox"
                            class="form-check-input"
                            id="show_anggota"
                            name="show_anggota"
                            value="1"
                            <?= $checkedAnggota ? 'checked' : ''; ?>>

                        <label class="form-check-label" for="show_anggota">
                            Tampilkan Daftar Anggota (tabel & modal) <br>
                            <small class="form-text text-muted">
                                <span class="text-danger">*</span> Centang jika sifat pengajuan memiliki Anggota
                            </small>
                        </label>
                    </div>
                </div>

                <!-- Show Lokasi -->
                <div class="col-md-6">
                    <div class="form-group form-check">
                        <?php
                        $checkedLokasi = old('show_lokasi', $jenis['show_lokasi'] ?? 1);
                        ?>
                        <input type="checkbox"
                            class="form-check-input"
                            id="show_lokasi"
                            name="show_lokasi"
                            value="1"
                            <?= $checkedLokasi ? 'checked' : ''; ?>>

                        <label class="form-check-label" for="show_lokasi">
                            Tampilkan Lokasi Kegiatan<br>
                            <small class="form-text text-muted">
                                <span class="text-danger">*</span> Centang jika sifat pengajuan memiliki Lokasi Kegiatan
                            </small>
                        </label>
                    </div>
                </div>

                <!-- Show Lokasi -->
                <div class="col-md-6">
                    <div class="form-group form-check">
                        <?php
                        $checkedLokasi = old('show_deskripsi', $jenis['show_deskripsi'] ?? 1);
                        ?>
                        <input type="checkbox"
                            class="form-check-input"
                            id="show_deskripsi"
                            name="show_deskripsi"
                            value="1"
                            <?= $checkedLokasi ? 'checked' : ''; ?>>

                        <label class="form-check-label" for="show_deskripsi">
                            Tampilkan Deskripsi Kegiatan<br>
                            <small class="form-text text-muted">
                                <span class="text-danger">*</span> Centang jika sifat pengajuan memiliki Deskripsi Kegiatan
                            </small>
                        </label>
                    </div>
                </div>

                <!-- Deskripsi Jenis Kegiatan -->
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="deskripsi_jenis_kegiatan">Deskripsi Jenis Kegiatan</label>
                        <textarea class="form-control <?= isset($errors['deskripsi_jenis_kegiatan']) ? 'is-invalid' : ''; ?>"
                            id="deskripsi_jenis_kegiatan"
                            name="deskripsi_jenis_kegiatan"
                            rows="3"
                            placeholder="Deskripsikan tujuan atau karakteristik jenis kegiatan ini."><?= old('deskripsi_jenis_kegiatan', $jenis['deskripsi_jenis_kegiatan'] ?? ''); ?></textarea>
                        <?php if (isset($errors['deskripsi_jenis_kegiatan'])): ?>
                            <div class="invalid-feedback">
                                <?= esc($errors['deskripsi_jenis_kegiatan']); ?>
                            </div>
                        <?php endif; ?>
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
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group mt-4">
                        <button type="submit" class="btn btn-primary btn-rounded">
                            <i class="fas fa-save"></i> Simpan
                        </button>
                        <a href="<?= base_url('jenis-kegiatan'); ?>" class="btn btn-secondary btn-rounded">
                            Batal
                        </a>
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Pastikan variabel didefinisikan sebagai null jika tidak ada session
    window.FLASH_SUCCESS = <?= session()->getFlashdata('success') ? json_encode(session()->getFlashdata('success')) : 'undefined' ?>;
    window.FLASH_ERROR = <?= session()->getFlashdata('error') ? json_encode(session()->getFlashdata('error')) : 'undefined' ?>;
    window.FLASH_VALIDATION = <?= session()->getFlashdata('validation') ? json_encode(session()->getFlashdata('validation')) : 'undefined' ?>;
    window.RELASI_ELEMEN_KATEGORI = <?= json_encode($relasiList); ?>;

    const oldPenilaian = <?= json_encode(old('penilaian')) ?>;
    window.addEventListener('load', function() {
        if (oldPenilaian && Array.isArray(oldPenilaian)) {
            // Reset dan masukkan data dari old input
            penilaianData = oldPenilaian.map(item => ({
                id_elemen: item.id_elemen,
                nama_elemen: item.nama_elemen,
                id_kategori: item.id_kategori,
                nama_kategori: item.nama_kategori,
                kredit: item.kredit
            }));

            // Render ulang tabel
            if (typeof renderTable === 'function') {
                renderTable();
            }
        }
    });
</script>
<script src="<?= base_url('/assets/js/crud_pimpinan.js') ?>"></script>
<?php $this->endSection(); ?>