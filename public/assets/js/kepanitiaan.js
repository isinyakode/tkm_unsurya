/**
 * kepanitiaan.js
 * Full-featured client script:
 * - fallback SweetAlert loader
 * - flash handling (success/error/validation)
 * - mark invalid fields from server
 * - restore anggotaData (old input)
 * - modal cari NIM, tambah anggota ke tabel utama
 * - hapus anggota (delegation)
 * - FE validation + submit
 * - delete pengajuan (AJAX POST with CSRF, fallback form)
 *
 * Pastikan view menyetel global vars & meta CSRF sebelum memuat file ini.
 */

(function ensureSwalAndInit() {
    if (window.Swal) {
        if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
        else init();
        return;
    }
    const s = document.createElement('script');
    s.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js';
    s.async = true;
    s.onload = function () {
        if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
        else init();
    };
    s.onerror = function () {
        console.error('Gagal memuat SweetAlert2 CDN. Pastikan Swal tersedia.');
        if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
        else init();
    };
    document.head.appendChild(s);
})();
document.addEventListener('DOMContentLoaded', function() {
    const tglMulai = document.getElementById('tanggal_mulai');
    const tglSelesai = document.getElementById('tanggal_selesai');
    const errorMsg = document.getElementById('error_tanggal');

    if (tglMulai && tglSelesai) {
        // Ketika tanggal mulai berubah
        tglMulai.addEventListener('change', function() {
            // Set minimal tanggal selesai adalah tanggal mulai
            tglSelesai.min = this.value;
            
            // Jika tanggal selesai sudah terisi dan ternyata jadi lebih kecil dari mulai
            if (tglSelesai.value && tglSelesai.value < this.value) {
                tglSelesai.value = ''; // Kosongkan tanggal selesai
                errorMsg.style.display = 'block';
            } else {
                errorMsg.style.display = 'none';
            }
        });

        // Validasi tambahan saat tanggal selesai berubah
        tglSelesai.addEventListener('change', function() {
            if (tglMulai.value && this.value < tglMulai.value) {
                Swal.fire('Opps!', 'Tanggal selesai tidak boleh sebelum tanggal mulai', 'error');
                this.value = '';
            }
        });
    }
});
function init() {
    // -------------------------
    // Helpers
    // -------------------------
    const $ = (sel) => document.querySelector(sel);
    const $all = (sel) => Array.from(document.querySelectorAll(sel));
    const byId = (id) => document.getElementById(id);

    const BASE_URL = window.BASE_URL || '';
    let JENIS_KEGIATAN = null;
    if (typeof window.JENIS_KEGIATAN !== 'undefined' && window.JENIS_KEGIATAN) {
        if (typeof window.JENIS_KEGIATAN === 'string') JENIS_KEGIATAN = window.JENIS_KEGIATAN;
        else if (typeof window.JENIS_KEGIATAN === 'object') {
            JENIS_KEGIATAN = window.JENIS_KEGIATAN.slug_jenis_kegiatan || window.JENIS_KEGIATAN.jenis_kegiatan || null;
        }
    }
    const NEED_ANGGOTA = (typeof window.NEED_ANGGOTA !== 'undefined') ? Boolean(window.NEED_ANGGOTA) : true;
    const HAS_OLD_FILE = (typeof window.HAS_OLD_FILE !== 'undefined') ? Boolean(window.HAS_OLD_FILE) : false;

    // CSRF meta (CodeIgniter)
    const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
    const csrfHeaderMeta = document.querySelector('meta[name="csrf-header"]');
    const CSRF_TOKEN = csrfTokenMeta ? csrfTokenMeta.getAttribute('content') : null;
    const CSRF_HEADER = csrfHeaderMeta ? csrfHeaderMeta.getAttribute('content') : null;

    function buildFetchHeaders() {
        const headers = { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' };
        if (CSRF_HEADER && CSRF_TOKEN) headers[CSRF_HEADER] = CSRF_TOKEN;
        else if (CSRF_TOKEN) headers['X-CSRF-TOKEN'] = CSRF_TOKEN;
        return headers;
    }

    // -------------------------
    // Utility: mark invalid + feedback
    // -------------------------
    function markInvalid(el, message) {
        if (!el) return;
        if (!el.classList.contains('is-invalid')) el.classList.add('is-invalid');

        const next = el.nextElementSibling;
        if (next && next.classList && next.classList.contains('invalid-feedback')) {
            next.innerText = message;
        } else {
            const div = document.createElement('div');
            div.className = 'invalid-feedback';
            div.innerText = message;
            if (el.parentNode) el.parentNode.insertBefore(div, el.nextSibling);
        }

        const remover = () => {
            el.classList.remove('is-invalid');
            const sib = el.nextElementSibling;
            if (sib && sib.classList && sib.classList.contains('invalid-feedback')) sib.remove();
            el.removeEventListener('input', remover);
            el.removeEventListener('change', remover);
        };
        el.addEventListener('input', remover);
        el.addEventListener('change', remover);
    }

    function showWarning(message, focusId = null) {
        if (focusId) {
            const f = byId(focusId) || document.querySelector(`[name="${focusId}"]`);
            if (f) markInvalid(f, message);
        }
        return Swal.fire(message, "", "warning").then(() => {
            if (focusId) {
                const el = byId(focusId) || document.querySelector(`[name="${focusId}"]`);
                if (el) {
                    el.focus();
                    el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        });
    }

    // -------------------------
    // Flash handler
    // -------------------------
    (function handleFlash() {
        const success = (typeof window.FLASH_SUCCESS !== 'undefined') ? window.FLASH_SUCCESS : null;
        const error = (typeof window.FLASH_ERROR !== 'undefined') ? window.FLASH_ERROR : null;
        const validation = (typeof window.FLASH_VALIDATION !== 'undefined') ? window.FLASH_VALIDATION : null;

        function showListError(title, data) {
            let html = '<div style="text-align:left">';
            if (Array.isArray(data)) {
                html += '<ul style="margin:0;">' + data.map(d => `<li>${d}</li>`).join('') + '</ul>';
            } else if (typeof data === 'object' && data !== null) {
                html += '<ul style="margin:0;">';
                Object.keys(data).forEach(k => {
                    html += `<li>${data[k]}</li>`;
                    const el = document.querySelector(`[name="${k}"]`) || document.getElementById(k);
                    if (el) markInvalid(el, data[k]);
                });
                html += '</ul>';
            } else {
                html += `<p>${data}</p>`;
            }
            html += '</div>';
            Swal.fire({ icon: 'error', title: title, html: html, width: '600px' });
        }

        if (success) {
            Swal.fire({ icon: 'success', title: 'Berhasil', text: success, timer: 2000, timerProgressBar: true });
            return;
        }
        if (error) {
            if (Array.isArray(error) || (typeof error === 'object' && error !== null)) {
                showListError('Terjadi Kesalahan', error);
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: error });
            }
            return;
        }
        if (validation) {
            showListError('Validasi Gagal', validation);
            return;
        }
    })();

    // -------------------------
    // Mark invalid fields from server (alternate entry)
    // -------------------------
    (function markInvalidFromServer() {
        const errors = window.FLASH_VALIDATION || null;
        if (!errors || typeof errors !== 'object') return;
        Object.keys(errors).forEach(fieldName => {
            const msg = errors[fieldName];
            let el = document.querySelector(`[name="${fieldName}"]`);
            if (!el) el = document.getElementById(fieldName);
            if (!el) return;
            markInvalid(el, msg);
        });
    })();

    // -------------------------
    // Restore anggota from hidden old input
    // -------------------------
    (function restoreAnggotaFromHidden() {
        const hidden = byId('anggotaData');
        const tbody = $("#tabelMahasiswa tbody");
        if (!hidden || !tbody) return;
        if (!hidden.value) return;
        try {
            const arr = JSON.parse(hidden.value);
            if (!Array.isArray(arr)) return;
            arr.forEach(r => {
                const nim = (r.nim || '').trim();
                if (!nim) return;
                if (byId('main-row-' + nim)) return;
                const nama = (r.nama || '').trim();
                const tempat_lahir = (r.tempat_lahir || '').trim();
                const tanggal_lahir = (r.tanggal_lahir || '').trim();
                const fakultas = (r.fakultas || '').trim();
                const prodi = (r.prodi || '').trim();
                const semester = (r.semester || '').trim();
                const jenjang = (r.jenjang || '').trim();
                const peranId = r.peran || '';
                const row = `
                    <tr id="main-row-${nim}" data-peran-id="${peranId}">
                        <td>${nim}</td>
                        <td>${nama}</td>
                        <td>${tempat_lahir}</td>
                        <td>${tanggal_lahir}</td>
                        <td>${fakultas}</td>
                        <td>${prodi}</td>
                        <td>${jenjang}</td>
                        <td>${semester}</td>
                        <td>${peranId}</td>
                        <td>
                            <button class="btn btn-sm btn-danger btn-round btnHapus" type="button" data-nim="${nim}">
                                <i class="fas fa-fw fa-trash"></i>
                            </button>
                        </td>
                    </tr>`;
                tbody.insertAdjacentHTML('beforeend', row);
            });
        } catch (e) {
            console.error('Gagal parse anggotaData', e);
        }
    })();

    // -------------------------
    // Modal: cari NIM
    // -------------------------
    const btnCariNIM = $('#btnCariNIM');
    if (btnCariNIM) {
        btnCariNIM.addEventListener('click', (ev) => {
            ev.preventDefault();
            const nimInput = $('#nimInput');
            if (!nimInput) return;
            
            const nim = nimInput.value.trim();
            if (nim === '') return showWarning('NIM kosong!', 'nimInput');
    
            // Tambahkan feedback loading (opsional)
            btnCariNIM.disabled = true;
    
            fetch(BASE_URL + '/carimhs?nim=' + encodeURIComponent(nim))
                .then(r => r.json())
                .then(json => {
                    btnCariNIM.disabled = false;
    
                    // --- PERBAIKAN DI SINI ---
                    // Jika status false, tampilkan pesan 'message' dari PHP
                    if (!json || json.status === false) {
                        return Swal.fire('Gagal', json.message || 'NIM Tidak ditemukan', 'error');
                    }
    
                    const tbodyModal = byId('tbodyMahasiswa');
                    if (!tbodyModal) return;
                    
                    tbodyModal.innerHTML = '';
                    
                    // Cek apakah sudah ada di tabel modal
                    if (byId('modal-row-' + json.mahasiswa.nim)) return;
    
                    const row = `
                        <tr id="modal-row-${json.mahasiswa.nim}">
                            <td>${json.mahasiswa.nim}</td>
                            <td>${json.mahasiswa.nama}</td>
                            <td>${json.mahasiswa.namakotalahir}</td>
                            <td>${json.mahasiswa.tgllahir}</td>
                            <td>${json.mahasiswa.fakultas}</td>
                            <td>${json.mahasiswa.prodi}</td>
                            <td>${json.mahasiswa.jenjang}</td>
                            <td>${json.mahasiswa.semester}</td>
                        </tr>`;
                    tbodyModal.insertAdjacentHTML('beforeend', row);
                })
                .catch((err) => {
                    btnCariNIM.disabled = false;
                    console.error(err);
                    Swal.fire('Error!', 'Server bermasalah', 'error');
                });
        });
    }

    // -------------------------
    // Simpan dari modal -> tabel utama
    // -------------------------
    const btnSimpanMahasiswa = byId('btnSimpanMahasiswa');
    if (btnSimpanMahasiswa) {
        btnSimpanMahasiswa.addEventListener('click', () => {
            const peranSelect = byId('peran-anggota');
            
            if (peranSelect.value === 1) {
                 return showWarning('Peran wajib dipilih!', 'peran-anggota');
            }
            
            const peranId = peranSelect.value;
            const peranText = (peranSelect.options[peranSelect.selectedIndex]?.text || '').toLowerCase();

            const modalRows = $all('#tbodyMahasiswa tr');
            if (!modalRows || modalRows.length === 0) return showWarning('Belum ada data mahasiswa!', null);

            const tbodyMain = $('#tabelMahasiswa tbody');
            if (!tbodyMain) return;

            // Ambil NIM Pengaju dari input (asumsi ID input adalah 'nim_pengaju')
            const nimPengaju = byId('nim_pengaju')?.value || '';

            // Hitung jumlah sekretaris dan bendahara yang sudah ada di tabel utama
            let jumlahSekretaris = 0;
            let jumlahBendahara = 0;
            let wakilKetuaExists = false;

            $all('#tabelMahasiswa tbody tr').forEach(tr => {
                const txtPeran = tr.children[8].innerText.toLowerCase();
                if (txtPeran.includes('sekretaris')) jumlahSekretaris++;
                if (txtPeran.includes('bendahara')) jumlahBendahara++;
                if (txtPeran.includes('wakil ketua')) wakilKetuaExists = true;
            });

            // Loop pengecekan validasi sebelum insert
            for (const tr of modalRows) {
                const nim = tr.children[0].innerText.trim();

                // 1. Validasi NIM Pengaju (Ketua)
                if (nim === nimPengaju) {
                    Swal.fire('Opps!', `NIM ${nim} adalah pengaju (Ketua), tidak perlu ditambahkan sebagai anggota lagi.`, 'error');
                    return;
                }

                // 2. Cek apakah NIM sudah ada di tabel (Duplikat Anggota)
                if (byId('main-row-' + nim)) {
                    Swal.fire('Peringatan', `Mahasiswa dengan NIM ${nim} sudah ada di daftar anggota.`, 'warning');
                    return;
                }

                // 3. Validasi Peran Spesifik
                if (peranText.includes('wakil ketua') && wakilKetuaExists) {
                    Swal.fire('Gagal', 'Wakil Ketua sudah ada. Maksimal hanya 1 wakil ketua.', 'error');
                    return;
                }

                if (peranText.includes('sekretaris') && jumlahSekretaris >= 2) {
                    Swal.fire('Gagal', 'Sekretaris sudah mencapai batas maksimal (2 orang).', 'error');
                    return;
                }

                if (peranText.includes('bendahara') && jumlahBendahara >= 2) {
                    Swal.fire('Gagal', 'Bendahara sudah mencapai batas maksimal (2 orang).', 'error');
                    return;
                }
            }

            // Jika lolos semua validasi, baru masukkan data
            modalRows.forEach(tr => {
                const nim = tr.children[0].innerText.trim();
                const nama = tr.children[1].innerText.trim();
                const tempat_lahir = tr.children[2].innerText.trim();
                const tanggal_lahir = tr.children[3].innerText.trim();
                const fakultas = tr.children[4].innerText.trim();
                const prodi = tr.children[5].innerText.trim();
                const semester = tr.children[6].innerText.trim();
                const jenjang = tr.children[7].innerText.trim();
                const row = `
                    <tr id="main-row-${nim}" data-peran-id="${peranId}">
                        <td>${nim}</td>
                        <td>${nama}</td>
                        <td>${tempat_lahir}</td>
                        <td>${tanggal_lahir}</td>
                        <td>${fakultas}</td>
                        <td>${prodi}</td>
                        <td>${semester}</td>
                        <td>${jenjang}</td>
                        <td>${peranSelect.options[peranSelect.selectedIndex].text}</td>
                        <td>
                            <button class="btn btn-sm btn-danger btn-round btnHapus" type="button" data-nim="${nim}">
                                <i class="fas fa-fw fa-trash"></i>
                            </button>
                        </td>
                    </tr>`;
                tbodyMain.insertAdjacentHTML('beforeend', row);
            });

            Swal.fire('Berhasil!', 'Mahasiswa ditambahkan ke tabel utama', 'success');

            // Tutup Modal dan Reset
            const modalEl = byId('modalAnggota');
            const instance = bootstrap.Modal.getInstance(modalEl);
            if (instance) instance.hide();

            const cleanUp = () => {
                if (byId('nimInput')) byId('nimInput').value = '';
                if (byId('tbodyMahasiswa')) byId('tbodyMahasiswa').innerHTML = '';
                peranSelect.value = '';
                modalEl.removeEventListener('hidden.bs.modal', cleanUp);
            };
            modalEl.addEventListener('hidden.bs.modal', cleanUp);
        });
    }

    // -------------------------
    // Hapus anggota (delegation)
    // -------------------------
    const tabelBody = $('#tabelMahasiswa tbody');
    if (tabelBody) {
        tabelBody.addEventListener('click', (e) => {
            const btn = e.target.closest('.btnHapus');
            if (!btn) return;
            const nim = btn.getAttribute('data-nim');
            if (!nim) return;
            Swal.fire({
                title: 'Hapus Data?',
                text: 'Data mahasiswa akan dihapus dari tabel utama.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then(res => {
                if (res.isConfirmed) {
                    const r = byId('main-row-' + nim);
                    if (r) r.remove();
                    Swal.fire('Terhapus!', 'Data mahasiswa berhasil dihapus.', 'success');
                }
            });
        });
    }

    // -------------------------
    // Validasi FE & submit
    // -------------------------
    const btnSubmit = byId('btnSubmitForm');
    if (btnSubmit) {
        btnSubmit.addEventListener('click', () => {
            const namaInput = byId('nama_kegiatan');
            if (namaInput && namaInput.value.trim() === '') return showWarning('Nama kegiatan wajib diisi!', 'nama_kegiatan');

            const peranUtama = byId('peran');
            if (peranUtama && peranUtama.tagName === 'SELECT' && peranUtama.value === '') return showWarning('Peran wajib dipilih!', 'peran');

            const tingkatSelect = byId('tingkat'); // Sesuaikan ID
            if (tingkatSelect && (tingkatSelect.value === '' || tingkatSelect.value === '0')) {
                return showWarning('Tingkat kegiatan wajib dipilih!', 'tingkat');
            }
            if (tingkatSelect && tingkatSelect.value === '') return showWarning('Tingkat kegiatan wajib dipilih!', 'tingkat');

            const tglMulai = byId('tanggal_mulai');
            if (tglMulai && tglMulai.value === '') return showWarning('Tanggal mulai wajib diisi!', 'tanggal_mulai');

            const tglSelesai = byId('tanggal_selesai');
            if (tglSelesai && tglSelesai.value === '') return showWarning('Tanggal selesai wajib diisi!', 'tanggal_selesai');

            const deskripsi = byId('deskripsi_kegiatan');
            if (deskripsi && deskripsi.value.trim() === '') return showWarning('Deskripsi wajib diisi!', 'deskripsi_kegiatan');

            const lokasi = byId('lokasi_kegiatan');
            if (lokasi && lokasi.value.trim() === '') return showWarning('Lokasi wajib diisi!', 'lokasi_kegiatan');

            const laporan = byId('laporan');
            if (laporan) {
                const isPKKMB = JENIS_KEGIATAN && (String(JENIS_KEGIATAN).toLowerCase().includes('pkkmb') || String(JENIS_KEGIATAN).toLowerCase().includes('kegiatan-pkkmb'));
                const laporanWajib = !isPKKMB && !HAS_OLD_FILE;
                if (laporanWajib && laporan.files.length === 0) return showWarning('Laporan kegiatan wajib diunggah!', 'laporan');
                if (laporan.files.length > 0) {
                    const f = laporan.files[0];
                    if (f.size > (5 * 1024 * 1024)) return showWarning('Ukuran file laporan maksimal 5MB!', 'laporan');
                    const allowed = /\.(pdf|doc|docx|jpg|jpeg|png)$/i;
                    if (!allowed.test(f.name)) return showWarning('Format file tidak diperbolehkan!', 'laporan');
                }
            }

            let anggotaCount = 0;
            if (NEED_ANGGOTA) {
                const rows = $all('#tabelMahasiswa tbody tr');
                if (!rows || rows.length === 0) return showWarning('Minimal 1 anggota harus ditambahkan!', null);
                anggotaCount = rows.length;
                const arr = [];
                rows.forEach(tr => {
                    const nim = tr.children[0]?.innerText?.trim() || '';
                    const nama = tr.children[1]?.innerText?.trim() || '';
                    const tmpt_lhr = tr.children[2]?.innerText?.trim() || '';
                    const tgl_lhr = tr.children[3]?.innerText?.trim() || '';
                    const fakultas = tr.children[4]?.innerText?.trim() || '';
                    const prodi = tr.children[5]?.innerText?.trim() || '';
                    const jenjang = tr.children[6]?.innerText?.trim() || '';
                    const semester = tr.children[7]?.innerText?.trim() || '';
                    const peranText = tr.children[8]?.innerText?.trim() || '';

                    arr.push({ nim, tmpt_lhr, tgl_lhr, nama, fakultas, prodi, jenjang, semester, peran: peranText });
                });
                const hidden = byId('anggotaData');
                if (hidden) hidden.value = JSON.stringify(arr);
            }

            const namaForConfirm = (namaInput && namaInput.value.trim()) ? namaInput.value.trim() : '(tanpa nama kegiatan)';
            const anggotaInfo = NEED_ANGGOTA ? `<br><b>Jumlah anggota:</b> ${anggotaCount}` : '';

            Swal.fire({
                title: 'Kirim Pengajuan?',
                html: `Pastikan data sudah benar.<br><b>Nama kegiatan:</b> ${namaForConfirm}${anggotaInfo}`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, kirim',
                cancelButtonText: 'Periksa lagi'
            }).then(result => {
                if (result.isConfirmed) {
                    const form = byId('formPengajuan');
                    if (form) form.submit();
                }
            });
        });
    }

    const btnEditForm = byId('btnEditForm');
    if (btnEditForm) {
        btnEditForm.addEventListener('click', () => {
            const namaInput = byId('nama_kegiatan');
            if (namaInput && namaInput.value.trim() === '') return showWarning('Nama kegiatan wajib diisi!', 'nama_kegiatan');

            const peranUtama = byId('peran');
            if (peranUtama && peranUtama.tagName === 'SELECT' && peranUtama.value === '') return showWarning('Peran wajib dipilih!', 'peran');

            const tingkatSelect = byId('tingkat_kegiatan');
            if (tingkatSelect && tingkatSelect.value === '') return showWarning('Tingkat kegiatan wajib dipilih!', 'tingkat_kegiatan');

            const tglMulai = byId('tanggal_mulai');
            if (tglMulai && tglMulai.value === '') return showWarning('Tanggal mulai wajib diisi!', 'tanggal_mulai');

            const deskripsi = byId('deskripsi_kegiatan');
            if (deskripsi && deskripsi.value.trim() === '') return showWarning('Deskripsi wajib diisi!', 'deskripsi_kegiatan');

            const lokasi = byId('lokasi_kegiatan');
            if (lokasi && lokasi.value.trim() === '') return showWarning('Lokasi wajib diisi!', 'lokasi_kegiatan');

            const laporan = byId('laporan');
            if (laporan) {
                // Ambil variabel dari window yang kita set di PHP tadi
                const hasOldFile = window.HAS_OLD_FILE || false;
                const jenisKegiatan = window.JENIS_KEGIATAN || '';

                const isPKKMB = jenisKegiatan && (String(jenisKegiatan).toLowerCase().includes('pkkmb') || String(jenisKegiatan).toLowerCase().includes('kegiatan-pkkmb'));
                
                // Logika: Laporan WAJIB jika BUKAN PKKMB DAN TIDAK ADA file lama
                const laporanWajib = !isPKKMB && !hasOldFile;

                if (laporanWajib && laporan.files.length === 0) {
                    return showWarning('Laporan kegiatan wajib diunggah!', 'laporan');
                }

                // Validasi tambahan jika user memilih file baru
                if (laporan.files.length > 0) {
                    const f = laporan.files[0];
                    // Validasi ukuran (5MB)
                    if (f.size > (5 * 1024 * 1024)) return showWarning('Ukuran file laporan maksimal 5MB!', 'laporan');
                    
                    // Validasi ekstensi
                    const allowed = /\.(pdf|doc|docx|jpg|jpeg|png)$/i;
                    if (!allowed.test(f.name)) return showWarning('Format file tidak diperbolehkan! Gunakan PDF/Doc/Gambar.', 'laporan');
                }
            }

            let anggotaCount = 0;
            if (NEED_ANGGOTA) {
                const rows = $all('#tabelMahasiswa tbody tr');
                if (!rows || rows.length === 0) return showWarning('Minimal 1 anggota harus ditambahkan!', null);
                anggotaCount = rows.length;
                const arr = [];
                rows.forEach(tr => {
                    const nim = tr.children[0]?.innerText?.trim() || '';
                    const nama = tr.children[1]?.innerText?.trim() || '';
                    const tmpt_lhr = tr.children[2]?.innerText?.trim() || '';
                    const tgl_lhr = tr.children[3]?.innerText?.trim() || '';
                    const fakultas = tr.children[4]?.innerText?.trim() || '';
                    const prodi = tr.children[5]?.innerText?.trim() || '';
                    const jenjang = tr.children[6]?.innerText?.trim() || '';
                    const semester = tr.children[7]?.innerText?.trim() || '';
                    const peranText = tr.children[8]?.innerText?.trim() || '';

                    arr.push({ nim, nama, tmpt_lhr, tgl_lhr, fakultas, prodi, jenjang, semester, peran: peranText });
                });
                const hidden = byId('anggotaData');
                if (hidden) hidden.value = JSON.stringify(arr);
            }

            const namaForConfirm = (namaInput && namaInput.value.trim()) ? namaInput.value.trim() : '(tanpa nama kegiatan)';
            const anggotaInfo = NEED_ANGGOTA ? `<br><b>Jumlah anggota:</b> ${anggotaCount}` : '';

            Swal.fire({
                title: 'Kirim Pengajuan?',
                html: `Pastikan data sudah benar.<br><b>Nama kegiatan:</b> ${namaForConfirm}${anggotaInfo}`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, kirim',
                cancelButtonText: 'Periksa lagi'
            }).then(result => {
                if (result.isConfirmed) {
                    const form = byId('formPengajuan');
                    if (form) form.submit();
                }
            });
        });
    }

    // -------------------------
    // Delete pengajuan (gabungan: AJAX POST + fallback)
    // Tombol harus punya class .btn-delete-kegiatan dan data-id
    // -------------------------
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-delete-kegiatan');
        if (!btn) return;

        e.preventDefault();

        const slug = btn.getAttribute('data-slug');
        const nim = btn.getAttribute('data-nim');
        if (!slug) {
            return Swal.fire('Error', 'Slug tidak ditemukan pada tombol.', 'error');
        }

        Swal.fire({
            title: 'Hapus Pengajuan?',
            text: 'Data akan dihapus permanen (termasuk file laporan dan anggota terkait).',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal'
        }).then(function (result) {
            if (!result.isConfirmed) return;

            const url = BASE_URL + '/Delete-Pengajuan-Kegiatan/' + encodeURIComponent(slug) + '/' + encodeURIComponent(nim || '');

            fetch(url, {
                method: 'POST',
                headers: buildFetchHeaders()
            })
            .then(res => {
                const ct = res.headers.get('content-type') || '';
                if (ct.indexOf('application/json') !== -1) return res.json();
                return { status: res.ok, message: res.ok ? 'Sukses' : 'Gagal' };
            })
            .then(json => {
                if (json && json.status) {
                    // hapus baris dari UI jika tombol berada di dalam baris
                    const tr = btn.closest('tr');
                    if (tr) tr.remove();
                    Swal.fire({ icon: 'success', title: 'Terhapus', text: json.message || 'Data berhasil dihapus.' });
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: (json && json.message) ? json.message : 'Gagal menghapus data.' });
                }
            })
            .catch(err => {
                console.error('delete error', err);
                Swal.fire('Error', 'Terjadi kesalahan ketika menghapus. Coba lagi.', 'error');
            });
        });
    });

    const tableEl = byId('tablePengajuan');
    if (tableEl) {
        // Inisialisasi DataTable (Asumsi library sudah dimuat di View)
        // Kita gunakan window.jQuery jika tersedia untuk DataTables
        const $table = window.jQuery ? window.jQuery(tableEl).DataTable({
            columnDefs: [{ orderable: false, targets: 0 }]
        }) : null;

        const selectAll = byId('selectAll');
        const bulkBtn = byId('btnBulkVerify');
        const countSpan = byId('countSelected');

        const updateBulkUI = () => {
            const checkedBoxes = $all('.sub_chk:checked');
            const count = checkedBoxes.length;
            if (countSpan) countSpan.innerText = count;
            
            if (count > 0) {
                bulkBtn.style.display = 'inline-block';
            } else {
                bulkBtn.style.display = 'none';
                if (selectAll) selectAll.checked = false;
            }
        };

        if (selectAll) {
            selectAll.addEventListener('change', function() {
                $all('.sub_chk').forEach(cb => cb.checked = this.checked);
                updateBulkUI();
            });
        }

        // Event delegation untuk checkbox di dalam tabel
        tableEl.addEventListener('change', (e) => {
            if (e.target.classList.contains('sub_chk')) {
                updateBulkUI();
            }
        });

        if (bulkBtn) {
            bulkBtn.addEventListener('click', () => {
                const selectedIds = $all('.sub_chk:checked').map(cb => cb.getAttribute('data-id'));

                Swal.fire({
                    title: 'Konfirmasi',
                    text: `Setujui ${selectedIds.length} data?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Disetujui!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // SESUAIKAN DENGAN META TAG ANDA:
                        const csrfTokenName = document.querySelector('meta[name="csrf-token-name"]').getAttribute('content');
                        const csrfHash = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                        const formData = new FormData();
                        formData.append(csrfTokenName, csrfHash); // Ini akan mengirim 'csrf_test_name' => 'hash_value'
                        
                        selectedIds.forEach(id => {
                            formData.append('ids[]', id);
                        });

                        fetch(BASE_URL + '/setujui-batch', {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(res => res.json())
                        .then(data => {
                            // Update hash token untuk request berikutnya (mencegah expired)
                            if (data.token) {
                                document.querySelector('meta[name="csrf-token"]').setAttribute('content', data.token);
                            }

                            if (data.status === 'success') {
                                Swal.fire('Berhasil!', data.message, 'success').then(() => location.reload());
                            } else {
                                Swal.fire('Gagal!', data.message, 'error');
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            Swal.fire('Error!', 'Terjadi kesalahan sistem atau keamanan.', 'error');
                        });
                    }
                });
            });
        }
    } 

    const tableallkegiatan = document.getElementById('allkegiatan');

    if (tableallkegiatan && window.jQuery) {
        const $ = window.jQuery;
        
        const table = $(tableallkegiatan).DataTable({
            // Menonaktifkan sorting pada kolom pertama (No)
            columnDefs: [{
                targets: 0,
                orderable: false,
                searchable: false
            }],
            // Urutan default berdasarkan kolom kedua (index 1)
            order: [[1, 'asc']], 
            // Pengaturan bahasa (opsional agar lebih user-friendly)
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json' 
            }
        });

        // Logika agar nomor urut (kolom 0) tetap berurutan 1, 2, 3 meskipun di-sorting
        table.on('order.dt search.dt', function () {
            table.column(0, { search: 'applied', order: 'applied' }).nodes().each(function (cell, i) {
                cell.innerHTML = i + 1;
            });
        }).draw();
    }
} // end init


