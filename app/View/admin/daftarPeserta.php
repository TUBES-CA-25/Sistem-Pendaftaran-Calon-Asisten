<?php
/**
 * Daftar Peserta Admin View
 * 
 * Data yang diterima dari Controller:
 * @var array $mahasiswaList - Daftar mahasiswa
 */
$mahasiswaList = $mahasiswaList ?? [];
$result = $mahasiswaList;
?>

<style>
    /* Font Poppins */
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');

    body {
        font-family: 'Poppins', sans-serif;
        background-color: #f0f4f8;
    }


    .table-modern {
        background: linear-gradient(145deg, #ffffff, #f3f6fa);
        /* Gradient background */
        border-radius: 16px;
        /* Rounded corners */
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
        /* Depth effect */
        overflow: hidden;
        border-collapse: separate;
        border-spacing: 0;
        /* Remove gaps */
        width: 100%;
        margin: 20px 0;
        /* Add spacing around the table */
    }

    .table-modern th,
    .table-modern td {
        padding: 16px 20px;
        /* Add padding for spacing */
        text-align: left;
        color: #333;
        /* Dark text for readability */
        border-bottom: 1px solid #eaeaea;
        /* Light borders between rows */
    }

    .table-modern th {
        background-color: #3dc2ec;
        /* Subtle header background */
        font-weight: 600;
        /* Bold font */
        color: white;
        /* Slightly darker text for headers */
        font-size: 1rem;
        /* Adjust header font size */
    }

    .table-modern tr:hover td {
        background-color: rgba(61, 194, 236, 0.1);
        /* Highlight row on hover */
        cursor: pointer;
    }

    .table-modern tr:last-child td {
        border-bottom: none;
        /* Remove bottom border for the last row */
    }

    .table-modern tbody tr:first-child td:first-child {
        border-top-left-radius: 16px;
        /* Rounded top-left corner */
    }

    .table-modern tbody tr:first-child td:last-child {
        border-top-right-radius: 16px;
        /* Rounded top-right corner */
    }

    .table-modern tbody tr:last-child td:first-child {
        border-bottom-left-radius: 16px;
        /* Rounded bottom-left corner */
    }

    .table-modern tbody tr:last-child td:last-child {
        border-bottom-right-radius: 16px;
        /* Rounded bottom-right corner */
    }

    .table-modern .action-icons img {
        width: 24px;
        height: 24px;
        margin: 0 8px;
        transition: transform 0.3s ease;
    }

    .table-modern .action-icons img:hover {
        transform: scale(1.1);
        /* Slight zoom on hover */
    }
</style>

<main>
    <h1 class="dashboard">Daftar peserta</h1>
    <table id="daftar" class="display table-modern">
        <button type="button" data-bs-toggle="modal" data-bs-target="#addNotification" class="btn btn-primary mb-3">
            Kirim pesan
        </button>
        <thead>
            <tr>
                <th>no</th>
                <th>Nama Lengkap</th>
                <th>Stambuk</th>
                <th>Jurusan</th>
                <th>Kelas</th>
                <th>Alamat</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 1; ?>
            <?php foreach ($result as $row) { ?>
                <tr data-id="<?= $row['id'] ?>" data-userid="<?= $row['idUser'] ?>" style="cursor: pointer;">
                    <td><?= $i ?></td>
                    <td>
                        <span data-bs-toggle="modal" data-bs-target="#detailModal" data-nama="<?= $row['nama_lengkap'] ?>"
                            data-stambuk="<?= $row['stambuk'] ?>" data-jurusan="<?= $row['jurusan'] ?>"
                            data-kelas="<?= $row['kelas'] ?>" data-alamat="<?= $row['alamat'] ?>"
                            data-tempat_lahir="<?= $row['tempat_lahir'] ?>" data-notelp="<?= $row['notelp'] ?>"
                            data-tanggal_lahir="<?= $row['tanggal_lahir'] ?>"
                            data-jenis_kelamin="<?= $row['jenis_kelamin'] ?>" data-foto="<?= $row['berkas']['foto'] ?>"
                            data-cv="<?= $row['berkas']['cv'] ?>" data-transkrip="<?= $row['berkas']['transkrip_nilai'] ?>"
                            data-surat="<?= $row['berkas']['surat_pernyataan'] ?>">
                            <?= $row['nama_lengkap'] ?>
                        </span>

                    </td>
                    <td><?= $row['stambuk'] ?></td>
                    <td><?= $row['jurusan'] ?></td>
                    <td><?= $row['kelas'] ?></td>
                    <td><?= $row['alamat'] ?></td>
                    <td>
                        <div style="display: flex; gap:5%;">
                            <img src="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Img/edit.svg" alt="edit" style="cursor: pointer;">
                            <img src="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Img/delete.svg" alt="delete" style="cursor: pointer;">
                        </div>
                    </td>
                </tr>
                <?php $i++; ?>
            <?php } ?>
        </tbody>
    </table>
</main>

<div class="modal fade" id="addNotification" tabindex="-1" aria-labelledby="addNotificationLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addNotificationLabel">Tambah Notifikasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addNotificationForm">
                    <div class="mb-3">
                        <label for="mahasiswa" class="form-label">Pilih Mahasiswa</label>
                        <select class="form-select" id="mahasiswa">
                            <option value="" disabled selected>-- Pilih Mahasiswa --</option>
                            <?php foreach ($mahasiswaList as $mahasiswa): ?>
                                <option value="<?= $mahasiswa['id'] ?>">
                                    <?= $mahasiswa['stambuk'] ?> - <?= $mahasiswa['nama_lengkap'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="button" class="btn btn-secondary mt-2" id="addMahasiswaButton">Tambah
                            Mahasiswa</button>
                        <button type="button" class="btn btn-success mt-2" id="addAllMahasiswaButton">Tambah
                            Semua</button>
                    </div>
                    <div class="mb-3">
                        <label for="selectedMahasiswa" class="form-label">Mahasiswa Terpilih</label>
                        <ul class="list-group" id="selectedMahasiswaList"></ul>
                    </div>
                    <div class="mb-3">
                        <label for="message" class="form-label">Pesan</label>
                        <textarea class="form-control" id="message" rows="4" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Kirim Pesan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel">Detail Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <img id="modalFoto" src="" alt="" style="width: 100%; max-height: 300px; object-fit: cover;">
                <p><strong>Nama Lengkap:</strong> <span id="modalNama"></span></p>
                <p><strong>Stambuk:</strong> <span id="modalStambuk"></span></p>
                <p><strong>Jurusan:</strong> <span id="modalJurusan"></span></p>
                <p><strong>Kelas:</strong> <span id="modalKelas"></span></p>
                <p><strong>Alamat:</strong> <span id="modalAlamat"></span></p>
                <p><strong>Tempat Lahir:</strong> <span id="modalTempat_lahir"></span></p>
                <p><strong>Tanggal Lahir:</strong> <span id="modalTanggal_lahir"></span></p>
                <p><strong>Jenis Kelamin:</strong> <span id="modalJenis_kelamin"></span></p>
                <p><strong>No Telephone:</strong> <span id="modalNoTelp"></span></p>
            </div>
            <div class="modal-footer">
                <div class="text-center">
                    <button type="button" class="btn btn-primary" data-bs-toggle="collapse"
                        data-bs-target="#downloadOptions" aria-expanded="false" aria-controls="downloadOptions">
                        Unduh Berkas
                    </button>

                    <div class="collapse mt-3" id="downloadOptions">
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-primary btn-sm" id="downloadFotoButton"
                                data-download-url="" target="blank">Unduh Foto</button>
                            <button type="button" class="btn btn-primary btn-sm" id="downloadCVButton"
                                data-download-url="" target="blank">Unduh CV</button>
                            <button type="button" class="btn btn-primary btn-sm" id="downloadTranskripButton"
                                data-download-url="" target="blank">Unduh Transkrip Nilai</button>
                            <button type="button" class="btn btn-primary btn-sm" id="downloadSuratButton"
                                data-download-url="" target="blank">Unduh Surat Pernyataan</button>
                        </div>
                    </div>
                </div>

                <button type="button" class="btn btn-success" id="acceptButton">
                    Accept
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus data ini?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Hapus</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Send Message</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editForm">
                    <div class="mb-3">
                        <label for="message" class="form-label">Pesan ke Mahasiswa:</label>
                        <textarea id="message" name="message" class="form-control" rows="4" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary" form="editForm">Simpan</button>
            </div>
        </div>
    </div>
</div>

<!-- Load JavaScript -->
<script src="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Script/common.js"></script>
<script src="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Script/daftarPeserta.js"></script>
<script>
// Call initialization after scripts are loaded
if (typeof window.initDaftarPeserta === 'function') {
    window.initDaftarPeserta();
} else {
    console.error('initDaftarPeserta function not found!');
}
</script>
