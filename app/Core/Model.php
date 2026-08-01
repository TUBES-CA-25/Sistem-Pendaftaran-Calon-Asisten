<?php

namespace App\Core;

/**
 * Base Model.
 *
 * Menyediakan koneksi PDO bersama (singleton lewat Database::getInstance())
 * dan nama tabel untuk seluruh model turunan.
 *
 * CATATAN RIWAYAT:
 * Kelas ini dulu memuat query builder (all/find/paginate/first/where/orderBy/
 * groupBy/limit/offset/get). Semuanya DIHAPUS karena:
 *   1. Tidak dipakai sama sekali — 19 model seluruhnya memakai
 *      getDB()->prepare() secara langsung (0 pemanggilan di seluruh project).
 *   2. Mengandung tiga cacat yang membuatnya berbahaya bila dipakai:
 *      - find() menyusun "WHERE id = ?" (posisional) tapi mengirim parameter
 *        bernama ['id' => ...], sehingga query tidak pernah jalan.
 *      - groupBy() menulis ke $wheres, bukan $groupBy, sehingga cabang
 *        GROUP BY di get() tidak pernah tercapai.
 *      - $wheres/$orderBys/$limit/$offset bersifat static tetapi diubah lewat
 *        method instance dan tidak pernah di-reset, sehingga kondisi query
 *        bocor antar-pemanggilan dalam satu request.
 *
 * Bila suatu saat query builder memang diperlukan, tulis ulang dengan state
 * per-instance (bukan static) dan sertakan pengujian.
 */
abstract class Model {
    /** Nama tabel, didefinisikan oleh tiap model turunan. */
    protected static $table;

    /** Nama kolom primary key. */
    protected static $primaryKey = "id";

    /** Koneksi PDO bersama. */
    public static function getDB() {
        return Database::getInstance();
    }

    /**
     * Ambil baris mahasiswa berdasarkan id_user.
     *
     * Query "SELECT id FROM mahasiswa WHERE id_user = ?" sebelumnya disalin
     * identik di BerkasUser, JawabanExam, NilaiAkhir, dan PresentasiUser.
     *
     * PERHATIAN kontrak return: method ini mengembalikan ARRAY (['id' => ...])
     * atau null — sama seperti keempat salinan yang digantikannya, karena
     * pemanggilnya memang membaca ['id'].
     *
     * Wawancara::getIdMahasiswa() SENGAJA tidak ikut disatukan: method itu
     * mengembalikan SKALAR id, bukan array, dan pemanggilnya bergantung pada
     * bentuk tersebut. Menyatukannya akan diam-diam merusak jadwal wawancara.
     *
     * @return array|null
     */
    protected function getIdMahasiswa($idUser) {
        $query = "SELECT id FROM mahasiswa WHERE id_user = ?";
        $stmt = self::getDB()->prepare($query);
        $stmt->bindParam(1, $idUser, \PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $result ? $result : null;
    }
}
