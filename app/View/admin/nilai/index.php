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
                                                    data-foto="<?= htmlspecialchars(\App\Controllers\HomeController::getUserPhotoPath($value['foto'] ?? 'default.png')) ?>"
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
        <?php /* Kartu identitas peserta.
                Sebelumnya nama, nilai akhir, form simpan, dan tombol filter berdesakan
                dalam satu baris putih sehingga tidak ada yang menonjol. Sekarang
                dipisah: identitas + nilai di kartu ini, filter turun ke atas daftar. */ ?>
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-primary to-secondary shadow-lg mb-5">
            <div class="absolute -top-16 -right-10 w-56 h-56 bg-white/10 rounded-full pointer-events-none"></div>
            <div class="absolute -bottom-20 left-24 w-40 h-40 bg-white/10 rounded-full pointer-events-none"></div>

            <div class="relative z-10 p-5 flex flex-col lg:flex-row lg:items-center gap-5">
                <div class="flex items-center gap-4 min-w-0 flex-1">
                    <button type="button" id="btnBack" aria-label="Kembali ke daftar"
                            class="shrink-0 w-10 h-10 rounded-xl bg-white/15 hover:bg-white/25 border border-white/25 text-white flex items-center justify-center transition">
                        <i class="bi bi-arrow-left text-lg"></i>
                    </button>
                    <img id="detailFoto" src="<?= APP_URL ?>/Assets/Downloads/default.png" alt=""
                         class="w-14 h-14 rounded-2xl object-cover border-2 border-white/40 shadow-md shrink-0 bg-white/20"
                         onerror="this.src='<?= APP_URL ?>/Assets/Downloads/default.png'">
                    <div class="min-w-0">
                        <h2 class="text-xl font-bold text-white truncate leading-tight" id="detailNama">Nama Calon Asisten</h2>
                        <span class="inline-flex items-center gap-1.5 mt-1.5 px-2.5 py-1 rounded-lg bg-white/15 border border-white/25 text-white text-xs font-semibold">
                            <i class="bi bi-person-vcard"></i><span id="detailStambuk">Stambuk</span>
                        </span>
                    </div>
                </div>

                <div class="shrink-0 rounded-2xl bg-white/15 border border-white/25 p-4 w-full lg:w-auto">
                    <div class="flex items-center gap-5">
                        <div class="text-center shrink-0">
                            <div class="text-[10px] font-bold uppercase tracking-wider text-white/70">Nilai Akhir</div>
                            <div class="text-3xl font-bold text-white leading-none mt-1 tabular-nums" id="detailTotalNilai">-</div>
                            <div class="text-[10px] font-semibold text-white/70 mt-1" id="detailNilaiKet">&nbsp;</div>
                        </div>
                        <div class="w-px h-14 bg-white/25"></div>
                        <!-- Manual scoring is now handled automatically -->
                        <form id="formNilaiAkhir" class="hidden flex items-end gap-2">
                            <div>
                                <label for="nilaiAkhir" class="block text-[10px] font-bold uppercase tracking-wider text-white/70 mb-1">Ubah Nilai</label>
                                <input type="number" id="nilaiAkhir" min="0" max="100" placeholder="0-100"
                                       class="w-24 px-3 py-2 rounded-xl border border-white/30 bg-white text-slate-800 text-sm font-bold text-center focus:outline-none focus:ring-2 focus:ring-white">
                            </div>
                            <button type="submit" class="px-4 py-2 rounded-xl bg-white text-blue-700 hover:bg-blue-50 font-bold text-sm transition shadow-sm flex items-center gap-1.5 whitespace-nowrap">
                                <i class="bi bi-check-lg"></i> Simpan
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <?php /* Bar akurasi: benar / total. Diisi nilai.js dari angka yang memang
                    sudah dihitung untuk kartu statistik, jadi tidak ada perhitungan baru. */ ?>
            <div class="relative z-10 px-5 pb-4">
                <div class="flex items-center justify-between text-[11px] font-semibold text-white/80 mb-1.5">
                    <span>Jawaban benar</span>
                    <span><span id="detailPersen">0</span>%</span>
                </div>
                <div class="h-2 rounded-full bg-white/20 overflow-hidden">
                    <div id="detailProgress" class="h-full rounded-full bg-white transition-all duration-500" style="width: 0%"></div>
                </div>
            </div>
        </div>

        <?php /* Kartu statistik. Keempat id di bawah SUDAH ditulis nilai.js sejak lama
                (#statTotal/#statBenar/#statSalah/#statTidakDijawab) tetapi elemennya
                tidak pernah ada di markup, jadi angkanya tidak pernah terlihat.
                #statPgBenar / #statPgSalah dipertahankan sebagai rincian PG. */ ?>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-3">
            <div class="relative bg-white rounded-2xl border border-slate-200 shadow-sm p-4 pt-5 overflow-hidden hover:shadow-md hover:border-slate-300 transition-all duration-200">
                <span class="absolute inset-x-0 top-0 h-1 bg-slate-300"></span>
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center shrink-0">
                        <i class="bi bi-list-ol text-xl"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Soal</div>
                        <div class="text-3xl font-bold text-slate-800 leading-none mt-0.5 tabular-nums" id="statTotal">0</div>
                    </div>
                </div>
            </div>

            <div class="relative bg-white rounded-2xl border border-slate-200 shadow-sm p-4 pt-5 overflow-hidden hover:shadow-md hover:border-emerald-200 transition-all duration-200">
                <span class="absolute inset-x-0 top-0 h-1 bg-emerald-400"></span>
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                        <i class="bi bi-check-circle-fill text-xl"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Benar</div>
                        <div class="text-3xl font-bold text-emerald-600 leading-none mt-0.5 tabular-nums" id="statBenar">0</div>
                        <div class="text-[10px] font-semibold text-slate-400 mt-1">Pil. Ganda: <span id="statPgBenar">0</span></div>
                    </div>
                </div>
            </div>

            <div class="relative bg-white rounded-2xl border border-slate-200 shadow-sm p-4 pt-5 overflow-hidden hover:shadow-md hover:border-red-200 transition-all duration-200">
                <span class="absolute inset-x-0 top-0 h-1 bg-red-400"></span>
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl bg-red-50 text-red-600 flex items-center justify-center shrink-0">
                        <i class="bi bi-x-circle-fill text-xl"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Salah</div>
                        <div class="text-3xl font-bold text-red-600 leading-none mt-0.5 tabular-nums" id="statSalah">0</div>
                        <div class="text-[10px] font-semibold text-slate-400 mt-1">Pil. Ganda: <span id="statPgSalah">0</span></div>
                    </div>
                </div>
            </div>

            <div class="relative bg-white rounded-2xl border border-slate-200 shadow-sm p-4 pt-5 overflow-hidden hover:shadow-md hover:border-amber-200 transition-all duration-200">
                <span class="absolute inset-x-0 top-0 h-1 bg-amber-400"></span>
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                        <i class="bi bi-pencil-square text-xl"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Tidak Dijawab</div>
                        <div class="text-3xl font-bold text-amber-600 leading-none mt-0.5 tabular-nums" id="statTidakDijawab">0</div>
                        <div class="text-[10px] font-semibold text-slate-400 mt-1">Essay kosong</div>
                    </div>
                </div>
            </div>
        </div>

        <?php /* Keterangan ini menjelaskan kenapa PG kosong muncul sebagai "Salah
                (Kosong)" di kartu soal tetapi tidak ikut dihitung di "Tidak Dijawab" -
                tanpa itu kedua angka terbaca bertentangan. Aturan hitungnya sendiri
                tidak diubah. */ ?>
        <p class="text-xs text-slate-500 mb-6 flex items-start gap-1.5">
            <i class="bi bi-info-circle text-slate-400 mt-0.5"></i>
            <span>Pilihan ganda yang dikosongkan dihitung sebagai <strong class="font-semibold text-slate-600">Salah</strong>; kartu <strong class="font-semibold text-slate-600">Tidak Dijawab</strong> hanya menghitung essay yang kosong.</span>
        </p>

        <?php /* Baris filter. Dipindah dari header supaya berdekatan dengan daftar yang
                disaringnya. Kelas warna sengaja TIDAK ditulis di sini: resetFilterButtons()
                di nilai.js yang memasang FILTER_ON/FILTER_OFF; kalau markup ikut membawa
                kelas warna sendiri, keduanya menumpuk dan tombol aktif tampil keliru. */ ?>
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-3">
            <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                <i class="bi bi-list-check text-blue-600 text-base"></i> Rincian Pekerjaan
            </h3>
            <div class="bg-white p-1 rounded-xl border border-slate-200 shadow-sm flex gap-1 w-full sm:w-auto">
                <button class="filter-btn flex-1 sm:flex-none px-3.5 py-1.5 border text-xs font-bold rounded-lg transition" data-filter="semua">Semua</button>
                <button class="filter-btn flex-1 sm:flex-none px-3.5 py-1.5 border text-xs font-bold rounded-lg transition" data-filter="pilihan_ganda">Pil. Ganda</button>
                <button class="filter-btn flex-1 sm:flex-none px-3.5 py-1.5 border text-xs font-bold rounded-lg transition" data-filter="essay">Essay</button>
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

