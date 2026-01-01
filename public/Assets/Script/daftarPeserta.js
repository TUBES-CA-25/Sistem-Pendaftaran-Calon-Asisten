/**
 * Daftar Peserta - Admin Panel
 * Script untuk mengelola fitur-fitur di halaman Daftar Peserta
 * 
 * Dependencies: common.js, jQuery, DataTables, Bootstrap
 */

(function() {
    // State management
    let selectedMahasiswa = [];
    let dataTable = null;

    /**
     * Inisialisasi halaman
     */
    function initDaftarPeserta() {
        // Cek apakah elemen tabel ada
        if (!document.getElementById('daftar')) {
            console.log('Table #daftar not found, skipping initialization');
            return;
        }

        initDataTable();
        initNotificationFeature();
        initModalHandlers();
        initActionButtons();
        initFormHandlers();
    }

    /**
     * Inisialisasi DataTable
     */
    function initDataTable() {
        // Destroy existing DataTable if it exists
        if ($.fn.DataTable.isDataTable('#daftar')) {
            $('#daftar').DataTable().destroy();
        }
        
        // Initialize DataTable
        dataTable = $('#daftar').DataTable();
    }

    /**
     * Inisialisasi fitur notifikasi
     */
    function initNotificationFeature() {
        const $dropdown = $('#mahasiswa');
        const $addButton = $('#addMahasiswaButton');
        const $addAllButton = $('#addAllMahasiswaButton');
        const $form = $('#addNotificationForm');

        // Tambah mahasiswa individual
        $addButton.off('click').on('click', function() {
            const selectedOption = $dropdown[0].options[$dropdown[0].selectedIndex];
            const mahasiswaId = $dropdown.val();
            const mahasiswaText = selectedOption ? selectedOption.text : null;

            if (!mahasiswaId) {
                showModal("Pilih mahasiswa terlebih dahulu");
                return;
            }

            if (selectedMahasiswa.some(item => item.id === mahasiswaId)) {
                showModal("Mahasiswa sudah dipilih");
                return;
            }

            selectedMahasiswa.push({ id: mahasiswaId, text: mahasiswaText });
            renderSelectedMahasiswa();
            $dropdown[0].selectedIndex = 0;
        });

        // Tambah semua mahasiswa
        $addAllButton.off('click').on('click', function() {
            $dropdown.find('option').each(function() {
                const mahasiswaId = $(this).val();
                const mahasiswaText = $(this).text();
                
                if (mahasiswaId && !selectedMahasiswa.some(item => item.id === mahasiswaId)) {
                    selectedMahasiswa.push({ id: mahasiswaId, text: mahasiswaText });
                }
            });
            renderSelectedMahasiswa();
        });

        // Submit form
        $form.off('submit').on('submit', function(e) {
            e.preventDefault();
            sendNotification();
        });
    }

    /**
     * Render daftar mahasiswa yang dipilih
     */
    function renderSelectedMahasiswa() {
        const $list = $('#selectedMahasiswaList');
        $list.empty();

        selectedMahasiswa.forEach(function(mahasiswa) {
            const $listItem = $('<li>')
                .addClass('list-group-item d-flex justify-content-between align-items-center')
                .text(mahasiswa.text);

            const $removeButton = $('<button>')
                .addClass('btn btn-sm btn-danger')
                .text('Hapus')
                .on('click', function() {
                    selectedMahasiswa = selectedMahasiswa.filter(item => item.id !== mahasiswa.id);
                    renderSelectedMahasiswa();
                });

            $listItem.append($removeButton);
            $list.append($listItem);
        });
    }

    /**
     * Kirim notifikasi ke mahasiswa terpilih
     */
    function sendNotification() {
        const message = $('#message').val();
        const mahasiswaIds = selectedMahasiswa.map(item => item.id);

        if (selectedMahasiswa.length === 0) {
            showModal("Pilih mahasiswa terlebih dahulu");
            return;
        }

        if (!message) {
            showModal("Isi pesan terlebih dahulu");
            return;
        }

        $.ajax({
            url: `${APP_URL}/addallnotif`,
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ mahasiswaIds, message }),
            success: function(response) {
                showModal(response.message || 'Pesan berhasil dikirim');
                if (response.status === 'success') {
                    setTimeout(() => location.reload(), 1500);
                }
            },
            error: function(xhr) {
                console.error('Error:', xhr.responseText);
                showModal("Gagal mengirim pesan. Silakan coba lagi.");
            }
        });
    }

    /**
     * Inisialisasi modal handlers
     */
    function initModalHandlers() {
        // Detail Modal Handler
        $('#detailModal').off('show.bs.modal').on('show.bs.modal', function(event) {
            const $button = $(event.relatedTarget);
            const data = {
                id: $button.closest('tr').data('id'),
                nama: $button.data('nama'),
                stambuk: $button.data('stambuk'),
                jurusan: $button.data('jurusan'),
                kelas: $button.data('kelas'),
                alamat: $button.data('alamat'),
                tempat_lahir: $button.data('tempat_lahir'),
                notelp: $button.data('notelp'),
                tanggal_lahir: $button.data('tanggal_lahir'),
                jenis_kelamin: $button.data('jenis_kelamin'),
                foto: $button.data('foto'),
                cv: $button.data('cv'),
                transkrip: $button.data('transkrip'),
                surat: $button.data('surat')
            };

            // Set modal data
            $(this).data('id', data.id);
            $('#modalNama').text(data.nama);
            $('#modalStambuk').text(data.stambuk);
            $('#modalJurusan').text(data.jurusan);
            $('#modalKelas').text(data.kelas);
            $('#modalAlamat').text(data.alamat);
            $('#modalTempat_lahir').text(data.tempat_lahir);
            $('#modalNoTelp').text(data.notelp);
            $('#modalTanggal_lahir').text(data.tanggal_lahir);
            $('#modalJenis_kelamin').text(data.jenis_kelamin);

            // Set foto menggunakan helper function
            $('#modalFoto').attr({
                'src': getImageUrl(data.foto),
                'alt': `Foto ${data.nama}`
            });

            // Set download URLs menggunakan helper functions
            $('#downloadFotoButton').attr('data-download-url', getImageUrl(data.foto));
            $('#downloadCVButton').attr('data-download-url', getDocumentUrl(data.cv));
            $('#downloadTranskripButton').attr('data-download-url', getDocumentUrl(data.transkrip));
            $('#downloadSuratButton').attr('data-download-url', getDocumentUrl(data.surat));
        });

        // Download Button Handler
        $('button[data-download-url]').off('click').on('click', function() {
            const url = $(this).data('download-url');
            if (url && url !== '#') {
                window.location.href = url;
            } else {
                alert('Berkas tidak tersedia.');
            }
        });
    }

    /**
     * Inisialisasi action buttons
     */
    function initActionButtons() {
        // Edit Button Handler
        $(document).off('click', '#daftar tbody img[alt="edit"]')
            .on('click', '#daftar tbody img[alt="edit"]', function(event) {
                event.stopPropagation();
                const id = $(this).closest('tr').data('id');
                $('#editModal').data('id', id).modal('show');
            });

        // Delete Button Handler
        $(document).off('click', '#daftar tbody img[alt="delete"]')
            .on('click', '#daftar tbody img[alt="delete"]', function(event) {
                event.stopPropagation();
                const id = $(this).closest('tr').data('id');
                const userid = $(this).closest('tr').data('userid');
                $('#deleteModal')
                    .data('id', id)
                    .data('userid', userid)
                    .modal('show');
            });

        // Accept Button Handler
        $('#acceptButton').off('click').on('click', function() {
            const idToSend = $('#detailModal').data('id');
            
            if (!idToSend) {
                alert('ID tidak ditemukan di modal!');
                return;
            }

            $.ajax({
                url: `${APP_URL}/acceptberkas`,
                type: 'POST',
                data: { id: idToSend },
                success: function(response) {
                    const isSuccess = response.status === 'success';
                    showModal(
                        isSuccess ? "Mahasiswa berhasil diterima" : "Gagal menerima mahasiswa",
                        isSuccess ? `${APP_PATHS.gifs}/success.gif` : `${APP_PATHS.gifs}/failed.gif`
                    );
                    
                    if (isSuccess) {
                        setTimeout(() => {
                            $('a[data-page="lihatPeserta"]').click();
                            $('#detailModal').modal('hide');
                        }, 1500);
                    }
                },
                error: function(xhr) {
                    console.error('Error:', xhr.responseText);
                    showModal("Gagal menerima mahasiswa", `${APP_PATHS.gifs}/failed.gif`);
                }
            });
        });

        // Confirm Delete Button Handler
        $('#confirmDelete').off('click').on('click', function(e) {
            e.preventDefault();
            const userid = $('#deleteModal').data('userid');
            
            $.ajax({
                url: `${APP_URL}/deletemhs`,
                type: 'POST',
                data: { id: userid },
                dataType: 'json',
                success: function(response) {
                    const isSuccess = response.status === 'success';
                    showModal(
                        isSuccess ? "Mahasiswa berhasil dihapus" : "Gagal menghapus mahasiswa",
                        isSuccess ? `${APP_PATHS.gifs}/success.gif` : `${APP_PATHS.gifs}/failed.gif`
                    );
                    
                    $('#deleteModal').modal('hide');
                    
                    if (isSuccess) {
                        setTimeout(() => $('a[data-page="lihatPeserta"]').click(), 1500);
                    }
                },
                error: function(xhr) {
                    console.error('Error:', xhr.responseText);
                    showModal("Gagal menghapus mahasiswa", `${APP_PATHS.gifs}/failed.gif`);
                }
            });
        });
    }

    /**
     * Inisialisasi form handlers
     */
    function initFormHandlers() {
        $('#editForm').off('submit').on('submit', function(e) {
            e.preventDefault();
            const id = $('#editModal').data('id');
            const message = $('#message').val();

            $.ajax({
                url: `${APP_URL}/notification`,
                type: 'POST',
                data: { id, message },
                dataType: 'json',
                success: function(response) {
                    const isSuccess = response.status === 'success';
                    showModal(
                        response.message || (isSuccess ? "Pesan berhasil dikirim" : "Gagal mengirim pesan"),
                        isSuccess ? `${APP_PATHS.gifs}/success.gif` : `${APP_PATHS.gifs}/failed.gif`
                    );
                    
                    $('#editModal').modal('hide');
                    
                    if (isSuccess) {
                        setTimeout(() => $('a[data-page="lihatPeserta"]').click(), 1500);
                    }
                },
                error: function(xhr) {
                    console.error('Error:', xhr.responseText);
                    showModal("Gagal mengirim pesan", `${APP_PATHS.gifs}/failed.gif`);
                }
            });
        });
    }

    // Expose initDaftarPeserta to global scope for external calls
    window.initDaftarPeserta = initDaftarPeserta;

    // Auto-initialize saat DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initDaftarPeserta);
    } else {
        // DOM sudah ready (untuk AJAX load)
        initDaftarPeserta();
    }
})();
