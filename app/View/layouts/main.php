<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Img/iclabs.png">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <link rel="stylesheet"
    href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="stylesheet" href="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Style/biodata.css" />
    <link rel="stylesheet" href="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Style/sidebarStyle.css">
    <link rel="stylesheet" href="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Style/wawancara.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Style/pengumuman.css" />
    <link rel="stylesheet" href="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Style/presentasi.css" />
    <link rel="stylesheet" href="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Style/uploadberkas.css" />
    <link rel="stylesheet" href="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Style/dashboardStyle.css" />

    <link rel="stylesheet" href="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Style/alertstyle.css">
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    
    <title>IC-ASSIST</title>
</head>

<body>
    <?php require_once __DIR__ . "/sidebar.php"?>
    
    <div class="main-content" id="content">
        <?php require_once __DIR__ . "/../user/dashboard.php"?>
    </div>

    <div id="customModal" class="alertmodal">
    <div class="modal-content">
        <!-- GIF Animasi -->
        <img id="modalGif" src="" alt="Animation" style="width: 100px; margin-bottom: 15px; display: none;">
        
        <!-- Pesan Custom -->
        <p id="modalMessage" style="margin: 10px 0; font-size: 18px;">Pesan akan ditampilkan di sini.</p>
        
        <!-- Tombol Close -->
        <button id="closeModal" class="btn btn-primary" style="margin-top: 10px;">Tutup</button>
    </div>
</div>
   
    <script> const APP_URL = '<?php echo APP_URL; ?>' </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    
    <script src="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Script/app.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"></script>
    <script src="/Sistem-Pendaftaran-Calon-Asisten/public/Assets/Script/sidebar/ScriptSidebar.js"></script>
    
</body>
