/**
 * CRUD Pimpinan JS
 * Mengelola interaksi UI untuk Elemen Penilaian dan Kategori Kegiatan
 */

(function () {
    // -------------------------------------------------------------------------
    // 1. SETUP BASE URL (Mendeteksi otomatis path aplikasi)
    // -------------------------------------------------------------------------
    function getBaseUrl() {
        const path = window.location.pathname.split('/')[1];
        if (window.location.hostname === 'localhost') {
            return `${window.location.origin}/${path}`;
        }
        return window.location.origin;
    }
    const BASE_URL = getBaseUrl();

    // -------------------------------------------------------------------------
    // HELPER: Escape HTML untuk mencegah XSS
    // -------------------------------------------------------------------------
    function escapeHtml(str) {
        if (str == null) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    // 2. VARIABEL GLOBAL (Terhubung ke window agar bisa diakses dari script di View)
    window.penilaianData = [];

    // -------------------------------------------------------------------------
    // 3. EVENT LISTENER SAAT HALAMAN SELESAI DIMUAT
    // -------------------------------------------------------------------------
    document.addEventListener('DOMContentLoaded', function () {
        
        // --- A. NOTIFIKASI FLASH DATA (SweetAlert2) ---
        if (window.FLASH_SUCCESS) {
            Swal.fire({ 
                icon: 'success', 
                title: 'Berhasil', 
                text: window.FLASH_SUCCESS, 
                timer: 2000, 
                showConfirmButton: false 
            });
        }
        
        if (window.FLASH_ERROR) {
            let errorText = window.FLASH_ERROR;
            if (typeof window.FLASH_ERROR === 'object') {
                errorText = Object.values(window.FLASH_ERROR).join(', ');
            }
            Swal.fire({ 
                icon: 'error', 
                title: 'Gagal', 
                text: errorText 
            });
        }

        // --- B. LOGIKA HAPUS DATA ---
        // FIX Bug 3.1: Hapus .btn-delete-jenis dari sini karena sudah di-handle terpisah di bawah
        const deleteButtons = document.querySelectorAll('.btn-delete-data, .btn-delete-kategori, .btn-delete-elemen');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const url = this.getAttribute('data-url');
                const nama = this.getAttribute('data-nama');

                Swal.fire({
                    title: 'Konfirmasi Hapus',
                    text: `Apakah Anda yakin ingin menghapus "${escapeHtml(nama)}"?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Mohon Tunggu',
                            html: 'Sedang memproses penghapusan...',
                            allowOutsideClick: false,
                            didOpen: () => { Swal.showLoading(); }
                        });
                        window.location.href = url;
                    }
                });
            });
        });

        // Handler terpisah untuk .btn-delete-jenis (dengan pesan konfirmasi khusus)
        const deleteButtonsJenis = document.querySelectorAll('.btn-delete-jenis');
        if (deleteButtonsJenis.length > 0) {
            deleteButtonsJenis.forEach(button => {
                button.addEventListener('click', function() {
                    const url = this.getAttribute('data-url');
                    const nama = this.getAttribute('data-nama');

                    Swal.fire({
                        title: 'Hapus Jenis Kegiatan?',
                        text: `Jenis kegiatan "${escapeHtml(nama)}" akan dihapus permanen!`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Ya, Hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = url;
                        }
                    });
                });
            });
        }

        // --- C. LOGIKA EDIT KATEGORI (MODAL) ---
        const editKatButtons = document.querySelectorAll('.btn-edit-kategori');
        const editKatModal = document.getElementById('modalEditKategori');
        if (editKatModal && editKatButtons.length > 0) {
            const editForm = editKatModal.querySelector('form');
            const editInput = editKatModal.querySelector('input[name="nama_kategori_kegiatan"]');

            editKatButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const slug = this.getAttribute('data-slug');
                    const nama = this.getAttribute('data-nama');
                    if(editInput) editInput.value = nama;
                    if(editForm) {
                        // FIX Bug 3.5: Gunakan route lengkap
                        editForm.action = `${BASE_URL}/kategori-kegiatan/update/${slug}`;
                    }
                });
            });
        }

        // --- D. LOGIKA TAMBAH BARIS TABEL DINAMIS (Client-Side) ---
        const btnTambahKategori = document.getElementById('btnSimpanKategori');
        if(btnTambahKategori) {
            btnTambahKategori.addEventListener('click', function() {
                const select = document.getElementById('input_id_kategori');
                if(!select) return;

                const idKategori = select.value;
                const namaKategori = select.options[select.selectedIndex]?.getAttribute('data-nama');

                if (!idKategori) {
                    Swal.fire('Peringatan', 'Silahkan pilih kategori terlebih dahulu!', 'warning');
                    return;
                }

                // Cek apakah sudah ada di array (duplikasi)
                const exists = window.penilaianData.some(item => item.id_kategori === idKategori);
                if (exists) {
                    Swal.fire('Info', 'Kategori ini sudah ada dalam daftar.', 'info');
                    return;
                }

                // Push ke array global
                window.penilaianData.push({
                    id_kategori: idKategori,
                    nama_kategori: namaKategori
                });

                // Render ulang tabel
                window.renderTable();
                
                // Tutup Modal (Bootstrap 5)
                hideModal('modalElemenKategori');
                select.value = ""; // Reset dropdown
            });
        }

        const btnSimpan = document.getElementById('btnSimpanPenilaian');
        // Cek apakah tombol ada (agar tidak error di halaman lain yang tidak punya tombol ini)
        if (btnSimpan) {
            btnSimpan.addEventListener('click', function() {
                // 1. Ambil Value dari Input Modal
                const elSelect = document.getElementById('input_id_elemen');
                const katSelect = document.getElementById('input_id_kategori');
                const kreditInput = document.getElementById('input_kredit');

                const idElemen = elSelect.value;
                // Ambil text/nama dari atribut data-nama (opsional) atau text option
                const namaElemen = elSelect.options[elSelect.selectedIndex]?.getAttribute('data-nama') || elSelect.options[elSelect.selectedIndex]?.text;
                
                const idKategori = katSelect.value;
                const namaKategori = katSelect.options[katSelect.selectedIndex]?.getAttribute('data-nama') || katSelect.options[katSelect.selectedIndex]?.text;
                
                const kredit = kreditInput.value;

                // 2. Validasi Input
                if (!idElemen || !idKategori || !kredit) {
                    Swal.fire('Error', 'Harap lengkapi Elemen, Kategori, dan Kredit Score!', 'error');
                    return;
                }

                // 3. Masukkan ke Array Global
                window.penilaianData.push({
                    id_elemen: idElemen,
                    nama_elemen: namaElemen,
                    id_kategori: idKategori,
                    nama_kategori: namaKategori,
                    kredit: kredit
                });

                // 4. Render Ulang Tabel
                window.renderTable();

                // 5. Reset Form & Tutup Modal
                elSelect.value = '';
                katSelect.value = '';
                kreditInput.value = '';

                // FIX Bug 3.3: Menutup Modal Bootstrap 5 dengan null-check
                hideModal('modalElemenKategori');

                // Notifikasi Sukses Kecil
                Swal.fire({
                    icon: 'success',
                    title: 'Item ditambahkan',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 1500
                });
            });
        }

        const elSelect = document.getElementById('input_id_elemen');
        const katSelect = document.getElementById('input_id_kategori');

        // Cek apakah elemen dropdown ada di halaman ini
        if (elSelect && katSelect) {
            
            elSelect.addEventListener('change', function() {
                const idElemenDipilih = this.value;
                
                // 1. Kosongkan Dropdown Kategori
                katSelect.innerHTML = '<option value="">-- Pilih Kategori --</option>';
                katSelect.disabled = true;

                // Jika user memilih "Pilih Elemen" (kosong), berhenti di sini
                if (!idElemenDipilih) return;

                // 2. Ambil Data Relasi dari variabel Global (yang dikirim dari View)
                const dataRelasi = window.RELASI_ELEMEN_KATEGORI || [];

                // 3. Filter Kategori yang sesuai dengan ID Elemen ini
                const kategoriTersaring = dataRelasi.filter(item => item.id_elemen_penilaian == idElemenDipilih);

                // 4. Masukkan ke Dropdown Kategori
                if (kategoriTersaring.length > 0) {
                    kategoriTersaring.forEach(kat => {
                        const option = document.createElement('option');
                        option.value = kat.id_kategori_kegiatan;
                        option.text = kat.nama_kategori_kegiatan;
                        // Simpan nama di data attribute untuk tombol simpan nanti
                        option.setAttribute('data-nama', kat.nama_kategori_kegiatan); 
                        katSelect.appendChild(option);
                    });
                    katSelect.disabled = false; // Aktifkan dropdown
                } else {
                    katSelect.innerHTML = '<option value="">-- Tidak ada kategori untuk elemen ini --</option>';
                }
            });
        }

        // FIX Bug 3.6: Pindahkan validasi input kredit ke dalam DOMContentLoaded
        const inputKredit = document.getElementById('input_kredit');
        if (inputKredit) {
            inputKredit.addEventListener('input', function() {
                if (this.value < 0) this.value = 0;
            });
        }
    });

    // -------------------------------------------------------------------------
    // 4. FUNGSI HELPER (Diletakkan di window agar global)
    // -------------------------------------------------------------------------

    /**
     * Helper: Tutup modal Bootstrap 5 dengan null-safety
     * FIX Bug 3.3: Mencegah crash jika modal belum pernah diinstansiasi
     */
    function hideModal(modalId) {
        const modalEl = document.getElementById(modalId);
        if (!modalEl) return;
        if (typeof bootstrap !== 'undefined') {
            const modalInstance = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
            modalInstance.hide();
        } else if (window.jQuery) {
            window.jQuery(modalEl).modal('hide'); // Fallback jQuery
        }
    }

    // FIX Bug 3.2: Satu definisi renderTable yang mendukung kedua layout
    // Menggambar baris tabel berdasarkan isi array window.penilaianData
    window.renderTable = function() {
        const tbody = document.querySelector('#ElemenKategori tbody');
        
        // Jika tabel tidak ditemukan di halaman ini, berhenti (agar tidak error)
        if (!tbody) return; 

        tbody.innerHTML = '';

        if (window.penilaianData.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" class="text-center">Belum ada item penilaian.</td></tr>';
            return;
        }

        window.penilaianData.forEach((item, index) => {
            // Cek apakah data memiliki elemen & kredit (Struktur Jenis Kegiatan)
            // Jika tidak (misal halaman lain), tampilkan default
            let rowContent = '';

            if (item.nama_elemen && item.kredit) {
                // Tampilan Lengkap (Untuk Jenis Kegiatan)
                rowContent = `
                    <td>
                        <strong>${escapeHtml(item.nama_elemen)}</strong>
                        <input type="hidden" name="penilaian[${index}][id_elemen]" value="${escapeHtml(item.id_elemen)}">
                    </td>
                    <td>
                        ${escapeHtml(item.nama_kategori)}
                        <input type="hidden" name="penilaian[${index}][id_kategori]" value="${escapeHtml(item.id_kategori)}">
                    </td>
                    <td class="text-center">
                        <span class="badge bg-primary" style="font-size:14px">${escapeHtml(item.kredit)}</span>
                        <input type="hidden" name="penilaian[${index}][kredit]" value="${escapeHtml(item.kredit)}">
                    </td>
                `;
            } else {
                // Tampilan Sederhana (Jika dipakai di halaman Kategori/Elemen saja)
                rowContent = `
                    <td colspan="3">
                        ${escapeHtml(item.nama_kategori)}
                        <input type="hidden" name="penilaian[${index}][id_kategori]" value="${escapeHtml(item.id_kategori)}">
                    </td>
                `;
            }

            const row = `
                <tr>
                    <td class="text-center">${index + 1}</td>
                    ${rowContent}
                    <td class="text-center">
                        <button type="button" class="btn btn-danger btn-sm btn-round" onclick="removePenilaianArray(${index})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
            tbody.insertAdjacentHTML('beforeend', row);
        });
    };

    // Menghapus item dari array berdasarkan index
    window.removePenilaianArray = function(index) {
        window.penilaianData.splice(index, 1);
        window.renderTable();
    };

    // Mengisi array awal (biasanya digunakan saat halaman Edit)
    window.setInitialData = function (data) {
        if(!data || !Array.isArray(data)) return;
        window.penilaianData = data.map(item => ({
            id_kategori: item.id_kategori_kegiatan,
            nama_kategori: item.nama_kategori_kegiatan
        }));
        window.renderTable();
    };

})();