<?php
/**
 * Tes Tulis Admin - Bank Soal Management
 * Modern Bootstrap 5 Design with Bank Soal System
 * 
 * MVC Pattern: This View only displays data. 
 * Business logic is handled by Model, data prepared by Controller.
 */
use App\Controllers\User\TesTulisController;

// Get all data from Controller (proper MVC pattern)
$pageData = TesTulisController::getAdminExamPageData();
$bankSoalList = $pageData['bankSoalList'];
$allSoal = $pageData['allSoal'];
$stats = $pageData['stats'];

// Use statistics from Model (no counting logic in View)
$bankCount = $stats['bank_count'];
$totalSoal = $stats['total_soal'];
$pgCount = $stats['pg_count'];
$essayCount = $stats['essay_count'];

// Tab yang terbuka saat halaman dimuat. Rute lama /importSoal harus mendarat
// langsung di tab Import & Export supaya tautan yang sudah tersebar tidak
// kehilangan tujuannya.
//
// Dua jalur pemuatan memberi petunjuk lewat variabel berbeda: mode SPA
// mengirim $data['tabAwal'] dari HomeController, sedangkan pemuatan penuh
// meng-include berkas ini langsung dari main_admin.php sehingga hanya
// $initialPage yang tersedia. Keduanya diperiksa.
$tabAwal = (($data['tabAwal'] ?? '') === 'impor' || ($initialPage ?? '') === 'importSoal')
    ? 'impor'
    : 'bank';
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/easymde/dist/easymde.min.css">
<script src="https://cdn.jsdelivr.net/npm/easymde/dist/easymde.min.js"></script>

<!-- Page Header -->
<div id="pageHeaderWrapper" class="transition-all duration-300">
<?php
    $title = 'Bank Soal';
    $subtitle = 'Kelola kumpulan soal dan akses token ujian';
    $icon = 'bi bi-journal-richtext';
    require_once __DIR__ . '/../../templates/components/PageHeader.php';
?>
</div>

<?php /* Batang gulir modal soal disembunyikan.

         Ditulis sebagai CSS biasa karena Tailwind tidak punya utility untuk
         menyembunyikan batang gulir, dan ketiga properti di bawah dibutuhkan
         sekaligus: `scrollbar-width` untuk Firefox, `-ms-overflow-style` untuk
         Edge lama, dan pseudo-elemen ::-webkit-scrollbar untuk Chrome/Safari.

         Yang disembunyikan hanya BATANGNYA - `overflow-y: auto` tetap ada
         sehingga isinya masih bisa digulir dengan roda tetikus, sentuhan,
         maupun papan ketik. */ ?>
<style>
    /* Tinggi modal memakai dvh, BUKAN vh.
       Di peramban HP, `vh` dihitung terhadap layar tanpa memperhitungkan bilah
       alamat, sehingga modal setinggi 85vh bisa menjulur ke bawah area yang
       benar-benar terlihat dan tombol Simpan jadi tak terjangkau. `dvh`
       mengikuti tinggi yang benar-benar tersedia saat itu.

       Baris `vh` ditulis lebih dulu sebagai cadangan untuk peramban lama yang
       belum mengenal dvh - keduanya WAJIB ada, bukan sekadar berjaga. */
    .panel-modal-soal {
        max-height: 85vh;
        max-height: 85dvh;
    }
    .gulir-tanpa-batang {
        /* 150px = tinggi kepala + kaki modal */
        max-height: calc(85vh - 150px);
        max-height: calc(85dvh - 150px);
        overflow-y: auto;
        scrollbar-width: none;        /* Firefox */
        -ms-overflow-style: none;     /* Edge lama */
    }
    .gulir-tanpa-batang::-webkit-scrollbar {
        width: 0;
        height: 0;
        display: none;                /* Chrome, Edge, Safari */
    }
</style>

<main class="max-w-7xl mx-auto pt-0 pb-6 [&_.editor-toolbar]:!border-slate-200 [&_.editor-toolbar]:rounded-t-xl [&_.CodeMirror]:!border-slate-200 [&_.CodeMirror]:rounded-b-xl [&_.CodeMirror]:min-h-[200px] [&_.CodeMirror]:max-h-[400px] [&_.editor-statusbar]:hidden [&_.condition-render-markdown_img]:max-w-full [&_.condition-render-markdown_img]:max-h-[400px] [&_.condition-render-markdown_img]:object-contain [&_.condition-render-markdown_img]:rounded-xl [&_.condition-render-markdown_img]:my-2.5 [&_.condition-render-markdown_img]:border [&_.condition-render-markdown_img]:border-slate-200 [&_.condition-render-markdown_img]:block [&_.type-option.selected]:bg-blue-600/5 [&_.type-option.selected]:!border-blue-600 [&_.type-option.selected_.check-icon]:!block [&_.EasyMDEContainer]:z-[1055]">
<!--
    Tab navigasi. Hanya tampil di daftar bank soal; saat masuk ke rincian
    sebuah bank (bankDetailView) tab ini disembunyikan supaya konteksnya
    tidak rancu - pengguna sedang mengelola satu bank, bukan berpindah menu.
-->
<div id="soalTabs" class="flex items-center gap-1 mb-4 border-b border-slate-200">
    <?php if ($tabAwal === 'impor'): ?>
    <button type="button" id="tabBankSoal" onclick="switchSoalTab('bank')"
            class="soal-tab px-4 py-2.5 text-sm font-bold rounded-t-lg border-b-2 transition-all flex items-center gap-2 border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50">
        <i class="bi bi-journal-richtext"></i> Daftar Bank Soal
    </button>
    <button type="button" id="tabImporSoal" onclick="switchSoalTab('impor')"
            class="soal-tab px-4 py-2.5 text-sm font-bold rounded-t-lg border-b-2 transition-all flex items-center gap-2 border-blue-600 text-blue-600 bg-blue-50/50">
        <i class="bx bx-transfer"></i> Import &amp; Export
    </button>
    <?php else: ?>
    <button type="button" id="tabBankSoal" onclick="switchSoalTab('bank')"
            class="soal-tab px-4 py-2.5 text-sm font-bold rounded-t-lg border-b-2 transition-all flex items-center gap-2 border-blue-600 text-blue-600 bg-blue-50/50">
        <i class="bi bi-journal-richtext"></i> Daftar Bank Soal
    </button>
    <button type="button" id="tabImporSoal" onclick="switchSoalTab('impor')"
            class="soal-tab px-4 py-2.5 text-sm font-bold rounded-t-lg border-b-2 transition-all flex items-center gap-2 border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50">
        <i class="bx bx-transfer"></i> Import &amp; Export
    </button>
    <?php endif; ?>
</div>

<div class="bank-list-view<?= $tabAwal === 'impor' ? ' hidden' : '' ?>" id="bankListView">
        <!-- Stats Bar -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-5">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 hover:shadow-md hover:border-slate-300 transition-all duration-200">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                        <i class="bi bi-folder-fill text-lg"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Bank Soal</div>
                        <div class="text-2xl font-bold text-slate-800 leading-tight tabular-nums" id="stat-count-bank"><?= $bankCount ?></div>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 hover:shadow-md hover:border-slate-300 transition-all duration-200">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                        <i class="bi bi-file-earmark-text-fill text-lg"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Soal</div>
                        <div class="text-2xl font-bold text-slate-800 leading-tight tabular-nums" id="stat-count-total"><?= $totalSoal ?></div>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 hover:shadow-md hover:border-slate-300 transition-all duration-200">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center shrink-0">
                        <i class="bi bi-ui-checks-grid text-lg"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Pilihan Ganda</div>
                        <div class="text-2xl font-bold text-slate-800 leading-tight tabular-nums" id="stat-count-pg"><?= $pgCount ?></div>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 hover:shadow-md hover:border-slate-300 transition-all duration-200">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                        <i class="bi bi-pencil-square text-lg"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Essay</div>
                        <div class="text-2xl font-bold text-slate-800 leading-tight tabular-nums" id="stat-count-essay"><?= $essayCount ?></div>
                    </div>
                </div>
            </div>
        </div>

    <!-- Bank Soal List Table -->
    <?php /* Bentuk tabel menggantikan grid kartu. Kontrak JS dipertahankan
             seluruhnya: id bank-card-<id> kini menempel pada <tr> (exam.js
             menghapusnya lewat .remove(), jadi <tr> aman), sedangkan
             #bankGrid berpindah ke <tbody> - exam.js membaca .children.length
             untuk mendeteksi tabel kosong, dan <tbody> tetap memenuhi itu. */ ?>
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden mb-6">

        <?php /* Tombol dipindah MASUK ke dalam kartu tabel + class vp-custom-button.
                 VanillaPaginator (app.js) memindahkannya ke baris kontrol di samping
                 kotak "Cari data..." lalu melepas class "hidden"-nya.

                 Letak elemen ini penting: paginator hanya memindai tombol di dalam
                 table.closest('div').parentElement - yaitu kartu ini. Kalau ditaruh
                 di luar kartu, tombol tidak ditemukan dan tetap tersembunyi.

                 #btnCreateBank dipertahankan - dipakai sebagai pemicu modal. */ ?>
        <button class="vp-custom-button hidden px-5 py-2.5 bg-gradient-to-r from-primary to-secondary hover:from-primary-hover hover:to-secondary-hover text-white font-bold rounded-xl items-center gap-2 shadow-md shadow-blue-500/20 transition-all border-0 text-sm whitespace-nowrap" type="button" id="btnCreateBank" data-modal-open="#createBankModal">
            <i class="bi bi-plus-circle-fill"></i>
            <span>Buat Bank Baru</span>
        </button>

        <?php if (empty($bankSoalList)): ?>
        <div class="text-center py-16 flex flex-col items-center justify-center p-8">
            <i class='bx bx-folder-open text-slate-300 text-6xl mb-4'></i>
            <h3 class="text-lg font-bold text-slate-700 mb-1">Belum Ada Bank Soal</h3>
            <p class="text-slate-500 text-sm max-w-sm">Klik tombol "Buat Bank Baru" untuk membuat bank soal pertama</p>
        </div>
        <?php endif; ?>

        <div class="overflow-x-auto">
            <table class="min-w-full align-middle text-sm text-left no-datatable" id="bankSoalTable" data-paginator="true" data-paginator-perpage="10">
                <thead>
                    <tr>
                        <th class="dt-head-cell text-center" style="width: 5%;">No</th>
                        <th class="dt-head-cell text-left" style="width: 28%;">Nama Bank Soal</th>
                        <th class="dt-head-cell text-left" style="width: 13%;">Token</th>
                        <th class="dt-head-cell text-center" style="width: 8%;">Total</th>
                        <th class="dt-head-cell text-left" style="width: 20%;">Komposisi</th>
                        <th class="dt-head-cell text-center" style="width: 13%;">Status</th>
                        <th class="dt-head-cell text-center" style="width: 13%;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="bankGrid" class="dt-tbody">
                    <?php $nomor = 0; foreach ($bankSoalList as $bank):
                        $nomor++;
                        $total    = (int)($bank['jumlah_soal'] ?? 0);
                        $pg       = (int)($bank['pg_count'] ?? 0);
                        $essay    = (int)($bank['essay_count'] ?? 0);
                        $pgPct    = $total > 0 ? round($pg / $total * 100) : 0;
                        $essayPct = $total > 0 ? round($essay / $total * 100) : 0;
                        $aktif    = ($bank['is_active'] ?? 0) == 1;
                        $namaBank = htmlspecialchars($bank['nama'] ?? '');
                    ?>
                    <tr id="bank-card-<?= $bank['id'] ?>" class="border-t border-slate-100 hover:bg-blue-50/40 transition-colors">
                        <td class="px-4 py-3.5 text-center text-[13px] text-slate-400 font-semibold tabular-nums"><?= $nomor ?></td>

                        <!-- Nama bank: pemicu buka detail -->
                        <td class="px-4 py-3.5">
                            <button type="button"
                                    class="group/nama flex items-center gap-3 text-left bg-transparent border-0 p-0 cursor-pointer w-full"
                                    onclick="openBankDetail(<?= $bank['id'] ?>, '<?= $namaBank ?>')"
                                    title="<?= $namaBank ?>">
                                <span class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 shadow-sm transition-transform duration-200 group-hover/nama:scale-105 bg-gradient-to-br from-primary to-secondary text-white">
                                    <i class="bi bi-folder-fill text-base"></i>
                                </span>
                                <span class="min-w-0">
                                    <span class="bank-nama block font-bold text-slate-700 text-[13px] leading-snug group-hover/nama:text-blue-600 transition-colors line-clamp-1">
                                        <?= $namaBank ?>
                                    </span>
                                    <span class="block text-[11px] font-medium text-slate-400 leading-tight">
                                        <?= $total ?> soal &middot; klik untuk detail
                                    </span>
                                </span>
                            </button>
                        </td>

                        <!-- Token -->
                        <td class="px-4 py-3.5">
                            <span class="inline-flex items-center gap-1.5 text-[11px] font-bold px-2.5 py-1 rounded-lg bg-amber-50 text-amber-700 border border-amber-200 tracking-wide">
                                <i class="bi bi-key-fill text-[10px]"></i><?= htmlspecialchars($bank['token'] ?? '') ?>
                            </span>
                        </td>

                        <!-- Total soal -->
                        <td class="px-4 py-3.5 text-center">
                            <span class="inline-flex items-center justify-center min-w-[2rem] px-2 py-1 rounded-lg text-[13px] font-extrabold tabular-nums <?= $total > 0 ? 'bg-blue-50 text-blue-600' : 'bg-slate-100 text-slate-400' ?>"><?= $total ?></span>
                        </td>

                        <!-- Komposisi PG / Essay -->
                        <td class="px-4 py-3.5">
                            <?php /* Bank tanpa soal: bar kosong tidak menyampaikan apa-apa,
                                     jadi diganti keterangan eksplisit. Sebelumnya barnya
                                     tampil sebagai garis abu polos yang membingungkan. */ ?>
                            <?php if ($total === 0): ?>
                                <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold text-slate-400 border border-dashed border-slate-300 rounded-lg px-2.5 py-1"><i class="bi bi-inbox"></i>Belum ada soal</span>
                            <?php else: ?>
                                <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden flex mb-1.5">
                                    <div class="h-full bg-blue-500 transition-all duration-500" style="width:<?= $pgPct ?>%"></div>
                                    <div class="h-full bg-amber-400 transition-all duration-500" style="width:<?= $essayPct ?>%"></div>
                                </div>
                                <div class="flex gap-3">
                                    <span class="flex items-center gap-1.5 text-[10px] font-bold text-slate-500">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500 inline-block"></span>PG <?= $pg ?> <span class="text-slate-400 font-medium">(<?= $pgPct ?>%)</span>
                                    </span>
                                    <span class="flex items-center gap-1.5 text-[10px] font-bold text-slate-500">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400 inline-block"></span>Essay <?= $essay ?> <span class="text-slate-400 font-medium">(<?= $essayPct ?>%)</span>
                                    </span>
                                </div>
                            <?php endif; ?>
                        </td>

                        <!-- Status: saklar + label + badge (ketiganya kontrak exam.js) -->
                        <td class="px-4 py-3.5">
                            <div class="flex items-center justify-center gap-2.5">
                                <label class="relative inline-flex items-center cursor-pointer mb-0" title="Klik untuk mengubah status bank soal">
                                    <input type="checkbox" id="activeSwitch_<?= $bank['id'] ?>"
                                        class="sr-only peer bank-active-switch"
                                        <?= $aktif ? 'checked' : '' ?>
                                        onchange="window.activateBank(<?= $bank['id'] ?>)">
                                    <div class="w-10 h-[22px] bg-slate-300 rounded-full peer peer-checked:after:translate-x-[18px] after:content-[''] after:absolute after:top-[3px] after:left-[3px] after:bg-white after:rounded-full after:h-4 after:w-4 after:shadow after:transition-all peer-checked:bg-emerald-500 peer-focus-visible:ring-2 peer-focus-visible:ring-emerald-300 peer-disabled:opacity-50 peer-disabled:cursor-not-allowed transition-colors shadow-inner"></div>
                                </label>
                                <span id="statusText_<?= $bank['id'] ?>" class="text-[11px] font-semibold w-[58px] text-left <?= $aktif ? 'text-emerald-600' : 'text-slate-500' ?>">
                                    <?= $aktif ? 'Aktif' : 'Non-aktif' ?>
                                </span>
                                <?php /* Badge disembunyikan tapi TETAP ADA: exam.js menulis
                                         ulang isinya saat status diubah. Menghapusnya akan
                                         mematikan pembaruan itu tanpa error yang terlihat. */ ?>
                                <span id="topBadge_<?= $bank['id'] ?>" class="hidden top-status-badge <?= $aktif ? 'bg-emerald-500/90 text-white' : 'bg-black/30 text-white/80' ?> text-[8px] font-bold px-1.5 py-0.5 rounded-full transition-colors"><?= $aktif ? '● AKTIF' : '○ NON-AKTIF' ?></span>
                            </div>
                        </td>

                        <!-- Aksi -->
                        <td class="px-4 py-3.5">
                            <div class="flex items-center justify-center gap-1.5">
                                <button type="button"
                                        class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 flex items-center justify-center transition-colors border-0 cursor-pointer"
                                        onclick="openBankDetail(<?= $bank['id'] ?>, '<?= $namaBank ?>')"
                                        title="Lihat detail soal">
                                    <i class="bi bi-box-arrow-in-right text-sm"></i>
                                </button>
                                <button type="button"
                                        class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 flex items-center justify-center transition-colors border-0 cursor-pointer"
                                        onclick="window.editBankModal(<?= $bank['id'] ?>)"
                                        title="Edit bank soal">
                                    <i class="bi bi-pencil-square text-sm"></i>
                                </button>
                                <button type="button"
                                        class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 flex items-center justify-center transition-colors border-0 cursor-pointer"
                                        onclick="deleteBank(<?= $bank['id'] ?>)"
                                        title="Hapus bank soal">
                                    <i class="bi bi-trash3 text-sm"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    </div><!-- end bankListView -->

    <!--
        Import & Export - dipindahkan ke sini dari halaman terpisah.
        Keduanya mengelola objek yang sama (soal), jadi digabung sebagai tab.
        Semua id dipertahankan persis seperti aslinya karena exam-import.js
        mencarinya lewat getElementById; mengubah nama akan mematikan fitur.
    -->
    <div class="<?= $tabAwal === 'impor' ? '' : 'hidden' ?>" id="importExportView">
        <div class="flex flex-col gap-5">
            <!-- Import Section -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-5 md:p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                        <div class="lg:col-span-7">
                            <div class="flex items-center mb-5">
                                <div class="rounded-xl flex items-center justify-center mr-3 shadow-sm w-11 h-11 bg-blue-50 text-blue-600 shrink-0">
                                    <i class='bx bx-import text-xl'></i>
                                </div>
                                <div>
                                    <h4 class="text-base font-bold text-slate-800 mb-0.5">Import Soal Baru</h4>
                                    <p class="text-slate-500 text-xs">Tambahkan soal masal ke bank soal pilihan Anda</p>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="selectedBankSoalImport" class="block text-xs font-bold text-slate-800 mb-1.5">1. Pilih Bank Soal Tujuan</label>
                                <select class="w-full px-3 py-2.5 rounded-xl border border-slate-200 bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm text-slate-700 font-semibold transition" id="selectedBankSoalImport">
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

                            <div>
                                <label class="block text-xs font-bold text-slate-800 mb-1.5">2. Upload File Data Soal</label>
                                <div class="upload-zone p-5 border-2 border-dashed border-slate-300 rounded-xl text-center bg-slate-50/60 hover:bg-blue-50/40 hover:border-blue-400 transition duration-200 relative">
                                    <input type="file" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" id="importFile" accept=".csv, .xls, .xlsx" aria-label="Pilih berkas soal">
                                    <div id="uploadContent" class="flex flex-col items-center">
                                        <i class='bx bx-cloud-upload text-blue-600 mb-1.5 text-4xl'></i>
                                        <h6 class="font-bold text-slate-800 text-sm mb-0.5" id="fileLabel">Klik atau drag file ke sini</h6>
                                        <p class="text-slate-500 text-xs">Mendukung .csv, .xls, .xlsx (Maksimal 1MB)</p>
                                    </div>
                                </div>
                                <div id="fileInfo" class="mt-2 text-center text-xs font-semibold text-emerald-600 hidden"></div>
                            </div>
                        </div>

                        <div class="lg:col-span-5">
                            <div class="bg-slate-50 p-5 rounded-xl border border-slate-200 flex flex-col justify-between h-full">
                                <div>
                                    <h6 class="font-bold text-slate-800 mb-3 flex items-center gap-1.5 text-sm"><i class='bx bx-info-circle text-blue-600 text-base'></i> Panduan Import</h6>
                                    <ul class="space-y-2 text-xs text-slate-600 mb-5">
                                        <li class="flex items-start gap-2"><i class='bx bx-check text-emerald-500 text-base shrink-0'></i> <span>Gunakan format kolom template yang disediakan.</span></li>
                                        <li class="flex items-start gap-2"><i class='bx bx-check text-emerald-500 text-base shrink-0'></i> <span>Kolom wajib: Deskripsi, Tipe (PG/Essay), Jawaban.</span></li>
                                        <li class="flex items-start gap-2"><i class='bx bx-check text-emerald-500 text-base shrink-0'></i> <span>Untuk PG, isi kolom Pilihan A sampai E.</span></li>
                                    </ul>
                                </div>

                                <div class="flex flex-col gap-2">
                                    <button class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-xl transition disabled:opacity-50 disabled:cursor-not-allowed shadow-sm flex items-center justify-center gap-2" onclick="importSoal()" id="btnImport" disabled>
                                        <i class='bx bx-download text-base'></i> Mulai Proses Import
                                    </button>
                                    <button type="button" onclick="downloadTemplate()" class="w-full py-2 text-center border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 font-bold text-sm rounded-xl transition flex items-center justify-center gap-2">
                                        <i class='bx bx-file-blank text-base'></i> Download Template Excel
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Export Section -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-5 md:p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                        <div class="lg:col-span-7">
                            <div class="flex items-center mb-5">
                                <div class="rounded-xl flex items-center justify-center mr-3 shadow-sm w-11 h-11 bg-emerald-50 text-emerald-600 shrink-0">
                                    <i class='bx bx-export text-xl'></i>
                                </div>
                                <div>
                                    <h4 class="text-base font-bold text-slate-800 mb-0.5">Export Data Soal</h4>
                                    <p class="text-slate-500 text-xs">Unduh seluruh soal dari bank soal ke format Excel</p>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="selectedBankSoal" class="block text-xs font-bold text-slate-800 mb-1.5">Pilih Bank Soal Sumber</label>
                                <select class="w-full px-3 py-2.5 rounded-xl border border-slate-200 bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm text-slate-700 font-semibold transition" id="selectedBankSoal">
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

                            <button class="w-full md:w-auto px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm rounded-xl transition disabled:opacity-50 disabled:cursor-not-allowed shadow-sm flex items-center justify-center gap-2" onclick="exportSoal()" id="btnExport" disabled>
                                <i class='bx bx-download text-base'></i> Unduh File Excel (.xlsx)
                            </button>
                        </div>

                        <div class="lg:col-span-5">
                            <div id="exportSummary" class="bg-slate-50 p-5 rounded-xl border border-slate-200">
                                <h6 class="font-bold text-slate-800 mb-3 text-sm">Ringkasan Data Terpilih</h6>
                                <div class="grid grid-cols-3 gap-3">
                                    <div class="text-center">
                                        <h3 class="text-xl font-bold mb-0.5 text-emerald-600" id="exportTotalCount">-</h3>
                                        <span class="block text-slate-400 text-[10px] font-bold uppercase tracking-wider">Total Soal</span>
                                    </div>
                                    <div class="text-center border-l border-r border-slate-200">
                                        <h3 class="text-xl font-bold mb-0.5 text-blue-600" id="exportPGCount">-</h3>
                                        <span class="block text-slate-400 text-[10px] font-bold uppercase tracking-wider">Pilihan Ganda</span>
                                    </div>
                                    <div class="text-center">
                                        <h3 class="text-xl font-bold mb-0.5 text-amber-500" id="exportEssayCount">-</h3>
                                        <span class="block text-slate-400 text-[10px] font-bold uppercase tracking-wider">Essay</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div><!-- end importExportView -->

    <!-- Bank Detail View -->
    <div class="bank-detail-view hidden" id="bankDetailView">
        <!-- Quizizz Style Header Card -->
        <div class="bg-gradient-to-br from-primary to-secondary rounded-2xl shadow-lg border-0 mb-5 overflow-hidden relative">
            <div class="absolute -top-16 -right-16 w-64 h-64 bg-white/10 rounded-full pointer-events-none"></div>
            <div class="absolute -bottom-20 left-8 w-40 h-40 bg-white/10 rounded-full pointer-events-none"></div>
            
            <div class="p-4 md:p-5 flex flex-col md:flex-row md:items-center gap-4 relative z-10">
                <!-- Icon/Cover -->
                <div class="w-14 h-14 bg-white/20 backdrop-blur-sm border border-white/30 rounded-lg flex items-center justify-center shrink-0 shadow-sm">
                    <i class='bx bx-book-content text-3xl text-white'></i>
                </div>
                <!-- Title & Meta -->
                <div class="flex-1">
                    <div class="flex items-center gap-3">
                        <h3 class="text-xl font-bold text-white" id="detailBankTitle">Nama Bank Soal</h3>
                    </div>
                </div>
            </div>
            
            <!-- Toolbar -->
            <div class="bg-slate-50 border-t border-slate-100 px-4 py-3 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center">
                    <button class="px-4 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-sm font-bold rounded-lg flex items-center gap-2 transition shadow-sm" onclick="closeBankDetail()">
                        <i class='bx bx-arrow-back'></i> Kembali
                    </button>
                </div>
                
                <div class="flex items-center gap-2">
                    <button class="px-4 py-2 bg-white border border-blue-200 text-blue-600 hover:bg-blue-50 text-sm font-bold rounded-lg flex items-center gap-2 transition shadow-sm" onclick="window.editBankModal(window.currentBankId)">
                        <i class='bx bx-edit'></i> Edit Bank Soal
                    </button>
                    <button class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-lg flex items-center gap-2 transition shadow-sm border border-blue-600" data-modal-open="#addSoalModal">
                        <i class='bx bx-plus'></i> Tambah Soal
                    </button>
                </div>
            </div>
        </div>

        <!-- Main Content 2 Columns -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <!-- Left Info Panel -->
            <div class="lg:col-span-4 xl:col-span-3">
                <div class="sticky top-6 space-y-4">
                    <!-- Progress / Status Card -->
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden relative group">
                        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-primary to-secondary"></div>
                        <div class="p-5">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="font-bold text-slate-800 text-sm">Status Bank Soal</h4>
                                <span id="detailBankStatusBadge" class="bg-blue-50 text-blue-600 text-[10px] font-bold px-2 py-1 rounded-md transition-colors">AKTIF</span>
                            </div>
                            
                            <div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div class="bg-slate-50 rounded-xl p-3 border border-slate-100 transition hover:border-blue-200">
                                        <div class="text-slate-400 text-[10px] font-bold uppercase mb-1">Total Poin</div>
                                        <div class="text-slate-800 font-bold text-lg" id="panelTotalPoints">0</div>
                                    </div>
                                    <div class="bg-slate-50 rounded-xl p-3 border border-slate-100 transition hover:border-blue-200">
                                        <div class="text-slate-400 text-[10px] font-bold uppercase mb-1">Durasi</div>
                                        <div class="text-slate-800 font-bold text-lg"><span id="panelDurasi">45</span><span class="text-xs text-slate-500 font-medium ml-1">m</span></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quick Actions Card -->
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
                        <h4 class="font-bold text-slate-800 text-sm mb-3">Aksi Cepat</h4>
                        <div class="space-y-2">
                            <button type="button" class="w-full flex items-center justify-between p-2.5 rounded-xl hover:bg-blue-50 text-slate-600 hover:text-blue-600 transition group border border-transparent hover:border-blue-100" data-modal-open="#addSoalModal">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-lg bg-slate-100 group-hover:bg-blue-100 flex items-center justify-center transition">
                                        <i class='bx bx-plus text-lg'></i>
                                    </div>
                                    <span class="text-sm font-semibold">Tambah Soal Baru</span>
                                </div>
                                <i class='bx bx-chevron-right text-slate-400 group-hover:text-blue-500'></i>
                            </button>
                            
                            <button type="button" onclick="imporUntukBankIni()" class="w-full flex items-center justify-between p-2.5 rounded-xl hover:bg-emerald-50 text-slate-600 hover:text-emerald-600 transition group border border-transparent hover:border-emerald-100">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-lg bg-slate-100 group-hover:bg-emerald-100 flex items-center justify-center transition">
                                        <i class='bx bx-import text-lg'></i>
                                    </div>
                                    <span class="text-sm font-semibold">Import dari Excel</span>
                                </div>
                                <i class='bx bx-chevron-right text-slate-400 group-hover:text-emerald-500'></i>
                            </button>

                            <button type="button" onclick="window.editBankModal(window.currentBankId)" class="w-full flex items-center justify-between p-2.5 rounded-xl hover:bg-blue-50 text-slate-600 hover:text-blue-600 transition group border border-transparent hover:border-blue-100">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-lg bg-slate-100 group-hover:bg-blue-100 flex items-center justify-center transition">
                                        <i class='bx bx-cog text-lg'></i>
                                    </div>
                                    <span class="text-sm font-semibold">Pengaturan Ujian</span>
                                </div>
                                <i class='bx bx-chevron-right text-slate-400 group-hover:text-blue-500'></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Right Content Panel -->
            <div class="lg:col-span-8 xl:col-span-9 bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <!-- Filter & Controls Header -->
                <div class="p-4 md:px-6 md:py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white">
                    <div class="text-slate-500 font-medium text-sm">
                        <span id="detailBankQuestionCount">0</span> pertanyaan &bull; <span id="detailBankPoints">0</span> Poin
                    </div>
                    
                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-semibold text-slate-600">Tunjukkan jawaban</span>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" id="toggleJawaban" class="sr-only peer" checked onchange="window.renderSoalList(window.currentBankSoal)">
                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                            </label>
                        </div>
                    </div>
                </div>
                
                <!-- Filters List -->
                <div class="px-6 py-3 border-b border-slate-100 bg-slate-50/50 flex gap-2 overflow-x-auto hide-scrollbar">
                    <button class="filter-btn active px-3.5 py-1.5 text-xs font-bold rounded-lg transition border border-slate-200 text-slate-600 hover:bg-slate-50 bg-white whitespace-nowrap" data-filter="all">Semua</button>
                    <button class="filter-btn px-3.5 py-1.5 text-xs font-bold rounded-lg transition border border-slate-200 text-slate-600 hover:bg-slate-50 bg-white whitespace-nowrap" data-filter="pilihan_ganda">Pilihan Ganda</button>
                    <button class="filter-btn px-3.5 py-1.5 text-xs font-bold rounded-lg transition border border-slate-200 text-slate-600 hover:bg-slate-50 bg-white whitespace-nowrap" data-filter="essay">Essay</button>
                </div>

                <!-- Soal List (tabel) -->
                <!-- overflow-x-auto: tabel bisa lebih lebar dari panel pada layar
                     kecil. `flex flex-col` dilepas dari #soalList karena isinya
                     kini <table> — flex akan merusak layout tabelnya. -->
                <div class="bg-white overflow-x-auto" style="min-height: 400px; max-height: 800px; overflow-y: auto;">
                    <div id="soalList">
                        <!-- Soal items rendered by JS -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Create Bank Modal -->
<div data-modal class="fixed inset-0 z-[1050] hidden items-center justify-center p-4 opacity-0 transition-opacity duration-200 ease-out data-[open]:opacity-100 bg-slate-900/50 backdrop-blur-sm" id="createBankModal" role="dialog" aria-hidden="true">
    <div class="absolute inset-0" data-modal-close></div>
    <div class="relative w-full max-w-[500px] scale-95 transition-transform duration-200 ease-out [[data-open]_&]:scale-100">
        <div class="relative bg-white w-full shadow-lg rounded-2xl overflow-hidden">
            <div class="bg-blue-600 text-white p-6 rounded-t-2xl flex justify-between items-center gap-3">
                <h5 class="font-bold text-lg"><i class='bx bx-folder-plus mr-2'></i>Buat Bank Soal Baru</h5>
                <button type="button" data-modal-close aria-label="Tutup" class="shrink-0 w-8 h-8 rounded-lg flex items-center justify-center transition-colors text-white/80 hover:text-white hover:bg-white/20"><i class="bi bi-x-lg text-sm pointer-events-none"></i></button>
            </div>
            <form id="createBankForm">
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Nama Bank Soal</label>
                        <input type="text" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm font-semibold transition" name="nama_bank" placeholder="Contoh: Ujian Masuk 2024" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Deskripsi</label>
                        <textarea class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm font-medium transition" name="deskripsi_bank" rows="3" placeholder="Deskripsi singkat bank soal..."></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Token Ujian</label>
                        <div class="relative">
                            <i class='bx bx-key absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-lg'></i>
                            <input type="text" class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm font-semibold transition uppercase tracking-wider" name="token_bank" placeholder="Kode Token" required>
                        </div>
                        <div class="text-xs text-slate-400 mt-1 font-semibold">Kode unik untuk peserta mengakses ujian ini.</div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Durasi (Menit)</label>
                            <input type="number" min="1" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm font-semibold transition" name="durasi_bank" placeholder="45" value="45" required>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Poin per Soal</label>
                            <input type="number" min="1" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm font-semibold transition" name="poin_bank" placeholder="10" value="10" required>
                        </div>
                    </div>
                </div>
                <div class="border-t border-slate-100 bg-slate-50 px-6 py-4 rounded-b-2xl flex justify-end gap-3">
                    <button type="button" class="px-4 py-2 text-slate-500 hover:text-slate-700 text-sm font-bold transition border-0 bg-transparent" data-modal-close>Batal</button>
                    <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl text-sm transition shadow-sm border-0">Buat Bank Soal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Bank Modal -->
<div data-modal class="fixed inset-0 z-[1050] hidden items-center justify-center p-4 opacity-0 transition-opacity duration-200 ease-out data-[open]:opacity-100 bg-slate-900/50 backdrop-blur-sm" id="editBankModal" role="dialog" aria-hidden="true">
    <div class="absolute inset-0" data-modal-close></div>
    <div class="relative w-full max-w-[500px] scale-95 transition-transform duration-200 ease-out [[data-open]_&]:scale-100">
        <div class="relative bg-white w-full shadow-lg rounded-2xl overflow-hidden">
            <div class="bg-blue-600 text-white p-6 rounded-t-2xl flex justify-between items-center gap-3">
                <h5 class="font-bold text-lg"><i class='bx bx-edit mr-2'></i>Edit Bank Soal</h5>
                <button type="button" data-modal-close aria-label="Tutup" class="shrink-0 w-8 h-8 rounded-lg flex items-center justify-center transition-colors text-white/80 hover:text-white hover:bg-white/20"><i class="bi bi-x-lg text-sm pointer-events-none"></i></button>
            </div>
            <form id="editBankForm">
                <input type="hidden" name="id" id="editBankId">
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Nama Bank Soal</label>
                        <input type="text" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm font-semibold transition" name="nama" id="editBankName" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Deskripsi</label>
                        <textarea class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm font-medium transition" name="deskripsi" id="editBankDesc" rows="3"></textarea>
                    </div>
                    <div>
                        <label for="editBankToken" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Token Ujian</label>
                        <div class="relative">
                            <i class='bx bx-key absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-lg'></i>
                            <input type="text" class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm font-semibold transition uppercase tracking-wider" name="token" id="editBankToken" required>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="editBankDurasi" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Durasi (Menit)</label>
                            <input type="number" min="1" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm font-semibold transition" name="durasi" id="editBankDurasi" required>
                        </div>
                        <div>
                            <label for="editBankPoin" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Poin per Soal</label>
                            <input type="number" min="1" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm font-semibold transition" name="poin_per_soal" id="editBankPoin" required>
                        </div>
                    </div>
                </div>
                <div class="border-t border-slate-100 bg-slate-50 px-6 py-4 rounded-b-2xl flex justify-end gap-3">
                    <button type="button" class="px-4 py-2 text-slate-500 hover:text-slate-700 text-sm font-bold transition border-0 bg-transparent" data-modal-close>Batal</button>
                    <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl text-sm transition shadow-sm border-0">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Soal Modal -->
<div data-modal class="fixed inset-0 z-[1050] hidden items-center justify-center p-4 opacity-0 transition-opacity duration-200 ease-out data-[open]:opacity-100 bg-slate-900/50 backdrop-blur-sm" id="addSoalModal" role="dialog" aria-hidden="true">
    <div class="absolute inset-0" data-modal-close></div>
    <div class="relative w-full max-w-3xl scale-95 transition-transform duration-200 ease-out [[data-open]_&]:scale-100 panel-modal-soal overflow-hidden">
        <div class="relative bg-white w-full shadow-lg rounded-2xl overflow-hidden">
            <div class="bg-blue-600 text-white p-6 rounded-t-2xl flex justify-between items-center gap-3">
                <h5 class="font-bold text-lg"><i class='bx bx-plus-circle mr-2'></i>Tambah Soal Baru</h5>
                <button type="button" data-modal-close aria-label="Tutup" class="shrink-0 w-8 h-8 rounded-lg flex items-center justify-center transition-colors text-white/80 hover:text-white hover:bg-white/20"><i class="bi bi-x-lg text-sm pointer-events-none"></i></button>
            </div>
            <form id="addSoalForm" enctype="multipart/form-data">
                <div class="p-6 space-y-6 gulir-tanpa-batang">
                    <!-- Tipe Soal Selection -->
                    <div class="text-center">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-3">Pilih Tipe Soal</label>
                        <div class="flex justify-center gap-4">
                            <div class="type-option selected p-4 rounded-2xl border-2 border-slate-200 cursor-pointer relative w-40 transition-all hover:border-blue-500 hover:bg-blue-50/20" data-type="pilihan_ganda">
                                <div class="check-icon absolute top-2 right-2 text-blue-600">
                                    <i class='bx bxs-check-circle text-xl'></i>
                                </div>
                                <i class='bx bx-list-ul text-4xl text-blue-600 mb-2'></i>
                                <div class="font-bold text-sm text-slate-700">Pilihan Ganda</div>
                            </div>
                            <div class="type-option p-4 rounded-2xl border-2 border-slate-200 cursor-pointer relative w-40 transition-all hover:border-blue-500 hover:bg-blue-50/20" data-type="essay">
                                <div class="check-icon absolute top-2 right-2 text-blue-600 hidden">
                                    <i class='bx bxs-check-circle text-xl'></i>
                                </div>
                                <i class='bx bx-align-left text-4xl text-amber-500 mb-2'></i>
                                <div class="font-bold text-sm text-slate-700">Essay</div>
                            </div>
                        </div>
                        <input type="hidden" name="status_soal" id="soalType" value="pilihan_ganda">
                    </div>

                    <!-- Gambar Soal -->
                    <div>
                        <label for="soalImageInput" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Gambar Soal (Opsional)</label>
                        <input type="file" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm font-semibold transition" name="soal_image" id="soalImageInput" accept="image/*">
                        <div class="text-xs text-slate-400 mt-1 font-semibold">Format: JPG, PNG, GIF. Maksimal 2MB.</div>
                        <div id="imagePreview" class="mt-4" style="display: none;">
                            <img id="previewImg" src="" alt="Preview" class="max-w-full max-h-[300px] rounded-xl border border-slate-200">
                        </div>
                    </div>

                    <!-- Pertanyaan -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Pertanyaan</label>
                        <textarea class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm font-semibold transition" name="deskripsi" rows="4" placeholder="Tulis pertanyaan disini..." style="resize: vertical;"></textarea>
                    </div>

                    <!-- Pilihan Ganda Container -->
                    <div id="pilihanContainer" class="space-y-3">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Pilihan Jawaban</label>
                        <?php foreach(['A', 'B', 'C', 'D', 'E'] as $opt): ?>
                        <div class="flex rounded-xl overflow-hidden border border-slate-200 focus-within:ring-2 focus-within:ring-blue-500 focus-within:border-blue-500 transition">
                            <span class="px-4 py-3 bg-slate-50 text-blue-600 font-bold border-r border-slate-200 text-sm flex items-center justify-center w-12 shrink-0"><?= $opt ?></span>
                            <input type="text" class="w-full px-4 py-3 text-sm font-medium border-0 focus:ring-0 focus:outline-none" name="pilihan_<?= strtolower($opt) ?>" 
                                   placeholder="Pilihan <?= $opt ?><?= $opt == 'E' ? ' (Opsional)' : '' ?>" 
                                   <?= $opt != 'E' ? 'required' : '' ?>>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Jawaban Benar Container -->
                    <div id="jawabanPGContainer">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Kunci Jawaban</label>
                        <div class="flex flex-wrap gap-4">
                            <?php foreach(['A', 'B', 'C', 'D', 'E'] as $opt): ?>
                            <label class="flex items-center gap-2 cursor-pointer bg-slate-50 hover:bg-slate-100 px-4 py-2 rounded-xl border border-slate-200 transition">
                                <input class="w-4 h-4 text-blue-600 focus:ring-blue-500 border-slate-300" type="radio" name="jawaban" id="jawab<?= $opt ?>" value="<?= $opt ?>" required>
                                <span class="font-bold text-slate-700 text-sm"><?= $opt ?></span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <div id="jawabanEssayContainer" style="display: none;">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Kunci Jawaban (Essay)</label>
                        <textarea class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm font-semibold transition" name="jawaban_essay" rows="3" placeholder="Jawaban referensi untuk essay..."></textarea>
                    </div>
                </div>
                <div class="border-t border-slate-100 bg-slate-50 px-6 py-4 rounded-b-2xl flex justify-end gap-3">
                    <button type="button" class="px-4 py-2 text-slate-500 hover:text-slate-700 text-sm font-bold transition border-0 bg-transparent" data-modal-close>Batal</button>
                    <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl text-sm transition shadow-sm border-0">Simpan Soal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Soal Modal -->
<div data-modal class="fixed inset-0 z-[1050] hidden items-center justify-center p-4 opacity-0 transition-opacity duration-200 ease-out data-[open]:opacity-100 bg-slate-900/50 backdrop-blur-sm" id="editSoalModal" role="dialog" aria-hidden="true">
    <div class="absolute inset-0" data-modal-close></div>
    <div class="relative w-full max-w-3xl scale-95 transition-transform duration-200 ease-out [[data-open]_&]:scale-100 panel-modal-soal overflow-hidden">
        <div class="relative bg-white w-full shadow-lg rounded-2xl overflow-hidden">
            <div class="bg-blue-600 text-white p-6 rounded-t-2xl flex justify-between items-center gap-3">
                <h5 class="font-bold text-lg"><i class='bx bx-edit mr-2'></i>Edit Soal</h5>
                <button type="button" data-modal-close aria-label="Tutup" class="shrink-0 w-8 h-8 rounded-lg flex items-center justify-center transition-colors text-white/80 hover:text-white hover:bg-white/20"><i class="bi bi-x-lg text-sm pointer-events-none"></i></button>
            </div>
            <form id="editSoalForm" enctype="multipart/form-data">
                <input type="hidden" name="id" id="editSoalId">
                <div class="p-6 space-y-6 gulir-tanpa-batang">
                    <!-- Tipe Soal Selection -->
                    <div class="text-center">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-3">Tipe Soal</label>
                        <div class="flex justify-center gap-4">
                            <div class="type-option p-4 rounded-2xl border-2 border-slate-200 cursor-pointer relative w-40 transition-all hover:border-blue-500 hover:bg-blue-50/20" data-type="pilihan_ganda" id="editTypePG">
                                <div class="check-icon absolute top-2 right-2 text-blue-600">
                                    <i class='bx bxs-check-circle text-xl'></i>
                                </div>
                                <i class='bx bx-list-ul text-4xl text-blue-600 mb-2'></i>
                                <div class="font-bold text-sm text-slate-700">Pilihan Ganda</div>
                            </div>
                            <div class="type-option p-4 rounded-2xl border-2 border-slate-200 cursor-pointer relative w-40 transition-all hover:border-blue-500 hover:bg-blue-50/20" data-type="essay" id="editTypeEssay">
                                <div class="check-icon absolute top-2 right-2 text-blue-600 hidden">
                                    <i class='bx bxs-check-circle text-xl'></i>
                                </div>
                                <i class='bx bx-align-left text-4xl text-amber-500 mb-2'></i>
                                <div class="font-bold text-sm text-slate-700">Essay</div>
                            </div>
                        </div>
                        <input type="hidden" name="status_soal" id="editSoalType">
                    </div>

                    <!-- Gambar Soal -->
                    <div>
                        <label for="soalImageEditInput" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Gambar Soal (Opsional)</label>
                        <input type="file" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm font-semibold transition" name="soal_image_edit" id="soalImageEditInput" accept="image/*">
                        <div class="text-xs text-slate-400 mt-1 font-semibold">Format: JPG, PNG, GIF. Maksimal 2MB. Kosongkan jika tidak ingin mengubah gambar.</div>
                        <div id="editImagePreview" class="mt-4" style="display: none;">
                            <img id="editPreviewImg" src="" alt="Preview" class="max-w-full max-h-[300px] rounded-xl border border-slate-200">
                        </div>
                        <input type="hidden" name="existing_image" id="existingImageUrl">
                    </div>

                    <!-- Pertanyaan -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Pertanyaan</label>
                        <textarea class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm font-semibold transition" name="deskripsi" id="editDeskripsi" rows="4"></textarea>
                    </div>

                    <!-- Pilihan Ganda -->
                    <div id="editPilihanContainer" class="space-y-3">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Pilihan Jawaban</label>
                        <?php foreach(['A', 'B', 'C', 'D', 'E'] as $opt): ?>
                        <div class="flex rounded-xl overflow-hidden border border-slate-200 focus-within:ring-2 focus-within:ring-blue-500 focus-within:border-blue-500 transition">
                            <span class="px-4 py-3 bg-slate-50 text-blue-600 font-bold border-r border-slate-200 text-sm flex items-center justify-center w-12 shrink-0"><?= $opt ?></span>
                            <input type="text" class="w-full px-4 py-3 text-sm font-medium border-0 focus:ring-0 focus:outline-none" id="editPilihan<?= $opt ?>" name="pilihan_<?= strtolower($opt) ?>">
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Jawaban -->
                    <div id="editJawabanPGContainer">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Kunci Jawaban</label>
                        <div class="flex flex-wrap gap-4">
                            <?php foreach(['A', 'B', 'C', 'D', 'E'] as $opt): ?>
                            <label class="flex items-center gap-2 cursor-pointer bg-slate-50 hover:bg-slate-100 px-4 py-2 rounded-xl border border-slate-200 transition">
                                <input class="w-4 h-4 text-blue-600 focus:ring-blue-500 border-slate-300" type="radio" name="jawaban" id="editJawab<?= $opt ?>" value="<?= $opt ?>">
                                <span class="font-bold text-slate-700 text-sm"><?= $opt ?></span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <div id="editJawabanEssayContainer" style="display: none;">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Kunci Jawaban (Essay)</label>
                        <textarea class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm font-semibold transition" name="jawaban_essay" id="editJawabanEssay" rows="3"></textarea>
                    </div>
                </div>
                <div class="border-t border-slate-100 bg-slate-50 px-6 py-4 rounded-b-2xl flex justify-end gap-3">
                    <button type="button" class="px-4 py-2 text-slate-500 hover:text-slate-700 text-sm font-bold transition border-0 bg-transparent" data-modal-close>Batal</button>
                    <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl text-sm transition shadow-sm border-0">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Pass PHP Data to JavaScript -->
<script>
    window.serverData = {
        allSoal: <?= json_encode(array_map(function($soal) {
            return [
                'id' => $soal['id'] ?? 0,
                'bank_soal_id' => $soal['bank_soal_id'] ?? null,
                'deskripsi' => $soal['deskripsi'] ?? '',
                'status_soal' => $soal['status_soal'] ?? 'essay',
                'pilihan' => $soal['pilihan'] ?? '',
                'jawaban' => $soal['jawaban'] ?? '',
                'image_url' => $soal['image_url'] ?? null
            ];
        }, $allSoal)) ?>,
        bankSoalList: <?= json_encode($bankSoalList) ?>
    };
    
    // Global Base URL
    var baseUrl = '<?= APP_URL ?>';
    
    // Legacy support for inline scripts that might expect these globals immediately
    window.allSoal = window.serverData.allSoal;
    window.bankSoalList = window.serverData.bankSoalList;

    // Image Modal Functions
    window.showImageModal = function(imageUrl) {
        const modalHtml = `
            <div id="imageModal" class="fixed inset-0 z-[1060] flex items-center justify-center bg-slate-900/75 p-4 animate-fade-in" onclick="closeImageModal()">
                <div class="relative max-w-5xl w-full flex items-center justify-center" onclick="event.stopPropagation()">
                    <button type="button" class="absolute -top-12 right-0 md:-right-12 text-white/70 hover:text-white transition w-10 h-10 flex items-center justify-center bg-black/20 hover:bg-black/40 rounded-full" onclick="closeImageModal()" aria-label="Tutup">
                        <i class="bi bi-x-lg text-xl"></i>
                    </button>
                    <img src="${imageUrl}" class="max-h-[85vh] max-w-full object-contain rounded-xl shadow-2xl" alt="Zoomed Image">
                </div>
            </div>
        `;
        
        const existingModal = document.getElementById('imageModal');
        if (existingModal) existingModal.remove();

        document.body.insertAdjacentHTML('beforeend', modalHtml);
    };

    window.closeImageModal = function() {
        const existingModal = document.getElementById('imageModal');
        if (existingModal) existingModal.remove();
    };

    // Perpindahan tab Daftar Bank Soal <-> Import & Export.
    // Keduanya mengelola objek yang sama (soal) sehingga disatukan di satu
    // halaman; sebelumnya Import & Export berdiri sebagai menu terpisah.
    window.switchSoalTab = function(tab) {
        const daftar = document.getElementById('bankListView');
        const imporExport = document.getElementById('importExportView');
        const tabBank = document.getElementById('tabBankSoal');
        const tabImpor = document.getElementById('tabImporSoal');
        if (!daftar || !imporExport || !tabBank || !tabImpor) return;

        const aktif = 'border-blue-600 text-blue-600 bg-blue-50/50';
        const nonaktif = 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50';
        const keImpor = (tab === 'impor');

        daftar.classList.toggle('hidden', keImpor);
        imporExport.classList.toggle('hidden', !keImpor);
        tabBank.className = 'soal-tab px-4 py-2.5 text-sm font-bold rounded-t-lg border-b-2 transition-all flex items-center gap-2 ' + (keImpor ? nonaktif : aktif);
        tabImpor.className = 'soal-tab px-4 py-2.5 text-sm font-bold rounded-t-lg border-b-2 transition-all flex items-center gap-2 ' + (keImpor ? aktif : nonaktif);
    };

    // Aksi Cepat "Import dari Excel" pada panel rincian bank.
    //
    // Tombolnya sebelumnya tidak punya handler sama sekali. Tab Import & Export
    // hidup di luar panel rincian, jadi rincian harus ditutup dulu - kalau
    // hanya switchSoalTab() yang dipanggil, panel rincian tetap tampil dan
    // menumpuk di atas isi tab Import.
    window.imporUntukBankIni = function () {
        const bankId = window.currentBankId;
        window.closeBankDetail();
        window.switchSoalTab('impor');

        // Bank yang tadi dibuka langsung dipilih sebagai tujuan import sekaligus
        // sumber export, supaya pengguna tidak perlu memilih ulang.
        const pasang = function (idSelect, segarkan) {
            const sel = document.getElementById(idSelect);
            if (!sel || !bankId) return;
            if (!sel.querySelector('option[value="' + bankId + '"]')) return;
            sel.value = String(bankId);
            if (typeof segarkan === 'function') segarkan();
        };
        pasang('selectedBankSoalImport', window.updateImportButtonState);
        pasang('selectedBankSoal', window.updateExportButtonState);

        window.scrollTo({ top: 0, behavior: 'smooth' });
    };

    // Menampilkan nama berkas yang dipilih. Fungsi ini dipanggil oleh halaman
    // Import lama lewat atribut onchange, tetapi definisinya tidak pernah ada
    // di berkas JS mana pun - jadi labelnya tidak pernah berubah. Sekarang
    // dipasang sebagai listener supaya benar-benar berjalan.
    (function () {
        const berkas = document.getElementById('importFile');
        const label = document.getElementById('fileLabel');
        const info = document.getElementById('fileInfo');
        if (!berkas || !label || !info) return;

        berkas.addEventListener('change', function () {
            const dipilih = berkas.files && berkas.files[0];
            if (dipilih) {
                label.textContent = dipilih.name;
                info.textContent = (dipilih.size / 1024).toFixed(1) + ' KB siap diimport';
                info.classList.remove('hidden');
            } else {
                label.textContent = 'Klik atau drag file ke sini';
                info.classList.add('hidden');
            }
        });
    })();
</script>

<!-- Load External JavaScript -->
<script src="<?= APP_URL ?>/Assets/js/admin/bank-soal/impor-ekspor.js"></script>
<script src="<?= APP_URL ?>/Assets/js/admin/bank-soal/index.js"></script>
<script src="<?= APP_URL ?>/Assets/js/admin/bank-soal/editor.js"></script>

