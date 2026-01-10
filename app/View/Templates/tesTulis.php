<?php
/**
 * Tes Tulis View
 * 
 * Data yang diterima dari controller:
 * @var bool $absensiTesTertulis - Status sudah absen tes tertulis
 * @var bool $berkasStatus - Status berkas sudah lengkap
 * @var bool $biodataStatus - Status biodata sudah lengkap
 */
$absensiTesTertulis = $absensiTesTertulis ?? false;
$berkasStatus = $berkasStatus ?? false;
$biodataStatus = $biodataStatus ?? false;
$isDisabled = !$berkasStatus || !$biodataStatus || $absensiTesTertulis;
?>
<style>
    /* Import Font */
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');

    body {
        font-family: 'Poppins', sans-serif;
        background-color: #f4f7fa;
        margin: 0;
        padding: 0;
    }

    .exam-container {
        max-width: 600px;
        margin: 50px auto;
        padding: 20px 30px;
        background: linear-gradient(135deg, #ffffff, #f9f9f9);
        border: 1px solid rgba(61, 194, 236, 0.2);
        border-radius: 16px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    }

    .exam-container h2 {
        text-align: center;
        color: #3DC2EC;
        margin-bottom: 20px;
        font-weight: 600;
    }

    .exam-container p {
        color: #555;
        line-height: 1.8;
        margin-bottom: 15px;
        text-align: justify;
        font-size: 16px;
    }

    .exam-container ul {
        list-style: none;
        padding: 0;
        margin: 15px 0;
    }

    .exam-container li {
        position: relative;
        padding-left: 30px;
        margin-bottom: 10px;
        color: #444;
        font-size: 15px;
    }

    .exam-container li::before {
        content: "";
        position: absolute;
        left: 0;
        top: 8px;
        width: 12px;
        height: 12px;
        background-color: #3DC2EC;
        border-radius: 50%;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .exam-container strong {
        color: #333;
    }

    .exam-container input[type="text"] {
        display: block;
        width: calc(100% - 20px);
        padding: 12px 15px;
        margin-top: 10px;
        border: 1px solid #ccc;
        border-radius: 8px;
        font-size: 16px;
        box-sizing: border-box;
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }

    .exam-container input[type="text"]:focus {
        border-color: #3DC2EC;
        outline: none;
        box-shadow: 0 0 5px rgba(61, 194, 236, 0.5);
    }

    .exam-container .error {
        color: red;
        font-size: 14px;
        margin-top: 5px;
        display: none;
    }

    .exam-container button {
        display: block;
        width: 100%;
        padding: 12px;
        background: linear-gradient(135deg, #3DC2EC, #3392cc);
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.3s, transform 0.2s;
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        margin-top: 15px;
    }

    .exam-container button:hover {
        background: linear-gradient(135deg, #3392cc, #3DC2EC);
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
        transform: translateY(-2px);
    }

    .exam-container button:active {
        transform: translateY(0);
    }
</style>

<main>
    <h1 class="dashboard">Tes Tertulis</h1>
    <div class="exam-container">
        <?php 
        if($absensiTesTertulis){
            echo '<h2>Anda sudah mengikuti tes tertulis</h2>';
            echo '<p>Anda tidak bisa mengikuti tes tertulis lebih dari sekali.</p>';
            echo '<p>Terima kasih.</p>';
            return;
        }
        if(!$berkasStatus){
            echo '<div class="alert alert-warning" role="alert">
            Lengkapi berkas terlebih dahulu';
            return;
        }
        if(!$biodataStatus){
            echo '<div class="alert alert-warning" role="alert">
            Lengkapi biodata terlebih dahulu';
            return;
        }
        ?>
        <?php if (!isset($activeBank) || !$activeBank): ?>
            <div class="alert alert-info text-center">
                <h4><i class='bx bx-info-circle'></i> Belum Ada Ujian Aktif</h4>
                <p class="mb-0">Mohon tunggu informasi dari pengawas ujian.</p>
            </div>
        <?php else: ?>
            <div class="text-center mb-4">
                <h4 class="text-primary mb-2"><?= htmlspecialchars($activeBank['nama']) ?></h4>
                <p class="text-muted small"><?= htmlspecialchars($activeBank['deskripsi'] ?? '') ?></p>
            </div>

            <h2>Test Exam</h2>
            <p>Pada tahap kali ini kalian akan melaksanakan ujian pilihan ganda.</p>
            <p>Tata tertib sebelum ujian meliputi:</p>
            <ul>
                <li><strong>Dilarang menghadap kiri kanan. Silahkan fokus di komputernya saja.</strong></li>
                <li><strong>Bila membutuhkan sesuatu silahkan angkat tangan dan panggil asistennya.</strong></li>
                <li><strong>Kerjakan dengan jujur.</strong></li>
            </ul>
            <p>Ujian kali ini memiliki durasi waktu <strong>80 Menit</strong>. Sebelum dimulai, dipersilahkan untuk membaca doa terlebih dahulu.</p>

            <strong><label for="nomorMeja" class="form-label">Masukkan nomor meja Anda untuk memulai ujian</label></strong>
            <input type="text" id="nomorMeja" class="form-control" placeholder="Masukkan nomor meja Anda" required <?php if($isDisabled) echo 'disabled';?>>
            <div id="errorMessage" class="error">Silahkan masukkan nomor meja.</div>
            <button id="startTestButton" <?php if($isDisabled) echo 'disabled';?>>Mulai Ujian</button>
        <?php endif; ?>
    </div>
</main>

<!-- Token Modal -->
<div class="modal fade" id="tokenModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-primary">Verifikasi Token Ujian</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-4">
                <div class="text-center mb-4">
                    <i class='bx bx-lock-alt' style="font-size: 4rem; color: #3DC2EC;"></i>
                    <p class="mt-3 text-muted">Silahkan masukkan token ujian yang diberikan oleh pengawas.</p>
                </div>
                <div class="mb-3">
                    <input type="text" id="inputToken" class="form-control form-control-lg text-center fw-bold" placeholder="TOKEN UJIAN" style="letter-spacing: 2px; text-transform: uppercase;">
                    <div id="tokenError" class="text-danger small mt-2 text-center" style="display:none;">Token yang Anda masukkan salah!</div>
                </div>
                <button type="button" class="btn btn-primary w-100 py-2" id="btnSubmitToken">Masuk Ujian</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function () {
  const APP_URL = '<?= APP_URL ?>'; // Ensure APP_URL is available
  
  $('#startTestButton').on('click', function () {
      const nomorMejaInput = $('#nomorMeja').val().trim();

      if (!nomorMejaInput || isNaN(nomorMejaInput) || parseInt(nomorMejaInput) <= 0) {
        $('#errorMessage').text('Nomor meja tidak valid!').show();
        return;
      }
      
      $('#errorMessage').hide();
      
      // Show Token Modal instead of direct redirect
      new bootstrap.Modal(document.getElementById('tokenModal')).show();
  });
  
  // Handle Token Submit
  $('#btnSubmitToken').on('click', function() {
      const token = $('#inputToken').val().trim();
      if(!token) {
          $('#tokenError').text('Masukkan token!').show();
          return;
      }
      
      // Verify Token AJAX
      const btn = $(this);
      btn.prop('disabled', true).text('Memverifikasi...');
      
      $.ajax({
          url: APP_URL + '/exam/verifyToken',
          method: 'POST',
          data: { token: token },
          success: function(res) {
              if(res.status === 'success') {
                  const nomorMeja = $('#nomorMeja').val().trim();
                  window.location.href = `${APP_URL}/soal?nomorMeja=${encodeURIComponent(nomorMeja)}`;
              } else {
                  $('#tokenError').text(res.message || 'Token salah!').show();
                  btn.prop('disabled', false).text('Masuk Ujian');
              }
          },
          error: function() {
              $('#tokenError').text('Terjadi kesalahan server').show();
              btn.prop('disabled', false).text('Masuk Ujian');
          }
      });
  });
  
  // Enter key support for token input
  $('#inputToken').on('keypress', function(e) {
      if(e.which === 13) {
          $('#btnSubmitToken').click();
      }
  });
});

</script>




