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
     * @return array<int, array{tanggal: string}>
     */
    public static function getKegiatanByMonth(int $year, int $month): array
    {
        $results = [];

        // Try pull from jadwal_wawancara
        $results = array_merge($results, self::selectTanggalByMonth('jadwal_wawancara', $year, $month));
        // Try pull from jadwal_presentasi
        $results = array_merge($results, self::selectTanggalByMonth('jadwal_presentasi', $year, $month));

        return $results;
    }

    /**
     * @return array<string, array{jumlah: int}>
     */
    public static function getStatusKegiatan(): array
    {
        $status = [
            'kelengkapan_berkas' => ['jumlah' => 0],
            'tes_tertulis' => ['jumlah' => 0],
            'tahap_wawancara' => ['jumlah' => 0],
            'pengumuman' => ['jumlah' => 0],
        ];

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
            // Prefer total_nilai if exists, else fall back to nilai.
            $sql = "SELECT COUNT(*) FROM nilai_akhir WHERE COALESCE(total_nilai, nilai) {$operator} :threshold";
            $stmt = self::getDB()->prepare($sql);
            $stmt->bindValue(':threshold', $threshold, PDO::PARAM_INT);
            $stmt->execute();
            return (int) $stmt->fetchColumn();
        } catch (\Throwable $e) {
            try {
                $sql = "SELECT COUNT(*) FROM nilai_akhir WHERE nilai {$operator} :threshold";
                $stmt = self::getDB()->prepare($sql);
                $stmt->bindValue(':threshold', $threshold, PDO::PARAM_INT);
                $stmt->execute();
                return (int) $stmt->fetchColumn();
            } catch (\Throwable $e2) {
                return 0;
            }
        }
    }

    /**
     * @return array<int, array{tanggal: string}>
     */
    private static function selectTanggalByMonth(string $table, int $year, int $month): array
    {
        try {
            $sql = "SELECT tanggal FROM {$table} WHERE YEAR(tanggal) = :year AND MONTH(tanggal) = :month";
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
                $result[] = ['tanggal' => (string) $row['tanggal']];
            }
            return $result;
        } catch (\Throwable $e) {
            return [];
        }
    }
}
