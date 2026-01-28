// Dependencies: common.js untuk showModal()
(function() {
    const downloadLink = `${APP_URL}/Assets/Downloads/Template%20CV%20Indo%20ATS.doc`;
    const docElement = document.getElementById("downloadFile1");
    if(docElement) {
        docElement.setAttribute("href", downloadLink);
    }

    $(document).ready(function () {
      $("#berkasForm").off('submit').submit(function (e) {
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
                "/Sistem-Pendaftaran-Calon-Asisten/public/Assets/gif/success.gif",
                function() {
                   document.querySelector('a[data-page="uploadBerkas"]').click();
                }
              );
            } else {
              showModal(
                "Berkas gagal disimpan",
                "/Sistem-Pendaftaran-Calon-Asisten/public/Assets/gif/failed.gif"
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
})();
