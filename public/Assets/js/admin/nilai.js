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

    /**
     * Tulis nilai akhir di kartu identitas.
     * Angka besar dipakai untuk nilainya saja; kata "Belum dinilai" ditaruh di
     * baris keterangan supaya tidak memaksa kartu melebar.
     */
    function tampilkanNilaiAkhir(total) {
        const ada = total !== null && typeof total !== 'undefined' && total !== '';
        dom.text(dom.qs('#detailTotalNilai'), ada ? total : '—');
        const ket = dom.qs('#detailNilaiKet');
        if (ket) ket.textContent = ada ? 'dari 100' : 'Belum dinilai';
    }

    /**
     * Ubah kolom `pilihan` menjadi daftar pasangan [huruf, isi].
     *
     * Dua bentuk yang beredar di data:
     *   1. JSON objek  -> {"A":"Variabel","B":"Operator"}
     *   2. satu string -> "A. Variabel, B. Operator, C. Identifier"
     * Bentuk kedua yang dipakai tabel `soal` saat ini.
     *
     * Kembalikan array kosong bila tidak ada bentuk yang cocok, sehingga
     * pemanggil bisa menampilkan teks aslinya apa adanya.
     */
    function uraikanPilihan(pilihan) {
        if (!pilihan) return [];

        try {
            const obj = JSON.parse(pilihan);
            if (obj && typeof obj === 'object' && !Array.isArray(obj)) {
                return Object.entries(obj);
            }
        } catch (e) { /* bukan JSON - coba bentuk string di bawah */ }

        if (typeof pilihan !== 'string') return [];

        const hasil = [];
        pilihan.split(/,\s*(?=[A-Ea-e]\s*[.)])/).forEach(function(bagian) {
            const cocok = bagian.match(/^\s*([A-Ea-e])\s*[.)]\s*([\s\S]*)$/);
            if (cocok) hasil.push([cocok[1].toUpperCase(), cocok[2].trim()]);
        });
        return hasil;
    }

    /** Isi bar akurasi dari angka yang sudah dihitung untuk kartu statistik. */
    function setAkurasi(benar, total) {
        const persen = total > 0 ? Math.round((benar / total) * 100) : 0;
        dom.text(dom.qs('#detailPersen'), persen);
        const bar = dom.qs('#detailProgress');
        if (bar) bar.style.width = persen + '%';
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
        const foto = dom.qs('#detailFoto');
        if (foto && data.foto) foto.src = data.foto;
        tampilkanNilaiAkhir(total);
        dom.val(dom.qs('#nilaiAkhir'), total || '');

        // Bar akurasi dikosongkan dulu supaya angka peserta sebelumnya tidak
        // sempat terbaca sebagai milik peserta yang baru dibuka.
        setAkurasi(0, 0);

        resetFilterButtons();

        // Loading State for Questions
        dom.html(dom.qs('#soalJawabanList'), '<tr><td colspan="1" class="py-12 text-center text-slate-400">Memuat data...</td></tr>');

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
                        const tipeSoal = item.status_soal || 'essay';
                        const isPilihanGanda = tipeSoal === 'pilihan_ganda';
                        
                        let isCorrect = false;
                        let skorAdmin = null;
                        const poin = item.poin_per_soal ? parseInt(item.poin_per_soal) : 10;
                        const poinMendekati = Math.floor(poin / 2);

                        if (isPilihanGanda) {
                            isCorrect = isAnswered && (item.jawaban === item.jawaban_user);
                        } else {
                            skorAdmin = item.skor_admin !== null && item.skor_admin !== undefined ? parseInt(item.skor_admin) : null;
                            isCorrect = skorAdmin !== null && skorAdmin === poin;
                        }

                        let statusBadge;
                        if (isPilihanGanda) {
                            // Pilihan Ganda logic
                            if (!isAnswered) {
                                salahCount++;
                                pgSalahCount++;
                                statusBadge = '<span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-50 text-red-700 text-xs font-bold rounded-lg border border-red-200"><i class="bi bi-dash-circle"></i> Salah (Kosong)</span>';
                            } else if (isCorrect) {
                                benarCount++;
                                pgBenarCount++;
                                statusBadge = '<span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 text-emerald-700 text-xs font-bold rounded-lg border border-emerald-200"><i class="bi bi-check-circle-fill"></i> Benar</span>';
                            } else {
                                salahCount++;
                                pgSalahCount++;
                                statusBadge = '<span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-50 text-red-700 text-xs font-bold rounded-lg border border-red-200"><i class="bi bi-x-circle-fill"></i> Salah</span>';
                            }
                        } else {
                            // Essay logic — prioritize skorAdmin over isAnswered
                            if (skorAdmin !== null) {
                                if (skorAdmin === 0) {
                                    salahCount++;
                                    statusBadge = '<span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-50 text-red-700 text-xs font-bold rounded-lg border border-red-200"><i class="bi bi-x-circle-fill"></i> Salah</span>';
                                } else if (skorAdmin === poinMendekati) {
                                    statusBadge = '<span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-50 text-amber-700 text-xs font-bold rounded-lg border border-amber-200"><i class="bi bi-exclamation-circle-fill"></i> Mendekati Benar</span>';
                                } else if (skorAdmin === poin) {
                                    benarCount++;
                                    statusBadge = '<span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 text-emerald-700 text-xs font-bold rounded-lg border border-emerald-200"><i class="bi bi-check-circle-fill"></i> Benar</span>';
                                } else {
                                    // skor_admin has a value but doesn't match any preset
                                    statusBadge = `<span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 text-blue-700 text-xs font-bold rounded-lg border border-blue-200"><i class="bi bi-pencil-square"></i> Skor: ${skorAdmin}</span>`;
                                }
                            } else if (!isAnswered) {
                                tidakDijawabCount++;
                                statusBadge = '<span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-200 text-slate-700 text-xs font-bold rounded-lg border border-slate-300"><i class="bi bi-dash-circle"></i> Kosong</span>';
                            } else {
                                statusBadge = '<span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 text-slate-600 text-xs font-bold rounded-lg border border-slate-200"><i class="bi bi-clock-history"></i> Belum Dinilai</span>';
                            }
                        }

                        let essayScoringHtml = '';
                        if (!isPilihanGanda) {
                            const isSalah = skorAdmin === 0;
                            const isMendekati = skorAdmin === poinMendekati;
                            const isBenar = skorAdmin === poin;

                            essayScoringHtml = `
                            <div class="mt-4 pt-3 border-t border-slate-100">
                                <div class="text-slate-500 font-bold uppercase tracking-wider text-[10px] mb-2 flex items-center gap-1.5">
                                    <i class="bi bi-star"></i> Nilai Essay (Bobot: ${poin} Pts)
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    <button type="button" onclick="window.updateEssayScore(${item.id}, ${id}, 0, this)" class="px-3 py-1.5 text-xs font-bold rounded-lg border transition-colors ${isSalah ? 'bg-red-500 text-white border-red-500' : 'bg-white text-slate-600 border-slate-300 hover:bg-slate-50'}">
                                        Salah (0 pts)
                                    </button>
                                    <button type="button" onclick="window.updateEssayScore(${item.id}, ${id}, ${poinMendekati}, this)" class="px-3 py-1.5 text-xs font-bold rounded-lg border transition-colors ${isMendekati ? 'bg-amber-500 text-white border-amber-500' : 'bg-white text-slate-600 border-slate-300 hover:bg-slate-50'}">
                                        Mendekati Benar (${poinMendekati} pts)
                                    </button>
                                    <button type="button" onclick="window.updateEssayScore(${item.id}, ${id}, ${poin}, this)" class="px-3 py-1.5 text-xs font-bold rounded-lg border transition-colors ${isBenar ? 'bg-emerald-500 text-white border-emerald-500' : 'bg-white text-slate-600 border-slate-300 hover:bg-slate-50'}">
                                        Benar (${poin} pts)
                                    </button>
                                </div>
                            </div>`;
                        }


                        // Susun opsi menjadi pasangan [huruf, isi] lebih dulu.
                        //
                        // Kolom `pilihan` di basis data TIDAK menyimpan JSON melainkan
                        // satu string "A. Variabel, B. Operator, ...". Akibatnya
                        // JSON.parse selalu gagal dan render jatuh ke cabang cadangan
                        // yang menampilkan opsi polos tanpa penanda apa pun - itulah
                        // sebabnya kunci jawaban tidak pernah tersorot hijau di layar.
                        // Kedua bentuk kini diseragamkan supaya sorotannya jalan.
                        const daftarOpsi = uraikanPilihan(item.pilihan);

                        let pilihanHTML = '';
                        if (daftarOpsi.length > 0) {
                            pilihanHTML = daftarOpsi
                                .map(([key, value]) => {
                                    // Huruf pilihan dijadikan lencana bulat: kunci dan
                                    // jawaban keliru langsung kelihatan tanpa harus
                                    // membaca teksnya lebih dulu.
                                    let optClass = 'bg-white border-slate-200 text-slate-700';
                                    let hurufClass = 'bg-slate-100 border-slate-200 text-slate-500';
                                    let icon = '';
                                    if (key === item.jawaban) {
                                        optClass = 'bg-emerald-50 border-emerald-300 text-emerald-900';
                                        hurufClass = 'bg-emerald-500 border-emerald-500 text-white';
                                        icon = '<i class="bi bi-check-circle-fill text-emerald-500 shrink-0 mt-0.5"></i>';
                                    } else if (isAnswered && key === item.jawaban_user && !isCorrect) {
                                        optClass = 'bg-red-50 border-red-300 text-red-900';
                                        hurufClass = 'bg-red-500 border-red-500 text-white';
                                        icon = '<i class="bi bi-x-circle-fill text-red-500 shrink-0 mt-0.5"></i>';
                                    }

                                    const huruf = `<span class="shrink-0 w-6 h-6 rounded-lg border flex items-center justify-center text-xs font-extrabold ${hurufClass}">${key}</span>`;
                                    const isi = (value && (value.includes('.jpg') || value.includes('.jpeg') || value.includes('.png') || value.includes('.gif') || value.includes('.webp')))
                                        ? `<img src="${getImageUrl(value)}" alt="Pilihan ${key}" class="max-w-full h-auto max-h-32 rounded-lg border border-slate-200">`
                                        : `<span class="leading-relaxed">${value}</span>`;

                                    return `<div class="flex items-start gap-2.5 px-3 py-2.5 border rounded-xl text-sm ${optClass}">${huruf}<div class="flex-1 min-w-0">${isi}</div>${icon}</div>`;
                                })
                                .join('');
                        } else if (item.pilihan) {
                            // Bentuk yang tidak dikenali - tampilkan apa adanya
                            // daripada menyembunyikannya dari penilai.
                            pilihanHTML = `<div class="px-3 py-2.5 border border-slate-200 bg-slate-50 text-slate-700 rounded-xl text-sm">${item.pilihan}</div>`;
                        }

                        const tipeBadge = isPilihanGanda
                            ? '<span class="inline-flex items-center gap-1 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider rounded-lg bg-blue-50 text-blue-700 border border-blue-100"><i class="bi bi-ui-checks"></i> PG</span>'
                            : '<span class="inline-flex items-center gap-1 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider rounded-lg bg-amber-50 text-amber-700 border border-amber-100"><i class="bi bi-pencil-square"></i> Essay</span>';

                        const hasImage = item.image_url && item.image_url.trim() !== '';

                        let numberClasses;
                        let kartuBorder;
                        if (isCorrect || (!isPilihanGanda && skorAdmin !== null && skorAdmin > 0)) {
                            numberClasses = 'bg-emerald-100 text-emerald-800 border-emerald-300';
                            kartuBorder = 'border-emerald-200';
                        } else if (!isAnswered || (!isPilihanGanda && skorAdmin === null)) {
                            numberClasses = 'bg-slate-200 text-slate-800 border-slate-300';
                            kartuBorder = 'border-slate-200';
                        } else {
                            numberClasses = 'bg-red-100 text-red-800 border-red-300';
                            kartuBorder = 'border-red-200';
                        }

                        // Satu soal = satu KARTU, bukan baris tabel 5 kolom.
                        //
                        // Struktur <tr>/<td> dipertahankan karena VanillaPaginator
                        // beroperasi pada baris tabel dan filter membaca data-tipe di
                        // <tr>. Kartunya diletakkan di dalam satu <td> yang dijadikan
                        // block, sehingga tata letaknya bebas dari kolom tabel.
                        html += `
                            <tr class="soal-item block mb-4" data-tipe="${tipeSoal}">
                                <td class="block p-0 border-0">
                                    <div class="bg-white rounded-2xl border ${kartuBorder} shadow-sm overflow-hidden">
                                        <div class="flex items-center gap-3 px-4 py-3 border-b border-slate-100 bg-slate-50/60">
                                            <div class="w-8 h-8 ${numberClasses} rounded-lg flex items-center justify-center font-bold text-sm shadow-sm border shrink-0">${index + 1}</div>
                                            ${tipeBadge}
                                            <div class="ml-auto">${statusBadge}</div>
                                        </div>

                                        <div class="p-4">
                                            ${hasImage ? `
                                            <div class="mb-3">
                                                <img src="${getImageUrl(item.image_url)}" alt="Gambar Soal ${index + 1}" class="max-w-full sm:max-w-sm h-auto max-h-56 rounded-xl border border-slate-200 object-contain">
                                            </div>
                                            ` : ''}
                                            <div class="font-semibold text-slate-800 text-sm leading-relaxed whitespace-pre-wrap">${item.deskripsi}</div>

                                            ${isPilihanGanda ? `
                                            <div class="space-y-2 mt-4">
                                                ${pilihanHTML}
                                            </div>
                                            ` : ''}
                                        </div>

                                        <div class="px-4 py-3 border-t border-slate-100 bg-slate-50/60 flex flex-col sm:flex-row gap-4">
                                            <div class="sm:w-40 shrink-0">
                                                <div class="text-blue-500 font-bold uppercase tracking-wider text-[10px] mb-1.5 flex items-center gap-1.5"><i class="bi bi-shield-check"></i> Kunci</div>
                                                <div class="inline-block px-3 py-1.5 bg-blue-50 text-blue-700 border border-blue-200 font-bold text-sm rounded-lg">${item.jawaban}</div>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="text-slate-500 font-bold uppercase tracking-wider text-[10px] mb-1.5 flex items-center gap-1.5"><i class="bi bi-card-text"></i> Jawaban Peserta</div>
                                                ${isAnswered
                                                    ? (isPilihanGanda
                                                        ? `<div class="inline-block px-3 py-1.5 ${isCorrect ? 'bg-emerald-50 text-emerald-800 border-emerald-400' : 'bg-red-50 text-red-800 border-red-400'} border font-bold text-sm rounded-lg">${item.jawaban_user}</div>`
                                                        : `<div class="px-3 py-2 bg-white text-slate-700 border border-slate-200 text-sm rounded-lg whitespace-pre-wrap leading-relaxed">${item.jawaban_user}</div>`)
                                                    : '<div class="inline-block px-3 py-1.5 bg-slate-100 text-slate-500 font-bold text-sm rounded-lg border border-slate-300 border-dashed">KOSONG</div>'
                                                }
                                                ${essayScoringHtml}
                                            </div>
                                        </div>
                                    </div>
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
                    setAkurasi(benarCount, totalSoal);

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
                    dom.html(dom.qs('#soalJawabanList'), '<tr><td colspan="1" class="py-12 text-center text-slate-400">Tidak ada data soal dan jawaban.</td></tr>');
                    ['#statTotal','#statBenar','#statSalah','#statTidakDijawab','#statPgBenar','#statPgSalah']
                        .forEach(function(sel) { dom.text(dom.qs(sel), 0); });
                    setAkurasi(0, 0);

                    if (window.vp_soal) window.vp_soal.updateData();
                }
        }).catch(function() {
            dom.html(dom.qs('#soalJawabanList'), '<tr><td colspan="1" class="py-12 text-center text-red-500">Gagal memuat data.</td></tr>');
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
                        tampilkanNilaiAkhir(nilaiAkhir);
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

window.updateEssayScore = function(idSoal, idMahasiswa, skor, btnEl) {
    if (btnEl) btnEl.disabled = true;

    dom.postJSON(APP_URL + '/update-essay-score', {
        id_soal: idSoal,
        id_mahasiswa: idMahasiswa,
        skor: skor
    })
    .then(res => {
        if (res.status === 'success' || res.success) {
            // 1. Update main table row data
            const detailBtn = dom.qs(`.btn-detail[data-id="${idMahasiswa}"]`);
            if (detailBtn && res.total_nilai !== undefined) {
                detailBtn.dataset.total = res.total_nilai;
                const tr = detailBtn.closest('tr');
                if (tr) {
                    const cells = tr.querySelectorAll('td');
                    if (cells[3]) {
                        cells[3].innerHTML = `<span class="inline-block px-3 py-1 bg-blue-50 text-blue-700 border border-blue-100 text-xs font-semibold rounded-lg">${res.total_nilai}</span>`;
                    }
                    // Update status badge in table
                    const score = parseInt(res.total_nilai);
                    let badgeClass, statusText;
                    if (score >= 70) {
                        badgeClass = 'text-emerald-700 bg-emerald-50 border border-emerald-100';
                        statusText = 'Memenuhi';
                    } else {
                        badgeClass = 'text-red-700 bg-red-50 border border-red-100';
                        statusText = 'Tidak Memenuhi';
                    }
                    if (cells[4]) {
                        cells[4].innerHTML = `<span class="inline-block px-3 py-1.5 text-xs font-semibold rounded-lg ${badgeClass}">${statusText}</span>`;
                    }
                }
            }

            // 2. Visually update the buttons and badge in the modal without re-rendering everything
            if (btnEl) {
                // Update total text in the header of the modal
                const detailTotalNilai = dom.qs('#detailTotalNilai');
                if (detailTotalNilai) {
                    detailTotalNilai.textContent = res.total_nilai;
                }

                const group = btnEl.closest('.flex');
                if (group) {
                    const buttons = group.querySelectorAll('button');
                    buttons.forEach(b => {
                        b.className = b.className.replace(/bg-red-500 text-white border-red-500/g, 'bg-white text-slate-600 border-slate-300 hover:bg-slate-50');
                        b.className = b.className.replace(/bg-amber-500 text-white border-amber-500/g, 'bg-white text-slate-600 border-slate-300 hover:bg-slate-50');
                        b.className = b.className.replace(/bg-emerald-500 text-white border-emerald-500/g, 'bg-white text-slate-600 border-slate-300 hover:bg-slate-50');
                    });

                    btnEl.className = btnEl.className.replace(/bg-white text-slate-600 border-slate-300 hover:bg-slate-50/g, '');
                    
                    if (btnEl.innerText.includes('Salah')) {
                        btnEl.className += ' bg-red-500 text-white border-red-500';
                    } else if (btnEl.innerText.includes('Mendekati')) {
                        btnEl.className += ' bg-amber-500 text-white border-amber-500';
                    } else {
                        btnEl.className += ' bg-emerald-500 text-white border-emerald-500';
                    }
                }
                
                // Update the status badge above the question
                // We know it's inside a tr/td, but there's a div containing the question.
                const card = btnEl.closest('div.border.border-slate-200'); 
                if (card) {
                    // Try to find the status badge container in the header
                    const headerFlex = card.querySelector('.flex.items-start.justify-between.gap-4');
                    if (headerFlex) {
                        const badges = headerFlex.querySelectorAll('span.inline-flex');
                        if (badges.length > 0) {
                            const badgeContainer = badges[badges.length - 1]; // Usually the last badge is the status
                            
                            if (skor === 0) {
                                badgeContainer.outerHTML = '<span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-50 text-red-700 text-xs font-bold rounded-lg border border-red-200"><i class="bi bi-x-circle-fill"></i> Salah</span>';
                            } else if (btnEl.innerText.includes('Mendekati')) {
                                badgeContainer.outerHTML = '<span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-50 text-amber-700 text-xs font-bold rounded-lg border border-amber-200"><i class="bi bi-exclamation-circle-fill"></i> Mendekati Benar</span>';
                            } else {
                                badgeContainer.outerHTML = '<span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 text-emerald-700 text-xs font-bold rounded-lg border border-emerald-200"><i class="bi bi-check-circle-fill"></i> Benar</span>';
                            }
                        }
                    }
                }
                
                // Helper to update stats dynamically
                const recalculateStatsFromDOM = () => {
                    let benar = 0, salah = 0, tidakDijawab = 0;
                    const badges = document.querySelectorAll('#soalJawabanList .flex.items-start.justify-between.gap-4 > span.inline-flex:last-child');
                    badges.forEach(badge => {
                        const txt = badge.innerText;
                        if (badge.classList.contains('bg-emerald-50')) {
                            benar++;
                        } else if (badge.classList.contains('bg-red-50')) {
                            salah++;
                        } else if (badge.classList.contains('bg-slate-200') && txt.includes('Kosong')) {
                            tidakDijawab++;
                        }
                    });
                    const total = document.querySelectorAll('#soalJawabanList > tr').length;
                    
                    const statBenar = document.querySelector('#statBenar');
                    const statSalah = document.querySelector('#statSalah');
                    const statTidakDijawab = document.querySelector('#statTidakDijawab');
                    
                    if (statBenar) statBenar.textContent = benar;
                    if (statSalah) statSalah.textContent = salah;
                    if (statTidakDijawab) statTidakDijawab.textContent = tidakDijawab;
                    
                    const persen = total > 0 ? Math.round((benar / total) * 100) : 0;
                    const persenEl = document.querySelector('#detailPersen');
                    if (persenEl) persenEl.textContent = persen;
                    const bar = document.querySelector('#detailProgress');
                    if (bar) bar.style.width = persen + '%';
                };
                
                recalculateStatsFromDOM();
                
                btnEl.disabled = false;
            }
        } else {
            if (btnEl) btnEl.disabled = false;
            alert(res.message || 'Gagal menyimpan nilai esai');
        }
    })
    .catch(err => {
        if (btnEl) btnEl.disabled = false;
        alert('Terjadi kesalahan sistem saat menyimpan nilai');
    });
};
