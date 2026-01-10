<?php

namespace App\Controllers\user;

use App\Core\Controller;
use App\Model\User\DashboardUser;
class DashboardUserController extends Controller {
    public static function getBiodataStatus() {
        $dashboardUser = new DashboardUser();
        return $dashboardUser->getBiodataStatus();
    }
    public static function getBerkasStatus() {
        $dashboardUser = new DashboardUser();
        return $dashboardUser->getBerkasStatus();
    }
    public static function getAbsensiTesTertulis() {
        $dashboardUser = new DashboardUser();
        return $dashboardUser->getAbsensiTesTertulis();
    }
    public static function getAbsensiWawancaraI() {
        $dashboardUser = new DashboardUser();
        return $dashboardUser->getAbsensiWawancaraI();
    }
    public static function getAbsensiWawancaraII() {
        $dashboardUser = new DashboardUser();
        return $dashboardUser->getAbsensiWawancaraII();
    }
    public static function getAbsensiWawancaraIII() {
        $dashboardUser = new DashboardUser();
        return $dashboardUser->getAbsensiWawancaraIII();
    }
    public static function getAbsensiPresentasi() {
        $dashboardUser = new DashboardUser();
        return $dashboardUser->getAbsensiPresentasi();
    }
    public static function getPptStatus() {
        $dashboardUser = new DashboardUser();
        return $dashboardUser->getStatusPpt();
    }
    public static function getPptJudulAccStatus() {
        $dashboardUser = new DashboardUser();
        return $dashboardUser->getPptAccStatus();
    }
    public static function getNumberTahapanSelesai() {
        $i = 0;
        if(self::getBiodataStatus()) {
            $i++;
        }
        if(self::getBerkasStatus()) {
            $i++;
        }
        if(self::getAbsensiTesTertulis()) {
            $i++;
        }
        if(self::getAbsensiWawancaraI()) {
            $i++;
        }
        if(self::getAbsensiWawancaraII()) {
            $i++;
        }
        if(self::getAbsensiWawancaraIII()) {
            $i++;
        }
        if(self::getAbsensiPresentasi()) {
            $i++;
        }
        if(self::getPptStatus()) {
            $i++;
        }
        if(self::getPptJudulAccStatus()) {
            $i++;
        }
        return $i;
    } 
    public static function getPercentage() {
        $completed = self::getNumberTahapanSelesai(); 
        $total = 9; 
        if ($completed == 0) {
            return 0;
        }
        return floor(($completed / $total) * 100); 
    }



    // --- TAMBAHAN HELPER WAKTU ---

    public static function getLastActivityString() {
        $model = new DashboardUser();
        $time = $model->getLastActivityTime();

        if (!$time) {
            return "Belum ada aktivitas";
        }

        return self::timeAgo($time);
    }

    private static function timeAgo($datetime) {
        // Set zona waktu agar akurat (WITA/WIB sesuaikan)
        date_default_timezone_set('Asia/Makassar'); 
        
        $time_ago = strtotime($datetime);
        $current_time = time();
        $time_difference = $current_time - $time_ago;
        $seconds = $time_difference;
        
        $minutes      = round($seconds / 60);           // value 60 is seconds
        $hours        = round($seconds / 3600);         // value 3600 is 60 minutes * 60 sec
        $days         = round($seconds / 86400);        // value 86400 is 24 hours * 60 min * 60 sec
        $weeks        = round($seconds / 604800);       // value 604800 is 7 days * 24 hours * 60 min * 60 sec
        $months       = round($seconds / 2629440);      // value 2629440 is ((365+365+365+365+366)/5/12) days * 24 hours * 60 min * 60 sec
        $years        = round($seconds / 31553280);     // value 31553280 is ((365+365+365+365+366)/5) days * 24 hours * 60 min * 60 sec

        if ($seconds <= 60) {
            return "Baru saja";
        } else if ($minutes <= 60) {
            return "$minutes menit yang lalu";
        } else if ($hours <= 24) {
            return "$hours jam yang lalu";
        } else if ($days <= 7) {
            return "$days hari yang lalu";
        } else if ($weeks <= 4.3) {
            return "$weeks minggu yang lalu";
        } else {
            return date('d M Y', $time_ago); // Tampilkan tanggal biasa jika sudah lama
        }
    }
    
    public static function generateCircle($percentage) {
        $radius = 38; // Radius lingkaran
        $circumference = 2 * pi() * $radius; // Keliling lingkaran
        $offset = $circumference * (1 - $percentage / 100); // Hitung offset
    
        return "
        <div class=\"progress\">
            <svg width=\"100\" height=\"100\">
                <circle cx=\"50\" cy=\"50\" r=\"$radius\" stroke=\"#e6e6e6\" stroke-width=\"14\" fill=\"none\"></circle>
                <circle 
                    cx=\"50\" 
                    cy=\"50\" 
                    r=\"$radius\" 
                    stroke=\"#00aaff\" 
                    stroke-width=\"14\" 
                    fill=\"none\" 
                    stroke-dasharray=\"$circumference\" 
                    stroke-dashoffset=\"$offset\"
                    transform=\"rotate(-90 50 50)\"
                ></circle>
            </svg>
            <div class=\"number\">{$percentage}%</div>
        </div>";
    }
    
}