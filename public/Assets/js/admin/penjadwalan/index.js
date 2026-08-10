/**
 * admin/penjadwalan/index.js
 *
 * Menggerakkan tab pada halaman Penjadwalan (Tes Tertulis -> Presentasi ->
 * Wawancara).
 *
 * Isi tiap tab diambil lewat fetch, bukan ditanam sekaligus di markup.
 * Alasannya ada di komentar app/View/admin/penjadwalan/index.php: ketiga
 * halaman asal memakai id yang bertabrakan ('addJadwalModal' di ketiganya,
 * 'table-body' dan 'addJadwalForm' di dua di antaranya) sementara skripnya
 * memakai delegasi dom.on() di document. Kalau ketiga markup hadir bersamaan,
 * handler satu tab akan menyambar elemen tab lain. Dengan lazy-load hanya satu
 * markup tab yang pernah ada di DOM sehingga tabrakan itu tidak mungkin.
 *
 * Bergantung pada: window.baseUrl (dari view), dom.js, ui.js.
 */
(function () {
    'use strict';

    var wadah = document.getElementById('jadwalTabContent');
    var barisTab = document.getElementById('jadwalTabs');
    if (!wadah || !barisTab) return;

    var AKTIF = 'border-blue-600 text-blue-600 bg-blue-50/50';
    var NONAKTIF = 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50';
    var DASAR = 'tab-jadwal shrink-0 px-4 py-2.5 text-sm font-bold rounded-t-lg border-b-2 transition-all flex items-center gap-2 ';
    var LENCANA_AKTIF = 'w-5 h-5 rounded-full bg-blue-600 text-white text-[11px] flex items-center justify-center shrink-0';
    var LENCANA_NONAKTIF = 'w-5 h-5 rounded-full bg-slate-200 text-slate-600 text-[11px] flex items-center justify-center shrink-0';

    // Cache markup tiap tab supaya berpindah bolak-balik tidak menembak
    // server berulang kali.
    var cache = {};
    var sedangMemuat = false;

    function tampilkanMemuat() {
        wadah.innerHTML =
            '<div class="flex flex-col items-center justify-center py-20 text-slate-400">' +
            '<i class="bi bi-arrow-repeat text-3xl animate-spin mb-2"></i>' +
            '<p class="text-sm font-semibold">Memuat jadwal...</p></div>';
    }

    function tampilkanGagal(kunci, halaman) {
        wadah.innerHTML =
            '<div class="flex flex-col items-center justify-center py-16 text-center">' +
            '<div class="w-16 h-16 rounded-full bg-red-50 text-red-500 flex items-center justify-center text-2xl mb-3">' +
            '<i class="bi bi-exclamation-triangle"></i></div>' +
            '<h4 class="text-base font-bold text-slate-800 mb-1">Gagal Memuat Jadwal</h4>' +
            '<p class="text-slate-500 text-xs mb-4">Periksa koneksi Anda lalu coba lagi.</p>' +
            '<button type="button" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-lg transition" ' +
            'data-muat-ulang="' + kunci + '" data-muat-page="' + halaman + '">Coba Lagi</button></div>';
    }

    /**
     * Menjalankan <script> di dalam markup yang baru disuntik. innerHTML tidak
     * pernah mengeksekusi script, jadi harus dibuat ulang - meniru perilaku
     * navigasi SPA di core/app.js.
     */
    function jalankanSkrip(induk) {
        var daftar = induk.querySelectorAll('script');
        Array.prototype.forEach.call(daftar, function (lama) {
            var baru = document.createElement('script');
            Array.prototype.forEach.call(lama.attributes, function (a) {
                baru.setAttribute(a.name, a.value);
            });
            if (lama.src) {
                baru.src = lama.src;
            } else {
                baru.textContent = lama.textContent;
            }
            lama.parentNode.replaceChild(baru, lama);
        });
    }

    /**
     * Menyalakan ulang VanillaPaginator untuk tabel yang baru disuntik.
     * Fungsinya dideklarasikan top-level di core/app.js sehingga tersedia
     * sebagai window._initVanillaPaginators; kedua bentuk tetap diperiksa
     * supaya tab tidak ikut mati kalau berkas itu berubah bentuk.
     */
    function initPaginator() {
        var f = (typeof window._initVanillaPaginators === 'function')
            ? window._initVanillaPaginators
            : (typeof _initVanillaPaginators === 'function' ? _initVanillaPaginators : null);
        if (f) {
            try { f(wadah); } catch (err) { /* tabel tanpa paginator - abaikan */ }
        }
    }

    /**
     * Menyelaraskan URL dengan tab yang sedang aktif.
     *
     * Tanpa ini URL selalu /penjadwalan apa pun tabnya, sehingga menyegarkan
     * halaman selalu kembali ke tab pertama - pekerjaan admin yang sedang di
     * tab Wawancara jadi terputus.
     *
     * Dipakai replaceState, bukan pushState: berpindah tab bukan navigasi
     * halaman baru, jadi tidak perlu menambah entri riwayat. Ini juga
     * mencegah tombol Back menelusuri tab satu per satu sebelum benar-benar
     * meninggalkan halaman.
     *
     * State disamakan bentuknya dengan core/app.js ({page: ...}) supaya
     * handler popstate di sana tetap mengenalinya.
     */
    function selaraskanUrl(kunci) {
        var tombol = barisTab.querySelector('[data-tab-jadwal="' + kunci + '"]');
        if (!tombol || !window.history || !history.replaceState) return;

        var halaman = tombol.getAttribute('data-tab-page');
        try {
            history.replaceState({ page: halaman }, '', window.baseUrl + '/' + halaman);
        } catch (e) {
            /* Beberapa peramban menolak replaceState pada origin tertentu;
               kegagalan di sini tidak boleh menghentikan perpindahan tab. */
        }
    }

    function segarkanTab(kunci) {
        var tombol = barisTab.querySelectorAll('[data-tab-jadwal]');
        Array.prototype.forEach.call(tombol, function (t) {
            var ini = t.getAttribute('data-tab-jadwal') === kunci;
            t.className = DASAR + (ini ? AKTIF : NONAKTIF);
            var lencana = t.querySelector('span');
            if (lencana) lencana.className = ini ? LENCANA_AKTIF : LENCANA_NONAKTIF;
        });
        wadah.setAttribute('data-tab-aktif', kunci);
    }

    function muatTab(kunci, halaman, paksa) {
        if (sedangMemuat) return;

        segarkanTab(kunci);

        if (!paksa && cache[kunci]) {
            wadah.innerHTML = cache[kunci];
            jalankanSkrip(wadah);
            initPaginator();
            return;
        }

        sedangMemuat = true;
        tampilkanMemuat();

        fetch(window.baseUrl + '/' + halaman, {
            method: 'GET',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin'
        })
            .then(function (res) {
                if (!res.ok) throw new Error('HTTP ' + res.status);
                return res.text();
            })
            .then(function (html) {
                // Buang PageHeader bawaan halaman asal - induk sudah punya
                // headernya sendiri, kalau dibiarkan judulnya dobel.
                //
                // Disasar lewat [data-page-header] (penanda di
                // templates/components/PageHeader.php). Sebelumnya memakai
                // '#pageHeaderWrapper', tetapi pembungkus itu hanya ada di
                // sebagian halaman - ketiga halaman jadwal merender <nav>
                // headernya langsung tanpa pembungkus, sehingga header lama
                // tetap ikut terbawa ke dalam tab.
                var kotak = document.createElement('div');
                kotak.innerHTML = html;
                var header = kotak.querySelectorAll('[data-page-header], #pageHeaderWrapper');
                Array.prototype.forEach.call(header, function (el) { el.remove(); });

                cache[kunci] = kotak.innerHTML;
                wadah.innerHTML = cache[kunci];
                jalankanSkrip(wadah);
                initPaginator();
            })
            .catch(function () {
                tampilkanGagal(kunci, halaman);
            })
            .then(function () {
                sedangMemuat = false;
            });
    }

    // Klik tab. Delegasi di baris tab supaya tetap hidup walau tombolnya
    // dirender ulang.
    barisTab.addEventListener('click', function (e) {
        var tombol = e.target.closest('[data-tab-jadwal]');
        if (!tombol) return;
        var kunci = tombol.getAttribute('data-tab-jadwal');
        if (kunci === wadah.getAttribute('data-tab-aktif') && cache[kunci]) return;
        muatTab(kunci, tombol.getAttribute('data-tab-page'), false);
        // URL diselaraskan hanya saat admin berpindah tab. Pemuatan awal dan
        // muat-ulang setelah simpan tidak perlu menyentuh URL - keduanya sudah
        // berada pada alamat yang benar.
        selaraskanUrl(kunci);
    });

    // Tombol "Coba Lagi" pada tampilan gagal.
    wadah.addEventListener('click', function (e) {
        var tombol = e.target.closest('[data-muat-ulang]');
        if (!tombol) return;
        muatTab(tombol.getAttribute('data-muat-ulang'), tombol.getAttribute('data-muat-page'), true);
    });

    /**
     * Membuang cache satu tab lalu memuatnya lagi. Dipanggil skrip tab setelah
     * menyimpan/menghapus jadwal supaya tabel ikut menyegar.
     */
    window.muatUlangTabJadwal = function (kunci) {
        var target = kunci || wadah.getAttribute('data-tab-aktif');
        var tombol = barisTab.querySelector('[data-tab-jadwal="' + target + '"]');
        if (!tombol) return;
        delete cache[target];
        muatTab(target, tombol.getAttribute('data-tab-page'), true);
    };

    // Muat tab awal.
    var awal = wadah.getAttribute('data-tab-aktif') || 'tes';
    var tombolAwal = barisTab.querySelector('[data-tab-jadwal="' + awal + '"]');
    if (tombolAwal) muatTab(awal, tombolAwal.getAttribute('data-tab-page'), true);
})();
