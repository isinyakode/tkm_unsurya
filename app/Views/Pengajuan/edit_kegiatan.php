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
        <h3 class="fw-bold mb-3"><?= esc($title); ?></h3>
        <h6 class="op-7 mb-2">Perbarui data pengajuan kegiatan Anda</h6>
    </div>
    <div class="ms-md-auto py-2 py-md-0">
        <a href="<?= base_url('pengajuan-kegiatan') ?>" class="btn btn-secondary btn-round">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card card-round">
            <div class="card-body">
                <form action="<?= base_url('Edit-Pengajuan-Kegiatan/' . $kegiatan['slug_kegiatan_mahasiswa']) ?>" id="formPengajuan" method="POST" enctype="multipart/form-data" novalidate>
                    <?= csrf_field() ?>

                    <input type="hidden" name="id_jenis_kegiatan" value="<?= esc($kegiatan['id_jenis_kegiatan']); ?>">

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
                                    <?php
                                    // Cek apakah ini yang dipilih sebelumnya
                                    $savedPeran = $selectedValues['peran'] ?? '';
                                    $selected = ($savedPeran == $p['id_kategori_kegiatan']) ? 'selected' : '';
                                    ?>
                                    <option value="<?= esc($p['id_kategori_kegiatan']); ?>" <?= $selected ?>>
                                        <?= esc($p['nama_kategori_kegiatan']); ?> - (<?= esc($p['kredit_score']); ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($showNamaKegiatan)): ?>
                        <div class="form-group">
                            <label for="nama_kegiatan">Nama Kegiatan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama_kegiatan" name="nama_kegiatan" value="<?= old('nama_kegiatan', $kegiatan['nama_kegiatan']) ?>">
                        </div>
                    <?php endif; ?>

                    <div class="row">
                        <?php if (!empty($tingkat)): ?>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="tingkat">Tingkat Kegiatan <span class="text-danger">*</span></label>
                                    <select class="form-control" id="tingkat" name="tingkat" required>
                                        <option value="">-- Pilih --</option>
                                        <?php foreach ($tingkat as $tp): ?>
                                            <?php $selected = ($selectedValues['tingkat'] ?? '') == $tp['id_kategori_kegiatan'] ? 'selected' : ''; ?>
                                            <option value="<?= $tp['id_kategori_kegiatan']; ?>" <?= $selected ?>>
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
                                            <?php $selected = ($selectedValues['viewers'] ?? '') == $ms['id_kategori_kegiatan'] ? 'selected' : ''; ?>
                                            <option value="<?= $ms['id_kategori_kegiatan']; ?>" <?= $selected ?>>
                                                <?= esc($ms['nama_kategori_kegiatan']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($jenis_kategori) && !empty($labelJenis)): ?>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="jenis"><?= esc($labelJenis); ?><span class="text-danger">*</span></label>
                                    <select class="form-control" id="jenis" name="jenis" required>
                                        <option value="">-- Pilih --</option>
                                        <?php foreach ($jenis_kategori as $jk): ?>
                                            <?php $selected = ($selectedValues['jenis'] ?? '') == $jk['id_kategori_kegiatan'] ? 'selected' : ''; ?>
                                            <option value="<?= $jk['id_kategori_kegiatan']; ?>" <?= $selected ?>>
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
                                            <?php $selected = ($selectedValues['waktu'] ?? '') == $wk['id_kategori_kegiatan'] ? 'selected' : ''; ?>
                                            <option value="<?= $wk['id_kategori_kegiatan']; ?>" <?= $selected ?>>
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
                                    <input type="date" class="form-control" id="tanggal_mulai" name="tanggal_mulai" value="<?= old('tanggal_mulai', $kegiatan['tanggal_mulai']) ?>" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="tanggal_selesai">Tanggal Selesai</label>
                                    <input type="date" class="form-control" id="tanggal_selesai" name="tanggal_selesai" value="<?= old('tanggal_selesai', $kegiatan['tanggal_selesai']) ?>">
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($showDeskripsi)): ?>
                        <div class="form-group">
                            <label for="deskripsi_kegiatan">Deskripsi & Rincian Kegiatan <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="deskripsi_kegiatan" name="deskripsi_kegiatan" rows="5"><?= old('deskripsi_kegiatan', $kegiatan['deskripsi_kegiatan']) ?></textarea>
                        </div>
                    <?php endif; ?>

                    <div class="row">
                        <?php if (!empty($showLokasi)): ?>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="lokasi_kegiatan">Lokasi Kegiatan</label>
                                    <input type="text" class="form-control" id="lokasi_kegiatan" name="lokasi_kegiatan" value="<?= old('lokasi_kegiatan', $kegiatan['lokasi_kegiatan']) ?>">
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="laporan">Bukti Kegiatan (Kosongkan jika tidak ingin ganti file)</label>
                                <input type="file" class="form-control" id="laporan" name="laporan" accept="application/pdf">
                                <small class="text-muted">File lama: <a href="<?= base_url('uploads/tkm/' . $kegiatan['nim_pengaju'] . '/' . $kegiatan['slug_kegiatan_mahasiswa'] . '/' . $kegiatan['file_laporan']) ?>" target="_blank"><?= $kegiatan['file_laporan'] ?></a></small>
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

                            <input type="hidden" id="anggotaData" name="anggotaData" value='<?= old('anggotaData', $anggotaJson) ?>'>
                        </div>
                    <?php endif; ?>
                    <div class="row form-group mt-4">
                        <div class="col">
                            <div class="d-flex gap-2 justify-content-between">
                                <a href="<?= base_url('/pengajuan-kegiatan') ?>" class="btn btn-danger btn-round">Batal</a>
                                <button type="button" id="btnEditForm" class="btn btn-success btn-round">
                                    Simpan Perubahan <i class="fas fa-save ms-2"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                </form>


            </div>
        </div>
        <!-- Logs -->
        <div class="card card-round shadow-sm">
            <div class="card-header">
                <div class="card-title">Track Record Verifikasi</div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="py-2 small">Waktu</th>
                                <th class="py-2 small">Status</th>
                                <th class="py-2 small">Catatan</th>
                                <th class="py-2 small">Verifikator</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($logs): foreach ($logs as $l): ?>
                                    <tr>
                                        <td class="py-2 small text-nowrap"><?= date('d/m/y H:i', strtotime($l['created_at'])) ?></td>
                                        <td class="py-2">
                                            <?php echo get_status_badge($l['status_baru']); ?>
                                        </td>
                                        <td class="py-2 small text-muted italic"><?= esc($l['catatan_verifikator']) ?: '-' ?></td>
                                        <td class="py-2 small"><?= esc($l['verifikator']) ?: '-' ?></td>
                                    </tr>
                                <?php endforeach;
                            else: ?>
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted small">Belum ada riwayat verifikasi.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($showAnggota)): ?>
    <!-- Modal Anggota (sama seperti markup modal Anda sebelumnya) -->
    <div class="modal fade" id="modalAnggota" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Anggota</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row align-items-end">
                        <div class="col-md-10">
                            <label>NIM</label>
                            <input type="text" id="nimInput" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <br>
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
                                    <th>Tempat Lahir</th>
                                    <th>Tanggal Lahir</th>
                                    <th>Fakultas</th>
                                    <th>Prodi</th>
                                    <th>Jenjang</th>
                                    <th>Semester</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyMahasiswa"></tbody>
                        </table>
                    </div>

                    <label class="mt-3">Peran</label>
                    <select class="form-control" id="peran-anggota">
                        <option value="">-- Pilih Peran --</option>
                        <?php foreach ($peran as $p): ?>
                            <option value="<?= esc($p['id_kategori_kegiatan']) ?>"><?= esc($p['nama_kategori_kegiatan']) ?></option>
                        <?php endforeach; ?>
                    </select>

                    <button class="btn btn-primary mt-3 w-100" id="btnSimpanMahasiswa">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<script>
    // Variable global untuk kepanitiaan.js
    window.BASE_URL = "<?= base_url() ?>";
    window.NEED_ANGGOTA = <?= $showAnggota ? 'true' : 'false' ?>;
    window.HAS_OLD_FILE = <?= !empty($kegiatan['file_laporan']) ? 'true' : 'false' ?>;
    window.JENIS_KEGIATAN = "<?= esc($kegiatan['jenis_kegiatan'] ?? '') ?>";
    window.FLASH_ERROR = <?= json_encode(session()->getFlashdata('error') ?: null) ?>;
</script>
<script src="<?= base_url('/assets/js/kepanitiaan.js') ?>"></script>

<?php $this->endSection(); ?>