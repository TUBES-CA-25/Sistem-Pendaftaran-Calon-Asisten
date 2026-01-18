<?php
use App\Controllers\Profile\ProfileController;
use App\Controllers\user\BerkasUserController;

$ViewNama = ProfileController::viewBiodata();
$userName = ProfileController::viewUser()["username"];
$nama = ProfileController::viewBiodata() == null ? "Nama Lengkap" : ProfileController::viewBiodata()["namaLengkap"];
$stambuk = ProfileController::viewUser()["stambuk"];
$jurusan = ProfileController::viewBiodata() == null ? "Jurusan" : ProfileController::viewBiodata()["jurusan"];
$alamat = ProfileController::viewBiodata() == null ? "Alamat" : ProfileController::viewBiodata()["alamat"];
$kelas = ProfileController::viewBiodata() == null ? "Kelas" : ProfileController::viewBiodata()["kelas"];
$jenisKelamin = ProfileController::viewBiodata() == null ? "Jenis Kelamin" : ProfileController::viewBiodata()["jenisKelamin"];
$tempatLahir = ProfileController::viewBiodata() == null ? "Tempat Lahir" : ProfileController::viewBiodata()["tempatLahir"];
$tanggalLahir = ProfileController::viewBiodata() == null ? "Tanggal Lahir" : ProfileController::viewBiodata()["tanggalLahir"];
$noHp = ProfileController::viewBiodata() == null ? "No Telephone" : ProfileController::viewBiodata()["noHp"];
$photo = "/tubes_web/res/imageUser/" . (BerkasUserController::viewPhoto()["foto"] ?? "default.png");
?>

<link rel="stylesheet" href="/tubes_web/public/Assets/Style/profileStyle.css" />
<main class="profile-main">
    <div class="profile-header">
        <h1>Profile</h1>
    </div>

    <div class="profile-container">
        <!-- Profile Card -->
        <div class="profile-card">
            <div class="profile-photo-section">
                <div class="photo-wrapper">
                    <img src="<?= $photo ?>" alt="Profile Picture" class="profile-photo">
                    <div class="photo-badge">
                        <i class='bx bx-check'></i>
                    </div>
                </div>
            </div>

            <div class="profile-info-section">
                <div class="info-item">
                    <span class="info-label">Email</span>
                    <span class="info-value"><?= $userName; ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">NIM</span>
                    <span class="info-value"><?= $stambuk; ?></span>
                </div>
            </div>

            <div class="profile-actions">
                <button type="button" class="btn btn-primary" id="editProfileButton">
                    <i class='bx bx-edit-alt'></i>
                    Edit Profile
                </button>
                <button type="button" class="btn btn-danger" id="logoutButton">
                    <i class='bx bx-log-out'></i>
                    Logout
                </button>
            </div>
        </div>

        <!-- Details Card -->
        <div class="details-card">
            <div class="details-header">
                <h2>Biodata Mahasiswa</h2>
            </div>

            <div class="details-grid">
                <div class="detail-item">
                    <span class="detail-label">Nama Lengkap</span>
                    <span class="detail-value"><?= $nama; ?></span>
                </div>

                <div class="detail-item">
                    <span class="detail-label">NIM</span>
                    <span class="detail-value"><?= $stambuk; ?></span>
                </div>

                <div class="detail-item">
                    <span class="detail-label">Jurusan</span>
                    <span class="detail-value"><?= $jurusan; ?></span>
                </div>

                <div class="detail-item">
                    <span class="detail-label">Kelas</span>
                    <span class="detail-value"><?= $kelas; ?></span>
                </div>

                <div class="detail-item">
                    <span class="detail-label">Alamat</span>
                    <span class="detail-value"><?= $alamat; ?></span>
                </div>

                <div class="detail-item">
                    <span class="detail-label">Jenis Kelamin</span>
                    <span class="detail-value"><?= $jenisKelamin; ?></span>
                </div>

                <div class="detail-item">
                    <span class="detail-label">Tempat Lahir</span>
                    <span class="detail-value"><?= $tempatLahir; ?></span>
                </div>

                <div class="detail-item">
                    <span class="detail-label">Tanggal Lahir</span>
                    <span class="detail-value"><?= $tanggalLahir; ?></span>
                </div>

                <div class="detail-item">
                    <span class="detail-label">No Telephone</span>
                    <span class="detail-value"><?= $noHp; ?></span>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Edit Profile Modal -->
<div id="editProfileModal" class="modal-overlay">
    <div class="modal-container">
        <div class="modal-header">
            <h2>Edit Profile</h2>
            <button type="button" class="modal-close" id="closeModaledit">
                <i class='bx bx-x'></i>
            </button>
        </div>

        <form id="editProfileForm" class="modal-form">
            <div class="form-grid">
                <div class="form-group">
                    <label for="nama">Nama Lengkap</label>
                    <input type="text" id="nama" name="nama" value="<?= $nama; ?>" required>
                </div>

                <div class="form-group">
                    <label for="jurusan">Jurusan</label>
                    <select id="jurusan" name="jurusan" required>
                        <option value="Teknik Informatika" <?= $jurusan === 'Teknik Informatika' ? 'selected' : ''; ?>>
                            Teknik Informatika
                        </option>
                        <option value="Sistem Informasi" <?= $jurusan === 'Sistem Informasi' ? 'selected' : ''; ?>>
                            Sistem Informasi
                        </option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="kelas">Kelas</label>
                    <select id="kelas" name="kelas" required></select>
                </div>

                <div class="form-group">
                    <label for="alamat">Alamat</label>
                    <input type="text" id="alamat" name="alamat" value="<?= $alamat; ?>" required>
                </div>

                <div class="form-group">
                    <label for="jenisKelamin">Jenis Kelamin</label>
                    <select id="jenisKelamin" name="jenisKelamin" required>
                        <option value="Pria" <?= $jenisKelamin === "Pria" ? "selected" : ""; ?>>Pria</option>
                        <option value="Wanita" <?= $jenisKelamin === "Wanita" ? "selected" : ""; ?>>Wanita</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="tempatLahir">Tempat Lahir</label>
                    <input type="text" id="tempatLahir" name="tempatLahir" value="<?= $tempatLahir; ?>" required>
                </div>

                <div class="form-group">
                    <label for="tanggalLahir">Tanggal Lahir</label>
                    <input type="date" id="tanggalLahir" name="tanggalLahir" value="<?= $tanggalLahir; ?>" required>
                </div>

                <div class="form-group">
                    <label for="noHp">No Telephone</label>
                    <input type="text" id="noHp" name="noHp" value="<?= $noHp; ?>" required>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class='bx bx-save'></i>
                    Save Changes
                </button>
                <button type="button" class="btn btn-secondary" id="cancelEdit">
                    <i class='bx bx-x'></i>
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function showModal(message, gifUrl = null) {
    const modal = document.getElementById("customModal");
    const modalMessage = document.getElementById("modalMessage");
    const modalGif = document.getElementById("modalGif");
    const closeModal = document.getElementById("closeModal");

    modalMessage.textContent = message;

    if (gifUrl) {
        modalGif.src = gifUrl;
        modalGif.style.display = "block";
    } else {
        modalGif.style.display = "none";
    }

    modal.style.display = "flex";

    closeModal.addEventListener("click", () => {
        modal.style.display = "none";
    });

    window.addEventListener("click", (event) => {
        if (event.target === modal) {
            modal.style.display = "none";
        }
    });
}

function validatePhoneNumber(phoneNumber) {
    const phoneRegex = /^(?:\+62|62|0)(8[1-9][0-9]{6,9})$/;
    return phoneRegex.test(phoneNumber) 
        ? { success: true, message: "Nomor telepon valid." }
        : { success: false, message: "Nomor telepon tidak valid." };
}

function validateNoNumber(input) {
    const noNumberRegex = /^[A-Za-z\s]*$/;
    return noNumberRegex.test(input)
        ? { success: true, message: "Input valid: Tidak ada angka." }
        : { success: false, message: "Input tidak valid: tidak boleh mengandung angka." };
}

$(document).ready(function () {
    const phoneInput = document.getElementById("noHp");
    const namaInput = document.getElementById("nama");
    const tempatLahirInput = document.getElementById("tempatLahir");
    
    phoneInput.addEventListener("input", function () {
        phoneInput.setCustomValidity("");
        phoneInput.reportValidity();
    });
    
    namaInput.addEventListener("input", function () {
        namaInput.setCustomValidity("");
        namaInput.reportValidity();
    });
    
    tempatLahirInput.addEventListener("input", function () {
        tempatLahirInput.setCustomValidity("");
        tempatLahirInput.reportValidity();
    });

    $('#editProfileButton').click(function () {
        $('#editProfileModal').addClass('active');
        updateKelasOptions();
    });

    $('#closeModaledit, #cancelEdit').click(function () {
        $('#editProfileModal').removeClass('active');
    });

    $(window).click(function (event) {
        if ($(event.target).is('#editProfileModal')) {
            $('#editProfileModal').removeClass('active');
        }
    });

    $('#jenisKelamin').on('change', function () {
        updateKelasOptions();
    });

    $('#logoutButton').click(function (e) {
        e.preventDefault();
        $.ajax({
            url: '/tubes_web/public/logout',
            type: 'POST',
            success: function (response) {
                if (response.status === 'success') {
                    showModal('Logout berhasil', '/tubes_web/public/Assets/gif/success.gif');
                    setTimeout(() => {
                        window.location.href = '/tubes_web/public/';
                        window.location.reload();
                    }, 1000);
                } else {
                    showModal('Logout gagal', '/tubes_web/public/Assets/gif/failed.gif');
                }
            },
            error: function (xhr, status, error) {
                console.log('Error:', xhr.responseText);
                alert('Terjadi kesalahan: ' + error);
            }
        });
    });

    $('#editProfileForm').submit(function (e) {
        e.preventDefault();
        
        const formData = {
            nama: $('#nama').val(),
            jurusan: $('#jurusan').val(),
            kelas: $('#kelas').val(),
            alamat: $('#alamat').val(),
            jenisKelamin: $('#jenisKelamin').val(),
            tempatLahir: $('#tempatLahir').val(),
            tanggalLahir: $('#tanggalLahir').val(),
            noHp: $('#noHp').val()
        };

        const phoneNumber = document.getElementById("noHp").value;
        const tempatLahir = document.getElementById("tempatLahir").value;
        const nama = document.getElementById("nama").value;

        let isValid = true;

        if (!validateNoNumber(nama).success) {
            namaInput.setCustomValidity(validateNoNumber(nama).message);
            namaInput.reportValidity();
            isValid = false;
        }
        
        if (!validateNoNumber(tempatLahir).success) {
            tempatLahirInput.setCustomValidity(validateNoNumber(tempatLahir).message);
            tempatLahirInput.reportValidity();
            isValid = false;
        }
        
        if (!validatePhoneNumber(phoneNumber).success) {
            phoneInput.setCustomValidity(validatePhoneNumber(phoneNumber).message);
            phoneInput.reportValidity();
            isValid = false;
        }

        if (!isValid) return;

        $.ajax({
            url: '/tubes_web/public/updatebiodata',
            method: 'POST',
            data: formData,
            success: function (response) {
                try {
                    const parsedResponse = typeof response === 'string' ? JSON.parse(response) : response;
                    if (parsedResponse.status === 'success') {
                        showModal('Data berhasil diperbarui', '/tubes_web/public/Assets/gif/success.gif');
                        document.querySelector('a[data-page="profile"]').click();
                    } else {
                        showModal('Data gagal diperbarui', '/tubes_web/public/Assets/gif/failed.gif');
                        document.querySelector('a[data-page="profile"]').click();
                    }
                } catch (error) {
                    console.error('Error parsing response:', error);
                }
            }
        });
    });

    function updateKelasOptions() {
        const gender = document.getElementById('jenisKelamin').value;
        const kelasSelect = document.getElementById('kelas');

        kelasSelect.innerHTML = '<option value="">Pilih Kelas</option>';

        const laki = ['A1', 'A2', 'A3', 'A4', 'A5', 'A6', 'A7', 'A8', 'A9', 'A10'];
        const perempuan = ['B1', 'B2', 'B3', 'B4', 'B5', 'B6', 'B7'];

        const kelasOptions = gender === 'Wanita' ? perempuan : laki;

        kelasOptions.forEach(kelas => {
            const option = document.createElement('option');
            option.value = kelas;
            option.textContent = kelas;
            kelasSelect.appendChild(option);
        });
    }
});
</script>