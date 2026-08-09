<?php
/**
 * Halaman 404 untuk nama halaman yang tidak dikenal.
 *
 * Dirender oleh HomeController::renderNotFound() dalam dua mode:
 *  - permintaan SPA (AJAX) -> hanya potongan ini, disuntik app.js ke #content
 *  - akses URL langsung    -> dibungkus layout (sidebar tetap tampil)
 *
 * @var string $pageTidakDikenal Nama halaman yang diminta pengguna
 */
$pageTidakDikenal = $pageTidakDikenal ?? '';
?>
<main class="max-w-7xl mx-auto pb-8">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-10 md:p-16 text-center">

        <div class="w-20 h-20 rounded-2xl bg-amber-50 text-amber-500 flex items-center justify-center mx-auto mb-6">
            <i class="bi bi-compass text-4xl"></i>
        </div>

        <p class="text-5xl md:text-6xl font-extrabold bg-gradient-to-br from-primary-dark to-secondary bg-clip-text text-transparent mb-3">404</p>

        <h1 class="text-xl md:text-2xl font-bold text-slate-800 mb-2">Halaman Tidak Ditemukan</h1>

        <p class="text-sm text-slate-500 leading-relaxed max-w-md mx-auto mb-8">
            <?php if ($pageTidakDikenal !== ''): ?>
                Alamat <span class="font-semibold text-slate-700">&ldquo;<?= htmlspecialchars($pageTidakDikenal) ?>&rdquo;</span>
                tidak tersedia. Periksa kembali penulisan URL, atau kembali ke dashboard.
            <?php else: ?>
                Halaman yang Anda cari tidak tersedia. Silakan kembali ke dashboard.
            <?php endif; ?>
        </p>

        <a href="<?= APP_URL ?>/dashboard"
           class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-primary via-primary-dark to-secondary text-white font-bold text-sm rounded-xl shadow-lg shadow-primary-dark/25 hover:-translate-y-0.5 hover:shadow-xl transition-all duration-300">
            <i class="bi bi-house-door-fill"></i>Kembali ke Dashboard
        </a>
    </div>
</main>
