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
            $fallbackFilename = "../app/View/Templates/" . $view . ".php";
            if (file_exists($fallbackFilename)) {
                if (!empty($data)) {
                    extract($data);
                }
                require $fallbackFilename;
            } else {
                redirect('shared/error');
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
