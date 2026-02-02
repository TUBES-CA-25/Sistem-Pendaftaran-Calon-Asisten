<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - Sistem Pendaftaran</title>
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

        .forgot-password-container {
            width: 100%;
            max-width: 420px;
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

        .forgot-password-card {
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

        .btn-submit:hover {
            background: linear-gradient(135deg, #0087c0 0%, #00a3c7 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 151, 217, 0.3);
        }

        .btn-submit:active {
            transform: translateY(0);
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
            display: block;
            background: #d4edda;
            color: #155724;
        }

        .alert-error {
            display: block;
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

        .reset-link-section {
            display: none;
            background: #f0f7ff;
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
            border-left: 4px solid #0097d9;
        }

        .reset-link-section.active {
            display: block;
        }

        .reset-link-section h6 {
            color: #0097d9;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .reset-link-section p {
            color: #666;
            font-size: 13px;
            margin: 5px 0;
            word-break: break-all;
        }

        .copy-btn {
            background: #0097d9;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            cursor: pointer;
            margin-top: 10px;
            transition: all 0.3s ease;
        }

        .copy-btn:hover {
            background: #0087c0;
        }
    </style>
</head>
<body>
    <div class="animated-bg"></div>

    <div class="forgot-password-container">
        <div class="forgot-password-card">
            <div class="card-header-section">
                <h1><i class="bi bi-lock"></i> Lupa Password</h1>
                <p>Masukkan email yang anda gunakan saat mendaftar untuk melanjutkan</p>
            </div>

            <div id="alertMessage" class="alert"></div>

            <form id="forgotPasswordForm">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input 
                        type="email" 
                        class="form-control" 
                        id="email" 
                        name="email" 
                        placeholder="Masukkan email Anda"
                        required
                    >
                </div>

                <button type="submit" class="btn-submit">
                    <span class="loading spinner-border"></span>
                    <span class="btn-text">Kirim Reset Link ke Email</span>
                </button>
            </form>

            <div class="reset-link-section"></div>

            <div class="back-link">
                <!-- Gunakan path absolute atau helper -->
                <a href="/Sistem-Pendaftaran-Calon-Asisten/public/login"><i class="bi bi-arrow-left"></i> Kembali ke Login</a>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.8/js/bootstrap.bundle.min.js"></script>
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
