<?php
/**
 * Tes Tulis View
 *
 * Data yang diterima dari controller:
 * @var bool $absensiTesTertulis - Status sudah absen tes tertulis
 * @var bool $berkasStatus - Status berkas sudah lengkap
 * @var bool $biodataStatus - Status biodata sudah lengkap
 */
$absensiTesTertulis = $absensiTesTertulis ?? false;
$berkasStatus = $berkasStatus ?? false;
$biodataStatus = $biodataStatus ?? false;
$isDisabled = !$berkasStatus || !$biodataStatus || $absensiTesTertulis;
?>

<!-- Page Header -->
<?php
    $title = 'Tes Tertulis';
    $subtitle = 'Ujian pilihan ganda online';
    $icon = 'bx bx-task';
    require_once __DIR__ . '/../../templates/components/PageHeader.php';
?>

<main class="max-w-4xl mx-auto px-4 pb-8">
    <!-- Exam Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 lg:p-8">
        <?php if (!$canAccess): ?>
            <?php if ($accessReason === 'completed'): ?>
                <!-- Already Completed -->
                <div class="text-center py-8">
                    <div class="w-16 h-16 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto mb-4">
                        <i class="bi bi-check-lg text-3xl"></i>
                    </div>
                    <h4 class="text-xl font-bold text-slate-800 mb-2">Anda sudah mengikuti tes tertulis</h4>
                    <p class="text-sm text-slate-500 leading-relaxed mb-1">Anda tidak bisa mengikuti tes tertulis lebih dari sekali.</p>
                    <p class="text-sm text-slate-500 leading-relaxed">Terima kasih.</p>
                </div>
            <?php else: ?>
                <!-- Access denied alert -->
                <div class="flex items-center gap-2.5 p-4 rounded-xl bg-amber-50 border border-amber-100 text-amber-800 text-sm animate-pulse" role="alert">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <div class="font-semibold"><?= $accessMessage ?></div>
                </div>
            <?php endif; ?>
        <?php elseif (!isset($activeBank) || !$activeBank): ?>
            <!-- No Active Exam -->
            <div class="text-center py-8">
                <div class="w-16 h-16 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center mx-auto mb-4">
                    <i class="bx bx-info-circle text-2xl"></i>
                </div>
                <h4 class="text-xl font-bold text-slate-800 mb-2">Belum Ada Ujian Aktif</h4>
                <p class="text-sm text-slate-500">Mohon tunggu informasi dari pengawas ujian.</p>
            </div>
        <?php else: ?>
            <!-- Exam Info -->
            <div class="text-center mb-6 pb-6 border-b border-slate-100">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-600 border border-blue-100 mb-3">Ujian Aktif</span>
                <h4 class="text-2xl font-black text-slate-800 mb-2"><?= htmlspecialchars($activeBank['nama']) ?></h4>
                <p class="text-sm text-slate-400 leading-relaxed max-w-xl mx-auto"><?= htmlspecialchars($activeBank['deskripsi'] ?? '') ?></p>
            </div>

            <h5 class="text-lg font-bold text-slate-800 flex items-center gap-2 mb-2">
                <i class="bi bi-journal-text text-blue-600"></i>Test Exam
            </h5>
            <p class="text-sm text-slate-500 leading-relaxed mb-6">Pada tahap kali ini kalian akan melaksanakan ujian pilihan ganda.</p>

            <div class="bg-slate-50 rounded-2xl border border-slate-100 p-6 mb-6">
                <h6 class="font-bold text-slate-700 flex items-center gap-2 mb-4">
                    <i class="bi bi-shield-check text-blue-600"></i>Tata Tertib Sebelum Ujian
                </h6>
                <ul class="space-y-4">
                    <li class="flex items-start gap-3 text-sm text-slate-600">
                        <span class="w-6 h-6 rounded-full bg-blue-600 text-white flex items-center justify-center shrink-0 font-bold text-xs">1</span>
                        <span class="leading-normal">Dilarang menghadap kiri kanan. Fokus di komputer Anda.</span>
                    </li>
                    <li class="flex items-start gap-3 text-sm text-slate-600">
                        <span class="w-6 h-6 rounded-full bg-blue-600 text-white flex items-center justify-center shrink-0 font-bold text-xs">2</span>
                        <span class="leading-normal">Bila membutuhkan sesuatu, angkat tangan dan panggil asisten.</span>
                    </li>
                    <li class="flex items-start gap-3 text-sm text-slate-600">
                        <span class="w-6 h-6 rounded-full bg-blue-600 text-white flex items-center justify-center shrink-0 font-bold text-xs">3</span>
                        <span class="leading-normal">Kerjakan dengan jujur.</span>
                    </li>
                </ul>
            </div>

            <div class="flex items-center gap-2.5 p-4 rounded-xl bg-blue-50 border border-blue-100 text-blue-800 text-sm mb-6" role="alert">
                <i class="bi bi-clock"></i>
                <div class="font-medium">Durasi ujian: <strong class="font-bold">80 Menit</strong>. Baca doa terlebih dahulu sebelum memulai.</div>
            </div>

            <!-- Mulai Ujian Button -->
            <button id="startTestButton" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-xl transition duration-200 flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed" <?php if ($isDisabled) echo 'disabled'; ?>>
                <i class="bi bi-play-circle text-lg"></i>Mulai Ujian
            </button>
        <?php endif; ?>
    </div>
</main>

<!-- Bootstrap Token Modal (retails classes for Bootstrap modal trigger but styles inner blocks with Tailwind) -->
<div data-modal class="fixed inset-0 z-[1050] hidden items-center justify-center p-4 opacity-0 transition-opacity duration-200 ease-out data-[open]:opacity-100 bg-slate-900/50 backdrop-blur-sm" id="tokenModal" role="dialog" aria-hidden="true">
    <div class="absolute inset-0" data-modal-close></div>
    <div class="relative w-full max-w-[500px] scale-95 transition-transform duration-200 ease-out data-[open]:scale-100">
        <div class="relative bg-white w-full rounded-2xl shadow-xl overflow-hidden bg-white">
            <div class="p-6">
                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <h5 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <i class="bx bx-lock-alt text-blue-600"></i>Verifikasi Token Ujian
                    </h5>
                    <button type="button" class="text-slate-400 hover:text-slate-600" data-modal-close aria-label="Close">
                        <i class="bi bi-x-lg text-lg"></i>
                    </button>
                </div>
                
                <!-- Body -->
                <div class="text-center mb-6">
                    <div class="w-16 h-16 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center mx-auto mb-4">
                        <i class="bx bx-lock-alt text-2xl"></i>
                    </div>
                    <p class="text-sm text-slate-500">Silahkan masukkan token ujian yang diberikan oleh pengawas.</p>
                </div>
                
                <div class="mb-6">
                    <input type="text" id="inputToken" class="w-full px-4 py-3 text-center text-lg font-extrabold text-slate-800 tracking-widest uppercase border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 rounded-xl" placeholder="TOKEN UJIAN">
                    <div id="tokenError" class="hidden text-red-500 text-xs font-semibold mt-2 text-center">Token yang Anda masukkan salah!</div>
                </div>
                
                <button type="button" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-xl transition duration-200 flex items-center justify-center gap-2" id="btnSubmitToken">
                    <i class="bi bi-box-arrow-in-right"></i>Masuk Ujian
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Vanilla JS (tanpa jQuery). dom.on() = delegasi di document -> idempoten
// terhadap SPA re-inject #content.
(function () {
    dom.on('click', '#startTestButton', function () {
        UI.modal.open('#tokenModal');
    });

    dom.on('click', '#btnSubmitToken', function () {
        const tokenInput = dom.qs('#inputToken');
        const token = (tokenInput ? tokenInput.value : '').trim();
        const errorEl = dom.qs('#tokenError');

        function showError(msg) {
            if (!errorEl) return;
            errorEl.textContent = msg;
            dom.show(errorEl);
        }

        if (!token) {
            showError('Masukkan token!');
            return;
        }

        const btn = this;
        const originalHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="animate-spin rounded-full h-4 w-4 border-2 border-white border-t-transparent inline-block mr-2"></span>Memverifikasi...';
        dom.hide(errorEl);

        function restoreBtn() {
            btn.disabled = false;
            btn.innerHTML = originalHtml;
        }

        dom.postJSON(APP_URL + '/exam/verifyToken', { token: token })
            .then(function (res) {
                if (res && res.status === 'success') {
                    window.location.href = APP_URL + '/soal';
                } else {
                    showError(res.message || 'Token salah!');
                    restoreBtn();
                }
            })
            .catch(function (err) {
                console.error('Token verification failed:', err);
                showError('Terjadi kesalahan server');
                restoreBtn();
            });
    });

    dom.on('keypress', '#inputToken', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const btn = dom.qs('#btnSubmitToken');
            if (btn) btn.click();
        }
    });
})();
</script>
