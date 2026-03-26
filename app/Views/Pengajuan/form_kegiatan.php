<?php /** @var \CodeIgniter\View\View $this */ ?>
<?php
/**
 * @var \CodeIgniter\View\View $this
 * @var array $peran
 * @var array $prestasi
 */
?>
<?php $this->extend('Layout/index'); ?>
<?php $this->section('Dashboard'); ?>

<div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
    <div>
        <h3 class="fw-bold mb-3"><?= esc($title) . ":" . esc($jenis_kegiatan) ?> </h3>
        <h6 class="op-7 mb-2">Daftarkan pengajuan kegiatan baru Anda</h6>
    </div>
    <div class="ms-md-auto py-2 py-md-0">
        <a href="<?= base_url('jenis-pengajuan') ?>" class="btn btn-secondary btn-round">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card card-round">
            <div class="card-body">
                <form action="<?= base_url('Pengajuan-Kegiatan/' . $slug_jenis_kegiatan) ?>" id="formPengajuan" method="POST" enctype="multipart/form-data" novalidate>
                    <?= csrf_field() ?>

                    <?php if ($peranMode === 'fixed'): ?>
                        <div class="form-group">
                            <label for="peran_display">Peran <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="peran_display" value="<?= esc($peranFixedValue); ?>" readonly>
                            <input type="hidden" id="peran" name="peran" value="<?= esc($peranFixedId ?? $peranFixedValue); ?>">
                        </div>
                    <?php elseif ($peranMode === 'select_peran' || $peranMode === 'select_prestasi'): ?>
                        <div class="form-group">
                            <label for="peran">Prestasi/Peran <span class="text-danger">*</span></label>
                            <select class="form-control" id="peran" name="peran" required>
                                <option value="">-- Pilih --</option>
                                <?php $options = ($peranMode === 'select_peran') ? $peran : $prestasi; ?>
                                <?php foreach ($options as $p): ?>
                                    <option value="<?= esc($p['id_kategori_kegiatan']); ?>" <?= old('peran') == $p['id_kategori_kegiatan'] ? 'selected' : '' ?>>
                                        <?= esc($p['nama_kategori_kegiatan']); ?> - (<?= esc($p['kredit_score']); ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($showNamaKegiatan)): ?>
                        <div class="form-group">
                            <label for="nama_kegiatan">Nama Kegiatan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama_kegiatan" name="nama_kegiatan" value="<?= old('nama_kegiatan') ?>" placeholder="Masukkan nama kegiatan">
                        </div>
                    <?php endif; ?>

                    <div class="row">
                        <?php if (!empty($badan_hukum)): ?>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="badan_hukum">Badan Hukum <span class="text-danger">*</span></label>
                                    <select class="form-control" id="badan_hukum" name="badan_hukum" required>
                                        <option value="0">-- Pilih --</option>
                                        <?php foreach ($badan_hukum as $tp): ?>
                                            <option value="<?= $tp['id_kategori_kegiatan']; ?>" <?= old('badan_hukum') == $tp['id_kategori_kegiatan'] ? 'selected' : '' ?>>
                                                <?= esc($tp['nama_kategori_kegiatan']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($omzet)): ?>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="omzet">Omzet <span class="text-danger">*</span></label>
                                    <select class="form-control" id="omzet" name="omzet" required>
                                        <option value="0">-- Pilih --</option>
                                        <?php foreach ($omzet as $tp): ?>
                                            <option value="<?= $tp['id_kategori_kegiatan']; ?>" <?= old('omzet') == $tp['id_kategori_kegiatan'] ? 'selected' : '' ?>>
                                                <?= esc($tp['nama_kategori_kegiatan']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($tingkat)): ?>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="tingkat">Tingkat Kegiatan <span class="text-danger">*</span></label>
                                    <select class="form-control" id="tingkat" name="tingkat" required>
                                        <option value="0">-- Pilih --</option>
                                        <?php foreach ($tingkat as $tp): ?>
                                            <option value="<?= $tp['id_kategori_kegiatan']; ?>" <?= old('tingkat') == $tp['id_kategori_kegiatan'] ? 'selected' : '' ?>>
                                                <?= esc($tp['nama_kategori_kegiatan']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($media_sosial)): ?>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="viewers">Viewers <span class="text-danger">*</span></label>
                                    <select class="form-control" id="viewers" name="viewers" required>
                                        <option value="">-- Pilih --</option>
                                        <?php foreach ($media_sosial as $ms): ?>
                                            <option value="<?= $ms['id_kategori_kegiatan']; ?>" <?= old('viewers') == $ms['id_kategori_kegiatan'] ? 'selected' : '' ?>>
                                                <?= esc($ms['nama_kategori_kegiatan']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($jenis) && !empty($labelJenis)): ?>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="jenis"><?= esc($labelJenis); ?><span class="text-danger">*</span></label>
                                    <select class="form-control" id="jenis" name="jenis" required>
                                        <option value="">-- Pilih --</option>
                                        <?php foreach ($jenis as $jk): ?>
                                            <option value="<?= $jk['id_kategori_kegiatan']; ?>" <?= old('jenis') == $jk['id_kategori_kegiatan'] ? 'selected' : '' ?>>
                                                <?= esc($jk['nama_kategori_kegiatan']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($waktu)): ?>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="waktu">Waktu Kegiatan <span class="text-danger">*</span></label>
                                    <select class="form-control" id="waktu" name="waktu" required>
                                        <option value="">-- Pilih --</option>
                                        <?php foreach ($waktu as $wk): ?>
                                            <option value="<?= $wk['id_kategori_kegiatan']; ?>" <?= old('waktu') == $wk['id_kategori_kegiatan'] ? 'selected' : '' ?>>
                                                <?= esc($wk['nama_kategori_kegiatan']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($showTanggal)): ?>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="tanggal_mulai">Tanggal Mulai <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="tanggal_mulai" name="tanggal_mulai" value="<?= old('tanggal_mulai') ?>" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="tanggal_selesai">Tanggal Selesai <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="tanggal_selesai" name="tanggal_selesai" value="<?= old('tanggal_selesai') ?>" required>
                                    <small id="error_tanggal" class="text-danger" style="display:none;">Tanggal selesai tidak boleh lebih awal!</small>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($showDeskripsi)): ?>
                        <div class="form-group">
                            <label for="deskripsi_kegiatan">Deskripsi & Rincian Kegiatan <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="deskripsi_kegiatan" name="deskripsi_kegiatan" rows="5" placeholder="Jelaskan detail kegiatan..."><?= old('deskripsi_kegiatan') ?></textarea>
                        </div>
                    <?php endif; ?>

                    <div class="row">
                        <?php if (!empty($showLokasi)): ?>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="lokasi_kegiatan">Lokasi Kegiatan</label>
                                    <input type="text" class="form-control" id="lokasi_kegiatan" name="lokasi_kegiatan" value="<?= old('lokasi_kegiatan') ?>" placeholder="Contoh: Kampus A, Jakarta">
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="laporan">Bukti Kegiatan (PDF) <span class="text-danger">*</span></label>
                                <input type="file" class="form-control" id="laporan" name="laporan" accept="application/pdf" required>
                                <small class="text-muted">Maksimal ukuran file: 2MB</small>
                            </div>
                        </div>
                    </div>

                    <?php if (!empty($showAnggota)): ?>
                        <div class="mt-3 mb-2">
                            <div class="row form-group mt-4">
                                <div class="col-sm-12">
                                    <div class="d-flex gap-2 justify-content-between text-center align-items-center">
                                        <h3><b>Daftar Anggota Tim</b></h3>
                                        <button type="button" class="btn btn-primary btn-round" data-bs-toggle="modal" data-bs-target="#modalAnggota">
                                            <i class="fas fa-plus"></i> Tambah Anggota
                                        </button>
                                    </div>
                                </div>
                                <div class="col-sm-12 mt-3">
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="tabelMahasiswa">
                                            <thead>
                                                <tr>
                                                    <th>NIM</th>
                                                    <th>Nama</th>
                                                    <th>Tempat Lahir</th>
                                                    <th>Tanggal Lahir</th>
                                                    <th>Fakultas</th>
                                                    <th>Prodi</th>
                                                    <th>Jenjang</th>
                                                    <th>Semester</th>
                                                    <th>Peran</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" id="anggotaData" name="anggotaData" value='<?= old('anggotaData', '[]') ?>'>
                        </div>
                    <?php endif; ?>

                    <div class="row form-group mt-4">
                        <div class="col">
                            <div class="d-flex gap-2 justify-content-between">
                                <a href="<?= base_url('/pengajuan-kegiatan') ?>" class="btn btn-danger btn-round">Batal</a>
                                <button type="button" id="btnSubmitForm" class="btn btn-success btn-round">
                                    Ajukan Kegiatan <i class="fas fa-paper-plane ms-2"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($showAnggota)): ?>
    <div class="modal fade" id="modalAnggota" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cari Anggota Mahasiswa</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row align-items-end">
                        <div class="col-md-10">
                            <label>Masukkan NIM Anggota</label>
                            <input type="text" id="nimInput" class="form-control" placeholder="Contoh: 2100123">
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-secondary w-100" id="btnCariNIM">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive mt-3">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>NIM</th>
                                    <th>Nama</th>
                                    <th>Prodi</th>
                                    <th>Jenjang</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyMahasiswa">
                            </tbody>
                        </table>
                    </div>

                    <label class="mt-3">Pilih Peran Anggota</label>
                    <select class="form-control" id="peran-anggota">
                        <option value="1">-- Pilih Peran --</option>
                        <?php foreach ($peran as $p): ?>
                            <option value="<?= esc($p['id_kategori_kegiatan']) ?>"><?= esc($p['nama_kategori_kegiatan']) ?></option>
                        <?php endforeach; ?>
                    </select>

                    <button class="btn btn-primary mt-3 w-100" id="btnSimpanMahasiswa">
                        <i class="fas fa-plus"></i> Masukkan ke Daftar Tim
                    </button>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<script>
    // Konfigurasi global untuk kepanitiaan.js
    window.BASE_URL = "<?= base_url() ?>";
    window.NEED_ANGGOTA = <?= $showAnggota ? 'true' : 'false' ?>;
    window.HAS_OLD_FILE = false; // Ini Form Tambah, jadi belum ada file
    window.JENIS_KEGIATAN = "<?= esc($nama_jenis_kegiatan ?? '') ?>";
    window.FLASH_ERROR = <?= json_encode(session()->getFlashdata('error') ?: null) ?>;
</script>
<script src="<?= base_url('/assets/js/kepanitiaan.js') ?>"></script>

<?php $this->endSection(); ?>