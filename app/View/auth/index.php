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
            /* Latar auth: gradien biru lembut, seragam dengan lupa/reset password */
            .bg-auth {
                @apply bg-gradient-auth;
            }
            .hide-scroll {
                overflow-x: hidden;
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

        /* Hormati preferensi pengguna: matikan animasi dekoratif */
        @media (prefers-reduced-motion: reduce) {
            .animate-blob,
            .animate-fade-up {
                animation: none !important;
            }
        }
    </style>
    
    <title>Pendaftaran Calon Asisten ICLABS</title>
</head>

<body class="bg-auth flex items-center justify-center min-h-screen py-5 hide-scroll text-gray-800 relative">

    <!-- ===== BLOB LATAR (dekoratif) ===== -->
    <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none" aria-hidden="true">
        <div class="absolute -top-24 -left-24 w-[420px] h-[420px] rounded-full bg-primary/30 blur-3xl opacity-40 animate-blob"></div>
        <div class="absolute top-1/3 -right-32 w-[480px] h-[480px] rounded-full bg-secondary/25 blur-3xl opacity-40 animate-blob" style="animation-delay: -6s;"></div>
        <div class="absolute -bottom-32 left-1/4 w-[400px] h-[400px] rounded-full bg-primary-dark/20 blur-3xl opacity-40 animate-blob" style="animation-delay: -12s;"></div>
    </div>

    <!--
      .group class is added so we can use group-active: variants for child animations
    -->
    <div class="group bg-white md:rounded-[30px] rounded-[24px] shadow-[0_5px_15px_rgba(0,0,0,0.18)] md:shadow-2xl relative overflow-hidden w-[95%] md:w-[90%] max-w-[440px] md:max-w-[768px] min-h-auto md:min-h-[520px] flex flex-col md:block animate-fade-up" id="container">

        <!-- ===== MOBILE TOGGLE BANNER (Only visible <= 768px) ===== -->
        <div class="md:hidden relative w-full h-[140px] shrink-0 bg-gradient-to-br from-primary to-secondary rounded-b-[28px] z-10 flex overflow-hidden">
            <!-- Mobile Right Panel (Login Active) -->
            <div class="absolute w-full h-full flex flex-col items-center justify-center text-center p-4 transition-transform duration-500 ease-in-out transform group-active:-translate-x-full">
                <h1 class="text-[1.1rem] font-bold text-white mb-1">Belum punya akun?</h1>
                <p class="text-[12px] text-white/90 mb-2">Silahkan daftar akun untuk melanjutkan</p>
                <button class="bg-transparent text-white text-[11px] py-1.5 px-6 border border-white rounded-xl font-bold uppercase transition-all duration-300 hover:bg-white/10" id="register-mobile">Daftar</button>
            </div>
            <!-- Mobile Left Panel (Register Active) -->
            <div class="absolute w-full h-full flex flex-col items-center justify-center text-center p-4 transition-transform duration-500 ease-in-out transform translate-x-full group-active:translate-x-0">
                <h1 class="text-[1.1rem] font-bold text-white mb-1">Sudah punya akun?</h1>
                <p class="text-[12px] text-white/90 mb-2">Silahkan login jika anda telah mempunyai akun</p>
                <button class="bg-transparent text-white text-[11px] py-1.5 px-6 border border-white rounded-xl font-bold uppercase transition-all duration-300 hover:bg-white/10" id="login-mobile">Masuk</button>
            </div>
        </div>

        <!-- ===== FORM SIGN-IN ===== -->
        <div class="relative md:absolute top-0 left-0 w-full md:w-1/2 h-auto md:h-full z-20 transition-all duration-[850ms] ease-[cubic-bezier(0.65,0,0.35,1)] 
                    md:group-active:translate-x-full md:group-active:opacity-0 md:group-active:z-10
                    grid transition-[grid-template-rows,opacity]
                    grid-rows-[1fr] opacity-100 group-active:grid-rows-[0fr] group-active:opacity-0 group-active:pointer-events-none md:group-active:pointer-events-auto md:grid-rows-[1fr] md:opacity-100">
            <div class="overflow-hidden min-h-0">
                <form id="loginForm" autocomplete="off" class="bg-white flex flex-col items-center justify-center px-6 md:px-10 py-8 md:py-0 h-full w-full">
                    <div class="flex items-center justify-center gap-2 md:gap-3 mb-6 w-full animate-fade-up">
                        <img src="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Img/umi.png" alt="UMI" class="w-[44px] md:w-[68px] object-contain">
                        <img src="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Img/fikom.png" alt="FIKOM" class="w-[44px] md:w-[68px] object-contain">
                        <img src="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Img/iclabs.png" alt="ICLABS" class="w-[44px] md:w-[68px] object-contain">
                    </div>

                    <h1 class="text-center mb-4 text-2xl md:text-3xl font-bold animate-fade-up" style="animation-delay: 60ms;">Masuk</h1>

                    <div class="float-group w-full mb-3 md:mb-4 flex items-center bg-white border-[1.5px] border-slate-200 hover:border-slate-300 rounded-xl px-3 md:px-4 h-[48px] md:h-[54px] transition-colors focus-within:border-primary focus-within:ring-4 focus-within:ring-primary/10 animate-fade-up" style="animation-delay: 120ms;">
                        <img src="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Img/idcard.svg" alt="ID" class="float-icon w-[18px] md:w-[22px] opacity-45 transition-all duration-300 z-10">
                        <input type="text" class="absolute inset-0 w-full h-full bg-transparent border-none focus:ring-0 focus:outline-none text-[13px] md:text-[14.5px] font-medium pl-[36px] md:pl-[44px] pr-4 pt-[2px]" id="stambuk" name="stambuk" placeholder=" " autocomplete="off" required>
                        <label class="float-label absolute left-[36px] md:left-[44px] top-1/2 -translate-y-1/2 text-gray-400 text-[13px] md:text-[14.5px] pointer-events-none transition-all duration-300 origin-left">Stambuk</label>
                    </div>

                    <div class="float-group w-full mb-3 md:mb-4 flex items-center bg-white border-[1.5px] border-slate-200 hover:border-slate-300 rounded-xl px-3 md:px-4 h-[48px] md:h-[54px] transition-colors focus-within:border-primary focus-within:ring-4 focus-within:ring-primary/10 animate-fade-up" style="animation-delay: 180ms;">
                        <img src="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Img/password.svg" alt="Password" class="float-icon w-[18px] md:w-[22px] opacity-45 transition-all duration-300 z-10">
                        <input type="password" class="absolute inset-0 w-full h-full bg-transparent border-none focus:ring-0 focus:outline-none text-[13px] md:text-[14.5px] font-medium pl-[36px] md:pl-[44px] pr-[40px] pt-[2px]" id="passwordLogin" name="password" placeholder=" " autocomplete="off" required>
                        <label class="float-label absolute left-[36px] md:left-[44px] top-1/2 -translate-y-1/2 text-gray-400 text-[13px] md:text-[14.5px] pointer-events-none transition-all duration-300 origin-left">Password</label>
                        <span class="absolute right-2 top-1/2 -translate-y-1/2 cursor-pointer text-gray-400 flex items-center justify-center w-7 h-7 md:w-8 md:h-8 rounded-md hover:text-primary hover:bg-primary/10 transition-colors z-10" id="loginIconPass">
                            <i class="bi bi-eye-slash pointer-events-none" id="togglePassLogin"></i>
                        </span>
                    </div>

                    <div class="flex flex-wrap justify-between items-center w-full mb-4 md:mb-5 text-[12px] md:text-[13px] gap-2 animate-fade-up" style="animation-delay: 240ms;">
                        <div class="flex items-center">
                            <input type="checkbox" class="w-3.5 h-3.5 text-primary rounded border-gray-300 focus:ring-primary mr-2" id="customCheck" name="check">
                            <label for="customCheck" class="text-gray-600 cursor-pointer select-none">Remember me</label>
                        </div>
                        <a href="lupa-password" class="text-gray-600 hover:text-primary transition-colors">Lupa password?</a>
                    </div>

                    <button type="submit" class="w-full bg-gradient-to-br from-primary to-secondary rounded-xl py-2.5 md:py-3 text-[14px] md:text-[15px] font-semibold text-white tracking-wide hover:-translate-y-0.5 hover:shadow-lg active:translate-y-0 active:shadow-sm transition-all flex justify-center items-center gap-2 animate-fade-up" style="animation-delay: 300ms;" name="login" id="btnlogin">
                        <i class="bi bi-box-arrow-in-right"></i>Login
                    </button>
                </form>
            </div>
        </div>

        <!-- ===== FORM SIGN-UP ===== -->
        <div class="relative md:absolute top-0 left-0 w-full md:w-1/2 h-auto md:h-full transition-all duration-[850ms] ease-[cubic-bezier(0.65,0,0.35,1)]
                    md:opacity-0 md:z-10
                    md:group-active:translate-x-full md:group-active:opacity-100 md:group-active:z-[50]
                    grid transition-[grid-template-rows,opacity]
                    grid-rows-[0fr] opacity-0 group-active:grid-rows-[1fr] group-active:opacity-100 group-active:pointer-events-auto pointer-events-none md:pointer-events-auto md:group-active:pointer-events-auto md:grid-rows-[1fr] md:group-active:grid-rows-[1fr]">
            <div class="overflow-hidden min-h-0">
                <form id="registerForm" autocomplete="off" class="bg-white flex flex-col items-center justify-center px-6 md:px-10 py-8 md:py-0 h-full w-full">
                    <h1 class="text-center mb-4 text-2xl md:text-3xl font-bold">Buat Akun</h1>

                    <div class="float-group w-full mb-3 md:mb-4 flex items-center bg-white border-[1.5px] border-slate-200 hover:border-slate-300 rounded-xl px-3 md:px-4 h-[48px] md:h-[52px] transition-colors focus-within:border-primary focus-within:ring-4 focus-within:ring-primary/10">
                        <img src="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Img/profile.svg" alt="Email" class="float-icon w-[18px] md:w-[22px] opacity-45 transition-all duration-300 z-10">
                        <input type="email" class="absolute inset-0 w-full h-full bg-transparent border-none focus:ring-0 focus:outline-none text-[13px] md:text-[14.5px] font-medium pl-[36px] md:pl-[44px] pr-4 pt-[2px]" id="email" name="email" placeholder=" " autocomplete="off" required>
                        <label class="float-label absolute left-[36px] md:left-[44px] top-1/2 -translate-y-1/2 text-gray-400 text-[13px] md:text-[14.5px] pointer-events-none transition-all duration-300 origin-left">email@student.umi.ac.id</label>
                    </div>
                    <div id="emailError" class="text-red-500 text-xs w-full text-left -mt-2 mb-2 pl-1 empty:hidden animate-fade-up"></div>

                    <div class="float-group w-full mb-3 md:mb-4 flex items-center bg-white border-[1.5px] border-slate-200 hover:border-slate-300 rounded-xl px-3 md:px-4 h-[48px] md:h-[52px] transition-colors focus-within:border-primary focus-within:ring-4 focus-within:ring-primary/10">
                        <img src="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Img/idcard.svg" alt="ID" class="float-icon w-[18px] md:w-[22px] opacity-45 transition-all duration-300 z-10">
                        <input type="text" class="absolute inset-0 w-full h-full bg-transparent border-none focus:ring-0 focus:outline-none text-[13px] md:text-[14.5px] font-medium pl-[36px] md:pl-[44px] pr-4 pt-[2px]" id="stambukregister" name="stambuk" placeholder=" " autocomplete="off" required>
                        <label class="float-label absolute left-[36px] md:left-[44px] top-1/2 -translate-y-1/2 text-gray-400 text-[13px] md:text-[14.5px] pointer-events-none transition-all duration-300 origin-left">Stambuk</label>
                    </div>
                    <div id="stambukRegisterError" class="text-red-500 text-xs w-full text-left -mt-2 mb-2 pl-1 empty:hidden animate-fade-up"></div>

                    <div class="float-group w-full mb-3 md:mb-4 flex items-center bg-white border-[1.5px] border-slate-200 hover:border-slate-300 rounded-xl px-3 md:px-4 h-[48px] md:h-[52px] transition-colors focus-within:border-primary focus-within:ring-4 focus-within:ring-primary/10">
                        <img src="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Img/password.svg" alt="Password" class="float-icon w-[18px] md:w-[22px] opacity-45 transition-all duration-300 z-10">
                        <input type="password" class="absolute inset-0 w-full h-full bg-transparent border-none focus:ring-0 focus:outline-none text-[13px] md:text-[14.5px] font-medium pl-[36px] md:pl-[44px] pr-[40px] pt-[2px]" id="password" name="password" placeholder=" " autocomplete="off" required>
                        <label class="float-label absolute left-[36px] md:left-[44px] top-1/2 -translate-y-1/2 text-gray-400 text-[13px] md:text-[14.5px] pointer-events-none transition-all duration-300 origin-left">Password</label>
                        <span class="absolute right-2 top-1/2 -translate-y-1/2 cursor-pointer text-gray-400 flex items-center justify-center w-7 h-7 md:w-8 md:h-8 rounded-md hover:text-primary hover:bg-primary/10 transition-colors z-10" id="togglePassword">
                            <i class="bi bi-eye-slash pointer-events-none" id="toggleIcon"></i>
                        </span>
                    </div>
                    <div id="passwordError" class="text-red-500 text-xs w-full text-left -mt-2 mb-2 pl-1 empty:hidden animate-fade-up"></div>

                    <div class="float-group w-full mb-3 md:mb-4 flex items-center bg-white border-[1.5px] border-slate-200 hover:border-slate-300 rounded-xl px-3 md:px-4 h-[48px] md:h-[52px] transition-colors focus-within:border-primary focus-within:ring-4 focus-within:ring-primary/10">
                        <img src="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Img/password.svg" alt="Confirm" class="float-icon w-[18px] md:w-[22px] opacity-45 transition-all duration-300 z-10">
                        <input type="password" class="absolute inset-0 w-full h-full bg-transparent border-none focus:ring-0 focus:outline-none text-[13px] md:text-[14.5px] font-medium pl-[36px] md:pl-[44px] pr-[40px] pt-[2px]" id="confirmPass" name="konfirmasiPassword" placeholder=" " autocomplete="off" required>
                        <label class="float-label absolute left-[36px] md:left-[44px] top-1/2 -translate-y-1/2 text-gray-400 text-[13px] md:text-[14.5px] pointer-events-none transition-all duration-300 origin-left">Konfirmasi Password</label>
                        <span class="absolute right-2 top-1/2 -translate-y-1/2 cursor-pointer text-gray-400 flex items-center justify-center w-7 h-7 md:w-8 md:h-8 rounded-md hover:text-primary hover:bg-primary/10 transition-colors z-10" id="confirmPassword">
                            <i class="bi bi-eye-slash pointer-events-none" id="toggleIconConfirmation"></i>
                        </span>
                    </div>
                    <div id="confirmPassError" class="text-red-500 text-xs w-full text-left -mt-2 mb-2 pl-1 empty:hidden animate-fade-up"></div>

                    <button type="submit" class="w-full bg-gradient-to-br from-primary to-secondary rounded-xl py-2.5 md:py-3 text-[14px] md:text-[15px] font-semibold text-white tracking-wide hover:-translate-y-0.5 hover:shadow-lg active:translate-y-0 active:shadow-sm transition-all flex justify-center items-center gap-2 mt-2" name="register" id="btndaftar">
                        <i class="bi bi-person-plus"></i>Daftar
                    </button>
                </form>
            </div>
        </div>

        <!-- ===== DESKTOP TOGGLE PANEL ===== -->
        <div class="hidden md:block absolute top-0 left-1/2 w-1/2 h-full overflow-hidden transition-all duration-[850ms] ease-[cubic-bezier(0.65,0,0.35,1)] z-[1000] rounded-tl-[150px] rounded-bl-[100px] group-active:-translate-x-full group-active:rounded-tl-none group-active:rounded-bl-none group-active:rounded-tr-[150px] group-active:rounded-br-[100px]">
            <div class="bg-gradient-to-br from-primary to-secondary text-white relative -left-full h-full w-[200%] transition-transform duration-[850ms] ease-[cubic-bezier(0.65,0,0.35,1)] group-active:translate-x-1/2">
                <!-- Right Panel (Login Active) -->
                <div class="absolute right-0 w-1/2 h-full flex flex-col items-center justify-center px-8 text-center top-0 transition-transform duration-[850ms] ease-[cubic-bezier(0.65,0,0.35,1)] group-active:translate-x-[200%]">
                    <h1 class="text-3xl font-bold mb-4 text-white">Belum punya akun?</h1>
                    <p class="text-[15px] text-white/90 font-medium leading-relaxed mb-5">Silahkan daftar akun untuk melanjutkan proses IC-ASSIST</p>
                    <button class="bg-transparent text-white text-[13px] py-2.5 px-12 border border-white rounded-xl font-bold tracking-wide uppercase mt-4 transition-all hover:bg-white/10 hover:-translate-y-0.5" id="register" onclick="document.getElementById('container').classList.add('active')">Daftar</button>
                </div>
                <!-- Left Panel (Register Active) -->
                <div class="absolute -translate-x-[200%] w-1/2 h-full flex flex-col items-center justify-center px-8 text-center top-0 transition-transform duration-[850ms] ease-[cubic-bezier(0.65,0,0.35,1)] group-active:translate-x-0">
                    <h1 class="text-3xl font-bold mb-4 text-white">Sudah punya akun?</h1>
                    <p class="text-[15px] text-white/90 font-medium leading-relaxed mb-5">Silahkan login jika anda telah mempunyai akun IC-ASSIST</p>
                    <button class="bg-transparent text-white text-[13px] py-2.5 px-12 border border-white rounded-xl font-bold tracking-wide uppercase mt-4 transition-all hover:bg-white/10 hover:-translate-y-0.5" id="login" onclick="document.getElementById('container').classList.remove('active')">Masuk</button>
                </div>
            </div>
        </div>

    </div><!-- /.container -->


    <!-- Tailwind Custom Modal -->
    <div id="customModal" class="fixed inset-0 z-[1050] hidden items-center justify-center overflow-x-hidden overflow-y-auto outline-none focus:outline-none">
        <div class="fixed inset-0 bg-black/50 transition-opacity" id="customModalBackdrop"></div>
        <div class="relative w-auto my-6 mx-auto max-w-sm z-50">
            <div class="relative flex w-full flex-col rounded-[20px] border-0 bg-white shadow-2xl outline-none focus:outline-none">
                <div class="relative flex-auto p-8 text-center">
                    <img id="modalGif" src="" alt="Animation" class="mb-4 mx-auto w-[80px] md:w-[100px] rounded-xl" style="display: none;">
                    <p id="modalMessage" class="text-[1rem] md:text-[1.05rem] font-medium text-gray-800 mb-4">Pesan akan ditampilkan di sini.</p>
                    <button type="button" id="closeModal" class="bg-gradient-to-br from-primary to-secondary border-none rounded-[10px] px-7 py-2 text-white font-semibold shadow-sm hover:shadow-md transition-shadow">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Tailwind Modal Verifikasi OTP -->
    <div id="otpModal" class="fixed inset-0 z-[1050] hidden items-center justify-center overflow-x-hidden overflow-y-auto outline-none focus:outline-none">
        <div class="fixed inset-0 bg-black/50 transition-opacity"></div>
        <div class="relative w-auto my-6 mx-auto max-w-md z-50 px-4">
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
                            <input type="text" class="w-[45px] h-[50px] text-center text-2xl font-bold text-gray-900 bg-white border border-gray-300 rounded-[10px] focus:ring-primary focus:border-primary otp-input p-0" maxlength="1" pattern="[0-9]" inputmode="numeric" required>
                            <input type="text" class="w-[45px] h-[50px] text-center text-2xl font-bold text-gray-900 bg-white border border-gray-300 rounded-[10px] focus:ring-primary focus:border-primary otp-input p-0" maxlength="1" pattern="[0-9]" inputmode="numeric" required>
                            <input type="text" class="w-[45px] h-[50px] text-center text-2xl font-bold text-gray-900 bg-white border border-gray-300 rounded-[10px] focus:ring-primary focus:border-primary otp-input p-0" maxlength="1" pattern="[0-9]" inputmode="numeric" required>
                            <input type="text" class="w-[45px] h-[50px] text-center text-2xl font-bold text-gray-900 bg-white border border-gray-300 rounded-[10px] focus:ring-primary focus:border-primary otp-input p-0" maxlength="1" pattern="[0-9]" inputmode="numeric" required>
                            <input type="text" class="w-[45px] h-[50px] text-center text-2xl font-bold text-gray-900 bg-white border border-gray-300 rounded-[10px] focus:ring-primary focus:border-primary otp-input p-0" maxlength="1" pattern="[0-9]" inputmode="numeric" required>
                            <input type="text" class="w-[45px] h-[50px] text-center text-2xl font-bold text-gray-900 bg-white border border-gray-300 rounded-[10px] focus:ring-primary focus:border-primary otp-input p-0" maxlength="1" pattern="[0-9]" inputmode="numeric" required>
                        </div>
                        <input type="hidden" id="otpCode" name="otp">
                        
                        <button type="submit" class="w-full text-white bg-gradient-to-br from-primary to-secondary border-none focus:ring-4 focus:outline-none focus:ring-primary/30 font-semibold rounded-xl text-sm px-5 py-3 text-center shadow-sm hover:-translate-y-0.5 hover:shadow-lg active:translate-y-0 active:shadow-sm transition-all" id="btnVerifyOtp">
                            Verifikasi Akun
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
    <script>
        // Attach mobile buttons for toggle manually since ID changed
        document.getElementById('register-mobile')?.addEventListener('click', () => {
            document.getElementById('container').classList.add('active');
        });
        document.getElementById('login-mobile')?.addEventListener('click', () => {
            document.getElementById('container').classList.remove('active');
        });
    </script>
</body>

</html>


