<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Style/alertstyle.css">
    <link rel="stylesheet" href="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Style/Styles1.css">
    <link rel="icon" href="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Img/iclabs.png">

    <title>Pendaftaran calon asisten ICLABS</title>
    <style>
        p {
            font-family: 'Poppins', sans-serif;
            font-weight: 500;
            font-size: 16px;
            line-height: 1.5;
        }

        h1 {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
        }
    </style>
</head>

<body>
    <div class="container" id="container">
        <div class="form-container sign-in">
            <form id="loginForm">
                <div>
                    <div style="margin-bottom: 20px; text-align: center; position: relative; top: -10px;">
                        <img src="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Img/umi.png" alt="logo fikom" class="logo"
                            style="margin-right: 10px; width: 80px;">
                        <img src="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Img/fikom.png" alt="" class="logo"
                            style="margin-right: 10px; width: 80px;">
                        <img src="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Img/iclabs.png" alt="" class="logo" style="width: 80px;">
                    </div>
                    <div style="margin-bottom : 50px">
                        <h1 style="text-align: center;">Masuk</h1>
                    </div>
                    <div>
                        <div style="position: relative; width: 100%; margin-bottom: 15px;">
                            <img src="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Img/idcard.svg" alt="ID Card Icon"
                                style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); width: 20px; height: 20px;">
                            <input type="text" class="form-control" id="stambuk" name="stambuk" placeholder="Stambuk"
                                style="padding-left: 40px; width: 100%; height: 45px; font-size: 16px; border-radius: 5px; border: 1px solid #ccc;"
                                required>
                        </div>

                        <div style="position: relative; width: 100%; margin-bottom: 15px;">
                            <img src="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Img/password.svg" alt="Lock Icon"
                                style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); width: 20px; height: 20px;">
                            <input type="password" id="passwordLogin" name="password" placeholder="Password"
                                style="padding-left: 40px; width: 100%; height: 45px; font-size: 16px; border-radius: 5px; border: 1px solid #ccc;"
                                required>
                            <span
                                style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer;"
                                id="loginIconPass">
                                <i class="bi bi-eye-slash" id="togglePassLogin"></i>
                            </span>
                        </div>
                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" name="check" class="custom-control-input" id="customCheck">
                                <label for="customCheck" class="custom-control-label"> Remember me</label>
                            </div>
                            <div>
                                <p><a href="lupapasword" style="color : black;">Lupa password ?</a></p>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary" name="login" id="btnlogin">Login</button>
                    </div>
                </div>
            </form>
        </div>
        <div class="form-container sign-up">
            <form id="registerForm">
                <div>
                    <h1 style="text-align: center;">Buat Akun</h1>
                    <div style="width: 100%; max-width: 400px; margin: auto; padding: 20px;">

                        <div style="position: relative; width: 100%; margin-bottom: 15px;">
                            <img src="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Img/profile.svg" alt="Profile Icon"
                                style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); width: 20px; height: 20px;">
                            <input type="text" id="email" name="email" placeholder="email@umi.ac.id"
                                style="padding-left: 40px; width: 100%; height: 45px; font-size: 16px; border-radius: 5px; border: 1px solid #ccc;"
                                required>
                            <div id="emailError" style="color: red; margin-top: 5px;"></div>
                        </div>

                        <div style="position: relative; width: 100%; margin-bottom: 15px;">
                            <img src="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Img/idcard.svg" alt="ID Card Icon"
                                style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); width: 20px; height: 20px;">
                            <input type="text" class="form-control" id="stambukregister" name="stambuk"
                                placeholder="Stambuk"
                                style="padding-left: 40px; width: 100%; height: 45px; font-size: 16px; border-radius: 5px; border: 1px solid #ccc;"
                                required>
                        </div>

                        <div style="position: relative; width: 100%; margin-bottom: 15px;">
                            <img src="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Img/password.svg" alt="Lock Icon"
                                style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); width: 20px; height: 20px;">
                            <input type="password" id="password" name="password" placeholder="Password"
                                style="padding-left: 40px; width: 100%; height: 45px; font-size: 16px; border-radius: 5px; border: 1px solid #ccc;"
                                required>
                            <div id="passwordError" style="color: red; margin-top: 5px;"></div>
                            <span
                                style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer;"
                                id="togglePassword">
                                <i class="bi bi-eye-slash" id="toggleIcon"></i>
                            </span>
                        </div>

                        <div style="position: relative; width: 100%; margin-bottom: 15px;">
                            <img src="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Img/password.svg" alt="Key Icon"
                                style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); width: 20px; height: 20px;">
                            <input type="password" id="confirmPass" name="konfirmasiPassword"
                                placeholder="Konfirmasi Password"
                                style="padding-left: 40px; width: 100%; height: 45px; font-size: 16px; border-radius: 5px; border: 1px solid #ccc;"
                                required>
                            <div id="passwordError" style="color: red; margin-top: 5px;"></div>
                            <span
                                style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer;"
                                id="confirmPassword">
                                <i class="bi bi-eye-slash" id="toggleIconConfirmation"></i>
                            </span>
                        </div>

                    </div>
                    <button type="submit" class="btn btn-primary" name="register" id="btndaftar">Daftar</button>
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

    <div id="customModal" class="alertmodal">
    <div class="modal-content">
        <!-- GIF Animasi -->
        <img id="modalGif" src="" alt="Animation" style="width: 100px; margin-bottom: 15px; display: none;">
        
        <!-- Pesan Custom -->
        <p id="modalMessage" style="margin: 10px 0; font-size: 18px;">Pesan akan ditampilkan di sini.</p>
        
        <!-- Tombol Close -->
        <button id="closeModal" class="btn btn-primary" style="margin-top: 10px;">Tutup</button>
    </div>
</div>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
        integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <script src="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Script/Login/ScriptLogin.js"></script>


</body>

</html>