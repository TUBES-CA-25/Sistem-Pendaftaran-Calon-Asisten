<?php
/**
 * Import/Export Soal Page
 * @var array $data
 */
$bankSoalList = $data['bankSoalList'] ?? [];
?>

<main>
    <!-- Page Header -->
    <?php
        $title = 'Import & Export Soal';
        $subtitle = 'Kelola pemindahan data soal via Excel/CSV';
        $icon = 'bx bx-transfer';
        require_once __DIR__ . '/../../templates/components/PageHeader.php';
    ?>

    <div class="max-w-7xl mx-auto px-4 py-6">
        <!-- View: Import/Export -->
        <div id="contentImportExport" class="pb-12">
            <div class="flex flex-col gap-8">
                <!-- Import Section -->
                <div class="w-full">
                    <div class="bg-gradient-to-br from-blue-50 to-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                        <div class="p-6 md:p-8 lg:p-10">
                            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                                <div class="lg:col-span-7">
                                    <div class="flex items-center mb-6">
                                        <div class="rounded-2xl flex items-center justify-content-center mr-4 shadow-sm w-14 h-14 bg-blue-600 flex-shrink-0 text-white">
                                            <i class='bx bx-import text-2xl'></i>
                                        </div>
                                        <div>
                                            <h4 class="text-xl font-bold text-blue-900 mb-1">Import Soal Baru</h4>
                                            <p class="text-slate-500 text-sm">Tambahkan soal masal ke bank soal pilihan Anda</p>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-6">
                                        <label class="block text-sm font-bold text-slate-800 mb-2">1. Pilih Bank Soal Tujuan</label>
                                        <select class="w-full px-4 py-3 rounded-xl border-2 border-sky-100 bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm text-slate-700 font-semibold transition" id="selectedBankSoalImport" onchange="updateImportButtonState()">
                                            <option value="" selected disabled>-- Pilih Bank Soal --</option>
                                            <?php foreach ($bankSoalList as $bank): ?>
                                            <option value="<?= $bank['id'] ?>" 
                                                    data-name="<?= htmlspecialchars($bank['nama'] ?? '') ?>"
                                                    data-count="<?= $bank['jumlah_soal'] ?>"
                                                    data-pg="<?= $bank['pg_count'] ?>"
                                                    data-essay="<?= $bank['essay_count'] ?>">
                                                <?= htmlspecialchars($bank['nama'] ?? '') ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="mb-6">
                                        <label class="block text-sm font-bold text-slate-800 mb-2">2. Upload File Data Soal</label>
                                        <div class="upload-zone p-6 border-2 border-dashed border-blue-400 rounded-2xl text-center bg-slate-50/50 hover:bg-blue-50/30 transition duration-200 relative">
                                            <input type="file" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" id="importFile" accept=".csv, .xls, .xlsx" onchange="updateImportButtonState(); updateFileLabel()">
                                            <div id="uploadContent" class="flex flex-col items-center">
                                                <i class='bx bx-cloud-upload text-blue-600 mb-2 text-5xl'></i>
                                                <h6 class="font-bold text-slate-800 text-sm mb-1" id="fileLabel">Klik atau drag file ke sini</h6>
                                                <p class="text-slate-500 text-xs">Mendukung .csv, .xls, .xlsx (Maksimal 5MB)</p>
                                            </div>
                                        </div>
                                        <div id="fileInfo" class="mt-2 text-center text-sm font-semibold text-emerald-600 hidden"></div>
                                    </div>
                                </div>

                                <div class="lg:col-span-5 h-full">
                                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex flex-col justify-between h-full">
                                        <div>
                                            <h6 class="font-bold text-slate-800 mb-4 flex items-center gap-1.5"><i class='bx bx-info-circle text-blue-600 text-lg'></i> Panduan Import</h6>
                                            <ul class="space-y-3 text-xs text-slate-600 mb-6">
                                                <li class="flex items-start gap-2"><i class='bx bx-check text-emerald-500 text-lg flex-shrink-0'></i> <span>Gunakan format kolom template yang disediakan.</span></li>
                                                <li class="flex items-start gap-2"><i class='bx bx-check text-emerald-500 text-lg flex-shrink-0'></i> <span>Kolom wajib: Deskripsi, Tipe (PG/Essay), Jawaban.</span></li>
                                                <li class="flex items-start gap-2"><i class='bx bx-check text-emerald-500 text-lg flex-shrink-0'></i> <span>Untuk PG, isi kolom Pilihan A sampai E.</span></li>
                                            </ul>
                                        </div>
                                        
                                        <div class="flex flex-col gap-3">
                                            <button class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-xl transition disabled:opacity-50 disabled:cursor-not-allowed shadow-sm flex items-center justify-center gap-2" onclick="importSoal()" id="btnImport" disabled>
                                                <i class='bx bx-download text-lg'></i> Mulai Proses Import
                                            </button>
                                            <a href="javascript:void(0)" onclick="downloadTemplate()" class="w-full py-2.5 text-center border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold text-sm rounded-xl transition flex items-center justify-center gap-2">
                                                <i class='bx bx-file-blank text-lg'></i> Download Template Excel
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Export Section -->
                <div class="w-full">
                    <div class="bg-gradient-to-br from-emerald-50 to-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                        <div class="p-6 md:p-8 lg:p-10">
                            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                                <div class="lg:col-span-7">
                                    <div class="flex items-center mb-6">
                                        <div class="rounded-2xl flex items-center justify-content-center mr-4 shadow-sm w-14 h-14 bg-emerald-600 flex-shrink-0 text-white">
                                            <i class='bx bx-export text-2xl'></i>
                                        </div>
                                        <div>
                                            <h4 class="text-xl font-bold text-emerald-900 mb-1">Export Data Soal</h4>
                                            <p class="text-slate-500 text-sm">Unduh seluruh soal dari database ke format Excel</p>
                                        </div>
                                    </div>

                                    <div class="mb-6">
                                        <label class="block text-sm font-bold text-slate-800 mb-2">Pilih Bank Soal Sumber</label>
                                        <select class="w-full px-4 py-3 rounded-xl border-2 border-emerald-100 bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm text-slate-700 font-semibold transition" id="selectedBankSoal" onchange="updateExportButtonState()">
                                            <option value="" selected disabled>-- Pilih Bank Soal --</option>
                                            <?php foreach ($bankSoalList as $bank): ?>
                                            <option value="<?= $bank['id'] ?>" 
                                                    data-name="<?= htmlspecialchars($bank['nama'] ?? '') ?>" 
                                                    data-count="<?= $bank['jumlah_soal'] ?>"
                                                    data-pg="<?= $bank['pg_count'] ?>"
                                                    data-essay="<?= $bank['essay_count'] ?>">
                                                <?= htmlspecialchars($bank['nama'] ?? '') ?> (<?= $bank['jumlah_soal'] ?> soal)
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <button class="w-full md:w-auto px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm rounded-xl transition disabled:opacity-50 disabled:cursor-not-allowed shadow-sm flex items-center justify-center gap-2" onclick="exportSoal()" id="btnExport" disabled>
                                        <i class='bx bx-download text-lg'></i> Unduh File Excel (.xlsx)
                                    </button>
                                </div>

                                <div class="lg:col-span-5">
                                    <div id="exportSummary" class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                                        <h6 class="font-bold text-slate-800 mb-4">Ringkasan Data Terpilih</h6>
                                        <div class="grid grid-cols-3 gap-4">
                                            <div class="text-center">
                                                <h3 class="text-2xl font-bold mb-1 text-emerald-600" id="exportTotalCount">-</h3>
                                                <span class="block text-slate-400 text-[10px] font-bold uppercase tracking-wider">Total Soal</span>
                                            </div>
                                            <div class="text-center border-l border-r border-slate-100">
                                                <h3 class="text-2xl font-bold mb-1 text-blue-600" id="exportPGCount">-</h3>
                                                <span class="block text-slate-400 text-[10px] font-bold uppercase tracking-wider">Pilihan Ganda</span>
                                            </div>
                                            <div class="text-center">
                                                <h3 class="text-2xl font-bold mb-1 text-amber-500" id="exportEssayCount">-</h3>
                                                <span class="block text-slate-400 text-[10px] font-bold uppercase tracking-wider">Essay</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    // Global Base URL and data for external scripts
    var baseUrl = '<?= APP_URL ?>';
    window.bankSoalList = <?= json_encode($bankSoalList) ?>;
</script>

<!-- Load External JavaScript -->
<script src="<?= APP_URL ?>/Assets/js/exam_import_export.js"></script>
