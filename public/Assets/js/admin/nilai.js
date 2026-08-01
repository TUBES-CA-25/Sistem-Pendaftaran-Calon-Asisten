/**
 * admin/nilai.js
 *
 * Dipindahkan dari app/View/admin/nilai/index.php agar berkas view tetap ringkas
 * (markup saja) dan skrip halaman bisa di-cache browser.
 *
 * Bergantung pada: APP_URL (global dari layout), dom.js, ui.js.
 * Handler memakai dom.on() = delegasi di document, sehingga aman
 * dieksekusi ulang ketika SPA menyuntik ulang konten.
 */
// Vanilla JS (tanpa jQuery). dom.on() = delegasi di document -> idempoten
// terhadap SPA re-inject #content.
(function() {
    let currentMahasiswaId = null;

    // Kelas tombol filter — dipakai bergantian saat reset/aktif
    const FILTER_ON  = 'bg-blue-600 border-blue-600 text-white hover:bg-blue-700';
    const FILTER_OFF = 'bg-white border-slate-200 text-slate-600 hover:text-blue-600 hover:border-blue-600 hover:bg-blue-50';

    // Helper function to get full image URL
    function getImageUrl(path) {
        if (!path) return '';
        if (path.startsWith('http') || path.startsWith('/')) {
            return path;
        }
        const baseUrl = APP_URL.replace('/public', '');
        return baseUrl + '/' + path;
    }

    /** Kembalikan semua tombol filter ke keadaan awal ("semua" aktif). */
    function resetFilterButtons() {
        dom.qsa('.filter-btn').forEach(function(btn) {
            dom.removeClass(btn, FILTER_ON);
            dom.addClass(btn, FILTER_OFF);
        });
        const semua = dom.qs('.filter-btn[data-filter="semua"]');
        if (semua) {
            dom.removeClass(semua, FILTER_OFF);
            dom.addClass(semua, FILTER_ON);
        }
        if (window.vp_soal) {
            window.vp_soal.customFilterFn = null;
        }
    }

    // Search functionality
    dom.on('input', '#searchInput', function() {
        const searchTerm = this.value.toLowerCase();
        dom.qsa('#tableNilai tbody tr').forEach(function(row) {
            const cells = row.querySelectorAll('td');
            const nama = cells[1] ? cells[1].textContent.toLowerCase() : '';
            const stambuk = cells[2] ? cells[2].textContent.toLowerCase() : '';
            dom.toggle(row, nama.includes(searchTerm) || stambuk.includes(searchTerm));
        });
    });

    // Input validation
    dom.on('input', '#nilaiAkhir', function() {
        const value = parseInt(this.value);
        if (value > 100) this.value = 100;
        if (value < 0) this.value = 0;
    });

    // Open Detail View
    dom.on('click', '.btn-detail', function() {
        const data = this.dataset;
        const id = data.id;
        const total = data.total;

        currentMahasiswaId = id;

        // Populate Data
        dom.text(dom.qs('#detailNama'), data.nama);
        dom.text(dom.qs('#detailStambuk'), data.stambuk);
        dom.text(dom.qs('#detailTotalNilai'), total || 'Belum dinilai');
        dom.val(dom.qs('#nilaiAkhir'), total || '');

        resetFilterButtons();

        // Loading State for Questions
        dom.html(dom.qs('#soalJawabanList'), '<tr><td colspan="5" class="py-12 text-center text-slate-400">Memuat data...</td></tr>');

        // Fetch Questions
        dom.postJSON(APP_URL + '/getsoaljawaban', { id: id }).then(function(response) {
                if (response.status === 'success' && response.data.length> 0) {
                    let html = '';
                    let totalSoal = response.data.length;
                    let benarCount = 0;
                    let salahCount = 0;
                    let tidakDijawabCount = 0;
                    let pgBenarCount = 0;
                    let pgSalahCount = 0;

                    // Sort data: Pilihan Ganda first, then Essay
                    response.data.sort((a, b) => {
                        const typeA = a.status_soal || 'essay';
                        const typeB = b.status_soal || 'essay';
                        if (typeA === 'pilihan_ganda' && typeB !== 'pilihan_ganda') return -1;
                        if (typeA !== 'pilihan_ganda' && typeB === 'pilihan_ganda') return 1;
                        return 0;
                    });

                    response.data.forEach((item, index) => {
                        const isAnswered = item.jawaban_user !== null && item.jawaban_user !== '';
                        const isCorrect = isAnswered && (item.jawaban === item.jawaban_user);
                        const tipeSoal = item.status_soal || 'essay';
                        const isPilihanGanda = tipeSoal === 'pilihan_ganda';

                        if (isCorrect) {
                            benarCount++;
                            if (isPilihanGanda) pgBenarCount++;
                            statusBadge = '<span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 text-emerald-700 text-xs font-bold rounded-lg border border-emerald-200"><i class="bi bi-check-circle-fill"></i> Benar</span>';
                        } else if (!isAnswered && isPilihanGanda) {
                            salahCount++;
                            pgSalahCount++;
                            statusBadge = '<span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-50 text-red-700 text-xs font-bold rounded-lg border border-red-200"><i class="bi bi-dash-circle"></i> Salah (Kosong)</span>';
                        } else if (!isAnswered) {
                            tidakDijawabCount++;
                            statusBadge = '<span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-200 text-slate-700 text-xs font-bold rounded-lg border border-slate-300"><i class="bi bi-dash-circle"></i> Kosong</span>';
                        } else {
                            salahCount++;
                            if (isPilihanGanda) pgSalahCount++;
                            statusBadge = '<span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-50 text-red-700 text-xs font-bold rounded-lg border border-red-200"><i class="bi bi-x-circle-fill"></i> Salah</span>';
                        }

                        // Format options for display
                        let pilihanHTML = '';
                        try {
                            const pilihanObj = JSON.parse(item.pilihan);
                            pilihanHTML = Object.entries(pilihanObj)
                                .map(([key, value]) => {
                                    let optClass = 'bg-slate-50 border-slate-300 text-slate-800';
                                    let icon = '';
                                    if (key === item.jawaban) {
                                        optClass = 'bg-emerald-50 border-emerald-300 text-emerald-800';
                                        icon = '<i class="bi bi-check-circle-fill text-emerald-500 float-right mt-0.5"></i>';
                                    } else if (isAnswered && key === item.jawaban_user && !isCorrect) {
                                        optClass = 'bg-red-50 border-red-300 text-red-800';
                                        icon = '<i class="bi bi-x-circle-fill text-red-500 float-right mt-0.5"></i>';
                                    }

                                    if (value && (value.includes('.jpg') || value.includes('.jpeg') || value.includes('.png') || value.includes('.gif') || value.includes('.webp'))) {
                                        return `
                                            <div class="mb-2 flex items-start gap-2 p-2.5 border rounded-xl shadow-sm w-fit ${optClass}">
                                                <strong class="font-bold">${key}.</strong>
                                                <div>
                                                    <img src="${getImageUrl(value)}" alt="Pilihan ${key}" class="max-w-full h-auto max-h-32 rounded-lg border border-slate-200">
                                                </div>
                                                ${icon}
                                            </div>
                                        `;
                                    } else {
                                        return `<div class="mb-1.5 px-3 py-2 border rounded-lg shadow-sm text-sm ${optClass}"><strong class="font-extrabold mr-1">${key}.</strong> ${value} ${icon}</div>`;
                                    }
                                })
                                .join('');
                        } catch (e) {
                            if (typeof item.pilihan === 'string' && /,\s*(?=[A-E]\.)/.test(item.pilihan)) {
                                const opts = item.pilihan.split(/,\s*(?=[A-E]\.)/);
                                pilihanHTML = opts.map(opt => `<div class="mb-1.5 text-slate-800 bg-slate-50 px-3 py-2 border border-slate-300 rounded-lg shadow-sm text-sm">${opt}</div>`).join('');
                            } else {
                                pilihanHTML = `<div class="text-slate-800 bg-slate-50 px-3 py-2 border border-slate-300 rounded-lg shadow-sm text-sm">${item.pilihan}</div>`;
                            }
                        }

                        const tipeBadge = isPilihanGanda
                            ? '<span class="inline-flex items-center gap-1 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider rounded-lg bg-blue-50 text-blue-700 border border-blue-100"><i class="bi bi-ui-checks"></i> PG</span>'
                            : '<span class="inline-flex items-center gap-1 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider rounded-lg bg-amber-50 text-amber-700 border border-amber-100"><i class="bi bi-pencil-square"></i> Essay</span>';

                        const hasImage = item.image_url && item.image_url.trim() !== '';

                        let numberClasses = '';
                        if (isCorrect) {
                            numberClasses = 'bg-emerald-100 text-emerald-800 border-emerald-300';
                        } else if (!isAnswered) {
                            numberClasses = 'bg-slate-200 text-slate-800 border-slate-300';
                        } else {
                            numberClasses = 'bg-red-100 text-red-800 border-red-300';
                        }
                        let rowBg = 'bg-white hover:bg-slate-50';
                        if (isCorrect) {
                            rowBg = 'bg-emerald-50/40 hover:bg-emerald-50/80';
                        } else if (isAnswered && !isCorrect) {
                            rowBg = 'bg-red-50/40 hover:bg-red-50/80';
                        }
                        
                        html += `
                            <tr class="soal-item transition-colors border-b border-slate-200 ${rowBg}" data-tipe="${tipeSoal}">
                                <td class="p-4 align-top text-center">
                                    <div class="w-8 h-8 mx-auto ${numberClasses} rounded-lg flex items-center justify-center font-bold text-sm shadow-sm border">${index + 1}</div>
                                </td>
                                <td class="p-4 align-top">
                                    ${tipeBadge}
                                </td>
                                <td class="p-4 align-top">
                                    ${hasImage ? `
                                    <div class="mb-3">
                                        <img src="${getImageUrl(item.image_url)}" alt="Gambar Soal ${index + 1}" class="max-w-xs h-auto max-h-48 rounded-xl border border-slate-200 object-cover shadow-sm">
                                    </div>
                                    ` : ''}
                                    <div class="font-bold text-slate-800 text-sm mb-3">${item.deskripsi}</div>
                                    
                                    ${isPilihanGanda ? `
                                    <div class="space-y-1.5 mt-3">
                                        ${pilihanHTML}
                                    </div>
                                    ` : ''}
                                </td>
                                <td class="p-4 align-top space-y-4">
                                    <div>
                                        <div class="text-blue-500 font-bold uppercase tracking-wider text-[10px] mb-1.5 flex items-center gap-1.5"><i class="bi bi-shield-check"></i> Kunci</div>
                                        <div class="inline-block px-3 py-1.5 bg-blue-50 text-blue-700 border border-blue-200 font-bold text-sm rounded-lg shadow-sm">${item.jawaban}</div>
                                    </div>
                                    <div>
                                        <div class="text-indigo-600 font-bold uppercase tracking-wider text-[10px] mb-1.5 flex items-center gap-1.5"><i class="bi bi-card-text"></i> Jawaban</div>
                                        ${isAnswered
                                            ? `<div class="inline-block px-3 py-1.5 ${isCorrect ? 'bg-emerald-50 text-emerald-800 border-emerald-400' : 'bg-red-50 text-red-800 border-red-400'} border font-bold text-sm rounded-lg shadow-sm">${item.jawaban_user}</div>`
                                            : '<div class="inline-block px-3 py-1.5 bg-slate-200 text-slate-500 font-bold text-sm rounded-lg border border-slate-300 border-dashed shadow-sm">KOSONG</div>'
                                        }
                                    </div>
                                </td>
                                <td class="p-4 align-top text-center">
                                    ${statusBadge}
                                </td>
                            </tr>
                        `;
                    });

                    // Update stats
                    dom.text(dom.qs('#statTotal'), totalSoal);
                    dom.text(dom.qs('#statBenar'), benarCount);
                    dom.text(dom.qs('#statSalah'), salahCount);
                    dom.text(dom.qs('#statTidakDijawab'), tidakDijawabCount);
                    dom.text(dom.qs('#statPgBenar'), pgBenarCount);
                    dom.text(dom.qs('#statPgSalah'), pgSalahCount);

                    dom.html(dom.qs('#soalJawabanList'), html);

                    if (window.vp_soal) {
                        window.vp_soal.customFilterFn = null;
                        if (window.vp_soal._searchInput) {
                            window.vp_soal._searchInput.value = '';
                            window.vp_soal.searchQuery = '';
                        }
                        window.vp_soal.updateData();
                    } else {
                        window.vp_soal = new VanillaPaginator('soalJawabanTable', { defaultPerPage: 10, searchable: true });
                    }
                } else {
                    dom.html(dom.qs('#soalJawabanList'), '<tr><td colspan="5" class="py-12 text-center text-slate-400">Tidak ada data soal dan jawaban.</td></tr>');
                    ['#statTotal','#statBenar','#statSalah','#statTidakDijawab','#statPgBenar','#statPgSalah']
                        .forEach(function(sel) { dom.text(dom.qs(sel), 0); });

                    if (window.vp_soal) window.vp_soal.updateData();
                }
        }).catch(function() {
            dom.html(dom.qs('#soalJawabanList'), '<tr><td colspan="5" class="py-12 text-center text-red-500">Gagal memuat data.</td></tr>');
        });

        dom.addClass(dom.qs('#view-list'), 'hidden');
        dom.removeClass(dom.qs('#view-detail'), 'hidden');
        window.scrollTo(0, 0);
    });

    // Back Button Logic
    dom.on('click', '#btnBack', function() {
        dom.addClass(dom.qs('#view-detail'), 'hidden');
        dom.removeClass(dom.qs('#view-list'), 'hidden');
        currentMahasiswaId = null;
    });

    // Filter Button Logic
    dom.on('click', '.filter-btn', function() {
        const filter = this.dataset.filter;

        // Update active state style
        dom.qsa('.filter-btn').forEach(function(b) {
            dom.removeClass(b, FILTER_ON);
            dom.addClass(b, FILTER_OFF);
        });
        dom.removeClass(this, FILTER_OFF);
        dom.addClass(this, FILTER_ON);

        if (window.vp_soal) {
            window.vp_soal.setFilter(row => {
                if (filter === 'semua') return true;
                return row.getAttribute('data-tipe') === filter;
            });
        }
    });

    // Submit score form
    dom.on('submit', '#formNilaiAkhir', function(e) {
        e.preventDefault();
        const nilaiAkhir = dom.val(dom.qs('#nilaiAkhir'));

        if (!currentMahasiswaId) {
            showAlert('ID calon asisten tidak ditemukan', false);
            return;
        }

        if (typeof nilaiAkhir === 'undefined' || nilaiAkhir === '') {
            showAlert('Mohon masukkan nilai', false);
            return;
        }

        const btnSubmit = this.querySelector('button[type="submit"]');
        const originalBtnText = btnSubmit ? btnSubmit.innerHTML : '';
        if (btnSubmit) {
            btnSubmit.disabled = true;
            btnSubmit.innerHTML = '<i class="bi bi-hourglass-split animate-spin mr-1"></i> Menyimpan...';
        }

        function restoreBtn() {
            if (btnSubmit) {
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = originalBtnText;
            }
        }

        dom.postJSON(APP_URL + '/updatenilaiakhir', { id: currentMahasiswaId, nilai: nilaiAkhir })
            .then(function(response) {
                restoreBtn();

                if (response.status === 'success') {
                    showAlert('Nilai berhasil disimpan!', true);

                    // Real-time Update Logic in Table
                    const btn = dom.qs(`.btn-detail[data-id="${currentMahasiswaId}"]`);
                    const tr = btn ? btn.closest('tr') : null;

                    if (tr) {
                        let badgeClass = 'text-slate-500 bg-slate-50 border border-slate-200';
                        let statusText = 'Belum Dinilai';

                        if (nilaiAkhir !== '') {
                            const score = parseInt(nilaiAkhir);
                            if (score >= 70) {
                                badgeClass = 'text-emerald-700 bg-emerald-50 border border-emerald-100';
                                statusText = 'Memenuhi';
                            } else {
                                badgeClass = 'text-red-700 bg-red-50 border border-red-100';
                                statusText = 'Tidak Memenuhi';
                            }
                        }

                        const cells = tr.querySelectorAll('td');
                        // Kolom Nilai Akhir (indeks 3)
                        if (cells[3]) cells[3].innerHTML = `<span class="inline-block px-3 py-1 bg-blue-50 text-blue-700 border border-blue-100 text-xs font-semibold rounded-lg">${nilaiAkhir}</span>`;
                        // Kolom Status (indeks 4)
                        if (cells[4]) cells[4].innerHTML = `<span class="inline-block px-3 py-1.5 text-xs font-semibold rounded-lg ${badgeClass}">${statusText}</span>`;

                        // Perbarui atribut data pada tombol
                        btn.dataset.total = nilaiAkhir;

                        // Update detail text
                        dom.text(dom.qs('#detailTotalNilai'), nilaiAkhir);
                    }
                } else {
                    showAlert(response.message || 'Gagal menyimpan nilai', false);
                }
            })
            .catch(function() {
                restoreBtn();
                showAlert('Terjadi kesalahan saat menyimpan nilai', false);
            });
    });
})();
