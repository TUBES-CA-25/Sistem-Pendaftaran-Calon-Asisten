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
            $subtitle = 'Kelola dan lihat nilai tes tertulis mahasiswa';
            $icon = 'bi bi-clipboard-data';
            require_once __DIR__ . '/../../templates/components/PageHeader.php';
        ?>

        <div class="max-w-7xl mx-auto px-4 py-6">


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
                        <table class="min-w-full align-middle text-sm text-left no-datatable" id="tableNilai" data-paginator="true" data-paginator-perpage="10">
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
        <!-- Header Section with vibrant gradient -->
        <div class="relative bg-gradient-to-r from-blue-600 to-indigo-700 rounded-b-3xl sm:rounded-3xl shadow-lg mb-8 overflow-hidden -mx-4 sm:mx-0 px-6 py-8 sm:p-10 mt-0 sm:mt-6">
            <!-- Decorative background elements -->
            <div class="absolute top-0 right-0 -mr-20 -mt-20 w-64 h-64 rounded-full bg-white opacity-10 blur-3xl"></div>
            <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-80 h-80 rounded-full bg-indigo-500 opacity-20 blur-3xl"></div>
            
            <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <div>
                    <button class="mb-5 px-4 py-2 bg-white/10 hover:bg-white/20 backdrop-blur-md text-white font-semibold text-sm rounded-xl transition flex items-center gap-2 border border-white/20" id="btnBack">
                        <i class="bi bi-arrow-left"></i> Kembali ke Daftar
                    </button>
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-white mb-3 flex items-center gap-3">
                        <span id="detailNama">Nama Mahasiswa</span>
                    </h2>
                    <div class="flex flex-wrap items-center gap-3">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-white/20 backdrop-blur-md text-white text-xs font-bold rounded-lg border border-white/10">
                            <i class="bi bi-person-badge"></i> <span id="detailStambuk">Stambuk</span>
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-500/80 backdrop-blur-md text-white text-xs font-bold rounded-lg border border-emerald-400/50 shadow-sm">
                            Nilai Akhir: <span id="detailTotalNilai">-</span>
                        </span>
                    </div>
                </div>
                
                <!-- Quick Score Input Form in Header -->
                <div class="bg-white/10 backdrop-blur-md border border-white/20 p-5 rounded-2xl shadow-xl w-full md:w-auto mt-4 md:mt-0">
                    <h5 class="font-bold text-white mb-3 text-sm flex items-center gap-2">
                        <i class="bi bi-pencil-square text-blue-200"></i> Input Nilai Akhir
                    </h5>
                    <form id="formNilaiAkhir" class="flex items-center gap-3">
                        <div class="relative">
                            <input type="number"
                                    id="nilaiAkhir"
                                    class="w-full sm:w-32 px-4 py-2.5 rounded-xl border border-white/30 bg-white/20 text-white placeholder-blue-100 focus:outline-none focus:ring-2 focus:ring-white focus:bg-white/30 font-bold text-center transition"
                                    placeholder="0-100"
                                    min="0"
                                    max="100">
                        </div>
                        <button type="submit" class="px-5 py-2.5 bg-white text-blue-700 hover:bg-blue-50 font-bold text-sm rounded-xl transition flex items-center gap-2 shadow-lg hover:-translate-y-0.5">
                            <i class="bi bi-check-lg"></i> Simpan
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Main Content Container -->
        <div class="space-y-8 pb-10">
            <!-- Statistik Jawaban - Premium Grid -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                <div class="bg-white p-5 sm:p-6 rounded-3xl shadow-sm border border-slate-100 flex items-center gap-4 hover:shadow-md hover:-translate-y-1 transition-all">
                    <div class="w-12 h-12 sm:w-14 sm:h-14 shrink-0 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl sm:text-2xl shadow-inner">
                        <i class="bi bi-list-task"></i>
                    </div>
                    <div>
                        <div class="text-slate-400 text-[10px] sm:text-[11px] font-bold uppercase tracking-wider mb-0.5">Total Soal</div>
                        <div class="text-2xl sm:text-3xl font-extrabold text-slate-800" id="statTotal">0</div>
                    </div>
                </div>
                <div class="bg-white p-5 sm:p-6 rounded-3xl shadow-sm border border-slate-100 flex items-center gap-4 hover:shadow-md hover:-translate-y-1 transition-all">
                    <div class="w-12 h-12 sm:w-14 sm:h-14 shrink-0 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl sm:text-2xl shadow-inner">
                        <i class="bi bi-check-lg"></i>
                    </div>
                    <div>
                        <div class="text-slate-400 text-[10px] sm:text-[11px] font-bold uppercase tracking-wider mb-0.5">Benar</div>
                        <div class="text-2xl sm:text-3xl font-extrabold text-slate-800" id="statBenar">0</div>
                    </div>
                </div>
                <div class="bg-white p-5 sm:p-6 rounded-3xl shadow-sm border border-slate-100 flex items-center gap-4 hover:shadow-md hover:-translate-y-1 transition-all">
                    <div class="w-12 h-12 sm:w-14 sm:h-14 shrink-0 rounded-2xl bg-red-50 text-red-600 flex items-center justify-center text-xl sm:text-2xl shadow-inner">
                        <i class="bi bi-x-lg"></i>
                    </div>
                    <div>
                        <div class="text-slate-400 text-[10px] sm:text-[11px] font-bold uppercase tracking-wider mb-0.5">Salah</div>
                        <div class="text-2xl sm:text-3xl font-extrabold text-slate-800" id="statSalah">0</div>
                    </div>
                </div>
                <div class="bg-white p-5 sm:p-6 rounded-3xl shadow-sm border border-slate-100 flex items-center gap-4 hover:shadow-md hover:-translate-y-1 transition-all">
                    <div class="w-12 h-12 sm:w-14 sm:h-14 shrink-0 rounded-2xl bg-slate-100 text-slate-500 flex items-center justify-center text-xl sm:text-2xl shadow-inner">
                        <i class="bi bi-dash-lg"></i>
                    </div>
                    <div>
                        <div class="text-slate-400 text-[10px] sm:text-[11px] font-bold uppercase tracking-wider mb-0.5">Kosong</div>
                        <div class="text-2xl sm:text-3xl font-extrabold text-slate-800" id="statTidakDijawab">0</div>
                    </div>
                </div>
            </div>

            <!-- Soal Jawaban Section -->
            <div>
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                    <h3 class="font-bold text-xl text-slate-800 flex items-center gap-2">
                        <i class="bi bi-journal-text text-blue-600"></i> Review Pekerjaan
                    </h3>
                    <div class="bg-white p-1.5 rounded-xl border border-slate-200 shadow-sm inline-flex">
                        <button class="filter-btn px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg transition" data-filter="semua">
                            Semua
                        </button>
                        <button class="filter-btn px-4 py-2 bg-transparent text-slate-600 hover:text-blue-600 text-sm font-semibold rounded-lg transition" data-filter="pilihan_ganda">
                            Pil. Ganda
                        </button>
                        <button class="filter-btn px-4 py-2 bg-transparent text-slate-600 hover:text-blue-600 text-sm font-semibold rounded-lg transition" data-filter="essay">
                            Essay
                        </button>
                    </div>
                </div>
                
                <div class="overflow-x-auto bg-white rounded-2xl border border-slate-200 shadow-sm">
                    <table class="w-full text-left border-collapse no-datatable" id="soalJawabanTable">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 text-xs uppercase tracking-wider">
                                <th class="p-4 font-semibold w-16 text-center">No</th>
                                <th class="p-4 font-semibold w-28">Tipe</th>
                                <th class="p-4 font-semibold min-w-[300px]">Soal & Pilihan</th>
                                <th class="p-4 font-semibold w-40">Kunci & Jawaban</th>
                                <th class="p-4 font-semibold w-32 text-center">Status</th>
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

                    response.data.forEach((item, index) => {
                        const isAnswered = item.jawaban_user !== null && item.jawaban_user !== '';
                        const isCorrect = isAnswered && (item.jawaban === item.jawaban_user);
                        const tipeSoal = item.status_soal || 'essay';
                        const isPilihanGanda = tipeSoal === 'pilihan_ganda';

                        let rowBg, statusBadge;

                        if (!isAnswered) {
                            tidakDijawabCount++;
                            rowBg = 'bg-slate-50/50';
                            statusBadge = '<span class="inline-flex items-center gap-1.5 px-3 py-1 bg-slate-100 text-slate-600 text-xs font-bold rounded-lg border border-slate-200"><i class="bi bi-dash-circle"></i> Kosong</span>';
                        } else if (isCorrect) {
                            benarCount++;
                            rowBg = 'bg-emerald-50/30';
                            statusBadge = '<span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-50 text-emerald-700 text-xs font-bold rounded-lg border border-emerald-200"><i class="bi bi-check-circle-fill"></i> Benar</span>';
                        } else {
                            salahCount++;
                            rowBg = 'bg-red-50/30';
                            statusBadge = '<span class="inline-flex items-center gap-1.5 px-3 py-1 bg-red-50 text-red-700 text-xs font-bold rounded-lg border border-red-200"><i class="bi bi-x-circle-fill"></i> Salah</span>';
                        }

                        // Format options for display
                        let pilihanHTML = '';
                        try {
                            const pilihanObj = JSON.parse(item.pilihan);
                            pilihanHTML = Object.entries(pilihanObj)
                                .map(([key, value]) => {
                                    if (value && (value.includes('.jpg') || value.includes('.jpeg') || value.includes('.png') || value.includes('.gif') || value.includes('.webp'))) {
                                        return `
                                            <div class="mb-2 flex items-start gap-2 bg-white p-2.5 border border-slate-200 rounded-xl shadow-sm w-fit">
                                                <strong class="text-slate-700">${key}.</strong>
                                                <div>
                                                    <img src="${getImageUrl(value)}" alt="Pilihan ${key}" class="max-w-full h-auto max-h-32 rounded-lg border border-slate-200">
                                                </div>
                                            </div>
                                        `;
                                    } else {
                                        return `<div class="mb-1.5 text-slate-700 bg-white px-3 py-2 border border-slate-200 rounded-lg shadow-sm text-sm"><strong class="text-slate-800">${key}.</strong> ${value}</div>`;
                                    }
                                })
                                .join('');
                        } catch (e) {
                            if (typeof item.pilihan === 'string' && /,\s*(?=[A-E]\.)/.test(item.pilihan)) {
                                const opts = item.pilihan.split(/,\s*(?=[A-E]\.)/);
                                pilihanHTML = opts.map(opt => `<div class="mb-1.5 text-slate-700 bg-white px-3 py-2 border border-slate-200 rounded-lg shadow-sm text-sm">${opt}</div>`).join('');
                            } else {
                                pilihanHTML = `<div class="text-slate-700 bg-white px-3 py-2 border border-slate-200 rounded-lg shadow-sm text-sm">${item.pilihan}</div>`;
                            }
                        }

                        const tipeBadge = isPilihanGanda
                            ? '<span class="inline-flex items-center gap-1 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider rounded-lg bg-blue-50 text-blue-700 border border-blue-100"><i class="bi bi-ui-checks"></i> PG</span>'
                            : '<span class="inline-flex items-center gap-1 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider rounded-lg bg-amber-50 text-amber-700 border border-amber-100"><i class="bi bi-pencil-square"></i> Essay</span>';

                        const hasImage = item.image_url && item.image_url.trim() !== '';

                        html += `
                            <tr class="soal-item hover:bg-slate-50 transition-colors ${rowBg}" data-tipe="${tipeSoal}">
                                <td class="p-4 align-top text-center">
                                    <div class="w-8 h-8 mx-auto bg-slate-800 text-white rounded-lg flex items-center justify-center font-bold text-sm shadow-sm">${index + 1}</div>
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
                                        <div class="text-slate-400 font-bold uppercase tracking-wider text-[10px] mb-1.5 flex items-center gap-1.5"><i class="bi bi-shield-check text-blue-400"></i> Kunci</div>
                                        <div class="inline-block px-3 py-1.5 bg-white text-slate-800 border border-slate-200 font-bold text-sm rounded-lg shadow-sm">${item.jawaban}</div>
                                    </div>
                                    <div>
                                        <div class="text-slate-400 font-bold uppercase tracking-wider text-[10px] mb-1.5 flex items-center gap-1.5"><i class="bi bi-person-fill text-indigo-400"></i> Jawaban</div>
                                        ${isAnswered
                                            ? `<div class="inline-block px-3 py-1.5 bg-white text-slate-800 border border-slate-200 font-bold text-sm rounded-lg shadow-sm">${item.jawaban_user}</div>`
                                            : '<div class="inline-block px-3 py-1.5 bg-slate-100/50 text-slate-400 font-bold text-sm rounded-lg border border-slate-200 border-dashed">KOSONG</div>'
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

                    $('#soalJawabanList').html(html);
                } else {
                    $('#soalJawabanList').html('<tr><td colspan="5" class="py-12 text-center text-slate-450">Tidak ada data soal dan jawaban.</td></tr>');
                    $('#statTotal').text(0);
                    $('#statBenar').text(0);
                    $('#statSalah').text(0);
                    $('#statTidakDijawab').text(0);
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

