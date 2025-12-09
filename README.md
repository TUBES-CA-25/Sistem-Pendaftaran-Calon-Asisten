# Dokumentasi Proyek Registrasi CCA

Dokumen ini menjelaskan struktur direktori dan fungsi file dalam proyek aplikasi web berbasis **PHP Native MVC** ini.

## 📂 Struktur Direktori Utama

### 1. `app/` (Aplikasi Inti)
Folder ini adalah jantung dari aplikasi yang menggunakan pola desain **MVC (Model-View-Controller)**. Semua logika bisnis, interaksi database, dan tampilan antarmuka berada di sini.

*   **`Controllers/`**: Berisi kelas-kelas Controller yang bertugas menerima permintaan dari pengguna, memproses logika, dan memanggil Model atau View yang sesuai. Direktori ini dikelompokkan berdasarkan fitur:
    *   `exam/`: Logika untuk ujian (Soal, Jawaban, Penilaian).
    *   `Home/`: Halaman utama aplikasi.
    *   `Login/`: Autentikasi pengguna (Login, Logout, Register).
    *   `notifications/`: Mengatur notifikasi sistem.
    *   `presentasi/`: Jadwal dan manajemen presentasi.
    *   `Profile/`: Manajemen profil pengguna.
    *   `user/`: Fitur terkait pengguna (Dashboard, Absensi, Biodata, dll).
    *   `utils/`: Fungsi-fungsi utilitas pendukung.

*   **`core/`**: Berisi file-file inti framework PHP Native yang kita bangun:
    *   `App.php`: Kelas utama untuk routing URL dan bootstrap aplikasi.
    *   `Controller.php`: Controller dasar yang diwarisi oleh semua controller lain.
    *   `Database.php`: Wrapper untuk koneksi dan query database menggunakan PDO.
    *   `Env.php`, `functions.php`, `Model.php`, `Router.php`, `View.php`: Komponen pendukung framework.

*   **`Model/`**: Berisi kelas-kelas yang berinteraksi langsung dengan database. Merepresentasikan data aplikasi.
    *   Kelompok folder: `exam`, `presentasi`, `User`, `wawancara`.

*   **`View/`**: Berisi file template (biasanya `.php` dengan HTML) yang akan ditampilkan ke pengguna.
    *   `Templates/`: Potongan layout yang bisa digunakan kembali (Header, Footer, Sidebar, dll).

### 2. `config/` (Konfigurasi)
Berisi file konfigurasi aplikasi.
*   `config.php`: Memuat konstanta global dan pengaturan dasar (seperti Base URL dan kredensial Database yan diambil dari `.env`).
*   `App.php`, `Database.php`: Konfigurasi spesifik modul.

### 3. `public/` (Akses Publik)
Satu-satunya folder yang boleh diakses langsung oleh browser/pengguna. Ini meningkatkan keamanan karena kode inti (`app/`) tidak bisa diakses langsung via URL.
*   **`index.php`**: **Entry Point** (Pintu Masuk) utama aplikasi. Semua request akan diarahkan ke file ini.
*   **`.htaccess`**: Konfigurasi server Apache untuk mengarahkan URL cantik (pretty URL) ke `index.php`.
*   **`Assets/`**: Menyimpan file statis seperti:
    *   `Style/`: File CSS.
    *   `Script/`: File JavaScript.
    *   `Img/`, `icon/`, `gif/`: Gambar dan ikon.
*   **`UML/`**: Dokumen diagram UML sistem.

### 4. `res/` (Sumber Daya Pengguna)
Folder penyimpanan file yang diunggah oleh pengguna atau file sistem yang bersifat statis namun bukan aset publik web.
*   `berkasUser`: Dokumen yang diupload user.
*   `imageUser`: Foto profil atau gambar user.
*   `makalahUser`, `pptUser`: File presentasi dan makalah.

### 5. `routes/`
Tempat mendefinisikan rute (routes) kustom jika diperlukan, meskipun routing utama biasanya ditangani secara otomatis oleh `App.php` berdasarkan Controller/Method.

---

## 📄 File Penting Lainnya

*   **`.env`**: File konfigurasi Environment. Simpan kredensial rahasia di sini (seperti username/password database). **Jangan pernah push file ini ke repository publik!**
*   **`.gitignore`**: Daftar file dan folder yang diabaikan oleh Git (tidak akan di-upload ke repository), seperti `.env` dan folder `vendor/` atau file upload user.

## 🚀 Cara Menjalankan

1.  Pastikan Web Server (Apache) dan MySQL sudah berjalan (misal via XAMPP).
2.  Atur konfigurasi database di file `.env`.
3.  Akses aplikasi melalui browser:
    *   Jika menggunakan Virtual Host/XAMPP: `http://localhost/registrasi-cca/public`
    *   Atau jalankan server PHP bawaan di folder `public`:
        ```bash
        cd public
        php -S localhost:8080
        ```
        Lalu buka `http://localhost:8080`.
