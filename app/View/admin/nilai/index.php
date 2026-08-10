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

        <div class="max-w-7xl mx-auto pt-0 pb-6">


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
                                        if ($score>= 70) {
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
    <div id="view-detail" class="hidden max-w-7xl mx-auto py-6">
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

                <?php /* Kelas warna sengaja TIDAK ditulis di sini. resetFilterButtons()
                        di nilai.js yang memasang FILTER_ON/FILTER_OFF; kalau markup ikut
                        membawa kelas warna sendiri, keduanya menumpuk dan tombol aktif
                        tampil keliru pada klik pertama. */ ?>
                <div class="bg-slate-50 p-1 rounded-lg border border-slate-200 flex gap-1">
                    <button class="filter-btn px-3 py-1.5 border text-xs font-semibold rounded-md transition" data-filter="semua">Semua</button>
                    <button class="filter-btn px-3 py-1.5 border text-xs font-semibold rounded-md transition" data-filter="pilihan_ganda">Pil. Ganda</button>
                    <button class="filter-btn px-3 py-1.5 border text-xs font-semibold rounded-md transition" data-filter="essay">Essay</button>
                </div>
            </div>
        </div>

        <?php /* Kartu statistik. Keempat id di bawah SUDAH ditulis nilai.js sejak lama
                (#statTotal/#statBenar/#statSalah/#statTidakDijawab) tetapi elemennya
                tidak pernah ada di markup, jadi angkanya tidak pernah terlihat.
                #statPgBenar / #statPgSalah dipertahankan sebagai rincian PG. */ ?>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center shrink-0">
                        <i class="bi bi-list-ol text-lg"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Soal</div>
                        <div class="text-2xl font-bold text-slate-800 leading-tight" id="statTotal">0</div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                        <i class="bi bi-check-circle-fill text-lg"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Benar</div>
                        <div class="text-2xl font-bold text-emerald-600 leading-tight" id="statBenar">0</div>
                        <div class="text-[10px] font-semibold text-slate-400">PG: <span id="statPgBenar">0</span></div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-red-50 text-red-600 flex items-center justify-center shrink-0">
                        <i class="bi bi-x-circle-fill text-lg"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Salah</div>
                        <div class="text-2xl font-bold text-red-600 leading-tight" id="statSalah">0</div>
                        <div class="text-[10px] font-semibold text-slate-400">PG: <span id="statPgSalah">0</span></div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                        <i class="bi bi-pencil-square text-lg"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Tidak Dijawab</div>
                        <div class="text-2xl font-bold text-amber-600 leading-tight" id="statTidakDijawab">0</div>
                        
                    </div>
                </div>
            </div>
        </div>


        <?php /* Daftar pekerjaan peserta.

                Struktur <table> DIPERTAHANKAN meski isinya kini berupa kartu:
                VanillaPaginator beroperasi pada baris tabel, dan filter tipe soal
                membaca data-tipe di <tr>. Mengganti ke <div> murni akan mematikan
                paginasi sekaligus filter.

                Header disembunyikan karena tiap baris hanya berisi SATU sel kartu -
                judul kolom per-field sudah tidak punya arti. */ ?>
        <div class="pb-10">
            <table class="w-full border-collapse no-datatable" id="soalJawabanTable">
                <thead class="hidden">
                    <tr><th>Soal</th></tr>
                </thead>
                <tbody id="soalJawabanList">
                    <tr>
                        <td class="py-12 text-center">
                            <i class="bi bi-hourglass-split text-4xl text-slate-300 mb-3 block"></i>
                            <p class="text-slate-500 font-medium">Memuat data pekerjaan...</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</main>

<script src="<?= APP_URL ?>/Assets/js/admin/nilai.js?v=<?= time() ?>"></script>

