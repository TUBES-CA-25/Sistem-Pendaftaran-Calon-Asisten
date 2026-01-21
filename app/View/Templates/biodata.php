<?php
use App\Controllers\user\BiodataUserController;
use App\Controllers\Profile\ProfileController;

$nama = ProfileController::viewBiodata() == null ? "Nama Lengkap" : ProfileController::viewBiodata()["namaLengkap"];
$stambuk = ProfileController::viewUser()["stambuk"];
$jurusan = ProfileController::viewBiodata() == null ? "Jurusan" : ProfileController::viewBiodata()["jurusan"];
$alamat = ProfileController::viewBiodata() == null ? "Alamat" : ProfileController::viewBiodata()["alamat"];
$kelas = ProfileController::viewBiodata() == null ? "Kelas" : ProfileController::viewBiodata()["kelas"];
$jenisKelamin = ProfileController::viewBiodata() == null ? "Jenis Kelamin" : ProfileController::viewBiodata()["jenisKelamin"];
$tempatLahir = ProfileController::viewBiodata() == null ? "Tempat Lahir" : ProfileController::viewBiodata()["tempatLahir"];
$tanggalLahir = ProfileController::viewBiodata() == null ? "Tanggal Lahir" : ProfileController::viewBiodata()["tanggalLahir"];
$noHp = ProfileController::viewBiodata() == null ? "No Telephone" : ProfileController::viewBiodata()["noHp"];

// Format Tanggal agar lebih enak dibaca (Opsional)
$tglLahirDisplay = ($tanggalLahir != "Tanggal Lahir") ? date("d F Y", strtotime($tanggalLahir)) : $tanggalLahir;
?>

<link rel="stylesheet" href="<?= APP_URL ?>/Style/biodata.css">

<main class="biodata-container">
    
    <div class="page-header">
        <h1 class="page-title">Biodata Diri</h1>
        <p class="page-subtitle">Kelola informasi pribadi Anda untuk keperluan seleksi.</p>
    </div>

    <div class="form-card">
        
        <?php if (BiodataUserController::isEmpty()) { ?>
            <form id="biodataForm" class="biodata-form">
                
                <div class="form-row">
                    <div class="input-group">
                        <label for="nama">Nama Lengkap <span class="required">*</span></label>
                        <input type="text" id="nama" name="nama" placeholder="Masukkan nama lengkap" required>
                    </div>
                    <div class="input-group">
                        <label for="stambuk">NIM / Stambuk</label>
                        <input type="text" value="<?=$stambuk?>" readonly class="input-readonly">
                    </div>
                </div>

                <div class="form-row">
                    <div class="input-group">
                        <label>Jenis Kelamin <span class="required">*</span></label>
                        <div class="radio-group">
                            <label class="radio-box">
                                <input type="radio" name="gender" value="pria" required onclick="updateKelasOptions()">
                                <span>Pria</span>
                            </label>
                            <label class="radio-box">
                                <input type="radio" name="gender" value="wanita" required onclick="updateKelasOptions()">
                                <span>Wanita</span>
                            </label>
                        </div>
                    </div>
                    <div class="input-group">
                        <label for="jurusan">Program Studi <span class="required">*</span></label>
                        <select id="jurusan" name="jurusan" required>
                            <option value="Teknik informatika">Teknik Informatika</option>
                            <option value="Sistem informasi">Sistem Informasi</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="input-group">
                        <label for="kelas">Kelas <span class="required">*</span></label>
                        <select id="floatingSelect" name="kelas" required>
                            <option selected disabled>Pilih Kelas</option>
                        </select>
                    </div>
                    <div class="input-group">
                        <label for="telephone">No. Handphone <span class="required">*</span></label>
                        <input type="text" id="telephone" name="telephone" placeholder="08..." required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="input-group">
                        <label for="tempatlahir">Tempat Lahir <span class="required">*</span></label>
                        <input type="text" id="tempatlahir" name="tempatlahir" placeholder="Kota kelahiran" required>
                    </div>
                    <div class="input-group">
                        <label for="tanggallahir">Tanggal Lahir <span class="required">*</span></label>
                        <input type="date" id="tanggallahir" name="tanggallahir" required>
                    </div>
                </div>

                <div class="input-group">
                    <label for="alamat">Alamat Domisili <span class="required">*</span></label>
                    <input type="text" id="alamat" name="alamat" placeholder="Alamat lengkap saat ini" required>
                </div>

                <div class="form-actions">
                    <button type="reset" class="btn btn-secondary">Reset</button>
                    <button type="submit" class="btn btn-primary">Simpan Biodata</button>
                </div>
            </form>

        <?php } else { ?>
            <div class="readonly-grid">
                <div class="info-group">
                    <label>Nama Lengkap</label>
                    <div class="info-value main-value"><?=$nama?></div>
                </div>
                <div class="info-group">
                    <label>Stambuk</label>
                    <div class="info-value"><?=$stambuk?></div>
                </div>

                <div class="info-group">
                    <label>Program Studi</label>
                    <div class="info-value"><?=$jurusan?></div>
                </div>
                <div class="info-group">
                    <label>Jenis Kelamin</label>
                    <div class="info-value"><?= ucfirst($jenisKelamin) ?></div>
                </div>

                <div class="info-group">
                    <label>Kelas</label>
                    <div class="info-value"><?=$kelas?></div>
                </div>
                <div class="info-group">
                    <label>No. Handphone</label>
                    <div class="info-value"><?=$noHp?></div>
                </div>

                <div class="info-group">
                    <label>Tempat Lahir</label>
                    <div class="info-value"><?=$tempatLahir?></div>
                </div>
                <div class="info-group">
                    <label>Tanggal Lahir</label>
                    <div class="info-value"><?=$tglLahirDisplay?></div>
                </div>

                <div class="info-group full-width">
                    <label>Alamat</label>
                    <div class="info-value"><?=$alamat?></div>
                </div>
            </div>
            
            <div class="readonly-footer">
                <small class="text-muted"><i class='bx bx-info-circle'></i> Hubungi Admin jika ada kesalahan data.</small>
            </div>

        <?php } ?>
    </div>
</main>

<script src="/tubes_web/public/Assets/Script/user/biodata.js"></script>