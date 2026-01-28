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

    <!-- Form Card -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4 p-lg-5">
            <?php if ($isBiodataEmpty): ?>
                <!-- Form Input -->
                <form id="biodataForm">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label for="nama" class="form-label fw-semibold">Nama Lengkap</label>
                            <input type="text" class="form-control form-control-lg rounded-3" id="nama" name="nama" placeholder="Nama Lengkap" required>
                        </div>
                        <div class="col-md-6">
                            <label for="stambuk" class="form-label fw-semibold">Stambuk</label>
                            <input type="text" class="form-control form-control-lg rounded-3 bg-light" value="<?= $stambuk ?>" readonly>
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="form-label fw-semibold d-block">Jenis Kelamin</label>
                        <div class="d-flex gap-4">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="gender" id="inlineRadio1" value="wanita" required onclick="updateKelasOptions()">
                                <label class="form-check-label" for="inlineRadio1">Wanita</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="gender" id="inlineRadio2" value="pria" required onclick="updateKelasOptions()">
                                <label class="form-check-label" for="inlineRadio2">Pria</label>
                            </div>
                        </div>
                    </div>

                    <div class="row g-4 mt-2">
                        <div class="col-md-6">
                            <label for="jurusan" class="form-label fw-semibold">Jurusan</label>
                            <select class="form-select form-select-lg rounded-3" name="jurusan" required>
                                <option value="Teknik informatika">Teknik Informatika</option>
                                <option value="Sistem informasi">Sistem Informasi</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="kelas" class="form-label fw-semibold">Kelas</label>
                            <select class="form-select form-select-lg rounded-3" id="floatingSelect" name="kelas" required>
                                <option selected disabled>Pilih Kelas Anda</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-4 mt-2">
                        <div class="col-md-6">
                            <label for="alamat" class="form-label fw-semibold">Alamat</label>
                            <input type="text" class="form-control form-control-lg rounded-3" id="alamat" name="alamat" placeholder="Alamat" required>
                        </div>
                        <div class="col-md-6">
                            <label for="tempatlahir" class="form-label fw-semibold">Kota Asal</label>
                            <input type="text" class="form-control form-control-lg rounded-3" id="tempatlahir" name="tempatlahir" placeholder="Tempat Lahir" required>
                        </div>
                    </div>

                    <div class="row g-4 mt-2">
                        <div class="col-md-6">
                            <label for="tanggallahir" class="form-label fw-semibold">Tanggal Lahir</label>
                            <input type="date" class="form-control form-control-lg rounded-3" id="tanggallahir" name="tanggallahir" required>
                        </div>
                        <div class="col-md-6">
                            <label for="telephone" class="form-label fw-semibold">No Telephone</label>
                            <input type="text" class="form-control form-control-lg rounded-3" id="telephone" name="telephone" placeholder="No Telephone" required>
                        </div>
                    </div>

                    <div class="d-flex gap-3 justify-content-end mt-5 pt-3 border-top">
                        <button type="reset" class="btn btn-outline-secondary btn-lg px-4 rounded-3">
                            <i class="bi bi-arrow-counterclockwise me-2"></i>Reset
                        </button>
                        <button type="submit" class="btn btn-primary btn-lg px-5 rounded-3" name="submit">
                            <i class="bi bi-check-circle me-2"></i>Submit
                        </button>
                    </div>
                </form>
            <?php else: ?>
                <!-- Display Mode (Read Only) -->
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

                <!-- Info Alert -->
                <div class="alert alert-info d-flex align-items-center gap-2 mt-4 rounded-3" role="alert">
                    <i class="bi bi-info-circle-fill"></i>
                    <div>Biodata sudah terisi. Hubungi admin jika ingin mengubah data.</div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<script src="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/js/biodata.js"></script>
