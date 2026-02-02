<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Sistem Pendaftaran</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.8/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #0097d9 0%, #00b4d8 50%, #17a2a2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        .animated-bg {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, #0097d9 0%, #00b4d8 50%, #17a2a2 100%);
            animation: gradientShift 15s ease infinite;
            z-index: -1;
        }

        @keyframes gradientShift {
            0% {
                background: linear-gradient(135deg, #0097d9 0%, #00b4d8 50%, #17a2a2 100%);
            }
            50% {
                background: linear-gradient(135deg, #00b4d8 0%, #17a2a2 50%, #0097d9 100%);
            }
            100% {
                background: linear-gradient(135deg, #0097d9 0%, #00b4d8 50%, #17a2a2 100%);
            }
        }

        .reset-password-container {
            width: 100%;
            max-width: 450px;
            padding: 20px;
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .reset-password-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 40px 30px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .card-header-section {
            text-align: center;
            margin-bottom: 35px;
        }

        .card-header-section h1 {
            color: #0097d9;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .card-header-section p {
            color: #666;
            font-size: 14px;
            margin: 0;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            color: #333;
            font-weight: 500;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .input-group {
            position: relative;
        }

        .form-control {
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            padding: 12px 15px;
            font-size: 14px;
            background: #fafafa;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #0097d9;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(0, 151, 217, 0.1);
        }

        .toggle-password {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #999;
            cursor: pointer;
            font-size: 18px;
            transition: all 0.3s ease;
        }

        .toggle-password:hover {
            color: #0097d9;
        }

        .password-requirements {
            background: #f5f5f5;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            border-left: 4px solid #ffc107;
        }

        .password-requirements h6 {
            color: #333;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .requirement {
            font-size: 12px;
            color: #666;
            margin: 6px 0;
            display: flex;
            align-items: center;
        }

        .requirement i {
            margin-right: 8px;
            width: 16px;
            color: #ccc;
        }

        .requirement.met i {
            color: #28a745;
        }

        .btn-submit {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #0097d9 0%, #00b4d8 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }

        .btn-submit:hover:not(:disabled) {
            background: linear-gradient(135deg, #0087c0 0%, #00a3c7 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 151, 217, 0.3);
        }

        .btn-submit:active:not(:disabled) {
            transform: translateY(0);
        }

        .btn-submit:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .back-link {
            text-align: center;
            margin-top: 20px;
        }

        .back-link a {
            color: #0097d9;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .back-link a:hover {
            color: #00b4d8;
            text-decoration: underline;
        }

        .alert {
            display: none;
            border-radius: 10px;
            margin-bottom: 20px;
            border: none;
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
        }

        .loading {
            display: none;
        }

        .loading.active {
            display: inline-block;
        }

        .spinner-border {
            width: 16px;
            height: 16px;
            border-width: 2px;
            margin-right: 8px;
        }
    </style>
</head>
<body>
    <div class="animated-bg"></div>

    <div class="reset-password-container">
        <div class="reset-password-card">
            <div class="card-header-section">
                <h1><i class="bi bi-shield-check"></i> Reset Password</h1>
                <p>Buat password baru Anda</p>
            </div>

            <div id="alertMessage" class="alert"></div>

            <div class="password-requirements">
                <h6>Persyaratan Password:</h6>
                <div class="requirement" id="req-length">
                    <i class="bi bi-check-circle"></i>
                    Minimal 6 karakter
                </div>
                <div class="requirement" id="req-match">
                    <i class="bi bi-check-circle"></i>
                    Password cocok dengan konfirmasi
                </div>
            </div>

            <form id="resetPasswordForm">
                <input type="hidden" name="token" value="<?= htmlspecialchars($_GET['token'] ?? '') ?>">
                
                <div class="form-group">
                    <label for="newPassword">Password Baru</label>
                    <div class="input-group">
                        <input 
                            type="password" 
                            class="form-control" 
                            id="newPassword" 
                            name="newPassword" 
                            placeholder="Masukkan password baru"
                            required
                        >
                        <button 
                            type="button" 
                            class="toggle-password" 
                            id="togglePassword1"
                            title="Tampilkan password"
                        >
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="form-group">
                    <label for="confirmPassword">Konfirmasi Password</label>
                    <div class="input-group">
                        <input 
                            type="password" 
                            class="form-control" 
                            id="confirmPassword" 
                            name="confirmPassword" 
                            placeholder="Konfirmasi password baru"
                            required
                        >
                        <button 
                            type="button" 
                            class="toggle-password" 
                            id="togglePassword2"
                            title="Tampilkan password"
                        >
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-submit" id="submitBtn">
                    <span class="loading spinner-border"></span>
                    <span class="btn-text">Ubah Password</span>
                </button>
            </form>

            <div class="back-link">
                <!-- Gunakan absolute path atau helper jika APP_URL tersedia, fallback ke /public/login -->
                <a href="/Sistem-Pendaftaran-Calon-Asisten/public/login"><i class="bi bi-arrow-left"></i> Kembali ke Login</a>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.8/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle password visibility
        document.getElementById('togglePassword1').addEventListener('click', function() {
            const input = document.getElementById('newPassword');
            const icon = this.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        });

        document.getElementById('togglePassword2').addEventListener('click', function() {
            const input = document.getElementById('confirmPassword');
            const icon = this.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        });

        // Validate password requirements
        const newPassword = document.getElementById('newPassword');
        const confirmPassword = document.getElementById('confirmPassword');
        const submitBtn = document.getElementById('submitBtn');

        function validatePassword() {
            const password = newPassword.value;
            const confirm = confirmPassword.value;

            // Check length requirement
            const lengthMet = password.length >= 6;
            const lengthReq = document.getElementById('req-length');
            if (lengthMet) {
                lengthReq.classList.add('met');
                lengthReq.querySelector('i').className = 'bi bi-check-circle-fill';
            } else {
                lengthReq.classList.remove('met');
                lengthReq.querySelector('i').className = 'bi bi-check-circle';
            }

            // Check match requirement
            const matchMet = password === confirm && password.length > 0;
            const matchReq = document.getElementById('req-match');
            if (matchMet) {
                matchReq.classList.add('met');
                matchReq.querySelector('i').className = 'bi bi-check-circle-fill';
            } else {
                matchReq.classList.remove('met');
                matchReq.querySelector('i').className = 'bi bi-check-circle';
            }

            // Enable/disable submit button
            submitBtn.disabled = !(lengthMet && matchMet);
        }

        newPassword.addEventListener('input', validatePassword);
        confirmPassword.addEventListener('input', validatePassword);

        // Form submission
        document.getElementById('resetPasswordForm').addEventListener('submit', async (e) => {
            e.preventDefault();

            const newPassword = document.getElementById('newPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            const alertDiv = document.getElementById('alertMessage');
            const loading = document.querySelector('.loading');
            const btnText = document.querySelector('.btn-text');

            // Reset alert
            alertDiv.className = 'alert';
            alertDiv.textContent = '';
            alertDiv.style.display = 'none';

            // Show loading
            loading.classList.add('active');
            btnText.textContent = 'Memproses...';
            submitBtn.disabled = true;

            // Get token directly from the hidden input we added
            const token = document.querySelector('input[name="token"]').value;

            try {
                // Ensure endpoint matches your routes
                const response = await fetch('/Sistem-Pendaftaran-Calon-Asisten/public/reset-password/update', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    // Keys must match Controller expectations: password, confirm_password, token
                    body: `password=${encodeURIComponent(newPassword)}&confirm_password=${encodeURIComponent(confirmPassword)}&token=${encodeURIComponent(token)}`
                });

                const data = await response.json();

                if (data.status === 'success') {
                    alertDiv.className = 'alert alert-success';
                    alertDiv.textContent = data.message;
                    alertDiv.style.display = 'block';

                    setTimeout(() => {
                        window.location.href = '/Sistem-Pendaftaran-Calon-Asisten/public/login';
                    }, 1500);
                } else {
                    alertDiv.className = 'alert alert-error';
                    alertDiv.textContent = data.message || 'Terjadi kesalahan. Silahkan coba lagi.';
                    alertDiv.style.display = 'block';
                    submitBtn.disabled = false;
                }
            } catch (error) {
                console.error('Error:', error);
                alertDiv.className = 'alert alert-error';
                alertDiv.textContent = 'Terjadi kesalahan jaringan atau server.';
                alertDiv.style.display = 'block';
                submitBtn.disabled = false;
            } finally {
                loading.classList.remove('active');
                if (!submitBtn.disabled) { 
                    btnText.textContent = 'Ubah Password';
                }
            }
        });

        // Initial validation
        validatePassword();
    </script>
</body>
</html>
