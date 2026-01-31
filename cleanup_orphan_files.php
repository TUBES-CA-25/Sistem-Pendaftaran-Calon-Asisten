<?php
/**
 * Script untuk membersihkan file orphan (file yang tidak ada di database)
 *
 * PERINGATAN: Script ini akan MENGHAPUS file dari server!
 * Pastikan Anda sudah backup folder sebelum menjalankan script ini.
 *
 * Cara menjalankan:
 * 1. Buka browser: http://localhost/Sistem-Pendaftaran-Calon-Asisten/cleanup_orphan_files.php
 * 2. Atau jalankan via terminal: php cleanup_orphan_files.php
 */

// Include autoload to load all necessary classes
require_once __DIR__ . '/app/Core/autoload.php';

// Set time limit untuk script yang mungkin memakan waktu
set_time_limit(300); // 5 menit

// Output styling
?>
<!DOCTYPE html>
<html>
<head>
    <title>Cleanup Files & Database</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h1 { color: #333; border-bottom: 3px solid #4CAF50; padding-bottom: 10px; }
        .tabs { display: flex; gap: 10px; margin: 20px 0; border-bottom: 2px solid #e5e7eb; }
        .tab { padding: 12px 24px; cursor: pointer; background: #f3f4f6; border: none; border-radius: 8px 8px 0 0; font-size: 16px; font-weight: 500; transition: all 0.3s; }
        .tab:hover { background: #e5e7eb; }
        .tab.active { background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%); color: white; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        .section { margin: 20px 0; padding: 15px; background: #f9f9f9; border-left: 4px solid #2196F3; }
        .warning { background: #fff3cd; border-left-color: #ffc107; padding: 15px; margin: 20px 0; }
        .danger-zone { background: #fee; border-left-color: #dc3545; padding: 15px; margin: 20px 0; }
        .success { color: #4CAF50; font-weight: bold; }
        .error { color: #f44336; font-weight: bold; }
        .info { color: #2196F3; }
        .file-list { background: white; padding: 10px; margin: 10px 0; border: 1px solid #ddd; max-height: 300px; overflow-y: auto; }
        .stats { display: flex; gap: 20px; margin: 20px 0; }
        .stat-box { flex: 1; padding: 15px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 8px; text-align: center; }
        .stat-number { font-size: 32px; font-weight: bold; }
        .stat-label { font-size: 14px; opacity: 0.9; }
        button { background: #4CAF50; color: white; border: none; padding: 12px 24px; border-radius: 4px; cursor: pointer; font-size: 16px; margin: 5px; }
        button:hover { background: #45a049; }
        button.danger { background: #f44336; }
        button.danger:hover { background: #da190b; }
        button.primary { background: #2196F3; }
        button.primary:hover { background: #1976D2; }
        .hidden { display: none; }
    </style>
    <script>
        function switchTab(tabName) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.remove('active');
            });
            // Remove active class from all tabs
            document.querySelectorAll('.tab').forEach(tab => {
                tab.classList.remove('active');
            });
            // Show selected tab content
            document.getElementById(tabName + '-content').classList.add('active');
            // Add active class to selected tab
            event.target.classList.add('active');
        }
    </script>
</head>
<body>
<div class="container">
    <h1>🧹 Cleanup Files & Database Manager</h1>

    <!-- Tabs -->
    <div class="tabs">
        <button class="tab active" onclick="switchTab('orphan')">🔍 Clean Orphan Files</button>
        <button class="tab" onclick="switchTab('all')">🗑️ Clean All Uploads</button>
    </div>

    <!-- Tab 1: Clean Orphan Files -->
    <div id="orphan-content" class="tab-content active">
        <div class="warning">
            <strong>⚠️ PERINGATAN:</strong> Tab ini akan menghapus file orphan (file yang ada di folder tapi tidak ada di database).
            Pastikan Anda sudah backup folder berikut sebelum melanjutkan:
            <ul>
                <li>res/imageUser/</li>
                <li>res/berkasUser/</li>
                <li>res/profile/</li>
                <li>res/makalahUser/</li>
                <li>res/pptUser/</li>
            </ul>
        </div>

<?php

$isPostRequest = $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']);
$activeTab = 'orphan'; // Default tab

if ($isPostRequest) {
    $action = $_POST['action'];

    // Determine which tab should be active
    if ($action === 'scan_all' || $action === 'cleanup_all') {
        $activeTab = 'all';
    }

    if ($action === 'scan') {
        scanOrphanFiles(false);
    } elseif ($action === 'cleanup' && isset($_POST['confirm'])) {
        scanOrphanFiles(true);
    } elseif ($action === 'scan_all') {
        scanAllUploads(false);
    } elseif ($action === 'cleanup_all' && isset($_POST['confirm_all'])) {
        cleanupAllUploads();
    }
} else {
    // Show scan button for orphan tab
    ?>
    <form method="POST" style="text-align: center; margin: 30px 0;">
        <input type="hidden" name="action" value="scan">
        <button type="submit" class="primary">🔍 Scan File Orphan</button>
    </form>
    </div>

    <!-- Tab 2: Clean All Uploads -->
    <div id="all-content" class="tab-content">
        <div class="danger-zone">
            <strong>🚨 DANGER ZONE - CLEAN ALL UPLOADS:</strong> Tab ini akan menghapus SEMUA file upload dan database records!
            <br><br>
            <strong>Yang akan dihapus:</strong>
            <ul>
                <li>✗ Semua file di res/imageUser/</li>
                <li>✗ Semua file di res/berkasUser/</li>
                <li>✗ Semua file di res/profile/</li>
                <li>✗ Semua file di res/makalahUser/</li>
                <li>✗ Semua file di res/pptUser/</li>
                <li>✗ Semua records di database: berkas_mahasiswa, presentasi, foto_profil mahasiswa</li>
            </ul>
            <strong>⚠️ PERINGATAN: Fitur ini untuk RESET SISTEM saat development/testing!</strong><br>
            <strong>⚠️ BACKUP DATABASE dan FOLDER sebelum menggunakan fitur ini!</strong>
        </div>

        <form method="POST" style="text-align: center; margin: 30px 0;">
            <input type="hidden" name="action" value="scan_all">
            <button type="submit" class="primary">📊 Lihat Data yang Akan Dihapus</button>
        </form>
    </div>

    <script>
        // Auto-switch to correct tab after POST request
        <?php if ($activeTab === 'all'): ?>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('orphan-content').classList.remove('active');
            document.getElementById('all-content').classList.add('active');
            document.querySelectorAll('.tab')[0].classList.remove('active');
            document.querySelectorAll('.tab')[1].classList.add('active');
        });
        <?php endif; ?>
    </script>
    <?php
}

function scanOrphanFiles($deleteFiles = false) {
    try {
        $db = \App\Core\Model::getDB();
        $basePath = __DIR__ . DIRECTORY_SEPARATOR . 'res' . DIRECTORY_SEPARATOR;

        $totalOrphans = 0;
        $totalSize = 0;
        $deletedCount = 0;
        $deletedSize = 0;

        echo '<div class="section">';
        echo '<h2>' . ($deleteFiles ? '🗑️ Menghapus File Orphan...' : '🔍 Hasil Scan File Orphan') . '</h2>';

        // 1. Scan imageUser (foto berkas)
        echo '<h3>📁 res/imageUser/ (Foto Berkas)</h3>';
        $result = scanDirectory($db, $basePath . 'imageUser', 'berkas_mahasiswa', 'foto', $deleteFiles);
        $totalOrphans += $result['orphan_count'];
        $totalSize += $result['orphan_size'];
        $deletedCount += $result['deleted_count'];
        $deletedSize += $result['deleted_size'];

        // 2. Scan berkasUser (CV, transkrip, surat)
        echo '<h3>📁 res/berkasUser/ (CV, Transkrip, Surat Pernyataan)</h3>';
        $result = scanBerkasUser($db, $basePath . 'berkasUser', $deleteFiles);
        $totalOrphans += $result['orphan_count'];
        $totalSize += $result['orphan_size'];
        $deletedCount += $result['deleted_count'];
        $deletedSize += $result['deleted_size'];

        // 3. Scan profile (foto profil)
        echo '<h3>📁 res/profile/ (Foto Profil)</h3>';
        $result = scanDirectory($db, $basePath . 'profile', 'mahasiswa', 'foto_profil', $deleteFiles);
        $totalOrphans += $result['orphan_count'];
        $totalSize += $result['orphan_size'];
        $deletedCount += $result['deleted_count'];
        $deletedSize += $result['deleted_size'];

        // 4. Scan makalahUser
        echo '<h3>📁 res/makalahUser/ (Makalah)</h3>';
        $result = scanDirectory($db, $basePath . 'makalahUser', 'presentasi', 'makalah', $deleteFiles);
        $totalOrphans += $result['orphan_count'];
        $totalSize += $result['orphan_size'];
        $deletedCount += $result['deleted_count'];
        $deletedSize += $result['deleted_size'];

        // 5. Scan pptUser
        echo '<h3>📁 res/pptUser/ (PPT)</h3>';
        $result = scanDirectory($db, $basePath . 'pptUser', 'presentasi', 'ppt', $deleteFiles);
        $totalOrphans += $result['orphan_count'];
        $totalSize += $result['orphan_size'];
        $deletedCount += $result['deleted_count'];
        $deletedSize += $result['deleted_size'];

        echo '</div>';

        // Statistics
        echo '<div class="stats">';
        echo '<div class="stat-box">';
        echo '<div class="stat-number">' . $totalOrphans . '</div>';
        echo '<div class="stat-label">File Orphan Ditemukan</div>';
        echo '</div>';
        echo '<div class="stat-box">';
        echo '<div class="stat-number">' . formatSize($totalSize) . '</div>';
        echo '<div class="stat-label">Total Ukuran</div>';
        echo '</div>';
        if ($deleteFiles) {
            echo '<div class="stat-box" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">';
            echo '<div class="stat-number">' . $deletedCount . '</div>';
            echo '<div class="stat-label">File Dihapus</div>';
            echo '</div>';
            echo '<div class="stat-box" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">';
            echo '<div class="stat-number">' . formatSize($deletedSize) . '</div>';
            echo '<div class="stat-label">Space Dibebaskan</div>';
            echo '</div>';
        }
        echo '</div>';

        if (!$deleteFiles && $totalOrphans > 0) {
            echo '<div class="warning">';
            echo '<p><strong>Ditemukan ' . $totalOrphans . ' file orphan (' . formatSize($totalSize) . ')</strong></p>';
            echo '<p>Klik tombol di bawah untuk menghapus file-file tersebut:</p>';
            echo '<form method="POST" onsubmit="return confirm(\'Apakah Anda yakin ingin menghapus ' . $totalOrphans . ' file orphan?\');">';
            echo '<input type="hidden" name="action" value="cleanup">';
            echo '<input type="hidden" name="confirm" value="1">';
            echo '<button type="submit" class="danger">🗑️ Hapus Semua File Orphan</button>';
            echo '</form>';
            echo '</div>';
        } elseif ($deleteFiles) {
            echo '<div class="section" style="background: #d4edda; border-left-color: #28a745;">';
            echo '<h3 class="success">✅ Cleanup Selesai!</h3>';
            echo '<p>Berhasil menghapus ' . $deletedCount . ' file orphan dan membebaskan ' . formatSize($deletedSize) . ' space.</p>';
            echo '</div>';
        } elseif ($totalOrphans === 0) {
            echo '<div class="section" style="background: #d4edda; border-left-color: #28a745;">';
            echo '<h3 class="success">✅ Tidak Ada File Orphan</h3>';
            echo '<p>Semua file sudah bersih dan sesuai dengan database.</p>';
            echo '</div>';
        }

    } catch (Exception $e) {
        echo '<div class="section" style="background: #f8d7da; border-left-color: #dc3545;">';
        echo '<h3 class="error">❌ Error</h3>';
        echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '</div>';
    }
}

function scanDirectory($db, $dirPath, $table, $column, $deleteFiles) {
    $orphans = [];
    $orphanSize = 0;
    $deletedCount = 0;
    $deletedSize = 0;

    if (!is_dir($dirPath)) {
        echo '<p class="info">Folder tidak ditemukan: ' . htmlspecialchars($dirPath) . '</p>';
        return ['orphan_count' => 0, 'orphan_size' => 0, 'deleted_count' => 0, 'deleted_size' => 0];
    }

    // Get all files in database
    $stmt = $db->prepare("SELECT DISTINCT $column FROM $table WHERE $column IS NOT NULL AND $column != ''");
    $stmt->execute();
    $dbFiles = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $dbFiles = array_filter($dbFiles); // Remove empty values

    // Scan directory
    $files = array_diff(scandir($dirPath), ['.', '..']);

    foreach ($files as $file) {
        $filePath = $dirPath . DIRECTORY_SEPARATOR . $file;
        if (is_file($filePath) && !in_array($file, $dbFiles)) {
            $size = filesize($filePath);
            $orphans[] = ['name' => $file, 'size' => $size, 'path' => $filePath];
            $orphanSize += $size;
        }
    }

    if (count($orphans) > 0) {
        echo '<div class="file-list">';
        echo '<p><strong>' . count($orphans) . ' file orphan ditemukan:</strong></p>';
        echo '<ul>';
        foreach ($orphans as $orphan) {
            echo '<li>' . htmlspecialchars($orphan['name']) . ' (' . formatSize($orphan['size']) . ')';
            if ($deleteFiles) {
                if (@unlink($orphan['path'])) {
                    echo ' <span class="success">✓ Dihapus</span>';
                    $deletedCount++;
                    $deletedSize += $orphan['size'];
                } else {
                    echo ' <span class="error">✗ Gagal dihapus</span>';
                }
            }
            echo '</li>';
        }
        echo '</ul>';
        echo '</div>';
    } else {
        echo '<p class="success">✓ Tidak ada file orphan</p>';
    }

    return [
        'orphan_count' => count($orphans),
        'orphan_size' => $orphanSize,
        'deleted_count' => $deletedCount,
        'deleted_size' => $deletedSize
    ];
}

function scanBerkasUser($db, $dirPath, $deleteFiles) {
    $orphans = [];
    $orphanSize = 0;
    $deletedCount = 0;
    $deletedSize = 0;

    if (!is_dir($dirPath)) {
        echo '<p class="info">Folder tidak ditemukan: ' . htmlspecialchars($dirPath) . '</p>';
        return ['orphan_count' => 0, 'orphan_size' => 0, 'deleted_count' => 0, 'deleted_size' => 0];
    }

    // Get all files in database (cv, transkrip, surat)
    $stmt = $db->prepare("SELECT cv, transkrip_nilai, surat_pernyataan FROM berkas_mahasiswa WHERE cv IS NOT NULL OR transkrip_nilai IS NOT NULL OR surat_pernyataan IS NOT NULL");
    $stmt->execute();
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $dbFiles = [];
    foreach ($records as $record) {
        if (!empty($record['cv'])) $dbFiles[] = $record['cv'];
        if (!empty($record['transkrip_nilai'])) $dbFiles[] = $record['transkrip_nilai'];
        if (!empty($record['surat_pernyataan'])) $dbFiles[] = $record['surat_pernyataan'];
    }

    // Scan directory
    $files = array_diff(scandir($dirPath), ['.', '..']);

    foreach ($files as $file) {
        $filePath = $dirPath . DIRECTORY_SEPARATOR . $file;
        if (is_file($filePath) && !in_array($file, $dbFiles)) {
            $size = filesize($filePath);
            $orphans[] = ['name' => $file, 'size' => $size, 'path' => $filePath];
            $orphanSize += $size;
        }
    }

    if (count($orphans) > 0) {
        echo '<div class="file-list">';
        echo '<p><strong>' . count($orphans) . ' file orphan ditemukan:</strong></p>';
        echo '<ul>';
        foreach ($orphans as $orphan) {
            echo '<li>' . htmlspecialchars($orphan['name']) . ' (' . formatSize($orphan['size']) . ')';
            if ($deleteFiles) {
                if (@unlink($orphan['path'])) {
                    echo ' <span class="success">✓ Dihapus</span>';
                    $deletedCount++;
                    $deletedSize += $orphan['size'];
                } else {
                    echo ' <span class="error">✗ Gagal dihapus</span>';
                }
            }
            echo '</li>';
        }
        echo '</ul>';
        echo '</div>';
    } else {
        echo '<p class="success">✓ Tidak ada file orphan</p>';
    }

    return [
        'orphan_count' => count($orphans),
        'orphan_size' => $orphanSize,
        'deleted_count' => $deletedCount,
        'deleted_size' => $deletedSize
    ];
}

function formatSize($bytes) {
    $units = ['B', 'KB', 'MB', 'GB'];
    $i = 0;
    while ($bytes >= 1024 && $i < count($units) - 1) {
        $bytes /= 1024;
        $i++;
    }
    return round($bytes, 2) . ' ' . $units[$i];
}

function scanAllUploads($deleteFiles = false) {
    try {
        $db = \App\Core\Model::getDB();
        $basePath = __DIR__ . DIRECTORY_SEPARATOR . 'res' . DIRECTORY_SEPARATOR;

        echo '<div class="section">';
        echo '<h2>📊 Data yang Akan Dihapus</h2>';

        // Count files in each directory
        $imageUserCount = countFilesInDir($basePath . 'imageUser');
        $berkasUserCount = countFilesInDir($basePath . 'berkasUser');
        $profileCount = countFilesInDir($basePath . 'profile');
        $makalahCount = countFilesInDir($basePath . 'makalahUser');
        $pptCount = countFilesInDir($basePath . 'pptUser');

        // Calculate total sizes
        $imageUserSize = getDirSize($basePath . 'imageUser');
        $berkasUserSize = getDirSize($basePath . 'berkasUser');
        $profileSize = getDirSize($basePath . 'profile');
        $makalahSize = getDirSize($basePath . 'makalahUser');
        $pptSize = getDirSize($basePath . 'pptUser');

        $totalSize = $imageUserSize + $berkasUserSize + $profileSize + $makalahSize + $pptSize;
        $totalFiles = $imageUserCount + $berkasUserCount + $profileCount + $makalahCount + $pptCount;

        // Count database records
        $berkasRecords = $db->query("SELECT COUNT(*) FROM berkas_mahasiswa")->fetchColumn();
        $presentasiRecords = $db->query("SELECT COUNT(*) FROM presentasi")->fetchColumn();
        $profileRecords = $db->query("SELECT COUNT(*) FROM mahasiswa WHERE foto_profil IS NOT NULL AND foto_profil != ''")->fetchColumn();

        echo '<h3>📁 File di Folder:</h3>';
        echo '<ul>';
        echo '<li>res/imageUser/: <strong>' . $imageUserCount . ' files (' . formatSize($imageUserSize) . ')</strong></li>';
        echo '<li>res/berkasUser/: <strong>' . $berkasUserCount . ' files (' . formatSize($berkasUserSize) . ')</strong></li>';
        echo '<li>res/profile/: <strong>' . $profileCount . ' files (' . formatSize($profileSize) . ')</strong></li>';
        echo '<li>res/makalahUser/: <strong>' . $makalahCount . ' files (' . formatSize($makalahSize) . ')</strong></li>';
        echo '<li>res/pptUser/: <strong>' . $pptCount . ' files (' . formatSize($pptSize) . ')</strong></li>';
        echo '</ul>';

        echo '<h3>💾 Records di Database:</h3>';
        echo '<ul>';
        echo '<li>berkas_mahasiswa: <strong>' . $berkasRecords . ' records</strong></li>';
        echo '<li>presentasi: <strong>' . $presentasiRecords . ' records</strong></li>';
        echo '<li>mahasiswa (foto_profil): <strong>' . $profileRecords . ' records</strong></li>';
        echo '</ul>';

        echo '</div>';

        // Statistics
        echo '<div class="stats">';
        echo '<div class="stat-box" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">';
        echo '<div class="stat-number">' . $totalFiles . '</div>';
        echo '<div class="stat-label">Total File</div>';
        echo '</div>';
        echo '<div class="stat-box" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">';
        echo '<div class="stat-number">' . formatSize($totalSize) . '</div>';
        echo '<div class="stat-label">Total Ukuran</div>';
        echo '</div>';
        echo '<div class="stat-box" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">';
        echo '<div class="stat-number">' . ($berkasRecords + $presentasiRecords + $profileRecords) . '</div>';
        echo '<div class="stat-label">Database Records</div>';
        echo '</div>';
        echo '</div>';

        if ($totalFiles > 0) {
            echo '<div class="danger-zone">';
            echo '<p><strong>⚠️ PERINGATAN KERAS:</strong></p>';
            echo '<p>Anda akan menghapus <strong>' . $totalFiles . ' file (' . formatSize($totalSize) . ')</strong> dan <strong>' . ($berkasRecords + $presentasiRecords + $profileRecords) . ' database records</strong>!</p>';
            echo '<p><strong>Aksi ini TIDAK BISA DIBATALKAN!</strong></p>';
            echo '<form method="POST" onsubmit="return confirm(\'APAKAH ANDA YAKIN MENGHAPUS SEMUA DATA?\\n\\nTotal: ' . $totalFiles . ' files + ' . ($berkasRecords + $presentasiRecords + $profileRecords) . ' database records\\n\\nAksi ini TIDAK BISA DIBATALKAN!\');">';
            echo '<input type="hidden" name="action" value="cleanup_all">';
            echo '<input type="hidden" name="confirm_all" value="1">';
            echo '<button type="submit" class="danger">🗑️ HAPUS SEMUA DATA (FILES + DATABASE)</button>';
            echo '</form>';
            echo '</div>';
        } else {
            echo '<div class="section" style="background: #d4edda; border-left-color: #28a745;">';
            echo '<h3 class="success">✅ Tidak Ada Data</h3>';
            echo '<p>Semua folder sudah kosong.</p>';
            echo '</div>';
        }

    } catch (Exception $e) {
        echo '<div class="section" style="background: #f8d7da; border-left-color: #dc3545;">';
        echo '<h3 class="error">❌ Error</h3>';
        echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '</div>';
    }
}

function cleanupAllUploads() {
    try {
        $db = \App\Core\Model::getDB();
        $basePath = __DIR__ . DIRECTORY_SEPARATOR . 'res' . DIRECTORY_SEPARATOR;

        echo '<div class="section">';
        echo '<h2>🗑️ Menghapus Semua Data...</h2>';

        $totalDeleted = 0;
        $totalSize = 0;

        // 1. Delete all files from directories
        echo '<h3>📁 Menghapus File dari Folder:</h3>';

        $result = deleteAllFilesInDir($basePath . 'imageUser');
        echo '<p>res/imageUser/: <span class="success">✓ ' . $result['count'] . ' files dihapus (' . formatSize($result['size']) . ')</span></p>';
        $totalDeleted += $result['count'];
        $totalSize += $result['size'];

        $result = deleteAllFilesInDir($basePath . 'berkasUser');
        echo '<p>res/berkasUser/: <span class="success">✓ ' . $result['count'] . ' files dihapus (' . formatSize($result['size']) . ')</span></p>';
        $totalDeleted += $result['count'];
        $totalSize += $result['size'];

        $result = deleteAllFilesInDir($basePath . 'profile');
        echo '<p>res/profile/: <span class="success">✓ ' . $result['count'] . ' files dihapus (' . formatSize($result['size']) . ')</span></p>';
        $totalDeleted += $result['count'];
        $totalSize += $result['size'];

        $result = deleteAllFilesInDir($basePath . 'makalahUser');
        echo '<p>res/makalahUser/: <span class="success">✓ ' . $result['count'] . ' files dihapus (' . formatSize($result['size']) . ')</span></p>';
        $totalDeleted += $result['count'];
        $totalSize += $result['size'];

        $result = deleteAllFilesInDir($basePath . 'pptUser');
        echo '<p>res/pptUser/: <span class="success">✓ ' . $result['count'] . ' files dihapus (' . formatSize($result['size']) . ')</span></p>';
        $totalDeleted += $result['count'];
        $totalSize += $result['size'];

        // 2. Clean database records
        echo '<h3>💾 Menghapus Records dari Database:</h3>';

        $db->beginTransaction();

        // Clear berkas_mahasiswa
        $stmt = $db->query("DELETE FROM berkas_mahasiswa");
        $berkasDeleted = $stmt->rowCount();
        echo '<p>berkas_mahasiswa: <span class="success">✓ ' . $berkasDeleted . ' records dihapus</span></p>';

        // Clear presentasi
        $stmt = $db->query("DELETE FROM presentasi");
        $presentasiDeleted = $stmt->rowCount();
        echo '<p>presentasi: <span class="success">✓ ' . $presentasiDeleted . ' records dihapus</span></p>';

        // Clear foto_profil from mahasiswa
        $stmt = $db->query("UPDATE mahasiswa SET foto_profil = NULL WHERE foto_profil IS NOT NULL");
        $profileUpdated = $stmt->rowCount();
        echo '<p>mahasiswa (foto_profil): <span class="success">✓ ' . $profileUpdated . ' records di-reset</span></p>';

        $db->commit();

        echo '</div>';

        // Final statistics
        echo '<div class="section" style="background: #d4edda; border-left-color: #28a745;">';
        echo '<h3 class="success">✅ Cleanup Selesai!</h3>';
        echo '<p><strong>File dihapus:</strong> ' . $totalDeleted . ' files (' . formatSize($totalSize) . ')</p>';
        echo '<p><strong>Database records dihapus/direset:</strong> ' . ($berkasDeleted + $presentasiDeleted + $profileUpdated) . ' records</p>';
        echo '<p><strong>Space dibebaskan:</strong> ' . formatSize($totalSize) . '</p>';
        echo '</div>';

    } catch (Exception $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }
        echo '<div class="section" style="background: #f8d7da; border-left-color: #dc3545;">';
        echo '<h3 class="error">❌ Error</h3>';
        echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '</div>';
    }
}

function countFilesInDir($dirPath) {
    if (!is_dir($dirPath)) return 0;
    $files = array_diff(scandir($dirPath), ['.', '..']);
    $count = 0;
    foreach ($files as $file) {
        if (is_file($dirPath . DIRECTORY_SEPARATOR . $file)) {
            $count++;
        }
    }
    return $count;
}

function getDirSize($dirPath) {
    if (!is_dir($dirPath)) return 0;
    $size = 0;
    $files = array_diff(scandir($dirPath), ['.', '..']);
    foreach ($files as $file) {
        $filePath = $dirPath . DIRECTORY_SEPARATOR . $file;
        if (is_file($filePath)) {
            $size += filesize($filePath);
        }
    }
    return $size;
}

function deleteAllFilesInDir($dirPath) {
    $count = 0;
    $size = 0;

    if (!is_dir($dirPath)) {
        return ['count' => 0, 'size' => 0];
    }

    $files = array_diff(scandir($dirPath), ['.', '..']);
    foreach ($files as $file) {
        $filePath = $dirPath . DIRECTORY_SEPARATOR . $file;
        if (is_file($filePath)) {
            $fileSize = filesize($filePath);
            if (@unlink($filePath)) {
                $count++;
                $size += $fileSize;
                error_log("File dihapus (clean all): " . $filePath);
            } else {
                error_log("Gagal menghapus file (clean all): " . $filePath);
            }
        }
    }
    return ['count' => $count, 'size' => $size];
}

?>

    <div style="text-align: center; margin-top: 30px; padding-top: 20px; border-top: 2px solid #ddd;">
        <p style="color: #666;">
            <strong>💡 Tips:</strong><br>
            • Tab "Clean Orphan Files": Hapus file yang tidak ada di database (aman)<br>
            • Tab "Clean All Uploads": Reset semua data upload + database (DANGER - untuk development/testing)
        </p>
    </div>
</div>
</body>
</html>
