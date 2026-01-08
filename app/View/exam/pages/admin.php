<?php
/**
 * Tes Tulis Admin View
 * 
 * Data yang diterima dari Controller:
 * @var array $allSoal - Daftar semua soal
 */
$allSoal = $allSoal ?? [];
?>

<style>
  @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');

  .table-hover {
    background-color: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
    width: 100%;
    margin-top: 20px;
    font-size: 0.95rem;
  }

  .table-hover th,
  .table-hover td {
    padding: 16px 20px;
    text-align: left;
    color: #555;
  }

  .table-hover th {
    background-color: #3DC2EC;
    color: white;
    font-weight: 600;
    text-transform: uppercase;
  }

  .table-hover tr:nth-child(odd) {
    background-color: #f8faff;
  }

  .table-hover tr:nth-child(even) {
    background-color: #e8f4fc;
  }

  .table-hover tr:hover {
    background-color: rgba(61, 194, 236, 0.2);
    cursor: pointer;
  }

  .rounded-table {
    border-radius: 12px;
    overflow: hidden;
  }

  .btn-primary {
    background: linear-gradient(135deg, #3DC2EC, #3392cc);
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 1rem;
    transition: all 0.3s ease;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
  }

  .btn-primary:hover {
    background: linear-gradient(135deg, #3392cc, #3DC2EC);
    box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
    transform: translateY(-2px);
  }

  .btn-primary:focus {
    outline: none;
  }

  .btn-danger {
    background-color: #dc3545;
    color: white;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s ease;
  }

  .btn-danger:hover {
    background-color: #c82333;
  }

  .btn-secondary {
    background-color: #6c757d;
    color: white;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s ease;
  }

  .btn-secondary:hover {
    background-color: #5a6268;
  }

  /* Modal Styles */
  .modal-content {
    border-radius: 12px;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
    font-family: 'Poppins', sans-serif;
  }

  .modal-header {
    background: linear-gradient(135deg, #3DC2EC, #3392cc);
    color: white;
    border-bottom: none;
    border-radius: 12px 12px 0 0;
    padding: 20px;
  }

  .modal-header h5 {
    font-size: 1.5rem;
    font-weight: 600;
  }

  .modal-body {
    padding: 20px;
    color: #555;
  }

  .modal-footer {
    border-top: none;
    padding: 20px;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
  }

  /* Form Inputs */
  .form-control {
    border-radius: 8px;
    border: 1px solid #ccc;
    padding: 10px 15px;
    font-size: 1rem;
    transition: border-color 0.3s ease;
  }

  .form-control:focus {
    border-color: #3DC2EC;
    box-shadow: 0 0 5px rgba(61, 194, 236, 0.5);
    outline: none;
  }

  .form-check-input:checked {
    background-color: #3DC2EC;
    border-color: #3DC2EC;
  }

  .form-check-label {
    font-size: 0.95rem;
    color: #555;
  }
</style>

<main>
  <h1 class="dashboard">Tes Tertulis</h1>
  <button type="button" data-bs-toggle="modal" data-bs-target="#addSoalModal" class="btn btn-primary mb-3">
    Tambah Soal
  </button>

  <table class="table table-hover rounded-table ">
    <thead class="table-dark">
      <tr>
        <th>No</th>
        <th>Deskripsi</th>
        <th>Pilihan</th>
        <th>Jawaban</th>
      </tr>
    </thead>
    <tbody id="soalTableBody">
      <?php $i = 1; ?>
      <?php foreach ($allSoal as $soal): ?>
        <tr class="soal-row" data-id="<?= $soal['id'] ?>" data-deskripsi="<?= htmlspecialchars($soal['deskripsi']) ?>"
          data-pilihan="<?= htmlspecialchars($soal['pilihan']) ?>"
          data-jawaban="<?= htmlspecialchars($soal['jawaban']) ?>">
          <td><?= $i++ ?></td>
          <td><?= $soal['deskripsi'] ?></td>
          <td><?= $soal['pilihan'] ?></td>
          <td><?= $soal['jawaban'] ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <div class="modal fade" id="addSoalModal" tabindex="-1" aria-labelledby="addSoalModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form id="addSoalForm" enctype="multipart/form-data">
          <div class="modal-header">
            <h5 class="modal-title" id="addSoalModalLabel">Tambah Soal</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label for="deskripsi" class="form-label"><b>Deskripsi</b></label>
              <textarea class="form-control" id="deskripsi" name="deskripsi" required></textarea>
            </div>
            <div class="mb-3">
              <label class="form-label"><b>Tipe Jawaban</b></label><br>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="tipeJawaban" id="pilihanGanda" value="pilihan_ganda"
                  checked>
                <label class="form-check-label" for="pilihanGanda">Pilihan Ganda</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="tipeJawaban" id="textBox" value="textbox">
                <label class="form-check-label" for="textBox">Textbox</label>
              </div>
            </div>

            <div id="pilihanGandaInput" class="mb-3">
              <label for="pilihan" class="form-label"><b>Pilihan</b></label>
              <textarea class="form-control" id="pilihan" name="pilihan"
                placeholder="Pisahkan dengan koma, contoh: A,B,C,D"></textarea>
            </div>

            <div id="jawabanGandaInput" class="mb-3">
              <label for="jawaban" class="form-label"><b>Jawaban</b></label>
              <textarea class="form-control" id="jawaban" name="jawaban"
                placeholder="Masukkan jawaban yang benar dari pilihan"></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            <button type="submit" class="btn btn-primary">Tambah</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</main>

<div class="modal fade" id="updateSoalModal" tabindex="-1" aria-labelledby="updateSoalModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="updateSoalForm" enctype="multipart/form-data">
        <div class="modal-header">
          <h5 class="modal-title" id="updateSoalModalLabel">Update Soal</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="updateSoalId" name="id">
          <div class="mb-3">
            <label for="updateDeskripsi" class="form-label"><b>Deskripsi</b></label>
            <textarea class="form-control" id="updateDeskripsi" name="deskripsi" required></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label"><b>Tipe Jawaban</b></label><br>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="updateTipeJawaban" id="updatePilihanGanda"
                value="pilihan_ganda" checked>
              <label class="form-check-label" for="updatePilihanGanda">Pilihan Ganda</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="updateTipeJawaban" id="updateTextBox" value="textbox">
              <label class="form-check-label" for="updateTextBox">Textbox</label>
            </div>
          </div>
          <div id="updatePilihanGandaInput" class="mb-3">
            <label for="updatePilihan" class="form-label"><b>Pilihan</b></label>
            <textarea class="form-control" id="updatePilihan" name="pilihan"
              placeholder="Pisahkan dengan koma, contoh: A,B,C,D"></textarea>
          </div>
          <div id="updateJawabanGandaInput" class="mb-3">
            <label for="updateJawaban" class="form-label"><b>Jawaban</b></label>
            <textarea class="form-control" id="updateJawaban" name="jawaban"
              placeholder="Masukkan jawaban yang benar dari pilihan"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
          <button type="submit" class="btn btn-primary">Update</button>
        </div>
      </form>
    </div>
  </div>
</div>


<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="detailModalLabel">Detail Soal</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p><strong>Deskripsi:</strong> <span id="modalDeskripsi"></span></p>
        <p><strong>Pilihan:</strong> <span id="modalPilihan"></span></p>
        <p><strong>Jawaban:</strong> <span id="modalJawaban"></span></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="editButton">Edit Soal</button>
        <button type="button" class="btn btn-danger" id="deleteButton">Hapus Soal</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<script>
  $(document).ready(function () {

    function showModal(message, gifUrl = null) {
      const modal = document.getElementById('customModal');
      if (!modal) {
        return;
      }

      const modalMessage = document.getElementById('modalMessage');
      const modalGif = document.getElementById('modalGif');
      const closeModal = document.getElementById('closeModal');

      modalMessage.textContent = message;
      modalGif.style.display = gifUrl ? 'block' : 'none';
      if (gifUrl) modalGif.src = gifUrl;

      modal.style.display = 'flex';

      $(closeModal).off('click').on('click', function () {
        modal.style.display = 'none';
      });

      $(window).off('click').on('click', function (event) {
        if (event.target === modal) {
          modal.style.display = 'none';
        }
      });
    }

    function showConfirm(message, onConfirm = null, onCancel = null) {
      const modal = document.getElementById('confirmModal');
      if (!modal) {
        return;
      }

      const modalMessage = document.getElementById('confirmModalMessage');
      const confirmButton = document.getElementById('confirmModalConfirm');
      const cancelButton = document.getElementById('confirmModalCancel');

      modalMessage.textContent = message;
      modal.style.display = 'flex';

      $(confirmButton).off('click').on('click', function () {
        if (onConfirm) onConfirm();
        modal.style.display = 'none';
      });

      $(cancelButton).off('click').on('click', function () {
        if (onCancel) onCancel();
        modal.style.display = 'none';
      });

      $(window).off('click').on('click', function (event) {
        if (event.target === modal) {
          modal.style.display = 'none';
        }
      });
    }

    $(".soal-row").on("click", function () {
      const id = $(this).data("id");
      const deskripsi = $(this).data("deskripsi");
      const pilihan = $(this).data("pilihan");
      const jawaban = $(this).data("jawaban");

      $("#modalDeskripsi").text(deskripsi);
      $("#modalPilihan").text(pilihan);
      $("#modalJawaban").text(jawaban);

      $("#editButton").data("id", id);
      $("#deleteButton").data("id", id);

      $("#detailModal").modal("show");
    });

    $('input[name="tipeJawaban"]').on("change", function () {
      if ($(this).val() === "pilihan_ganda") {
        $("#pilihanGandaInput").show();
        $("#jawabanGandaInput").show();
      } else {
        $("#pilihanGandaInput").hide();
        $("#jawabanGandaInput").hide();
      }
    });

    if ($('input[name="tipeJawaban"]:checked').val() === "pilihan_ganda") {
      $("#pilihanGandaInput").show();
      $("#jawabanGandaInput").show();
    } else {
      $("#pilihanGandaInput").hide();
      $("#jawabanGandaInput").hide();
    }

    $('input[name="updateTipeJawaban"]').on("change", function () {
      if ($(this).val() === "pilihan_ganda") {
        $("#updatePilihanGandaInput").show();
        $("#updateJawabanGandaInput").show();
      } else {
        $("#updatePilihanGandaInput").hide();
        $("#updateJawabanGandaInput").hide();
      }
    });

    $('input[name="updateTipeSoal"]').on("change", function () {
      if ($(this).val() === "iya") {
        $("#updateGambar").closest(".mb-3").show();
      } else {
        $("#updateGambar").closest(".mb-3").hide();
      }
    });

    if ($('input[name="updateTipeSoal"]:checked').val() === "iya") {
      $("#updateGambar").closest(".mb-3").show();
    } else {
      $("#updateGambar").closest(".mb-3").hide();
    }

    if ($('input[name="updateTipeJawaban"]:checked').val() === "pilihan_ganda") {
      $("#updatePilihanGandaInput").show();
      $("#updateJawabanGandaInput").show();
    } else {
      $("#updatePilihanGandaInput").hide();
      $("#updateJawabanGandaInput").hide();
    }

    $("#addSoalForm").on("submit", function (e) {
      e.preventDefault();

      const formData = new FormData();
      formData.append("deskripsi", $("#deskripsi").val());
      formData.append("tipeSoal", $('input[name="tipeSoal"]:checked').val());

      formData.append("tipeJawaban", $('input[name="tipeJawaban"]:checked').val());

      if ($('input[name="tipeJawaban"]:checked').val() === "pilihan_ganda") {
        formData.append("pilihan", $("#pilihan").val());
        formData.append("jawaban", $("#jawaban").val());
      }

      console.log("deskripsi: ", formData.get("deskripsi"));
      console.log("pilihan: ", formData.get("pilihan"));
      console.log("jawaban: ", formData.get("jawaban"));

      $.ajax({
        url: '/Sistem-Pendaftaran-Calon-Asisten/public/addingsoal',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        },
        success: function (response) {
          if (response.status === 'success') {
            showModal('soal berhasil ditambahkan!', '/Sistem-Pendaftaran-Calon-Asisten/public/Assets/gif/success.gif');
            document.querySelector('a[data-page="tesTulis"]').click();
          } else {
            showModal('soal berhasil ditambahkan!', '/Sistem-Pendaftaran-Calon-Asisten/public/Assets/gif/success.gif');
            document.querySelector('a[data-page="tesTulis"]').click();
          }
        },
        error: function (xhr) {
          console.error('Error:', xhr.responseText);
        }
      });

      $('#addSoalModal').modal('hide');
    });


    $("#deleteButton").on("click", function () {
      const id = $(this).data("id");
      console.log("id: ", id);
      $('#detailModal').modal('hide');
      showConfirm('Apakah Anda yakin ingin menghapus ruangan ini?', function () {
        $.ajax({
          url: '<?= APP_URL ?>/deletesoal',
          type: 'POST',
          data: { id },
          success: function (response) {
            if (response.status === 'success') {
              showModal('Soal berhasil dihapus!', '/Sistem-Pendaftaran-Calon-Asisten/public/Assets/gif/success.gif');
              document.querySelector('a[data-page="tesTulis"]').click();
            } else {
              showModal('Soal berhasil dihapus!', '/Sistem-Pendaftaran-Calon-Asisten/public/Assets/gif/success.gif');
              document.querySelector('a[data-page="tesTulis"]').click();

            }
          },
          error: function (xhr) {
            console.error('Error:', xhr.responseText);
          }
        });
        $('#detailModal').modal('hide');
      });
    });

    $("#editButton").on("click", function () {
      const id = $(this).data("id");
      const deskripsi = $("#modalDeskripsi").text();
      const pilihan = $("#modalPilihan").text();
      const jawaban = $("#modalJawaban").text();

      $("#updateSoalId").val(id);
      $("#updateDeskripsi").val(deskripsi);
      $("#updatePilihan").val(pilihan);
      $("#updateJawaban").val(jawaban);
      $("#detailModal").modal("hide");
      $("#updateSoalModal").modal("show");
    });

    $("#updateSoalForm").on("submit", function (e) {
      e.preventDefault();

      const formData = new FormData();
      formData.append("id", $("#updateSoalId").val());
      formData.append("deskripsi", $("#updateDeskripsi").val());
      formData.append("tipeSoal", $('input[name="updateTipeSoal"]:checked').val());

      formData.append("tipeJawaban", $('input[name="updateTipeJawaban"]:checked').val());

      if ($('input[name="updateTipeJawaban"]:checked').val() === "pilihan_ganda") {
        formData.append("pilihan", $("#updatePilihan").val());
        formData.append("jawaban", $("#updateJawaban").val());
      }
      console.log("id: ", formData.get("id"));
      console.log("deskripsi: ", formData.get("deskripsi"));
      console.log("pilihan: ", formData.get("pilihan"));
      console.log("jawaban: ", formData.get("jawaban"));

      $.ajax({
        url: '/Sistem-Pendaftaran-Calon-Asisten/public/updatesoal',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
        },
        success: function (response) {
          try {
            const jsonResponse = JSON.parse(response);
            if (jsonResponse.status === 'success') {
              showModal('Soal berhasil diupdate!', '/Sistem-Pendaftaran-Calon-Asisten/public/Assets/gif/success.gif');
              document.querySelector('a[data-page="tesTulis"]').click();
            } else {
              showModal('Soal gagal diupdate: ' + jsonResponse.message, '/Sistem-Pendaftaran-Calon-Asisten/public/Assets/gif/failed.gif');
              document.querySelector('a[data-page="tesTulis"]').click();
            }
          } catch (error) {
            console.log('Error parsing response:', error);
            alert('Terjadi kesalahan, coba lagi.');
          }
        },
        error: function (xhr) {
          console.error('Error:', xhr.responseText);
          alert('Terjadi kesalahan server. Silakan coba lagi.');
        },
      });

      $("#updateSoalModal").modal("hide");
    });
  });
</script>