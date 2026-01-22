<?php
/**
 * Profile View
 * 
 * Data yang diterima dari controller:
 * @var string $userName - Username user
 * @var string $nama - Nama lengkap
 * @var string $stambuk - Stambuk
 * @var string $jurusan - Jurusan
 * @var string $alamat - Alamat
 * @var string $kelas - Kelas
 * @var string $jenisKelamin - Jenis kelamin
 * @var string $tempatLahir - Tempat lahir
 * @var string $tanggalLahir - Tanggal lahir
 * @var string $noHp - No HP
 * @var string $photo - Path foto
 */
$userName = $userName ?? 'Guest';
$nama = $nama ?? 'Nama Lengkap';
$stambuk = $stambuk ?? '';
$jurusan = $jurusan ?? 'Jurusan';
$alamat = $alamat ?? 'Alamat';
$kelas = $kelas ?? 'Kelas';
$jenisKelamin = $jenisKelamin ?? 'Jenis Kelamin';
$tempatLahir = $tempatLahir ?? 'Tempat Lahir';
$tanggalLahir = $tanggalLahir ?? 'Tanggal Lahir';
$noHp = $noHp ?? 'No Telephone';
$photo = $photo ?? '/Sistem-Pendaftaran-Calon-Asisten/res/imageUser/default.png';
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

<!-- Page Header -->
<?php
    $title = 'Profile';
    $subtitle = 'Informasi akun dan pengaturan';
    $icon = 'bx bx-user-circle';
    require_once __DIR__ . '/../../templates/components/PageHeader.php';
?>

<main class="pb-4" style="margin-top: -30px; position: relative; z-index: 10;">
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
                <div class="form-group">
                    <label for="username">Username (Email)</label>
                    <input type="text" id="username" name="username" value="<?= $userName; ?>">
                </div>
                <div class="form-group">
                    <label for="password">Password (Kosongkan jika tidak ingin diubah)</label>
                    <input type="password" id="password" name="password" placeholder="Password Baru">
                </div>
                <div class="form-group">
                    <label for="image">Foto Profil</label>
                    <input type="file" id="image" name="image" accept="image/*">
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
        const modalEl = document.getElementById("customModal");
        if (!modalEl) return;

        const modalMessage = document.getElementById("modalMessage");
        const modalGif = document.getElementById("modalGif");

        if (modalMessage) modalMessage.textContent = message;
        if (modalGif) {
            modalGif.style.display = gifUrl ? "block" : "none";
            if (gifUrl) modalGif.src = gifUrl;
        }

        // Use Bootstrap Modal API
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();
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
        
        const safeListeners = (el) => {
            if (el) {
                el.addEventListener("input", function () {
                    el.setCustomValidity("");
                    el.reportValidity();
                });
            }
        };

        safeListeners(phoneInput);
        safeListeners(namaInput);
        safeListeners(tempatLahirInput);

        $('#editProfileButton').click(function () {
            $('#editProfileModal').css('display', 'flex');
            updateKelasOptions();
            // Set current values for username
            const currentUsername = "<?= $userName ?>";
            if($('#username').length === 0) {
                // Add username and password fields if they don't exist (legacy fallback)
                // In a real scenario, we'd update the HTML once
            }
        });

        $('#closeModaledit').click(function () {
            $('#editProfileModal').css('display', 'none');
        });

        $(window).click(function (event) {
            if ($(event.target).is('#editProfileModal')) {
                $('#editProfileModal').css('display', 'none'); // User usually expects clicks outside to CLOSE
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
                        }, 1000);
                    } else {
                        showModal('Logout gagal', '/Sistem-Pendaftaran-Calon-Asisten/public/Assets/gif/failed.gif');
                    }
                }
            });
        });

        $('#editProfileForm').submit(function (e) {
            e.preventDefault();
            
            const formData = new FormData();
            formData.append('nama', $('#nama').val());
            formData.append('jurusan', $('#jurusan').val());
            formData.append('kelas', $('#kelas').val());
            formData.append('alamat', $('#alamat').val());
            formData.append('jenisKelamin', $('#jenisKelamin').val());
            formData.append('tempatLahir', $('#tempatLahir').val());
            formData.append('tanggalLahir', $('#tanggalLahir').val());
            formData.append('noHp', $('#noHp').val());
            
            // Password & Username (if added to form)
            if($('#username').length) formData.append('username', $('#username').val());
            if($('#password').length) formData.append('password', $('#password').val());
            
            // File upload (if added to form)
            if($('#image').length && $('#image')[0].files[0]) {
                formData.append('image', $('#image')[0].files[0]);
            }

            let isValid = true;
            if (namaInput && !validateNoNumber(namaInput.value).success) {
                namaInput.setCustomValidity(validateNoNumber(namaInput.value).message);
                namaInput.reportValidity();
                isValid = false;
            }
            if (tempatLahirInput && !validateNoNumber(tempatLahirInput.value).success) {
                tempatLahirInput.setCustomValidity(validateNoNumber(tempatLahirInput.value).message);
                tempatLahirInput.reportValidity();
                isValid = false;
            }
            if (phoneInput && !validatePhoneNumber(phoneInput.value).success) {
                phoneInput.setCustomValidity(validatePhoneNumber(phoneInput.value).message);
                phoneInput.reportValidity();
                isValid = false;
            }

            if (!isValid) return;

            $.ajax({
                url: '/Sistem-Pendaftaran-Calon-Asisten/public/updateprofile',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    try {
                        const res = typeof response === 'string' ? JSON.parse(response) : response;
                        if (res.status === 'success') {
                            showModal(res.message || 'Profil berhasil diperbarui', '/Sistem-Pendaftaran-Calon-Asisten/public/Assets/gif/success.gif');
                            setTimeout(() => {
                                // Reload page content to show changes
                                if(window.loadPage) loadPage('profile');
                                else window.location.reload();
                            }, 1500);
                        } else {
                            showModal(res.message || 'Gagal memperbarui profil', '/Sistem-Pendaftaran-Calon-Asisten/public/Assets/gif/failed.gif');
                        }
                    } catch (e) {
                        console.error('Error parsing response:', e);
                    }
                }
            });
        });

        function updateKelasOptions() {
            const genderSelect = document.getElementById('jenisKelamin');
            const kelasSelect = document.getElementById('kelas');
            if(!genderSelect || !kelasSelect) return;

            const gender = genderSelect.value;
            const currentKelas = "<?= $kelas ?>";

            kelasSelect.innerHTML = '<option value="">Pilih Kelas</option>';

            const kelasOptions = gender === 'Wanita'
                ? ['B1', 'B2', 'B3', 'B4', 'B5', 'B6']
                : ['A1', 'A2', 'A3', 'A4', 'A5', 'A6', 'A7', 'A8', 'A9'];
            
            kelasOptions.forEach(kelas => {
                const option = document.createElement('option');
                option.value = kelas;
                option.textContent = kelas;
                if(kelas === currentKelas) option.selected = true;
                kelasSelect.appendChild(option);
            });
        }
    });
</script>