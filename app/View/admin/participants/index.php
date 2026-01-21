<?php
/**
 * Daftar Peserta View
 * 
 * Data yang diterima dari controller:
 * @var array $mahasiswaList - Daftar mahasiswa
 * @var array $result - Result mahasiswa
 */
$mahasiswaList = $mahasiswaList ?? [];
$result = $result ?? [];
?>

<!-- Page Header -->
<?php
    $title = 'Daftar Peserta';
    $subtitle = 'Kelola data peserta pendaftaran calon asisten';
    $icon = 'bi bi-people-fill';
    require_once __DIR__ . '/../../templates/components/PageHeader.php';
?>

<!-- Main Content -->
<div class="container-fluid px-4">
    <!-- Table Card -->

            <!-- Table Controls -->


            <!-- Data Table -->
            <!-- Data Table -->
            <!-- Data Table -->
            <div class="table-responsive rounded-3 overflow-hidden shadow-sm">
                <table id="daftarPesertaTable" class="table table-hover align-middle mb-0" style="width:100%;">
                    <thead class="bg-white">
                        <tr style="border-top: 1px solid #dee2e6; border-bottom: 2px solid #dee2e6;">
                            <th class="text-center text-uppercase text-dark fw-bold py-3" style="width: 50px; font-size: 0.8rem;">No</th>
                            <th class="text-center text-uppercase text-dark fw-bold py-3" style="width: 100px; font-size: 0.8rem;">Avatar</th>
                            <th class="text-uppercase text-dark fw-bold py-3" style="font-size: 0.8rem;">Nama Lengkap</th>
                            <th class="text-uppercase text-dark fw-bold py-3" style="font-size: 0.8rem; white-space: nowrap;">Judul Presentasi</th>
                            <th class="text-uppercase text-dark fw-bold py-3" style="font-size: 0.8rem;">Stambuk</th>
                            <th class="text-uppercase text-dark fw-bold py-3" style="font-size: 0.8rem;">Jurusan</th>
                            <th class="text-uppercase text-dark fw-bold py-3" style="font-size: 0.8rem;">Kelas</th>
                            <th class="text-center text-uppercase text-dark fw-bold py-3" style="font-size: 0.8rem;">Status</th>
                            <th class="text-center text-uppercase text-dark fw-bold py-3" style="width: 150px; font-size: 0.8rem;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; ?>
                        <?php foreach ($result as $row): ?>
                            <?php
                                // Determine status
                                $status = $row['status'] ?? 'pending';
                                // Bootstrap Badge Styles (Solid Colors)
                                $statusClass = 'badge rounded-pill bg-secondary text-white fw-medium px-3 py-2'; // Default Pending/Belum Upload
                                $statusText = 'Belum Upload';
                                
                                if (isset($row['berkas']['accepted'])) {
                                    if ($row['berkas']['accepted'] == 1) {
                                        $statusClass = 'badge rounded-pill bg-success text-white fw-medium px-3 py-2';
                                        $statusText = 'Disetujui';
                                    } elseif ($row['berkas']['accepted'] == 2) {
                                        $statusClass = 'badge rounded-pill bg-danger text-white fw-medium px-3 py-2';
                                        $statusText = 'Ditolak';
                                    } elseif ($row['berkas']['accepted'] == 0) {
                                        $statusClass = 'badge rounded-pill bg-info text-white fw-medium px-3 py-2';
                                        $statusText = 'Proses';
                                    }
                                }
                                
                                // Get photo path with server-side existence check
                                $photoName = $row['berkas']['foto'] ?? 'default.png';
                                $photoUrl = '/Sistem-Pendaftaran-Calon-Asisten/res/imageUser/' . $photoName;
                                $serverPath = $_SERVER['DOCUMENT_ROOT'] . '/Sistem-Pendaftaran-Calon-Asisten/res/imageUser/' . $photoName;
                                
                                if (!file_exists($serverPath)) {
                                    $photoUrl = '/Sistem-Pendaftaran-Calon-Asisten/res/imageUser/default.png';
                                }
                                $photoPath = $photoUrl;
                            ?>
                            <tr style="border-bottom: 1px solid #f0f0f0;" data-id="<?= $row['id'] ?>" data-userid="<?= $row['idUser'] ?>">
                                <td class="text-center text-secondary py-3"><?= $i ?></td>
                                <td class="text-center py-3">
                                    <img src="<?= $photoPath ?>" alt="Avatar" class="rounded-circle border" style="width: 45px; height: 45px; object-fit: cover;" onerror="this.src='/Sistem-Pendaftaran-Calon-Asisten/res/imageUser/default.png'">
                                </td>
                                <td class="py-3">
                                    <p class="mb-0 fw-bold text-dark" style="font-size: 0.9rem;"><?= htmlspecialchars($row['nama_lengkap'] ?? '-') ?></p>
                                    <small class="text-muted d-block" style="font-size: 0.75rem;">Mahasiswa</small>
                                </td>
                                <td class="py-3">
                                    <?php if (!empty($row['judul_presentasi'])): ?>
                                        <span class="text-dark fw-medium small"><?= htmlspecialchars($row['judul_presentasi']) ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-light text-secondary border rounded-1 fw-normal">Belum Ada</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-secondary small fw-medium py-3"><?= htmlspecialchars($row['stambuk'] ?? '-') ?></td>
                                <td class="text-secondary small py-3"><?= htmlspecialchars($row['jurusan'] ?? '-') ?></td>
                                <td class="text-secondary small py-3"><?= htmlspecialchars($row['kelas'] ?? '-') ?></td>
                                <td class="text-center py-3">
                                    <span class="<?= $statusClass ?>" style="font-size: 0.75rem;">
                                        <?= $statusText ?>
                                    </span>
                                </td>
                                <td class="py-3">
                                        <div class="d-flex justify-content-center gap-2">
                                            <!-- View Button -->
                                            <button class="btn btn-sm btn-info bg-info-subtle text-info border-0 rounded-3 btn-view" 
                                                    title="Lihat Detail"
                                                    data-id="<?= $row['id'] ?>"
                                                    data-userid="<?= $row['idUser'] ?>"
                                                    data-nama="<?= htmlspecialchars($row['nama_lengkap'] ?? '') ?>"
                                                    data-stambuk="<?= htmlspecialchars($row['stambuk'] ?? '') ?>"
                                                    data-jurusan="<?= htmlspecialchars($row['jurusan'] ?? '') ?>"
                                                    data-kelas="<?= htmlspecialchars($row['kelas'] ?? '') ?>"
                                                    data-alamat="<?= htmlspecialchars($row['alamat'] ?? '') ?>"
                                                    data-tempat_lahir="<?= htmlspecialchars($row['tempat_lahir'] ?? '') ?>"
                                                    data-notelp="<?= htmlspecialchars($row['notelp'] ?? '') ?>"
                                                    data-tanggal_lahir="<?= htmlspecialchars($row['tanggal_lahir'] ?? '') ?>"
                                                    data-jenis_kelamin="<?= htmlspecialchars($row['jenis_kelamin'] ?? '') ?>"
                                                    data-judul_presentasi="<?= htmlspecialchars($row['judul_presentasi'] ?? '') ?>"
                                                    data-foto="<?= $row['berkas']['foto'] ?? '' ?>"
                                                    data-cv="<?= $row['berkas']['cv'] ?? '' ?>"
                                                    data-transkrip="<?= $row['berkas']['transkrip_nilai'] ?? '' ?>"
                                                    data-surat="<?= $row['berkas']['surat_pernyataan'] ?? '' ?>"
                                                    data-berkas_accepted="<?= $row['berkas']['accepted'] ?? '' ?>"
                                                    data-makalah="<?= $row['presentasi']['makalah'] ?? '' ?>"
                                                    data-ppt="<?= $row['presentasi']['ppt'] ?? ''?>">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            
                                            <?php if (!isset($row['berkas']['accepted']) || $row['berkas']['accepted'] === null): ?>
                                                <!-- Reminder Button -->
                                                <button class="btn btn-sm btn-warning bg-warning-subtle text-warning border-0 rounded-3 btn-reminder" 
                                                        title="Kirim Reminder" 
                                                        data-id="<?= $row['id'] ?>"
                                                        data-userid="<?= $row['idUser'] ?>"
                                                        data-nama="<?= htmlspecialchars($row['nama_lengkap'] ?? '') ?>">
                                                    <i class="bi bi-bell"></i>
                                                </button>
                                            <?php endif; ?>
                                            
                                            <!-- Delete Button -->
                                            <button class="btn btn-sm btn-danger bg-danger-subtle text-danger border-0 rounded-3 btn-delete" 
                                                    title="Hapus">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </div>

                                </td>
                            </tr>
                            <?php $i++; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

</div>



<!-- Modal Kirim Notifikasi -->
<div class="modal fade" id="addNotification" tabindex="-1" aria-labelledby="addNotificationLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="addNotificationLabel">
                    <i class="bi bi-send me-2"></i>Kirim Notifikasi ke Peserta
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addNotificationForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="mahasiswa" class="form-label fw-semibold">
                                    <i class="bi bi-person-plus me-1"></i>Pilih Peserta
                                </label>
                                <select class="form-select" id="mahasiswa">
                                    <option value="" disabled selected>-- Pilih Peserta --</option>
                                    <?php foreach ($mahasiswaList as $mahasiswa): ?>
                                        <option value="<?= $mahasiswa['id'] ?>" data-userid="<?= $mahasiswa['idUser'] ?>">
                                            <?= htmlspecialchars($mahasiswa['stambuk']) ?> - <?= htmlspecialchars($mahasiswa['nama_lengkap']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="mt-2 d-flex gap-2">
                                    <button type="button" class="btn btn-outline-primary btn-sm" id="addMahasiswaButton">
                                        <i class="bi bi-plus-circle me-1"></i>Tambah
                                    </button>
                                    <button type="button" class="btn btn-outline-success btn-sm" id="addAllMahasiswaButton">
                                        <i class="bi bi-people me-1"></i>Tambah Semua
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-people-fill me-1"></i>Peserta Terpilih
                                    <span class="badge bg-primary ms-1" id="selectedCount">0</span>
                                </label>
                                <div class="border rounded p-2" style="max-height: 150px; overflow-y: auto;">
                                    <ul class="list-group list-group-flush" id="selectedMahasiswaList">
                                        <li class="list-group-item text-muted text-center py-3">
                                            <i class="bi bi-inbox me-1"></i>Belum ada peserta dipilih
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="notifMessage" class="form-label fw-semibold">
                            <i class="bi bi-chat-text me-1"></i>Pesan Notifikasi
                        </label>
                        <textarea class="form-control" id="notifMessage" rows="4" placeholder="Tulis pesan notifikasi untuk peserta..." required></textarea>
                        <div class="form-text">Pesan ini akan dikirim ke semua peserta yang dipilih.</div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>Batal
                </button>
                <button type="submit" class="btn btn-primary" form="addNotificationForm">
                    <i class="bi bi-send me-1"></i>Kirim Notifikasi
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Peserta -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0" style="border-radius: var(--bs-border-radius-2xl); overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);">
            <!-- Header dengan Background Gradient -->
            <div class="position-relative" style="background: var(--bs-primary-dark); padding: 25px 30px 90px 30px;">
                <button type="button" class="btn-close btn-close-white position-absolute" style="top: 15px; right: 15px; opacity: 0.8; z-index: 20;" data-bs-dismiss="modal" aria-label="Close"></button>
                
                <!-- Decorative Elements -->
                <div class="position-absolute" style="top: -40px; right: -40px; width: 150px; height: 150px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
                <div class="position-absolute" style="bottom: 30px; left: -30px; width: 100px; height: 100px; background: rgba(255,255,255,0.08); border-radius: 50%;"></div>
                <div class="position-absolute" style="top: 20px; left: 30%; width: 60px; height: 60px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>
                
                <!-- Title -->
                <h5 class="text-white fw-semibold mb-0 position-relative" style="z-index: 10;">
                    <i class="bi bi-person-badge me-2"></i>Detail Peserta
                </h5>
            </div>
            
            <!-- Profile Card yang Overlap -->
            <div class="px-4" style="margin-top: -70px; position: relative; z-index: 10;">
                <div class="bg-white rounded-4 shadow p-4" style="border: 1px solid rgba(0,0,0,0.05);">
                    <div class="row align-items-center">
                        <!-- Photo Column -->
                        <div class="col-md-3 text-center mb-3 mb-md-0">
                            <div class="position-relative d-inline-block">
                                <img id="modalFoto" src="" alt="Foto Peserta" 
                                     class="rounded-circle shadow-lg"
                                     style="width: 130px; height: 130px; object-fit: cover; border: 5px solid white; background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);">
                                <span id="modalStatusIcon" class="position-absolute bottom-0 end-0 rounded-circle shadow" 
                                      style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem;">
                                </span>
                            </div>
                        </div>
                        
                        <!-- Info Column -->
                        <div class="col-md-9">
                            <div class="d-flex flex-wrap align-items-start justify-content-between">
                                <div>
                                    <h3 class="fw-bold mb-1" id="modalNamaHeader" style="color: #1f2937; font-size: 1.5rem;">Nama Peserta</h3>
                                    <p class="text-muted mb-2" style="font-size: 0.95rem;">
                                        <i class="bi bi-credit-card-2-front me-1"></i>
                                        <span id="modalStambukHeader">-</span>
                                    </p>
                                    <span id="modalStatusBadge" class="badge rounded-pill px-4 py-2" style="font-size: 0.85rem; font-weight: 500;">
                                        Status
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Quick Stats Row -->
                            <div class="row g-2 mt-3">
                                <div class="col-6 col-lg-3">
                                    <div class="rounded-3 p-2 text-center" style="background: linear-gradient(135deg, #667eea15 0%, #764ba215 100%); border: 1px solid #667eea20;">
                                        <i class="bi bi-mortarboard-fill d-block mb-1" style="font-size: 1.2rem; color: #667eea;"></i>
                                        <p class="text-muted mb-0" style="font-size: 0.65rem; text-transform: uppercase; letter-spacing: 0.5px;">Jurusan</p>
                                        <p class="fw-semibold mb-0 text-truncate" id="modalJurusan" style="font-size: 0.8rem; color: #374151;" title="">-</p>
                                    </div>
                                </div>
                                <div class="col-6 col-lg-3">
                                    <div class="rounded-3 p-2 text-center" style="background: linear-gradient(135deg, #10b98115 0%, #059b7015 100%); border: 1px solid #10b98120;">
                                        <i class="bi bi-door-open-fill d-block mb-1" style="font-size: 1.2rem; color: #10b981;"></i>
                                        <p class="text-muted mb-0" style="font-size: 0.65rem; text-transform: uppercase; letter-spacing: 0.5px;">Kelas</p>
                                        <p class="fw-semibold mb-0" id="modalKelas" style="font-size: 0.8rem; color: #374151;">-</p>
                                    </div>
                                </div>
                                <div class="col-6 col-lg-3">
                                    <div class="rounded-3 p-2 text-center" style="background: linear-gradient(135deg, #8b5cf615 0%, #7c3aed15 100%); border: 1px solid #8b5cf620;">
                                        <i class="bi bi-gender-ambiguous d-block mb-1" style="font-size: 1.2rem; color: #8b5cf6;"></i>
                                        <p class="text-muted mb-0" style="font-size: 0.65rem; text-transform: uppercase; letter-spacing: 0.5px;">Gender</p>
                                        <p class="fw-semibold mb-0" id="modalJenis_kelamin" style="font-size: 0.8rem; color: #374151;">-</p>
                                    </div>
                                </div>
                                <div class="col-6 col-lg-3">
                                    <div class="rounded-3 p-2 text-center" style="background: linear-gradient(135deg, #f59e0b15 0%, #d9770615 100%); border: 1px solid #f59e0b20;">
                                        <i class="bi bi-telephone-fill d-block mb-1" style="font-size: 1.2rem; color: #f59e0b;"></i>
                                        <p class="text-muted mb-0" style="font-size: 0.65rem; text-transform: uppercase; letter-spacing: 0.5px;">Telepon</p>
                                        <p class="fw-semibold mb-0 text-truncate" id="modalNoTelp" style="font-size: 0.8rem; color: #374151;">-</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Body Content -->
            <div class="modal-body px-4 pb-4 pt-3">
                <div class="row g-3">
                    <!-- Left Column -->
                    <div class="col-lg-6">
                        <!-- Biodata Section -->
                        <div class="bg-white rounded-4 p-4 h-100 shadow-sm" style="border: 1px solid #e5e7eb;">
                            <div class="d-flex align-items-center mb-4">
                                <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 42px; height: 42px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                    <i class="bi bi-person-vcard text-white" style="font-size: 1.1rem;"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-0" style="color: #1f2937;">Biodata Peserta</h6>
                                    <small class="text-muted">Informasi personal</small>
                                </div>
                            </div>
                            
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="p-3 rounded-3" style="background: #f8fafc; border-left: 3px solid #667eea;">
                                        <label class="text-muted d-block mb-1" style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px;">Nama Lengkap</label>
                                        <p class="fw-semibold mb-0" id="modalNama" style="color: #1f2937; font-size: 1rem;">-</p>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-3 rounded-3" style="background: #f8fafc;">
                                        <label class="text-muted d-block mb-1" style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px;">Stambuk/NIM</label>
                                        <p class="fw-medium mb-0" id="modalStambuk" style="color: #374151; font-size: 0.9rem;">-</p>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-3 rounded-3" style="background: #f8fafc;">
                                        <label class="text-muted d-block mb-1" style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px;">Tempat Lahir</label>
                                        <p class="fw-medium mb-0" id="modalTempat_lahir" style="color: #374151; font-size: 0.9rem;">-</p>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-3 rounded-3" style="background: #f8fafc;">
                                        <label class="text-muted d-block mb-1" style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px;">Tanggal Lahir</label>
                                        <p class="fw-medium mb-0" id="modalTanggal_lahir" style="color: #374151; font-size: 0.9rem;">-</p>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-3 rounded-3" style="background: #f8fafc;">
                                        <label class="text-muted d-block mb-1" style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px;">Jenis Kelamin</label>
                                        <p class="fw-medium mb-0" id="modalJenisKelaminDetail" style="color: #374151; font-size: 0.9rem;">-</p>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="p-3 rounded-3" style="background: #f8fafc;">
                                        <label class="text-muted d-block mb-1" style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                            <i class="bi bi-geo-alt me-1"></i>Alamat
                                        </label>
                                        <p class="fw-medium mb-0" id="modalAlamat" style="color: #374151; font-size: 0.9rem; line-height: 1.5;">-</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Right Column -->
                    <div class="col-lg-6">
                        <!-- Presentasi Section -->
                        <div class="bg-white rounded-4 p-4 mb-3 shadow-sm" id="presentasiSection" style="border: 1px solid #e5e7eb;">
                            <div class="d-flex align-items-center mb-4">
                                <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 42px; height: 42px; background: linear-gradient(135deg, #10b981 0%, #059b70 100%);">
                                    <i class="bi bi-easel text-white" style="font-size: 1.1rem;"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-0" style="color: #1f2937;">Presentasi</h6>
                                    <small class="text-muted">Materi presentasi peserta</small>
                                </div>
                            </div>
                            
                            <div class="p-3 rounded-3 mb-3" style="background: linear-gradient(135deg, #10b98110 0%, #059b7010 100%); border: 1px solid #10b98130;">
                                <label class="text-muted d-block mb-1" style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px;">Judul Presentasi</label>
                                <p class="fw-semibold mb-0" id="modalJudulPresentasi" style="color: #1f2937; font-size: 0.95rem;">-</p>
                            </div>
                            
                            <div class="d-flex gap-2 flex-wrap" id="presentasiButtons">
                                <button type="button" class="btn btn-sm px-3 py-2" id="downloadMakalahButton" data-download-url="" style="display: none; background: linear-gradient(135deg, #10b981 0%, #059b70 100%); color: white; border: none; border-radius: 10px;">
                                    <i class="bi bi-file-earmark-text me-2"></i>Download Makalah
                                </button>
                                <button type="button" class="btn btn-sm px-3 py-2" id="downloadPptButton" data-download-url="" style="display: none; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; border: none; border-radius: 10px;">
                                    <i class="bi bi-file-earmark-slides me-2"></i>Download PPT
                                </button>
                                <span id="noPresentasiFiles" class="text-muted fst-italic" style="font-size: 0.85rem; display: none;">
                                    <i class="bi bi-info-circle me-1"></i>Belum ada file presentasi
                                </span>
                            </div>
                        </div>
                        
                        <!-- Berkas Section -->
                        <div class="bg-white rounded-4 p-4 shadow-sm" style="border: 1px solid #e5e7eb;">
                            <div class="d-flex align-items-center mb-4">
                                <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 42px; height: 42px; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                                    <i class="bi bi-folder2-open text-white" style="font-size: 1.1rem;"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-0" style="color: #1f2937;">Berkas Pendaftaran</h6>
                                    <small class="text-muted">Dokumen yang diunggah</small>
                                </div>
                            </div>
                            
                            <div class="row g-3">
                                <!-- Foto -->
                                <div class="col-12">
                                    <div class="d-flex align-items-center justify-content-between p-3 rounded-3" style="background: #f8fafc; border: 1px solid #e5e7eb;">
                                        <div class="d-flex align-items-center gap-3 flex-grow-1">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: #dbeafe;">
                                                <i class="bi bi-image" style="color: #2563eb; font-size: 1.1rem;"></i>
                                            </div>
                                            <div>
                                                <p class="mb-0 fw-medium" style="font-size: 0.9rem; color: #374151;">Foto</p>
                                                <small class="text-muted" style="font-size: 0.75rem;">Pas foto mahasiswa</small>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-download-berkas" id="downloadFotoButton" data-download-url="" style="background: #e0f2fe; color: #0284c7; border: none; border-radius: 6px; padding: 6px 12px;">
                                            <i class="bi bi-download"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- CV -->
                                <div class="col-12">
                                    <div class="d-flex align-items-center justify-content-between p-3 rounded-3" style="background: #f8fafc; border: 1px solid #e5e7eb;">
                                        <div class="d-flex align-items-center gap-3 flex-grow-1">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: #ede9fe;">
                                                <i class="bi bi-file-person" style="color: #7c3aed; font-size: 1.1rem;"></i>
                                            </div>
                                            <div>
                                                <p class="mb-0 fw-medium" style="font-size: 0.9rem; color: #374151;">CV</p>
                                                <small class="text-muted" style="font-size: 0.75rem;">Curriculum Vitae</small>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-download-berkas" id="downloadCVButton" data-download-url="" style="background: #e0f2fe; color: #0284c7; border: none; border-radius: 6px; padding: 6px 12px;">
                                            <i class="bi bi-download"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- Transkrip -->
                                <div class="col-12">
                                    <div class="d-flex align-items-center justify-content-between p-3 rounded-3" style="background: #f8fafc; border: 1px solid #e5e7eb;">
                                        <div class="d-flex align-items-center gap-3 flex-grow-1">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: #d1fae5;">
                                                <i class="bi bi-file-text" style="color: #059669; font-size: 1.1rem;"></i>
                                            </div>
                                            <div>
                                                <p class="mb-0 fw-medium" style="font-size: 0.9rem; color: #374151;">Transkrip Nilai</p>
                                                <small class="text-muted" style="font-size: 0.75rem;">Transkrip nilai akademik</small>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-download-berkas" id="downloadTranskripButton" data-download-url="" style="background: #e0f2fe; color: #0284c7; border: none; border-radius: 6px; padding: 6px 12px;">
                                            <i class="bi bi-download"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- Surat Pernyataan -->
                                <div class="col-12">
                                    <div class="d-flex align-items-center justify-content-between p-3 rounded-3" style="background: #f8fafc; border: 1px solid #e5e7eb;">
                                        <div class="d-flex align-items-center gap-3 flex-grow-1">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: #fef3c7;">
                                                <i class="bi bi-file-earmark-check" style="color: #d97706; font-size: 1.1rem;"></i>
                                            </div>
                                            <div>
                                                <p class="mb-0 fw-medium" style="font-size: 0.9rem; color: #374151;">Surat Pernyataan</p>
                                                <small class="text-muted" style="font-size: 0.75rem;">Surat pernyataan bermaterai</small>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-download-berkas" id="downloadSuratButton" data-download-url="" style="background: #e0f2fe; color: #0284c7; border: none; border-radius: 6px; padding: 6px 12px;">
                                            <i class="bi bi-download"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="modal-footer border-top px-4 py-3" style="background: #f8fafc;">
                <input type="hidden" id="modalMahasiswaId" value="">
                <input type="hidden" id="modalUserId" value="">
                
                <div class="d-flex justify-content-between align-items-center w-100 gap-2">
                    <button type="button" class="btn px-4 py-2" data-bs-dismiss="modal" style="background: white; border: 1px solid #e5e7eb; border-radius: 10px; color: #6b7280;">
                        <i class="bi bi-x-lg me-2"></i>Tutup
                    </button>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn px-4 py-2" id="btnSendMessageToUser" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 10px; box-shadow: 0 4px 6px rgba(102, 126, 234, 0.2);">
                            <i class="bi bi-envelope me-2"></i>Kirim Pesan
                        </button>
                        
                        <!-- ACCEPT BUTTON FOR BELUM UPLOAD STATUS -->
                        <button type="button" class="btn px-4 py-2" id="btnTerimaModal" onclick="acceptParticipant()" style="background: linear-gradient(135deg, #10b981 0%, #059b70 100%); color: white; border: none; border-radius: 10px; box-shadow: 0 4px 6px rgba(16, 185, 129, 0.3); display: none;">
                            <i class="bi bi-check-circle me-2"></i>Verifikasi Berkas
                        </button>
                        
                        <!-- REJECT BUTTON FOR BELUM UPLOAD STATUS -->
                        <button type="button" class="btn px-4 py-2" id="btnTolakModal" onclick="rejectParticipant()" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white; border: none; border-radius: 10px; box-shadow: 0 4px 6px rgba(239, 68, 68, 0.3); display: none;">
                            <i class="bi bi-x-circle me-2"></i>Batalkan Verifikasi Berkas
                        </button>
                        
                        <!-- VERIFICATION BUTTON WITH POPUP -->
                        <button type="button" class="btn px-4 py-2" id="btnVerifikasiModal" onclick="triggerVerificationFromModal()" style="background: linear-gradient(135deg, #10b981 0%, #059b70 100%); color: white; border: none; border-radius: 10px; box-shadow: 0 4px 6px rgba(16, 185, 129, 0.3);">
                            <i class="bi bi-check-circle me-2"></i>Verifikasi Berkas
                        </button>
                        <!-- REJECT BUTTON - HIDDEN BY DEFAULT -->
                        <button type="button" class="btn px-4 py-2" id="btnBatalkanModal" onclick="cancelVerification()" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white; border: none; border-radius: 10px; box-shadow: 0 4px 6px rgba(239, 68, 68, 0.3); display: none;">
                            <i class="bi bi-x-circle me-2"></i>Batal kan Verifikasi
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Detail Modal Styles */
    #detailModal .modal-content {
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }
    
    #detailModal .modal-footer .btn:hover {
        transform: translateY(-2px);
        transition: all 0.2s ease;
    }
    
    #detailModal #btnVerifikasiModal:hover {
        box-shadow: 0 6px 16px rgba(16, 185, 129, 0.4) !important;
    }
    
    #detailModal #btnBatalkanModal:hover {
        box-shadow: 0 6px 16px rgba(239, 68, 68, 0.4) !important;
    }
    
    #detailModal #btnSendMessageToUser:hover {
        box-shadow: 0 6px 16px rgba(102, 126, 234, 0.3) !important;
    }
    
    /* Ensure modal body is scrollable */
    #detailModal .modal-body {
        max-height: calc(100vh - 300px);
        overflow-y: auto;
    }
    
    
    #detailModal .berkas-btn:hover {
        background: #f1f5f9 !important;
        border-color: #cbd5e1 !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    
    #detailModal .berkas-btn:hover .bi-download {
        color: #667eea !important;
    }
    
    #detailModal .rounded-4 {
        border-radius: 16px !important;
    }
    
    #detailModal .modal-body::-webkit-scrollbar {
        width: 6px;
    }
    
    #detailModal .modal-body::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 3px;
    }
    
    #detailModal .modal-body::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 3px;
    }
    
    #detailModal .modal-body::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
    
    /* Status Icon Styles */
    .status-icon-verified {
        background: #10b981;
        color: white;
    }

    .status-icon-pending {
        background: #f59e0b;
        color: white;
    }

    .status-icon-none {
        background: #6b7280;
        color: white;
    }
    
    /* Animation */
    #detailModal .modal-dialog {
        animation: modalSlideIn 0.3s ease-out;
    }
    
    @keyframes modalSlideIn {
        from {
            opacity: 0;
            transform: translateY(-30px) scale(0.95);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }
</style>

<!-- Modal Kirim Pesan Individual -->
<div class="modal fade" id="sendMessageModal" tabindex="-1" aria-labelledby="sendMessageModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="sendMessageModalLabel">
                    <i class="bi bi-envelope me-2"></i>Kirim Pesan
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Kepada:</label>
                    <p class="mb-0" id="messageRecipient">-</p>
                </div>
                <div class="mb-3">
                    <label for="individualMessage" class="form-label fw-semibold">Pesan:</label>
                    <textarea class="form-control" id="individualMessage" rows="4" placeholder="Tulis pesan untuk peserta..." required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <input type="hidden" id="messageUserId" value="">
                <input type="hidden" id="messageMahasiswaId" value="">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="sendIndividualMessage">
                    <i class="bi bi-send me-1"></i>Kirim
                </button>
            </div>
        </div>
    </div>
</div>



<!-- Bootstrap Bundle JS -->


<!-- Load Custom JavaScript -->
<script src="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Script/admin/participants.js"></script>


