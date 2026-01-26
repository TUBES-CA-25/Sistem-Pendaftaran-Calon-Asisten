<?php
namespace App\Controllers\exam;

use App\Core\Controller;
use App\Model\exam\BankSoal;

class JadwalTesController extends Controller
{
    public function index()
    {
        // Check if user is logged in
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Admin') {
            // Fix redirect to respect subdirectory
            $baseUrl = dirname($_SERVER['SCRIPT_NAME']);
            // Remove /public if it exists in the path to get root
            $baseUrl = str_replace('/public', '', $baseUrl);
            
            header('Location: ' . $baseUrl . '/login');
            exit;
        }

        // Load Bank Soal data for the schedule view
        // Ideally we would have a separate 'Schedule' mode, but for now we'll allow admins to 
        // essentially see which banks are active or set up a 'session'.
        // Since we don't have a 'written_test_schedule' table yet, we will focus on managing 
        // the 'is_active' state of Bank Soal as the 'Schedule' (Open/Close).
        
        $bankSoalModel = new BankSoal();
        $bankSoalList = $bankSoalModel->getAllBanks();

        $data = [
            'bankSoalList' => $bankSoalList
        ];

        // Detect AJAX
        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

        if ($isAjax) {
             $this->view('admin/exam/schedule', $data);
        } else {
             // Full Page - Need Sidebar Data
             // We can fetch basic info from Session for now
             $sidebarData = [
                 'role' => 'Admin',
                 'userName' => $_SESSION['user']['username'] ?? 'Admin',
                 'photo' => '/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Img/default-avatar.png', // Fallback or fetch real photo
                 'initialPage' => 'jadwaltes'
             ];
             
             // Try to get real photo if helper exists
             if (class_exists('App\Controllers\Admin\AdminProfileController')) {
                 $photoPath = \App\Controllers\Admin\AdminProfileController::getAdminPhoto($_SESSION['user']['id']);
                 $sidebarData['photo'] = $photoPath;
             }
             
             // Merge data
             $fullData = array_merge($data, $sidebarData);
             $this->view('layouts/mainAdmin', $fullData);
        }
    }
}
