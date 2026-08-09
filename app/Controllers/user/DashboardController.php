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
        // User request: Must attend both Wawancara I and II
        if (self::getAbsensiWawancaraI() && self::getAbsensiWawancaraII()) {
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

    
    
    public static function getActivities() {
        header('Content-Type: application/json');

        // Wajib login: data kegiatan tidak boleh dibaca anonim.
        self::requireAuth();
        
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        
        // Default to current date if no data passed
        $year = isset($data['year']) ? (int)$data['year'] : (int)date('Y');
        $month = isset($data['month']) ? (int)$data['month'] : (int)date('m');
        
        try {
            // Re-use logic from DashboardAdmin to get activities
            $activities = \App\Model\DashboardAdmin::getKegiatanByMonth($year, $month);
            
            ob_start();
            include __DIR__ . '/../../View/user/dashboard/partials/calendar_grid.php';
            $calendarHtml = ob_get_clean();
            
            ob_start();
            include __DIR__ . '/../../View/user/dashboard/partials/upcoming_activities.php';
            $upcomingHtml = ob_get_clean();
            
            self::jsonSuccess(['data' => $activities, 'calendarHtml' => $calendarHtml, 'upcomingHtml' => $upcomingHtml]);
        } catch (\Exception $e) {
            http_response_code(500);
            self::jsonError($e->getMessage());
        }
    }
}