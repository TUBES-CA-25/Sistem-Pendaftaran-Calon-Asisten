<?php

namespace App\Model\admin;

use App\Core\Model;
use PDO;

class DashboardAdmin extends Model
{
    protected static $tableMahasiswa = 'mahasiswa';

    public static function getTotalPendaftar(): int
    {
        try {
            $sql = "SELECT COUNT(*) FROM absensi"; // Changed from tableMahasiswa to absensi to match Monitoring view
            $stmt = self::getDB()->prepare($sql);
            $stmt->execute();
            return (int) $stmt->fetchColumn();
        } catch (\Throwable $e) {
            return 0;
        }
    }

    public static function getPendaftarLulus(): int
    {
        return self::countByNilaiThreshold('>=', 70);
    }

    public static function getPendaftarGagal(): int
    {
        return self::countByNilaiThreshold('<', 70);
    }

    public static function getPendaftarPending(): int
    {
        try {
            $total = self::getTotalPendaftar();
            $lulus = self::getPendaftarLulus();
            $gagal = self::getPendaftarGagal();
            $pending = $total - $lulus - $gagal;
            return $pending > 0 ? $pending : 0;
        } catch (\Throwable $e) {
            return 0;
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
                    $results[] = [
                        'tanggal' => $row['tanggal'],
                        'judul' => $row['judul'],
                        'jenis' => 'Wawancara'
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

    public static function updateDeadline(string $jenis, string $tanggal): bool
    {
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
        // 1. Determine Deadlines from DB
        $deadlines = [];
        try {
            $sql = "SELECT jenis, tanggal FROM deadline_kegiatan";
            $stmt = self::getDB()->prepare($sql);
            $stmt->execute();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $deadlines[$row['jenis']] = $row['tanggal'];
            }
        } catch (\Throwable $e) {}

        // Fallbacks if DB is empty or missing keys
        $defaultDeadlines = [
            'kelengkapan_berkas' => '2026-02-01',
            'tes_tertulis' => '2026-02-05',
            'tahap_wawancara' => '2026-02-15',
            'pengumuman' => '2026-02-28'
        ];
        
        foreach ($defaultDeadlines as $key => $val) {
            if (!isset($deadlines[$key])) {
                $deadlines[$key] = $val;
            }
        }

        // Special dynamic override for Wawancara if desired, 
        // BUT user asked for "bisa diatur sendiri". So we should probably respect the DB value if set.
        // However, if the user explicitly saves a date, it will be in the DB.
        // 2. Build Status Sequence
        $today = date('Y-m-d');

        // Helper to determining status
        $determineStatus = function($deadline, $prevStatusIsDone) use ($today) {
            // If previous stage isn't done, this one is "Akan Datang" (unless it's the first one)
            if (!$prevStatusIsDone) {
                return [
                    'status' => 'Akan Datang',
                    'css_class' => 'bg-secondary bg-opacity-10 text-secondary' // Gray
                ];
            }

            // If we are past the deadline, it's "Selesai"
            if ($deadline && $today > $deadline) {
                return [
                    'status' => 'Selesai',
                    'css_class' => 'bg-success bg-opacity-10 text-success' // Green
                ];
            }

            // Otherwise, it's "Sedang Berlangsung"
            return [
                'status' => 'Sedang Berlangsung',
                'css_class' => 'bg-warning bg-opacity-10 text-warning' // Yellow
            ];
        };

        // Initialize status array
        $status = [];

        // 1. Kelengkapan Berkas (First stage, always starts if not done)
        // Check if deadline passed
        $berkasDeadline = $deadlines['kelengkapan_berkas'];
        $berkasIsDone = ($berkasDeadline && $today > $berkasDeadline);
        
        $berkasState = $berkasIsDone 
            ? ['status' => 'Selesai', 'css_class' => 'bg-success bg-opacity-10 text-success']
            : ['status' => 'Sedang Berlangsung', 'css_class' => 'bg-warning bg-opacity-10 text-warning'];

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

        // 3. Tahap Wawancara (Depends on Tes Tertulis)
        $wawancaraDeadline = $deadlines['tahap_wawancara'];
        $wawancaraState = $determineStatus($wawancaraDeadline, $tesIsDone);
        $wawancaraIsDone = ($wawancaraState['status'] === 'Selesai');

        $status['tahap_wawancara'] = [
            'label' => 'Tahap Wawancara',
            'jumlah' => 0,
            'deadline' => $wawancaraDeadline,
            'status' => $wawancaraState['status'],
            'css_class' => $wawancaraState['css_class']
        ];

        // 4. Pengumuman (Depends on Tahap Wawancara)
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

    private static function countByNilaiThreshold(string $operator, int $threshold): int
    {
        try {
            // Join with absensi to ensure we only count students in the monitoring list
            $sql = "SELECT COUNT(n.id_mahasiswa) 
                    FROM nilai_akhir n
                    JOIN absensi a ON n.id_mahasiswa = a.id_mahasiswa
                    WHERE COALESCE(n.total_nilai, n.nilai) {$operator} :threshold";
            
            $stmt = self::getDB()->prepare($sql);
            $stmt->bindValue(':threshold', $threshold, PDO::PARAM_INT);
            $stmt->execute();
            return (int) $stmt->fetchColumn();
        } catch (\Throwable $e) {
            return 0;
        }
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
