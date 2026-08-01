<?php

namespace App\Core;
use BadMethodCallException;
abstract class Controller {
    public function view($view, $data = []) {
        $viewFile = "../app/View/" . $view . ".php";
        if (file_exists($viewFile)) {
            if (!empty($data)) {
                extract($data);
            }
            require $viewFile;
        } else {
            // Optional: fallback or error
            // echo "View not found: " . $viewFile;
            throw new \Exception("View not found: " . $viewFile);
        }
    }

    public function __call($name, $arguments) {
        throw new BadMethodCallException (sprintf(
            'Method "%s" is not Implemented in class "%s" .',
            $name,
            get_class($this)
        ));
    }

    /* ================================================================
       Helper respons JSON & autentikasi.

       Sebelum ini tidak ada tempat berbagi: 336 `echo json_encode`,
       176 header Content-Type, dan 45 pemeriksaan sesi tersebar di
       26 controller, masing-masing dengan ejaan sendiri.

       CATATAN KOMPATIBILITAS: bentuk respons sengaja mempertahankan
       DUA kunci sekaligus, `status` (dipakai 339x di project) dan
       `success` (dipakai front-end lama seperti exam.js). Mengubah
       salah satunya akan mematahkan pemanggil yang sudah ada.
       ================================================================ */

    /* ----------------------------------------------------------------
       Kenapa helper JSON ini `static`?

       Sejumlah handler di project ini dideklarasikan `public static function`
       (mis. PesertaController::getDetailPeserta, DashboardController::
       getActivities) tetapi didaftarkan di Routes sebagai [new Controller,
       'method']. call_user_func() TIDAK mengikat $this pada method static,
       sehingga `$this->jsonError(...)` di dalamnya melempar
       Error: "Using $this when not in object context" — fatal.

       Helper ini tidak menyentuh state instance sama sekali (hanya
       superglobal + output), jadi menjadikannya static aman.

       KONVENSI: panggil selalu dengan `self::jsonError(...)`, bukan
       `$this->jsonError(...)`. Keduanya berjalan sama di runtime, tetapi
       bentuk `$this->` pada method static ditandai peringatan oleh IDE
       dan static analyzer. Seluruh controller sudah diseragamkan ke self::.
       ---------------------------------------------------------------- */

    /**
     * Kirim respons JSON lalu hentikan eksekusi.
     * Membersihkan output buffer lebih dulu supaya warning/echo liar
     * tidak merusak JSON (dulu ditangani ad-hoc dengan 5 ejaan ob_* berbeda).
     */
    protected static function json(array $payload, int $code = 200): void
    {
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($payload);
        exit();
    }

    /** Respons sukses. $data digabung ke root agar kompatibel dengan pemanggil lama. */
    protected static function jsonSuccess($data = [], string $message = '', int $code = 200): void
    {
        $payload = [
            'success' => true,
            'status'  => 'success',
        ];
        if ($message !== '') {
            $payload['message'] = $message;
        }
        if (is_array($data) && $data !== []) {
            $payload = array_merge($payload, $data);
        } elseif (!is_array($data) && $data !== null) {
            $payload['data'] = $data;
        }
        self::json($payload, $code);
    }

    /** Respons gagal. Default 400 (bukan 500) karena mayoritas kegagalan di sini adalah validasi. */
    protected static function jsonError(string $message, int $code = 400, array $extra = []): void
    {
        self::json(array_merge([
            'success' => false,
            'status'  => 'error',
            'message' => $message,
        ], $extra), $code);
    }

    /** Pastikan sesi sudah aktif tanpa memulai ulang sesi yang berjalan. */
    protected static function ensureSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /** Id user yang sedang login, atau null bila belum login. */
    protected static function currentUserId(): ?int
    {
        self::ensureSession();
        return isset($_SESSION['user']['id']) ? (int) $_SESSION['user']['id'] : null;
    }

    /**
     * Wajib login. Mengembalikan id user, atau mengirim 401 lalu berhenti.
     * Menggantikan blok `if (!isset($_SESSION['user']['id'])) throw ...` yang berulang.
     */
    protected static function requireAuth(): int
    {
        $id = self::currentUserId();
        if ($id === null) {
            self::jsonError('User tidak terautentikasi', 401);
        }
        return $id;
    }
}