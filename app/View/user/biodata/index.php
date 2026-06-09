<?php
/**
 * Biodata View
 *
 * Data yang diterima dari controller:
 * @var string $nama - Nama lengkap user
 * @var string $stambuk - Stambuk user
 * @var string $jurusan - Jurusan user
 * @var string $alamat - Alamat user
 * @var string $kelas - Kelas user
 * @var string $jenisKelamin - Jenis kelamin
 * @var string $tempatLahir - Tempat lahir
 * @var string $tanggalLahir - Tanggal lahir
 * @var string $noHp - No HP
 * @var bool $isBiodataEmpty - Status biodata kosong
 */
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
$isBiodataEmpty = $isBiodataEmpty ?? true;
?>


<!-- Page Header -->
<?php
    $title = 'Biodata';
    $subtitle = 'Lengkapi data diri Anda';
    $icon = 'bx bxs-id-card';
    require_once __DIR__ . '/../../templates/components/PageHeader.php';
?>

<main class="container-fluid px-4 pb-4">
    <div class="row g-4">
        <!-- Left Column: Profile Card -->
        <div class="col-12 col-md-4 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 text-center h-100">
                <div class="card-body p-4 d-flex flex-column align-items-center justify-content-center">
                    <div class="position-relative d-inline-block mb-3 profile-img-container" style="width: 130px; height: 130px;">
                        <img src="<?= $photo ?>" alt="Profile Picture" class="rounded-4 object-fit-cover w-100 h-100 profile-img-target" onerror="this.src='/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Downloads/default.png'">
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
                    <h5 class="fw-bold text-primary mb-1 text-wrap text-break"><?= htmlspecialchars($nama) ?></h5>
                    <p class="text-muted mb-0"><small><i class="bx bx-id-card me-1"></i><?= htmlspecialchars($stambuk) ?></small></p>
                </div>
            </div>
        </div>

        <!-- Right Column: Form / Display Card -->
        <div class="col-12 col-md-8 col-lg-9">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4 p-lg-5">
                    <?php 
                        // Determine initial visibility
                        $formDisplay = $isBiodataEmpty ? 'block' : 'none';
                        $viewDisplay = $isBiodataEmpty ? 'none' : 'block';
                    ?>

                    <!-- Form Section (Edit/Create) -->
                    <div id="formSection" style="display: <?= $formDisplay ?>;">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="card-title fw-bold text-primary mb-0">
                                <i class="bi bi-pencil-square me-2"></i><?= $isBiodataEmpty ? 'Isi Biodata' : 'Edit Biodata' ?>
                            </h5>
                            <?php if (!$isBiodataEmpty): ?>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="cancelEdit()">
                                    <i class="bi bi-x-lg me-1"></i>Batal
                                </button>
                            <?php endif; ?>
                        </div>

                        <form id="biodataForm">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label for="nama" class="form-label fw-semibold">Nama Lengkap</label>
                                    <input type="text" class="form-control form-control-lg rounded-3" id="nama" name="nama" 
                                        placeholder="Nama Lengkap" 
                                        value="<?= ($nama !== 'Nama Lengkap') ? htmlspecialchars($nama) : '' ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="stambuk" class="form-label fw-semibold">Stambuk</label>
                                    <input type="text" class="form-control form-control-lg rounded-3 bg-light" value="<?= htmlspecialchars($stambuk) ?>" readonly>
                                </div>
                            </div>

                            <div class="mt-4">
                                <label class="form-label fw-semibold d-block">Jenis Kelamin</label>
                                <div class="d-flex gap-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="gender" id="inlineRadio1" value="wanita" required onclick="updateKelasOptions()"
                                            <?= (strtolower($jenisKelamin) === 'wanita') ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="inlineRadio1">Wanita</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="gender" id="inlineRadio2" value="pria" required onclick="updateKelasOptions()"
                                            <?= (strtolower($jenisKelamin) === 'pria' || strtolower($jenisKelamin) === 'laki-laki') ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="inlineRadio2">Pria</label>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-4 mt-2">
                                <div class="col-md-6">
                                    <label for="jurusan" class="form-label fw-semibold">Jurusan</label>
                                    <select class="form-select form-select-lg rounded-3" name="jurusan" required>
                                        <option value="Teknik informatika" <?= (strtolower($jurusan) === 'teknik informatika') ? 'selected' : '' ?>>Teknik Informatika</option>
                                        <option value="Sistem informasi" <?= (strtolower($jurusan) === 'sistem informasi') ? 'selected' : '' ?>>Sistem Informasi</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="kelas" class="form-label fw-semibold">Kelas</label>
                                    <select class="form-select form-select-lg rounded-3" id="floatingSelect" name="kelas" required>
                                        <option selected disabled>Pilih Kelas Anda</option>
                                        <option value="<?php echo htmlspecialchars($kelas) ?>" selected><?= htmlspecialchars($kelas) ?></option>                                
                                        <?php if ($kelas !== 'Kelas'): ?>
                                            <?php 
                                                $kelasOptions = [];
                                                if ($jenisKelamin === "wanita") {
                                                    $kelasOptions = ["B1", "B2", "B3", "B4", "B5"];
                                                } else if ($jenisKelamin === "pria" || $jenisKelamin === "laki-laki") {
                                                    $kelasOptions = ["A1", "A2", "A3", "A4", "A5", "A6", "A7", "A8", "A9"];
                                                }
                                                ?>
                                            <option value="<?php echo htmlspecialchars($kelas) ?>" selected><?= htmlspecialchars($kelas) ?></option>
                                             <?php foreach ($kelasOptions as $kelasOps) : ?>
                                                <option value="<?= htmlspecialchars($kelasOps) ?>" <?= ($kelas === $kelasOps) ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($kelasOps) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="row g-4 mt-2">
                                <div class="col-md-6">
                                    <label for="alamat" class="form-label fw-semibold">Alamat</label>
                                    <input type="text" class="form-control form-control-lg rounded-3" id="alamat" name="alamat" 
                                        placeholder="Alamat" 
                                        value="<?= ($alamat !== 'Alamat') ? htmlspecialchars($alamat) : '' ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="tempatlahir" class="form-label fw-semibold">Kota Asal</label>
                                    <input type="text" class="form-control form-control-lg rounded-3" id="tempatlahir" name="tempatlahir" 
                                        placeholder="Tempat Lahir" 
                                        value="<?= ($tempatLahir !== 'Tempat Lahir') ? htmlspecialchars($tempatLahir) : '' ?>" required>
                                </div>
                            </div>

                            <div class="row g-4 mt-2">
                                <div class="col-md-6">
                                    <label for="tanggallahir" class="form-label fw-semibold">Tanggal Lahir</label>
                                    <input type="date" class="form-control form-control-lg rounded-3" id="tanggallahir" name="tanggallahir" 
                                        value="<?= ($tanggalLahir !== 'Tanggal Lahir') ? htmlspecialchars($tanggalLahir) : '' ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="telephone" class="form-label fw-semibold">No Telephone</label>
                                    <input type="text" class="form-control form-control-lg rounded-3" id="telephone" name="telephone" 
                                        placeholder="No Telephone" 
                                        value="<?= ($noHp !== 'No Telephone') ? htmlspecialchars($noHp) : '' ?>" required>
                                </div>
                            </div>

                            <div class="d-flex gap-3 justify-content-end mt-5 pt-3 border-top">
                                <?php if ($isBiodataEmpty): ?>
                                    <button type="reset" class="btn btn-outline-secondary btn-lg px-4 rounded-3">
                                        <i class="bi bi-arrow-counterclockwise me-2"></i>Reset
                                    </button>
                                <?php endif; ?>
                                <button type="submit" class="btn btn-primary btn-lg px-5 rounded-3" name="submit">
                                    <i class="bi bi-check-circle me-2"></i><?= $isBiodataEmpty ? 'Submit' : 'Update' ?>
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Display Section (Read Only) -->
                    <div id="displaySection" style="display: <?= $viewDisplay ?>;">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="card-title fw-bold text-primary mb-0">
                                <i class="bi bi-person-badge me-2"></i>Data Diri
                            </h5>
                            <button class="btn btn-primary" onclick="enableEditMode()">
                                <i class="bi bi-pencil me-2"></i>Edit Biodata
                            </button>
                        </div>

                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-muted small">Nama Lengkap</label>
                                <div class="form-control form-control-lg rounded-3 bg-light"><?= htmlspecialchars($nama) ?></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-muted small">Stambuk</label>
                                <div class="form-control form-control-lg rounded-3 bg-light"><?= htmlspecialchars($stambuk) ?></div>
                            </div>
                        </div>

                        <div class="row g-4 mt-2">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-muted small">Jurusan</label>
                                <div class="form-control form-control-lg rounded-3 bg-light"><?= htmlspecialchars($jurusan) ?></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-muted small">Jenis Kelamin</label>
                                <div class="form-control form-control-lg rounded-3 bg-light"><?= htmlspecialchars($jenisKelamin) ?></div>
                            </div>
                        </div>

                        <div class="row g-4 mt-2">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-muted small">Kelas</label>
                                <div class="form-control form-control-lg rounded-3 bg-light"><?= htmlspecialchars($kelas) ?></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-muted small">Alamat</label>
                                <div class="form-control form-control-lg rounded-3 bg-light"><?= htmlspecialchars($alamat) ?></div>
                            </div>
                        </div>

                        <div class="row g-4 mt-2">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-muted small">Tempat Lahir</label>
                                <div class="form-control form-control-lg rounded-3 bg-light"><?= htmlspecialchars($tempatLahir) ?></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-muted small">Tanggal Lahir</label>
                                <div class="form-control form-control-lg rounded-3 bg-light"><?= htmlspecialchars($tanggalLahir) ?></div>
                            </div>
                        </div>

                        <div class="row g-4 mt-2">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-muted small">No Telephone</label>
                                <div class="form-control form-control-lg rounded-3 bg-light"><?= htmlspecialchars($noHp) ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
function enableEditMode() {
    document.getElementById('displaySection').style.display = 'none';
    document.getElementById('formSection').style.display = 'block';
    
    if (typeof updateKelasOptions === "function") {
        updateKelasOptions(); 
    } else {    
        const gender = document.querySelector('input[name="gender"]:checked');
        if(gender) $(gender).trigger('change'); 
    }
}

function cancelEdit() {
    document.getElementById('formSection').style.display = 'none';
    document.getElementById('displaySection').style.display = 'block';
}

$(document).ready(function () {
    // Profile Image Upload Handler (Change Event)
    $('#profileImageInput').change(function() {
        if (this.files && this.files[0]) {
            const formData = new FormData();
            formData.append('image', this.files[0]);
            
            const $overlay = $('.profile-overlay');
            const originalContent = $overlay.html();

            // Show uploading state using DOM creation
            const spinner = document.createElement('div');
            spinner.className = 'spinner-border spinner-border-sm text-white';
            spinner.setAttribute('role', 'status');
            
            $overlay.empty().append(spinner);
            $overlay.css('opacity', '1');
            
            $.ajax({
                url: '/Sistem-Pendaftaran-Calon-Asisten/public/updateprofile',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    try {
                         const res = typeof response === 'string' ? JSON.parse(response) : response;
                          if (res.status === 'success') {
                             showAlert('Upload Success!', true); 
                             if (res.newPhoto) {
                                 const newUrl = res.newPhoto + '?t=' + new Date().getTime();
                                 $('.profile-img-target, .navbar-profile-img, img[alt="Profile Picture"]').attr('src', newUrl);
                             }
                         } else {
                             console.log('Debug Info:', res.debug);
                             showAlert(res.message || 'Gagal upload foto', false);
                         }
                    } catch(e) { 
                         console.error('JSON Parse Error:', e);
                         showAlert('Error parsing response from server.', false);
                    } finally {
                         $overlay.html(originalContent);
                         $overlay.css('opacity', '');
                    }
                },
                error: function(xhr, status, error) {
                     console.error('AJAX Error:', error);
                     showAlert('Terjadi kesalahan koneksi saat mengunggah gambar.', false);
                     $overlay.html(originalContent);
                     $overlay.css('opacity', '');
                }
            });
        }
    });
});
</script>

<script src="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/js/biodata.js"></script>
