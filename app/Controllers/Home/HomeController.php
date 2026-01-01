<?php

namespace App\Controllers\Home;

use App\Core\Controller;
use App\Core\View;


class HomeController extends Controller
{
    public function index()
    {
        if ($this->isLoggedIn() && $this->getRole() == "User") {
            View::render('main', 'Templates');

        } else if ($this->isLoggedIn() && $this->getRole() == "Admin") {
            View::render('mainAdmin', 'Templates');

        } else {
            View::render('index', 'login');
            exit();
        }
    }

    public function loadContent($page)
    {
        if (is_array($page)) {
            $page = $page['page'];
        }

        if ($this->getRole() == "Admin") {
            switch ($page) {
                case 'dashboard':
                    View::render('dashboardAdmin', 'Templates');
                    break;
                case 'ruangan':
                    View::render('ruangan', 'Templates');
                    break;
                case 'lihatPeserta':
                    View::render('daftarPeserta', 'Templates');
                    break;
                case 'daftarKehadiran':
                    View::render('DaftarHadirPesertaAdmin', 'Templates');
                    break;
                case 'presentasi':
                    View::render('presentasiAdmin', 'Templates');
                    break;
                case 'tesTulis':
                    View::render('tesTulisAdmin', 'Templates');
                    break;
                case 'uploadBerkas':
                    View::render('uploadBerkasAdmin', 'Templates');
                    break;
                case 'wawancara':
                    View::render('wawancaraAdmin', 'Templates');
                    break;
                case 'profile':
                    View::render('profileAdmin', 'Templates');
                    break;
                case 'lihatnilai':
                    View::render('DaftarNilaiTesTertulisAdmin', 'Templates');
                    break;
                case 'logout':
                    session_destroy();
                    $_SESSION = [];
                    echo "<script>window.location.href = '';</script>";
                    exit;
            }

        } else {
            switch ($page) {
                case 'dashboard':
                    View::render('dashboard', 'Templates');
                    break;
                case 'biodata':
                    View::render('biodata', 'Templates');
                    break;
                case 'pengumuman':
                    View::render('pengumuman', 'Templates');
                    break;
                case 'presentasi':
                    View::render('presentasi', 'Templates');
                    break;
                case 'tesTulis':
                    View::render('tesTulis', 'Templates');
                    break;
                case 'uploadBerkas':
                    View::render('uploadBerkas', 'Templates');
                    break;
                case 'wawancara':
                    View::render('wawancara', 'Templates');
                    break;
                case 'profile':
                    View::render('profile', 'Templates');
                    break;
                case 'editprofile':
                    View::render('editprofile', 'Templates');
                    break;
                case 'notifcation':
                    View::render('notification', 'Templates');
                    break;
            }
        }
    }

    private function isLoggedIn()
    {
        return isset($_SESSION['user']);
    }
    private function getRole()
    {
        return $_SESSION['user']['role'];
    }
}
