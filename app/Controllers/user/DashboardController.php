<?php

namespace App\Controllers\User;

use App\Core\Controller;
use App\Model\DashboardUser;
class DashboardController extends Controller {
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
    public static function getGraduationStatus() {
        $dashboardUser = new DashboardUser();
        return $dashboardUser->getGraduationStatus();
    }
    public static function isPengumumanOpen() {
        $dashboardUser = new DashboardUser();
        return $dashboardUser->isPengumumanOpen();
    }
    public static function getKegiatanByMonth() {
        return \App\Model\DashboardAdmin::getKegiatanByMonth((int)date('Y'), (int)date('m'));
    }
    public static function getMajorStagesSelesai() {
        $i = 0;
        
        // Stage 1: Berkas
        if (self::getBiodataStatus() && self::getBerkasStatus()) {
            $i++;
        } else {
            return $i; // Sequential: stop if previous not complete
        }

        // Stage 2: Tes Tertulis (Exam Submitted)
        if (self::getAbsensiTesTertulis()) {
            $i++;
        } else {
            return $i;
        }

        // Stage 3: Presentasi (Absensi Hadir)
        if (self::getAbsensiPresentasi()) {
            $i++;
        } else {
            return $i;
        }

        // Stage 4: Wawancara (Absensi Hadir)
        if (self::getAbsensiWawancaraI()) {
            $i++;
        } else {
            return $i;
        }

        // Stage 5: Pengumuman (Status Akhir is Lulus/Tidak Lulus)
        // If status is finalized (Lulus/Tidak Lulus), user has reached the final stage
        if (self::getGraduationStatus() === 'Lulus' || self::getGraduationStatus() === 'Tidak Lulus') {
             $i++;
        }

        return $i;
    }

    public static function getPercentage() {
        $completed = self::getMajorStagesSelesai(); 
        $total = 5; 
        return ($completed / $total) * 100; 
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
    
    public static function getActivities() {
        header('Content-Type: application/json');
        
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        
        // Default to current date if no data passed
        $year = isset($data['year']) ? (int)$data['year'] : (int)date('Y');
        $month = isset($data['month']) ? (int)$data['month'] : (int)date('m');
        
        try {
            // Re-use logic from DashboardAdmin to get activities
            $activities = \App\Model\DashboardAdmin::getKegiatanByMonth($year, $month);
            
            echo json_encode([
                'status' => 'success',
                'data' => $activities
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
}