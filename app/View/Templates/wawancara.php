<?php
  use app\Controllers\user\WawancaraController;
  $wawancara = WawancaraController::getAllById() ;
?>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');

    .recent-wawancara {
        margin: 0 auto;
        max-width: 90%;
        background-color: #fff;
        padding: 20px;
        border-radius: 16px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    table {
        width: 100%;
        border-collapse: collapse;
        overflow: hidden;
        font-size: 0.95rem;
    }

    table thead {
        background-color: white;
        text-transform: uppercase;
        font-weight: 600;
    }

    table thead th {
        padding: 15px;
        text-align: left;
    }

    table tbody tr {
        transition: background-color 0.3s ease;
    }

    table tbody tr:nth-child(odd) {
        background-color: #f8faff;
    }

    table tbody tr:nth-child(even) {
        background-color: #e8f4fc;
    }

    table tbody tr:hover {
        background-color: rgba(61, 194, 236, 0.2);
    }

    table tbody td {
        padding: 15px;
        text-align: left;
        color: #555;
    }

    table tbody td[colspan] {
        text-align: center;
        font-style: italic;
        color: #888;
    }

    table tbody tr:last-child td {
        border-bottom: none;
    }

    table th,
    table td {
        border-bottom: 1px solid #ddd;
    }

    table th:first-child,
    table td:first-child {
        border-left: none;
    }

    table th:last-child,
    table td:last-child {
        border-right: none;
    }
</style>

<main>
    <h1 class="dashboard">Jadwal Kegiatan</h1>
    <div class="recent-wawancara">
        <table>
            <thead>
                    <th>No</th>
                    <th>Jenis Kegiatan</th>
                    <th>Lokasi</th>
                    <th>Tanggal</th>
                    <th>Waktu</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($wawancara)) : ?>
                    <tr>
                        <td colspan="5">Belum ada Jadwal</td>
                    </tr>
                <?php endif; $i = 1;?>
                <?php foreach ($wawancara as $value) : ?>
                    <tr>
                        <td><?= $i?></td>
                        <td><?= $value['jenis_wawancara'] ?? "" ?></td>
                        <td><?= $value['ruangan'] ?? "" ?></td>
                        <td><?= $value['tanggal']?? "" ?></td>
                        <td><?= $value['waktu'] ?? "" ?></td>
                    </tr>
                       
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>