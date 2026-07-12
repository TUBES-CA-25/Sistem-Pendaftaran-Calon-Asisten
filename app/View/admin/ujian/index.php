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
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="bank-list-view" id="bankListView">
        <!-- Stats Badges -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4 flex items-center gap-3 hover:shadow-md transition">
                <div class="rounded-lg flex justify-center items-center shrink-0 w-10 h-10 bg-blue-50 text-blue-600">
                    <i class='bx bx-folder text-xl'></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-slate-800" id="stat-count-bank"><?= $bankCount ?></h3>
                    <div class="text-slate-400 text-[10px] font-bold uppercase tracking-wider">Bank Soal</div>
                </div>
            </div>
        </div>
        <div class="col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4 flex items-center gap-3 hover:shadow-md transition">
                <div class="rounded-lg flex justify-center items-center shrink-0 w-10 h-10 bg-emerald-50 text-emerald-600">
                    <i class='bx bx-file text-xl'></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-slate-800" id="stat-count-total"><?= $totalSoal ?></h3>
                    <div class="text-slate-400 text-[10px] font-bold uppercase tracking-wider">Total Soal</div>
                </div>
            </div>
        </div>
        <div class="col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4 flex items-center gap-3 hover:shadow-md transition">
                <div class="rounded-lg flex justify-center items-center shrink-0 w-10 h-10 bg-sky-50 text-sky-600">
                    <i class='bx bx-list-check text-xl'></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-slate-800" id="stat-count-pg"><?= $pgCount ?></h3>
                    <div class="text-slate-400 text-[10px] font-bold uppercase tracking-wider">Pil. Ganda</div>
                </div>
            </div>
        </div>
        <div class="col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4 flex items-center gap-3 hover:shadow-md transition">
                <div class="rounded-lg flex justify-center items-center shrink-0 w-10 h-10 bg-amber-50 text-amber-600">
                    <i class='bx bx-edit text-xl'></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-slate-800" id="stat-count-essay"><?= $essayCount ?></h3>
                    <div class="text-slate-400 text-[10px] font-bold uppercase tracking-wider">Essay</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Header & Controls -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <div class="flex items-center gap-2 text-slate-800">
            <i class='bx bx-folder text-2xl text-blue-600'></i>
            <h2 class="text-xl font-bold">Daftar Bank Soal</h2>
        </div>
        <button class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl flex items-center gap-2 transition shadow-sm border-0" id="btnCreateBank" data-bs-toggle="modal" data-bs-target="#createBankModal">
            <i class='bx bx-plus text-xl'></i> 
            <span>Bank Soal Baru</span>
        </button>
    </div>

    <!-- Bank Soal List Grid -->
        <?php if (empty($bankSoalList)): ?>
        <div class="text-center py-16 flex flex-col items-center justify-center bg-white rounded-2xl border border-slate-100 shadow-sm p-8 mb-6">
            <i class='bx bx-folder-open text-slate-300 text-6xl mb-4'></i>
            <h3 class="text-lg font-bold text-slate-700 mb-1">Belum Ada Bank Soal</h3>
            <p class="text-slate-500 text-sm max-w-sm">Klik tombol "Bank Soal Baru" atau card di bawah untuk membuat bank soal pertama</p>
        </div>
        <?php endif; ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6" id="bankGrid">
            <!-- Existing Banks -->
            <?php foreach ($bankSoalList as $bank): ?>
            <!-- Bank Card Item -->
            <div class="col-span-1 group relative" id="bank-card-<?= $bank['id'] ?>">
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden flex flex-col h-full relative cursor-pointer" onclick="openBankDetail(<?= $bank['id'] ?>, '<?= htmlspecialchars($bank['nama'] ?? '') ?>')">
                    
                    <!-- Cover Section -->
                    <div class="h-32 relative shrink-0 flex items-center justify-center overflow-hidden bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500">
                        <div class="absolute inset-0 bg-white/10" style="background-image: radial-gradient(circle at 2px 2px, rgba(255,255,255,0.2) 1px, transparent 0); background-size: 16px 16px;"></div>
                        <i class='bx bx-book-bookmark text-white/50 text-6xl absolute -bottom-4 -right-4 transform rotate-12 transition-transform duration-500 group-hover:scale-110 group-hover:rotate-6'></i>
                        <span class="absolute top-3 left-3 bg-white/20 backdrop-blur-sm text-white text-[10px] font-bold px-2.5 py-1 rounded-lg border border-white/20 uppercase tracking-wider">
                            <?= $bank['jumlah_soal'] ?> Pertanyaan
                        </span>
                        
                        <!-- Actions Dropdown -->
                        <div class="absolute top-3 right-3 z-10" onclick="event.stopPropagation()">
                            <div class="dropdown">
                                <button class="w-8 h-8 flex items-center justify-center bg-white/20 hover:bg-white/40 backdrop-blur-sm text-white rounded-lg transition border border-white/20" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class='bx bx-dots-vertical-rounded text-lg'></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end border border-slate-100 shadow-xl rounded-xl p-2 mt-1 bg-white">
                                    <li>
                                        <a class="dropdown-item flex items-center gap-3 px-3 py-2 text-slate-700 hover:bg-blue-50 hover:text-blue-600 rounded-lg text-sm font-semibold transition" href="javascript:void(0)" onclick="window.editBankModal(<?= $bank['id'] ?>)">
                                            <i class='bx bx-edit text-base'></i> Edit
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item flex items-center gap-3 px-3 py-2 text-red-600 hover:bg-red-50 rounded-lg text-sm font-semibold transition" href="javascript:void(0)" onclick="deleteBank(<?= $bank['id'] ?>)">
                                            <i class='bx bx-trash text-base'></i> Hapus
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Content Section -->
                    <div class="p-5 flex flex-col flex-grow bg-white relative">
                        <h3 class="font-bold text-slate-800 text-lg mb-1.5 line-clamp-1 group-hover:text-blue-600 transition-colors" title="<?= htmlspecialchars($bank['nama'] ?? '') ?>">
                            <?= htmlspecialchars($bank['nama'] ?? '') ?>
                        </h3>
                        <p class="text-slate-500 text-xs mb-4 line-clamp-2 leading-relaxed flex-grow">
                            <?= htmlspecialchars($bank['deskripsi'] ?? 'Tidak ada deskripsi') ?>
                        </p>
                        
                        <div class="flex flex-wrap gap-2 mb-4">
                            <span class="inline-flex items-center text-[10px] font-bold px-2 py-1 rounded bg-blue-50 text-blue-600 border border-blue-100">
                                PG: <?= $bank['pg_count'] ?? 0 ?>
                            </span>
                            <span class="inline-flex items-center text-[10px] font-bold px-2 py-1 rounded bg-amber-50 text-amber-600 border border-amber-100">
                                Essay: <?= $bank['essay_count'] ?? 0 ?>
                            </span>
                            <span class="inline-flex items-center text-[10px] font-bold px-2 py-1 rounded bg-emerald-50 text-emerald-600 border border-emerald-100">
                                <i class='bx bx-key mr-1 text-sm'></i> <?= htmlspecialchars($bank['token'] ?? '') ?>
                            </span>
                        </div>
                        
                        <!-- Footer: Active Switch -->
                        <div class="pt-4 border-t border-slate-100 flex justify-between items-center" onclick="event.stopPropagation()">
                            <span class="text-xs font-bold text-slate-500 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full <?= ($bank['is_active'] ?? 0) == 1 ? 'bg-emerald-500' : 'bg-slate-300' ?>" id="statusDot_<?= $bank['id'] ?>"></span>
                                <span id="statusText_<?= $bank['id'] ?>" class="<?= ($bank['is_active'] ?? 0) == 1 ? 'text-slate-700' : 'text-slate-400' ?>">
                                    <?= ($bank['is_active'] ?? 0) == 1 ? 'Aktif' : 'Non-Aktif' ?>
                                </span>
                            </span>
                            <div class="form-check form-switch p-0 m-0 flex items-center">
                                <input class="form-check-input bank-active-switch cursor-pointer w-10 h-5 bg-slate-200 checked:bg-blue-600 border-0 rounded-full appearance-none transition-colors" type="checkbox" id="activeSwitch_<?= $bank['id'] ?>" 
                                    value="<?= $bank['id'] ?>" <?= ($bank['is_active'] ?? 0) == 1 ? 'checked' : '' ?> onchange="window.activateBank(<?= $bank['id'] ?>)">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            
            <!-- Create New Bank Card -->
            <div class="col-span-1">
                <div class="bg-white rounded-2xl border-2 border-dashed border-slate-200 hover:border-blue-400 hover:bg-blue-50/30 cursor-pointer transition-all duration-300 p-4 flex flex-col justify-center items-center text-center h-full min-h-[280px] group" 
                     data-bs-toggle="modal" data-bs-target="#createBankModal">
                    <div class="w-16 h-16 rounded-2xl mb-4 flex justify-center items-center bg-blue-100 text-blue-600 transition-transform duration-300 group-hover:scale-110 group-hover:rotate-3 shadow-inner">
                        <i class='bx bx-plus text-3xl'></i>
                    </div>
                    <h3 class="font-bold text-slate-700 mb-1.5 text-base group-hover:text-blue-700 transition-colors">Buat Bank Baru</h3>
                    <p class="text-slate-400 text-xs max-w-[180px] font-medium leading-relaxed">Klik untuk membuat bank soal baru</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Bank Detail View -->
    <div class="bank-detail-view hidden" id="bankDetailView">
        <!-- Quizizz Style Header Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 mb-6 overflow-hidden">
            <div class="p-6 md:p-8 flex flex-col md:flex-row md:items-start gap-6">
                <!-- Icon/Cover -->
                <div class="w-24 h-24 bg-blue-50 rounded-xl flex items-center justify-center shrink-0 border border-blue-100">
                    <i class='bx bx-book-content text-5xl text-blue-400'></i>
                </div>
                <!-- Title & Meta -->
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-2">
                        <h3 class="text-2xl font-bold text-slate-800" id="detailBankTitle">Nama Bank Soal</h3>
                        <span class="px-2.5 py-1 bg-slate-100 text-slate-600 text-xs font-semibold rounded border border-slate-200">Draf</span>
                    </div>
                    
                    <div class="flex flex-wrap items-center gap-y-2 gap-x-3 text-sm font-medium text-slate-500 mb-4">
                        <div class="flex items-center gap-1.5 text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-100">
                            <i class='bx bxs-check-circle'></i> Assessment
                        </div>
                        <span class="w-1 h-1 bg-slate-300 rounded-full"></span>
                        <div class="flex items-center gap-1.5">
                            <div class="w-5 h-5 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class='bx bx-user text-xs text-blue-600'></i>
                            </div>
                            <span id="detailBankAuthor">Admin</span>
                        </div>
                        <span class="w-1 h-1 bg-slate-300 rounded-full"></span>
                        <span>Universitas</span>
                        <span class="w-1 h-1 bg-slate-300 rounded-full"></span>
                        <span class="text-red-500 font-semibold" id="detailBankStatus">Sedang</span>
                    </div>
                </div>
            </div>
            
            <!-- Toolbar -->
            <div class="bg-slate-50 border-t border-slate-100 px-6 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-2">
                    <button class="px-4 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-sm font-semibold rounded-lg flex items-center gap-2 transition">
                        <i class='bx bx-folder'></i> Simpan
                    </button>
                    <button class="px-4 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-sm font-semibold rounded-lg flex items-center gap-2 transition">
                        <i class='bx bx-share-alt'></i> Sebarkan <i class='bx bx-chevron-down ml-1 text-slate-400'></i>
                    </button>
                    <button class="w-10 h-10 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 rounded-lg flex items-center justify-center transition">
                        <i class='bx bx-dots-vertical-rounded'></i>
                    </button>
                </div>
                
                <div class="flex items-center gap-2">
                    <button class="px-4 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-sm font-semibold rounded-lg flex items-center gap-2 transition" onclick="closeBankDetail()">
                        <i class='bx bx-undo'></i> Batal
                    </button>
                    <button class="px-4 py-2 bg-white border border-pink-200 text-pink-600 hover:bg-pink-50 text-sm font-semibold rounded-lg flex items-center gap-2 transition">
                        <i class='bx bx-edit'></i> Edit
                    </button>
                    <button class="px-4 py-2 bg-pink-600 hover:bg-pink-700 text-white text-sm font-semibold rounded-lg flex items-center gap-2 transition" data-bs-toggle="modal" data-bs-target="#addSoalModal">
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
                                <span class="bg-blue-50 text-blue-600 text-[10px] font-bold px-2 py-1 rounded-md">AKTIF</span>
                            </div>
                            
                            <div class="space-y-4">
                                <div>
                                    <div class="flex justify-between text-xs font-semibold mb-1.5">
                                        <span class="text-slate-500">Kesiapan Materi</span>
                                        <span class="text-blue-600">85%</span>
                                    </div>
                                    <div class="w-full bg-slate-100 rounded-full h-2">
                                        <div class="bg-gradient-to-r from-blue-500 to-indigo-500 h-2 rounded-full" style="width: 85%"></div>
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-2 gap-3 pt-2">
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
                            <button class="w-full flex items-center justify-between p-2.5 rounded-xl hover:bg-blue-50 text-slate-600 hover:text-blue-600 transition group border border-transparent hover:border-blue-100" data-bs-toggle="modal" data-bs-target="#addSoalModal">
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
<div class="modal fade" id="createBankModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-2xl overflow-hidden">
            <div class="modal-header border-b border-slate-100 bg-blue-600 text-white p-6 rounded-t-2xl">
                <h5 class="modal-title font-bold text-lg"><i class='bx bx-folder-plus mr-2'></i>Buat Bank Soal Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="createBankForm">
                <div class="modal-body p-6 space-y-4">
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
                </div>
                <div class="modal-footer border-t border-slate-100 bg-slate-50 px-6 py-4 rounded-b-2xl flex justify-end gap-3">
                    <button type="button" class="px-4 py-2 text-slate-500 hover:text-slate-700 text-sm font-bold transition border-0 bg-transparent" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl text-sm transition shadow-sm border-0">Buat Bank Soal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Bank Modal -->
<div class="modal fade" id="editBankModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-2xl overflow-hidden">
            <div class="modal-header border-b border-slate-100 bg-blue-600 text-white p-6 rounded-t-2xl">
                <h5 class="modal-title font-bold text-lg"><i class='bx bx-edit mr-2'></i>Edit Bank Soal</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="editBankForm">
                <input type="hidden" name="id" id="editBankId">
                <div class="modal-body p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Nama Bank Soal</label>
                        <input type="text" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm font-semibold transition" name="nama" id="editBankName" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Deskripsi</label>
                        <textarea class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm font-medium transition" name="deskripsi" id="editBankDesc" rows="3"></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Token Ujian</label>
                        <div class="relative">
                            <i class='bx bx-key absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-lg'></i>
                            <input type="text" class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm font-semibold transition uppercase tracking-wider" name="token" id="editBankToken" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-t border-slate-100 bg-slate-50 px-6 py-4 rounded-b-2xl flex justify-end gap-3">
                    <button type="button" class="px-4 py-2 text-slate-500 hover:text-slate-700 text-sm font-bold transition border-0 bg-transparent" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl text-sm transition shadow-sm border-0">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Soal Modal -->
<div class="modal fade" id="addSoalModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-2xl overflow-hidden">
            <div class="modal-header border-b border-slate-100 bg-blue-600 text-white p-6 rounded-t-2xl">
                <h5 class="modal-title font-bold text-lg"><i class='bx bx-plus-circle mr-2'></i>Tambah Soal Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="addSoalForm" enctype="multipart/form-data">
                <div class="modal-body p-6 space-y-6" style="max-height: 65vh; overflow-y: auto;">
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
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Gambar Soal (Opsional)</label>
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
                <div class="modal-footer border-t border-slate-100 bg-slate-50 px-6 py-4 rounded-b-2xl flex justify-end gap-3">
                    <button type="button" class="px-4 py-2 text-slate-500 hover:text-slate-700 text-sm font-bold transition border-0 bg-transparent" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl text-sm transition shadow-sm border-0">Simpan Soal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Soal Modal -->
<div class="modal fade" id="editSoalModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-2xl overflow-hidden">
            <div class="modal-header border-b border-slate-100 bg-blue-600 text-white p-6 rounded-t-2xl">
                <h5 class="modal-title font-bold text-lg"><i class='bx bx-edit mr-2'></i>Edit Soal</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="editSoalForm" enctype="multipart/form-data">
                <input type="hidden" name="id" id="editSoalId">
                <div class="modal-body p-6 space-y-6" style="max-height: 65vh; overflow-y: auto;">
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
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Gambar Soal (Opsional)</label>
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
                <div class="modal-footer border-t border-slate-100 bg-slate-50 px-6 py-4 rounded-b-2xl flex justify-end gap-3">
                    <button type="button" class="px-4 py-2 text-slate-500 hover:text-slate-700 text-sm font-bold transition border-0 bg-transparent" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl text-sm transition shadow-sm border-0">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .editor-toolbar { border-color: #e2e8f0; border-radius: 0.75rem 0.75rem 0 0; }
    .CodeMirror { border-color: #e2e8f0; border-radius: 0 0 0.75rem 0.75rem; min-height: 200px; max-height: 400px; }
    .editor-statusbar { display: none; }
    
    /* Limit rendered markdown image size */
    .condition-render-markdown img {
        max-width: 100%;
        max-height: 400px; /* Reasonable limit */
        object-fit: contain;
        border-radius: 12px;
        margin-top: 10px;
        margin-bottom: 10px; /* Space between image and text */
        border: 1px solid #e2e8f0;
        display: block; /* Force new line */
    }
    
    /* Type Option Selection */
    .type-option.selected { background-color: rgba(37, 99, 235, 0.05); border-color: #2563eb !important; }
    .type-option.selected .check-icon { display: block !important; }
    
    /* Allow scrolling in modals with EasyMDE */
    .modal-dialog-scrollable .modal-body {
        overflow-y: auto !important;
        max-height: 65vh !important;
        scrollbar-width: thin;
    }
    .modal-dialog-scrollable .modal-body::-webkit-scrollbar {
        width: 6px;
    }
    .modal-dialog-scrollable .modal-body::-webkit-scrollbar-track {
        background: #f1f1f1;
    }
    .modal-dialog-scrollable .modal-body::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }
    
    /* Fix EasyMDE z-index issues in modal */
    .EasyMDEContainer {
        z-index: 1055; 
    }
</style>

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
</script>

<!-- Load External JavaScript -->
<script src="<?= APP_URL ?>/Assets/js/exam_import_export.js"></script>
<script src="<?= APP_URL ?>/Assets/js/exam_import_export.js"></script>
<script src="<?= APP_URL ?>/Assets/js/exam.js"></script>
<script>
    // Initialize EasyMDE
    document.addEventListener('DOMContentLoaded', function() {
        // Options matching user screenshot
        const mdeOptions = {
             element: null, // to be set
             autoDownloadFontAwesome: false,
             spellChecker: false,
             status: false,
             uploadImage: true,
             imageUploadEndpoint: baseUrl + '/uploadImage',
             imagePathAbsolute: true,
             imageAccept: "image/png, image/jpeg, image/gif, image/webp",
             imageTexts: {
                 sbInit: 'Drag & drop image here',
                 sbOnDragEnter: 'Drop image to upload',
                 sbOnDrop: 'Uploading...',
                 sbProgress: 'Uploading... (#progress#)',
                 sbOnUploaded: 'Uploaded',
                 sizeUnits: 'b,kb,mb'
             },
             errorMessages: {
                 noFileGiven: 'Please select a file.',
                 typeNotAllowed: 'This file type is not allowed.',
                 fileTooLarge: 'Image is too big detected.',
                 importError: 'Something went wrong during image upload.'
             },
             toolbar: [
                 "bold", "italic", "heading",
                 "quote", "unordered-list", "ordered-list"
             ]
        };

        // Create Editor for Add Question
        window.easyMDE_add = new EasyMDE({
            ...mdeOptions,
            element: document.querySelector('#addSoalForm textarea[name="deskripsi"]')
        });

        // Create Editor for Edit Question
        window.easyMDE_edit = new EasyMDE({
            ...mdeOptions,
            element: document.querySelector('#editSoalForm textarea[name="deskripsi"]') 
        });

        // Refresh on Modal Open to fix rendering issues
        const addModal = document.getElementById('addSoalModal');
        addModal.addEventListener('shown.bs.modal', function () {
            window.easyMDE_add.codemirror.refresh();
        });

        const editModal = document.getElementById('editSoalModal');
        editModal.addEventListener('shown.bs.modal', function () {
            window.easyMDE_edit.codemirror.refresh();
        });

        // Sync before submit
        document.getElementById('addSoalForm').addEventListener('submit', function(e) {
             const val = window.easyMDE_add.value();
             if (!val.trim()) {
                 e.preventDefault();
                 alert('Pertanyaan tidak boleh kosong');
                 return;
             }
        });

        document.getElementById('editSoalForm').addEventListener('submit', function(e) {
             const val = window.easyMDE_edit.value();
             if (!val.trim()) {
                 e.preventDefault();
                 alert('Pertanyaan tidak boleh kosong');
                 return;
             }
        });

        // Image Preview untuk Add Soal
        document.getElementById('soalImageInput').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Check file size (max 2MB)
                if (file.size > 2 * 1024 * 1024) {
                    alert('Ukuran file terlalu besar. Maksimal 2MB.');
                    e.target.value = '';
                    document.getElementById('imagePreview').style.display = 'none';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(event) {
                    document.getElementById('previewImg').src = event.target.result;
                    document.getElementById('imagePreview').style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                document.getElementById('imagePreview').style.display = 'none';
            }
        });

        // Image Preview untuk Edit Soal
        document.getElementById('soalImageEditInput').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Check file size (max 2MB)
                if (file.size > 2 * 1024 * 1024) {
                    alert('Ukuran file terlalu besar. Maksimal 2MB.');
                    e.target.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(event) {
                    document.getElementById('editPreviewImg').src = event.target.result;
                    document.getElementById('editImagePreview').style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        });
    });
</script>

<!-- Error Handling & Suppression Scripts -->
<script>
    // 1. Suppress external extension errors (Visual cleanup for console)
    if (!window.originalConsoleError) {
        window.originalConsoleError = console.error;
        console.error = function(...args) {
            if (args[0] && typeof args[0] === 'string' && 
               (args[0].includes('chrome-extension://') || args[0].includes('quillbot'))) {
                return; // Suppress extension noise
            }
            window.originalConsoleError.apply(console, args);
        };
    }

    // 2. Global Image Error Handler (Handle 404s gracefully in UI)
    document.addEventListener('error', function(e) {
        if (e.target && e.target.tagName === 'IMG') {
            // Stop if specific suppression class is present
            if (e.target.classList.contains('suppress-error')) return;

            // Check if src is current page (often happens with src="" or src="#")
            if (e.target.src === window.location.href || e.target.getAttribute('src') === '') {
                e.target.style.display = 'none'; // Just hide empty images
                return;
            }

            // Check if it's already a placeholder to prevent loops
            if (!e.target.src.includes('placehold.co')) {
                console.warn('Image failed to load, swapping with placeholder:', e.target.src);
                e.target.src = 'https://placehold.co/600x400?text=Image+Not+Found';
                e.target.alt = 'Broken Image';
                e.target.style.border = '1px dashed #ff0000';
            }
        }
    }, true); // Capture phase to catch load errors
</script>
