let chartJenis, chartBulan, chartProdi, chartFakultas;

function initCharts() {
    // FIX: Registrasi plugin agar tidak Error 'Not Defined'
    if (typeof ChartDataLabels !== 'undefined') {
        Chart.register(ChartDataLabels); 
    } else {
        console.error("Datalabels plugin is missing!");
        return;
    }

    const db = window.DB_STATS;
    const config = window.configSemester;
    const dbMonthly = window.dbMonthly;

    if (!db || !config || !dbMonthly) return;

    // --- CHART 1: SEBARAN JENIS KEGIATAN (v4 Style) ---
    const ctxJenis = document.getElementById('jenisBarChart');
    if (ctxJenis) {
        chartJenis = new Chart(ctxJenis, {
            type: 'bar',
            data: {
                labels: db.jenis.labels,
                datasets: [{
                    label: 'Jenis Kegiatan',
                    data: db.jenis.ganjil,
                    backgroundColor: db.jenis.colors,
                    borderColor: db.jenis.colors,
                    borderWidth: 1,
                    hoverBackgroundColor: db.jenis.colors,
                    hoverBorderColor: db.jenis.colors,
                    hoverBorderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                layout: {
                    padding: { top: 30 } // Ruang untuk angka di atas batang
                },
                plugins: {
                    // KONFIGURASI ANGKA DI ATAS
                    datalabels: {
                        display: true,
                        anchor: 'end',
                        align: 'top',
                        color: '#444',
                        font: { weight: 'bold', size: 12 },
                        formatter: (val) => (val > 0 ? val : '') 
                    },
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            generateLabels: (chart) => {
                                const data = chart.data;
                                return data.labels.map((label, i) => ({
                                    text: label,
                                    fillStyle: data.datasets[0].backgroundColor[i],
                                    strokeStyle: data.datasets[0].borderColor[i],
                                    lineWidth: 0,
                                    index: i
                                }));
                            }
                        }
                    }
                },
                scales: {
                    x: { display: false }, // Sumbu X bawah hilang
                    y: { 
                        beginAtZero: true,
                        suggestedMax: Math.max(...(db.jenis.ganjil || [5])) + 2 
                    }
                }
            }
        });
    }

    // --- CHART 2: TREN BULANAN (v4 Style) ---
    const ctxBulan = document.getElementById('monthlyBarChart');
    if (ctxBulan) {
        chartBulan = new Chart(ctxBulan, {
            type: 'bar',
            plugins: [ChartDataLabels], // Tambahkan jika ingin ada angka di atas batang
            data: {
                labels: config.ganjil.labels,
                datasets: [
                    { label: 'Disetujui', data: config.ganjil.months.map(m => dbMonthly.disetujui[m - 1] || 0), backgroundColor: '#007bff' },
                    { label: 'Proses', data: config.ganjil.months.map(m => dbMonthly.pending[m - 1] || 0), backgroundColor: '#ffc107' },
                    { label: 'Ditolak', data: config.ganjil.months.map(m => dbMonthly.ditolak[m - 1] || 0), backgroundColor: '#dc3545' }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    datalabels: {
                        anchor: 'end',
                        align: 'top',
                        formatter: (val) => (val > 0 ? val : ''),
                        font: { weight: 'bold' }
                    }
                },
                scales: {
                    y: { // v4 style: langsung y, bukan yAxes
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });
    }

    // --- CHART 3: PER FAKULTAS (v4 Style) ---
    const ctxFakultas = document.getElementById('fakultasLineChart');
    if (ctxFakultas) {
        chartFakultas = new Chart(ctxFakultas, {
            type: 'bar',
            plugins: [ChartDataLabels],
            data: {
                labels: db.fakultas.labels,
                datasets: [{
                    label: 'Total Kegiatan',
                    data: db.fakultas.ganjil,
                    backgroundColor: '#177dff',
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        top: 20 // Memberikan ruang ekstra di dalam canvas bagian atas
                    }
                },
                plugins: {
                    datalabels: {
                        anchor: 'end',
                        align: 'top',
                        offset: 4, // Jarak angka dari ujung batang
                        formatter: (val) => (val > 0 ? val : ''),
                        font: { weight: 'bold' }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grace: '15%', // Memberi ruang agar label tidak menabrak atap chart
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });
    }

    // --- CHART 4: PER PRODI (v4 Style) ---
    const ctxProdi = document.getElementById('prodiLineChart');
    if (ctxProdi) {
        chartProdi = new Chart(ctxProdi, {
            type: 'bar',
            plugins: [ChartDataLabels],
            data: {
                labels: db.prodi.labels,
                datasets: [{
                    label: 'Total Kegiatan',
                    data: db.prodi.ganjil,
                    backgroundColor: '#177dff',
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                // Memberikan padding tambahan di bagian atas canvas
                layout: {
                    padding: {
                        top: 25 
                    }
                },
                plugins: {
                    datalabels: {
                        anchor: 'end',
                        align: 'top',
                        offset: 5, // Jarak vertikal antara angka dan bar
                        formatter: (val) => (val > 0 ? val : ''),
                        font: { weight: 'bold' }
                    },
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        // Menambahkan ruang kosong 15% di atas nilai tertinggi
                        grace: '15%', 
                        ticks: { 
                            stepSize: 1 
                        }
                    }
                }
            }
        });
    }

    const filterEl = document.getElementById('filterSemester');
    if (filterEl) {
        filterEl.addEventListener('change', function() {
            updateChartData(this.value);
        });
    }
}

/**
 * Fungsi Update Data
 */
function updateChartData(smt) {
    if (!chartJenis || !chartBulan || !chartProdi || !chartFakultas) return;

    const db = window.DB_STATS;
    const cfg = window.configSemester ? window.configSemester[smt] : null;
    const dataBulanan = window.dbMonthly;
    if (!db || !cfg || !dataBulanan) return;

    // 1. Update Chart Jenis
    const newDataJenis = (smt === 'ganjil') ? db.jenis.ganjil : db.jenis.genap;
    chartJenis.data.datasets[0].data = newDataJenis;
    chartJenis.options.scales.y.suggestedMax = Math.max(...(newDataJenis || [0])) + 2;
    chartJenis.update();

    // 2. Update Chart Tren Bulanan
    chartBulan.data.labels = cfg.labels;
    chartBulan.data.datasets[0].data = cfg.months.map(m => dataBulanan.disetujui[m - 1] || 0);
    chartBulan.data.datasets[1].data = cfg.months.map(m => dataBulanan.pending[m - 1] || 0);
    chartBulan.data.datasets[2].data = cfg.months.map(m => dataBulanan.ditolak[m - 1] || 0);
    chartBulan.update();

    // 3. Update Chart Fakultas
    const newDataFakultas = (smt === 'ganjil') ? db.fakultas.ganjil : db.fakultas.genap;
    chartFakultas.data.datasets[0].data = newDataFakultas;
    chartFakultas.data.datasets[0].backgroundColor = (smt === 'ganjil') ? '#177dff' : '#28a745';
    chartFakultas.update();

    // 4. Update Chart Prodi
    const newDataProdi = (smt === 'ganjil') ? db.prodi.ganjil : db.prodi.genap;
    chartProdi.data.datasets[0].data = newDataProdi;
    // Ganti warna batang prodi agar ada perbedaan visual ganjil/genap
    chartProdi.data.datasets[0].backgroundColor = (smt === 'ganjil') ? '#177dff' : '#28a745';
    chartProdi.update();
}
/**
 * Fungsi Snapshot PDF
 */
async function exportSemuaPeriodePDF(event) {
    const btn = event.currentTarget;
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
    btn.disabled = true;

    const takeSnapshot = (id) => {
        const canvas = document.getElementById(id);
        const tCanvas = document.createElement('canvas');
        tCanvas.width = canvas.width;
        tCanvas.height = canvas.height;
        const ctx = tCanvas.getContext('2d');
        ctx.fillStyle = '#FFFFFF';
        ctx.fillRect(0, 0, tCanvas.width, tCanvas.height);
        ctx.drawImage(canvas, 0, 0);
        return tCanvas.toDataURL('image/jpeg', 0.9);
    };

    try {
        updateChartData('ganjil');
        await new Promise(r => setTimeout(r, 1000));
        document.getElementById('ganjil_jenis').value = takeSnapshot('jenisBarChart');
        document.getElementById('ganjil_bulan').value = takeSnapshot('monthlyBarChart');
        document.getElementById('ganjil_fakultas').value = takeSnapshot('fakultasLineChart');
        document.getElementById('ganjil_prodi').value = takeSnapshot('prodiLineChart');

        updateChartData('genap');
        await new Promise(r => setTimeout(r, 1000));
        document.getElementById('genap_jenis').value = takeSnapshot('jenisBarChart');
        document.getElementById('genap_bulan').value = takeSnapshot('monthlyBarChart');
        document.getElementById('genap_fakultas').value = takeSnapshot('fakultasLineChart');
        document.getElementById('genap_prodi').value = takeSnapshot('prodiLineChart');

        updateChartData(document.getElementById('filterSemester').value);
        document.getElementById('formCetakSemua').submit();

    } catch (e) {
        console.error(e);
        alert("Gagal memproses gambar.");
    } finally {
        btn.innerHTML = originalText;
        btn.disabled = false;
    }
}

document.addEventListener('DOMContentLoaded', initCharts);