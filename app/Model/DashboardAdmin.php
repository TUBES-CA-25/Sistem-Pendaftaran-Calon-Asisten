<?php

namespace App\Model;

use App\Core\Model;
use PDO;

class DashboardAdmin extends Model
{
    protected static $tableMahasiswa = 'mahasiswa';

    public static function getTotalPendaftar(): int
    {
        try {
            $sql = "SELECT COUNT(*) FROM " . self::$tableMahasiswa;
            $stmt = self::getDB()->prepare($sql);
            $stmt->execute();
            return (int) $stmt->fetchColumn();
        } catch (\Throwable $e) {
            return 0;
        }
    }

    public static function getPendaftarLulus(): int
    {
        try {
            $sql = "SELECT COUNT(*) FROM " . self::$tableMahasiswa . " WHERE status_akhir = 'Lulus'";
            $stmt = self::getDB()->prepare($sql);
            $stmt->execute();
            return (int) $stmt->fetchColumn();
        } catch (\Throwable $e) {
            return 0;
        }
    }

    public static function getPendaftarGagal(): int
    {
        try {
            $sql = "SELECT COUNT(*) FROM " . self::$tableMahasiswa . " WHERE status_akhir = 'Tidak Lulus'";
            $stmt = self::getDB()->prepare($sql);
            $stmt->execute();
            return (int) $stmt->fetchColumn();
        } catch (\Throwable $e) {
            return 0;
        }
    }

    public static function getPendaftarPending(): int
    {
        try {
            // Pending includes explicitly 'Pending' OR NULL/Empty
            $sql = "SELECT COUNT(*) FROM " . self::$tableMahasiswa . " WHERE status_akhir = 'Pending' OR status_akhir IS NULL OR status_akhir = ''";
            $stmt = self::getDB()->prepare($sql);
            $stmt->execute();
            return (int) $stmt->fetchColumn();
        } catch (\Throwable $e) {
            return 0;
        }
    }

    /**
     * Jumlah pendaftar dikelompokkan per angkatan.
     *
     * Angkatan dibaca dari stambuk 11 digit dengan format:
     *   130      2023        0306
     *   |        |           |
     *   fakultas angkatan    nomor urut pendaftaran
     *   (1-3)    (4-7)       (8-11)
     *
     * REGEXP menyaring stambuk yang bukan 11 digit angka (mis. akun 'admin'),
     * supaya tidak memunculkan kelompok angkatan palsu.
     *
     * @return array<int, array{angkatan: string, jumlah: int}> terurut menaik
     */
    public static function getPendaftarPerAngkatan(): array
    {
        try {
            $sql = "SELECT SUBSTRING(stambuk, 4, 4) AS angkatan, COUNT(*) AS jumlah
                    FROM " . self::$tableMahasiswa . "
                    WHERE stambuk REGEXP '^[0-9]{11}$'
                    GROUP BY angkatan
                    ORDER BY angkatan ASC";
            $stmt = self::getDB()->prepare($sql);
            $stmt->execute();

            $hasil = [];
            foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $row) {
                $hasil[] = [
                    'angkatan' => (string) $row['angkatan'],
                    'jumlah'   => (int) $row['jumlah'],
                ];
            }
            return $hasil;
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * @return array<int, array{tanggal: string, judul: string, jenis: string, deskripsi?: string}>
     */
    public static function getKegiatanByMonth(int $year, int $month): array
    {
        $results = [];

        // Fetch from wawancara table (Distinct types per day)
        try {
            $sql = "SELECT DISTINCT tanggal, jenis_wawancara as judul FROM wawancara WHERE YEAR(tanggal) = :year AND MONTH(tanggal) = :month";
            $stmt = self::getDB()->prepare($sql);
            $stmt->bindValue(':year', $year, PDO::PARAM_INT);
            $stmt->bindValue(':month', $month, PDO::PARAM_INT);
            $stmt->execute();
            $wawancaraRows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($wawancaraRows) {
                foreach ($wawancaraRows as $row) {
                    $isTes = stripos($row['judul'], 'tes tertulis') !== false;
                    $results[] = [
                        'tanggal' => $row['tanggal'],
                        'judul' => $row['judul'],
                        'jenis' => $isTes ? 'Tes Tertulis' : 'Wawancara'
                    ];
                }
            }
        } catch (\Throwable $e) {}

        // Try pull from jadwal_presentasi
        $presentasi = self::selectTanggalByMonth('jadwal_presentasi', $year, $month, 'Presentasi', true);
        $results = array_merge($results, $presentasi);

        // Try pull from kegiatan_admin
        try {
            $sql = "SELECT id, judul, tanggal, deskripsi FROM kegiatan_admin WHERE YEAR(tanggal) = :year AND MONTH(tanggal) = :month";
            $stmt = self::getDB()->prepare($sql);
            $stmt->bindValue(':year', $year, PDO::PARAM_INT);
            $stmt->bindValue(':month', $month, PDO::PARAM_INT);
            $stmt->execute();
            $customRows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($customRows) {
                foreach ($customRows as $row) {
                    $results[] = [
                        'id' => $row['id'],
                        'tanggal' => $row['tanggal'],
                        'judul' => $row['judul'],
                        'jenis' => 'Kegiatan',
                        'deskripsi' => $row['deskripsi'] ?? ''
                    ];
                }
            }
        } catch (\Throwable $e) {}

        return $results;
    }

    /**
     * Kegiatan terdekat mulai hari ini, untuk panel ringkas di dashboard.
     *
     * Sengaja TIDAK dibatasi bulan berjalan (berbeda dari getKegiatanByMonth):
     * kegiatan terdekat bisa jatuh di bulan berikutnya, dan panel ini justru
     * berguna untuk mengingatkan hal itu.
     *
     * @return array<int, array{id: int, judul: string, tanggal: string, deskripsi: string}>
     */
    public static function getKegiatanMendatang(int $limit = 4): array
    {
        try {
            $sql = "SELECT id, judul, tanggal, deskripsi
                    FROM kegiatan_admin
                    WHERE tanggal >= CURDATE()
                    ORDER BY tanggal ASC
                    LIMIT :limit";
            $stmt = self::getDB()->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (\Throwable $e) {
            return [];
        }
    }

    public static function addKegiatan(array $data): bool
    {
        try {
            $sql = "INSERT INTO kegiatan_admin (judul, tanggal, deskripsi) VALUES (:judul, :tanggal, :deskripsi)";
            $stmt = self::getDB()->prepare($sql);
            $stmt->bindValue(':judul', $data['judul']);
            $stmt->bindValue(':tanggal', $data['tanggal']);
            $stmt->bindValue(':deskripsi', $data['deskripsi'] ?? '');
            return $stmt->execute();
        } catch (\Throwable $e) {
            return false;
        }
    }

    public static function updateKegiatan(array $data): bool
    {
        try {
            $sql = "UPDATE kegiatan_admin SET judul = :judul, tanggal = :tanggal, deskripsi = :deskripsi WHERE id = :id";
            $stmt = self::getDB()->prepare($sql);
            $stmt->bindValue(':judul', $data['judul']);
            $stmt->bindValue(':tanggal', $data['tanggal']);
            $stmt->bindValue(':deskripsi', $data['deskripsi'] ?? '');
            $stmt->bindValue(':id', $data['id'], PDO::PARAM_INT);
            return $stmt->execute();
        } catch (\Throwable $e) {
            return false;
        }
    }

    public static function deleteKegiatan(int $id): bool
    {
        try {
            $sql = "DELETE FROM kegiatan_admin WHERE id = :id";
            $stmt = self::getDB()->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Tahapan seleksi beserta urutannya - SATU-SATUNYA sumber kebenaran.
     *
     * Dipakai bersama oleh timeline admin (getStatusKegiatan) dan timeline
     * peserta (DashboardUser::getTimelineSeleksi). Ditaruh di satu tempat
     * supaya kedua sisi tidak bisa menampilkan daftar tahap yang berbeda.
     *
     * 'presentasi' sebelumnya tidak ada di sini padahal tahapan peserta
     * memuatnya, sehingga tahap itu satu-satunya yang tanggalnya tidak bisa
     * diatur admin.
     *
     * @var array<string, array{label: string, default: string}>
     */
    public const TAHAPAN_SELEKSI = [
        'kelengkapan_berkas' => ['label' => 'Kelengkapan Berkas', 'default' => '2026-02-01'],
        'tes_tertulis'       => ['label' => 'Tes Tertulis',       'default' => '2026-02-05'],
        'presentasi'         => ['label' => 'Presentasi',         'default' => '2026-02-10'],
        'tahap_wawancara'    => ['label' => 'Tahap Wawancara',    'default' => '2026-02-15'],
        'pengumuman'         => ['label' => 'Pengumuman',         'default' => '2026-02-28'],
    ];

    /**
     * Tanggal tiap tahap, sudah dilengkapi nilai bawaan bila belum diatur.
     *
     * @return array<string, string> jenis => tanggal (Y-m-d), urut sesuai tahapan
     */
    public static function getDeadlines(): array
    {
        $tersimpan = [];
        try {
            $stmt = self::getDB()->prepare("SELECT jenis, tanggal FROM deadline_kegiatan");
            $stmt->execute();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $tersimpan[$row['jenis']] = $row['tanggal'];
            }
        } catch (\Throwable $e) {}

        // Dibangun ulang mengikuti urutan TAHAPAN_SELEKSI, bukan urutan baris
        // di basis data - urutan tahap tidak boleh bergantung pada id/insert.
        $hasil = [];
        foreach (self::TAHAPAN_SELEKSI as $jenis => $def) {
            $hasil[$jenis] = $tersimpan[$jenis] ?? $def['default'];
        }
        return $hasil;
    }

    public static function updateDeadline(string $jenis, string $tanggal): bool
    {
        // Hanya tahap yang dikenal yang boleh disimpan. Tanpa ini sembarang
        // nilai `jenis` dari permintaan akan membuat baris baru di
        // deadline_kegiatan dan mengotori timeline kedua sisi.
        if (!isset(self::TAHAPAN_SELEKSI[$jenis])) {
            return false;
        }
        if (!\DateTime::createFromFormat('Y-m-d', $tanggal)) {
            return false;
        }

        try {
            // Upsert (Insert or Update on duplicate key)
            $sql = "INSERT INTO deadline_kegiatan (jenis, tanggal) VALUES (:jenis, :tanggal)
                    ON DUPLICATE KEY UPDATE tanggal = :tanggal_update";
            $stmt = self::getDB()->prepare($sql);
            $stmt->bindValue(':jenis', $jenis);
            $stmt->bindValue(':tanggal', $tanggal);
            $stmt->bindValue(':tanggal_update', $tanggal);
            return $stmt->execute();
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * @return array<string, array{jumlah: int, label: string, deadline: ?string, status: string, css_class: string}>
     */
    public static function getStatusKegiatan(): array
    {
        // 1. Tanggal tiap tahap (sudah termasuk nilai bawaan bila belum diatur)
        $deadlines = self::getDeadlines();

        // 2. Build Status Sequence
        $today = date('Y-m-d');

        // Helper to determining status
        $determineStatus = function($deadline, $prevStatusIsDone) use ($today) {
            // If previous stage isn't done, this one is "Akan Datang" (unless it's the first one)
            if (!$prevStatusIsDone) {
                return [
                    'status' => 'Akan Datang',
                    'css_class' => 'bg-slate-100 text-slate-500'
                ];
            }

            // If we are past the deadline, it's "Selesai"
            if ($deadline && $today > $deadline) {
                return [
                    'status' => 'Selesai',
                    'css_class' => 'bg-emerald-50 text-emerald-600'
                ];
            }

            // Otherwise, it's "Sedang Berlangsung"
            return [
                'status' => 'Sedang Berlangsung',
                'css_class' => 'bg-amber-50 text-amber-600'
            ];
        };

        // Initialize status array
        $status = [];

        // 1. Kelengkapan Berkas (First stage, always starts if not done)
        // Check if deadline passed
        $berkasDeadline = $deadlines['kelengkapan_berkas'];
        $berkasIsDone = ($berkasDeadline && $today > $berkasDeadline);
        
        $berkasState = $berkasIsDone 
            ? ['status' => 'Selesai', 'css_class' => 'bg-emerald-50 text-emerald-600']
            : ['status' => 'Sedang Berlangsung', 'css_class' => 'bg-amber-50 text-amber-600'];

        $status['kelengkapan_berkas'] = [
            'label' => 'Kelengkapan Berkas',
            'jumlah' => 0,
            'deadline' => $berkasDeadline,
            'status' => $berkasState['status'],
            'css_class' => $berkasState['css_class']
        ];

        // 2. Tes Tertulis (Depends on Kelengkapan Berkas)
        $tesDeadline = $deadlines['tes_tertulis'];
        $tesState = $determineStatus($tesDeadline, $berkasIsDone);
        $tesIsDone = ($tesState['status'] === 'Selesai');

        $status['tes_tertulis'] = [
            'label' => 'Tes Tertulis',
            'jumlah' => 0,
            'deadline' => $tesDeadline,
            'status' => $tesState['status'],
            'css_class' => $tesState['css_class']
        ];

        // 3. Presentasi (Depends on Tes Tertulis)
        $presentasiDeadline = $deadlines['presentasi'];
        $presentasiState = $determineStatus($presentasiDeadline, $tesIsDone);
        $presentasiIsDone = ($presentasiState['status'] === 'Selesai');

        $status['presentasi'] = [
            'label' => self::TAHAPAN_SELEKSI['presentasi']['label'],
            'jumlah' => 0,
            'deadline' => $presentasiDeadline,
            'status' => $presentasiState['status'],
            'css_class' => $presentasiState['css_class']
        ];

        // 4. Tahap Wawancara (Depends on Presentasi)
        $wawancaraDeadline = $deadlines['tahap_wawancara'];
        $wawancaraState = $determineStatus($wawancaraDeadline, $presentasiIsDone);
        $wawancaraIsDone = ($wawancaraState['status'] === 'Selesai');

        $status['tahap_wawancara'] = [
            'label' => 'Tahap Wawancara',
            'jumlah' => 0,
            'deadline' => $wawancaraDeadline,
            'status' => $wawancaraState['status'],
            'css_class' => $wawancaraState['css_class']
        ];

        // 5. Pengumuman (Depends on Tahap Wawancara)
        $pengumumanDeadline = $deadlines['pengumuman'];
        $pengumumanState = $determineStatus($pengumumanDeadline, $wawancaraIsDone);
        
        $status['pengumuman'] = [
            'label' => 'Pengumuman',
            'jumlah' => 0,
            'deadline' => $pengumumanDeadline,
            'status' => $pengumumanState['status'],
            'css_class' => $pengumumanState['css_class']
        ];

        // 2. Fetch Counts
        // Kelengkapan berkas: berkas_mahasiswa.accepted = 1
        try {
            $sql = "SELECT COUNT(*) FROM berkas_mahasiswa WHERE accepted = 1";
            $stmt = self::getDB()->prepare($sql);
            $stmt->execute();
            $status['kelengkapan_berkas']['jumlah'] = (int) $stmt->fetchColumn();
        } catch (\Throwable $e) {
        }

        // Tes tertulis: absensi.absensi_tes_tertulis = 'Hadir' (fallback: nilai_akhir rows)
        try {
            $sql = "SELECT COUNT(*) FROM absensi WHERE absensi_tes_tertulis = 'Hadir'";
            $stmt = self::getDB()->prepare($sql);
            $stmt->execute();
            $status['tes_tertulis']['jumlah'] = (int) $stmt->fetchColumn();
        } catch (\Throwable $e) {
            try {
                $sql = "SELECT COUNT(*) FROM nilai_akhir";
                $stmt = self::getDB()->prepare($sql);
                $stmt->execute();
                $status['tes_tertulis']['jumlah'] = (int) $stmt->fetchColumn();
            } catch (\Throwable $e2) {
            }
        }

        // Presentasi: peserta yang kehadiran presentasinya sudah ditandai Hadir
        try {
            $sql = "SELECT COUNT(*) FROM absensi WHERE absensi_presentasi = 'Hadir'";
            $stmt = self::getDB()->prepare($sql);
            $stmt->execute();
            $status['presentasi']['jumlah'] = (int) $stmt->fetchColumn();
        } catch (\Throwable $e) {
        }

        // Tahap wawancara: any wawancara attendance marked Hadir
        try {
            $sql = "SELECT COUNT(*) FROM absensi WHERE absensi_wawancara_I = 'Hadir' OR absensi_wawancara_II = 'Hadir' OR absensi_wawancara_III = 'Hadir'";
            $stmt = self::getDB()->prepare($sql);
            $stmt->execute();
            $status['tahap_wawancara']['jumlah'] = (int) $stmt->fetchColumn();
        } catch (\Throwable $e) {
        }

        // Pengumuman: fallback pakai jumlah lulus
        $status['pengumuman']['jumlah'] = self::getPendaftarLulus();

        return $status;
    }



    /**
     * @return array<int, array{tanggal: string, judul: string, jenis: string}>
     */
    private static function selectTanggalByMonth(string $table, int $year, int $month, string $jenis, bool $excludePast = false): array
    {
        try {
            // Check if 'judul' column exists or if we need to infer it
            // For jadwal_wawancara and jadwal_presentasi, we might not have 'judul' directly.
            // Let's assume they don't have 'judul' for now and just use a generic name, 
            // OR if table is 'kegiatan_admin', we use 'judul'.
            
            $columns = "tanggal";
            if ($table === 'kegiatan_admin') {
                $columns .= ", judul, deskripsi";
            }
            
            $sql = "SELECT {$columns} FROM {$table} WHERE YEAR(tanggal) = :year AND MONTH(tanggal) = :month";
            
            if ($excludePast) {
                $sql .= " AND tanggal >= CURDATE()";
            }

            $stmt = self::getDB()->prepare($sql);
            $stmt->bindValue(':year', $year, PDO::PARAM_INT);
            $stmt->bindValue(':month', $month, PDO::PARAM_INT);
            $stmt->execute();

            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $rows = is_array($rows) ? $rows : [];

            $result = [];
            foreach ($rows as $row) {
                if (!isset($row['tanggal'])) {
                    continue;
                }
                
                $title = $jenis;
                if ($table === 'kegiatan_admin' && !empty($row['judul'])) {
                    $title = $row['judul'];
                }
                
                $item = [
                    'tanggal' => (string) $row['tanggal'], 
                    'judul' => $title,
                    'jenis' => $jenis
                ];

                if ($table === 'kegiatan_admin' && isset($row['deskripsi'])) {
                    $item['deskripsi'] = $row['deskripsi'];
                }

                $result[] = $item;
            }
            return $result;
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Get statistics for presentation fullness chart
     * @return array{scheduled: int, eligible: int}
     */
    public static function getPresentationStats(): array
    {
        $stats = ['scheduled' => 0, 'eligible' => 0];

        try {
            // Count Scheduled (Distinct presentations that have a schedule)
            $sql = "SELECT COUNT(DISTINCT id_presentasi) FROM jadwal_presentasi";
            $stmt = self::getDB()->prepare($sql);
            $stmt->execute();
            $stats['scheduled'] = (int) $stmt->fetchColumn();

            // Count Eligible (Presentations that are accepted)
            $sql = "SELECT COUNT(*) FROM presentasi WHERE is_accepted = 1";
            $stmt = self::getDB()->prepare($sql);
            $stmt->execute();
            $stats['eligible'] = (int) $stmt->fetchColumn();

        } catch (\Throwable $e) {
            // Return defaults on error
        }

        return $stats;
    }
}
