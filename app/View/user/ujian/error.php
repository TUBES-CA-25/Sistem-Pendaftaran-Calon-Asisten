<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Config wajib ikut dimuat: di sinilah fontFamily.sans -> Poppins didefinisikan,
         sehingga tidak perlu lagi blok <style> body{font-family} -->
    <script src="<?= APP_URL ?>/Assets/js/core/tailwind-config.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="font-sans bg-rose-50 flex items-center justify-center min-h-screen p-4">
    <div class="bg-white rounded-2xl shadow-xl border border-rose-100 max-w-md w-full p-6 text-center">
        <div class="w-16 h-16 rounded-full bg-rose-50 text-rose-600 flex items-center justify-center mx-auto mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
        </div>
        <h1 class="text-xl font-bold text-slate-800 mb-2">Terjadi Kesalahan</h1>
        <p class="text-slate-500 text-sm leading-relaxed mb-6"><?= htmlspecialchars($message ?? 'Terjadi kesalahan.') ?></p>
        <a href="<?= APP_URL ?>" class="inline-flex w-full items-center justify-center py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-xl transition duration-200">
            Kembali ke Dashboard
        </a>
    </div>
</body>
</html>
