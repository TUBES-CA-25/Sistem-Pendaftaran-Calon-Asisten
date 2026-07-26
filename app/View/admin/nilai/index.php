<?php
/**
 * Daftar Nilai Tes Tertulis Admin View
 *
 * Data yang diterima dari controller:
 * @var array $nilai - Daftar nilai mahasiswa
 */
$nilai = $nilai ?? [];
?>

<main>
    <!-- View List -->
    <div id="view-list">
        <!-- Page Header -->
        <?php
            $title = 'Daftar Nilai Tes Tertulis';
            $subtitle = 'Kelola dan lihat nilai tes tertulis calon asisten';
            $icon = 'bi bi-clipboard-data';
            require_once __DIR__ . '/../../templates/components/PageHeader.php';
        ?>

        <div class="max-w-7xl mx-auto px-4 pt-0 pb-6">


            <?php if (empty($nilai)): ?>
                <div class="flex flex-col items-center justify-center py-16 text-slate-400 bg-white rounded-2xl border border-slate-100 shadow-sm p-8 text-center">
                    <i class="bi bi-inbox text-6xl mb-4 text-slate-300"></i>
                    <h3 class="text-lg font-bold text-slate-700 mb-1">Belum ada peserta</h3>
                    <p class="text-sm max-w-sm text-slate-500">Data nilai akan muncul setelah calon asisten mengerjakan tes tertulis</p>
                </div>
            <?php else: ?>
                <!-- Clean Table -->
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden mb-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full align-middle text-sm text-left no-datatable" id="tableNilai" data-paginator="true" data-paginator-perpage="10">
                            <thead class="">
                                <tr>
                                    <th class="dt-head-cell text-center" style="width: 60px;">No</th>
                                    <th class="dt-head-cell">Calon Asisten</th>
                                    <th class="dt-head-cell">Stambuk</th>
                                    <th class="dt-head-cell text-center" style="width: 150px;">Nilai Akhir</th>
                                    <th class="dt-head-cell text-center" style="width: 180px;">Status</th>
                                    <th class="dt-head-cell text-center" style="width: 120px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="dt-tbody">
                                <?php $i = 1; foreach ($nilai as $value): ?>
                                    <?php
                                    // Raw auto-calculated score
                                    $nilaiTes = $value['nilai'] ?? null;
                                    // Manual override/Final score
                                    $nilaiTotal = $value['total'] ?? null;

                                    // Determine which value to display
                                    $displayNilai = ($nilaiTotal !== null && $nilaiTotal !== '') ? $nilaiTotal : $nilaiTes;
                                    
                                    // Determine status badge
                                    $statusLabel = 'Belum Dinilai';
                                    $statusBadge = 'text-slate-500 bg-slate-50 border border-slate-200';

                                    if ($displayNilai !== null && $displayNilai !== '-' && $displayNilai !== '') {
                                        $score = (int)$displayNilai;
                                        if ($score >= 70) {
                                            $statusLabel = 'Memenuhi';
                                            $statusBadge = 'text-emerald-700 bg-emerald-50 border border-emerald-100';
                                        } else {
                                            $statusLabel = 'Tidak Memenuhi';
                                            $statusBadge = 'text-red-700 bg-red-50 border border-red-100';
                                        }
                                    }
                                    ?>
                                    <tr class="dt-body-row">
                                        <td class="text-center py-4 px-4"><?= $i ?></td>
                                        <td class="py-4 px-4">
                                            <div class="flex items-center gap-3">
                                                <img src="<?= \App\Controllers\HomeController::getUserPhotoPath($value['foto'] ?? 'default.png') ?>" alt="Avatar" class="rounded-full w-10 h-10 object-cover border-2 border-slate-100 shrink-0" onerror="this.src='/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Downloads/default.png'">
                                                <div>
                                                    <div class="font-bold text-slate-800"><?= htmlspecialchars($value['nama_lengkap'] ?? '-') ?></div>
                                                    <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wider mt-0.5">Calon Asisten</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-4 px-4"><?= htmlspecialchars($value['stambuk'] ?? '-') ?></td>
                                        <td class="text-center py-4 px-4">
                                            <span class="inline-block px-3 py-1 bg-blue-50 text-blue-700 border border-blue-100 text-xs font-semibold rounded-lg">
                                                <?= ($displayNilai !== null && $displayNilai !== '') ? $displayNilai : 'Belum ada' ?>
                                            </span>
                                        </td>
                                        <td class="text-center py-4 px-4">
                                            <span class="inline-block px-3 py-1.5 text-xs font-semibold rounded-lg <?= $statusBadge ?>">
                                                <?= $statusLabel ?>
                                            </span>
                                        </td>
                                        <td class="text-center py-4 px-4">
                                            <button class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs rounded-xl transition flex items-center justify-center gap-1.5 shadow-sm shadow-blue-500/10 mx-auto btn-detail"
                                                    data-id="<?= htmlspecialchars($value['id'] ?? '') ?>"
                                                    data-nama="<?= htmlspecialchars($value['nama_lengkap'] ?? '-') ?>"
                                                    data-stambuk="<?= htmlspecialchars($value['stambuk'] ?? '-') ?>"
                                                    data-nilai="<?= htmlspecialchars($nilaiTes) ?>"
                                                    data-total="<?= htmlspecialchars($nilaiTotal ?? '') ?>">
                                                <i class="bi bi-eye"></i> Detail
                                            </button>
                                        </td>
                                    </tr>
                                <?php $i++; endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- View Detail (Inline) -->
    <div id="view-detail" class="hidden max-w-7xl mx-auto px-4 py-6">
        <!-- Compact Header -->
        <div class="flex flex-col xl:flex-row justify-between items-start xl:items-center mb-6 gap-4 bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
            <div class="flex items-center gap-4 w-full xl:w-auto">
                <button class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold text-sm rounded-lg transition flex items-center gap-2" id="btnBack">
                    <i class="bi bi-arrow-left"></i> Kembali
                </button>
                <div class="hidden sm:block w-px h-10 bg-slate-200"></div>
                <div class="flex-1 min-w-0">
                    <h2 class="text-lg font-bold text-slate-800 truncate" id="detailNama">Nama Calon Asisten</h2>
                    <div class="text-xs font-semibold text-slate-500" id="detailStambuk">Stambuk</div>
                </div>
            </div>
            
            <div class="flex flex-wrap items-center gap-3 w-full xl:w-auto justify-start xl:justify-end">
                <div class="flex items-center gap-2 px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs font-semibold text-slate-600">
                    <span class="text-slate-500">PG:</span>
                    <span class="text-emerald-600 flex items-center gap-1"><i class="bi bi-check-circle-fill"></i> <span id="statPgBenar">0</span></span>
                    <span class="text-red-600 flex items-center gap-1 ml-1"><i class="bi bi-x-circle-fill"></i> <span id="statPgSalah">0</span></span>
                </div>

                <div class="w-px h-6 bg-slate-200 hidden sm:block"></div>

                <div class="flex items-center gap-2">
                    <div class="text-xs font-semibold text-slate-500">Nilai Akhir: <span id="detailTotalNilai" class="text-blue-600 font-bold text-sm">-</span></div>
                    <form id="formNilaiAkhir" class="flex items-center gap-2 border-l border-slate-200 pl-3 ml-1">
                        <input type="number" id="nilaiAkhir" class="w-16 px-2 py-1.5 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 font-bold text-center" placeholder="0-100" min="0" max="100">
                        <button type="submit" class="px-3 py-1.5 bg-[#1d4ed8] hover:bg-blue-700 text-white font-bold text-xs rounded-lg transition flex items-center gap-1 shadow-sm">
                            <i class="bi bi-check-lg"></i> Simpan
                        </button>
                    </form>
                </div>

                <div class="w-px h-6 bg-slate-200 hidden md:block"></div>

                <!-- Filters -->
                <div class="bg-slate-50 p-1 rounded-lg border border-slate-200 flex">
                    <button class="filter-btn px-3 py-1.5 bg-blue-600 text-white text-xs font-semibold rounded-md transition" data-filter="semua">Semua</button>
                    <button class="filter-btn px-3 py-1.5 text-slate-600 hover:bg-slate-200 text-xs font-semibold rounded-md transition" data-filter="pilihan_ganda">Pil. Ganda</button>
                    <button class="filter-btn px-3 py-1.5 text-slate-600 hover:bg-slate-200 text-xs font-semibold rounded-md transition" data-filter="essay">Essay</button>
                </div>
            </div>
        </div>

        <!-- Soal Jawaban Section (Table) -->
        <div class="space-y-4 pb-10">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse no-datatable" id="soalJawabanTable">
                        <thead>
                            <tr class="bg-slate-50 text-slate-600 border-b border-slate-200 text-xs uppercase tracking-wider">
                                <th class="p-4 font-bold w-16 text-center">No</th>
                                <th class="p-4 font-bold w-28">Tipe</th>
                                <th class="p-4 font-bold min-w-[300px]">Soal & Pilihan</th>
                                <th class="p-4 font-bold w-40">Kunci & Jawaban</th>
                                <th class="p-4 font-bold w-32 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody id="soalJawabanList" class="divide-y divide-slate-100">
                            <tr>
                                <td colspan="5" class="py-12 text-center">
                                    <i class="bi bi-hourglass-split text-4xl text-slate-300 mb-3 block"></i>
                                    <p class="text-slate-500 font-medium">Memuat data pekerjaan...</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
$(document).ready(function() {
    let currentMahasiswaId = null;

    // Helper function to get full image URL
    function getImageUrl(path) {
        if (!path) return '';
        if (path.startsWith('http') || path.startsWith('/')) {
            return path;
        }
        const baseUrl = '<?= APP_URL ?>'.replace('/public', '');
        return baseUrl + '/' + path;
    }

    // Search functionality
    $('#searchInput').on('input', function() {
        const searchTerm = $(this).val().toLowerCase();
        $('#tableNilai tbody tr').each(function() {
            const nama = $(this).find('td:eq(1)').text().toLowerCase();
            const stambuk = $(this).find('td:eq(2)').text().toLowerCase();
            if (nama.includes(searchTerm) || stambuk.includes(searchTerm)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    // Input validation
    $('#nilaiAkhir').on('input', function() {
        let value = parseInt(this.value);
        if (value > 100) this.value = 100;
        if (value < 0) this.value = 0;
    });

    // Open Detail View
    $('.btn-detail').on('click', function() {
        const id = $(this).data('id');
        const nama = $(this).data('nama');
        const stambuk = $(this).data('stambuk');
        const nilai = $(this).data('nilai');
        const total = $(this).data('total');

        currentMahasiswaId = id;

        // Populate Data
        $('#detailNama').text(nama);
        $('#detailStambuk').text(stambuk);
        $('#detailTotalNilai').text(total || 'Belum dinilai');
        $('#nilaiAkhir').val(total || '');

        // Reset filters
        $('.filter-btn').removeClass('bg-blue-600 border-blue-600 text-white hover:bg-blue-700').addClass('bg-white border-slate-200 text-slate-600 hover:text-blue-600 hover:border-blue-600 hover:bg-blue-50');
        $('.filter-btn[data-filter="semua"]').removeClass('bg-white border-slate-200 text-slate-600 hover:text-blue-600 hover:border-blue-600 hover:bg-blue-50').addClass('bg-blue-600 border-blue-600 text-white hover:bg-blue-700');
        if (window.vp_soal) {
            window.vp_soal.customFilterFn = null;
        }

        // Loading State for Questions
        $('#soalJawabanList').html('<tr><td colspan="5" class="py-12 text-center text-slate-400">Memuat data...</td></tr>');

        // Fetch Questions
        $.ajax({
            url: '<?= APP_URL ?>/getsoaljawaban',
            type: 'POST',
            data: { id: id },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success' && response.data.length > 0) {
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
                                        <div class="text-indigo-600 font-bold uppercase tracking-wider text-[10px] mb-1.5 flex items-center gap-1.5"><i class="bi bi-person-fill"></i> Jawaban</div>
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
                    $('#statTotal').text(totalSoal);
                    $('#statBenar').text(benarCount);
                    $('#statSalah').text(salahCount);
                    $('#statTidakDijawab').text(tidakDijawabCount);
                    $('#statPgBenar').text(pgBenarCount);
                    $('#statPgSalah').text(pgSalahCount);

                    $('#soalJawabanList').html(html);

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
                    $('#soalJawabanList').html('<tr><td colspan="5" class="py-12 text-center text-slate-450">Tidak ada data soal dan jawaban.</td></tr>');
                    $('#statTotal').text(0);
                    $('#statBenar').text(0);
                    $('#statSalah').text(0);
                    $('#statTidakDijawab').text(0);
                    $('#statPgBenar').text(0);
                    $('#statPgSalah').text(0);
                    
                    if (window.vp_soal) window.vp_soal.updateData();
                }
            },
            error: function() {
                $('#soalJawabanList').html('<tr><td colspan="5" class="py-12 text-center text-red-500">Gagal memuat data.</td></tr>');
            }
        });

        $('#view-list').addClass('hidden');
        $('#view-detail').removeClass('hidden');
        window.scrollTo(0, 0);
    });

    // Back Button Logic
    $('#btnBack').on('click', function() {
        $('#view-detail').addClass('hidden');
        $('#view-list').removeClass('hidden');
        currentMahasiswaId = null;
    });

    // Filter Button Logic
    $(document).on('click', '.filter-btn', function() {
        const filter = $(this).data('filter');

        // Update active state style
        $('.filter-btn').removeClass('bg-blue-600 border-blue-600 text-white hover:bg-blue-700').addClass('bg-white border-slate-200 text-slate-600 hover:text-blue-600 hover:border-blue-600 hover:bg-blue-50');
        $(this).removeClass('bg-white border-slate-200 text-slate-600 hover:text-blue-600 hover:border-blue-600 hover:bg-blue-50').addClass('bg-blue-600 border-blue-600 text-white hover:bg-blue-700');

        if (window.vp_soal) {
            window.vp_soal.setFilter(row => {
                if (filter === 'semua') return true;
                return row.getAttribute('data-tipe') === filter;
            });
        }
    });

    // Submit score form
    $('#formNilaiAkhir').on('submit', function(e) {
        e.preventDefault();
        const nilaiAkhir = $('#nilaiAkhir').val();

        if (!currentMahasiswaId) {
            showAlert('ID calon asisten tidak ditemukan', false);
            return;
        }

        if (typeof nilaiAkhir === 'undefined' || nilaiAkhir === '') {
            showAlert('Mohon masukkan nilai', false);
            return;
        }

        const btnSubmit = $(this).find('button[type="submit"]');
        const originalBtnText = btnSubmit.html();
        btnSubmit.prop('disabled', true).html('<i class="bi bi-hourglass-split animate-spin mr-1"></i> Menyimpan...');

        $.ajax({
            url: '<?= APP_URL ?>/updatenilaiakhir',
            type: 'POST',
            data: { id: currentMahasiswaId, nilai: nilaiAkhir },
            dataType: 'json',
            success: function(response) {
                btnSubmit.prop('disabled', false).html(originalBtnText);

                if (response.status === 'success') {
                    showAlert('Nilai berhasil disimpan!', true);

                    // Real-time Update Logic in Table
                    const btn = $(`.btn-detail[data-id="${currentMahasiswaId}"]`);
                    const tr = btn.closest('tr');

                    if (tr.length) {
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

                        // Update Nilai Akhir Column (Index 3)
                        tr.find('td:eq(3)').html(`<span class="inline-block px-3 py-1 bg-blue-50 text-blue-700 border border-blue-100 text-xs font-semibold rounded-lg">${nilaiAkhir}</span>`);

                        // Update Status Column (Index 4)
                        tr.find('td:eq(4)').html(`<span class="inline-block px-3 py-1.5 text-xs font-semibold rounded-lg ${badgeClass}">${statusText}</span>`);

                        // Update Button Data Attribute
                        btn.data('total', nilaiAkhir);

                        // Update detail text
                        $('#detailTotalNilai').text(nilaiAkhir);
                    }
                } else {
                    showAlert(response.message || 'Gagal menyimpan nilai', false);
                }
            },
            error: function() {
                btnSubmit.prop('disabled', false).html(originalBtnText);
                showAlert('Terjadi kesalahan saat menyimpan nilai', false);
            }
        });
    });
});
</script>

