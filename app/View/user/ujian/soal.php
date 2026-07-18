<?php
/**
 * Exam View
 * 
 * Data yang diterima dari controller:
 * @var string $stambuk - Stambuk mahasiswa
 * @var array $profile - Data profile
 * @var string $nama - Nama mahasiswa
 * @var string $photo - Path foto
 * @var array $results - Soal-soal ujian
 */
$stambuk = $stambuk ?? '';
$profile = $profile ?? [];
$nama = $nama ?? 'Nama Lengkap';
$photo = $photo ?? '/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Downloads/default.png';
$results = $results ?? [];
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ICLabs - Tes Tertulis</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="<?= APP_URL ?>/Assets/js/tailwind-config.js"></script>
    <!-- Icon Libraries -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link class="suppress-error" rel="icon" href="<?=APP_URL?>/Assets/Img/iclabs.png">
    
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
        /* Custom scrollbar for sidebar & question pane */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
    
    <script>
        // Suppress tracking prevention warnings in console
        (function() {
            const originalError = console.error;
            console.error = function() {
                const args = Array.from(arguments);
                const message = args.join(' ');
                if (message && (
                    message.includes('Tracking Prevention') ||
                    message.includes('blocked access to storage') ||
                    message.includes('cdn.jsdelivr.net')
                )) {
                    return;
                }
                originalError.apply(console, arguments);
            };
        })();

        const APP_URL = <?= json_encode(APP_URL) ?>;

        window.storage = (function() {
            let memoryStorage = {};
            let useMemory = false;
            try {
                localStorage.setItem('__test__', '1');
                localStorage.removeItem('__test__');
            } catch(e) {
                useMemory = true;
            }
            return {
                get: function(key) {
                    if (useMemory) return memoryStorage[key] || null;
                    try { return localStorage.getItem(key); } catch(e) {
                        useMemory = true;
                        return memoryStorage[key] || null;
                    }
                },
                set: function(key, val) {
                    if (useMemory) { memoryStorage[key] = val; return; }
                    try { localStorage.setItem(key, val); } catch(e) {
                        useMemory = true;
                        memoryStorage[key] = val;
                    }
                },
                remove: function(key) {
                    if (useMemory) { delete memoryStorage[key]; return; }
                    try { localStorage.removeItem(key); } catch(e) {
                        useMemory = true;
                        delete memoryStorage[key];
                    }
                }
            };
        })();
    </script>
</head>

<body class="bg-slate-50 overflow-hidden flex flex-col h-screen">
    <!-- Navbar Header -->
    <nav class="bg-blue-600 shadow-md py-3 px-6 text-white shrink-0 flex items-center justify-between z-10">
        <div class="flex items-center gap-2">
            <img src="<?=APP_URL?>/Assets/Img/iclabs.png" alt="Logo" class="h-8 w-auto">
            <span class="font-extrabold text-lg tracking-tight">ICLabs - Tes Tertulis</span>
        </div>
    </nav>

    <!-- Main Grid / Wrapper -->
    <div class="flex-1 flex flex-col md:flex-row overflow-hidden">
        <!-- Sidebar Navigation & Profile -->
        <aside class="w-full md:w-80 bg-white border-r border-slate-100 flex flex-col shrink-0 overflow-y-auto">
            <!-- Profile Section -->
            <div class="p-6 border-b border-slate-100 text-center">
                <img src="<?= htmlspecialchars($photo) ?>"
                     alt="User Photo"
                     class="w-20 h-20 rounded-2xl border-4 border-slate-50 shadow-inner mx-auto mb-3 object-cover"
                     onerror="this.src='/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Downloads/default.png'">
                <h6 class="font-bold text-slate-800 text-sm"><?= htmlspecialchars($nama) ?></h6>
                <p class="text-xs text-slate-400 font-semibold tracking-wider uppercase"><?= htmlspecialchars($stambuk) ?></p>
            </div>

            <!-- Question Navigation -->
            <div class="p-6">
                <h6 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">NAVIGASI SOAL</h6>
                <div class="grid grid-cols-5 gap-2" id="examNavButtons">
                    <?php for ($i = 1; $i <= count($results); $i++): ?>
                        <button class="relative w-10 h-10 rounded-xl border border-slate-200 hover:border-blue-600 hover:bg-blue-50 text-slate-600 hover:text-blue-600 font-bold text-xs flex items-center justify-center transition-all nav-btn-soal [&.active]:bg-blue-600 [&.active]:border-blue-600 [&.active]:text-white [&.active]:font-bold [&.active]:ring-4 [&.active]:ring-blue-500/40 [&.answered]:bg-sky-500 [&.answered]:border-sky-500 [&.answered]:text-white [&.answered.active]:bg-blue-600 [&.answered.active]:border-blue-600 [&.answered.active]:after:content-['?'] [&.answered.active]:after:absolute [&.answered.active]:after:-top-1 [&.answered.active]:after:-right-1 [&.answered.active]:after:bg-sky-500 [&.answered.active]:after:text-white [&.answered.active]:after:rounded-full [&.answered.active]:after:w-3.5 [&.answered.active]:after:h-3.5 [&.answered.active]:after:text-[8px] [&.answered.active]:after:flex [&.answered.active]:after:items-center [&.answered.active]:after:justify-center [&.answered.active]:after:border-[1.5px] [&.answered.active]:after:border-white [&.answered.active]:after:font-black"
                                data-index="<?= $i - 1 ?>">
                            <?= $i ?>
                        </button>
                    <?php endfor; ?>
                </div>
            </div>
        </aside>

        <!-- Question Pane -->
        <main class="flex-grow flex flex-col overflow-hidden">
            <!-- Header Timer -->
            <div class="bg-white border-b border-slate-100 p-6 flex items-center justify-between shrink-0">
                <h5 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                    <i class="bi bi-file-earmark-text text-blue-600"></i>Soal <span id="current-question-number">1</span>
                </h5>
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-50 text-blue-600 border border-blue-100 font-black text-sm">
                    <i class="bi bi-clock-fill"></i>
                    <span id="timer">30:00</span>
                </div>
            </div>

            <!-- Question Slide Content -->
            <div class="flex-grow overflow-y-auto p-6 md:p-8">
                <div class="max-w-3xl mx-auto questions-container">
                    <?php foreach ($results as $index => $result): ?>
                        <div class="question bg-white rounded-2xl border border-slate-100 shadow-sm p-6 md:p-8 space-y-6"
                             data-id-soal="<?= htmlspecialchars($result['id']) ?>"
                             style="display: none;">
                            
                            <!-- Question Body -->
                            <div class="space-y-4">
                                <?php if (!empty($result['image_url'])): ?>
                                    <?php
                                    $imageUrl = $result['image_url'];
                                    if (!preg_match('/^(http|\/)/i', $imageUrl)) {
                                        $imageUrl = str_replace('/public', '', APP_URL) . '/' . $imageUrl;
                                    }
                                    ?>
                                    <div class="w-full max-w-md">
                                        <img src="<?= htmlspecialchars($imageUrl) ?>"
                                             class="rounded-xl border border-slate-200 max-h-60 object-contain cursor-pointer hover:border-blue-500 hover:scale-[1.02] transition duration-200"
                                             alt="Gambar Soal"
                                             onclick="showImageModal('<?= htmlspecialchars($imageUrl) ?>')">
                                    </div>
                                <?php endif; ?>
                                <p class="text-slate-700 font-medium leading-relaxed text-base"><?= nl2br(htmlspecialchars($result['deskripsi'])) ?></p>
                            </div>

                            <!-- Choice Section -->
                            <?php if ($result['status_soal'] === 'pilihan_ganda'): ?>
                                <div class="space-y-3">
                                    <?php
                                    $options = json_decode($result['pilihan'], true);
                                    if (!is_array($options) || empty($options)) {
                                        $pilihanString = trim($result['pilihan']);
                                        if (!empty($pilihanString) && $pilihanString !== 'Bukan soal pilihan ganda') {
                                            $optionsArray = explode(',', $pilihanString);
                                            $options = [];
                                            foreach ($optionsArray as $opt) {
                                                $opt = trim($opt);
                                                $cleanOption = preg_replace('/^[A-Z]\.\s*/', '', $opt);
                                                if (!empty($cleanOption)) {
                                                    $options[] = $cleanOption;
                                                }
                                            }
                                        }
                                    }
                                    $optionLabels = ['A', 'B', 'C', 'D', 'E', 'F'];
                                    if (is_array($options) && !empty($options)):
                                        foreach ($options as $optionIndex => $option):
                                            $label = $optionLabels[$optionIndex] ?? ($optionIndex + 1);
                                    ?>
                                            <label class="flex items-center gap-3 p-4 rounded-xl border border-slate-200 hover:border-blue-500 hover:bg-slate-100 hover:translate-x-1 has-[:checked]:bg-blue-50 has-[:checked]:border-blue-600 has-[:checked]:border-2 has-[:checked]:font-semibold cursor-pointer transition-all duration-150 ease-in-out option-label-container">
                                                <input class="w-4 h-4 text-blue-600 border-slate-300 focus:ring-blue-500 flex-shrink-0"
                                                       type="radio"
                                                       name="answer[<?= htmlspecialchars($result['id']) ?>]"
                                                       value="<?= htmlspecialchars($optionIndex) ?>">
                                                <span class="font-bold text-blue-600"><?= $label ?>.</span>
                                                <span class="text-sm text-slate-700 font-medium"><?= htmlspecialchars($option) ?></span>
                                            </label>
                                    <?php
                                        endforeach;
                                    else:
                                    ?>
                                        <div class="flex items-center gap-2.5 p-4 rounded-xl bg-amber-50 border border-amber-100 text-amber-800 text-sm">
                                            <i class="bi bi-exclamation-triangle-fill"></i>
                                            <div class="font-semibold">Pilihan jawaban tidak tersedia untuk soal ini.</div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <!-- Essay -->
                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Jawaban Anda:</label>
                                    <textarea class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm text-slate-700 font-semibold transition"
                                              name="answer[<?= htmlspecialchars($result['id']) ?>]"
                                              rows="8"
                                              placeholder="Tulis jawaban Anda di sini..."></textarea>
                                </div>
                            <?php endif; ?>

                            <!-- Bottom Navigation -->
                            <div class="flex justify-between items-center gap-3 pt-6 border-t border-slate-100">
                                <button type="button" class="px-5 py-2.5 border border-slate-200 hover:bg-slate-50 text-slate-600 font-bold text-sm rounded-xl transition flex items-center gap-2 back-btn">
                                    <i class="bi bi-arrow-left"></i>Sebelumnya
                                </button>
                                <button type="button" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-xl transition flex items-center gap-2 next-btn">
                                    Selanjutnya<i class="bi bi-arrow-right"></i>
                                </button>
                                <button type="button" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm rounded-xl transition flex items-center gap-2 finish-btn" style="display: none;">
                                    <i class="bi bi-check-circle"></i>Selesai
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </main>
    </div>

    <!-- Custom Alert Modal -->
    <div id="customModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/60 p-4 animate-fade-in">
        <div class="bg-white rounded-2xl shadow-xl border border-slate-100 max-w-md w-full overflow-hidden p-6 text-center">
            <div class="flex justify-center items-center mb-4">
                <img id="modalGif" src="" alt="Animation" class="w-20 h-20" style="display: none;">
            </div>
            <p id="modalMessage" class="text-slate-700 font-medium text-sm leading-relaxed mb-6">Pesan akan ditampilkan di sini.</p>
            <button type="button" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-xl transition duration-200" id="closeModal">Tutup</button>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div id="confirmModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/60 p-4 animate-fade-in">
        <div class="bg-white rounded-2xl shadow-xl border border-slate-100 max-w-md w-full overflow-hidden p-6">
            <div class="text-center mb-6">
                <div class="w-16 h-16 rounded-full bg-amber-50 text-amber-500 flex items-center justify-center mx-auto mb-4">
                    <i class="bi bi-question-circle text-3xl"></i>
                </div>
                <h5 class="text-lg font-bold text-slate-800 mb-2">Konfirmasi</h5>
                <p id="confirmModalMessage" class="text-slate-500 text-sm leading-relaxed">Apakah Anda yakin?</p>
            </div>
            <div class="flex gap-3">
                <button type="button" class="flex-1 py-3 border border-slate-200 hover:bg-slate-50 text-slate-600 font-bold text-sm rounded-xl transition" id="confirmModalCancel">
                    Tidak
                </button>
                <button type="button" class="flex-1 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-xl transition" id="confirmModalConfirm">
                    Ya, Lanjutkan
                </button>
            </div>
        </div>
    </div>


    <script>
        // Modal Shim for Tailwind Modals
        class TailwindModalShim {
            constructor(element) {
                this.element = element;
            }
            show() {
                this.element.classList.remove('hidden');
                this.element.classList.add('flex');
            }
            hide() {
                this.element.classList.remove('flex');
                this.element.classList.add('hidden');
            }
        }

        const bootstrap = {
            Modal: function(element) {
                return new TailwindModalShim(element);
            }
        };

        let customModalInstance = null;
        let confirmModalInstance = null;

        document.addEventListener('DOMContentLoaded', function() {
            const customModalEl = document.getElementById('customModal');
            const confirmModalEl = document.getElementById('confirmModal');

            if (customModalEl) {
                customModalInstance = new bootstrap.Modal(customModalEl);
            }
            if (confirmModalEl) {
                confirmModalInstance = new bootstrap.Modal(confirmModalEl);
            }
        });

        window.showActionConfirmation = function(options) {
            const msg = document.getElementById('confirmModalMessage');
            const btnConfirm = document.getElementById('confirmModalConfirm');
            const btnCancel = document.getElementById('confirmModalCancel');

            if (msg && btnConfirm && btnCancel && confirmModalInstance) {
                msg.innerHTML = options.message || 'Apakah Anda yakin?';

                const newBtnConfirm = btnConfirm.cloneNode(true);
                btnConfirm.parentNode.replaceChild(newBtnConfirm, btnConfirm);

                const newBtnCancel = btnCancel.cloneNode(true);
                btnCancel.parentNode.replaceChild(newBtnCancel, btnCancel);

                newBtnConfirm.addEventListener('click', function() {
                    confirmModalInstance.hide();
                    if (options.onConfirm) options.onConfirm();
                });

                newBtnCancel.addEventListener('click', function() {
                    confirmModalInstance.hide();
                });

                confirmModalInstance.show();
            } else {
                if (confirm(options.message || 'Apakah Anda yakin?')) {
                    if (options.onConfirm) options.onConfirm();
                }
            }
        };

        window.showModal = function(message, gifUrl) {
            const msgEl = document.getElementById('modalMessage');
            const gifEl = document.getElementById('modalGif');
            const closeBtn = document.getElementById('closeModal');

            if (msgEl && customModalInstance) {
                msgEl.innerText = message;

                if (gifEl) {
                    if (gifUrl) {
                        gifEl.src = gifUrl;
                        gifEl.style.display = 'block';
                    } else {
                        gifEl.style.display = 'none';
                    }
                }

                if (closeBtn) {
                    closeBtn.onclick = function() {
                        customModalInstance.hide();
                    };
                }

                customModalInstance.show();
            } else {
                alert(message);
            }
        };

        window.showImageModal = function(imageUrl) {
            const modalHtml = `
                <div id="imageModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/75 p-4 animate-fade-in">
                    <div class="bg-white rounded-2xl shadow-xl border border-slate-100 max-w-4xl w-full overflow-hidden flex flex-col max-h-[90vh]">
                        <!-- Header -->
                        <div class="flex justify-between items-center px-6 py-4 border-b border-slate-100 flex-shrink-0">
                            <h5 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                                <i class="bi bi-image text-blue-600"></i>Gambar Soal
                            </h5>
                            <button type="button" class="text-slate-400 hover:text-slate-600" onclick="closeImageModal()">
                                <i class="bi bi-x-lg text-lg"></i>
                            </button>
                        </div>
                        <!-- Body -->
                        <div class="p-6 overflow-y-auto text-center flex-grow flex items-center justify-center">
                            <img src="${imageUrl}" class="max-h-[60vh] max-w-full object-contain rounded-xl border border-slate-200" alt="Gambar Soal">
                        </div>
                        <!-- Footer -->
                        <div class="px-6 py-4 border-t border-slate-100 flex justify-end flex-shrink-0">
                            <button type="button" class="px-5 py-2.5 border border-slate-200 hover:bg-slate-50 text-slate-600 font-bold text-sm rounded-xl transition" onclick="closeImageModal()">
                                <i class="bi bi-x-circle mr-1.5"></i>Tutup
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            const existingModal = document.getElementById('imageModal');
            if (existingModal) {
                existingModal.remove();
            }

            document.body.insertAdjacentHTML('beforeend', modalHtml);
        };

        window.closeImageModal = function() {
            const existingModal = document.getElementById('imageModal');
            if (existingModal) {
                existingModal.remove();
            }
        };

        window.examSessionId = 'exam_<?= $bank['id'] ?? 'default' ?>_<?= $_SESSION['user']['id'] ?? 'guest' ?>_<?= $_SESSION['exam_session_timestamp'] ?? time() ?>';
    </script>
    <script src="<?=APP_URL?>/Assets/js/examScript.js"></script>
</body>

</html>

