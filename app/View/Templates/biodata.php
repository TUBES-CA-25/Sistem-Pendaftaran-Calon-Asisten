<?php
use App\Controllers\user\BiodataUserController;
use App\Controllers\Profile\ProfileController;

$nama = ProfileController::viewBiodata() == null ? "Nama Lengkap" : ProfileController::viewBiodata()["namaLengkap"];
$stambuk = ProfileController::viewUser()["stambuk"];
$jurusan = ProfileController::viewBiodata() == null ? "Jurusan" : ProfileController::viewBiodata()["jurusan"];
$alamat = ProfileController::viewBiodata() == null ? "Alamat" : ProfileController::viewBiodata()["alamat"];
$kelas = ProfileController::viewBiodata() == null ? "kelas" : ProfileController::viewBiodata()["kelas"];
$jenisKelamin = ProfileController::viewBiodata() == null ? "Jenis Kelamin" : ProfileController::viewBiodata()["jenisKelamin"];
$tempatLahir = ProfileController::viewBiodata() == null ? "Tempat Lahir" : ProfileController::viewBiodata()["tempatLahir"];
$tanggalLahir = ProfileController::viewBiodata() == null ? "Tanggal Lahir" : ProfileController::viewBiodata()["tanggalLahir"];
$noHp = ProfileController::viewBiodata() == null ? "No Telephone" : ProfileController::viewBiodata()["noHp"];
?>
<div>
    <main>
    <h1 class="dashboard">Biodata</h1>
</div>
<div class="form-container">
    <?php if (BiodataUserController::isEmpty()) { ?>
        <form id="biodataForm" class="biodata-form">
            <div class="form-row">
                <div class="form-group">
                    <label for="nama" class="form-label">Nama Lengkap</label>
                    <input type="text" class="form-control" id="nama" name="nama" placeholder="Nama Lengkap" required>
                </div>
                <div class="form-group">
                    <label for="stambuk" class="form-label">Stambuk</label>
                    <input type="text" class="form-control" value="<?=$stambuk?>" readonly>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Jenis Kelamin</label>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="gender" id="inlineRadio1" value="wanita" required onclick="updateKelasOptions()">
                    <label class="form-check-label" for="inlineRadio1">Wanita</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="gender" id="inlineRadio2" value="pria" required onclick="updateKelasOptions()">
                    <label class="form-check-label" for="inlineRadio2">Pria</label>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="jurusan" class="form-label">Jurusan</label>
                    <select class="form-select" name="jurusan" required>
                        <option value="Teknik informatika">Teknik Informatika</option>
                        <option value="Sistem informasi">Sistem Informasi</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="kelas" class="form-label">Kelas</label>
                    <select class="form-select" id="floatingSelect" name="kelas" required>
                        <option selected disabled>Pilih Kelas Anda</option>
                    </select>
                </div>
            </div>
            
            <!-- Row 3 -->
            <div class="form-row">
                <div class="form-group">
                    <label for="alamat" class="form-label">Alamat</label>
                    <input type="text" class="form-control" id="alamat" name="alamat" placeholder="Alamat" required>
                </div>
                <div class="form-group">
                    <label for="tempatlahir" class="form-label">Kota Asal</label>
                    <input type="text" class="form-control" id="tempatlahir" name="tempatlahir" placeholder="Tempat Lahir" required>
                </div>
            </div>

            <!-- Row 4 -->
            <div class="form-row">
                <div class="form-group">
                    <label for="tanggallahir" class="form-label">Tanggal Lahir</label>
                    <input type="date" class="form-control" id="tanggallahir" name="tanggallahir" required>
                </div>
                <div class="form-group">
                    <label for="telephone" class="form-label">No Telephone</label>
                    <input type="text" class="form-control" id="telephone" name="telephone" placeholder="No Telephone" required>
                </div>
            </div>

            <div class="button-container">
                <button type="submit" class="btn btn-primary" name="submit">Submit</button>
                <button type="reset" class="btn btn-secondary" name="reset">Reset</button>
            </div>
        </form>
    <?php } else { ?>
        <div class="form-row">
            <div class="form-group">
                <label for="nama" class="form-label">Nama Lengkap</label>
                <input type="text" class="form-control" value="<?=$nama?>" readonly>
            </div>
            <div class="form-group">
                <label for="stambuk" class="form-label">Stambuk</label>
                <input type="text" class="form-control" value="<?=$stambuk?>" readonly>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="jurusan" class="form-label">Jurusan</label>
                <input type="text" class="form-control" value="<?=$jurusan?>" readonly>
            </div>
            <div class="form-group">
                <label for="gender" class="form-label">Jenis Kelamin</label>
                <input type="text" class="form-control" value="<?=$jenisKelamin?>" readonly>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="kelas" class="form-label">Kelas</label>
                <input type="text" class="form-control" value="<?=$kelas?>" readonly>
            </div>
            <div class="form-group">
                <label for="alamat" class="form-label">Alamat</label>
                <input type="text" class="form-control" value="<?=$alamat?>" readonly>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="tempatlahir" class="form-label">Tempat Lahir</label>
                <input type="text" class="form-control" value="<?=$tempatLahir?>" readonly>
            </div>
            <div class="form-group">
                <label for="tanggallahir" class="form-label">Tanggal Lahir</label>
                <input type="text" class="form-control" value="<?=$tanggalLahir?>" readonly>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="telephone" class="form-label">No Telephone</label>
                <input type="text" class="form-control" value="<?=$noHp?>" readonly>
            </div>
        </div>
    <?php } ?>
</div>
</main>
<script src="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Script/user/biodata.js"></script>
