# IC-ASSIST - Sistem Pendaftaran Calon Asisten Laboratorium

Sistem informasi berbasis web untuk mengelola proses pendaftaran dan seleksi calon asisten laboratorium ICLABS menggunakan arsitektur MVC (Model-View-Controller) dengan PHP.

---

## Struktur Direktori

```
Sistem-Pendaftaran-Calon-Asisten/
├── app/                    # Kode aplikasi utama (MVC, config, business logic)
├── public/                 # Entry point web dan aset statis
├── res/                    # Resource storage untuk file upload user
├── .env                    # Konfigurasi environment
└── .gitignore             # Git ignore rules
```

---

## 1. Direktori `app/`

### 1.1 `app/core/`
Framework MVC custom berisi komponen inti: routing, controller base, model base, database connection, view rendering, environment handler, dan autoloader.

### 1.2 `app/config/`
Konfigurasi aplikasi, database, dan definisi routes.

### 1.3 `app/Controllers/`
Controllers diorganisir berdasarkan role:
- **Root** - Authentication, home routing, notifikasi
- **admin/** - Controllers untuk fitur admin (dashboard, peserta, ruangan, soal, jadwal, nilai, kehadiran)
- **user/** - Controllers untuk fitur user (dashboard, biodata, berkas, ujian, presentasi, profil, jadwal)

### 1.4 `app/Model/`
Models untuk interaksi dengan database, mencakup user, mahasiswa, biodata, berkas, ujian, presentasi, wawancara, nilai, ruangan, dan notifikasi.

### 1.5 `app/View/`

**Struktur View:**
```
View/
├── layouts/            # Layout utama (main.php, mainAdmin.php)
├── user/               # Halaman user (dashboard, biodata, berkas, ujian, presentasi, wawancara, profil, notifikasi, pengumuman)
├── admin/              # Halaman admin (dashboard, peserta, ruangan, ujian, presentasi, wawancara, kehadiran, nilai, profil)
├── auth/               # Login dan registrasi
├── Home/               # Landing page
└── Templates/          # Komponen reusable (sidebar, components)
```

---

## 2. Direktori `public/`

Entry point aplikasi yang dapat diakses web.

**Struktur:**
```
public/
├── index.php          # Entry point dengan security headers
├── .htaccess          # URL rewriting
└── Assets/
    ├── css/           # Stylesheet (style, theme, exam, login-animation)
    ├── js/            # JavaScript (app, dashboard, exam, participants, rooms, dll)
    ├── Img/           # Gambar, icon, avatar
    ├── Downloads/     # Template file download
    ├── gif/           # Animated feedback
    └── UML/           # Diagram UML
```

---

## 3. Direktori `res/` - Resource Storage

```
res/
├── berkasUser/              # Dokumen user (CV, transkrip)
├── imageUser/               # Gambar profil
├── makalahUser/             # File makalah
├── pptUser/                 # File presentasi
├── profile/                 # Foto profil
└── uploads/
    └── soal_content/        # Konten soal ujian
```

---

## Instalasi

1. **Clone Repository**
   ```bash
   git clone <repository-url>
   cd Sistem-Pendaftaran-Calon-Asisten
   ```

2. **Setup Web Server**
   ```bash
   # XAMPP Windows
   C:\xampp\htdocs\Sistem-Pendaftaran-Calon-Asisten
   ```

3. **Konfigurasi Environment**
   ```bash
   cp .env.example .env
   # Edit .env sesuai konfigurasi database
   ```

4. **Set Permission Folder**
   ```bash
   chmod -R 755 res/
   ```

5. **Akses Aplikasi**
   ```
   http://localhost/Sistem-Pendaftaran-Calon-Asisten/public
   ```

