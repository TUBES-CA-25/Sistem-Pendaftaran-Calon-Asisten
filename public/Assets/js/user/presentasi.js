// Dependencies: common.js untuk showModal()
// Vanilla JS — tidak lagi memakai jQuery (layout user tidak pernah memuat jQuery,
// sehingga kedua form ini sebelumnya melempar "$ is not defined").
(function () {
    // Delegasi di document supaya tetap bekerja setelah SPA re-inject #content
    document.addEventListener("submit", async function (e) {
        const form = e.target;

        // ── Form pengajuan judul ────────────────────────────────────────
        if (form.id === "berkasPresentasiForm") {
            e.preventDefault();

            // Pengganti $(this).serialize()
            const params = new URLSearchParams(new FormData(form)).toString();

            try {
                const res = await fetch("/Sistem-Pendaftaran-Calon-Asisten/public/judul", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: params,
                });
                const response = await res.json();

                if (response.status === "success") {
                    showModal(
                        response.message || "Data berhasil disimpan",
                        "/Sistem-Pendaftaran-Calon-Asisten/public/Assets/gif/success.gif"
                    );
                    const link = document.querySelector('a[data-page="presentasi"]');
                    if (link) link.click();
                } else {
                    showModal(
                        response.message || "Data gagal disimpan",
                        "/Sistem-Pendaftaran-Calon-Asisten/public/Assets/gif/failed.gif"
                    );
                }
            } catch (error) {
                console.log("Error:", error);
                if (typeof showAlert === "function") {
                    showAlert("Terjadi kesalahan: " + error.message, false);
                }
            }
            return;
        }

        // ── Form upload berkas presentasi ───────────────────────────────
        if (form.id === "presentasiFormAccepted") {
            e.preventDefault();

            const formData = new FormData(form);

            try {
                const res = await fetch("/Sistem-Pendaftaran-Calon-Asisten/public/presentasi", {
                    method: "POST",
                    body: formData,
                });
                const response = await res.json();

                showModal(
                    response.message || "Data berhasil disimpan",
                    "/Sistem-Pendaftaran-Calon-Asisten/public/Assets/gif/success.gif"
                );
                const link = document.querySelector('a[data-page="presentasi"]');
                if (link) link.click();
            } catch (error) {
                console.error("Error:", error);
            }
        }
    });
})();
