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

function showModal(message, gifUrl = null) {
    const modal = document.getElementById('customModal');
    const modalMessage = document.getElementById('modalMessage');
    const modalGif = document.getElementById('modalGif');
    const closeModal = document.getElementById('closeModal');

    modalMessage.textContent = message;

    if (gifUrl) {
        modalGif.src = gifUrl;
        modalGif.style.display = 'block';
    } else {
        modalGif.style.display = 'none';
    }

    modal.style.display = 'flex';

    closeModal.addEventListener('click', () => {
        modal.style.display = 'none';
        if (onClose) {
            onClose(); 
        }
    });

    window.addEventListener('click', (event) => {
        if (event.target === modal) {
            modal.style.display = 'none';
            if (onClose) {
                onClose();
            }
        }
    });
}
function validateStambuk(stambuk) {
  const stambukRegex = /^(131|130)(2022|2023|2024|2025)[0-9]{4}$/;

  if (!stambukRegex.test(stambuk)) {
    return {
      success: false,
      message:
        "Stambuk harus diawali dengan 131 atau 130, diikuti tahun 2022-2025, dan panjangnya 11 karakter.",
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

  if (domain !== "umi.ac.id" && domain !== "student.umi.ac.id") {
    return { success: false, message: "Email harus menggunakan domain UMI." };
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

registerBtn.addEventListener("click", () => {
  container.classList.add("active");
});

loginBtn.addEventListener("click", () => {
  container.classList.remove("active");
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

$(document).ready(function () {
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
        $.ajax({
            url: '/Kelompok1_IC-ASSIST/public/register/authenticate',
            type: 'post',
            data: $('#registerForm').serialize(),
            dataType: 'json',
            success: function (response) {
                if (response.status === 'success') {
                    showModal('Register Berhasil', '/Kelompok1_IC-ASSIST/public/Assets/gif/registergif.gif');
                    document.getElementById('login').click();
                } else {
                    showModal('Register Gagal stambuk sudah digunakan', '/Kelompok1_IC-ASSIST/public/Assets/gif/failedregistergif.gif');
                    console.log(response.message);
                }
            },
            error: function (xhr, status, error) {
                console.log('Terjadi kesalahan: ' + error);
            },
        });
    });
    

  $("#loginForm").submit(function (e) {
    e.preventDefault();

    $.ajax({
      url: "/Kelompok1_IC-ASSIST/public/login/authenticate",
      type: "post",
      data: $("#loginForm").serialize(),
      dataType: "json",
      success: function (response) {
        if (response.status === "success") {
          showModal("Login Berhasil", "/Kelompok1_IC-ASSIST/public/Assets/gif/loginsuccess.gif");
          setTimeout(() => {
            window.location.href = response.redirect;
        }, 1000);
        } else {
            showModal("Stambuk atau password salah", "/Kelompok1_IC-ASSIST/public/Assets/gif/failedregistergif.gif");
        }
      },
      error: function (xhr, status, error) {
        console.log("Terjadi kesalahan: " + xhr.responseText);
      },
    });
  });
});
