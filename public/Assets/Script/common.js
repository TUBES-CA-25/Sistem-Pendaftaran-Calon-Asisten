/**
 * Common Utilities
 * Fungsi-fungsi yang digunakan di banyak halaman
 */

// Constants untuk path
const APP_PATHS = {
    images: '/Sistem-Pendaftaran-Calon-Asisten/res/imageUser',
    documents: '/Sistem-Pendaftaran-Calon-Asisten/res/berkasUser',
    gifs: '/Sistem-Pendaftaran-Calon-Asisten/public/Assets/gif'
};

/**
 * Menampilkan modal custom
 * @param {string} message - Pesan yang akan ditampilkan
 * @param {string|null} gifUrl - URL GIF (optional)
 */
function showModal(message, gifUrl = null) {
    const modal = document.getElementById('customModal');
    if (!modal) return;

    const modalMessage = document.getElementById('modalMessage');
    const modalGif = document.getElementById('modalGif');
    const closeModal = document.getElementById('closeModal');
    
    if (modalMessage) modalMessage.textContent = message;
    if (modalGif) {
        modalGif.style.display = gifUrl ? 'block' : 'none';
        if (gifUrl) modalGif.src = gifUrl;
    }

    modal.style.display = 'flex';

    if (closeModal) {
        $(closeModal).off('click').on('click', () => modal.style.display = 'none');
    }

    $(window).off('click.customModal').on('click.customModal', (event) => {
        if (event.target === modal) modal.style.display = 'none';
    });
}

/**
 * Menampilkan modal konfirmasi
 * @param {string} message - Pesan konfirmasi
 * @param {function} onConfirm - Callback saat confirm
 * @param {function} onCancel - Callback saat cancel
 */
function showConfirm(message, onConfirm = null, onCancel = null) {
    const modal = document.getElementById('confirmModal');
    if (!modal) return;

    const modalMessage = document.getElementById('confirmModalMessage');
    const confirmButton = document.getElementById('confirmModalConfirm');
    const cancelButton = document.getElementById('confirmModalCancel');

    if (modalMessage) modalMessage.textContent = message;
    modal.style.display = 'flex';

    if (confirmButton) {
        $(confirmButton).off('click').on('click', () => {
            if (onConfirm) onConfirm();
            modal.style.display = 'none';
        });
    }

    if (cancelButton) {
        $(cancelButton).off('click').on('click', () => {
            if (onCancel) onCancel();
            modal.style.display = 'none';
        });
    }

    $(window).off('click.confirmModal').on('click.confirmModal', (event) => {
        if (event.target === modal) modal.style.display = 'none';
    });
}

/**
 * Helper untuk mendapatkan URL gambar
 * @param {string} filename - Nama file
 * @returns {string} URL lengkap
 */
function getImageUrl(filename) {
    return filename ? `${APP_PATHS.images}/${filename}` : `${APP_PATHS.images}/default-image.jpg`;
}

/**
 * Helper untuk mendapatkan URL dokumen
 * @param {string} filename - Nama file
 * @returns {string} URL lengkap atau '#' jika tidak ada
 */
function getDocumentUrl(filename) {
    return filename ? `${APP_PATHS.documents}/${filename}` : '#';
}

/**
 * Helper untuk AJAX request
 * @param {string} url - URL endpoint
 * @param {object} data - Data yang akan dikirim
 * @param {function} onSuccess - Callback success
 * @param {function} onError - Callback error
 */
function sendAjax(url, data, onSuccess, onError) {
    $.ajax({
        url: url,
        type: 'POST',
        data: data,
        dataType: 'json',
        success: onSuccess,
        error: onError || function(xhr) {
            console.error('AJAX Error:', xhr.responseText);
        }
    });
}
