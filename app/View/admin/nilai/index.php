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
    <!-- Page Header -->
    <?php
        $title = 'Daftar Nilai Tes Tertulis';
        $subtitle = 'Kelola dan lihat nilai tes tertulis mahasiswa';
        $icon = 'bi bi-clipboard-data';
        require_once __DIR__ . '/../../templates/components/PageHeader.php';
    ?>

    <div class="max-w-7xl mx-auto px-4 py-6">
        <!-- View List -->
        <div id="view-list">


            <?php if (empty($nilai)): ?>
                <div class="flex flex-col items-center justify-center py-16 text-slate-400 bg-white rounded-2xl border border-slate-100 shadow-sm p-8 text-center">
                    <i class="bi bi-inbox text-6xl mb-4 text-slate-300"></i>
                    <h3 class="text-lg font-bold text-slate-700 mb-1">Belum ada peserta</h3>
                    <p class="text-sm max-w-sm text-slate-500">Data nilai akan muncul setelah mahasiswa mengerjakan tes tertulis</p>
                </div>
            <?php else: ?>
                <!-- Clean Table -->
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden mb-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full align-middle text-sm text-left" id="tableNilai">
                            <thead class="">
                                <tr>
                                    <th class="dt-head-cell text-center" style="width: 60px;">No</th>
                                    <th class="dt-head-cell">Mahasiswa</th>
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
                                            <div class="flex flex-col">
                                                <span><?= htmlspecialchars($value['nama_lengkap'] ?? '-') ?></span>
                                                <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider mt-0.5">Mahasiswa</span>
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

        <!-- View Detail (Inline) -->
        <div id="view-detail" class="hidden pt-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                    <i class="bi bi-person-badge text-blue-600"></i> Detail Nilai Mahasiswa
                </h2>
                <button class="px-4 py-2.5 bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 font-semibold text-sm rounded-xl transition flex items-center gap-2" id="btnBack">
                    <i class="bi bi-arrow-left"></i> Kembali
                </button>
            </div>

            <div class="bg-slate-50 rounded-2xl border border-slate-100 shadow-sm p-6 space-y-6">
                <!-- Info Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white p-5 rounded-2xl border-l-4 border-blue-600 shadow-sm border border-slate-100 flex flex-col justify-center">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Nama Lengkap</span>
                        <div class="text-lg font-bold text-slate-800" id="detailNama">-</div>
                    </div>
                    <div class="bg-white p-5 rounded-2xl border-l-4 border-blue-600 shadow-sm border border-slate-100 flex flex-col justify-center">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Stambuk</span>
                        <div class="text-lg font-bold text-slate-800" id="detailStambuk">-</div>
                    </div>
                    <div class="bg-white p-5 rounded-2xl border-l-4 border-blue-600 shadow-sm border border-slate-100 flex flex-col justify-center">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Nilai Akhir</span>
                        <div class="text-lg font-bold text-slate-800" id="detailTotalNilai">-</div>
                    </div>
                </div>

                <!-- Statistik Jawaban -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-white p-5 rounded-2xl border-l-4 border-slate-400 shadow-sm border border-slate-100 flex flex-col justify-center">
                        <div class="text-2xl font-bold text-slate-800" id="statTotal">0</div>
                        <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider mt-1">Total Soal</div>
                    </div>
                    <div class="bg-white p-5 rounded-2xl border-l-4 border-emerald-500 shadow-sm border border-slate-100 flex flex-col justify-center">
                        <div class="text-2xl font-bold text-emerald-600" id="statBenar">0</div>
                        <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider mt-1">Benar</div>
                    </div>
                    <div class="bg-white p-5 rounded-2xl border-l-4 border-red-500 shadow-sm border border-slate-100 flex flex-col justify-center">
                        <div class="text-2xl font-bold text-red-600" id="statSalah">0</div>
                        <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider mt-1">Salah</div>
                    </div>
                    <div class="bg-white p-5 rounded-2xl border-l-4 border-slate-350 shadow-sm border border-slate-100 flex flex-col justify-center">
                        <div class="text-2xl font-bold text-slate-400" id="statTidakDijawab">0</div>
                        <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider mt-1">Tidak Dijawab</div>
                    </div>
                </div>

                <!-- Soal Jawaban Section -->
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center pb-4 border-b border-slate-100 mb-6 gap-4">
                        <h5 class="font-bold text-slate-800 flex items-center gap-2 text-base">
                            <i class="bi bi-list-check text-blue-600"></i> Soal dan Jawaban
                        </h5>
                        <div class="flex flex-wrap gap-2">
                            <button class="filter-btn px-4 py-2 bg-blue-600 border border-blue-600 text-white text-sm font-semibold rounded-xl transition cursor-pointer" data-filter="semua">
                                <i class="bi bi-grid-3x3-gap"></i> Semua
                            </button>
                            <button class="filter-btn px-4 py-2 bg-white border border-slate-200 text-slate-600 hover:text-blue-600 hover:border-blue-600 hover:bg-blue-50 text-sm font-semibold rounded-xl transition cursor-pointer" data-filter="pilihan_ganda">
                                <i class="bi bi-ui-checks"></i> Pilihan Ganda
                            </button>
                            <button class="filter-btn px-4 py-2 bg-white border border-slate-200 text-slate-600 hover:text-blue-600 hover:border-blue-600 hover:bg-blue-50 text-sm font-semibold rounded-xl transition cursor-pointer" data-filter="essay">
                                <i class="bi bi-pencil-square"></i> Essay
                            </button>
                        </div>
                    </div>
                    <div id="soalJawabanList" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <p class="text-slate-400">Memuat data...</p>
                    </div>
                </div>

                <!-- Nilai Form -->
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
                    <h5 class="font-bold text-slate-800 mb-4 flex items-center gap-2 text-base">
                        <i class="bi bi-pencil-square text-blue-600"></i> Input Nilai Akhir
                    </h5>
                    <form id="formNilaiAkhir" class="flex items-center gap-3 flex-wrap">
                        <input type="number"
                               id="nilaiAkhir"
                               class="w-full sm:w-48 px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm bg-white transition"
                               placeholder="Masukkan nilai (0-100)"
                               min="0"
                               max="100">
                        <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm rounded-xl transition flex items-center gap-2 shadow-md shadow-emerald-500/10">
                            <i class="bi bi-check-lg"></i> Simpan Nilai
                        </button>
                    </form>
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

        // Loading State for Questions
        $('#soalJawabanList').html('<div class="w-full"><p class="text-slate-400">Memuat data...</p></div>');

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

                    response.data.forEach((item, index) => {
                        const isAnswered = item.jawaban_user !== null && item.jawaban_user !== '';
                        const isCorrect = isAnswered && (item.jawaban === item.jawaban_user);
                        const tipeSoal = item.status_soal || 'essay';
                        const isPilihanGanda = tipeSoal === 'pilihan_ganda';

                        let cardClass, borderClass, icon, statusBadge;

                        if (!isAnswered) {
                            tidakDijawabCount++;
                            cardClass = 'border-l-4 border-slate-400 bg-slate-50/50 rounded-2xl border border-slate-100 p-5 shadow-sm';
                            icon = '<i class="bi bi-question-circle-fill text-slate-400"></i>';
                            statusBadge = 'bg-slate-100 text-slate-600';
                        } else if (isCorrect) {
                            benarCount++;
                            cardClass = 'border-l-4 border-emerald-500 bg-white rounded-2xl border border-slate-100 p-5 shadow-sm';
                            icon = '<i class="bi bi-check-circle-fill text-emerald-500"></i>';
                            statusBadge = 'bg-emerald-50 text-emerald-700';
                        } else {
                            salahCount++;
                            cardClass = 'border-l-4 border-red-500 bg-white rounded-2xl border border-slate-100 p-5 shadow-sm';
                            icon = '<i class="bi bi-x-circle-fill text-red-500"></i>';
                            statusBadge = 'bg-red-50 text-red-700';
                        }

                        // Format options for display
                        let pilihanHTML = '';
                        try {
                            const pilihanObj = JSON.parse(item.pilihan);
                            pilihanHTML = Object.entries(pilihanObj)
                                .map(([key, value]) => {
                                    if (value && (value.includes('.jpg') || value.includes('.jpeg') || value.includes('.png') || value.includes('.gif') || value.includes('.webp'))) {
                                        return `
                                            <div class="mb-2 flex items-start gap-2">
                                                <strong class="text-slate-700">${key}.</strong>
                                                <div>
                                                    <img src="${getImageUrl(value)}" alt="Pilihan ${key}" class="max-w-full h-auto max-h-40 rounded-lg border border-slate-200">
                                                </div>
                                            </div>
                                        `;
                                    } else {
                                        return `<div class="mb-1 text-slate-700"><strong class="text-slate-800">${key}.</strong> ${value}</div>`;
                                    }
                                })
                                .join('');
                        } catch (e) {
                            pilihanHTML = item.pilihan;
                        }

                        const tipeBadge = isPilihanGanda
                            ? '<span class="inline-block px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider rounded bg-blue-50 text-blue-700 border border-blue-100 ml-2"><i class="bi bi-ui-checks"></i> PG</span>'
                            : '<span class="inline-block px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider rounded bg-amber-50 text-amber-700 border border-amber-100 ml-2"><i class="bi bi-pencil-square"></i> Essay</span>';

                        const hasImage = item.image_url && item.image_url.trim() !== '';

                        html += `
                            <div class="soal-item" data-tipe="${tipeSoal}">
                                <div class="${cardClass}">
                                    <div class="flex justify-between items-start mb-4">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg flex items-center justify-center font-bold text-sm shadow-sm">${index + 1}</div>
                                            ${tipeBadge}
                                        </div>
                                        <div class="text-xl">${icon}</div>
                                    </div>
                                    ${hasImage ? `
                                    <div class="mb-4">
                                        <img src="${getImageUrl(item.image_url)}" alt="Gambar Soal ${index + 1}" class="max-w-full h-auto max-h-60 rounded-xl border border-slate-200">
                                    </div>
                                    ` : ''}
                                    <div class="mb-4">
                                        <span class="font-bold text-slate-800 leading-relaxed text-sm block">${item.deskripsi}</span>
                                    </div>
                                    <div class="text-xs space-y-3">
                                        ${isPilihanGanda ? `
                                        <div class="space-y-1.5 text-slate-600 bg-slate-50/50 p-3 rounded-xl border border-slate-100">
                                            ${pilihanHTML}
                                        </div>
                                        ` : ''}
                                        <div class="flex items-center gap-1.5">
                                            <span class="text-slate-400 font-semibold uppercase tracking-wider text-[10px]">Jawaban Benar:</span>
                                            <span class="inline-block px-2 py-1 bg-emerald-50 text-emerald-700 border border-emerald-100 font-semibold rounded">${item.jawaban}</span>
                                        </div>
                                        <div class="flex items-center gap-1.5">
                                            <span class="text-slate-400 font-semibold uppercase tracking-wider text-[10px]">Jawaban Mahasiswa:</span>
                                            ${isAnswered
                                                ? `<span class="inline-block px-2 py-1 ${statusBadge} font-semibold rounded">${item.jawaban_user}</span>`
                                                : '<span class="inline-block px-2 py-1 bg-slate-100 text-slate-600 font-semibold rounded">Tidak menjawab</span>'
                                            }
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    });

                    // Update stats
                    $('#statTotal').text(totalSoal);
                    $('#statBenar').text(benarCount);
                    $('#statSalah').text(salahCount);
                    $('#statTidakDijawab').text(tidakDijawabCount);

                    $('#soalJawabanList').html(html);
                } else {
                    $('#soalJawabanList').html('<div class="w-full"><p class="text-slate-450">Tidak ada data soal dan jawaban.</p></div>');
                    $('#statTotal').text(0);
                    $('#statBenar').text(0);
                    $('#statSalah').text(0);
                    $('#statTidakDijawab').text(0);
                }
            },
            error: function() {
                $('#soalJawabanList').html('<div class="w-full"><p class="text-red-500">Gagal memuat data.</p></div>');
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

        // Filter soal items
        if (filter === 'semua') {
            $('.soal-item').show();
        } else {
            $('.soal-item').hide();
            $(`.soal-item[data-tipe="${filter}"]`).show();
        }
    });

    // Submit score form
    $('#formNilaiAkhir').on('submit', function(e) {
        e.preventDefault();
        const nilaiAkhir = $('#nilaiAkhir').val();

        if (!currentMahasiswaId) {
            showAlert('ID mahasiswa tidak ditemukan', false);
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

