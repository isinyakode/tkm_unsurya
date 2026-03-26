<?php /** @var \CodeIgniter\View\View $this */ ?>
<?php $this->extend('Layout/index'); ?>
<?php $this->section('Dashboard'); ?>
<div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
    <div>
        <h3 class="fw-bold mb-3"><?= $title; ?></h3>
        <h6 class="op-7 mb-2">All Activities Recorded in the System</h6>
    </div>
    <div class="ms-md-auto py-2 py-md-0">
        <button onclick="exportSemuaPeriodePDF(event)" class="btn btn-dark btn-round me-2">
            <i class="fas fa-file-pdf"></i> Cetak Semua Periode
        </button>

        <!-- Form Tersembunyi -->
        <form id="formCetakSemua" action="<?= base_url('Cetak-Semua-Kegiatan') ?>" method="post" target="_blank">
            <?= csrf_field(); ?>
            <input type="hidden" name="ganjil_jenis" id="ganjil_jenis">
            <input type="hidden" name="ganjil_bulan" id="ganjil_bulan">
            <input type="hidden" name="ganjil_fakultas" id="ganjil_fakultas">
            <input type="hidden" name="ganjil_prodi" id="ganjil_prodi">

            <input type="hidden" name="genap_jenis" id="genap_jenis">
            <input type="hidden" name="genap_bulan" id="genap_bulan">
            <input type="hidden" name="genap_fakultas" id="genap_fakultas">
            <input type="hidden" name="genap_prodi" id="genap_prodi">
        </form>
    </div>
</div>
<div class="row">
    <div class="col-sm-6 col-md-4">
        <div class="card card-stats card-round">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-icon">
                        <div class="icon-big text-center icon-primary bubble-shadow-small">
                            <i class="fas fa-file-alt"></i>
                        </div>
                    </div>
                    <div class="col col-stats ms-3 ms-sm-0">
                        <div class="numbers">
                            <p class="card-category">Total Seluruh Pengajuan</p>
                            <h4 class="card-title"><?= array_sum($chart_ganjil) + array_sum($chart_genap); ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-md-4">
        <div class="card card-stats card-round">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-icon">
                        <div class="icon-big text-center icon-info bubble-shadow-small">
                            <i class="far fa-calendar-alt"></i>
                        </div>
                    </div>
                    <div class="col col-stats ms-3 ms-sm-0">
                        <div class="numbers">
                            <p class="card-category">Periode Ganjil (Sept - Feb)</p>
                            <h4 class="card-title"><?= array_sum($chart_ganjil); ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-md-4">
        <div class="card card-stats card-round">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-icon">
                        <div class="icon-big text-center icon-secondary bubble-shadow-small">
                            <i class="far fa-calendar-check"></i>
                        </div>
                    </div>
                    <div class="col col-stats ms-3 ms-sm-0">
                        <div class="numbers">
                            <p class="card-category">Periode Genap (Mar - Ags)</p>
                            <h4 class="card-title"><?= array_sum($chart_genap); ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12 mb-3">
        <div class="card card-round bg-primary text-white">
            <div class="card-body d-flex justify-content-between align-items-center">
                <h3 class="fw-bold mb-0">Statistik Pengajuan</h3>
                <select id="filterSemester" class="form-control" style="width: 250px;">
                    <option value="ganjil">Semester Ganjil (Sept - Feb)</option>
                    <option value="genap">Semester Genap (Mar - Ags)</option>
                </select>
            </div>
        </div>
    </div>

    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="card-title">Sebaran Jenis Kegiatan</div>
            </div>
            <div class="card-body">
                <div class="chart-container" style="min-height: 450px">
                    <canvas id="jenisBarChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="card-title">Tren Bulanan</div>
            </div>
            <div class="card-body">
                <div class="chart-container" style="min-height: 450px">
                    <canvas id="monthlyBarChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="card-title">Kegiatan Per Fakultas</div>
            </div>
            <div class="card-body">
                <div class="chart-container" style="min-height: 400px">
                    <canvas id="fakultasLineChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="card-title">Kegiatan Per Program Studi</div>
            </div>
            <div class="card-body">
                <div class="chart-container" style="min-height: 400px">
                    <canvas id="prodiLineChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="card-title">Point Mahasiswa</div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>NIM</th>
                                <th>Nama</th>
                                <th>Prodi</th>
                                <th>Fakultas</th>
                                <th>Point</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        <tbody>
                            <?php $no = 1;
                            foreach ($data_point as $row) : ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= $row['nim']; ?></td>
                                    <td><?= $row['nama']; ?></td>
                                    <td><?= $row['prodi']; ?></td>
                                    <td><?= $row['fakultas']; ?></td>
                                    <td class="text-center">
                                        <span class="badge badge-success"><?= $row['total_point']; ?></span>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?= base_url("detail-point-mahasiswa/" . $row['nim']) ?>" class="btn btn-success btn-sm btn-round"><i class="fas fa-fw fa-file-alt"></i></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($data_point)) : ?>
                                <tr>
                                    <td colspan="6" class="text-center">Belum ada data poin disetujui.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Data Global untuk Chart
    window.DB_STATS = {
        jenis: {
            labels: <?= json_encode($chart_labels) ?>,
            ganjil: <?= json_encode($chart_ganjil) ?>,
            genap: <?= json_encode($chart_genap) ?>,
            colors: <?= json_encode($chart_colors ?? []) ?>
        },
        prodi: {
            labels: <?= json_encode($chart_prodi['labels']) ?>,
            ganjil: <?= json_encode($chart_prodi['ganjil']) ?>,
            genap: <?= json_encode($chart_prodi['genap']) ?>
        },
        fakultas: {
            labels: <?= json_encode($chart_fakultas['labels']) ?>,
            ganjil: <?= json_encode($chart_fakultas['ganjil']) ?>,
            genap: <?= json_encode($chart_fakultas['genap']) ?>
        }
    };

    // Di pimpinan.php bagian bawah (script)
    window.dbMonthly = {
        disetujui: <?= json_encode($monthly_disetujui) ?>,
        ditolak: <?= json_encode($monthly_ditolak) ?>,
        pending: <?= json_encode($monthly_pending) ?>
    };

    // Ubah const menjadi window.configSemester agar global dan tidak bentrok
    window.configSemester = {
        ganjil: {
            months: [9, 10, 11, 12, 1, 2],
            labels: ["Sep", "Okt", "Nov", "Des", "Jan", "Feb"],
            color: 'rgba(23, 125, 255, 0.7)'
        },
        genap: {
            months: [3, 4, 5, 6, 7, 8],
            labels: ["Mar", "Apr", "Mei", "Jun", "Jul", "Agt"],
            color: 'rgba(40, 167, 69, 0.7)'
        }
    };

    window.BASE_URL = "<?= base_url() ?>";
    window.FLASH_ERROR = <?= json_encode(session()->getFlashdata('error') ?: null) ?>;
</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.5.1/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0"></script>

<script src="<?= base_url('/assets/js/kepanitiaan.js') ?>"></script>
<script src="<?= base_url('/assets/js/chart.js') ?>"></script>
<?php $this->endSection() ?>