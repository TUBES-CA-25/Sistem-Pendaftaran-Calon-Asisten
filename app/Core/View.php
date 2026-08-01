<?php
namespace App\Core;

class View {
    /**
     * Render view dengan folder yang lebih fleksibel
     * @param string $view - Nama view file (tanpa .php)
     * @param string $folder - Folder view (admin, user, layouts, templates, auth, exam)
     * @param array $data - Data untuk view
     */
    public static function render($view, $folder, $data = []) {
        $filename = "../app/View/" . $folder . "/" . $view . ".php";

        if (file_exists($filename)) {
            if (!empty($data)) {
                extract($data);
            }
            require $filename;
        } else {
            // Fallback untuk backward compatibility
            // Catatan: folder ditulis huruf kecil "templates" agar cocok dengan
            // nama folder sebenarnya. Di server Linux (case-sensitive) penulisan
            // "Templates" menyebabkan file tidak ketemu.
            $fallbackFilename = "../app/View/templates/" . $view . ".php";
            if (file_exists($fallbackFilename)) {
                if (!empty($data)) {
                    extract($data);
                }
                require $fallbackFilename;
            } else {
                // Dulu redirect ke '/shared/error' yang TIDAK punya route,
                // sehingga jatuh ke catch-all '/{page}' dan berpotensi
                // memicu redirect beruntun. Sekarang tampilkan 404 langsung.
                http_response_code(404);
                $notFound = "../app/View/user/ujian/error.php";
                $message = "Halaman tidak ditemukan.";
                if (file_exists($notFound)) {
                    require $notFound;
                } else {
                    echo "<h1>404 - Halaman tidak ditemukan</h1>";
                }
                exit();
            }
        }
    }

    /**
     * Include partial/component
     * @param string $component - Nama component
     * @param array $data - Data untuk component
     */
    public static function component($component, $data = []) {
        $filename = "../app/View/templates/components/" . $component . ".php";

        if (file_exists($filename)) {
            if (!empty($data)) {
                extract($data);
            }
            require $filename;
        }
    }
}
