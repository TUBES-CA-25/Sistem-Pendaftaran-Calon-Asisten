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

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-8">
    <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
        <!-- Left Column: Profile Card -->
        <div class="md:col-span-4 lg:col-span-3">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 text-center p-6 h-full flex flex-col justify-center items-center">
                <div class="relative w-32 h-32 mb-4 group cursor-pointer overflow-hidden rounded-2xl border-4 border-slate-50 shadow-inner profile-img-container">
                    <img src="<?= $photo ?>" alt="Profile Picture" class="w-full h-full object-cover profile-img-target" onerror="this.src='/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Downloads/default.png'">
                    <div onclick="document.getElementById('profileImageInput').click();" 
                         class="absolute inset-0 bg-slate-900/60 flex flex-col items-center justify-center text-white opacity-0 group-hover:opacity-100 transition-opacity duration-200 profile-overlay">
                        <i class="bx bx-camera text-2xl mb-1"></i>
                        <span class="text-[10px] font-bold tracking-wider uppercase">Ubah Foto</span>
                    </div>
                </div>
                <input type="file" id="profileImageInput" class="hidden" accept="image/*">
                <h5 class="font-bold text-slate-800 mb-1 text-wrap text-break"><?= htmlspecialchars($nama) ?></h5>
                <p class="text-slate-400 text-xs flex items-center justify-center gap-1">
                    <i class="bx bx-id-card"></i><?= htmlspecialchars($stambuk) ?>
                </p>
            </div>
        </div>

        <!-- Right Column: Form / Display Card -->
        <div class="md:col-span-8 lg:col-span-9">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 lg:p-8 h-full">
                <?php 
                    // Determine initial visibility
                    $formDisplay = $isBiodataEmpty ? 'block' : 'none';
                    $viewDisplay = $isBiodataEmpty ? 'none' : 'block';
                ?>

                <!-- Form Section (Edit/Create) -->
                <div id="formSection" style="display: <?= $formDisplay ?>;">
                    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 mb-6">
                        <h5 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                            <i class="bi bi-pencil-square text-blue-600"></i><?= $isBiodataEmpty ? 'Isi Biodata' : 'Edit Biodata' ?>
                        </h5>
                        <?php if (!$isBiodataEmpty): ?>
                            <button type="button" class="px-4 py-2 border border-slate-200 text-slate-600 hover:bg-slate-50 font-semibold text-sm rounded-xl transition flex items-center gap-1.5" onclick="cancelEdit()">
                                <i class="bi bi-x-lg"></i>Batal
                            </button>
                        <?php endif; ?>
                    </div>

                    <form id="biodataForm" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="nama" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Nama Lengkap</label>
                                <input type="text" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm text-slate-700 font-semibold transition" id="nama" name="nama" 
                                    placeholder="Nama Lengkap" 
                                    value="<?= ($nama !== 'Nama Lengkap') ? htmlspecialchars($nama) : '' ?>" required>
                            </div>
                            <div>
                                <label for="stambuk_display" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Stambuk</label>
                                <input type="text" class="w-full px-4 py-3 rounded-xl border border-slate-100 bg-slate-50 text-slate-400 cursor-not-allowed text-sm font-semibold" value="<?= htmlspecialchars($stambuk) ?>" readonly>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Jenis Kelamin</label>
                            <div class="flex gap-6 items-center">
                                <label class="flex items-center gap-2 text-sm text-slate-600 cursor-pointer">
                                    <input type="radio" name="gender" id="inlineRadio1" value="wanita" required onclick="updateKelasOptions()"
                                        <?= (strtolower($jenisKelamin) === 'wanita') ? 'checked' : '' ?>
                                        class="w-4 h-4 text-blue-600 border-slate-300 focus:ring-blue-500">
                                    <span>Wanita</span>
                                </label>
                                <label class="flex items-center gap-2 text-sm text-slate-600 cursor-pointer">
                                    <input type="radio" name="gender" id="inlineRadio2" value="pria" required onclick="updateKelasOptions()"
                                        <?= (strtolower($jenisKelamin) === 'pria' || strtolower($jenisKelamin) === 'laki-laki') ? 'checked' : '' ?>
                                        class="w-4 h-4 text-blue-600 border-slate-300 focus:ring-blue-500">
                                    <span>Pria</span>
                                </label>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="jurusan" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Jurusan</label>
                                <select class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm text-slate-700 font-semibold transition bg-white" name="jurusan" required>
                                    <option value="Teknik informatika" <?= (strtolower($jurusan) === 'teknik informatika') ? 'selected' : '' ?>>Teknik Informatika</option>
                                    <option value="Sistem informasi" <?= (strtolower($jurusan) === 'sistem informasi') ? 'selected' : '' ?>>Sistem Informasi</option>
                                </select>
                            </div>
                            <div>
                                <label for="kelas" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Kelas</label>
                                <select class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm text-slate-700 font-semibold transition bg-white" id="floatingSelect" name="kelas" required>
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

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="alamat" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Alamat</label>
                                <input type="text" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm text-slate-700 font-semibold transition" id="alamat" name="alamat" 
                                    placeholder="Alamat" 
                                    value="<?= ($alamat !== 'Alamat') ? htmlspecialchars($alamat) : '' ?>" required>
                            </div>
                            <div>
                                <label for="tempatlahir" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Kota Asal</label>
                                <input type="text" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm text-slate-700 font-semibold transition" id="tempatlahir" name="tempatlahir" 
                                    placeholder="Tempat Lahir" 
                                    value="<?= ($tempatLahir !== 'Tempat Lahir') ? htmlspecialchars($tempatLahir) : '' ?>" required>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="tanggallahir" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Tanggal Lahir</label>
                                <input type="date" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm text-slate-700 font-semibold transition" id="tanggallahir" name="tanggallahir" 
                                    value="<?= ($tanggalLahir !== 'Tanggal Lahir') ? htmlspecialchars($tanggalLahir) : '' ?>" required>
                            </div>
                            <div>
                                <label for="telephone" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">No Telephone</label>
                                <input type="text" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm text-slate-700 font-semibold transition" id="telephone" name="telephone" 
                                    placeholder="No Telephone" 
                                    value="<?= ($noHp !== 'No Telephone') ? htmlspecialchars($noHp) : '' ?>" required>
                            </div>
                        </div>

                        <div class="flex gap-3 justify-end mt-8 pt-4 border-t border-slate-100">
                            <?php if ($isBiodataEmpty): ?>
                                <button type="reset" class="px-5 py-2.5 border border-slate-200 text-slate-600 hover:bg-slate-50 font-semibold text-sm rounded-xl transition flex items-center gap-2">
                                    <i class="bi bi-arrow-counterclockwise"></i>Reset
                                </button>
                            <?php endif; ?>
                            <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-xl transition flex items-center gap-2" name="submit">
                                <i class="bi bi-check-circle"></i><?= $isBiodataEmpty ? 'Submit' : 'Update' ?>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Display Section (Read Only) -->
                <div id="displaySection" style="display: <?= $viewDisplay ?>;">
                    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 mb-6">
                        <h5 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                            <i class="bi bi-person-badge text-blue-600"></i>Data Diri
                        </h5>
                        <button class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-xl transition flex items-center gap-2" onclick="enableEditMode()">
                            <i class="bi bi-pencil"></i>Edit Biodata
                        </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Nama Lengkap</label>
                            <div class="w-full px-4 py-3 rounded-xl border border-slate-100 bg-slate-50/50 text-slate-700 font-semibold text-sm"><?= htmlspecialchars($nama) ?></div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Stambuk</label>
                            <div class="w-full px-4 py-3 rounded-xl border border-slate-100 bg-slate-50/50 text-slate-700 font-semibold text-sm"><?= htmlspecialchars($stambuk) ?></div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Jurusan</label>
                            <div class="w-full px-4 py-3 rounded-xl border border-slate-100 bg-slate-50/50 text-slate-700 font-semibold text-sm"><?= htmlspecialchars($jurusan) ?></div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Jenis Kelamin</label>
                            <div class="w-full px-4 py-3 rounded-xl border border-slate-100 bg-slate-50/50 text-slate-700 font-semibold text-sm"><?= htmlspecialchars($jenisKelamin) ?></div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Kelas</label>
                            <div class="w-full px-4 py-3 rounded-xl border border-slate-100 bg-slate-50/50 text-slate-700 font-semibold text-sm"><?= htmlspecialchars($kelas) ?></div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Alamat</label>
                            <div class="w-full px-4 py-3 rounded-xl border border-slate-100 bg-slate-50/50 text-slate-700 font-semibold text-sm"><?= htmlspecialchars($alamat) ?></div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Tempat Lahir</label>
                            <div class="w-full px-4 py-3 rounded-xl border border-slate-100 bg-slate-50/50 text-slate-700 font-semibold text-sm"><?= htmlspecialchars($tempatLahir) ?></div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Tanggal Lahir</label>
                            <div class="w-full px-4 py-3 rounded-xl border border-slate-100 bg-slate-50/50 text-slate-700 font-semibold text-sm"><?= htmlspecialchars($tanggalLahir) ?></div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">No Telephone</label>
                            <div class="w-full px-4 py-3 rounded-xl border border-slate-100 bg-slate-50/50 text-slate-700 font-semibold text-sm"><?= htmlspecialchars($noHp) ?></div>
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
            spinner.className = 'animate-spin rounded-full h-5 w-5 border-2 border-white border-t-transparent';
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
