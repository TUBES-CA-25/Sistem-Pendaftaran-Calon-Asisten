<?php
/**
 * Edit Profile View
 */
?>

<!-- Page Header -->
<?php
    $title = 'Edit Profil';
    $subtitle = 'Perbarui informasi akun Anda';
    $icon = 'bx bx-user-circle';
    require_once __DIR__ . '/../../templates/components/PageHeader.php';
?>

<main class="container-fluid px-4 pb-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class='bx bx-edit'></i> Edit Biodata
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form action="" method="POST" enctype="multipart/form-data">
                        <div class="row g-4">
                            <div class="col-12">
                                <label for="username" class="form-label fw-medium">Username</label>
                                <input type="text" class="form-control form-control-lg rounded-3" id="username" name="username" placeholder="Masukkan username">
                            </div>
                            <div class="col-12">
                                <label for="password" class="form-label fw-medium">Password</label>
                                <input type="password" class="form-control form-control-lg rounded-3" id="password" name="password" placeholder="Masukkan password baru">
                                <small class="text-muted">Kosongkan jika tidak ingin mengubah password</small>
                            </div>
                            <div class="col-12">
                                <label for="image" class="form-label fw-medium">Foto Profil</label>
                                <input type="file" class="form-control form-control-lg rounded-3" id="image" name="image" accept="image/*">
                                <small class="text-muted">Format: JPG, PNG. Maksimal 2MB</small>
                            </div>
                            <div class="col-12">
                                <div class="d-flex gap-3">
                                    <button type="submit" class="btn btn-primary btn-lg rounded-3 px-5">
                                        <i class='bx bx-save'></i> Simpan Perubahan
                                    </button>
                                    <button type="reset" class="btn btn-secondary btn-lg rounded-3 px-5">
                                        <i class='bx bx-reset'></i> Reset
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
