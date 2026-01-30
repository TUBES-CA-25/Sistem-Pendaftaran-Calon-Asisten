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

<main class="container-fluid px-4 pb-4">
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

<script src="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Script/user/profil.js"></script> 