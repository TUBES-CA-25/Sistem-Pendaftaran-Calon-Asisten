// Dependencies: common.js untuk showModal()

function showModal(message, gifUrl = null) {
    // ... (Kode showModal Mas yang lama biarkan saja di sini) ...
    let feedbackModalEscaped = new bootstrap.Modal(document.getElementById('editProfileModal'));
    const modalEl = document.getElementById("customModal");
    if (modalEl) {
         const modalMessage = document.getElementById("modalMessage");
         const modalGif = document.getElementById("modalGif");
         if (modalMessage) modalMessage.textContent = message;
         if (modalGif) {
             modalGif.style.display = gifUrl ? "block" : "none";
             if (gifUrl) modalGif.src = gifUrl;
         }
         const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
         modal.show();
    } else {
         alert(message);
    }
}

// Fungsi Validasi (Tetap sama)
function validatePhoneNumber(phoneNumber) {
    const phoneRegex = /^(?:\+62|62|0)(8[1-9][0-9]{6,9})$/;
    return phoneRegex.test(phoneNumber) ? 
        { success: true, message: "Nomor telepon valid." } : 
        { success: false, message: "nomor telepon tidak valid." };
}

function validateNoNumber(input) {
    const noNumberRegex = /^[A-Za-z\s]*$/;
    return noNumberRegex.test(input) ? 
        { success: true, message: "Input valid." } : 
        { success: false, message: "Input tidak valid: tidak boleh mengandung angka." };
}

// --- BAGIAN UTAMA YANG DIPERBAIKI ---

// Kita hapus $(document).ready dan ganti dengan Event Delegation
// Artinya: "Wahai Dokumen, kalau ada elemen bernama #biodataForm di-submit, jalankan ini..."

$(document).on('submit', '#biodataForm', function (e) {
    e.preventDefault();
    
    // Ambil elemen SAAT form disubmit (bukan di awal loading)
    // Ini kuncinya biar variabel tidak "null"
    const telephoneInput = document.getElementById("telephone");
    const tempatLahirInput = document.getElementById("tempatlahir");
    const namaInput = document.getElementById("nama");
    
    const phoneNumber = telephoneInput.value;
    const tempatLahir = tempatLahirInput.value;
    const nama = namaInput.value;

    let isValid = true;

    // Reset validitas lama
    telephoneInput.setCustomValidity("");
    tempatLahirInput.setCustomValidity("");
    namaInput.setCustomValidity("");

    // Validasi
    if (!validatePhoneNumber(phoneNumber).success) {
        telephoneInput.setCustomValidity(validatePhoneNumber(phoneNumber).message);
        telephoneInput.reportValidity();
        isValid = false;
    }

    if (!validateNoNumber(tempatLahir).success) {
        tempatLahirInput.setCustomValidity(validateNoNumber(tempatLahir).message);
        tempatLahirInput.reportValidity();
        isValid = false;
    }

    if (!validateNoNumber(nama).success) {
        namaInput.setCustomValidity(validateNoNumber(nama).message);
        namaInput.reportValidity();
        isValid = false;
    }

    if(!isValid) return;

    // AJAX Submit
    $.ajax({
        url: "/Sistem-Pendaftaran-Calon-Asisten/public/store", // URL Sudah benar
        type: "POST",
        data: $(this).serialize(), // Gunakan $(this) biar lebih aman
        dataType: "json",
        success: function (response) {
            if (response.status === "success") {
                showModal(
                    "Biodata berhasil disimpan",
                    "/Sistem-Pendaftaran-Calon-Asisten/public/Assets/gif/success.gif"
                );
                // Refresh halaman atau trigger klik
                if(document.querySelector('a[data-page="biodata"]')) {
                    document.querySelector('a[data-page="biodata"]').click();
                } else {
                    location.reload();
                }
            } else {
                showModal(
                    response.message || "Biodata gagal disimpan",
                    "/Sistem-Pendaftaran-Calon-Asisten/public/Assets/gif/failed.gif"
                );
            }
        },
        error: function (xhr, status, error) {
            console.log("Error:", xhr.responseText);
            showModal("Terjadi kesalahan sistem.", "/Sistem-Pendaftaran-Calon-Asisten/public/Assets/gif/failed.gif");
        },
    });
});

// Event Listener untuk Logout (Pakai delegation juga)
$(document).on('click', '#logoutButton', function (e) {
    e.preventDefault();
    $.ajax({
        url: "/Sistem-Pendaftaran-Calon-Asisten/public/logout",
        type: "POST",
        success: function (response) {
            window.location.href = "/Sistem-Pendaftaran-Calon-Asisten/public";
        }
    });
});

// Event Listener untuk Update Kelas (Pengganti onclick di HTML)
// Ini akan mendeteksi perubahan pada radio button gender secara otomatis
$(document).on('change', 'input[name="gender"]', function() {
    const gender = this.value;
    const kelasSelect = document.getElementById("floatingSelect");
    
    if(!kelasSelect) return;

    kelasSelect.innerHTML = ""; // Bersihkan opsi lama

    let kelasOptions = [];
    if (gender === "wanita") {
        kelasOptions = ["B1", "B2", "B3", "B4", "B5"];
    } else if (gender === "pria") {
        kelasOptions = ["A1", "A2", "A3", "A4", "A5", "A6", "A7", "A8", "A9"];
    }

    kelasOptions.forEach((kelas) => {
        const option = document.createElement("option");
        option.value = kelas;
        option.text = kelas;
        kelasSelect.appendChild(option);
    });
});