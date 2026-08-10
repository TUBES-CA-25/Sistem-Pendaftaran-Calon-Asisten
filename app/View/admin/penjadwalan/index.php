<?php
/**
 * Penjadwalan - halaman induk dengan tiga tab.
 *
 * Menyatukan Tes Tertulis, Presentasi, dan Wawancara yang sebelumnya berdiri
 * sebagai tiga menu terpisah. Urutan tab mengikuti alur seleksi yang
 * sebenarnya: tes tertulis -> presentasi -> wawancara.
 *
 * CATATAN PENTING soal cara memuat isi tab:
 * Ketiga halaman asal memakai id yang bertabrakan - 'addJadwalModal' dipakai
 * ketiganya, sedangkan 'table-body', 'addJadwalForm', 'updateJadwalModal',
 * 'editId', 'editRuangan', 'editTanggal', dan 'editWaktu' dipakai dua di
 * antaranya. Skrip halamannya memakai delegasi dom.on() di document dengan
 * selektor id, sehingga jika ketiga markup hadir bersamaan di DOM, handler
 * satu tab akan menyambar elemen tab lain (mis. submit jadwal tes bisa
 * menyimpan ke wawancara).
 *
 * Karena itu isi tab TIDAK ditanam sekaligus, melainkan diambil lewat fetch
 * saat tab dibuka - persis mekanisme SPA yang sudah dipakai aplikasi ini.
 * Dengan begitu hanya satu markup tab yang pernah ada di DOM dan tabrakan id
 * tidak mungkin terjadi, tanpa perlu merename id di tiga view + tiga berkas JS.
 *
 * @var array $data
 */

// Tab yang aktif saat halaman dimuat. Rute lama masing-masing halaman tetap
// hidup dan mendarat di tabnya sendiri supaya tautan lama tidak mati.
$petaTab = [
    'jadwaltes'        => 'tes',
    'jadwalPresentasi' => 'presentasi',
    'wawancara'        => 'wawancara',
];
$halamanAwal = $data['tabAwal'] ?? ($initialPage ?? '');
$tabAwal = $petaTab[$halamanAwal] ?? 'tes';

$tabs = [
    'tes' => [
        'label' => 'Tes Tertulis',
        'ikon'  => 'bi bi-pencil-square',
        'page'  => 'jadwaltes',
        'urut'  => 1,
    ],
    'presentasi' => [
        'label' => 'Presentasi',
        'ikon'  => 'bi bi-easel',
        'page'  => 'jadwalPresentasi',
        'urut'  => 2,
    ],
    'wawancara' => [
        'label' => 'Wawancara',
        'ikon'  => 'bi bi-chat-dots',
        'page'  => 'wawancara',
        'urut'  => 3,
    ],
];
?>

<div id="pageHeaderWrapper" class="transition-all duration-300">
<?php
    $title = 'Penjadwalan';
    $subtitle = 'Atur jadwal tes tertulis, presentasi, dan wawancara';
    $icon = 'bi bi-calendar-event';
    require_once __DIR__ . '/../../templates/components/PageHeader.php';
?>
</div>

<main class="max-w-7xl mx-auto px-4 pt-0 pb-6">

    <!--
        Tab urut sesuai tahapan seleksi. Nomor urut ditampilkan supaya
        alurnya terbaca sebagai rangkaian, bukan tiga menu setara.
    -->
    <div id="jadwalTabs" class="flex items-center gap-1 mb-4 border-b border-slate-200 overflow-x-auto">
        <?php foreach ($tabs as $kunci => $tab): ?>
            <?php if ($kunci === $tabAwal): ?>
            <button type="button" data-tab-jadwal="<?= $kunci ?>" data-tab-page="<?= $tab['page'] ?>"
                    class="tab-jadwal shrink-0 px-4 py-2.5 text-sm font-bold rounded-t-lg border-b-2 transition-all flex items-center gap-2 border-blue-600 text-blue-600 bg-blue-50/50">
                <span class="w-5 h-5 rounded-full bg-blue-600 text-white text-[11px] flex items-center justify-center shrink-0"><?= $tab['urut'] ?></span>
                <i class="<?= $tab['ikon'] ?>"></i> <?= $tab['label'] ?>
            </button>
            <?php else: ?>
            <button type="button" data-tab-jadwal="<?= $kunci ?>" data-tab-page="<?= $tab['page'] ?>"
                    class="tab-jadwal shrink-0 px-4 py-2.5 text-sm font-bold rounded-t-lg border-b-2 transition-all flex items-center gap-2 border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50">
                <span class="w-5 h-5 rounded-full bg-slate-200 text-slate-600 text-[11px] flex items-center justify-center shrink-0"><?= $tab['urut'] ?></span>
                <i class="<?= $tab['ikon'] ?>"></i> <?= $tab['label'] ?>
            </button>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>

    <!-- Wadah isi tab. Diisi lewat fetch oleh penjadwalan.js -->
    <div id="jadwalTabContent" data-tab-aktif="<?= $tabAwal ?>">
        <div class="flex flex-col items-center justify-center py-20 text-slate-400">
            <i class="bi bi-arrow-repeat text-3xl animate-spin mb-2"></i>
            <p class="text-sm font-semibold">Memuat jadwal...</p>
        </div>
    </div>
</main>

<script>
    window.baseUrl = '<?= APP_URL ?>';
</script>
<script src="<?= APP_URL ?>/Assets/js/admin/penjadwalan.js?v=<?= time() ?>"></script>
