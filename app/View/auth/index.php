<?php
$remember_stambuk = $_COOKIE['remember_stambuk'] ?? '';
$remember_password = $_COOKIE['remember_password'] ?? '';
$checked = ($remember_stambuk !== '') ? 'checked' : '';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
            /* Latar auth: foto laboratorium terpadu FIKOM UMI. */
            /* LATAR & KARTU JADI SATU BIDANG.
               Seluruh halaman berlatar gradasi brand - TANPA bidang putih apa
               pun di latar. Sisi panel kartu memakai gradasi yang sama sehingga
               melebur ke latar, dan satu-satunya bidang putih di halaman ini
               adalah area formulir DI DALAM kartu.

               Pola ini mengikuti referensi: di sana latar juga satu warna penuh,
               dan putihnya hanya ada di dalam kartu. Versi sebelumnya sempat
               memakai sapuan putih di latar; itu justru memecah halaman jadi dua
               bidang dan membuat kartu terbaca menempel, bukan menyambung. */
            .bg-auth {
                @apply bg-gradient-auth;
                background-color: #2563eb;
                background-image: linear-gradient(150deg, #3dc2ec 0%, #2563eb 52%, #4b70f5 100%);
                background-attachment: fixed;
            }
            @media (max-width: 767.98px) {
                .bg-auth { background-attachment: scroll; }
            }

            /* Panel biru kartu memakai gradasi yang sama dengan latar, sehingga
               warnanya berdekatan dan kartu terbaca menyambung - tetapi kartunya
               TETAP berbingkai; tepinya masih terlihat sebagai batas objek. */
            .panel-melebur {
                background-image: linear-gradient(150deg, #3dc2ec 0%, #2563eb 52%, #4b70f5 100%);
                background-size: cover;
            }

            /* Lapisan lambang ICLABS di atas bidang gradasi. */
            .lapis-cap {
                position: fixed;
                inset: 0;
                z-index: -10;
                overflow: hidden;
                pointer-events: none;
            }

            /* Latar kini putih, jadi filter pemutih pada lambang DICABUT -
               kalau dibiarkan, lambangnya putih di atas putih dan hilang total.
               Warna teal aslinya dipakai apa adanya, hanya diredam opasitasnya.
               Semua salinan memakai src yang sama sehingga peramban hanya
               mengunduhnya sekali (terukur). */
            .cap-iclabs {
                position: absolute;
                /* Diputihkan lewat filter: di atas gradasi biru, warna teal asli
                   logonya nyaris tidak terlihat. Semua salinan memakai src yang
                   sama sehingga peramban hanya mengunduhnya sekali (terukur). */
                filter: brightness(0) invert(1);
                user-select: none;
            }

            .hide-scroll {
                overflow-x: hidden;
            }

            /* Pola titik halus untuk latar. radial-gradient dipakai langsung
               (bukan @apply) karena Tailwind tidak punya utility pola titik. */
            .auth-dots {
                background-image: radial-gradient(theme('colors.primary.dark') 1px, transparent 1px);
                background-size: 28px 28px;
            }

            /* Kilau diagonal tipis pada panel biru agar tidak terasa datar. */
            .panel-sheen {
                background-image: linear-gradient(135deg, rgba(255,255,255,.16) 0%, rgba(255,255,255,0) 45%);
            }

            /* Floating Label Pattern */
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
            /* Spinner tombol - menggantikan .spinner-border milik Bootstrap.
               Disalin dari forgot_password.php agar ketiga halaman auth memakai
               spinner yang sama (Bootstrap CSS tidak dimuat di halaman auth). */
            .loading {
                @apply hidden w-4 h-4 border-2 border-white/40 border-t-white rounded-full mr-2 align-[-2px];
            }
            .loading.active {
                @apply inline-block animate-spin;
            }

            /* Kotak OTP yang sudah terisi diberi penanda warna. */
            .otp-input.filled {
                @apply border-primary bg-primary-light/40 text-primary-dark;
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
            /* Guard di atas hanya menutup `animation`, bukan `transition`.
               Geser panel 600ms adalah gerakan paling dominan di halaman ini. */
            .transition-all,
            .transition-opacity,
            .transition-transform {
                transition-duration: 0.01ms !important;
            }
        }
    </style>
    <!-- CSS biasa: SENGAJA di luar <style type="text/tailwindcss">.
         @keyframes yang ditaruh di dalam blok itu tidak ikut dikompilasi -
         terukur: kelas .mengocok terpasang, tetapi sudut putar kartu tetap 0
         derajat sepanjang animasi. Di <style> biasa, peramban memakainya
         apa adanya. -->
    <style>
        /* Animasi memutar kartu SATU KALI PENUH saat berpindah Masuk <-> Daftar.
           Sebelumnya kartu diputar ke tepi lalu kembali dari sisi seberang
           (0 -> -86 -> +86 -> 0); gerakan itu terbaca sebagai DUA hentakan.
           Sekarang satu putaran utuh 0 -> 360 derajat: satu gerakan saja.

           Pergantian isi (transisi panel, 600 ms) jatuh tepat saat kartu
           berada pada posisi tertipisnya (sekitar 340-410 ms), jadi
           pertukaran formulirnya tersamarkan - itulah yang membuat sisi
           "belakang" terasa benar-benar ada.

           Dipasang lewat kelas terpisah .mengocok oleh login.js, bukan
           digantung pada .active: animasi CSS tidak terpicu ulang hanya
           karena sebuah kelas dilepas, sehingga arah sebaliknya
           (Daftar -> Masuk) tidak akan ikut beranimasi. */
        @keyframes kocok-kartu {
            0%   { transform: perspective(1600px) rotateY(0deg)   scale(1); }
            50%  { transform: perspective(1600px) rotateY(180deg) scale(.93); }
            100% { transform: perspective(1600px) rotateY(360deg) scale(1); }
        }
        /* HANYA untuk layar HP (< 768px, ambang yang sama dengan md: milik
           Tailwind). Di layar lebar, kartu tetap memakai panel biru yang
           menggeser seperti sebelumnya - animasi balik ini justru akan menimpa
           `animate-fade-up` pada kartu dan mengacaukan gerak geser panelnya. */
        /* Pertukaran formulir ikut dipersingkat agar tetap selesai SELAGI
           kartu tak terlihat. Bawaannya 600 ms - lebih lama daripada putaran
           yang kini 450 ms, sehingga form lama masih terlihat memudar setelah
           kartu muncul kembali. 260 ms jatuh di dalam jendela tak-terlihat
           (sekitar 112-337 ms pada putaran 450 ms). */
        @media (max-width: 767.98px) {
        /* #container ditambahkan bukan sekadar gaya penulisan: tanpa itu
           spesifisitasnya sama dengan `duration-[600ms]` milik Tailwind, dan
           CSS Play CDN disuntik ke <head> BELAKANGAN sehingga justru Tailwind
           yang menang. Terukur: durasinya tetap terbaca 0.6s. */
        #container .panel-form {
            transition-duration: .26s;
        }
        #container.mengocok {
            animation: kocok-kartu .20s cubic-bezier(.5, 0, .5, 1) both;
            transform-origin: center center;
            will-change: transform;
            /* Sisi belakang disembunyikan: kartu ini hanya punya SATU muka,
               jadi tanpa ini bagian 90-270 derajat menampilkan isinya dalam
               keadaan tercermin - terbaca sebagai depan > belakang > depan,
               seolah berputar dua kali. Dengan disembunyikan, kartu lenyap
               selagi memunggungi layar dan muncul lagi setelah lewat, sehingga
               satu putaran terbaca sebagai satu kali berbalik.

               Pertukaran formulir (transisi panel 600 ms) berlangsung persis
               dalam rentang tak-terlihat itu (sekitar 190-560 ms). */
            backface-visibility: hidden;
            -webkit-backface-visibility: hidden;
        }
        }
        @media (prefers-reduced-motion: reduce) {
            #container.mengocok { animation: none; }
        }
    </style>

    
    <title>Pendaftaran Calon Asisten ICLABS</title>
</head>

<body class="bg-auth flex items-start justify-center min-h-screen overflow-y-auto py-5 hide-scroll text-gray-800 relative animate-page-fade">

    <?php /* Lapisan blob dekoratif DIHAPUS. Latar kini sengaja berupa dua
             bidang warna rata yang meniru belahan kartu; blob biru berpendar
             di atasnya justru mengotori sisi putih dan membuat garis belahannya
             tidak lagi bersih. */ ?>

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
        <div class="absolute -top-24 -left-24 w-[420px] h-[420px] rounded-full bg-primary/20 blur-3xl opacity-30 animate-blob"></div>
    </div>

    <!--
      .group class is added so we can use group-active: variants for child animations
    -->
    <?php /* `ring-1 ring-white/60` DIHAPUS dari kartu.
             Cincin putih itu tidak terlihat sewaktu latar masih putih, tetapi
             begitu latar menjadi biru penuh ia muncul sebagai garis terang yang
             mengelilingi kartu - justru membatalkan efek melebur pada sisi
             panel. Pemisah kartu kini cukup ditanggung bayangannya saja. */ ?>
    <div class="group bg-white my-auto md:rounded-[30px] rounded-[24px] shadow-[0_10px_28px_-8px_rgba(6,18,50,0.40)] md:shadow-[0_18px_35px_-12px_rgba(6,18,50,0.35),0_45px_90px_-25px_rgba(6,18,50,0.55)] relative overflow-hidden w-[95%] md:w-[90%] max-w-[440px] md:max-w-[768px] min-h-auto md:min-h-[520px] flex flex-col md:block animate-fade-up" id="container">

        <!-- ===== MOBILE TOGGLE BANNER (Only visible <= 768px) ===== -->
        <?php /* Pengalih Masuk <-> Daftar untuk HP.
                 Dulu berupa blok biru setinggi 140 px berisi judul, kalimat
                 penjelas, dan tombol berbingkai - porsinya hampir sebesar
                 formulirnya sendiri padahal isinya cuma satu tautan. Sekarang
                 satu baris teks, pola yang lazim di halaman masuk.
                 Kedua id tombol DIPERTAHANKAN karena login.js mengikatnya. */ ?>
        <div class="md:hidden order-last px-6 pb-7 pt-1 text-center">
            <p class="text-[13px] font-medium text-slate-500 group-active:hidden">
                Belum punya akun?
                <button type="button" id="register-mobile" class="ml-1 font-bold text-primary-dark hover:underline underline-offset-2">Daftar sekarang</button>
            </p>
            <p class="hidden text-[13px] font-medium text-slate-500 group-active:block">
                Sudah punya akun?
                <button type="button" id="login-mobile" class="ml-1 font-bold text-primary-dark hover:underline underline-offset-2">Masuk</button>
            </p>
        </div>

        <!-- ===== FORM SIGN-IN ===== -->
        <div class="relative md:absolute top-0 left-0 w-full md:w-1/2 h-auto md:h-full z-20 transition-all duration-[600ms] ease-[cubic-bezier(0.65,0,0.35,1)] 
                    md:group-active:translate-x-full md:group-active:opacity-0 md:group-active:z-10
                    panel-form grid transition-[grid-template-rows,opacity]
                    grid-rows-[1fr] opacity-100 group-active:grid-rows-[0fr] group-active:opacity-0 group-active:pointer-events-none md:group-active:pointer-events-auto md:grid-rows-[1fr] md:opacity-100">
            <div class="overflow-hidden min-h-0">
                <form id="loginForm" autocomplete="off" class="bg-white flex flex-col items-center justify-center px-6 md:px-10 py-8 md:py-0 h-full w-full">
                    <div class="flex items-center justify-center gap-2 md:gap-3 mb-6 w-full animate-fade-up">
                        <img src="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Img/umi.png" alt="UMI" class="w-[44px] md:w-[68px] object-contain">
                        <img src="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Img/fikom.png" alt="FIKOM" class="w-[44px] md:w-[68px] object-contain">
                        <img src="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Img/iclabs.png" alt="ICLABS" class="w-[44px] md:w-[68px] object-contain">
                    </div>

                    <h1 class="text-center mb-4 text-2xl md:text-3xl font-extrabold text-primary-dark animate-fade-up" style="animation-delay: 60ms;">Masuk</h1>

                    <div class="float-group w-full mb-3 md:mb-4 flex items-center bg-slate-50/60 hover:bg-white focus-within:bg-white border-[1.5px] border-slate-200 hover:border-slate-300 rounded-xl px-3 md:px-4 h-[48px] md:h-[54px] transition-all duration-200 focus-within:border-primary focus-within:shadow-md focus-within:shadow-primary/10 focus-within:ring-4 focus-within:ring-primary/10 animate-fade-up" style="animation-delay: 120ms;">
                        <img src="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Img/idcard.svg" alt="ID" class="float-icon w-[18px] md:w-[22px] opacity-45 transition-all duration-300 z-10">
                        <input type="text" class="absolute inset-0 w-full h-full bg-transparent border-none focus:ring-0 focus:outline-none text-[13px] md:text-[14.5px] font-medium pl-[36px] md:pl-[44px] pr-4 pt-[2px]" id="stambuk" name="stambuk" placeholder=" " value="<?= htmlspecialchars($remember_stambuk) ?>" autocomplete="off" required>
                        <label class="float-label absolute left-[36px] md:left-[44px] top-1/2 -translate-y-1/2 text-gray-400 text-[13px] md:text-[14.5px] pointer-events-none transition-all duration-300 origin-left">Stambuk</label>
                    </div>

                    <div class="float-group w-full mb-3 md:mb-4 flex items-center bg-slate-50/60 hover:bg-white focus-within:bg-white border-[1.5px] border-slate-200 hover:border-slate-300 rounded-xl px-3 md:px-4 h-[48px] md:h-[54px] transition-all duration-200 focus-within:border-primary focus-within:shadow-md focus-within:shadow-primary/10 focus-within:ring-4 focus-within:ring-primary/10 animate-fade-up" style="animation-delay: 180ms;">
                        <img src="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Img/password.svg" alt="Password" class="float-icon w-[18px] md:w-[22px] opacity-45 transition-all duration-300 z-10">
                        <input type="password" class="absolute inset-0 w-full h-full bg-transparent border-none focus:ring-0 focus:outline-none text-[13px] md:text-[14.5px] font-medium pl-[36px] md:pl-[44px] pr-[40px] pt-[2px]" id="passwordLogin" name="password" placeholder=" " value="<?= htmlspecialchars($remember_password) ?>" autocomplete="off" required>
                        <label class="float-label absolute left-[36px] md:left-[44px] top-1/2 -translate-y-1/2 text-gray-400 text-[13px] md:text-[14.5px] pointer-events-none transition-all duration-300 origin-left">Password</label>
                        <span class="absolute right-2 top-1/2 -translate-y-1/2 cursor-pointer text-gray-400 flex items-center justify-center w-7 h-7 md:w-8 md:h-8 rounded-md hover:text-primary hover:bg-primary/10 transition-colors z-10" id="loginIconPass">
                            <i class="bi bi-eye-slash pointer-events-none" id="togglePassLogin"></i>
                        </span>
                    </div>

                    <div class="flex flex-wrap justify-between items-center w-full mb-4 md:mb-5 text-[12px] md:text-[13px] gap-2 animate-fade-up" style="animation-delay: 240ms;">
                        <div class="flex items-center">
                            <input type="checkbox" class="w-3.5 h-3.5 text-primary rounded border-gray-300 focus:ring-primary mr-2" id="customCheck" name="check" <?= $checked ?>>
                            <label for="customCheck" class="text-gray-600 cursor-pointer select-none">Remember me</label>
                        </div>
                        <a href="lupa-password" class="text-gray-600 hover:text-primary transition-colors">Lupa password?</a>
                    </div>

                    <button type="submit" class="w-full bg-gradient-primary hover:bg-secondary rounded-xl py-3 md:py-3.5 text-[14px] md:text-[15px] font-bold text-white tracking-wide shadow-lg shadow-primary-dark/25 hover:-translate-y-0.5 hover:shadow-xl hover:shadow-primary-dark/35 active:translate-y-0 transition-all duration-300 flex justify-center items-center gap-2 animate-fade-up disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:translate-y-0 disabled:hover:shadow-none" style="animation-delay: 300ms;" name="login" id="btnlogin">
                        <span class="loading"></span>
                        <i class="bi bi-box-arrow-in-right btn-icon"></i><span class="btn-text">Login</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- ===== FORM SIGN-UP ===== -->
        <div class="relative md:absolute top-0 left-0 w-full md:w-1/2 h-auto md:h-full transition-all duration-[600ms] ease-[cubic-bezier(0.65,0,0.35,1)]
                    md:opacity-0 md:z-10
                    md:group-active:translate-x-full md:group-active:opacity-100 md:group-active:z-[50]
                    panel-form grid transition-[grid-template-rows,opacity]
                    grid-rows-[0fr] opacity-0 group-active:grid-rows-[1fr] group-active:opacity-100 group-active:pointer-events-auto pointer-events-none md:pointer-events-auto md:group-active:pointer-events-auto md:grid-rows-[1fr] md:group-active:grid-rows-[1fr]">
            <div class="overflow-hidden min-h-0">
                <form id="registerForm" autocomplete="off" class="bg-white flex flex-col items-center justify-center px-6 md:px-10 py-8 md:py-0 h-full w-full">
                    <!-- Logo trio: menyamakan panel Daftar dengan panel Masuk.
                         Ukuran lebih kecil dari panel Masuk (md:w-[56px] vs 68px)
                         karena panel ini punya 4 input - risiko meluber di 520px. -->
                    <div class="flex items-center justify-center gap-2 md:gap-3 mb-4 w-full animate-fade-up">
                        <img src="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Img/umi.png" alt="UMI" class="w-[38px] md:w-[52px] object-contain">
                        <img src="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Img/fikom.png" alt="FIKOM" class="w-[38px] md:w-[52px] object-contain">
                        <img src="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Img/iclabs.png" alt="ICLABS" class="w-[38px] md:w-[52px] object-contain">
                    </div>

                    <h1 class="text-center mb-4 text-2xl md:text-3xl font-extrabold text-primary-dark animate-fade-up" style="animation-delay: 60ms;">Buat Akun</h1>

                    <div class="float-group w-full mb-3 md:mb-4 flex items-center bg-slate-50/60 hover:bg-white focus-within:bg-white border-[1.5px] border-slate-200 hover:border-slate-300 rounded-xl px-3 md:px-4 h-[48px] md:h-[52px] transition-all duration-200 focus-within:border-primary focus-within:shadow-md focus-within:shadow-primary/10 focus-within:ring-4 focus-within:ring-primary/10 animate-fade-up" style="animation-delay: 120ms;">
                        <img src="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Img/profile.svg" alt="Email" class="float-icon w-[18px] md:w-[22px] opacity-45 transition-all duration-300 z-10">
                        <input type="email" class="absolute inset-0 w-full h-full bg-transparent border-none focus:ring-0 focus:outline-none text-[13px] md:text-[14.5px] font-medium pl-[36px] md:pl-[44px] pr-4 pt-[2px]" id="email" name="email" placeholder=" " autocomplete="off" required>
                        <label class="float-label absolute left-[36px] md:left-[44px] top-1/2 -translate-y-1/2 text-gray-400 text-[13px] md:text-[14.5px] pointer-events-none transition-all duration-300 origin-left">stambuk@student.umi.ac.id</label>
                    </div>
                    <div id="emailError" class="text-red-500 text-xs w-full text-left -mt-2 mb-2 pl-1 empty:hidden animate-fade-up"></div>

                    <div class="float-group w-full mb-3 md:mb-4 flex items-center bg-slate-50/60 hover:bg-white focus-within:bg-white border-[1.5px] border-slate-200 hover:border-slate-300 rounded-xl px-3 md:px-4 h-[48px] md:h-[52px] transition-all duration-200 focus-within:border-primary focus-within:shadow-md focus-within:shadow-primary/10 focus-within:ring-4 focus-within:ring-primary/10 animate-fade-up" style="animation-delay: 180ms;">
                        <img src="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Img/idcard.svg" alt="ID" class="float-icon w-[18px] md:w-[22px] opacity-45 transition-all duration-300 z-10">
                        <input type="text" class="absolute inset-0 w-full h-full bg-transparent border-none focus:ring-0 focus:outline-none text-[13px] md:text-[14.5px] font-medium pl-[36px] md:pl-[44px] pr-4 pt-[2px]" id="stambukregister" name="stambuk" placeholder=" " autocomplete="off" required>
                        <label class="float-label absolute left-[36px] md:left-[44px] top-1/2 -translate-y-1/2 text-gray-400 text-[13px] md:text-[14.5px] pointer-events-none transition-all duration-300 origin-left">Stambuk</label>
                    </div>
                    <div id="stambukRegisterError" class="text-red-500 text-xs w-full text-left -mt-2 mb-2 pl-1 empty:hidden animate-fade-up"></div>

                    <div class="float-group w-full mb-3 md:mb-4 flex items-center bg-slate-50/60 hover:bg-white focus-within:bg-white border-[1.5px] border-slate-200 hover:border-slate-300 rounded-xl px-3 md:px-4 h-[48px] md:h-[52px] transition-all duration-200 focus-within:border-primary focus-within:shadow-md focus-within:shadow-primary/10 focus-within:ring-4 focus-within:ring-primary/10 animate-fade-up" style="animation-delay: 240ms;">
                        <img src="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Img/password.svg" alt="Password" class="float-icon w-[18px] md:w-[22px] opacity-45 transition-all duration-300 z-10">
                        <input type="password" class="absolute inset-0 w-full h-full bg-transparent border-none focus:ring-0 focus:outline-none text-[13px] md:text-[14.5px] font-medium pl-[36px] md:pl-[44px] pr-[40px] pt-[2px]" id="password" name="password" placeholder=" " autocomplete="off" required>
                        <label class="float-label absolute left-[36px] md:left-[44px] top-1/2 -translate-y-1/2 text-gray-400 text-[13px] md:text-[14.5px] pointer-events-none transition-all duration-300 origin-left">Password</label>
                        <span class="absolute right-2 top-1/2 -translate-y-1/2 cursor-pointer text-gray-400 flex items-center justify-center w-7 h-7 md:w-8 md:h-8 rounded-md hover:text-primary hover:bg-primary/10 transition-colors z-10" id="togglePassword">
                            <i class="bi bi-eye-slash pointer-events-none" id="toggleIcon"></i>
                        </span>
                    </div>
                    <div id="passwordError" class="text-red-500 text-xs w-full text-left -mt-2 mb-2 pl-1 empty:hidden animate-fade-up"></div>

                    <div class="float-group w-full mb-3 md:mb-4 flex items-center bg-slate-50/60 hover:bg-white focus-within:bg-white border-[1.5px] border-slate-200 hover:border-slate-300 rounded-xl px-3 md:px-4 h-[48px] md:h-[52px] transition-all duration-200 focus-within:border-primary focus-within:shadow-md focus-within:shadow-primary/10 focus-within:ring-4 focus-within:ring-primary/10 animate-fade-up" style="animation-delay: 300ms;">
                        <img src="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Img/password.svg" alt="Confirm" class="float-icon w-[18px] md:w-[22px] opacity-45 transition-all duration-300 z-10">
                        <input type="password" class="absolute inset-0 w-full h-full bg-transparent border-none focus:ring-0 focus:outline-none text-[13px] md:text-[14.5px] font-medium pl-[36px] md:pl-[44px] pr-[40px] pt-[2px]" id="confirmPass" name="konfirmasiPassword" placeholder=" " autocomplete="off" required>
                        <label class="float-label absolute left-[36px] md:left-[44px] top-1/2 -translate-y-1/2 text-gray-400 text-[13px] md:text-[14.5px] pointer-events-none transition-all duration-300 origin-left">Konfirmasi Password</label>
                        <span class="absolute right-2 top-1/2 -translate-y-1/2 cursor-pointer text-gray-400 flex items-center justify-center w-7 h-7 md:w-8 md:h-8 rounded-md hover:text-primary hover:bg-primary/10 transition-colors z-10" id="confirmPassword">
                            <i class="bi bi-eye-slash pointer-events-none" id="toggleIconConfirmation"></i>
                        </span>
                    </div>
                    <div id="confirmPassError" class="text-red-500 text-xs w-full text-left -mt-2 mb-2 pl-1 empty:hidden animate-fade-up"></div>

                    <button type="submit" class="w-full bg-gradient-primary hover:bg-secondary rounded-xl py-3 md:py-3.5 text-[14px] md:text-[15px] font-bold text-white tracking-wide shadow-lg shadow-primary-dark/25 hover:-translate-y-0.5 hover:shadow-xl hover:shadow-primary-dark/35 active:translate-y-0 transition-all duration-300 flex justify-center items-center gap-2 mt-2 animate-fade-up disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:translate-y-0 disabled:hover:shadow-none" style="animation-delay: 360ms;" name="register" id="btndaftar">
                        <span class="loading"></span>
                        <i class="bi bi-person-plus btn-icon"></i><span class="btn-text">Daftar</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- ===== DESKTOP TOGGLE PANEL ===== -->
        <div class="hidden md:block absolute top-0 left-1/2 w-1/2 h-full overflow-hidden transition-all duration-[600ms] ease-[cubic-bezier(0.65,0,0.35,1)] z-[1000] rounded-tl-[150px] rounded-bl-[100px] group-active:-translate-x-full group-active:rounded-tl-none group-active:rounded-bl-none group-active:rounded-tr-[150px] group-active:rounded-br-[100px]">
            <div class="panel-melebur text-white relative -left-full h-full w-[200%] transition-transform duration-[600ms] ease-[cubic-bezier(0.65,0,0.35,1)] group-active:translate-x-1/2">

                <?php /* Kilau diagonal dan lingkaran putih transparan DIHAPUS.
                         Wadah panel memakai overflow-hidden, jadi hiasan itu
                         terpotong tepat di tepi dan sudut panel - membuat panel
                         sedikit lebih terang daripada latar di sana (terukur
                         selisih 24 dari jumlah RGB di tepi bawah) sehingga batas
                         kartu tetap terbaca. Setelah dihapus, panel benar-benar
                         melebur; kedalaman visualnya kini ditanggung lambang
                         ICLABS di latar yang memang sudah ada. */ ?>

                <!-- Right Panel (Login Active) -->
                <div class="absolute right-0 w-1/2 h-full flex flex-col items-center justify-center px-8 text-center top-0 transition-transform duration-[600ms] ease-[cubic-bezier(0.65,0,0.35,1)] group-active:translate-x-[200%]">
                    <div class="w-20 h-20 rounded-2xl bg-white flex items-center justify-center mb-5 shadow-lg ring-1 ring-white/50 p-2.5">
                        <img src="<?= APP_URL ?>/Assets/Img/iclabs.png" alt="Logo ICLABS-UMI" class="w-full h-full object-contain">
                    </div>
                    <h1 class="text-3xl font-bold mb-3 text-white">Belum punya akun?</h1>
                    <p class="text-[15px] text-white/90 font-medium leading-relaxed mb-5">Silahkan daftar akun untuk melanjutkan proses IC-ASSIST</p>
                    <button class="bg-white/10 backdrop-blur-sm text-white text-[13px] py-2.5 px-12 border border-white/70 rounded-xl font-bold tracking-wide uppercase mt-2 transition-all hover:bg-white hover:text-primary-dark hover:-translate-y-0.5 hover:shadow-lg active:translate-y-0" id="register">Daftar</button>
                </div>

                <!-- Left Panel (Register Active) -->
                <div class="absolute -translate-x-[200%] w-1/2 h-full flex flex-col items-center justify-center px-8 text-center top-0 transition-transform duration-[600ms] ease-[cubic-bezier(0.65,0,0.35,1)] group-active:translate-x-0">
                    <div class="w-20 h-20 rounded-2xl bg-white flex items-center justify-center mb-5 shadow-lg ring-1 ring-white/50 p-2.5">
                        <img src="<?= APP_URL ?>/Assets/Img/iclabs.png" alt="Logo ICLABS-UMI" class="w-full h-full object-contain">
                    </div>
                    <h1 class="text-3xl font-bold mb-3 text-white">Sudah punya akun?</h1>
                    <p class="text-[15px] text-white/90 font-medium leading-relaxed mb-5">Silahkan login jika anda telah mempunyai akun IC-ASSIST</p>
                    <button class="bg-white/10 backdrop-blur-sm text-white text-[13px] py-2.5 px-12 border border-white/70 rounded-xl font-bold tracking-wide uppercase mt-2 transition-all hover:bg-white hover:text-primary-dark hover:-translate-y-0.5 hover:shadow-lg active:translate-y-0" id="login">Masuk</button>
                </div>
            </div>
        </div>

    </div><!-- /.container -->


    <!-- Tailwind Custom Modal -->
    <div id="customModal" class="fixed inset-0 z-[1050] hidden items-center justify-center overflow-x-hidden overflow-y-auto outline-none focus:outline-none opacity-0 transition-opacity duration-200 ease-out data-[open]:opacity-100 bg-slate-900/50 backdrop-blur-sm" aria-hidden="true">
        <div class="absolute inset-0" id="customModalBackdrop"></div>
        <!-- scale mengikuti data-open pada WADAH (#customModal), bukan pada panel
             ini sendiri - karena itu memakai varian arbitrary [&] terhadap induk. -->
        <div class="relative my-6 mx-auto w-[90%] max-w-[340px] z-50 scale-95 transition-transform duration-200 ease-out [[data-open]_&]:scale-100">
            <div class="relative flex w-full flex-col rounded-[20px] border-0 bg-white shadow-2xl outline-none focus:outline-none">
                <div class="relative flex-auto p-7 text-center">
                    <img id="modalGif" src="" alt="Animation" class="mb-4 mx-auto w-[72px] md:w-[84px] rounded-xl" style="display: none;">
                    <p id="modalMessage" class="text-[1rem] md:text-[1.05rem] font-medium text-gray-800 mb-4">Pesan akan ditampilkan di sini.</p>
                    <button type="button" id="closeModal" class="bg-gradient-to-br from-primary to-secondary border-none rounded-[10px] px-7 py-2 text-white font-semibold shadow-sm hover:shadow-md transition-shadow">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Tailwind Modal Verifikasi OTP -->
    <div id="otpModal" class="fixed inset-0 z-[1050] hidden items-center justify-center overflow-x-hidden overflow-y-auto outline-none focus:outline-none opacity-0 transition-opacity duration-200 ease-out data-[open]:opacity-100 bg-slate-900/50 backdrop-blur-sm" aria-hidden="true">
        <div class="absolute inset-0" id="otpModalBackdrop"></div>
        <div class="relative w-auto my-6 mx-auto max-w-md z-50 px-4 scale-95 transition-transform duration-200 ease-out [[data-open]_&]:scale-100">
            <div class="relative flex w-full flex-col rounded-2xl border-0 bg-white shadow-xl outline-none focus:outline-none">
                <div class="flex items-start justify-between rounded-t p-5 pb-0 border-0">
                    <h5 class="text-xl font-bold text-gray-900" id="otpModalLabel">Verifikasi OTP</h5>
                    <button type="button" class="ml-auto bg-transparent border-0 text-black float-right text-3xl leading-none font-semibold outline-none focus:outline-none opacity-50 hover:opacity-75" id="closeOtpModal">
                        <span class="bg-transparent text-black h-6 w-6 text-2xl block outline-none focus:outline-none">&times;</span>
                    </button>
                </div>
                <div class="relative flex-auto p-6 pt-0 text-center">
                    <div class="mb-3 mt-2">
                        <div class="inline-flex items-center justify-center rounded-full w-[70px] h-[70px] bg-primary-light text-primary-dark">
                            <i class="bi bi-shield-lock-fill text-[2.2rem]"></i>
                        </div>
                    </div>
                    <p class="mb-4 text-gray-500 text-sm">Masukkan 6 digit kode OTP yang telah dikirimkan ke email <strong id="otpEmailSpan" class="text-gray-900"></strong></p>
                    <div class="mb-4 text-red-500 font-semibold text-sm">
                        Sisa waktu verifikasi: <span id="otpExpiryTimer">05:00</span>
                    </div>
                        
                    <form id="otpForm" class="mx-auto max-w-[340px]">
                        <div class="flex justify-between gap-2 mb-6">
                            <input type="text" class="w-[45px] h-[50px] text-center text-2xl font-bold text-gray-900 bg-white border border-gray-300 rounded-[10px] focus:border-primary focus:ring-4 focus:ring-primary/20 focus:scale-105 transition-all duration-200 otp-input p-0" maxlength="1" pattern="[0-9]" inputmode="numeric" required>
                            <input type="text" class="w-[45px] h-[50px] text-center text-2xl font-bold text-gray-900 bg-white border border-gray-300 rounded-[10px] focus:border-primary focus:ring-4 focus:ring-primary/20 focus:scale-105 transition-all duration-200 otp-input p-0" maxlength="1" pattern="[0-9]" inputmode="numeric" required>
                            <input type="text" class="w-[45px] h-[50px] text-center text-2xl font-bold text-gray-900 bg-white border border-gray-300 rounded-[10px] focus:border-primary focus:ring-4 focus:ring-primary/20 focus:scale-105 transition-all duration-200 otp-input p-0" maxlength="1" pattern="[0-9]" inputmode="numeric" required>
                            <input type="text" class="w-[45px] h-[50px] text-center text-2xl font-bold text-gray-900 bg-white border border-gray-300 rounded-[10px] focus:border-primary focus:ring-4 focus:ring-primary/20 focus:scale-105 transition-all duration-200 otp-input p-0" maxlength="1" pattern="[0-9]" inputmode="numeric" required>
                            <input type="text" class="w-[45px] h-[50px] text-center text-2xl font-bold text-gray-900 bg-white border border-gray-300 rounded-[10px] focus:border-primary focus:ring-4 focus:ring-primary/20 focus:scale-105 transition-all duration-200 otp-input p-0" maxlength="1" pattern="[0-9]" inputmode="numeric" required>
                            <input type="text" class="w-[45px] h-[50px] text-center text-2xl font-bold text-gray-900 bg-white border border-gray-300 rounded-[10px] focus:border-primary focus:ring-4 focus:ring-primary/20 focus:scale-105 transition-all duration-200 otp-input p-0" maxlength="1" pattern="[0-9]" inputmode="numeric" required>
                        </div>
                        <input type="hidden" id="otpCode" name="otp">
                        
                        <button type="submit" class="w-full text-white bg-gradient-to-br from-primary to-secondary border-none focus:ring-4 focus:outline-none focus:ring-primary/30 font-semibold rounded-xl text-sm px-5 py-3 text-center shadow-sm hover:-translate-y-0.5 hover:shadow-lg active:translate-y-0 active:shadow-sm transition-all flex justify-center items-center disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:translate-y-0 disabled:hover:shadow-none" id="btnVerifyOtp">
                            <span class="loading"></span>
                            <span class="btn-text">Verifikasi Akun</span>
                        </button>
                    </form>
                    
                    <div class="mt-5 text-gray-500 text-sm">
                        Tidak menerima email? 
                        <button type="button" class="bg-transparent border-none p-0 inline align-baseline font-semibold text-primary-dark hover:text-primary cursor-pointer transition-colors" id="btnResendOtp">
                            Kirim Ulang
                        </button>
                        <span id="otpTimer" class="ml-1 text-gray-400 hidden">(60s)</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/js/auth/login.js?v=<?= time() ?>"></script>
    <!-- Skrip toggle inline dihapus: logikanya kini terpusat di auth/login.js
         (showRegister/showLogin) yang mengikat keempat tombol sekaligus. -->
</body>

</html>


