/**
 * admin/pengajuan-judul.js
 *
 * Dipindahkan dari app/View/admin/judul/index.php agar berkas view tetap ringkas
 * (markup saja) dan skrip halaman bisa di-cache browser.
 *
 * Bergantung pada: APP_URL (global dari layout), dom.js, ui.js.
 * Handler memakai dom.on() = delegasi di document, sehingga aman
 * dieksekusi ulang ketika SPA menyuntik ulang konten.
 */
// Vanilla JS (tanpa jQuery). Semua handler memakai dom.on() = delegasi di
// document, sehingga tetap aktif setelah SPA menyuntik ulang #content.
(function () {
    // APP_URL sudah tersedia sebagai konstanta global dari layout.
    let currentMessageId = null, currentUserId = null;

    // Simpan URL unduhan tanpa jQuery .data()
    let pptUrl = '', makalahUrl = '';

    dom.on('keyup', '#searchPengajuan', function () {
        const term = this.value.toLowerCase();
        dom.qsa('#tablePengajuan tbody tr').forEach(function (row) {
            dom.toggle(row, row.textContent.toLowerCase().indexOf(term) > -1);
        });
    });

    dom.on('click', '.btn-detail-pengajuan', function () {
        const data = this.dataset;
        currentUserId = data.userid;

        dom.text(dom.qs('#detailNama'), data.nama);
        dom.text(dom.qs('#detailStambuk'), data.stambuk);
        dom.text(dom.qs('#detailJudul'), data.judul);

        pptUrl = data.ppt || '';
        makalahUrl = data.makalah || '';

        const accept = dom.qs('#btnModalAccept');
        const reject = dom.qs('#btnModalReject');

        const ACCEPT_ON  = 'bg-white border border-emerald-500 text-emerald-600 hover:bg-emerald-50 shadow-sm';
        const ACCEPT_OFF = 'bg-slate-100 text-slate-400 border border-slate-200 cursor-not-allowed';
        const REJECT_ON  = 'bg-red-600 hover:bg-red-700 text-white shadow-md shadow-red-500/10';
        const REJECT_OFF = 'bg-slate-100 text-slate-400 border border-slate-200 cursor-not-allowed';

        function setBtn(el, onCls, offCls, disabled, innerHtml) {
            if (!el) return;
            el.className = 'px-5 py-2.5 font-semibold text-sm rounded-xl transition flex items-center justify-center gap-2 ' + (disabled ? offCls : onCls);
            el.disabled = disabled;
            el.innerHTML = innerHtml;
        }

        if (data.status == 1) {          // Diterima
            setBtn(accept, ACCEPT_ON, ACCEPT_OFF, true,  '<i class="bi bi-lock-fill"></i> Berhasil Diterima');
            setBtn(reject, REJECT_ON, REJECT_OFF, false, '<i class="bi bi-x-circle"></i> Tolak Judul');
        } else if (data.status == 2) {   // Ditolak
            setBtn(reject, REJECT_ON, REJECT_OFF, true,  '<i class="bi bi-lock-fill"></i> Berhasil Ditolak');
            setBtn(accept, ACCEPT_ON, ACCEPT_OFF, false, '<i class="bi bi-check-circle-fill"></i> Berhasil Diterima');
        } else {                          // Belum diproses
            setBtn(accept, ACCEPT_ON, ACCEPT_OFF, false, '<i class="bi bi-check-circle-fill"></i> Berhasil Diterima');
            setBtn(reject, REJECT_ON, REJECT_OFF, false, '<i class="bi bi-x-circle"></i> Tolak Judul');
        }

        UI.modal.open('#detailPengajuanModal');
    });

    dom.on('click', '#btnDownloadPpt', function () {
        if (pptUrl) window.location.href = APP_URL.replace(/\/public$/, '') + '/res/pptUser/' + pptUrl;
        else showAlert('File tidak tersedia', false);
    });
    dom.on('click', '#btnDownloadMakalah', function () {
        if (makalahUrl) window.location.href = APP_URL.replace(/\/public$/, '') + '/res/makalahUser/' + makalahUrl;
        else showAlert('File tidak tersedia', false);
    });

    /** Alur terima/tolak judul: tutup modal detail dulu, lalu konfirmasi. */
    function handleDecision(status, opts) {
        const nama = dom.text(dom.qs('#detailNama'));
        UI.modal.close('#detailPengajuanModal');

        setTimeout(function () {
            showActionConfirmation({
                title: opts.title,
                message: opts.message(nama),
                btnText: opts.btnText,
                type: opts.type,
                onConfirm: function () {
                    dom.postJSON(APP_URL + '/updatestatus', { id: currentUserId, status: status })
                        .then(function (res) {
                            if (res.status === 'success') {
                                showAlert(opts.okMessage, true);
                                setTimeout(function () { location.reload(); }, 1000);
                            } else {
                                showAlert(res.message || opts.failMessage, false);
                            }
                        })
                        .catch(function () { showAlert(opts.failMessage, false); });
                }
            });
        }, 300); // beri jeda agar modal detail selesai menutup
    }

    dom.on('click', '#btnModalAccept', function () {
        handleDecision(1, {
            title: 'Terima Judul',
            message: function (nama) {
                return `Anda akan menerima judul untuk:<br><strong class="text-lg text-slate-800 font-bold block my-2">${nama}</strong><span class="text-emerald-600 text-xs font-semibold"><i class="bi bi-check-circle mr-1"></i>Judul memenuhi kriteria</span>`;
            },
            btnText: 'Terima Judul',
            type: 'success',
            okMessage: 'Judul berhasil diterima!',
            failMessage: 'Gagal menerima judul'
        });
    });

    dom.on('click', '#btnModalReject', function () {
        handleDecision(2, {
            title: 'Tolak Judul',
            message: function (nama) {
                return `Anda akan menolak judul untuk:<br><strong class="text-lg text-slate-800 font-bold block my-2">${nama}</strong><span class="text-red-600 text-xs font-semibold"><i class="bi bi-exclamation-triangle mr-1"></i>Mahasiswa akan diminta mengajukan ulang</span>`;
            },
            btnText: 'Tolak Judul',
            type: 'danger',
            okMessage: 'Judul berhasil ditolak!',
            failMessage: 'Gagal menolak judul'
        });
    });

    dom.on('click', '.btn-send-message', function () {
        currentMessageId = this.dataset.id;
        currentUserId = this.dataset.userid;
        dom.val(dom.qs('#messageContent'), '');
        UI.modal.open('#sendMessageModal');
    });

    dom.on('submit', '#formSendMessage', function (e) {
        e.preventDefault();
        dom.postJSON(APP_URL + '/updatepresentasi', {
            id: currentMessageId,
            userid: currentUserId,
            message: dom.val(dom.qs('#messageContent'))
        }).then(function (res) {
            UI.modal.close('#sendMessageModal');
            if (res.status === 'success') {
                showAlert('Pesan berhasil terkirim!', true);
            } else {
                showAlert(res.message || 'Gagal mengirim pesan', false);
            }
        }).catch(function () {
            UI.modal.close('#sendMessageModal');
            showAlert('Gagal mengirim pesan', false);
        });
    });
})();
