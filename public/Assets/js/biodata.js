// Dependencies: common.js untuk showModal()

function validatePhoneNumber(phoneNumber) {
  const phoneRegex = /^(?:\+62|62|0)(8[1-9][0-9]{6,9})$/;

  if (!phoneRegex.test(phoneNumber)) {
    return {
      success: false,
      message:
        "nomor telepon tidak valid.",
    };
  }

  return { success: true, message: "Nomor telepon valid." };
}

function validateNoNumber(input) {
  const noNumberRegex = /^[A-Za-z\s]*$/;

  if (!noNumberRegex.test(input)) {
    return {
      success: false,
      message: "Input tidak valid: tidak boleh mengandung angka.",
    };
  }

  return { success: true, message: "Input valid: Tidak ada angka." };
}

$(document).ready(function () {
  const telephoneInput = document.getElementById("telephone");
const tempatLahirInput = document.getElementById("tempatlahir");
const namaInput = document.getElementById("nama");

  // Logout Button
  $("#logoutButton").click(function (e) {
    e.preventDefault();
    
    showActionConfirmation({
        title: 'Konfirmasi Keluar',
        message: 'Apakah Anda yakin ingin keluar dari aplikasi?',
        btnText: 'Keluar',
        type: 'danger',
        onConfirm: function() {
            $.ajax({
              url: `${APP_URL}/logout`,
              type: "POST",
              success: function (response) {
                 window.location.href = APP_URL;
              },
              error: function (xhr, status, error) {
                 window.location.href = APP_URL;
              },
            });
        }
    });
  });

  if (telephoneInput) {
    telephoneInput.addEventListener("input", function () {
      telephoneInput.setCustomValidity("");
      telephoneInput.reportValidity();
    });
  }

  if (namaInput) {
    namaInput.addEventListener("input", function () {
      namaInput.setCustomValidity("");
      namaInput.reportValidity();
    });
  }

  if (tempatLahirInput) {
    tempatLahirInput.addEventListener("input", function () {
      tempatLahirInput.setCustomValidity("");
      tempatLahirInput.reportValidity();
    });
  }
  
  $("#biodataForm").submit(function (e) {
    e.preventDefault();
    console.log($("#biodataForm").serialize());

    const phoneNumber = document.getElementById("telephone").value;
    const tempatLahir = document.getElementById("tempatlahir").value;
    const nama = document.getElementById("nama").value;

    let isValid = true;

    if (!validatePhoneNumber(phoneNumber).success) {
      showModal(
        validatePhoneNumber(phoneNumber).message,
        `${APP_URL}/Assets/gif/failed.gif`
      );
      isValid = false;
    } else if (!validateNoNumber(tempatLahir).success) {
      showModal(
        validateNoNumber(tempatLahir).message,
        `${APP_URL}/Assets/gif/failed.gif`
      );
      isValid = false;
    } else if (!validateNoNumber(nama).success) {
      showModal(
        validateNoNumber(nama).message,
        `${APP_URL}/Assets/gif/failed.gif`
      );
      isValid = false;
    }

    if(!isValid) return;
    $.ajax({
      url: `${APP_URL}/store`,
      type: "post",
      data: $("#biodataForm").serialize(),
      dataType: "json",
      success: function (response) {
        if (response.status === "success") {
          // Changed to standard Toast Alert for success
          if (typeof showAlert === 'function') {
             showAlert('Biodata berhasil disimpan!', true);
          } else {
             alert('Biodata berhasil disimpan!');
          }
          
          // Reload page to reflect changes
          if (typeof loadPage === 'function') {
               loadPage('biodata');
          } else {
             document.querySelector('a[data-page="biodata"]').click();
          }
        } else {
          showModal(
            response.message || "Biodata gagal disimpan",
            `${APP_URL}/Assets/gif/failed.gif`
          );
        }
      },
      error: function (xhr, status, error) {
        console.error("AJAX Error Details:");
        console.error("Status:", xhr.status);
        console.error("Response Text:", xhr.responseText);
        console.error("Error:", error);
        
        let errorMessage = "Terjadi kesalahan: " + error;
        if (xhr.responseText) {
          try {
            const response = JSON.parse(xhr.responseText);
            errorMessage = response.message || errorMessage;
          } catch(e) {
            errorMessage = "Server error: " + xhr.responseText;
          }
        }
        
        showModal(
          errorMessage,
          `${APP_URL}/Assets/gif/failed.gif`
        );
      },
    });
  });
});

// Fungsi Update Pilihan Kelas Berdasarkan Gender
function updateKelasOptions() {
  const genderElement = document.querySelector('input[name="gender"]:checked');

  if (!genderElement) return; // Hindari error jika tidak ada input gender

  const gender = genderElement.value;
  const kelasSelect = document.getElementById("floatingSelect");

  kelasSelect.innerHTML = ""; // Hapus opsi lama

  let kelasOptions = [];

  if (gender === "wanita") {
    kelasOptions = ["B1", "B2", "B3", "B4", "B5"];
  } else if (gender === "pria") {
    kelasOptions = ["A1", "A2", "A3", "A4", "A5", "A6", "A7", "A8", "A9"];
  }

  kelasOptions.forEach((kelas) => {
    const option = document.createElement("option");
    option.value = kelas;
    option.text = kelas;
    kelasSelect.appendChild(option);
  });
}