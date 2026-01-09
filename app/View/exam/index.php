<?php
/**
 * Exam View
 * 
 * Data yang diterima dari controller:
 * @var string $stambuk - Stambuk mahasiswa
 * @var array $profile - Data profile
 * @var string $nama - Nama mahasiswa
 * @var string $photo - Path foto
 * @var array $results - Soal-soal ujian
 */
$stambuk = $stambuk ?? '';
$profile = $profile ?? [];
$nama = $nama ?? 'Nama Lengkap';
$photo = $photo ?? '/Sistem-Pendaftaran-Calon-Asisten/res/imageUser/default.png';
$results = $results ?? [];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?=APP_URL?>/Assets/Style/exam.css" />
    <link rel="stylesheet" href="<?=APP_URL?>/Assets/Style/alertstyle.css">
    <link rel="icon" href="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Img/iclabs.png">
</head>

<body>
    <header>
        <div class="header-container">
            <img src="<?=APP_URL?>/Assets/Img/iclabs.png" alt="Logo" class="logo">
            <h1>ICLabs - Tes Tertulis</h1>
        </div>
    </header>

    <div class="container">
        <div class="sidebar">
            <div class="profile-section">
                <img src="<?= htmlspecialchars($photo) ?>" alt="User Photo" name="userphoto" id="userphoto"
                    class="profile-picture">
                <h3><?= htmlspecialchars($nama) ?></h3>
                <p><?= htmlspecialchars($stambuk) ?></p>
            </div>
            <div class="nav">
                <?php for ($i = 1; $i <= count($results); $i++): ?>
                    <button><?= $i ?></button>
                <?php endfor; ?>
            </div>
        </div>
        <div class="main-content">
            <div class="timer">Time Remaining: <span id="timer">30:00</span></div>
            <div class="questions-container">
            <?php
foreach ($results as $index => $result): ?>
    <div class="question" data-id-soal="<?= htmlspecialchars($result['id']) ?>" style="display: none;">
        <h3>Soal <?= $index + 1 ?></h3>
        <p><?= htmlspecialchars($result['deskripsi']) ?></p>
        <?php if ($result['status_soal'] === 'pilihan_ganda'): ?>
            <ul class="options">
                <?php
                $options = json_decode($result['pilihan']);
                foreach ($options as $optionIndex => $option): ?>
                    <li>
                        <label>
                            <input type="radio" name="answer[<?= htmlspecialchars($result['id']) ?>]"
                                value="<?= htmlspecialchars($optionIndex) ?>">
                            <?= htmlspecialchars($option) ?>
                        </label>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <textarea name="answer[<?= htmlspecialchars($result['id']) ?>]" 
          class="text-answer" 
          placeholder="Write your answer here..." 
          style="width: 60%; margin: 0 auto; margin-top: 1.5rem; display: block; padding: 15px; border-radius: 10px; border: 1px solid #ddd; font-family: 'Poppins', sans-serif; font-size: 16px; line-height: 1.5; resize: none; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); background-color: #f9f9f9; color: #333;">
</textarea>

        <?php endif; ?>

        <div class="navigation-buttons">
            <button class="nav-button back">Back</button>
            <button class="nav-button next">Next</button>
        </div>
    </div>
<?php endforeach; ?>

            </div>
        </div>
    </div>
    <div id="customModal" class="alertmodal">
    <div class="modal-content">
        <img id="modalGif" src="" alt="Animation" style="width: 100px; margin-bottom: 15px; display: none;">
        
        <p id="modalMessage" style="margin: 10px 0; font-size: 18px;">Pesan akan ditampilkan di sini.</p>
        
        <button id="closeModal" class="btn btn-primary" style="margin-top: 10px;">Tutup</button>
    </div>
</div>

<div id="confirmModal" class="modal">
  <div class="modal-content">
    <p id="confirmModalMessage"></p>
    <div class="modal-buttons">
      <button id="confirmModalConfirm" class="btn-confirm">Confirm</button>
      <button id="confirmModalCancel" class="btn-cancel">Tidak</button>
    </div>
  </div>
</div>
    <footer>
        <p>&copy; 2024 by ICLabs</p>
    </footer>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Script/exam/examScript.js"></script>
</body>

</html>