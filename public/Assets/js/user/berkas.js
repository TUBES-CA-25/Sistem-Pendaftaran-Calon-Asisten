// Dependencies: common.js untuk showModal()
// Vanilla JS — tidak lagi memakai jQuery (dulu memanggil $ padahal layout user
// tidak pernah memuat jQuery, sehingga form ini melempar "$ is not defined").
(function () {
    const downloadLink = `${APP_URL}/Assets/Downloads/Template%20CV%20Indo%20ATS.doc`;
    const docElement = document.getElementById("downloadFile1");
    if (docElement) {
        docElement.setAttribute("href", downloadLink);
    }

    // Delegasi di document: idempoten terhadap SPA re-inject (#content diganti
    // oleh app.js), sekaligus menggantikan pola $(...).off('submit').submit(...)
    document.addEventListener("submit", async function (e) {
        const form = e.target.closest("#berkasForm");
        if (!form) return;

        e.preventDefault();
        const formData = new FormData(form);

        try {
            const res = await fetch(`${APP_URL}/berkas`, {
                method: "POST",
                body: formData,
            });
            const response = await res.json();

            if (response.status === "success") {
                showModal(
                    "Berkas berhasil disimpan",
                    "/Sistem-Pendaftaran-Calon-Asisten/public/Assets/gif/success.gif",
                    function () {
                        const link = document.querySelector('a[data-page="uploadBerkas"]');
                        if (link) link.click();
                    }
                );
            } else {
                showModal(
                    "Berkas gagal disimpan",
                    "/Sistem-Pendaftaran-Calon-Asisten/public/Assets/gif/failed.gif"
                );
                console.log(response.message);
            }
        } catch (error) {
            console.log("Error Details:", error);
            if (typeof showAlert === "function") {
                showAlert("Terjadi kesalahan: " + error.message, false);
            } else {
                alert("Terjadi kesalahan: " + error.message);
            }
        }
    });
})();
