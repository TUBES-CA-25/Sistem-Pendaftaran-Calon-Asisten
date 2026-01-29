<?php

// Controllers Shared/Umum
use App\Controllers\HomeController;
use App\Controllers\AuthController;
use App\Controllers\NotifikasiController;

// Controllers User
use App\Controllers\User\DashboardController;
use App\Controllers\User\BiodataController;
use App\Controllers\User\BerkasController;
use App\Controllers\User\TesTulisController;
use App\Controllers\User\JadwalController;
use App\Controllers\User\ProfilController;
use App\Controllers\User\PresentasiUserController;

// Controllers Admin
use App\Controllers\Admin\DashboardAdminController;
use App\Controllers\Admin\PesertaController;
use App\Controllers\Admin\PengajuanJudulController;
use App\Controllers\Admin\BankSoalController;
use App\Controllers\Admin\ImporSoalController;
use App\Controllers\Admin\JadwalTesController;
use App\Controllers\Admin\JadwalPresentasiController;
use App\Controllers\Admin\JadwalWawancaraController;
use App\Controllers\Admin\RuanganController;
use App\Controllers\Admin\NilaiController;
use App\Controllers\Admin\RekapKehadiranController;
use App\Controllers\Admin\ProfilAdminController;

use App\Core\Router;

Router::get('/jadwaltes', [new JadwalTesController, 'index']); // Lowercase route
Router::get('/soal', [new TesTulisController, 'index']);
Router::get('/login', [new AuthController, 'index']);

// IMPORTANT: Specific routes MUST come before catch-all route /{page}
// Export and download routes
Router::get("/exportSoal", [new ImporSoalController, 'exportSoal']);
Router::get("/soal/export",[new ImporSoalController,'exportSoal']);
Router::get("/getBankDetails", [new BankSoalController, 'getBankDetails']);
Router::get("/downloadTemplatesoal", [new ImporSoalController, 'downloadTemplate']);
Router::get("/soal/download-template",[new ImporSoalController, 'downloadTemplate']);
Router::get('/download',[new BerkasController, 'downloadBerkas']);

// Home routes - catch-all MUST be last
Router::get('/',[new HomeController, 'index']);
Router::get('/{page}', [new HomeController, 'loadContent']);


Router::post('/login/authenticate', [new AuthController, 'authenticate']);
Router::post('/register/authenticate', [new AuthController, 'register']);
Router::post('/logout', [new AuthController, 'logout']);
Router::post("/store", [new BiodataController, 'saveBiodata']);
Router::post("/berkas", [new BerkasController, 'saveBerkas']);
Router::post("/judul", [new PresentasiUserController, 'saveJudul']);
Router::post("/presentasi", [new PresentasiUserController, 'saveMakalahAndPpt']);
Router::post("/hasil",[new TesTulisController, 'saveAnswer']);
Router::post("/notification",[new NotifikasiController, 'sendMessage']);
Router::post("/deletemahasiswa",[new PesertaController,'deleteMahasiswa']);
Router::post("/getdetailpeserta",[new PesertaController,'getDetailPeserta']);

Router::post("/updatestatus",[new PresentasiUserController, 'updateStatusJudul']);
Router::post("/tambahjadwal",[new JadwalPresentasiController,'saveJadwal']);

Router::post("/addingsoal",[new BankSoalController,'saveSoal']);
Router::post("/deletesoal",[new BankSoalController,'deleteSoal']);
Router::post("/updatesoal",[new BankSoalController,'updateSoal']);

Router::post("/importSoal", [new ImporSoalController, 'importSoal']);

Router::post("/absensi",[new RekapKehadiranController, 'saveData']);
Router::post("/wawancara",[new JadwalWawancaraController, 'save']);
Router::post("/updatewawancara",[new JadwalWawancaraController, 'update']);
Router::post("/deletewawancara",[new JadwalWawancaraController, 'delete']);
Router::post("/updatepresentasi",[new PresentasiUserController, 'sendKeteranganAndRevisi']);
Router::post("/tambahruangan",[new RuanganController, 'addRuangan']);
Router::post("/deleteruangan",[new RuanganController, 'deleteRuangan']);
Router::post("/updateruangan",[new RuanganController, 'updateRuangan']);
Router::post("/getsoaljawaban",[new NilaiController, 'getSoalAndJawabanMahasiswa']);
Router::post("/updatebiodata",[new ProfilController, 'updateBiodata']);
Router::post("/updateprofile",[new ProfilController, 'updateProfile']);
Router::post("/acceptberkas",[new BerkasController, 'updateAcceptedStatus']);
Router::post("/ruangan/getfilter",[new JadwalWawancaraController,'getAllFilterByIdRuangan']);
Router::post("/updatenilaiakhir",[new NilaiController, 'updateTotalNilai']);
Router::post("/updateabsensi",[new RekapKehadiranController, 'updateData']);
Router::post("/deleteabsensi",[new RekapKehadiranController, 'deleteData']);
Router::post("/addallnotif",[new NotifikasiController, 'sendAllMessage']);
Router::get("/getnotifications",[new NotifikasiController, 'fetchNotifications']);
Router::post("/marknotificationsread",[new NotifikasiController, 'markRead']);

// Bank Soal Routes
Router::post("/createBank",[new BankSoalController, 'createBank']);
Router::post("/updateBank",[new BankSoalController, 'updateBank']);
Router::post("/deleteBank",[new BankSoalController, 'deleteBank']);
Router::post("/getBankQuestions",[new BankSoalController, 'getBankQuestions']);
Router::post("/exam/verifyToken",[new TesTulisController, 'verifyToken']);
Router::post("/activateBank",[new BankSoalController, 'activateBank']);
Router::post("/deactivateBank",[new BankSoalController, 'deactivateBank']);
Router::post("/soal/import",[new ImporSoalController, 'importSoal']);
Router::post("/uploadImage", [new BankSoalController, 'uploadImage']);

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
Router::post("/addkegiatan", [new DashboardAdminController, 'storeKegiatan']);
Router::post("/updatekegiatan", [new DashboardAdminController, 'updateKegiatan']);
Router::post("/deletekegiatan", [new DashboardAdminController, 'destroyKegiatan']);
Router::post("/updatedeadline", [new DashboardAdminController, 'saveDeadline']);
Router::post("/dashboard/stats", [new DashboardAdminController, 'getStats']);
Router::post("/getactivities", [new DashboardController, 'getActivities']);

Router::post("/updateadminprofile", [new ProfilAdminController, 'updateProfile']);

// Jadwal Tes Tertulis Individual
Router::post("/saveJadwalTes", [new JadwalTesController, 'save']);
Router::post("/deleteJadwalTes", [new JadwalTesController, 'delete']);
Router::post("/updateJadwalTes", [new JadwalTesController, 'update']);
