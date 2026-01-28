<?php

use App\Controllers\AnswersController;
use App\Controllers\ExamController;
use App\Controllers\NilaiAkhirController;
use App\Controllers\HomeController;
use App\Controllers\LoginController;
use App\Controllers\RegisterController;
use App\Controllers\LogoutController;
use App\Controllers\JadwalPresentasiController;
use App\Controllers\ProfileController;
use App\Controllers\AbsensiUserController;
use App\Controllers\BerkasUserController;
use App\Controllers\BiodataUserController;
use App\Controllers\DashboardUserController;
use App\Controllers\MahasiswaController;
use App\Controllers\PresentasiUserController;
use App\Controllers\NotificationControllers;
use App\Controllers\SoalController;
use App\Controllers\WawancaraController;
use App\Controllers\RuanganController;
use App\Controllers\JadwalTesController;
use App\Core\Router;

Router::get('/jadwaltes', [new JadwalTesController, 'index']); // Lowercase route
Router::get('/soal', [new ExamController, 'index']);
Router::get('/login', [new LoginController, 'index']);

// IMPORTANT: Specific routes MUST come before catch-all route /{page}
// Export and download routes
Router::get("/exportSoal", [new SoalController, 'exportSoal']);
Router::get("/soal/export",[new SoalController,'exportSoal']);
Router::get("/getBankDetails", [new SoalController, 'getBankDetails']);
Router::get("/downloadTemplatesoal", [new SoalController, 'downloadTemplate']);
Router::get("/soal/download-template",[new SoalController, 'downloadTemplate']);
Router::get('/download',[new BerkasUserController, 'downloadBerkas']);

// Home routes - catch-all MUST be last
Router::get('/',[new HomeController, 'index']);
Router::get('/{page}', [new HomeController, 'loadContent']);


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

Router::post("/importSoal", [new SoalController, 'importSoal']);

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
Router::post("/updateprofile",[new ProfileController, 'updateProfile']);
Router::post("/acceptberkas",[new BerkasUserController, 'updateAcceptedStatus']);
Router::post("/ruangan/getfilter",[new WawancaraController,'getAllFilterByIdRuangan']);
Router::post("/updatenilaiakhir",[new NilaiAkhirController, 'updateTotalNilai']);
Router::post("/updateabsensi",[new AbsensiUserController, 'updateData']);
Router::post("/deleteabsensi",[new AbsensiUserController, 'deleteData']);
Router::post("/addallnotif",[new NotificationControllers, 'sendAllMessage']);
Router::get("/getnotifications",[new NotificationControllers, 'fetchNotifications']);
Router::post("/marknotificationsread",[new NotificationControllers, 'markRead']);

// Bank Soal Routes
Router::post("/createBank",[new SoalController, 'createBank']);
Router::post("/updateBank",[new SoalController, 'updateBank']);
Router::post("/deleteBank",[new SoalController, 'deleteBank']);
Router::post("/getBankQuestions",[new SoalController, 'getBankQuestions']);
Router::post("/exam/verifyToken",[new ExamController, 'verifyToken']);
Router::post("/activateBank",[new SoalController, 'activateBank']);
Router::post("/deactivateBank",[new SoalController, 'deactivateBank']);
Router::get("/soal/download-template",[new SoalController, 'downloadTemplate']);
Router::post("/soal/import",[new SoalController, 'importSoal']);
Router::post("/uploadImage", [new SoalController, 'uploadImage']);

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

// Admin Dashboard Activities
use App\Controllers\DashboardAdminController;
use App\Controllers\AdminProfileController;

Router::post("/addkegiatan", [new DashboardAdminController, 'storeKegiatan']);
Router::post("/updatekegiatan", [new DashboardAdminController, 'updateKegiatan']);
Router::post("/deletekegiatan", [new DashboardAdminController, 'destroyKegiatan']);
Router::post("/updatedeadline", [new DashboardAdminController, 'saveDeadline']);
Router::post("/dashboard/stats", [new DashboardAdminController, 'getStats']);
Router::post("/getactivities", [new DashboardUserController, 'getActivities']);

Router::post("/updateadminprofile", [new AdminProfileController, 'updateProfile']);

// Jadwal Tes Tertulis Individual
Router::post("/saveJadwalTes", [new JadwalTesController, 'save']);
Router::post("/deleteJadwalTes", [new JadwalTesController, 'delete']);
Router::post("/updateJadwalTes", [new JadwalTesController, 'update']);
