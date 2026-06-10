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

// Dependencies: common.js untuk showModal()
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

    const modal = new bootstrap.Modal(modalEl);
    modal.show();

    // Handle close callback exactly once when the modal finishes hiding
    if (onCloseCallback) {
        modalEl.addEventListener('hidden.bs.modal', function handler() {
            onCloseCallback();
            modalEl.removeEventListener('hidden.bs.modal', handler);
        }, { once: true });
    }
};

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
// Animasi FULL CSS via max-height + opacity + transform transition.
// JS hanya menambah/hapus class 'active' pada container.
// ─────────────────────────────────────────────────────────────

registerBtn.addEventListener('click', () => container.classList.add('active'));
loginBtn.addEventListener('click',    () => container.classList.remove('active'));

// Fallback mobile links (jika ada di HTML)
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

$(document).ready(function () {
    let expiryTimer = null;

    function startOtpExpiryCountdown(durationSeconds) {
        if (expiryTimer) clearInterval(expiryTimer);
        
        const timerSpan = $('#otpExpiryTimer');
        const btnVerify = $('#btnVerifyOtp');
        const otpInputs = $('.otp-input');
        
        btnVerify.prop('disabled', false);
        otpInputs.prop('disabled', false);
        
        let timeLeft = durationSeconds;
        
        function updateDisplay() {
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            const formattedMinutes = minutes < 10 ? '0' + minutes : minutes;
            const formattedSeconds = seconds < 10 ? '0' + seconds : seconds;
            timerSpan.text(`${formattedMinutes}:${formattedSeconds}`);
        }
        
        updateDisplay();
        
        expiryTimer = setInterval(function() {
            timeLeft--;
            if (timeLeft <= 0) {
                clearInterval(expiryTimer);
                timerSpan.text('Waktu habis! Silakan kirim ulang OTP.');
                btnVerify.prop('disabled', true);
                otpInputs.prop('disabled', true);
            } else {
                updateDisplay();
            }
        }, 1000);
    }

    $('#registerForm').submit(function (e) {
        console.log('Form submit initiated'); 
        e.preventDefault();
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirmPass').value;
        const stambuk = document.getElementById('stambukregister').value;
    
        console.log('Values captured:', { email, password, confirmPassword, stambuk });
    
        const stambukResult = validateStambuk(stambuk);
        const emailResult = validateEmail(email);
        const passwordResult = validatePassword(password, confirmPassword);
    
        console.log('Validation results:', { stambukResult, emailResult, passwordResult });
    
        let isValid = true;
    
        if (!emailResult.success) {
            console.log('Email validation failed');
            emailinput.setCustomValidity(emailResult.message);
            emailinput.reportValidity();
            isValid = false;
        } 
    
        if (!stambukResult.success) {
            console.log('Stambuk validation failed');
            stambukInput.setCustomValidity(stambukResult.message);
            stambukInput.reportValidity();
            isValid = false;
        } 
    
        if (!passwordResult.success) {
            console.log('Password validation failed');
            passwordinput.setCustomValidity(passwordResult.message);
            passwordinput.reportValidity();
            isValid = false;
        } 
    
        if (!isValid) {
            console.log('Form validation failed, exiting');
            return; 
        }
    
        console.log('Form validation passed, submitting...');
        const btnRegister = $('#registerForm button[type="submit"]');
        const originalBtnText = btnRegister.html();
        btnRegister.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Loading...');
        
        $.ajax({
            url: '/Sistem-Pendaftaran-Calon-Asisten/public/register/authenticate',
            type: 'post',
            data: $('#registerForm').serialize(),
            dataType: 'json',
            success: function (response) {
                btnRegister.prop('disabled', false).html(originalBtnText);
                if (response.status === 'success') {
                    showModal('Register Berhasil', '/Sistem-Pendaftaran-Calon-Asisten/public/Assets/gif/registergif.gif');
                    document.getElementById('login').click();
                } else if (response.status === 'otp_required') {
                    $('#otpEmailSpan').text(response.email);
                    $('.otp-input').val('');
                    $('#otpCode').val('');
                    
                    const otpModalEl = document.getElementById('otpModal');
                    const otpModal = new bootstrap.Modal(otpModalEl);
                    otpModal.show();
                    
                    startOtpExpiryCountdown(300); // Start the 5-minute countdown (300 seconds)
                    
                    setTimeout(() => {
                        $('.otp-input').first().focus();
                    }, 500);
                } else {
                    showModal(response.message || 'Register Gagal', '/Sistem-Pendaftaran-Calon-Asisten/public/Assets/gif/failedregistergif.gif');
                }
            },
            error: function (xhr, status, error) {
                btnRegister.prop('disabled', false).html(originalBtnText);
                console.log('Terjadi kesalahan: ' + error);
                showModal('Terjadi kesalahan koneksi server', '/Sistem-Pendaftaran-Calon-Asisten/public/Assets/gif/failedregistergif.gif');
            },
        });
    });

    // ── OTP Autofocus & Key Handling ──────────────────────────
    const $otpInputs = $('.otp-input');
    $otpInputs.each(function(index) {
        $(this).on('keyup', function(e) {
            const val = $(this).val();
            if (val && /^[0-9]$/.test(val)) {
                if (index < $otpInputs.length - 1) {
                    $otpInputs.eq(index + 1).focus();
                }
            }
        });
        
        $(this).on('keydown', function(e) {
            if (e.key === 'Backspace') {
                if (!$(this).val() && index > 0) {
                    $otpInputs.eq(index - 1).focus();
                }
            }
        });

        $(this).on('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    });

    // ── OTP Submission ────────────────────────────────────────
    $('#otpForm').submit(function(e) {
        e.preventDefault();
        
        let combinedOtp = '';
        $otpInputs.each(function() {
            combinedOtp += $(this).val();
        });
        
        $('#otpCode').val(combinedOtp);
        
        const btnVerify = $('#btnVerifyOtp');
        const originalText = btnVerify.html();
        btnVerify.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Memverifikasi...');
        
        $.ajax({
            url: '/Sistem-Pendaftaran-Calon-Asisten/public/register/verify-otp',
            type: 'post',
            data: { otp: combinedOtp },
            dataType: 'json',
            success: function(response) {
                btnVerify.prop('disabled', false).html(originalText);
                if (response.status === 'success') {
                    const modalEl = document.getElementById('otpModal');
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) modal.hide();
                    
                    showModal('Registrasi Berhasil', '/Sistem-Pendaftaran-Calon-Asisten/public/Assets/gif/registergif.gif');
                    document.getElementById('login').click();
                } else {
                    // Hide OTP modal to prevent background overlay stacking bug
                    const modalEl = document.getElementById('otpModal');
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) modal.hide();
                    
                    showModal(response.message || 'Verifikasi Gagal', '/Sistem-Pendaftaran-Calon-Asisten/public/Assets/gif/failedregistergif.gif', function() {
                        // Re-show OTP modal, clear incorrect inputs, and focus first input
                        if (modal) modal.show();
                        $('.otp-input').val('');
                        setTimeout(() => {
                            $('.otp-input').first().focus();
                        }, 500);
                    });
                }
            },
            error: function(xhr, status, error) {
                btnVerify.prop('disabled', false).html(originalText);
                console.log('Terjadi kesalahan: ' + error);
                
                const modalEl = document.getElementById('otpModal');
                const modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) modal.hide();

                showModal('Terjadi kesalahan koneksi server', '/Sistem-Pendaftaran-Calon-Asisten/public/Assets/gif/failedregistergif.gif', function() {
                    if (modal) modal.show();
                });
            }
        });
    });

    // ── Resend OTP Cooldown Timer ──────────────────────────────
    let cooldownTimer = null;
    $('#btnResendOtp').click(function(e) {
        e.preventDefault();
        
        const btnResend = $(this);
        const timerSpan = $('#otpTimer');
        
        btnResend.addClass('pointer-events-none opacity-50');
        
        $.ajax({
            url: '/Sistem-Pendaftaran-Calon-Asisten/public/register/resend-otp',
            type: 'post',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    const modalEl = document.getElementById('otpModal');
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) modal.hide();

                    showModal(response.message, '/Sistem-Pendaftaran-Calon-Asisten/public/Assets/gif/registergif.gif', function() {
                        if (modal) modal.show();
                        $otpInputs.val('');
                        setTimeout(() => {
                            $otpInputs.first().focus();
                        }, 500);
                    });
                    
                    startOtpExpiryCountdown(300); // Restart the 5-minute countdown
                    
                    let secondsLeft = 60;
                    timerSpan.text(`(${secondsLeft}s)`).removeClass('d-none');
                    
                    if (cooldownTimer) clearInterval(cooldownTimer);
                    cooldownTimer = setInterval(function() {
                        secondsLeft--;
                        if (secondsLeft <= 0) {
                            clearInterval(cooldownTimer);
                            timerSpan.addClass('d-none');
                            btnResend.removeClass('pointer-events-none opacity-50');
                        } else {
                            timerSpan.text(`(${secondsLeft}s)`);
                        }
                    }, 1000);
                } else {
                    btnResend.removeClass('pointer-events-none opacity-50');
                    
                    const modalEl = document.getElementById('otpModal');
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) modal.hide();

                    showModal(response.message || 'Gagal mengirim ulang OTP', '/Sistem-Pendaftaran-Calon-Asisten/public/Assets/gif/failedregistergif.gif', function() {
                        if (modal) modal.show();
                    });
                }
            },
            error: function(xhr, status, error) {
                btnResend.removeClass('pointer-events-none opacity-50');
                console.log('Terjadi kesalahan: ' + error);
                
                const modalEl = document.getElementById('otpModal');
                const modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) modal.hide();

                showModal('Terjadi kesalahan koneksi server', '/Sistem-Pendaftaran-Calon-Asisten/public/Assets/gif/failedregistergif.gif', function() {
                    if (modal) modal.show();
                });
            }
        });
    });

    $('#otpModal').on('hidden.bs.modal', function () {
        if (cooldownTimer) {
            clearInterval(cooldownTimer);
        }
        if (expiryTimer) {
            clearInterval(expiryTimer);
        }
        $('#otpTimer').addClass('d-none');
        $('#btnResendOtp').removeClass('pointer-events-none opacity-50');
    });
    

  $("#loginForm").submit(function (e) {
    e.preventDefault();

    $.ajax({
      url: "/Sistem-Pendaftaran-Calon-Asisten/public/login/authenticate",
      type: "post",
      data: $("#loginForm").serialize(),
      dataType: "json",
      success: function (response) {
        if (response.status === "success") {
          showModal("Login Berhasil", "/Sistem-Pendaftaran-Calon-Asisten/public/Assets/gif/loginsuccess.gif");
          setTimeout(() => {
            window.location.href = response.redirect;
        }, 1000);
        } else {
            showModal("Stambuk atau password salah", "/Sistem-Pendaftaran-Calon-Asisten/public/Assets/gif/failedregistergif.gif");
        }
      },
      error: function (xhr, status, error) {
        console.log("Terjadi kesalahan: " + xhr.responseText);
      },
    });
  });
});
