// Dependencies: common.js untuk showModal()

document.getElementById("downloadFile1").setAttribute("href", "/path/to/template_cv.pdf");

$(document).ready(function () {
  $("#berkasForm").submit(function (e) {
    e.preventDefault();
    var formData = new FormData(this);
    $.ajax({
      url: `${APP_URL}/berkas`,
      type: "POST",
      data: formData,
      dataType: "json",
      processData: false,
      contentType: false,
      success: function (response) {
        if (response.status === "success") {
          showModal(
            "Berkas berhasil disimpan",
            `${APP_URL}/Assets/gif/success.gif`
          );
          document.querySelector('a[data-page="uploadBerkas"]').click();
        } else {
          showModal(
            "Berkas gagal disimpan",
            `${APP_URL}/Assets/gif/failed.gif`
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
