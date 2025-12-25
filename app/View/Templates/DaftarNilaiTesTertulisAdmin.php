<?php

use App\Controllers\Exam\NilaiAkhirController;
$nilai = NilaiAkhirController::getAllNilaiAkhirMahasiswa();
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

    /* Button Styles */
    .btn.nama-button {
        background: linear-gradient(135deg, #3DC2EC, #3392cc);
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 1rem;
        text-transform: capitalize;
        transition: all 0.3s ease;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .btn.nama-button:hover {
        background: linear-gradient(135deg, #3392cc, #3DC2EC);
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
        transform: translateY(-2px);
    }

    .btn.nama-button:focus {
        outline: none;
    }

    .custom-modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 1000;
    }

    .custom-modal.hidden {
        display: none;
    }

    .custom-modal-content {
        max-height: 80vh;
        background: #fff;
        overflow-y: auto;
        */ border-radius: 12px;
        padding: 20px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        font-family: 'Poppins', sans-serif;
    }


    .close-modal {
        position: absolute;
        top: 10px;
        right: 20px;
        font-size: 24px;
        cursor: pointer;
    }

    .soal-jawaban-cards {
        display: flex;
        flex-wrap: wrap;
        gap: 16px;
        margin-top: 20px;
    }

    .soal-jawaban-card {
        background: rgba(255, 255, 255, 0.8);
        border: 1px solid rgba(0, 0, 0, 0.1);
        border-radius: 12px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        padding: 16px;
        flex: 1 1 calc(50% - 16px);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .soal-jawaban-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 25px rgba(0, 0, 0, 0.2);
    }

    .soal-jawaban-card h6 {
        font-size: 1.1rem;
        color: rgba(0, 0, 0, 0.8);
        margin-bottom: 8px;
        font-weight: 600;
        font-family: 'Poppins', sans-serif;
    }

    .soal-jawaban-card p {
        font-size: 0.95rem;
        color: rgba(0, 0, 0, 0.6);
        margin: 4px 0;
        font-family: 'Poppins', sans-serif;
    }

    .soal-jawaban-card strong {
        color: rgba(0, 0, 0, 0.9);
        font-weight: 600;
    }

    @media (max-width: 768px) {
        .soal-jawaban-card {
            flex: 1 1 100%;
        }
    }

    .input-container {
        position: relative;
        width: 100%;
        max-width: 300px;
        margin-top: 20px;
    }

    #nilaiAkhir {
        width: 100%;
        padding: 12px 40px 12px 15px;
        border-radius: 8px;
        border: 1px solid #ccc;
        font-size: 16px;
        outline: none;
        transition: 0.3s ease-in-out;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    #nilaiAkhir:focus {
        border-color: #007bff;
        box-shadow: 0 2px 8px rgba(0, 123, 255, 0.3);
    }

    #nilaiAkhir:focus+.icon {
        color: #007bff;
    }
</style>

<main>

    <h1 class="dashboard">Daftar Nilai</h1>
    <table id="daftar-nilai" class="table table-hover rounded-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Stambuk</th>
                <th>Nilai Tes tertulis</th>
                <th>Nilai Keseluruhan</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 1;
            foreach ($nilai as $value): ?>
                <tr>
                    <td><?= $i ?></td>
                    <td>
                        <button class="btn nama-button" data-id="<?= htmlspecialchars($value['id'] ?? '') ?>"
                            data-nama="<?= htmlspecialchars($value['nama_lengkap'] ?? 'Tidak ada nama') ?>"
                            data-stambuk="<?= htmlspecialchars($value['stambuk'] ?? 'Tidak ada stambuk') ?>"
                            data-nilai="<?= htmlspecialchars($value['nilai'] ?? 'Tidak ada nilai') ?>"
                            data-total="<?= htmlspecialchars($value['total'] ?? 'Tidak ada nilai') ?>">
                            <?= htmlspecialchars($value['nama_lengkap'] ?? 'Tidak ada nama') ?>
                        </button>
                    </td>
                    <td><?= htmlspecialchars($value['stambuk'] ?? 'Tidak ada stambuk') ?></td>
                    <td><?= htmlspecialchars($value['nilai'] ?? 'Tidak ada nilai') ?></td>
                    <td><?= htmlspecialchars($value['total'] ?? 'Tidak ada nilai') ?></td>
                </tr>
                <?php
                $i++;
            endforeach; ?>
        </tbody>
    </table>
</main>

<div id="customModal" class="custom-modal hidden">
    <div class="custom-modal-content">
        <span id="closeModal" class="close-modal">&times;</span>
        <h2>Detail Mahasiswa</h2>
        <p><strong>Nama:</strong> <span id="modalNama">Tidak ada data</span></p>
        <p><strong>Stambuk:</strong> <span id="modalStambuk">Tidak ada data</span></p>
        <p><strong>Nilai Tes Tertulis:</strong> <span id="modalNilai">Tidak ada data</span></p>
        <p><strong>Nilai Total : <span id="modalTotalNilai">Tidak ada data</span></strong></p>
        <div id="soalJawaban" style="margin-top: 20px;">
            <h5>Soal dan Jawaban</h5>
            <div id="soalJawabanList" class="soal-jawaban-cards"></div>
            <div class="input-container">
                <form id="formNilaiAkhir">
                    <input type="number" id="nilaiAkhir" placeholder="Masukkan Nilai Akhir" max="100">

                    <button type="submit" class="btn btn-primary" style="margin-top: 10px;">Submit</button>
                </form>
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

        document.getElementById("nilaiAkhir").addEventListener("input", function () {
            if (this.value > 100) {
                this.value = 100;
            }
        });
        $('#formNilaiAkhir').on('submit', function (e) {
            e.preventDefault();

            const id = $(this).data('id');
            const nilaiAkhir = $('#nilaiAkhir').val();

            console.log("ID : ", id);
            console.log("Nilai Akhir : ", nilaiAkhir);
            $.ajax({
                url: '<?= APP_URL ?>/updatenilaiakhir',
                type: 'POST',
                data: { id: id, nilai: nilaiAkhir },
                dataType: 'json',
                success: function (response) {
                    if (response.status === 'success') {
                        showModal('Nilai berhasil ditambahkan!', '/tubes_web/public/Assets/gif/success.gif');
                        document.querySelector('a[data-page="lihatnilai"').click();
                    } else {
                        showModal(
                            'Error saat mengupdate nilai',
                            '/tubes_web/public/Assets/gif/failed.gif'
                        );
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error updating nilai:', error);
                    alert('Error saat mengupdate nilai.');
                }
            });
        });
        $('.nama-button').on('click', function (e) {
            e.preventDefault();

            const id = $(this).data('id');
            const nama = $(this).data('nama') || 'Tidak ada data';
            const stambuk = $(this).data('stambuk') || 'Tidak ada data';
            const nilai = $(this).data('nilai') || 'Tidak ada data';
            const nilaiAkhir = $(this).data('total') || 'Tidak ada data';
            console.log("ID:", id);
            $('#modalNama').text(nama);
            $('#modalStambuk').text(stambuk);
            $('#modalNilai').text(nilai);
            $('#modalTotalNilai').text(nilaiAkhir);
            $('#formNilaiAkhir').data('id', id);
            $.ajax({
                url: '<?= APP_URL ?>/getsoaljawaban',
                type: 'POST',
                data: { id: id },
                dataType: 'json',
                success: function (response) {
                    if (response.status === 'success' && response.data.length > 0) {
                        let soalJawabanHTML = '';
                        let i = 0;
                        response.data.forEach(item => {
                            i++;
                            soalJawabanHTML += `
        <div class="soal-jawaban-card">
        <h6>Soal ke-${i}</h6>
            <h6>Pertanyaan:</h6>
            <p>${item.deskripsi}</p>
            <h6>Pilihan:</h6>
            <p>${item.pilihan}</p>
            <h6>Jawaban Benar:</h6>
            <p><strong>${item.jawaban}</strong></p>
            <h6>Jawaban User:</h6>
            <p><strong>${item.jawaban_user}</strong></p>
        </div>`;
                        });
                        $('#soalJawabanList').html(soalJawabanHTML);
                    } else {
                        const message = response.message || 'Tidak ada data soal dan jawaban.';
                        $('#soalJawabanList').html(`<li>${message}</li>`);
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error fetching soal dan jawaban:', error);
                    $('#soalJawabanList').html('<li>Error saat mengambil data.</li>');
                }
            });

            $('#customModal').removeClass('hidden');
        });

        $('#closeModal').on('click', function () {
            $('#customModal').addClass('hidden');
        });

        $('#customModal').on('click', function (e) {
            if ($(e.target).is('#customModal')) {
                $('#customModal').addClass('hidden');
            }
        });
    });
</script>