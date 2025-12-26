<?php
use App\Controllers\Profile\ProfileController;
use App\Controllers\user\BerkasUserController;

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
$photo = "/Sistem-Pendaftaran-Calon-Asisten/res/imageUser/" . (BerkasUserController::viewPhoto()["foto"] ?? "default.png");
?>

<style>
    .modal-edit {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        justify-content: center;
        align-items: center;
        z-index: 1000;
    }

    .modal-edit-content {
        background: #fff;
        padding: 25px;
        height: auto;
        border-radius: 12px;
        max-width: 700px;
        width: 90%;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        animation: slideIn 0.3s ease-out;
    }

    @keyframes slideIn {
        from {
            transform: translateY(-20px);
            opacity: 0;
        }

        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    h2 {
        margin-bottom: 20px;
        font-size: 24px;
        color: #333;
        text-align: center;
    }

    /* Grid layout for form */
    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        /* Two columns */
        gap: 20px;
        /* Spacing between grid items */
    }

    .form-group {
        margin-bottom: 10px;
    }

    label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #555;
    }

    input[type="text"],
    input[type="date"],
    select {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 14px;
        transition: border-color 0.3s ease;
    }

    input[type="text"]:focus,
    input[type="date"]:focus,
    select:focus {
        border-color: #007BFF;
        outline: none;
    }

    /* Buttons */
    .form-actions {
        grid-column: span 2;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 20px;
    }

    button {
        padding: 12px 20px;
        border: none;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    button[type="submit"] {
        padding: 12px 20px;
        font-size: 14px;
        background: #007BFF;
        color: #fff;
        border-radius: 6px;
        cursor: pointer;
        border: none;
        transition: background-color 0.3s ease;
    }

    button[type="submit"]:hover {
        background: #0056b3;
    }

    #closeModaledit {
        padding: 6px 14px;
        font-size: 12px;
        background: #FF0000;
        color: #fff;
        border-radius: 6px;
        cursor: pointer;
        border: none;
        transition: background-color 0.3s ease;
    }

    #closeModaledit:hover {
        background: #cc0000;
    }
</style>

<main>
    <h1 class="dashboard">Profile</h1>
    <div class="profile-container"
        style="display: grid; grid-template-columns: 1fr; gap: 2rem; padding: 2.5rem; max-width: 900px; margin: 0 auto;">
        <div class="profile-card"
            style="background-color: #fff; border-radius: 20px; box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1); padding: 2.5rem; display: grid; grid-template-columns: auto 1fr auto; gap: 1.5rem; align-items: center;">
            <img src="<?= $photo ?>" alt="Profile Picture"
                style="width: 150px; height: 150px; object-fit: cover; border-radius: 15px;">
            <div style="font-size: 1.1rem;">
                <p>Email: <strong><?= $userName; ?></strong></p>
                <p>NIM: <strong><?= $stambuk; ?></strong></p>
            </div>
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <button type="button" class="btn" id="editProfileButton"
                    style="background-color: #007BFF; color: white; padding: 0.75rem 1.5rem; border: none; border-radius: 8px; font-size: 1rem; cursor: pointer;">Edit
                    Profile</button>
                <button id="logoutButton" class="btn"
                    style="background-color: #FF0000; color: white; padding: 0.75rem 1.5rem; border: none; border-radius: 8px; font-size: 1rem; cursor: pointer;">Logout</button>
            </div>
        </div>

        <div class="details-card"
            style="background-color: #fff; border-radius: 20px; box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1); padding: 2.5rem; display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
            <div>
                <p style="font-size: 1rem; color: #333; margin-bottom: 0.5rem;">Nama Lengkap</p>
                <p style="font-size: 1.1rem; font-weight: bold; color: #555;"><?= $nama; ?></p>
            </div>
            <div>
                <p style="font-size: 1rem; color: #333; margin-bottom: 0.5rem;">NIM</p>
                <p style="font-size: 1.1rem; font-weight: bold; color: #555;"><?= $stambuk; ?></p>
            </div>
            <div>
                <p style="font-size: 1rem; color: #333; margin-bottom: 0.5rem;">Jurusan</p>
                <p style="font-size: 1.1rem; font-weight: bold; color: #555;"><?= $jurusan; ?></p>
            </div>
            <div>
                <p style="font-size: 1rem; color: #333; margin-bottom: 0.5rem;">Kelas</p>
                <p style="font-size: 1.1rem; font-weight: bold; color: #555;"><?= $kelas; ?></p>
            </div>
            <div>
                <p style="font-size: 1rem; color: #333; margin-bottom: 0.5rem;">Alamat</p>
                <p style="font-size: 1.1rem; font-weight: bold; color: #555;"><?= $alamat; ?></p>
            </div>
            <div>
                <p style="font-size: 1rem; color: #333; margin-bottom: 0.5rem;">Jenis Kelamin</p>
                <p style="font-size: 1.1rem; font-weight: bold; color: #555;"><?= $jenisKelamin; ?></p>
            </div>
            <div>
                <p style="font-size: 1rem; color: #333; margin-bottom: 0.5rem;">Tempat Lahir</p>
                <p style="font-size: 1.1rem; font-weight: bold; color: #555;"><?= $tempatLahir; ?></p>
            </div>
            <div>
                <p style="font-size: 1rem; color: #333; margin-bottom: 0.5rem;">Tanggal Lahir</p>
                <p style="font-size: 1.1rem; font-weight: bold; color: #555;"><?= $tanggalLahir; ?></p>
            </div>
            <div>
                <p style="font-size: 1rem; color: #333; margin-bottom: 0.5rem;">No Telephone</p>
                <p style="font-size: 1.1rem; font-weight: bold; color: #555;"><?= $noHp; ?></p>
            </div>
        </div>
    </div>
</main>



<div id="editProfileModal" class="modal-edit" style="display: none;">
    <div class="modal-edit-content">
        <h2>Edit Profile</h2>
        <form id="editProfileForm">
            <div class="form-grid">
                <div class="form-group">
                    <label for="nama">Nama Lengkap</label>
                    <input type="text" id="nama" name="nama" value="<?= $nama; ?>">
                </div>
                <div class="form-group">
                    <label for="jurusan">Jurusan</label>
                    <select id="jurusan" name="jurusan">
                        <option value="Teknik Informatika" <?= $jurusan === 'Teknik Informatika' ? 'selected' : ''; ?>>
                            Teknik Informatika</option>
                        <option value="Sistem Informasi" <?= $jurusan === 'Sistem Informasi' ? 'selected' : ''; ?>>Sistem
                            Informasi</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="kelas">Kelas</label>
                    <select id="kelas" name="kelas" required></select>
                </div>
                <div class="form-group">
                    <label for="alamat">Alamat</label>
                    <input type="text" id="alamat" name="alamat" value="<?= $alamat; ?>">
                </div>
                <div class="form-group">
                    <label for="jenisKelamin">Jenis Kelamin</label>
                    <select id="jenisKelamin" name="jenisKelamin">
                        <option value="Pria" <?= $jenisKelamin === "Pria" ? "selected" : ""; ?>>Pria</option>
                        <option value="Wanita" <?= $jenisKelamin === "Wanita" ? "selected" : ""; ?>>Wanita</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="tempatLahir">Tempat Lahir</label>
                    <input type="text" id="tempatLahir" name="tempatLahir" value="<?= $tempatLahir; ?>">
                </div>
                <div class="form-group">
                    <label for="tanggalLahir">Tanggal Lahir</label>
                    <input type="date" id="tanggalLahir" name="tanggalLahir" value="<?= $tanggalLahir; ?>">
                </div>
                <div class="form-group">
                    <label for="noHp">No Telephone</label>
                    <input type="text" id="noHp" name="noHp" value="<?= $noHp; ?>">
                </div>
            </div>
            <div class="form-actions">
                <button type="submit">Save Changes</button>
                <button type="reset" id="closeModaledit">Cancel</button>
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

        if (!phoneRegex.test(phoneNumber)) {
            return {
                success: false,
                message:
                    "nomor telepon tidak valid.",
            };
        }

        return { success: true, message: "Nomor telepon valid." };
    }

    function validateNoNumber(input) {
        const noNumberRegex = /^[A-Za-z\s]*$/;

        if (!noNumberRegex.test(input)) {
            return {
                success: false,
                message: "Input tidak valid: tidak boleh mengandung angka.",
            };
        }

        return { success: true, message: "Input valid: Tidak ada angka." };
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
            $('#editProfileModal').css('display', 'flex');
            updateKelasOptions();
        });

        $('#closeModaledit').click(function () {
            $('#editProfileModal').css('display', 'none');
        });

        $(window).click(function (event) {
            if ($(event.target).is('#editProfileModal')) {
                $('#editProfileModal').css('display', 'flex');
            }
        });

        $('#jenisKelamin').on('change', function () {
            updateKelasOptions();
        });

        $('#logoutButton').click(function (e) {
            e.preventDefault();
            $.ajax({
                url: '/Sistem-Pendaftaran-Calon-Asisten/public/logout',
                type: 'POST',
                success: function (response) {
                    if (response.status === 'success') {
                        showModal('Logout berhasil', '/Sistem-Pendaftaran-Calon-Asisten/public/Assets/gif/success.gif');
                        setTimeout(() => {
                            window.location.href = '/Sistem-Pendaftaran-Calon-Asisten/public/';
                            window.location.reload();
        }, 1000);
                        
                    } else {
                        showModal('Logout gagal', '/Sistem-Pendaftaran-Calon-Asisten/public/Assets/gif/failed.gif');
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

            console.log("Form Data:", formData);
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
                url: '/Sistem-Pendaftaran-Calon-Asisten/public/updatebiodata',
                method: 'POST',
                data: formData,
                success: function (response) {
                    try {
                        const parsedResponse = typeof response === 'string' ? JSON.parse(response) : response;
                        console.log('Parsed Response:', parsedResponse);

                        if (parsedResponse.status === 'success') {
                            showModal('Data berhasil diperbarui', '/Sistem-Pendaftaran-Calon-Asisten/public/Assets/gif/success.gif');
                            document.querySelector('a[data-page="profile"]').click();
                        } else {
                            console.log('Error:', parsedResponse.message);
                            showModal('Data gagal diperbarui', '/Sistem-Pendaftaran-Calon-Asisten/public/Assets/gif/failed.gif');
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

            const kelasOptions = gender === 'Wanita'
                ? ['B1', 'B2', 'B3', 'B4', 'B5', 'B6']
                : ['A1', 'A2', 'A3', 'A4', 'A5', 'A6', 'A7', 'A8', 'A9'];
            kelasOptions.forEach(kelas => {
                const option = document.createElement('option');
                option.value = kelas;
                option.textContent = kelas;
                kelasSelect.appendChild(option);
            });
        }
    });
</script>