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

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-0 pb-6 [&_.editor-toolbar]:!border-slate-200 [&_.editor-toolbar]:rounded-t-xl [&_.CodeMirror]:!border-slate-200 [&_.CodeMirror]:rounded-b-xl [&_.CodeMirror]:min-h-[200px] [&_.CodeMirror]:max-h-[400px] [&_.editor-statusbar]:hidden [&_.condition-render-markdown_img]:max-w-full [&_.condition-render-markdown_img]:max-h-[400px] [&_.condition-render-markdown_img]:object-contain [&_.condition-render-markdown_img]:rounded-xl [&_.condition-render-markdown_img]:my-2.5 [&_.condition-render-markdown_img]:border [&_.condition-render-markdown_img]:border-slate-200 [&_.condition-render-markdown_img]:block [&_.type-option.selected]:bg-blue-600/5 [&_.type-option.selected]:!border-blue-600 [&_.type-option.selected_.check-icon]:!block [&_.EasyMDEContainer]:z-[1055]">
<div class="bank-list-view" id="bankListView">
        <!-- Stats Bar -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-2 mb-4">
            <div class="bg-white rounded-xl shadow-sm border border-slate-100 px-4 py-3 flex items-center gap-3 hover:-translate-y-0.5 hover:shadow-md transition-all duration-200">
                <div class="w-9 h-9 rounded-lg flex justify-center items-center shrink-0 bg-gradient-to-br from-blue-500 to-blue-600 text-white shadow-sm">
                    <i class="bi bi-folder-fill text-sm"></i>
                </div>
                <div>
                    <div class="text-slate-400 text-[9px] font-bold uppercase tracking-wider">Total Bank Soal</div>
                    <div class="text-xl font-extrabold text-slate-800 leading-tight" id="stat-count-bank"><?= $bankCount ?></div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-slate-100 px-4 py-3 flex items-center gap-3 hover:-translate-y-0.5 hover:shadow-md transition-all duration-200">
                <div class="w-9 h-9 rounded-lg flex justify-center items-center shrink-0 bg-gradient-to-br from-emerald-400 to-emerald-600 text-white shadow-sm">
                    <i class="bi bi-file-earmark-text-fill text-sm"></i>
                </div>
                <div>
                    <div class="text-slate-400 text-[9px] font-bold uppercase tracking-wider">Total Soal</div>
                    <div class="text-xl font-extrabold text-slate-800 leading-tight" id="stat-count-total"><?= $totalSoal ?></div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-slate-100 px-4 py-3 flex items-center gap-3 hover:-translate-y-0.5 hover:shadow-md transition-all duration-200">
                <div class="w-9 h-9 rounded-lg flex justify-center items-center shrink-0 bg-gradient-to-br from-sky-400 to-sky-600 text-white shadow-sm">
                    <i class="bi bi-ui-checks-grid text-sm"></i>
                </div>
                <div>
                    <div class="text-slate-400 text-[9px] font-bold uppercase tracking-wider">Pilihan Ganda</div>
                    <div class="text-xl font-extrabold text-slate-800 leading-tight" id="stat-count-pg"><?= $pgCount ?></div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-slate-100 px-4 py-3 flex items-center gap-3 hover:-translate-y-0.5 hover:shadow-md transition-all duration-200">
                <div class="w-9 h-9 rounded-lg flex justify-center items-center shrink-0 bg-gradient-to-br from-amber-400 to-amber-600 text-white shadow-sm">
                    <i class="bi bi-pencil-square text-sm"></i>
                </div>
                <div>
                    <div class="text-slate-400 text-[9px] font-bold uppercase tracking-wider">Essay</div>
                    <div class="text-xl font-extrabold text-slate-800 leading-tight" id="stat-count-essay"><?= $essayCount ?></div>
                </div>
            </div>
        </div>

    <!-- Header & Controls -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-3 gap-2">
        <div>
            <h2 class="text-lg font-bold text-slate-800">Daftar Bank Soal</h2>
            <p class="text-slate-400 text-sm mt-0.5"><?= count($bankSoalList) ?> bank tersedia &bull; Klik kartu untuk melihat daftar soal</p>
        </div>
        <button class="px-5 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold rounded-xl flex items-center gap-2 shadow-md shadow-blue-500/20 transition-all hover:-translate-y-0.5 border-0 text-sm" id="btnCreateBank" data-modal-open="#createBankModal">
            <i class="bi bi-plus-circle-fill"></i>
            <span>Buat Bank Baru</span>
        </button>
    </div>

    <!-- Bank Soal List Grid -->
        <?php if (empty($bankSoalList)): ?>
        <div class="text-center py-16 flex flex-col items-center justify-center bg-white rounded-2xl border border-slate-100 shadow-sm p-8 mb-6">
            <i class='bx bx-folder-open text-slate-300 text-6xl mb-4'></i>
            <h3 class="text-lg font-bold text-slate-700 mb-1">Belum Ada Bank Soal</h3>
            <p class="text-slate-500 text-sm max-w-sm">Klik tombol "Buat Bank Baru" untuk membuat bank soal pertama</p>
        </div>
        <?php endif; ?>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-2" id="bankGrid">
            <?php
            $cardThemes = [
                ['from' => 'from-blue-500',   'via' => 'via-blue-600',   'to' => 'to-indigo-600'],
                ['from' => 'from-violet-500', 'via' => 'via-violet-600', 'to' => 'to-purple-700'],
                ['from' => 'from-emerald-500','via' => 'via-emerald-600','to' => 'to-teal-700'],
                ['from' => 'from-rose-500',   'via' => 'via-rose-600',   'to' => 'to-pink-700'],
                ['from' => 'from-amber-500',  'via' => 'via-amber-500',  'to' => 'to-orange-600'],
                ['from' => 'from-sky-500',    'via' => 'via-sky-600',    'to' => 'to-cyan-700'],
            ];
            $themeIdx = 0;
            ?>
            <?php foreach ($bankSoalList as $bank):
                $theme    = $cardThemes[$themeIdx % count($cardThemes)];
                $themeIdx++;
                $total    = (int)($bank['jumlah_soal'] ?? 0);
                $pg       = (int)($bank['pg_count'] ?? 0);
                $essay    = (int)($bank['essay_count'] ?? 0);
                $pgPct    = $total> 0 ? round($pg / $total * 100) : 0;
                $essayPct = $total> 0 ? round($essay / $total * 100) : 0;
            ?>
            <div class="group" id="bank-card-<?= $bank['id'] ?>">
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 hover:shadow-lg hover:border-slate-300 transition-all duration-200 hover:-translate-y-0.5 flex flex-col">

                    <!-- Header -->
                    <div class="relative h-[88px] bg-gradient-to-br <?= $theme['from'] ?> <?= $theme['via'] ?> <?= $theme['to'] ?> shrink-0 rounded-t-xl">
                        <!-- Decorative background -->
                        <div class="absolute inset-0 overflow-hidden pointer-events-none rounded-t-xl">
                            <div class="absolute inset-0 opacity-[0.07]" style="background-image:radial-gradient(circle at 20% 50%,#fff 1px,transparent 1px),radial-gradient(circle at 80% 20%,#fff 1px,transparent 1px);background-size:24px 24px;"></div>
                            <div class="absolute -top-6 -right-6 w-24 h-24 bg-white/10 rounded-full blur-xl"></div>
                        </div>

                        <!-- Center icon -->
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center border border-white/30">
                                <i class="bi bi-folder-fill text-white text-base"></i>
                            </div>
                        </div>

                        <!-- Status badge top-left -->
                        <div class="absolute top-2 left-2">
                            <?php if (($bank['is_active'] ?? 0) == 1): ?>
                            <span id="topBadge_<?= $bank['id'] ?>" class="top-status-badge bg-emerald-500/90 text-white text-[8px] font-bold px-1.5 py-0.5 rounded-full transition-colors">● AKTIF</span>
                            <?php else: ?>
                            <span id="topBadge_<?= $bank['id'] ?>" class="top-status-badge bg-black/30 text-white/80 text-[8px] font-bold px-1.5 py-0.5 rounded-full transition-colors">○ NON-AKTIF</span>
                            <?php endif; ?>
                        </div>

                        <!-- 3-dot menu top-right -->
                        <div data-dropdown class="absolute top-1.5 right-1.5 relative" onclick="event.stopPropagation()">
                            <button class="w-6 h-6 flex items-center justify-center text-white/70 hover:text-white hover:bg-white/20 rounded-lg transition border-0 bg-transparent text-xs" type="button" data-dropdown-toggle aria-expanded="false" aria-label="Menu lainnya">
                                <i class="bi bi-three-dots-vertical pointer-events-none"></i>
                            </button>
                            <ul data-dropdown-menu class="hidden absolute right-0 top-full z-50 min-w-[150px] border-0 shadow-xl rounded-xl p-2 mt-1 bg-white ring-1 ring-slate-100 list-none">
                                <li>
                                    <a class="flex items-center gap-2 px-3 py-1.5 text-slate-700 hover:bg-slate-50 hover:text-blue-600 rounded-lg text-sm font-medium no-underline" href="javascript:void(0)" onclick="window.editBankModal(<?= $bank['id'] ?>)">
                                        <i class="bi bi-pencil-square text-xs"></i> Edit
                                    </a>
                                </li>
                                <li><hr class="my-1 border-slate-100"></li>
                                <li>
                                    <a class="flex items-center gap-2 px-3 py-1.5 text-red-600 hover:bg-red-50 rounded-lg text-sm font-medium no-underline" href="javascript:void(0)" onclick="deleteBank(<?= $bank['id'] ?>)">
                                        <i class="bi bi-trash3 text-xs"></i> Hapus
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Body -->
                    <div class="p-3 flex flex-col flex-grow">
                        <!-- Name + total -->
                        <div class="flex items-start justify-between gap-2 mb-1.5">
                            <h3 class="font-bold text-slate-800 text-[13px] leading-tight line-clamp-2 group-hover:text-blue-600 transition-colors cursor-pointer flex-1"
                                onclick="openBankDetail(<?= $bank['id'] ?>, '<?= htmlspecialchars($bank['nama'] ?? '') ?>')"
                                title="<?= htmlspecialchars($bank['nama'] ?? '') ?>">
                                <?= htmlspecialchars($bank['nama'] ?? '') ?>
                            </h3>
                            <div class="text-right shrink-0">
                                <div class="text-base font-extrabold text-blue-600 leading-none"><?= $total ?></div>
                                <div class="text-[8px] font-bold text-slate-400 uppercase tracking-wide">Soal</div>
                            </div>
                        </div>

                        <!-- Token badge -->
                        <span class="inline-flex items-center gap-1 text-[9px] font-bold px-1.5 py-0.5 rounded bg-amber-50 text-amber-700 border border-amber-200 mb-2 self-start">
                            <i class="bi bi-key-fill text-[8px]"></i><?= htmlspecialchars($bank['token'] ?? '') ?>
                        </span>

                        <!-- Distribution bar -->
                        <div class="mb-2">
                            <div class="w-full h-1.5 bg-slate-100 rounded-full overflow-hidden flex mb-1">
                                <div class="h-full bg-blue-500 transition-all duration-500" style="width:<?= $pgPct ?>%"></div>
                                <div class="h-full bg-amber-400 transition-all duration-500" style="width:<?= $essayPct ?>%"></div>
                            </div>
                            <div class="flex gap-2.5">
                                <span class="flex items-center gap-1 text-[8px] font-bold text-slate-400">
                                    <span class="w-1.5 h-1.5 rounded-sm bg-blue-500 inline-block"></span>PG (<?= $pg ?>)
                                </span>
                                <span class="flex items-center gap-1 text-[8px] font-bold text-slate-400">
                                    <span class="w-1.5 h-1.5 rounded-sm bg-amber-400 inline-block"></span>Essay (<?= $essay ?>)
                                </span>
                            </div>
                        </div>

                        <!-- Footer: toggle + button -->
                        <div class="mt-auto pt-2 border-t border-slate-100 flex items-center justify-between" onclick="event.stopPropagation()">
                            <div class="flex items-center gap-1.5">
                                <label class="relative inline-flex items-center cursor-pointer mb-0">
                                    <input type="checkbox" id="activeSwitch_<?= $bank['id'] ?>" 
                                        class="sr-only peer bank-active-switch"
                                        <?= ($bank['is_active'] ?? 0) == 1 ? 'checked' : '' ?>
                                        onchange="window.activateBank(<?= $bank['id'] ?>)">
                                    <div class="w-8 h-4 bg-slate-300 rounded-full peer peer-checked:after:translate-x-4 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-3 after:w-3 after:transition-all peer-checked:bg-emerald-500 shadow-inner"></div>
                                </label>
                                <span id="statusText_<?= $bank['id'] ?>" class="text-[9px] font-semibold <?= ($bank['is_active'] ?? 0) == 1 ? 'text-emerald-600' : 'text-slate-500' ?>">
                                    <?= ($bank['is_active'] ?? 0) == 1 ? 'Aktif' : 'Non-aktif' ?>
                                </span>
                            </div>
                            <button class="flex items-center gap-1 text-[10px] font-bold text-blue-600 hover:text-blue-700 border border-blue-200 hover:border-blue-400 hover:bg-blue-50 px-2 py-1 rounded-lg transition-all bg-transparent"
                                    onclick="openBankDetail(<?= $bank['id'] ?>, '<?= htmlspecialchars($bank['nama'] ?? '') ?>')">
                                Detail <i class="bi bi-arrow-right text-[9px]"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>

            <!-- New Bank Card -->
            <div>
                <div class="rounded-xl border-2 border-dashed border-slate-200 hover:border-blue-400 hover:bg-blue-50/40 cursor-pointer transition-all duration-200 flex flex-col justify-center items-center text-center min-h-[200px] group"
                     data-modal-open="#createBankModal">
                    <div class="w-10 h-10 rounded-xl mb-2 flex justify-center items-center bg-white text-blue-500 shadow-sm group-hover:scale-110 group-hover:shadow-md border border-slate-100 group-hover:bg-blue-50 transition-all">
                        <i class="bi bi-plus-lg text-lg"></i>
                    </div>
                    <p class="font-bold text-slate-500 text-xs group-hover:text-blue-600 transition-colors">Bank Soal Baru</p>
                    <p class="text-slate-400 text-[10px] mt-0.5 max-w-[110px] leading-relaxed">Buat set soal ujian baru</p>
                </div>
            </div>
        </div>
    </div><!-- end bankListView -->

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
                        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-blue-500 to-indigo-500"></div>
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
                                        <div class="text-slate-800 font-bold text-lg">45<span class="text-xs text-slate-500 font-medium ml-1">m</span></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quick Actions Card -->
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
                        <h4 class="font-bold text-slate-800 text-sm mb-3">Aksi Cepat</h4>
                        <div class="space-y-2">
                            <button class="w-full flex items-center justify-between p-2.5 rounded-xl hover:bg-blue-50 text-slate-600 hover:text-blue-600 transition group border border-transparent hover:border-blue-100" data-modal-open="#addSoalModal">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-lg bg-slate-100 group-hover:bg-blue-100 flex items-center justify-center transition">
                                        <i class='bx bx-plus text-lg'></i>
                                    </div>
                                    <span class="text-sm font-semibold">Tambah Soal Baru</span>
                                </div>
                                <i class='bx bx-chevron-right text-slate-400 group-hover:text-blue-500'></i>
                            </button>
                            
                            <button class="w-full flex items-center justify-between p-2.5 rounded-xl hover:bg-emerald-50 text-slate-600 hover:text-emerald-600 transition group border border-transparent hover:border-emerald-100">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-lg bg-slate-100 group-hover:bg-emerald-100 flex items-center justify-center transition">
                                        <i class='bx bx-import text-lg'></i>
                                    </div>
                                    <span class="text-sm font-semibold">Import dari Excel</span>
                                </div>
                                <i class='bx bx-chevron-right text-slate-400 group-hover:text-emerald-500'></i>
                            </button>

                            <button class="w-full flex items-center justify-between p-2.5 rounded-xl hover:bg-purple-50 text-slate-600 hover:text-purple-600 transition group border border-transparent hover:border-purple-100">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-lg bg-slate-100 group-hover:bg-purple-100 flex items-center justify-center transition">
                                        <i class='bx bx-cog text-lg'></i>
                                    </div>
                                    <span class="text-sm font-semibold">Pengaturan Ujian</span>
                                </div>
                                <i class='bx bx-chevron-right text-slate-400 group-hover:text-purple-500'></i>
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

                <!-- Soal List -->
                <div class="bg-white" style="min-height: 400px; max-height: 800px; overflow-y: auto;">
                    <div id="soalList" class="flex flex-col">
                        <!-- Soal items rendered by JS -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Create Bank Modal -->
<div data-modal class="fixed inset-0 z-[1050] hidden items-center justify-center p-4 opacity-0 transition-opacity duration-200 ease-out data-[open]:opacity-100" id="createBankModal" role="dialog" aria-hidden="true">
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm [will-change:opacity] [transform:translateZ(0)]" data-modal-close></div>
    <div class="relative w-full max-w-[500px] scale-95 transition-transform duration-200 ease-out data-[open]:scale-100">
        <div class="relative bg-white w-full shadow-lg rounded-2xl overflow-hidden">
            <div class="border-b border-slate-100 bg-blue-600 text-white p-6 rounded-t-2xl flex justify-between items-center gap-3">
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
<div data-modal class="fixed inset-0 z-[1050] hidden items-center justify-center p-4 opacity-0 transition-opacity duration-200 ease-out data-[open]:opacity-100" id="editBankModal" role="dialog" aria-hidden="true">
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm [will-change:opacity] [transform:translateZ(0)]" data-modal-close></div>
    <div class="relative w-full max-w-[500px] scale-95 transition-transform duration-200 ease-out data-[open]:scale-100">
        <div class="relative bg-white w-full shadow-lg rounded-2xl overflow-hidden">
            <div class="border-b border-slate-100 bg-blue-600 text-white p-6 rounded-t-2xl flex justify-between items-center gap-3">
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
<div data-modal class="fixed inset-0 z-[1050] hidden items-center justify-center p-4 opacity-0 transition-opacity duration-200 ease-out data-[open]:opacity-100" id="addSoalModal" role="dialog" aria-hidden="true">
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm [will-change:opacity] [transform:translateZ(0)]" data-modal-close></div>
    <div class="relative w-full max-w-3xl scale-95 transition-transform duration-200 ease-out data-[open]:scale-100 max-h-[85vh] overflow-y-auto">
        <div class="relative bg-white w-full shadow-lg rounded-2xl overflow-hidden">
            <div class="border-b border-slate-100 bg-blue-600 text-white p-6 rounded-t-2xl flex justify-between items-center gap-3">
                <h5 class="font-bold text-lg"><i class='bx bx-plus-circle mr-2'></i>Tambah Soal Baru</h5>
                <button type="button" data-modal-close aria-label="Tutup" class="shrink-0 w-8 h-8 rounded-lg flex items-center justify-center transition-colors text-white/80 hover:text-white hover:bg-white/20"><i class="bi bi-x-lg text-sm pointer-events-none"></i></button>
            </div>
            <form id="addSoalForm" enctype="multipart/form-data">
                <div class="p-6 space-y-6" style="max-height: 65vh; overflow-y: auto;">
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
<div data-modal class="fixed inset-0 z-[1050] hidden items-center justify-center p-4 opacity-0 transition-opacity duration-200 ease-out data-[open]:opacity-100" id="editSoalModal" role="dialog" aria-hidden="true">
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm [will-change:opacity] [transform:translateZ(0)]" data-modal-close></div>
    <div class="relative w-full max-w-3xl scale-95 transition-transform duration-200 ease-out data-[open]:scale-100 max-h-[85vh] overflow-y-auto">
        <div class="relative bg-white w-full shadow-lg rounded-2xl overflow-hidden">
            <div class="border-b border-slate-100 bg-blue-600 text-white p-6 rounded-t-2xl flex justify-between items-center gap-3">
                <h5 class="font-bold text-lg"><i class='bx bx-edit mr-2'></i>Edit Soal</h5>
                <button type="button" data-modal-close aria-label="Tutup" class="shrink-0 w-8 h-8 rounded-lg flex items-center justify-center transition-colors text-white/80 hover:text-white hover:bg-white/20"><i class="bi bi-x-lg text-sm pointer-events-none"></i></button>
            </div>
            <form id="editSoalForm" enctype="multipart/form-data">
                <input type="hidden" name="id" id="editSoalId">
                <div class="p-6 space-y-6" style="max-height: 65vh; overflow-y: auto;">
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
</script>

<!-- Load External JavaScript -->
<script src="<?= APP_URL ?>/Assets/js/admin/exam-import.js"></script>
<script src="<?= APP_URL ?>/Assets/js/admin/exam.js"></script>
<script src="<?= APP_URL ?>/Assets/js/admin/exam-ui.js"></script>

