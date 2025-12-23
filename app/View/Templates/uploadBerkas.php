<?php
use App\Controllers\User\BerkasUserController;
use App\Controllers\user\DashboardUserController;
use App\Controllers\Profile\ProfileController;
$res = BerkasUserController::viewBerkas() ?? [];
$nama = ProfileController::viewBiodata() == null ? "Nama Lengkap" : ProfileController::viewBiodata()["namaLengkap"] ?? [];
?>

<style>
    .dashboard-container {
        padding: 20px;
        background-color: #f9f9f9;
        display: flex;
        flex-direction: column;
        gap: 20px;
        align-items: center;
        min-height: 100vh;
    }

    .form-container {
        background-color: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        padding: 24px;
        width: 100%;
        max-width: 600px;
        transition: box-shadow 0.3s ease;
    }

    .form-container:hover {
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
    }

    .form-container .mb-3 {
        margin-bottom: 16px;
    }

    .form-container .form-label {
        font-size: 1rem;
        font-weight: 600;
        color: #333;
        margin-bottom: 8px;
        display: block;
    }

    .form-container .form-control {
        width: 100%;
        padding: 12px;
        font-size: 0.95rem;
        border: 1px solid #ccc;
        border-radius: 8px;
        transition: border-color 0.3s ease;
    }

    .form-container .form-control:focus {
        border-color: #3DC2EC;
        outline: none;
        box-shadow: 0 0 6px rgba(61, 194, 236, 0.5);
    }

    .form-container .btn-submit {
        background-color: #3DC2EC;
        color: #fff;
        font-size: 1rem;
        font-weight: 600;
        padding: 12px 24px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .form-container .btn-submit:hover {
        background-color: #3392cc;
    }

    .table-section {
    background-color: #f0f8ff; /* Warna biru lembut */
    padding: 40px;
    margin: 30px auto;
    border-radius: 16px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    width: 80%;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.table-section h2 {
    font-size: 1.8rem;
    font-weight: 700;
    color: #3DC2EC;
    margin-bottom: 20px;
    text-align: center;
}

.table-container {
    width: 100%;
    overflow-x: auto;
}

.table {
    width: 100%;
    border-collapse: collapse;
    border-spacing: 0;
}

.table th,
.table td {
    text-align: left;
    padding: 12px 16px;
    border-bottom: 1px solid #ddd;
}

.table th {
    background-color: #3DC2EC;
    color: white;
    font-size: 1rem;
    font-weight: 600;
    text-transform: uppercase;
}

.table tr:hover {
    background-color: #f9f9f9;
}

.table td {
    font-size: 0.95rem;
    color: #333;
}

.table .status {
    font-weight: 600;
    color: #FFC107;
    background-color: rgba(255, 193, 7, 0.2);
    padding: 4px 8px;
    border-radius: 8px;
    text-align: center;
}

.table .status-acc {
    color: #4CAF50;
    background-color: rgba(76, 175, 80, 0.2);
    padding: 4px 8px;
    border-radius: 8px;
    text-align: center;
}


</style>

<main>
    <h1 class="dashboard">Upload Berkas</h1>

    <div>
        <div class="form-container">
            <?php
            if(!DashboardUserController::getBiodataStatus()) {
                echo '<div class="alert alert-warning" role="alert">
                Lengkapi biodata terlebih dahulu';
                return;
            }
            ?>
            <form id="berkasForm" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="foto" class="form-label">Masukkan Foto 3x4</label>
                    <input class="form-control" type="file" id="foto" name="foto" accept="image/png, image/jpeg, image/jpg" required
                           <?php if (!DashboardUserController::getBiodataStatus()) echo 'disabled'; ?>>
                </div>

                <div class="mb-3">
                    <label for="cv" class="form-label">Masukkan CV</label>
                    <input class="form-control" type="file" id="cv" name="cv" accept="application/pdf" required
                           <?php if (!DashboardUserController::getBiodataStatus()) echo 'disabled'; ?>>
                </div>

                <div class="mb-3">
                    <label for="transkrip" class="form-label">Masukkan Transkrip Nilai</label>
                    <input class="form-control" type="file" id="transkrip" name="transkrip" accept="application/pdf" required
                           <?php if (!DashboardUserController::getBiodataStatus()) echo 'disabled'; ?>>
                </div>

                <div class="mb-3">
                    <label for="suratpernyataan" class="form-label">Masukkan Surat Pernyataan</label>
                    <input class="form-control" type="file" id="suratpernyataan" name="suratpernyataan" accept="application/pdf" required
                           <?php if (!DashboardUserController::getBiodataStatus()) echo 'disabled'; ?>>
                </div>
                <div style="display: flex; flex-direction: row; gap: 10px; margin-top: 20px; margin-bottom: 20px;">
                <a id="downloadFile1" style="display: flex; align-items: center; gap: 10px; text-decoration: none; color: #007bff; font-size: 16px;" href="#" download>
    <div style="display: flex; width: 40px; height: 40px; background-color: #e6f0ff; border-radius: 8px;">
        <i class="bx bx-file" style="font-size: 40px;"></i>
    </div>
    <span>Download Template CV</span>
</a>


</div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-submit" <?php if (!DashboardUserController::getBiodataStatus()) echo 'disabled'; ?>>Submit</button>
                </div>
            </form>
        </div>
        <div class="table-section">
    <h2>Riwayat Submit Berkas</h2>
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tanggal</th>
                    <th>Nama</th>
                    <th>Kategori</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!BerkasUserController::isEmptyBerkas()) { ?>
                    <tr>
                        <?php foreach($res as $result) {?>
                        <td>CCA00<?=$result['id_mahasiswa']?></td>
                        <td><?=$result['created_at']?></td>
                        <td><?=$nama?></td>
                        <td>Submit Berkas</td>
                        <?php if ($result['accepted'] == 1) { ?>
                            <td><span class="status-acc">Terverifikasi</span></td>
                        <?php } else if ($result['accepted'] == 0) { ?>
                        <td><span class="status">Belum Terverifikasi</span></td>
                    </tr>
                <?php } 
                }
            }else { ?>
                    <tr>
                        <td colspan="5" style="text-align: center; color: #777;">Belum ada data berkas</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>


</main>
<script src="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Script/user/berkas.js"></script>