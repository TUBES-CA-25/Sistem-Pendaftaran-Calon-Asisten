# Dynamic Environment Path Refactoring Report

This document outlines the codebase changes made to replace hardcoded paths with dynamic environment variables defined in [.env](file:///c:/xampp/htdocs/tubes_web/.env).

## 1. Javascript Files Configuration
The following files now rely on global constants (`APP_URL`, `BASE_URL`, `RES_PATH`) instead of hardcoded strings.

| Javascript File | Previous Hardcoded Path | Refactored Dynamic Path |
| :--- | :--- | :--- |
| **[common.js](file:///c:/xampp/htdocs/tubes_web/public/Assets/Script/common.js)** | `/Sistem-Pendaftaran-Calon-Asisten/res/imageUser` | `RES_PATH + '/imageUser'` |
| | `/Sistem-Pendaftaran-Calon-Asisten/res/berkasUser` | `RES_PATH + '/berkasUser'` |
| | `/Sistem-Pendaftaran-Calon-Asisten/public/Assets/gif` | `BASE_URL + '/Assets/gif'` |
| **[presentasi.js](file:///c:/xampp/htdocs/tubes_web/public/Assets/Script/user/presentasi.js)** | `/Sistem-Pendaftaran-Calon-Asisten/public/judul` | `APP_URL + '/judul'` |
| | `/Sistem-Pendaftaran-Calon-Asisten/public/presentasi` | `APP_URL + '/presentasi'` |
| **[biodata.js](file:///c:/xampp/htdocs/tubes_web/public/Assets/Script/user/biodata.js)** | `/Sistem-Pendaftaran-Calon-Asisten/public/logout` | `APP_URL + '/logout'` |
| **[berkas.js](file:///c:/xampp/htdocs/tubes_web/public/Assets/Script/user/berkas.js)** | `/Sistem-Pendaftaran-Calon-Asisten/public/berkas` | `APP_URL + '/berkas'` |
| **[ScriptLogin.js](file:///c:/xampp/htdocs/tubes_web/public/Assets/Script/Login/ScriptLogin.js)** | `/Sistem-Pendaftaran-Calon-Asisten/public/register/authenticate` | `APP_URL + '/register/authenticate'` |
| | `/Sistem-Pendaftaran-Calon-Asisten/public/login/authenticate` | `APP_URL + '/login/authenticate'` |
| **[examScript.js](file:///c:/xampp/htdocs/tubes_web/public/Assets/Script/exam/examScript.js)** | `/Sistem-Pendaftaran-Calon-Asisten/public/hasil` | `APP_URL + '/hasil'` |
| | `/Sistem-Pendaftaran-Calon-Asisten/public/calculate` | `APP_URL + '/calculate'` |

## 2. PHP View Templates
These files now use PHP constants (`<?= BASE_URL ?>`, `<?= RES_PATH ?>`) for asset loading.

| PHP View File | Content Type | New Implementation |
| :--- | :--- | :--- |
| **[DaftarHadirPesertaAdmin.php](file:///c:/xampp/htdocs/tubes_web/app/View/Templates/DaftarHadirPesertaAdmin.php)** | Status GIFs | `<?= BASE_URL ?>/Assets/gif/...` |
| **[DaftarNilaiTesTertulisAdmin.php](file:///c:/xampp/htdocs/tubes_web/app/View/Templates/DaftarNilaiTesTertulisAdmin.php)** | Status GIFs | `<?= BASE_URL ?>/Assets/gif/...` |
| **[daftarPeserta.php](file:///c:/xampp/htdocs/tubes_web/app/View/Templates/daftarPeserta.php)** | Edit/Delete Icons | `<?= BASE_URL ?>/Assets/Img/...` |
| | Script Includes | `<?= BASE_URL ?>/Assets/Script/...` |
| **[biodata.php](file:///c:/xampp/htdocs/tubes_web/app/View/Templates/biodata.php)** | Script Includes | `<?= BASE_URL ?>/Assets/Script/user/biodata.js` |
| **[exam/index.php](file:///c:/xampp/htdocs/tubes_web/app/View/exam/index.php)** | User Photo | `RES_PATH . "/imageUser/..."` |
| | Favicon | `<?= BASE_URL ?>/Assets/Img/iclabs.png` |

## 3. Global Configuration (Layout Files)
To bridge PHP environment variables to Javascript, the following global constants were added to [main.php](file:///c:/xampp/htdocs/tubes_web/app/View/Templates/main.php), [mainAdmin.php](file:///c:/xampp/htdocs/tubes_web/app/View/Templates/mainAdmin.php), and [login/index.php](file:///c:/xampp/htdocs/tubes_web/app/View/login/index.php):

```html
<script> 
    const APP_URL = '<?php echo APP_URL; ?>';       // e.g. http://localhost/tubes_web/public
    const BASE_URL = '<?php echo BASE_URL; ?>'; // e.g. /tubes_web/public
    const RES_PATH = '<?php echo RES_PATH; ?>';       // e.g. /tubes_web/res
</script>
```

## Benefits
*   **Portability**: Project can be renamed or moved without breaking paths.
*   **Maintainability**: Path changes only need to be updated in [.env](file:///c:/xampp/htdocs/tubes_web/.env).
*   **Consistency**: Frontend and Backend share the same configuration source.
