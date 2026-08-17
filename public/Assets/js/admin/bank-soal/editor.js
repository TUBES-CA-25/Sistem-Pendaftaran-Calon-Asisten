/**
 * admin/bank-soal/editor.js
 *
 * EDITOR INPUT SOAL pada halaman Bank Soal.
 * Menyalakan EasyMDE (editor teks berformat) di kolom deskripsi soal pada
 * modal Tambah & Edit, serta pratinjau gambar sebelum diunggah.
 *
 * Hanya mengurus tampilan form soal - tidak menyentuh data. Aksi simpan,
 * ubah, dan hapus ada di bank-soal.js.
 *
 * Dulu bernama bank-soal-ui.js. Nama itu terlalu umum: isinya bukan UI
 * halaman secara keseluruhan, melainkan khusus editor input soal.
 */
// Initialize EasyMDE
document.addEventListener('DOMContentLoaded', function() {
    // Options matching user screenshot
    const mdeOptions = {
         element: null, // to be set
         autoDownloadFontAwesome: false,
         spellChecker: false,
         status: false,
         uploadImage: true,
         imageUploadEndpoint: baseUrl + '/uploadImage',
         imagePathAbsolute: true,
         imageAccept: "image/png, image/jpeg, image/gif, image/webp",
         imageTexts: {
             sbInit: 'Drag & drop image here',
             sbOnDragEnter: 'Drop image to upload',
             sbOnDrop: 'Uploading...',
             sbProgress: 'Uploading... (#progress#)',
             sbOnUploaded: 'Uploaded',
             sizeUnits: 'b,kb,mb'
         },
         errorMessages: {
             noFileGiven: 'Please select a file.',
             typeNotAllowed: 'This file type is not allowed.',
             fileTooLarge: 'Image is too big detected.',
             importError: 'Something went wrong during image upload.'
         },
         toolbar: [
             "bold", "italic", "heading",
             "quote", "unordered-list", "ordered-list"
         ]
    };

    // Create Editor for Add Question
    window.easyMDE_add = new EasyMDE({
        ...mdeOptions,
        element: document.querySelector('#addSoalForm textarea[name="deskripsi"]')
    });

    // Create Editor for Edit Question
    window.easyMDE_edit = new EasyMDE({
        ...mdeOptions,
        element: document.querySelector('#editSoalForm textarea[name="deskripsi"]') 
    });

    // Refresh on Modal Open to fix rendering issues.
    //
    // Event-nya 'modal:shown' (dipancarkan core/ui.js), BUKAN 'shown.bs.modal'.
    // Bootstrap sudah dibuang dari project ini, jadi listener lama tidak pernah
    // menyala dan CodeMirror tampil kacau saat modal dibuka: editor dibangun
    // ketika modal masih display:none sehingga tinggi/gutter-nya terhitung 0.
    /**
     * Segarkan CodeMirror SETELAH animasi modal benar-benar selesai.
     *
     * core/ui.js memancarkan 'modal:shown' tepat setelah atribut data-open
     * dipasang - artinya di AWAL transisi scale-95 -> scale-100, bukan di
     * akhirnya. Kalau refresh() jalan saat itu, CodeMirror mengukur lebar huruf
     * ketika panel masih berskala 0,95 sehingga ukurannya meleset 5%.
     *
     * Catatan kejujuran: penundaan ini TIDAK terbukti lewat pengujian, karena
     * peramban headless yang dipakai melompati transisi. Sebab utama kursor
     * meleset adalah varian `data-[open]:scale-100` yang salah sasaran (lihat
     * view) - itu sudah diperbaiki dan terukur menyelesaikan masalahnya
     * (-13,6px menjadi -0,1px). Penundaan ini dipertahankan sebagai pengaman
     * justru KARENA perbaikan itu: dulu panel selalu 0,95 sehingga waktu
     * refresh tak berpengaruh, sekarang transisinya benar-benar berjalan.
     */
    function segarkanSetelahAnimasi(modal, ambilEditor) {
        modal.addEventListener('modal:shown', function () {
            const panel = modal.querySelector('.panel-modal-soal') || modal.firstElementChild;
            let sudah = false;

            const jalankan = function () {
                if (sudah) return;
                sudah = true;
                const editor = ambilEditor();
                if (editor) editor.codemirror.refresh();
            };

            if (panel) {
                panel.addEventListener('transitionend', function pada(e) {
                    if (e.propertyName !== 'transform') return;
                    panel.removeEventListener('transitionend', pada);
                    jalankan();
                });
            }
            // Jaring pengaman: transitionend tidak dipancarkan bila animasi
            // dimatikan (prefers-reduced-motion) atau transisinya tak berjalan.
            setTimeout(jalankan, 320);
        });
    }

    const addModal = document.getElementById('addSoalModal');
    if (addModal) {
        segarkanSetelahAnimasi(addModal, function () { return window.easyMDE_add; });
    }

    const editModal = document.getElementById('editSoalModal');
    if (editModal) {
        segarkanSetelahAnimasi(editModal, function () { return window.easyMDE_edit; });
    }

    // Refactored Image Preview helper
    function setupImagePreview(inputId, previewImgId, containerId) {
        const input = document.getElementById(inputId);
        if (input) {
            input.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    // Check file size (max 1MB)
                    if (file.size > 1 * 1024 * 1024) {
                        if (typeof showAlert === 'function') showAlert('Ukuran file terlalu besar. Maksimal 1MB.', false);
                        e.target.value = '';
                        document.getElementById(containerId).style.display = 'none';
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = function(event) {
                        document.getElementById(previewImgId).src = event.target.result;
                        document.getElementById(containerId).style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                } else {
                    document.getElementById(containerId).style.display = 'none';
                }
            });
        }
    }

    setupImagePreview('soalImageInput', 'previewImg', 'imagePreview');
    setupImagePreview('soalImageEditInput', 'editPreviewImg', 'editImagePreview');
});

// Error Handling & Suppression Scripts
// 1. Suppress external extension errors (Visual cleanup for console)
if (!window.originalConsoleError) {
    window.originalConsoleError = console.error;
    console.error = function(...args) {
        if (args[0] && typeof args[0] === 'string' && 
           (args[0].includes('chrome-extension://') || args[0].includes('quillbot'))) {
            return; // Suppress extension noise
        }
        window.originalConsoleError.apply(console, args);
    };
}

// 2. Global Image Error Handler (Handle 404s gracefully in UI)
document.addEventListener('error', function(e) {
    if (e.target && e.target.tagName === 'IMG') {
        // Stop if specific suppression class is present
        if (e.target.classList.contains('suppress-error')) return;

        // Check if src is current page (often happens with src="" or src="#")
        if (e.target.src === window.location.href || e.target.getAttribute('src') === '') {
            e.target.style.display = 'none'; // Just hide empty images
            return;
        }

        // Check if it's already a placeholder to prevent loops
        if (!e.target.src.includes('placehold.co')) {
            console.warn('Image failed to load, swapping with placeholder:', e.target.src);
            e.target.src = 'https://placehold.co/600x400?text=Image+Not+Found';
            e.target.alt = 'Broken Image';
            e.target.style.border = '1px dashed #ff0000';
        }
    }
}, true); // Capture phase to catch load errors
