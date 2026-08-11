/**
 * user/berkas.js — Halaman Upload Berkas peserta.
 *
 * Tiga hal yang ditangani di sini:
 *  1. Validasi file SEBELUM dikirim (tipe + ukuran), dengan pesan yang menyebut
 *     berkas mana yang salah — bukan sekadar "Berkas gagal disimpan".
 *  2. Menampilkan pesan error dari server apa adanya (dulu hanya masuk
 *     console.log sehingga peserta tidak tahu apa yang salah).
 *  3. Tombol hapus pada tabel "Riwayat Submit Berkas" (menghapus seluruh
 *     submission, karena tabel berkas_mahasiswa menyimpan satu baris per
 *     mahasiswa yang di-update tiap submit ulang).
 *
 * Semua listener didelegasikan di document supaya idempoten terhadap SPA
 * re-inject (#content diganti oleh app.js).
 */
(function () {
    const MAX_SIZE = 5 * 1024 * 1024; // 5 MB — samakan dengan $maxFileSize di BerkasUser.php

    // Aturan per input: ekstensi yang diterima + label untuk pesan error.
    const RULES = {
        foto:            { ext: ['jpg', 'jpeg', 'png'], label: 'Foto 3x4',         teks: 'JPG, JPEG, atau PNG' },
        cv:              { ext: ['pdf'],                label: 'CV',               teks: 'PDF' },
        transkrip:       { ext: ['pdf'],                label: 'Transkrip Nilai',  teks: 'PDF' },
        suratpernyataan: { ext: ['pdf'],                label: 'Surat Pernyataan', teks: 'PDF' },
    };

    // Tautan unduh template TIDAK lagi dipasang dari sini. Nama berkasnya
    // ditulis langsung di view; menaruhnya di JS membuat tautan diam-diam mati
    // begitu berkasnya diganti, karena view-nya sendiri hanya berisi href="#".

    /* ------------------------------------------------------------------ *
     * Pesan error per input
     * ------------------------------------------------------------------ */

    function showFieldError(inputId, message) {
        const box = document.querySelector(`[data-error-for="${inputId}"]`);
        const input = document.getElementById(inputId);
        if (box) {
            box.querySelector('span').textContent = message;
            box.classList.remove('hidden');
            box.classList.add('flex');
        }
        if (input) {
            input.classList.add('border-red-400', 'ring-2', 'ring-red-100');
        }
    }

    function clearFieldError(inputId) {
        const box = document.querySelector(`[data-error-for="${inputId}"]`);
        const input = document.getElementById(inputId);
        if (box) {
            box.classList.add('hidden');
            box.classList.remove('flex');
        }
        if (input) {
            input.classList.remove('border-red-400', 'ring-2', 'ring-red-100');
        }
    }

    function formatSize(bytes) {
        return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
    }

    /**
     * Periksa satu input file. Mengembalikan pesan error, atau '' bila valid.
     */
    function validateInput(input) {
        const rule = RULES[input.id];
        if (!rule) return '';

        const file = input.files && input.files[0];
        if (!file) return '';   // kosong ditangani atribut `required`

        const ext = (file.name.split('.').pop() || '').toLowerCase();

        if (!rule.ext.includes(ext)) {
            return `${rule.label} harus berformat ${rule.teks}. File yang dipilih ".${ext}".`;
        }
        if (file.size > MAX_SIZE) {
            return `${rule.label} berukuran ${formatSize(file.size)}, melebihi batas 5 MB.`;
        }
        return '';
    }

    // Validasi langsung saat file dipilih, jadi peserta tahu sebelum submit.
    document.addEventListener('change', function (e) {
        const input = e.target;
        if (!input.matches('#berkasForm input[type="file"]')) return;

        const error = validateInput(input);
        if (error) {
            showFieldError(input.id, error);
        } else {
            clearFieldError(input.id);
        }
    });

    /* ------------------------------------------------------------------ *
     * Submit form
     * ------------------------------------------------------------------ */

    document.addEventListener('submit', async function (e) {
        const form = e.target.closest('#berkasForm');
        if (!form) return;

        e.preventDefault();

        // Validasi seluruh input dulu; kumpulkan semua yang salah sekaligus
        // supaya peserta tidak memperbaiki satu per satu.
        const inputs = form.querySelectorAll('input[type="file"]');
        const errors = [];
        inputs.forEach(function (input) {
            const err = validateInput(input);
            if (err) {
                showFieldError(input.id, err);
                errors.push(err);
            } else {
                clearFieldError(input.id);
            }
        });

        if (errors.length > 0) {
            showAlert(
                errors.length === 1
                    ? errors[0]
                    : `Ada ${errors.length} berkas yang belum sesuai. Periksa keterangan merah di bawah tiap kolom.`,
                false
            );
            return;
        }

        const submitBtn = form.querySelector('button[type="submit"]');
        const originalHtml = submitBtn ? submitBtn.innerHTML : '';
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="bi bi-arrow-repeat"></i>Mengunggah...';
        }

        try {
            const res = await fetch(`${APP_URL}/berkas`, {
                method: 'POST',
                body: new FormData(form),
            });
            const response = await res.json();

            if (response.status === 'success') {
                showModal(
                    'Berkas berhasil disimpan',
                    '/Sistem-Pendaftaran-Calon-Asisten/public/Assets/gif/success.gif',
                    function () {
                        const link = document.querySelector('a[data-page="uploadBerkas"]');
                        if (link) link.click();
                    }
                );
            } else {
                // Tampilkan alasan dari server, bukan pesan generik.
                showAlert(response.message || 'Berkas gagal disimpan.', false);
            }
        } catch (error) {
            showAlert('Terjadi kesalahan saat mengunggah: ' + error.message, false);
        } finally {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalHtml;
            }
        }
    });

    /* ------------------------------------------------------------------ *
     * Hapus seluruh submission (dari tabel Riwayat Submit Berkas)
     *
     * Tabel berkas_mahasiswa menyimpan SATU baris per mahasiswa yang di-update
     * tiap submit ulang, jadi menghapus baris riwayat = menghapus keempat
     * berkas sekaligus.
     * ------------------------------------------------------------------ */

    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-hapus-submit');
        if (!btn) return;

        showConfirmDelete(async function () {
            btn.disabled = true;
            try {
                const res = await fetch(`${APP_URL}/hapussubmitberkas`, { method: 'POST' });
                const data = await res.json();

                if (data.status === 'success') {
                    showAlert('Data berkas berhasil dihapus.', true);
                    const link = document.querySelector('a[data-page="uploadBerkas"]');
                    if (link) link.click();
                } else {
                    showAlert(data.message || 'Gagal menghapus data berkas.', false);
                    btn.disabled = false;
                }
            } catch (error) {
                showAlert('Terjadi kesalahan: ' + error.message, false);
                btn.disabled = false;
            }
        }, 'Hapus seluruh data berkas yang sudah disubmit? Keempat berkas (Foto, CV, Transkrip, Surat Pernyataan) akan ikut terhapus dan Anda perlu mengunggah ulang.');
    });
})();
