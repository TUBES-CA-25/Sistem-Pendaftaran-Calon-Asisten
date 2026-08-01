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

// Helper tampil/sembunyi memakai class Tailwind `hidden`
// (dulu $().show()/$().hide() yang menulis style display inline).
function _show(el) { if (el) el.classList.remove("hidden"); }
function _hide(el) { if (el) el.classList.add("hidden"); }

// Delegasi di document: idempoten terhadap SPA re-inject #content.
document.addEventListener("click", function (e) {
  const logoutBtn = e.target.closest("#logoutButton");
  if (!logoutBtn) return;
  e.preventDefault();

  showActionConfirmation({
    title: 'Konfirmasi Keluar',
    message: 'Apakah Anda yakin ingin keluar dari aplikasi?',
    btnText: 'Keluar',
    type: 'danger',
    onConfirm: function () {
      fetch(`${APP_URL}/logout`, { method: "POST" })
        .finally(function () { window.location.href = APP_URL; });
    }
  });
});

document.addEventListener("change", function (e) {
  const sel = e.target.closest("#floatingSelect");
  if (!sel) return;

  const container = document.getElementById("kelasManualContainer");
  const manual = document.getElementById("kelasManual");
  if (sel.value === "Lainnya") {
    _show(container);
    if (manual) manual.required = true;
  } else {
    _hide(container);
    if (manual) { manual.required = false; manual.value = ""; }
  }
});

function _initBiodataPage() {
  const telephoneInput = document.getElementById("telephone");
  const tempatLahirInput = document.getElementById("tempatlahir");
  const namaInput = document.getElementById("nama");

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

}

// Jalankan init saat load pertama maupun setelah SPA menyuntik konten baru
document.addEventListener("DOMContentLoaded", _initBiodataPage);
window._initBiodataPage = _initBiodataPage;

document.addEventListener("submit", async function (e) {
    const formEl = e.target.closest("#biodataForm");
    if (!formEl) return;
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
    try {
      // Pengganti $("#biodataForm").serialize()
      const params = new URLSearchParams(new FormData(formEl)).toString();

      const res = await fetch(`${APP_URL}/store`, {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: params,
      });
      const response = await res.json();

      if (response.status === "success") {
        if (typeof showAlert === 'function') {
          showAlert(response.message || 'Biodata berhasil disimpan!', true);
        } else {
          alert(response.message || 'Biodata berhasil disimpan!');
        }

        // Reload page to reflect changes
        if (typeof loadPage === 'function') {
          loadPage('biodata');
        } else {
          const link = document.querySelector('a[data-page="biodata"]');
          if (link) link.click();
        }
      } else {
        showModal(
          response.message || "Biodata gagal disimpan",
          `${APP_URL}/Assets/gif/failed.gif`
        );
      }
    } catch (error) {
      console.log("Error:", error);
      showModal(
        "Terjadi kesalahan: " + error.message,
        `${APP_URL}/Assets/gif/failed.gif`
      );
    } finally {
      if (lainnyaOption) {
        lainnyaOption.value = "Lainnya";
      }
    }
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
    const manualContainer = document.getElementById("kelasManualContainer");
    const manualInput = document.getElementById("kelasManual");
    _show(manualContainer);
    if (manualInput) {
      manualInput.required = true;
      if (currentValue !== "Lainnya") manualInput.value = currentValue;
    }
  }
  
  kelasSelect.appendChild(lainnyaOption);
}