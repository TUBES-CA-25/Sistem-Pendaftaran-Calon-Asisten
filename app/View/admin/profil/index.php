<?php
/**
 * Admin Profile View
 */
$userName = $userName ?? 'Admin';
$nama = $nama ?? 'Administrator';
$photo = $photo ?? '/Sistem-Pendaftaran-Calon-Asisten/res/imageUser/default.png';
?>

<!-- Page Header -->
<?php
    $title = 'Profile Admin';
    $subtitle = 'Informasi akun administrator';
    $icon = 'bx bx-user-circle';
    require_once __DIR__ . '/../../templates/components/PageHeader.php';
?>

<main class="container-fluid pb-4">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-8">
            <!-- Profile Card -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-4 p-md-5">
                    <div class="row align-items-center text-center text-md-start">
                        <!-- Profile Image -->
                        <div class="col-md-auto mb-3 mb-md-0 position-relative">
                            <img src="<?= $photo ?>" alt="Profile Picture" 
                                class="rounded-circle border border-4 border-light shadow-sm bg-white" 
                                style="width: 140px; height: 140px; object-fit: cover;">
                        </div>
                        
                        <!-- Profile Info -->
                        <div class="col-md ms-md-3">
                            <h2 class="fw-bold text-dark mb-1"><?= htmlspecialchars($userName) ?></h2>
                            <p class="text-muted mb-3 fs-5">Administrator System</p>
                            <span class="badge bg-primary px-3 py-2 rounded-pill shadow-sm">
                                <i class="bx bx-shield-quarter me-1"></i> Admin
                            </span>
                        </div>
                        
                        <!-- Actions -->
                        <div class="col-md-auto mt-4 mt-md-0 d-flex flex-column flex-md-row gap-2 justify-content-center">
                            <button id="editProfileBtn" class="btn btn-warning text-white px-4 py-2 rounded-pill fw-semibold shadow-sm" 
                                    data-bs-toggle="modal" data-bs-target="#editProfileModal">
                                <i class='bx bx-edit-alt me-2'></i> Edit Profile
                            </button>
                            <button id="logoutButton" class="btn btn-danger px-4 py-2 rounded-pill fw-semibold shadow-sm">
                                <i class='bx bx-log-out-circle me-2'></i> Logout
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</main>

<!-- Edit Profile Modal -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold">Edit Profile</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-4">
                <div id="alertMessage" class="alert d-none" role="alert"></div>
                <form id="editProfileForm" enctype="multipart/form-data">
                    <div class="mb-4">
                        <label for="inputUsername" class="form-label fw-semibold text-secondary">Username</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class='bx bx-user'></i></span>
                            <input type="text" class="form-control border-start-0 ps-0 bg-light" id="inputUsername" name="username" value="<?= htmlspecialchars($userName) ?>" required>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label for="inputPhoto" class="form-label fw-semibold text-secondary">Foto Profil</label>
                        <input type="file" class="form-control" id="inputPhoto" name="photo" accept="image/png, image/jpeg, image/jpg">
                        <div class="form-text text-muted small"><i class='bx bx-info-circle me-1'></i>Format: JPG, JPEG, PNG. Max 5MB.</div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-top-0 pt-0 pb-4 pe-4">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm" id="saveProfileBtn">
                    <span id="btnText">Simpan Perubahan</span>
                    <span id="btnLoader" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                </button>
            </div>
        </div>
    </div>
</div>
<!-- End Modal -->

<script>
    $(document).ready(function () {
        // Logout Handler
        $('#logoutButton').click(function (e) {
            e.preventDefault();
            
            // SweetAlert2 style confirmation if available, else standard confirm
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Konfirmasi Logout',
                    text: "Apakah anda yakin ingin keluar dari sistem?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Logout',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        performLogout();
                    }
                })
            } else {
                if(confirm('Apakah anda yakin ingin keluar?')) {
                    performLogout();
                }
            }
        });

        function performLogout() {
             $.ajax({
                url: '/Sistem-Pendaftaran-Calon-Asisten/public/logout',
                type: 'POST',
                success: function (response) {
                    window.location.href = '/Sistem-Pendaftaran-Calon-Asisten/public/';
                },
                error: function (xhr) {
                    alert('Logout gagal. Silakan coba lagi.');
                }
            });
        }

        // Save Profile Handler
        $('#saveProfileBtn').click(function() {
            var btn = $(this);
            var btnText = $('#btnText');
            var btnLoader = $('#btnLoader');
            var alertBox = $('#alertMessage');
            
            // Reset Alert
            alertBox.addClass('d-none').removeClass('alert-success alert-danger');
            
            // Loading State
            btn.prop('disabled', true);
            btnText.addClass('d-none');
            btnLoader.removeClass('d-none');

            var formData = new FormData($('#editProfileForm')[0]);
            
            $.ajax({
                url: '/Sistem-Pendaftaran-Calon-Asisten/public/updateadminprofile',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    if(response.status === 'success') {
                        alertBox.removeClass('d-none alert-danger').addClass('alert-success').html('<i class="bx bx-check-circle me-2"></i>' + response.message);
                        setTimeout(function() {
                            location.reload(); 
                        }, 1000);
                    } else {
                        alertBox.removeClass('d-none alert-success').addClass('alert-danger').html('<i class="bx bx-error-circle me-2"></i>' + response.message);
                        btn.prop('disabled', false);
                        btnText.removeClass('d-none');
                        btnLoader.addClass('d-none');
                    }
                },
                error: function(xhr) {
                    alertBox.removeClass('d-none alert-success').addClass('alert-danger').html('<i class="bx bx-wifi-off me-2"></i>Terjadi kesalahan server.');
                    btn.prop('disabled', false);
                    btnText.removeClass('d-none');
                    btnLoader.addClass('d-none');
                    console.error(xhr.responseText);
                }
            });
        });
    });
</script>
