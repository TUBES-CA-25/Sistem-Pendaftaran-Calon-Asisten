const passwordInputLogin = document.getElementById('passwordLogin');
const registerBtn = document.getElementById("register");
const container = document.getElementById("container");
const loginBtn = document.getElementById("login");
const togglePassword = document.querySelector("#togglePassword");
const password = document.querySelector("#password");
const passwordconfirm = document.querySelector("#confirmPass");
const toggleIcon = document.querySelector("#toggleIcon");
const confirmPassword = document.querySelector("#confirmPassword");
const toggleIconConfirmation = document.querySelector(
  "#toggleIconConfirmation"
);
const loginIconPass = document.querySelector("#loginIconPass");
const togglePassLogin = document.querySelector("#togglePassLogin");
const passwordLogin = document.querySelector("#passwordLogin");

const emailinput = document.getElementById('email');
const passwordinput = document.getElementById('password');
const stambukInput = document.getElementById('stambukregister');

// ── Modal Helper (Tailwind Implementation) ──────────────────
function showModalUI(modalId) {
    const modal = document.getElementById(modalId);
    if(modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
}
function hideModalUI(modalId) {
    const modal = document.getElementById(modalId);
    if(modal) {
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }
}

// Define showModal function for login page (global scope)
window.showModal = function(message, gifUrl = null, onCloseCallback = null) {
    const modalEl = document.getElementById("customModal");
    if (!modalEl) {
        alert(message);
        if (onCloseCallback) onCloseCallback();
        return;
    }

    const modalMessage = document.getElementById("modalMessage");
    const modalGif = document.getElementById("modalGif");

    if (modalMessage) modalMessage.textContent = message;
    
    if (modalGif) {
        if (gifUrl) {
            modalGif.src = gifUrl;
            modalGif.style.display = "block";
        } else {
            modalGif.style.display = "none";
        }
    }

    showModalUI("customModal");

    const closeBtn = document.getElementById("closeModal");
    if (closeBtn) {
        const handler = function() {
            hideModalUI("customModal");
            closeBtn.removeEventListener('click', handler);
            if (onCloseCallback) onCloseCallback();
        };
        closeBtn.addEventListener('click', handler);
    }
};

// Close modals when clicking outside
window.addEventListener('click', function(e) {
    if (e.target.id === 'customModal') {
        document.getElementById('closeModal')?.click();
    }
});
document.getElementById('closeOtpModal')?.addEventListener('click', function() {
    hideModalUI("otpModal");
});

function validateStambuk(stambuk) {
  if (stambuk.length !== 11) {
    return {
      success: false,
      message: "Stambuk/NIM harus memiliki panjang 11 karakter.",
    };
  }

  const prefix = stambuk.substring(0, 3);
  if (prefix !== '130' && prefix !== '131') {
    return {
      success: false,
      message: "Stambuk harus diawali dengan 130 atau 131.",
    };
  }

  const yearStr = stambuk.substring(3, 7);
  const yearOfNim = parseInt(yearStr, 10);
  const currentYear = new Date().getFullYear();
  const minYear = currentYear - 3;
  const maxYear = currentYear;

  if (isNaN(yearOfNim) || yearOfNim < minYear || yearOfNim > maxYear) {
    return {
      success: false,
      message: `Pendaftaran hanya terbuka untuk mahasiswa angkatan ${minYear} sampai ${maxYear}.`,
    };
  }

  const suffix = stambuk.substring(7);
  const suffixRegex = /^[0-9]{4}$/;
  if (!suffixRegex.test(suffix)) {
    return {
      success: false,
      message: "Format Stambuk/NIM tidak valid pada 4 digit terakhir.",
    };
  }

  return { success: true, message: "Stambuk valid." };
}

function validateEmail(email) {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

  if (!emailRegex.test(email)) {
    return { success: false, message: "Format email tidak valid." };
  }
  const domain = email.split("@")[1];

  if (domain !== "student.umi.ac.id") {
    return { success: false, message: "Email harus menggunakan domain student.umi.ac.id." };
  }
  return { success: true, message: "Email Valid" };
}

function validatePassword(password, confirmPassword) {
  const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z]).{8,}$/;

  if (!passwordRegex.test(password)) {
    return {
      success: false,
      message:
        "Password harus mengandung huruf besar, huruf kecil, dan minimal 8 karakter.",
    };
  }
  if (password !== confirmPassword) {
    return { success: false, message: "Password tidak sama." };
  }
  return { success: true, message: "Password Valid" };
}

function validatePasswordLogin(password) {
  const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z]).{8,}$/;

  if (!passwordRegex.test(password)) {
    return {
      success: false,
      message:
        "Password harus mengandung huruf besar, huruf kecil, dan minimal 8 karakter.",
    };
  }
  return { success: true, message: "Password Valid" };

}


// ── Toggle Login / Register ───────────────────────────────────
registerBtn.addEventListener('click', () => container.classList.add('active'));
loginBtn.addEventListener('click',    () => container.classList.remove('active'));

const mobileRegisterBtn = document.getElementById('mobile-register-btn');
const mobileLoginBtn    = document.getElementById('mobile-login-btn');

if (mobileRegisterBtn) {
    mobileRegisterBtn.addEventListener('click', (e) => {
        e.preventDefault();
        container.classList.add('active');
    });
}

if (mobileLoginBtn) {
    mobileLoginBtn.addEventListener('click', (e) => {
        e.preventDefault();
        container.classList.remove('active');
    });
}


togglePassword.addEventListener("click", function () {
  const type =
    password.getAttribute("type") === "password" ? "text" : "password";
  password.setAttribute("type", type);

  toggleIcon.classList.toggle("bi-eye");
  toggleIcon.classList.toggle("bi-eye-slash");
});

confirmPassword.addEventListener("click", function () {
  const type =
    passwordconfirm.getAttribute("type") === "password" ? "text" : "password";
  passwordconfirm.setAttribute("type", type);

  toggleIconConfirmation.classList.toggle("bi-eye");
  toggleIconConfirmation.classList.toggle("bi-eye-slash");
});

loginIconPass.addEventListener("click", () => {
  const type =
    passwordLogin.getAttribute("type") === "password" ? "text" : "password";
  passwordLogin.setAttribute("type", type);

  togglePassLogin.classList.toggle("bi-eye");
  togglePassLogin.classList.toggle("bi-eye-slash");
});


//ajax

passwordInputLogin.addEventListener('input', function () {
    passwordInputLogin.setCustomValidity(''); 
    passwordInputLogin.reportValidity(); 
});
emailinput.addEventListener('input', function () {
    emailinput.setCustomValidity(''); // Bersihkan error
    emailinput.reportValidity(); // Perbarui tampilan error
});

stambukInput.addEventListener('input', function () {
    stambukInput.setCustomValidity(''); 
    stambukInput.reportValidity(); 
});

// Validasi Real-Time untuk Password
passwordinput.addEventListener('input', function () {
    passwordinput.setCustomValidity(''); 
    passwordinput.reportValidity(); 
});

const confirmPasswordInput = document.getElementById('confirmPass');
confirmPasswordInput.addEventListener('input', function () {
    confirmPasswordInput.setCustomValidity(''); // Bersihkan error
    confirmPasswordInput.reportValidity(); // Perbarui tampilan error
});


// ── Vanilla JS DOMContentLoaded block ────────────────────────
document.addEventListener('DOMContentLoaded', function () {
    // Kosongkan form saat halaman dimuat (mencegah browser menyimpan input lama)
    {
        const lForm = document.getElementById('loginForm');
        if (lForm) lForm.reset();
        const rForm = document.getElementById('registerForm');
        if (rForm) rForm.reset();
    }

    let expiryTimer  = null;
    let cooldownTimer = null;

    // ── OTP Expiry Countdown ──────────────────────────────────
    function startOtpExpiryCountdown(durationSeconds) {
        if (expiryTimer) clearInterval(expiryTimer);

        const timerSpan = document.getElementById('otpExpiryTimer');
        const btnVerify = document.getElementById('btnVerifyOtp');
        const otpInputs = document.querySelectorAll('.otp-input');

        if (btnVerify) btnVerify.disabled = false;
        otpInputs.forEach(inp => inp.disabled = false);

        let timeLeft = durationSeconds;

        function updateDisplay() {
            if (!timerSpan) return;
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            timerSpan.textContent = `${String(minutes).padStart(2,'0')}:${String(seconds).padStart(2,'0')}`;
        }
        updateDisplay();

        expiryTimer = setInterval(function () {
            timeLeft--;
            if (timeLeft <= 0) {
                clearInterval(expiryTimer);
                if (timerSpan) timerSpan.textContent = 'Waktu habis! Silakan kirim ulang OTP.';
                if (btnVerify) btnVerify.disabled = true;
                otpInputs.forEach(inp => inp.disabled = true);
            } else {
                updateDisplay();
            }
        }, 1000);
    }

    // ── Register Form ─────────────────────────────────────────
    var registerForm = document.getElementById('registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', function (e) {
            console.log('Form submit initiated');
            e.preventDefault();

            const email           = document.getElementById('email').value;
            const password        = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirmPass').value;
            const stambuk         = document.getElementById('stambukregister').value;

            console.log('Values captured:', { email, password, confirmPassword, stambuk });

            const stambukResult   = validateStambuk(stambuk);
            const emailResult     = validateEmail(email);
            const passwordResult  = validatePassword(password, confirmPassword);

            console.log('Validation results:', { stambukResult, emailResult, passwordResult });

            let isValid = true;
            if (!emailResult.success)   { emailinput.setCustomValidity(emailResult.message);   emailinput.reportValidity();   isValid = false; }
            if (!stambukResult.success) { stambukInput.setCustomValidity(stambukResult.message); stambukInput.reportValidity(); isValid = false; }
            if (!passwordResult.success){ passwordinput.setCustomValidity(passwordResult.message); passwordinput.reportValidity(); isValid = false; }
            if (!isValid) { console.log('Form validation failed, exiting'); return; }

            console.log('Form validation passed, submitting...');
            const btnRegister = registerForm.querySelector('button[type="submit"]');
            const originalBtnText = btnRegister ? btnRegister.innerHTML : '';
            if (btnRegister) { btnRegister.disabled = true; btnRegister.innerHTML = '<span class="spinner-border spinner-border-sm mr-2"></span>Loading...'; }

            const formData = new FormData(registerForm);
            fetch('/Sistem-Pendaftaran-Calon-Asisten/public/register/authenticate', {
                method: 'POST',
                body: new URLSearchParams(formData)
            })
            .then(res => res.json())
            .then(response => {
                if (btnRegister) { btnRegister.disabled = false; btnRegister.innerHTML = originalBtnText; }
                if (response.status === 'success') {
                    showModal('Register Berhasil', '/Sistem-Pendaftaran-Calon-Asisten/public/Assets/gif/registergif.gif');
                    document.getElementById('login').click();
                } else if (response.status === 'otp_required') {
                    var emailSpan = document.getElementById('otpEmailSpan');
                    if (emailSpan) emailSpan.textContent = response.email;
                    document.querySelectorAll('.otp-input').forEach(inp => inp.value = '');
                    var otpCodeEl = document.getElementById('otpCode');
                    if (otpCodeEl) otpCodeEl.value = '';

                    showModalUI('otpModal');
                    startOtpExpiryCountdown(300);

                    setTimeout(() => {
                        var first = document.querySelector('.otp-input');
                        if (first) first.focus();
                    }, 500);
                } else {
                    showModal(response.message || 'Register Gagal', '/Sistem-Pendaftaran-Calon-Asisten/public/Assets/gif/failedregistergif.gif');
                }
            })
            .catch(error => {
                if (btnRegister) { btnRegister.disabled = false; btnRegister.innerHTML = originalBtnText; }
                console.log('Terjadi kesalahan: ' + error);
                showModal('Terjadi kesalahan koneksi server', '/Sistem-Pendaftaran-Calon-Asisten/public/Assets/gif/failedregistergif.gif');
            });
        });
    }

    // ── OTP Autofocus & Key Handling ──────────────────────────
    var otpInputs = Array.from(document.querySelectorAll('.otp-input'));
    otpInputs.forEach(function (inp, index) {
        inp.addEventListener('keyup', function (e) {
            const val = this.value;
            if (val && /^[0-9]$/.test(val)) {
                if (index < otpInputs.length - 1) otpInputs[index + 1].focus();
            }
        });
        inp.addEventListener('keydown', function (e) {
            if (e.key === 'Backspace' && !this.value && index > 0) {
                otpInputs[index - 1].focus();
            }
        });
        inp.addEventListener('input', function () {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    });

    // ── OTP Submission ────────────────────────────────────────
    var otpForm = document.getElementById('otpForm');
    if (otpForm) {
        otpForm.addEventListener('submit', function (e) {
            e.preventDefault();

            var combinedOtp = otpInputs.map(inp => inp.value).join('');
            var otpCodeEl = document.getElementById('otpCode');
            if (otpCodeEl) otpCodeEl.value = combinedOtp;

            const btnVerify = document.getElementById('btnVerifyOtp');
            const originalText = btnVerify ? btnVerify.innerHTML : '';
            if (btnVerify) { btnVerify.disabled = true; btnVerify.innerHTML = '<span class="spinner-border spinner-border-sm mr-2"></span>Memverifikasi...'; }

            fetch('/Sistem-Pendaftaran-Calon-Asisten/public/register/verify-otp', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'otp=' + encodeURIComponent(combinedOtp)
            })
            .then(res => res.json())
            .then(response => {
                if (btnVerify) { btnVerify.disabled = false; btnVerify.innerHTML = originalText; }
                if (response.status === 'success') {
                    hideModalUI('otpModal');
                    showModal('Registrasi Berhasil', '/Sistem-Pendaftaran-Calon-Asisten/public/Assets/gif/registergif.gif');
                    document.getElementById('login').click();
                } else {
                    hideModalUI('otpModal');
                    showModal(response.message || 'Verifikasi Gagal', '/Sistem-Pendaftaran-Calon-Asisten/public/Assets/gif/failedregistergif.gif', function () {
                        showModalUI('otpModal');
                        otpInputs.forEach(inp => inp.value = '');
                        setTimeout(() => { if (otpInputs[0]) otpInputs[0].focus(); }, 500);
                    });
                }
            })
            .catch(error => {
                if (btnVerify) { btnVerify.disabled = false; btnVerify.innerHTML = originalText; }
                console.log('Terjadi kesalahan: ' + error);
                hideModalUI('otpModal');
                showModal('Terjadi kesalahan koneksi server', '/Sistem-Pendaftaran-Calon-Asisten/public/Assets/gif/failedregistergif.gif', function () {
                    showModalUI('otpModal');
                });
            });
        });
    }

    // ── Resend OTP ────────────────────────────────────────────
    var btnResendOtp = document.getElementById('btnResendOtp');
    if (btnResendOtp) {
        btnResendOtp.addEventListener('click', function (e) {
            e.preventDefault();
            const timerSpan = document.getElementById('otpTimer');

            btnResendOtp.classList.add('pointer-events-none', 'opacity-50');

            fetch('/Sistem-Pendaftaran-Calon-Asisten/public/register/resend-otp', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
            })
            .then(res => res.json())
            .then(response => {
                if (response.status === 'success') {
                    hideModalUI('otpModal');
                    showModal(response.message, '/Sistem-Pendaftaran-Calon-Asisten/public/Assets/gif/registergif.gif', function () {
                        showModalUI('otpModal');
                        otpInputs.forEach(inp => inp.value = '');
                        setTimeout(() => { if (otpInputs[0]) otpInputs[0].focus(); }, 500);
                    });

                    startOtpExpiryCountdown(300);

                    let secondsLeft = 60;
                    if (timerSpan) { timerSpan.textContent = `(${secondsLeft}s)`; timerSpan.classList.remove('hidden'); }

                    if (cooldownTimer) clearInterval(cooldownTimer);
                    cooldownTimer = setInterval(function () {
                        secondsLeft--;
                        if (secondsLeft <= 0) {
                            clearInterval(cooldownTimer);
                            if (timerSpan) timerSpan.classList.add('hidden');
                            btnResendOtp.classList.remove('pointer-events-none', 'opacity-50');
                        } else {
                            if (timerSpan) timerSpan.textContent = `(${secondsLeft}s)`;
                        }
                    }, 1000);
                } else {
                    btnResendOtp.classList.remove('pointer-events-none', 'opacity-50');
                    hideModalUI('otpModal');
                    showModal(response.message || 'Gagal mengirim ulang OTP', '/Sistem-Pendaftaran-Calon-Asisten/public/Assets/gif/failedregistergif.gif', function () {
                        showModalUI('otpModal');
                    });
                }
            })
            .catch(error => {
                btnResendOtp.classList.remove('pointer-events-none', 'opacity-50');
                console.log('Terjadi kesalahan: ' + error);
                hideModalUI('otpModal');
                showModal('Terjadi kesalahan koneksi server', '/Sistem-Pendaftaran-Calon-Asisten/public/Assets/gif/failedregistergif.gif', function () {
                    showModalUI('otpModal');
                });
            });
        });
    }

    // Close OTP modal — clear timers
    var closeOtpModal = document.getElementById('closeOtpModal');
    if (closeOtpModal) {
        closeOtpModal.addEventListener('click', function () {
            if (cooldownTimer) clearInterval(cooldownTimer);
            if (expiryTimer)   clearInterval(expiryTimer);
            var timerSpan = document.getElementById('otpTimer');
            if (timerSpan) timerSpan.classList.add('hidden');
            if (btnResendOtp) btnResendOtp.classList.remove('pointer-events-none', 'opacity-50');
        });
    }

    // ── Login Form ────────────────────────────────────────────
    var loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', function (e) {
            e.preventDefault();

            fetch('/Sistem-Pendaftaran-Calon-Asisten/public/login/authenticate', {
                method: 'POST',
                body: new URLSearchParams(new FormData(loginForm))
            })
            .then(res => res.json())
            .then(response => {
                if (response.status === 'success') {
                    showModal('Login Berhasil', '/Sistem-Pendaftaran-Calon-Asisten/public/Assets/gif/loginsuccess.gif');
                    setTimeout(() => { window.location.href = response.redirect; }, 1000);
                } else {
                    showModal('Stambuk atau password salah', '/Sistem-Pendaftaran-Calon-Asisten/public/Assets/gif/failedregistergif.gif');
                }
            })
            .catch(error => {
                console.log('Terjadi kesalahan: ' + error);
            });
        });
    }
});

