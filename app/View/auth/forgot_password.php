<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - Sistem Pendaftaran</title>
    <link rel="icon" href="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Img/iclabs.png">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="<?= APP_URL ?>/Assets/js/core/tailwind-config.js"></script>

    <style type="text/tailwindcss">
        @layer utilities {
            /* Latar auth: sama persis dengan halaman login - foto
               laboratorium terpadu FIKOM UMI di bawah tirai gradasi brand. */
            .bg-auth {
                @apply bg-gradient-auth;
                /* Komposisi abstrak lambang ICLABS di atas gradasi yang sama
                   dengan frame login. Tirainya sangat tipis dan HANYA memakai
                   warna brand: henti navy yang dipakai sebelumnya menarik
                   warna latar menjauh dari biru frame. */
                /* Disamakan dengan halaman login: latar putih.
                   Kartu di halaman ini putih seluruhnya, jadi yang memisahkannya
                   dari latar tinggal bayangan - karena itu bayangannya
                   diperkuat di bawah. */
                background-color: #2563eb;
                background-image: linear-gradient(150deg, #3dc2ec 0%, #2563eb 52%, #4b70f5 100%);
                background-attachment: fixed;
            }
            /* background-attachment: fixed berat di peramban HP dan membuat
               latar meloncat saat bilah alamat muncul-hilang. */
            @media (max-width: 767.98px) {
                .bg-auth { background-attachment: scroll; }
            }

            /* Lapisan lambang ICLABS di latar - sama dengan halaman login,
               supaya ketiga halaman auth terasa satu rangkaian. Warna teal
               aslinya dipakai apa adanya, hanya diredam opasitasnya. */
            .lapis-cap {
                position: fixed;
                inset: 0;
                z-index: -10;
                overflow: hidden;
                pointer-events: none;
            }
            .cap-iclabs {
                position: absolute;
                /* Diputihkan lewat filter: di atas gradasi biru, warna teal asli
                   logonya nyaris tidak terlihat. Semua salinan memakai src yang
                   sama sehingga peramban hanya mengunduhnya sekali (terukur). */
                filter: brightness(0) invert(1);
                user-select: none;
            }

            /* Floating Label Pattern - identik dengan auth/index.php */
            .float-group {
                position: relative;
            }
            .float-group input:focus ~ .float-label,
            .float-group input:not(:placeholder-shown) ~ .float-label,
            .float-group input:-webkit-autofill ~ .float-label {
                top: 0;
                transform: translateY(-50%) scale(0.85);
                color: theme('colors.primary.DEFAULT');
                font-weight: 500;
                background-color: white;
                padding: 0 4px;
            }
            .float-group:focus-within .float-icon {
                filter: invert(56%) sepia(87%) saturate(2256%) hue-rotate(164deg) brightness(101%) contrast(92%);
                opacity: 1;
            }
        }

        @layer components {
            /* Alert: class .alert/.alert-success/.alert-error dipertahankan
               karena di-toggle oleh JS di bawah. Hanya gayanya yang berubah. */
            .alert {
                @apply hidden rounded-xl mb-5 px-4 py-3 text-sm font-medium animate-fade-up;
            }
            .alert-success {
                @apply block bg-emerald-50 text-emerald-700 border border-emerald-200;
            }
            .alert-error {
                @apply block bg-red-50 text-red-600 border border-red-200;
            }

            /* Spinner tombol - menggantikan .spinner-border milik Bootstrap */
            .loading {
                @apply hidden w-4 h-4 border-2 border-white/40 border-t-white rounded-full mr-2 align-[-2px];
            }
            .loading.active {
                @apply inline-block animate-spin;
            }

            /* Section link reset (ditampilkan via .active oleh JS) */
            .reset-link-section {
                @apply hidden bg-primary-light/60 rounded-xl p-5 mt-5 border-l-4 border-primary;
            }
            .reset-link-section.active {
                @apply block;
            }
        }

        /* Hormati preferensi pengguna: matikan animasi dekoratif */
        @media (prefers-reduced-motion: reduce) {
            .animate-blob,
            .animate-fade-up,
            .animate-shake,
            .animate-spin {
                animation: none !important;
            }
            /* Guard di atas hanya menutup `animation`, bukan `transition`. */
            .transition-all,
            .transition-opacity,
            .transition-transform {
                transition-duration: 0.01ms !important;
            }
        }
    </style>
</head>
<body class="bg-auth flex items-start sm:items-center justify-center min-h-screen overflow-y-auto py-8 px-4 text-gray-800 relative overflow-x-hidden animate-page-fade">

    <div class="lapis-cap" aria-hidden="true">
        <img src="<?= APP_URL ?>/Assets/Img/iclabs.png" alt="" class="cap-iclabs"
             style="width: 26%; left: -6%; top: -8%; transform: rotate(-14deg); opacity: 0.17;">
        <img src="<?= APP_URL ?>/Assets/Img/iclabs.png" alt="" class="cap-iclabs"
             style="width: 17%; left: 74%; top: 6%; transform: rotate(18deg); opacity: 0.13;">
        <img src="<?= APP_URL ?>/Assets/Img/iclabs.png" alt="" class="cap-iclabs"
             style="width: 13%; left: 30%; top: 22%; transform: rotate(-8deg); opacity: 0.15;">
        <img src="<?= APP_URL ?>/Assets/Img/iclabs.png" alt="" class="cap-iclabs"
             style="width: 20%; left: 88%; top: 34%; transform: rotate(26deg); opacity: 0.11;">
        <img src="<?= APP_URL ?>/Assets/Img/iclabs.png" alt="" class="cap-iclabs"
             style="width: 11%; left: 6%; top: 48%; transform: rotate(12deg); opacity: 0.14;">
        <img src="<?= APP_URL ?>/Assets/Img/iclabs.png" alt="" class="cap-iclabs"
             style="width: 15%; left: 62%; top: 58%; transform: rotate(-22deg); opacity: 0.12;">
        <img src="<?= APP_URL ?>/Assets/Img/iclabs.png" alt="" class="cap-iclabs"
             style="width: 23%; left: -4%; top: 70%; transform: rotate(8deg); opacity: 0.13;">
        <img src="<?= APP_URL ?>/Assets/Img/iclabs.png" alt="" class="cap-iclabs"
             style="width: 12%; left: 84%; top: 80%; transform: rotate(-16deg); opacity: 0.13;">
        <img src="<?= APP_URL ?>/Assets/Img/iclabs.png" alt="" class="cap-iclabs"
             style="width: 16%; left: 40%; top: 88%; transform: rotate(22deg); opacity: 0.11;">
    </div>

    <!-- ===== BLOB LATAR (dekoratif) ===== -->
    <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none" aria-hidden="true">
        <?php /* Blob dikecilkan: dirancang untuk latar terang, di atas foto lab
                 yang gelap hanya jadi kabut yang memudarkan fotonya. */ ?>
        <div class="absolute -top-24 -left-24 w-[420px] h-[420px] rounded-full bg-primary/20 blur-3xl opacity-30 animate-blob"></div>
    </div>

    <div class="w-full max-w-[440px]">
        <div class="bg-white rounded-[24px] shadow-[0_18px_35px_-12px_rgba(6,18,50,0.22),0_45px_90px_-25px_rgba(6,18,50,0.28)] p-8 md:p-10 animate-fade-up">

            <!-- Logo trio - menyatukan halaman ini dengan halaman login -->
            <div class="flex items-center justify-center gap-2 md:gap-3 mb-6 w-full">
                <img src="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Img/umi.png" alt="UMI" class="w-[44px] md:w-[56px] object-contain">
                <img src="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Img/fikom.png" alt="FIKOM" class="w-[44px] md:w-[56px] object-contain">
                <img src="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Img/iclabs.png" alt="ICLABS" class="w-[44px] md:w-[56px] object-contain">
            </div>

            <div class="text-center mb-7">
                <div class="inline-flex items-center justify-center rounded-full w-[64px] h-[64px] bg-primary-light text-primary-dark mb-4">
                    <i class="bi bi-lock-fill text-[1.8rem]"></i>
                </div>
                <h1 class="text-2xl md:text-[28px] font-bold text-primary-dark mb-2">Lupa Password</h1>
                <p class="text-gray-500 text-sm leading-relaxed">Masukkan email yang anda gunakan saat mendaftar untuk melanjutkan</p>
            </div>

            <div id="alertMessage" class="alert"></div>

            <form id="forgotPasswordForm">
                <div class="float-group w-full mb-5 flex items-center bg-white border-[1.5px] border-slate-200 hover:border-slate-300 rounded-xl px-3 md:px-4 h-[52px] transition-colors focus-within:border-primary focus-within:ring-4 focus-within:ring-primary/10">
                    <img src="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Img/profile.svg" alt="Email" class="float-icon w-[18px] md:w-[22px] opacity-45 transition-all duration-300 z-10">
                    <input type="email"
                        class="absolute inset-0 w-full h-full bg-transparent border-none focus:ring-0 focus:outline-none text-[13px] md:text-[14.5px] font-medium pl-[36px] md:pl-[44px] pr-4"
                        id="email"
                        name="email"
                        placeholder=" "
                        autocomplete="off"
                        required>
                    <label class="float-label absolute left-[36px] md:left-[44px] top-1/2 -translate-y-1/2 text-gray-400 text-[13px] md:text-[14.5px] pointer-events-none transition-all duration-300 origin-left" for="email">Email</label>
                </div>

                <button type="submit" class="w-full bg-gradient-to-br from-primary to-secondary rounded-xl py-3 text-[14px] md:text-[15px] font-semibold text-white tracking-wide hover:-translate-y-0.5 hover:shadow-lg active:translate-y-0 active:shadow-sm transition-all flex justify-center items-center">
                    <span class="loading"></span>
                    <span class="btn-text">Kirim Reset Link ke Email</span>
                </button>
            </form>

            <div class="reset-link-section"></div>

            <div class="text-center mt-6">
                <a href="/Sistem-Pendaftaran-Calon-Asisten/public/login" class="inline-flex items-center gap-1.5 text-sm font-medium text-gray-500 hover:text-primary transition-colors no-underline">
                    <i class="bi bi-arrow-left"></i> Kembali ke Login
                </a>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('forgotPasswordForm').addEventListener('submit', async (e) => {
            e.preventDefault();

            const email = document.getElementById('email').value;
            const alertDiv = document.getElementById('alertMessage');
            const loading = document.querySelector('.loading');
            const btnText = document.querySelector('.btn-text');

            // Reset alert
            alertDiv.className = 'alert';
            alertDiv.textContent = '';
            alertDiv.style.display = 'none';

            // Show loading
            loading.classList.add('active');
            btnText.textContent = 'Mengirim...';

            try {
                // Ensure correct endpoint
                const response = await fetch('/Sistem-Pendaftaran-Calon-Asisten/public/lupa-password/send', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `email=${encodeURIComponent(email)}`
                });

                const data = await response.json();

                if (data.status === 'success') {
                    alertDiv.className = 'alert alert-success';
                    alertDiv.style.display = 'block';
                    alertDiv.textContent = data.message;

                    // Clear form
                    document.getElementById('forgotPasswordForm').reset();
                } else {
                    alertDiv.className = 'alert alert-error';
                    alertDiv.style.display = 'block';
                    alertDiv.textContent = data.message || 'Terjadi kesalahan. Silahkan coba lagi.';
                }
            } catch (error) {
                console.error('Error:', error);
                alertDiv.className = 'alert alert-error';
                alertDiv.style.display = 'block';
                alertDiv.textContent = 'Terjadi kesalahan jaringan atau server.';
            } finally {
                loading.classList.remove('active');
                btnText.textContent = 'Kirim Reset Link ke Email';
            }
        });
    </script>
</body>
</html>
