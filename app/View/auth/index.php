<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Img/iclabs.png">

    <!-- Bootstrap 5.3.8 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Login Animation Styles -->
    <link rel="stylesheet" href="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/css/login-animation.css">

    <!-- Auth Custom Styles -->
    <link rel="stylesheet" href="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/css/auth-custom.css">

    <title>Pendaftaran Calon Asisten ICLABS</title>
</head>

<body>
    <div class="container" id="container">
        <div class="form-container sign-in">
            <form id="loginForm">
                <div class="logo-section">
                    <img src="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Img/umi.png" alt="UMI Logo">
                    <img src="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Img/fikom.png" alt="FIKOM Logo">
                    <img src="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Img/iclabs.png" alt="ICLABS Logo">
                </div>

                <h1 class="text-center mb-4">Masuk</h1>

                <div class="input-wrapper">
                    <img src="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Img/idcard.svg" alt="ID" class="input-icon">
                    <input type="text" class="form-control" id="stambuk" name="stambuk" placeholder="Stambuk" required>
                </div>

                <div class="input-wrapper">
                    <img src="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Img/password.svg" alt="Password" class="input-icon">
                    <input type="password" class="form-control" id="passwordLogin" name="password" placeholder="Password" required>
                    <span class="toggle-password" id="loginIconPass">
                        <i class="bi bi-eye-slash" id="togglePassLogin"></i>
                    </span>
                </div>

                <div class="helper-links">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="customCheck" name="check">
                        <label class="form-check-label" for="customCheck">Remember me</label>
                    </div>
                    <a href="lupapasword">Lupa password?</a>
                </div>

                <button type="submit" class="btn btn-primary btn-submit w-100" name="login" id="btnlogin">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Login
                </button>
                
                <div class="d-block d-md-none mt-3 text-center">
                    <p class="small text-muted mb-0">Belum punya akun? <a href="#" id="mobile-register-btn" class="text-primary fw-semibold text-decoration-none">Daftar</a></p>
                </div>
            </form>
        </div>
        <div class="form-container sign-up">
            <form id="registerForm">
                <h1 class="text-center mb-4">Buat Akun</h1>

                <div class="input-wrapper">
                    <img src="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Img/profile.svg" alt="Email" class="input-icon">
                    <input type="email" class="form-control" id="email" name="email" placeholder="email@umi.ac.id" required>
                </div>
                <div id="emailError" class="error-msg"></div>

                <div class="input-wrapper">
                    <img src="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Img/idcard.svg" alt="ID" class="input-icon">
                    <input type="text" class="form-control" id="stambukregister" name="stambuk" placeholder="Stambuk" required>
                </div>

                <div class="input-wrapper">
                    <img src="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Img/password.svg" alt="Password" class="input-icon">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                    <span class="toggle-password" id="togglePassword">
                        <i class="bi bi-eye-slash" id="toggleIcon"></i>
                    </span>
                </div>
                <div id="passwordError" class="error-msg"></div>

                <div class="input-wrapper">
                    <img src="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Img/password.svg" alt="Confirm" class="input-icon">
                    <input type="password" class="form-control" id="confirmPass" name="konfirmasiPassword" placeholder="Konfirmasi Password" required>
                    <span class="toggle-password" id="confirmPassword">
                        <i class="bi bi-eye-slash" id="toggleIconConfirmation"></i>
                    </span>
                </div>

                <button type="submit" class="btn btn-primary btn-submit w-100 mt-3" name="register" id="btndaftar">
                    <i class="bi bi-person-plus me-2"></i>Daftar
                </button>
                
                <div class="d-block d-md-none mt-3 text-center">
                    <p class="small text-muted mb-0">Sudah punya akun? <a href="#" id="mobile-login-btn" class="text-primary fw-semibold text-decoration-none">Masuk</a></p>
                </div>
            </form>
        </div>
        <div class="toggle-container">
            <div class="toggle">
                <div class="toggle-panel toggle-right">
                    <h1>Belum punya akun?</h1>
                    <p>Silahkan daftar akun untuk melanjutkan proses IC-ASSIST</p>

                    <button class="hidden" id="register">Daftar</button>
                </div>
                <div class="toggle-panel toggle-left">
                    <h1>Sudah punya akun?</h1>

                    <p>Silahkan login jika anda telah mempunyai akun IC-ASSIST</p>
                    <button class="hidden" id="login">Masuk</button>
                </div>
            </div>
        </div>
    </div>
    </div>

    <!-- Bootstrap Modal -->
    <div class="modal fade" id="customModal" tabindex="-1" aria-labelledby="customModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-4">
                <div class="modal-body text-center p-4">
                    <!-- GIF Animasi -->
                    <img id="modalGif" src="" alt="Animation" class="mb-3 mx-auto" style="width: 100px; display: none;">

                    <!-- Pesan Custom -->
                    <p id="modalMessage" class="fs-5 mb-3">Pesan akan ditampilkan di sini.</p>

                    <!-- Tombol Close -->
                    <button type="button" id="closeModal" class="btn btn-primary rounded-3 px-4" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
        integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Load common utilities -->
    <script src="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/js/ScriptLogin.js?v=<?= time() ?>"></script>


</body>

</html>