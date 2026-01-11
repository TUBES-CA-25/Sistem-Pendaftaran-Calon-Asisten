<?php

use App\Controllers\exam\AnswersController;
use App\Controllers\exam\ExamController;
use App\Controllers\Exam\NilaiAkhirController;
use App\Controllers\Home\HomeController;
use App\Controllers\Login\LoginController;
use App\Controllers\Login\RegisterController;
use App\Controllers\Login\LogoutController;
use App\Controllers\presentasi\JadwalPresentasiController;
use App\Controllers\Profile\ProfileController;
use App\Controllers\user\AbsensiUserController;
use App\Controllers\user\BerkasUserController;
use App\Controllers\user\BiodataUserController;
use App\Controllers\user\MahasiswaController;
use App\Controllers\user\PresentasiUserController;
use App\Controllers\notifications\NotificationControllers;
use App\Controllers\exam\SoalController;
use App\Controllers\user\WawancaraController;
use App\Controllers\presentasi\RuanganController;
use App\Core\Router;



Router::get('/soal', [new ExamController, 'index']);
Router::get('/login', [new LoginController, 'index']);
Router::get('/',[new HomeController, 'index']);
Router::get('/{page}', [new HomeController, 'loadContent']);
Router::get('/download',[new BerkasUserController, 'downloadBerkas']);


Router::post('/login/authenticate', [new LoginController, 'authenticate']);
Router::post('/register/authenticate', [new RegisterController, 'register']);
Router::post('/logout', [new LogoutController, 'logout']);
Router::post("/store", [new BiodataUserController, 'saveBiodata']);
Router::post("/berkas", [new BerkasUserController, 'saveBerkas']);
Router::post("/judul", [new PresentasiUserController, 'saveJudul']);
Router::post("/presentasi", [new PresentasiUserController, 'saveMakalahAndPpt']);
Router::post("/hasil",[new AnswersController, 'saveAnswer']);
Router::post("/notification",[new NotificationControllers, 'sendMessage']);
Router::post("/deletemahasiswa",[new MahasiswaController,'deleteMahasiswa']);
Router::post("/getdetailpeserta",[new MahasiswaController,'getDetailPeserta']);

Router::post("/updatestatus",[new PresentasiUserController, 'updateStatusJudul']);
Router::post("/tambahjadwal",[new JadwalPresentasiController,'saveJadwal']);

Router::post("/addingsoal",[new SoalController,'saveSoal']);
Router::post("/deletesoal",[new SoalController,'deleteSoal']);
Router::post("/updatesoal",[new SoalController,'updateSoal']);
Router::post("/absensi",[new AbsensiUserController, 'saveData']);
Router::post("/wawancara",[new WawancaraController, 'save']);
Router::post("/updatewawancara",[new WawancaraController, 'update']);
Router::post("/deletewawancara",[new WawancaraController, 'delete']);
Router::post("/updatepresentasi",[new PresentasiUserController, 'sendKeteranganAndRevisi']);
Router::post("/tambahruangan",[new RuanganController, 'addRuangan']);
Router::post("/deleteruangan",[new RuanganController, 'deleteRuangan']);
Router::post("/updateruangan",[new RuanganController, 'updateRuangan']);
Router::post("/getsoaljawaban",[new NilaiAkhirController, 'getSoalAndJawabanMahasiswa']);
Router::post("/updatebiodata",[new ProfileController, 'updateBiodata']);
Router::post("/acceptberkas",[new BerkasUserController, 'updateAcceptedStatus']);
Router::post("/ruangan/getfilter",[new WawancaraController,'getAllFilterByIdRuangan']);
Router::post("/updatenilaiakhir",[new NilaiAkhirController, 'updateTotalNilai']);
Router::post("/updateabsensi",[new AbsensiUserController, 'updateData']);
Router::post("/addallnotif",[new NotificationControllers, 'sendAllMessage']);

// Bank Soal Routes
Router::post("/createBank",[new SoalController, 'createBank']);
Router::post("/updateBank",[new SoalController, 'updateBank']);
Router::post("/deleteBank",[new SoalController, 'deleteBank']);
Router::post("/getBankQuestions",[new SoalController, 'getBankQuestions']);
Router::post("/exam/verifyToken",[new ExamController, 'verifyToken']);
Router::post("/exam/activateBank",[new SoalController, 'activateBank']);

// Room Participant Management Routes
Router::post("/getroomparticipants",[new RuanganController, 'getRoomParticipants']);
Router::post("/assignparticipant",[new RuanganController, 'assignParticipant']);
Router::post("/removeparticipant",[new RuanganController, 'removeParticipant']);
Router::post("/getroomoccupants",[new RuanganController, 'getRoomOccupants']);

// Jadwal Presentasi Routes
Router::post("/getjadwalpresentasi",[new JadwalPresentasiController, 'getAllJadwal']);
Router::post("/getjadwalpresentasiuser",[new JadwalPresentasiController, 'getJadwalUser']);
Router::post("/updatejadwalpresentasi",[new JadwalPresentasiController, 'updateJadwal']);
Router::post("/deletejadwalpresentasi",[new JadwalPresentasiController, 'deleteJadwal']);
Router::post("/savejadwalpresentasi",[new JadwalPresentasiController, 'saveSingleJadwal']);
Router::post("/getavailablemahasiswa",[new JadwalPresentasiController, 'getAvailableMahasiswa']);
Router::post("/getallruangan",[new JadwalPresentasiController, 'getAllRuangan']);