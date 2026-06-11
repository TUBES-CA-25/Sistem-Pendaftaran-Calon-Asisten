// Dependencies: common.js untuk showModal()

function validatePhoneNumber(phoneNumber) {
  const phoneRegex = /^(?:\+62|62|0)(8[1-9][0-9]{6,9})$/;

  if (!phoneRegex.test(phoneNumber)) {
    return {
      success: false,
      message: "Nomor telepon tidak valid.",
    };
  }

  return { success: true, message: "Nomor telepon valid." };
}

function validateNoSpecialChar(input, allowNumbers = true) {
  if (!input || input.trim() === "") {
    return {
      success: false,
      message: "tidak boleh kosong.",
    };
  }

  const regex = allowNumbers ? /^[A-Za-z0-9\s]+$/ : /^[A-Za-z\s]+$/;
  if (!regex.test(input)) {
    return {
      success: false,
      message: allowNumbers ? "tidak boleh mengandung karakter spesial." : "tidak boleh mengandung angka atau karakter spesial.",
    };
  }

  return { success: true, message: "Input valid." };
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

  // Handle select change event for manual input
  $("#floatingSelect").change(function() {
    if ($(this).val() === "Lainnya") {
      $("#kelasManualContainer").show();
      $("#kelasManual").prop('required', true);
    } else {
      $("#kelasManualContainer").hide();
      $("#kelasManual").prop('required', false).val("");
    }
  });
  
  $("#biodataForm").submit(function (e) {
    e.preventDefault();

    const selectEl = document.getElementById("floatingSelect");
    const isLainnya = selectEl.value === "Lainnya";
    const manualValue = document.getElementById("kelasManual").value.trim().toUpperCase();

    const genderElement = document.querySelector('input[name="gender"]:checked');
    const gender = genderElement ? genderElement.value : '';

    let kelasToValidate = isLainnya ? manualValue : selectEl.value;

    if (!kelasToValidate || kelasToValidate === "" || kelasToValidate === "Pilih Kelas Anda") {
      showModal(
        "Kelas tidak boleh kosong.",
        `${APP_URL}/Assets/gif/failed.gif`
      );
      return;
    }

    if (gender === "wanita" && !/^[Bb][0-9]+$/.test(kelasToValidate)) {
      showModal(
        "Kelas untuk wanita harus diawali dengan huruf B lalu diikuti dengan angka (contoh: B5 atau B7).",
        `${APP_URL}/Assets/gif/failed.gif`
      );
      return;
    }

    if (gender === "pria" && !/^[Aa][0-9]+$/.test(kelasToValidate)) {
      showModal(
        "Kelas untuk pria harus diawali dengan huruf A lalu diikuti dengan angka (contoh: A9 atau A10).",
        `${APP_URL}/Assets/gif/failed.gif`
      );
      return;
    }

    const phoneNumber = document.getElementById("telephone").value;
    const tempatLahir = document.getElementById("tempatlahir").value;
    const nama = document.getElementById("nama").value;
    const alamat = document.getElementById("alamat").value;

    let isValid = true;

    const nameValidation = validateNoSpecialChar(nama, false);
    const birthValidation = validateNoSpecialChar(tempatLahir, false);
    const addressValidation = validateNoSpecialChar(alamat, true);
    const phoneValidation = validatePhoneNumber(phoneNumber);

    if (!nameValidation.success) {
      showModal(
        "Nama Lengkap " + nameValidation.message,
        `${APP_URL}/Assets/gif/failed.gif`
      );
      isValid = false;
    } else if (!birthValidation.success) {
      showModal(
        "Tempat Lahir " + birthValidation.message,
        `${APP_URL}/Assets/gif/failed.gif`
      );
      isValid = false;
    } else if (!addressValidation.success) {
      showModal(
        "Alamat " + addressValidation.message,
        `${APP_URL}/Assets/gif/failed.gif`
      );
      isValid = false;
    } else if (!phoneValidation.success) {
      showModal(
        "No Telephone: " + phoneValidation.message,
        `${APP_URL}/Assets/gif/failed.gif`
      );
      isValid = false;
    }

    if(!isValid) return;

    // Temporarily replace "Lainnya" option's value with the manual input value so it gets serialized
    let lainnyaOption = null;
    if (isLainnya) {
      lainnyaOption = Array.from(selectEl.options).find(opt => opt.value === "Lainnya");
      if (lainnyaOption) {
        lainnyaOption.value = manualValue;
      }
    }
    $.ajax({
      url: `${APP_URL}/store`,
      type: "post",
      data: $("#biodataForm").serialize(),
      dataType: "json",
      success: function (response) {
        if (response.status === "success") {
          // Changed to standard Toast Alert for success
          if (typeof showAlert === 'function') {
             showAlert(response.message || 'Biodata berhasil disimpan!', true);
          } else {
             alert(response.message || 'Biodata berhasil disimpan!');
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
        console.log("Error:", xhr.responseText);
        showModal(
          "Terjadi kesalahan: " + error,
          `${APP_URL}/Assets/gif/failed.gif`
        );
      },
      complete: function() {
        if (lainnyaOption) {
          lainnyaOption.value = "Lainnya";
        }
      }
    });
  });
});

// Fungsi Update Pilihan Kelas Berdasarkan Gender
function updateKelasOptions() {
  const genderElement = document.querySelector('input[name="gender"]:checked');

  if (!genderElement) return; // Hindari error jika tidak ada input gender

  const gender = genderElement.value;
  const kelasSelect = document.getElementById("floatingSelect");
  const currentValue = kelasSelect.value;

  kelasSelect.innerHTML = ""; // Hapus opsi lama

  const defaultOption = document.createElement("option");
  defaultOption.text = "Pilih Kelas Anda";
  defaultOption.disabled = true;
  defaultOption.selected = true;
  kelasSelect.appendChild(defaultOption);

  let kelasOptions = [];

  if (gender === "wanita") {
    kelasOptions = ["B1", "B2", "B3", "B4", "B5"];
  } else if (gender === "pria") {
    kelasOptions = ["A1", "A2", "A3", "A4", "A5", "A6", "A7", "A8", "A9"];
  }

  let matched = false;
  kelasOptions.forEach((kelas) => {
    const option = document.createElement("option");
    option.value = kelas;
    option.text = kelas;
    if (kelas === currentValue) {
      option.selected = true;
      defaultOption.selected = false;
      matched = true;
    }
    kelasSelect.appendChild(option);
  });

  const lainnyaOption = document.createElement("option");
  lainnyaOption.value = "Lainnya";
  lainnyaOption.text = "Lainnya (Input Manual)";
  
  if (currentValue === "Lainnya" || (currentValue && currentValue !== "Pilih Kelas Anda" && !matched)) {
    lainnyaOption.selected = true;
    defaultOption.selected = false;
    $("#kelasManualContainer").show();
    $("#kelasManual").prop('required', true);
    if (currentValue !== "Lainnya") {
      $("#kelasManual").val(currentValue);
    }
  }
  
  kelasSelect.appendChild(lainnyaOption);
}