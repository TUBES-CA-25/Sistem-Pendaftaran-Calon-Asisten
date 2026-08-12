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

// Durasi harus SAMA dengan duration-200 pada markup modal.
// Kalau lebih kecil, modal disembunyikan sebelum fade selesai (terlihat putus);
// kalau lebih besar, backdrop tertinggal di layar.
const MODAL_TRANSITION_MS = 200;

// ── Modal Helper (Tailwind Implementation) ──────────────────
// Mengikuti urutan kanonik di core/ui.js: tampilkan -> paksa reflow -> set
// data-open. Reflow WAJIB; tanpanya penambahan atribut terjadi di frame yang
// sama dengan perubahan class sehingga transisi tidak pernah berjalan.
function showModalUI(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;

    modal.classList.remove('hidden');
    modal.classList.add('flex');
    modal.removeAttribute('aria-hidden');
    modal.setAttribute('aria-modal', 'true');

    void modal.offsetHeight;          // paksa reflow
    modal.setAttribute('data-open', '');
}

function hideModalUI(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;

    // Lepas fokus dari dalam modal sebelum disembunyikan agar tidak terjebak.
    if (modal.contains(document.activeElement) && document.activeElement.blur) {
        document.activeElement.blur();
    }

    modal.removeAttribute('data-open');
    modal.setAttribute('aria-hidden', 'true');
    modal.removeAttribute('aria-modal');

    setTimeout(function () {
        // PENJAGA: halaman ini kerap membuka ulang modal dari callback DI DALAM
        // jendela 200ms ini. Tanpa penjaga, modal yang baru dibuka langsung
        // kena `hidden` lagi dan hilang.
        if (modal.hasAttribute('data-open')) return;
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }, MODAL_TRANSITION_MS);
}

// ── Loading state tombol ────────────────────────────────────
// Mengganti pola lama `innerHTML = '<span class="spinner-border">…'` yang mati
// total (Bootstrap CSS tidak dimuat di halaman auth). Memakai .loading yang
// sudah ada di ketiga halaman auth, dan TIDAK menyentuh innerHTML supaya node
// tidak dibongkar-pasang tiap submit.
function setBtnLoading(btn, teks) {
    if (!btn) return function () {};

    const spinner = btn.querySelector('.loading');
    const icon    = btn.querySelector('.btn-icon');
    const label   = btn.querySelector('.btn-text');
    const teksAsli = label ? label.textContent : '';

    btn.disabled = true;
    if (spinner) spinner.classList.add('active');
    if (icon) icon.classList.add('hidden');
    if (label && teks) label.textContent = teks;

    return function restore() {
        btn.disabled = false;
        if (spinner) spinner.classList.remove('active');
        if (icon) icon.classList.remove('hidden');
        if (label) label.textContent = teksAsli;
    };
}

// ── Umpan balik error: getarkan elemen ──────────────────────
// Mengaktifkan keyframe `shake` yang sudah lama didefinisikan di
// tailwind-config.js tetapi tidak pernah dipakai.
function shakeElement(el) {
    if (!el) return;
    el.classList.remove('animate-shake');
    void el.offsetWidth;              // paksa reflow agar bisa menyala lagi
    el.classList.add('animate-shake');
    el.addEventListener('animationend', function () {
        el.classList.remove('animate-shake');
    }, { once: true });
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

// Close modals when clicking outside.
// Sejak backdrop menjadi elemen sendiri (bg-slate-900/50 backdrop-blur-sm),
// klik mengenai backdrop-nya, bukan wadah modal — jadi kedua id diperiksa.
// Penutupan diarahkan ke tombol tutup masing-masing, bukan hideModalUI
// langsung, karena penutupan OTP juga harus membersihkan timer.
window.addEventListener('click', function(e) {
    if (e.target.id === 'customModal' || e.target.id === 'customModalBackdrop') {
        document.getElementById('closeModal')?.click();
    }
    if (e.target.id === 'otpModal' || e.target.id === 'otpModalBackdrop') {
        document.getElementById('closeOtpModal')?.click();
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

/**
 * Email kampus wajib memakai stambuk sendiri sebagai bagian sebelum '@'.
 * Cerminan dari validasi server di AuthController::register(); server tetap
 * jadi penentu akhir, ini hanya agar pengguna tahu sebelum submit.
 */
function validateEmailStambukCocok(email, stambuk) {
  const lokal = email.split("@")[0];
  if (lokal.toLowerCase() !== stambuk.toLowerCase()) {
    return {
      success: false,
      message: "Email harus memakai stambuk Anda sendiri, yaitu " + stambuk + "@student.umi.ac.id.",
    };
  }
  return { success: true, message: "Email cocok dengan stambuk." };
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

// validatePasswordLogin() dihapus: Validator password khusus form login; tidak pernah dipanggil (form login memakai validasi bawaan browser + respons server).


// ── Toggle Login / Register ───────────────────────────────────
// Dulu logikanya tersebar di 3 tempat (onclick inline di view, listener di sini,
// dan <script> inline di bawah view) plus satu blok mati yang mengacu ke ID
// #mobile-register-btn / #mobile-login-btn — ID yang tidak pernah ada di markup
// (markup memakai register-mobile / login-mobile). Kini terpusat di sini.
//
// Hanya menyentuh container.classList add/remove('active'); variant
// `group-active` (.group.active &) di tailwind-config.js tetap utuh.

/**
 * Putar ulang animasi masuk pada panel yang baru ditampilkan.
 * `fade-up` memakai fill-mode `both` dan hanya berjalan sekali saat load,
 * sehingga tanpa pemicu ulang ini stagger panel Daftar tidak akan pernah
 * terlihat (panelnya tidak tampil saat halaman dimuat).
 */
function replayEntrance(form) {
    if (!form) return;
    form.querySelectorAll('.animate-fade-up').forEach(function (el) {
        el.classList.remove('animate-fade-up');
        void el.offsetWidth;          // paksa reflow
        el.classList.add('animate-fade-up');
    });
}

/**
 * Jalankan animasi membalik kartu.
 *
 * Kelasnya dilepas dan dipasang ulang dengan reflow paksa di antaranya, sama
 * seperti replayEntrance: tanpa itu animasi hanya berjalan sekali karena
 * memasang kelas yang sudah menempel bukan perubahan bagi mesin animasi.
 */
function kocokKartu() {
    if (!container) return;
    container.classList.remove('mengocok');
    void container.offsetWidth;   // paksa reflow
    container.classList.add('mengocok');
}

function showRegister() {
    kocokKartu();
    container.classList.add('active');
    replayEntrance(document.getElementById('registerForm'));
}

function showLogin() {
    kocokKartu();
    container.classList.remove('active');
    replayEntrance(document.getElementById('loginForm'));
}

// Keempat tombol (desktop + mobile) memakai fungsi yang sama.
['register', 'register-mobile'].forEach(function (id) {
    document.getElementById(id)?.addEventListener('click', showRegister);
});
['login', 'login-mobile'].forEach(function (id) {
    document.getElementById(id)?.addEventListener('click', showLogin);
});


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

            const stambukResult   = validateStambuk(stambuk);
            const emailResult     = validateEmail(email);
            const passwordResult  = validatePassword(password, confirmPassword);

            let isValid = true;
            if (!emailResult.success)   { emailinput.setCustomValidity(emailResult.message);   emailinput.reportValidity();   shakeElement(emailinput.closest('.float-group'));   isValid = false; }
            if (!stambukResult.success) { stambukInput.setCustomValidity(stambukResult.message); stambukInput.reportValidity(); shakeElement(stambukInput.closest('.float-group')); isValid = false; }
            if (!passwordResult.success){ passwordinput.setCustomValidity(passwordResult.message); passwordinput.reportValidity(); shakeElement(passwordinput.closest('.float-group')); isValid = false; }

            // Kecocokan email<->stambuk hanya diperiksa bila keduanya sudah
            // valid, supaya pesan yang muncul tidak menumpuk/membingungkan.
            if (emailResult.success && stambukResult.success) {
                const cocokResult = validateEmailStambukCocok(email, stambuk);
                if (!cocokResult.success) {
                    emailinput.setCustomValidity(cocokResult.message);
                    emailinput.reportValidity();
                    shakeElement(emailinput.closest('.float-group'));
                    isValid = false;
                }
            }

            if (!isValid) return;

            const btnRegister = registerForm.querySelector('button[type="submit"]');
            const restoreRegister = setBtnLoading(btnRegister, 'Mendaftar...');

            const formData = new FormData(registerForm);
            fetch('/Sistem-Pendaftaran-Calon-Asisten/public/register/authenticate', {
                method: 'POST',
                body: new URLSearchParams(formData)
            })
            .then(res => res.json())
            .then(response => {
                restoreRegister();
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
                restoreRegister();
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
            // Penanda visual kotak yang sudah terisi (.otp-input.filled)
            this.classList.toggle('filled', this.value.length > 0);
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
            const restoreVerify = setBtnLoading(btnVerify, 'Memverifikasi...');

            fetch('/Sistem-Pendaftaran-Calon-Asisten/public/register/verify-otp', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'otp=' + encodeURIComponent(combinedOtp)
            })
            .then(res => res.json())
            .then(response => {
                restoreVerify();
                if (response.status === 'success') {
                    hideModalUI('otpModal');
                    showModal('Registrasi Berhasil', '/Sistem-Pendaftaran-Calon-Asisten/public/Assets/gif/registergif.gif');
                    document.getElementById('login').click();
                } else {
                    hideModalUI('otpModal');
                    showModal(response.message || 'Verifikasi Gagal', '/Sistem-Pendaftaran-Calon-Asisten/public/Assets/gif/failedregistergif.gif', function () {
                        showModalUI('otpModal');
                        otpInputs.forEach(inp => { inp.value = ''; inp.classList.remove('filled'); });
                        setTimeout(() => { if (otpInputs[0]) otpInputs[0].focus(); }, 500);
                    });
                }
            })
            .catch(error => {
                restoreVerify();
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
                        otpInputs.forEach(inp => { inp.value = ''; inp.classList.remove('filled'); });
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

            const btnLogin = document.getElementById('btnlogin');

            // Cegah pengiriman ganda saat jaringan lambat: dulu tombol ini
            // sama sekali tidak punya umpan balik sehingga pengguna menekannya
            // berkali-kali.
            if (btnLogin && btnLogin.disabled) return;
            const restoreLogin = setBtnLoading(btnLogin, 'Masuk...');

            fetch('/Sistem-Pendaftaran-Calon-Asisten/public/login/authenticate', {
                method: 'POST',
                body: new URLSearchParams(new FormData(loginForm))
            })
            .then(res => res.json())
            .then(response => {
                if (response.status === 'success') {
                    // Sengaja TIDAK di-restore: halaman sedang berpindah,
                    // biarkan tetap nonaktif agar tidak bisa disubmit ulang.
                    showModal('Login Berhasil', '/Sistem-Pendaftaran-Calon-Asisten/public/Assets/gif/loginsuccess.gif');
                    setTimeout(() => { window.location.href = response.redirect; }, 1000);
                } else {
                    restoreLogin();
                    shakeElement(loginForm);
                    // Sengaja memakai teks Indonesia sendiri: pesan server untuk
                    // kasus ini masih berbahasa Inggris ("Stambuk or password is
                    // incorrect."), sedangkan seluruh UI berbahasa Indonesia.
                    showModal('Stambuk atau password salah', '/Sistem-Pendaftaran-Calon-Asisten/public/Assets/gif/failedregistergif.gif');
                }
            })
            .catch(error => {
                // Dulu hanya console.log -> layar diam, pengguna tidak tahu
                // apa pun ketika server mati atau koneksi putus.
                restoreLogin();
                showModal('Terjadi kesalahan koneksi ke server. Coba lagi.', '/Sistem-Pendaftaran-Calon-Asisten/public/Assets/gif/failedregistergif.gif');
                console.error('Login error:', error);
            });
        });
    }
});

