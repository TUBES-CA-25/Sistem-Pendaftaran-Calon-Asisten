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
        if (self::getGraduationStatus() === 'Lulus' && self::isPengumumanOpen()) {
            return 4;
        }
        $i = 0;
        // Stage 1: Berkas (Biodata + Documents)
        if (self::getBiodataStatus() && self::getBerkasStatus()) {
            $i++;
        }
        // Stage 2: Tes (Written Test + PPT Judul)
        if (self::getAbsensiTesTertulis() && self::getPptJudulAccStatus()) {
            $i++;
        }
        // Stage 3: Wawancara (PPT Submission + Presentation + Interview I)
        if (self::getPptStatus() && self::getAbsensiPresentasi() && self::getAbsensiWawancaraI()) {
            $i++;
        }
        // Stage 4: Final (Interview II + Interview III + Selection Result)
        if (self::getAbsensiWawancaraII() && self::getAbsensiWawancaraIII() && self::getGraduationStatus() !== 'Pending') {
            $i++;
        }
        return $i;
    }
    public static function getNumberTahapanSelesai() {
        // Keep raw count for internal use if needed, but major stages is what's displayed
        $i = 0;
        if(self::getBiodataStatus()) { $i++; }
        if(self::getBerkasStatus()) { $i++; }
        if(self::getAbsensiTesTertulis()) { $i++; }
        if(self::getAbsensiWawancaraI()) { $i++; }
        if(self::getAbsensiWawancaraII()) { $i++; }
        if(self::getAbsensiWawancaraIII()) { $i++; }
        if(self::getAbsensiPresentasi()) { $i++; }
        if(self::getPptStatus()) { $i++; }
        if(self::getPptJudulAccStatus()) { $i++; }
        if(self::getGraduationStatus() !== 'Pending') { $i++; }
        return $i;
    }
    public static function getPercentage() {
        if (self::getGraduationStatus() === 'Lulus' && self::isPengumumanOpen()) {
            return 100;
        }

        $completed = self::getMajorStagesSelesai(); 
        $total = 4; 
        if ($completed == 0) {
            return 0;
        }
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