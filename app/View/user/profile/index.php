<?php
/**
 * Profile View - Bootstrap 5 Refactor
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

<!-- Page Header -->
<?php
    $title = 'Profile';
    $subtitle = 'Informasi akun dan pengaturan';
    $icon = 'bx bx-user-circle';
    require_once __DIR__ . '/../../templates/components/PageHeader.php';
?>

<main class="container-fluid pb-4" style="margin-top: -30px; position: relative; z-index: 10;">
    <div class="row g-4 justify-content-center">
        <!-- Profile Card -->
        <div class="col-12 col-lg-10">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4 p-md-5">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <img src="<?= $photo ?>" alt="Profile Picture" class="rounded-4 object-fit-cover" style="width: 120px; height: 120px;">
                        </div>
                        <div class="col">
                            <h4 class="fw-bold text-primary mb-1"><?= $nama ?></h4>
                            <p class="text-muted mb-0"><?= $userName ?></p>
                            <p class="text-muted mb-0"><small><i class="bx bx-id-card me-1"></i><?= $stambuk ?></small></p>
                        </div>
                        <div class="col-auto mt-3 mt-md-0">
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-primary px-4 rounded-3 d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                                    <i class="bx bx-edit"></i> Edit Profile
                                </button>
                                <button type="button" id="logoutButton" class="btn btn-danger px-4 rounded-3 d-flex align-items-center gap-2">
                                    <i class="bx bx-log-out"></i> Logout
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Details Card -->
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4 p-md-5">
                    <h5 class="card-title fw-bold mb-4 border-bottom pb-2">Informasi Pribadi</h5>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">Nama Lengkap</label>
                            <div class="fw-semibold text-dark"><?= $nama ?></div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">NIM</label>
                            <div class="fw-semibold text-dark"><?= $stambuk ?></div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">Jurusan</label>
                            <div class="fw-semibold text-dark"><?= $jurusan ?></div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">Kelas</label>
                            <div class="fw-semibold text-dark"><?= $kelas ?></div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">Jenis Kelamin</label>
                            <div class="fw-semibold text-dark"><?= $jenisKelamin ?></div>
                        </div>
                         <div class="col-md-6">
                            <label class="text-muted small mb-1">No Telephone</label>
                            <div class="fw-semibold text-dark"><?= $noHp ?></div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">Tempat Lahir</label>
                            <div class="fw-semibold text-dark"><?= $tempatLahir ?></div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">Tanggal Lahir</label>
                            <div class="fw-semibold text-dark"><?= $tanggalLahir ?></div>
                        </div>
                         <div class="col-12">
                            <label class="text-muted small mb-1">Alamat</label>
                            <div class="fw-semibold text-dark"><?= $alamat ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Bootstrap Edit Modal -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 border-0">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold" id="editProfileModalLabel">Edit Profile</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 pt-3">
                <form id="editProfileForm">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="nama" class="form-label small text-muted">Nama Lengkap</label>
                            <input type="text" class="form-control" id="nama" name="nama" value="<?= $nama; ?>">
                        </div>
                        <div class="col-md-6">
                             <label for="noHp" class="form-label small text-muted">No Telephone</label>
                            <input type="text" class="form-control" id="noHp" name="noHp" value="<?= $noHp; ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="jurusan" class="form-label small text-muted">Jurusan</label>
                            <select class="form-select" id="jurusan" name="jurusan">
                                <option value="Teknik Informatika" <?= $jurusan === 'Teknik Informatika' ? 'selected' : ''; ?>>Teknik Informatika</option>
                                <option value="Sistem Informasi" <?= $jurusan === 'Sistem Informasi' ? 'selected' : ''; ?>>Sistem Informasi</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="kelas" class="form-label small text-muted">Kelas</label>
                            <select class="form-select" id="kelas" name="kelas" required></select>
                        </div>
                        <div class="col-md-6">
                            <label for="jenisKelamin" class="form-label small text-muted">Jenis Kelamin</label>
                            <select class="form-select" id="jenisKelamin" name="jenisKelamin">
                                <option value="Pria" <?= $jenisKelamin === "Pria" ? "selected" : ""; ?>>Pria</option>
                                <option value="Wanita" <?= $jenisKelamin === "Wanita" ? "selected" : ""; ?>>Wanita</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                             <label for="username" class="form-label small text-muted">Username (Email)</label>
                             <input type="text" class="form-control" id="username" name="username" value="<?= $userName; ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="tempatLahir" class="form-label small text-muted">Tempat Lahir</label>
                            <input type="text" class="form-control" id="tempatLahir" name="tempatLahir" value="<?= $tempatLahir; ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="tanggalLahir" class="form-label small text-muted">Tanggal Lahir</label>
                            <input type="date" class="form-control" id="tanggalLahir" name="tanggalLahir" value="<?= $tanggalLahir; ?>">
                        </div>
                        <div class="col-12">
                            <label for="alamat" class="form-label small text-muted">Alamat</label>
                            <input type="text" class="form-control" id="alamat" name="alamat" value="<?= $alamat; ?>">
                        </div>
                        <div class="col-12">
                            <label for="password" class="form-label small text-muted">Password (Kosongkan jika tidak ingin diubah)</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Password Baru">
                        </div>
                        <div class="col-12">
                            <label for="image" class="form-label small text-muted">Foto Profil</label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-top-0 pt-0 pb-4 px-4">
                <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="editProfileForm" class="btn btn-primary rounded-3 px-4">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<script>
    function showModal(message, gifUrl = null) {
        // Reuse global sweet alert or Bootstrap Modal if exists
        // For consistency in this refactor, let's use a dynamic Bootstrap modal notification
        let feedbackModalEscaped = new bootstrap.Modal(document.getElementById('editProfileModal')); // Close the edit modal first if open
        
        // Simple alert for now as per previous logic reusing custom modal code logic but adapted
        // Ideally we should use SweetAlert2 or the Toast component, but sticking to logic:
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

    function validatePhoneNumber(phoneNumber) {
        const phoneRegex = /^(?:\+62|62|0)(8[1-9][0-9]{6,9})$/;
        if (!phoneRegex.test(phoneNumber)) {
            return { success: false, message: "Nomor telepon tidak valid." };
        }
        return { success: true, message: "Nomor telepon valid." };
    }

    function validateNoNumber(input) {
        const noNumberRegex = /^[A-Za-z\s]*$/;
        if (!noNumberRegex.test(input)) {
            return { success: false, message: "Input tidak valid: tidak boleh mengandung angka." };
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

        // Update Kelas on Load and Change
        updateKelasOptions();
        $('#jenisKelamin').on('change', function () {
            updateKelasOptions();
        });

        function updateKelasOptions() {
            const genderSelect = document.getElementById('jenisKelamin');
            const kelasSelect = document.getElementById('kelas');
            if(!genderSelect || !kelasSelect) return;

            const gender = genderSelect.value;
            const currentKelas = "<?= $kelas ?>";

            // Clear existing options except default if we want, but here we rebuild
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

        $('#logoutButton').click(function (e) {
            e.preventDefault();
            $.ajax({
                url: '/Sistem-Pendaftaran-Calon-Asisten/public/logout',
                type: 'POST',
                success: function (response) {
                    // Assuming response is JSON
                    const res = (typeof response === 'string') ? JSON.parse(response) : response;
                    if (res.status === 'success') {
                         // We can use the simple modal or just redirect
                         window.location.href = '/Sistem-Pendaftaran-Calon-Asisten/public/';
                    } else {
                        alert('Logout gagal');
                    }
                }
            });
        });

        $('#editProfileForm').submit(function (e) {
            e.preventDefault();
            
            const formData = new FormData(this); // Easier way to capture form data

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
                        // Hide the modal first
                        const modalInstance = bootstrap.Modal.getInstance(document.getElementById('editProfileModal'));
                        if (modalInstance) modalInstance.hide();
                        
                        if (res.status === 'success') {
                            showModal(res.message || 'Profil berhasil diperbarui', '/Sistem-Pendaftaran-Calon-Asisten/public/Assets/gif/success.gif');
                            
                            // Update all profile images on the page immediately
                            if (res.newPhoto) {
                                const newPhotoUrl = res.newPhoto + '?v=' + new Date().getTime();
                                // Select common profile image indicators: navbar img, sidebar icons if any, and page images
                                // FIXED: Removed .sidebar img.icon because it targets the App Logo!
                                $('.navbar-profile-img, .rounded-circle.border-primary img, img.rounded-4, img[alt="Profile Picture"]').attr('src', newPhotoUrl);
                            }

                            setTimeout(() => {
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
    });
</script>