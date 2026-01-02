<?php

namespace App\Controllers\admin;

use App\Core\Controller;
use App\Model\admin\DashboardAdmin;

class DashboardAdminController extends Controller
{
    public static function getTotalPendaftar(): int
    {
        return DashboardAdmin::getTotalPendaftar();
    }

    public static function getPendaftarLulus(): int
    {
        return DashboardAdmin::getPendaftarLulus();
    }

    public static function getPendaftarPending(): int
    {
        return DashboardAdmin::getPendaftarPending();
    }

    public static function getPendaftarGagal(): int
    {
        return DashboardAdmin::getPendaftarGagal();
    }

    /**
     * @return array<int, array{tanggal: string}>
     */
    public static function getKegiatanByMonth(?int $year = null, ?int $month = null): array
    {
        $year ??= (int) date('Y');
        $month ??= (int) date('m');

        return DashboardAdmin::getKegiatanByMonth($year, $month);
    }

    /**
     * @return array<string, array{jumlah: int}>
     */
    public static function getStatusKegiatan(): array
    {
        return DashboardAdmin::getStatusKegiatan();
    }
}
