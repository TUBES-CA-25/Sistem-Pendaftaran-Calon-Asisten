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

<main class="max-w-7xl mx-auto pb-8">
    <div class="grid grid-cols-1 gap-6">

        <!-- Form / Display Card (kartu foto+nama di kiri sudah dihapus, jadi
             kartu ini memakai lebar penuh) -->
        <div>
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
                                <input type="text" id="stambuk_display" class="w-full px-4 py-3 rounded-xl border border-slate-100 bg-slate-50 text-slate-400 cursor-not-allowed text-sm font-semibold" value="<?= htmlspecialchars($stambuk) ?>" readonly>
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
                                <select id="jurusan" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm text-slate-700 font-semibold transition bg-white" name="jurusan" required>
                                    <option value="Teknik informatika" <?= (strtolower($jurusan) === 'teknik informatika') ? 'selected' : '' ?>>Teknik Informatika</option>
                                    <option value="Sistem informasi" <?= (strtolower($jurusan) === 'sistem informasi') ? 'selected' : '' ?>>Sistem Informasi</option>
                                </select>
                            </div>
                            <div>
                                <label for="floatingSelect" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Kelas</label>
                                <select class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm text-slate-700 font-semibold transition bg-white" id="floatingSelect" name="kelas" required>
                                    <option disabled <?= ($kelas === 'Kelas' || empty($kelas)) ? 'selected' : '' ?>>Pilih Kelas Anda</option>
                                    <?php 
                                    $genderLower = strtolower($jenisKelamin);
                                    
                                    $initialKelasOptions = [];
                                    if ($genderLower === 'wanita') {
                                        $initialKelasOptions = ["B1", "B2", "B3", "B4", "B5"];
                                    } else if ($genderLower === 'pria' || $genderLower === 'laki-laki') {
                                        $initialKelasOptions = ["A1", "A2", "A3", "A4", "A5", "A6", "A7", "A8", "A9"];
                                    } else {
                                        $initialKelasOptions = [
                                            "A1", "A2", "A3", "A4", "A5", "A6", "A7", "A8", "A9",
                                            "B1", "B2", "B3", "B4", "B5"
                                        ];
                                    }

                                    $isCustomKelas = ($kelas !== 'Kelas' && !empty($kelas) && !in_array($kelas, $initialKelasOptions));

                                    foreach ($initialKelasOptions as $kelasOps) : 
                                    ?>
                                        <option value="<?= htmlspecialchars($kelasOps) ?>" <?= ($kelas === $kelasOps) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($kelasOps) ?>
                                        </option>
                                    <?php endforeach; ?>
                                    <option value="Lainnya" <?= $isCustomKelas ? 'selected' : '' ?>>Lainnya (Input Manual)</option>
                                </select>
                            </div>
                            
                            <div id="kelasManualContainer" style="display: <?= $isCustomKelas ? 'block' : 'none' ?>;" class="mt-3">
                                <label for="kelasManual" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Input Kelas Manual</label>
                                <input type="text" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm text-slate-700 font-semibold transition" id="kelasManual" placeholder="Contoh: A10 atau B7" value="<?= $isCustomKelas ? htmlspecialchars($kelas) : '' ?>">
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
                            <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-xl transition flex items-center gap-2" name="submit" aria-label="Konfirmasi">
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
const dbClasses = <?= json_encode(\App\Model\BiodataUser::getAllKelas()) ?>;
function enableEditMode() {
    document.getElementById('displaySection').style.display = 'none';
    document.getElementById('formSection').style.display = 'block';
    
    if (typeof updateKelasOptions === "function") {
        updateKelasOptions();
    } else {
        const gender = document.querySelector('input[name="gender"]:checked');
        if (gender) gender.dispatchEvent(new Event('change', { bubbles: true }));
    }
}

function cancelEdit() {
    document.getElementById('formSection').style.display = 'none';
    document.getElementById('displaySection').style.display = 'block';
}

// Vanilla JS (tanpa jQuery). dom.on() = delegasi di document -> idempoten
// terhadap SPA re-inject #content.
(function () {
})();
</script>

<script src="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/js/user/biodata.js"></script>
