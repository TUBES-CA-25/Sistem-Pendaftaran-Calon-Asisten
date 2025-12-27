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
    });

    window.addEventListener('click', (event) => {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });
}

document.getElementById("downloadFile1").setAttribute("href", "/path/to/template_cv.pdf");

$(document).ready(function () {
  $("#berkasForm").submit(function (e) {
    e.preventDefault();
    var formData = new FormData(this);
    $.ajax({
      url: "/tubes_web/public/berkas",
      type: "POST",
      data: formData,
      dataType: "json",
      processData: false,
      contentType: false,
      success: function (response) {
        if (response.status === "success") {
          showModal(
            "Berkas berhasil disimpan",
            "/tubes_web/public/Assets/gif/success.gif"
          );
          document.querySelector('a[data-page="uploadBerkas"]').click();
        } else {
          showModal(
            "Berkas gagal disimpan",
            "/tubes_web/public/Assets/gif/failed.gif"
          );
          console.log(response.message);
        }
      },
      error: function (xhr, status, error) {
        console.log("Error Status:", status);
        console.log("Error Details:", error);
        console.log("Server Response:", xhr.responseText);
        alert("Terjadi kesalahan: " + error);
      },
    });
  });
});
