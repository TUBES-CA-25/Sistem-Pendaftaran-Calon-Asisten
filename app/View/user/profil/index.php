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
$photo = $photo ?? '/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Downloads/default.png';
?>

<!-- Page Header -->
<?php
    $title = 'Profile';
    $subtitle = 'Informasi akun dan pengaturan';
    $icon = 'bx bx-user-circle';
    require_once __DIR__ . '/../../templates/components/PageHeader.php';
?>

<main class="container-fluid px-4 pb-4">
    <div class="row g-4 justify-content-center">
        <!-- Profile Card -->
        <div class="col-12 col-lg-10">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4 p-md-5">
                    <div class="row align-items-center">
                        <div class="col-auto position-relative profile-img-container">
                             <div class="position-relative d-inline-block" style="width: 120px; height: 120px;">
                                <img src="<?= $photo ?>" alt="Profile Picture" class="rounded-4 object-fit-cover w-100 h-100 profile-img-target">
                                <div onclick="document.getElementById('profileImageInput').click();" 
                                     class="position-absolute top-0 start-0 w-100 h-100 bg-dark bg-opacity-50 rounded-4 d-flex flex-column align-items-center justify-content-center text-white opacity-0 profile-overlay" 
                                     style="transition: opacity 0.2s; cursor: pointer;">
                                    <i class="bx bx-camera fs-3 mb-1"></i>
                                    <small class="fw-bold" style="font-size: 0.7rem;">Ubah Foto</small>
                                </div>
                            </div>
                            <input type="file" id="profileImageInput" class="d-none" accept="image/*">
                            <style>
                                .profile-img-container:hover .profile-overlay { opacity: 1 !important; }
                            </style>
                        </div>
                        <div class="col">
                            <h4 class="fw-bold text-primary mb-1"><?= $nama ?></h4>
                            <p class="text-muted mb-0"><?= $userName ?></p>
                            <p class="text-muted mb-0"><small><i class="bx bx-id-card me-1"></i><?= $stambuk ?></small></p>
                        </div>
                        <div class="col-auto mt-3 mt-md-0">
                            <div class="d-flex gap-2">
                                <a href="/Sistem-Pendaftaran-Calon-Asisten/public/biodata" class="btn btn-primary px-4 rounded-3 d-flex align-items-center gap-2">
                                    <i class="bx bx-edit"></i> Edit Profile
                                </a>
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

<!-- Clean Script (No Modal, No Form Submit for Edit Profile) -->
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

    function validateTempatLahir(input) {
        // Tempat lahir boleh mengandung huruf, angka, dan spasi
        const tempatLahirRegex = /^[A-Za-z0-9\s,.-]*$/;
        if (!tempatLahirRegex.test(input)) {
            return { success: false, message: "Tempat lahir tidak valid." };
        }
        return { success: true, message: "Tempat lahir valid." };
    }
    $(document).ready(function () {
        $('#logoutButton').click(function (e) {
            e.preventDefault();
            $.ajax({
                url: '/Sistem-Pendaftaran-Calon-Asisten/public/logout',
                type: 'POST',
                success: function (response) {
                    const res = (typeof response === 'string') ? JSON.parse(response) : response;
                    if (res.status === 'success') {
                         window.location.href = '/Sistem-Pendaftaran-Calon-Asisten/public/';
                    } else {
                        showAlert('Logout gagal', false);
                    }
                }
            });
        });

        // Profile Image Upload Handler (Change Event)
        $('#profileImageInput').change(function() {
            if (this.files && this.files[0]) {
                const formData = new FormData();
                formData.append('image', this.files[0]);
                
                const $overlay = $('.profile-overlay');
                const originalContent = $overlay.html();


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
                    } catch(e) {
                         console.error('JSON Parse Error:', e);
                         showModal('Error parsing response from server.', null);
                    }
                },
                error: function(xhr, status, error) {
                     console.error('AJAX Error:', error);
                     showModal('Terjadi kesalahan koneksi saat mengunggah gambar.', null);
                }
            });
            }
        });
    });
</script>