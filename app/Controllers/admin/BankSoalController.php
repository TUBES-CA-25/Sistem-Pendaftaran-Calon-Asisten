<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Model\SoalExam;
use App\Model\BankSoal;

class BankSoalController extends Controller
{
    /** Ekstensi gambar yang diterima untuk lampiran soal. */
    private const ALLOWED_IMAGE_EXT = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    /** Folder tujuan unggahan gambar soal (absolut, di luar webroot app). */
    private function soalUploadDir(): string
    {
        $projectRoot = dirname(dirname(dirname(__DIR__)));
        return $projectRoot . DIRECTORY_SEPARATOR . 'res' . DIRECTORY_SEPARATOR
             . 'uploads' . DIRECTORY_SEPARATOR . 'soal' . DIRECTORY_SEPARATOR;
    }

    /**
     * Simpan satu gambar soal dari $_FILES.
     * Menggantikan blok unggah yang sebelumnya disalin di saveSoal() dan
     * updateSoal() dengan isi nyaris identik.
     *
     * @return string|null path relatif untuk disimpan ke DB, null bila tidak ada
     *                     file / ekstensi ditolak / gagal dipindahkan.
     */
    private function storeSoalImage(string $field = 'soal_image'): ?string
    {
        if (!isset($_FILES[$field]) || $_FILES[$field]['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $ext = strtolower(pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, self::ALLOWED_IMAGE_EXT, true)) {
            error_log("BankSoal: ekstensi gambar ditolak: {$ext}");
            return null;
        }

        $uploadDir = $this->soalUploadDir();
        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true) && !is_dir($uploadDir)) {
            error_log("BankSoal: gagal membuat folder unggahan: {$uploadDir}");
            return null;
        }

        $newFilename = uniqid('soal_') . '.' . $ext;
        if (!move_uploaded_file($_FILES[$field]['tmp_name'], $uploadDir . $newFilename)) {
            error_log("BankSoal: gagal memindahkan berkas unggahan ke {$uploadDir}{$newFilename}");
            return null;
        }

        return 'res/uploads/soal/' . $newFilename;
    }

    /** Hapus berkas gambar soal berdasarkan path relatif yang tersimpan di DB. */
    private function deleteSoalImage(?string $relativePath): void
    {
        if (empty($relativePath)) {
            return;
        }
        $full = dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR
              . str_replace('/', DIRECTORY_SEPARATOR, $relativePath);
        if (is_file($full)) {
            @unlink($full);
        }
    }

    public function saveSoal()
    {
        try {
            self::requireAuth();

            $deskripsi = $_POST['deskripsi'] ?? '';
            $tipeJawaban = $_POST['status_soal'] ?? $_POST['tipeJawaban'] ?? '';
            $pilihan = $_POST['pilihan'] ?? 'bukan soal pilihan';
            $jawaban = $_POST['jawaban'] ?? null;
            $bankId = $_POST['bank_id'] ?? null;

            $imageUrl = $this->storeSoalImage();

            if (empty($deskripsi)) {
                throw new \Exception('Deskripsi soal harus diisi');
            }

            if (empty($bankId)) {
                throw new \Exception('Bank soal tidak ditemukan');
            }

            $soalExam = new SoalExam(
                $deskripsi,
                $pilihan,
                $jawaban,
                $tipeJawaban,
                $imageUrl
            );

            if ($soalExam->getJawaban() === null) {
                $soalExam->saveWithoutAnswer($soalExam, $bankId);
            } else {
                $soalExam->save($soalExam, $bankId);
            }

            self::jsonSuccess([], 'Soal berhasil disimpan');

        } catch (\Exception $e) {
            error_log("Error in saveSoal: " . $e->getMessage());
            self::jsonError($e->getMessage(), 500);
        }
    }

    public function updateSoal()
    {
        try {
            self::requireAuth();

            $id = $_POST['id'] ?? null;
            $deskripsi = $_POST['deskripsi'] ?? '';
            $tipeJawaban = $_POST['status_soal'] ?? '';
            $pilihan = $_POST['pilihan'] ?? 'bukan soal pilihan';
            $jawaban = $_POST['jawaban'] ?? null;
            $existingImage = $_POST['existing_image'] ?? null;
            $imageUrl = $existingImage; // pertahankan gambar lama secara default

            if (!$id) {
                throw new \Exception('ID soal tidak ditemukan');
            }

            // Gambar baru diunggah -> pakai yang baru, buang yang lama
            $uploaded = $this->storeSoalImage('soal_image_edit');
            if ($uploaded !== null) {
                $this->deleteSoalImage($existingImage);
                $imageUrl = $uploaded;
            }

            if (empty($deskripsi)) {
                throw new \Exception('Deskripsi soal harus diisi');
            }

            // Akses data lewat Model (dulu controller menulis SQL UPDATE sendiri)
            $soal = new SoalExam($deskripsi, $pilihan, $jawaban, $tipeJawaban, $imageUrl);
            if (!$soal->updateSoalWithImage($id, $soal, $imageUrl)) {
                throw new \Exception('Gagal mengupdate soal');
            }

            self::jsonSuccess([], 'Soal berhasil diupdate');

        } catch (\Exception $e) {
            error_log("Error in updateSoal: " . $e->getMessage());
            self::jsonError($e->getMessage(), 500);
        }
    }

    public function uploadImage()
    {
        try {
            self::requireAuth();

            if (!isset($_FILES['image'])) {
                throw new \Exception('No image uploaded');
            }

            $file = $_FILES['image'];
            if ($file['error'] !== UPLOAD_ERR_OK) {
                throw new \Exception('Upload error: ' . $file['error']);
            }

            // Validate type
            $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->file($file['tmp_name']);
            
            if (!in_array($mime, $allowed)) {
                throw new \Exception('Invalid file type. Only JPG, PNG, GIF, WEBP allowed.');
            }

            // Create directory if not exists
            $uploadDir = __DIR__ . '/../../../res/uploads/soal_content/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            // Generate filename
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $newFilename = 'img_' . time() . '_' . uniqid() . '.' . $ext;
            $destPath = $uploadDir . $newFilename;

            if (!move_uploaded_file($file['tmp_name'], $destPath)) {
                throw new \Exception('Failed to move uploaded file');
            }

            // PENTING: bentuk respons {data:{filePath}} / {error} adalah kontrak
            // unggah gambar EasyMDE. Sengaja TIDAK memakai jsonSuccess()/jsonError()
            // agar editor markdown tetap mengenali baliknya.
            self::json(['data' => ['filePath' => 'res/uploads/soal_content/' . $newFilename]]);

        } catch (\Exception $e) {
            self::json(['error' => $e->getMessage()], 400);
        }
    }

    public function getBankQuestions()
    {
        try {
            self::requireAuth();

            $bankId = $_POST['bank_id'] ?? null;
            
            if (!$bankId) {
                throw new \Exception('Bank ID tidak ditemukan');
            }

            $soalExam = new SoalExam();
            $questions = $soalExam->getSoalByBankId($bankId);
            
            self::jsonSuccess(['data' => $questions]);
            
        } catch (\Exception $e) {
            self::jsonError($e->getMessage());
        }
        exit;
    }

    public function getBankQuestionsHtml()
    {
        try {
            self::requireAuth();

            $bankId = $_POST['bank_id'] ?? null;

            // Pesan polos: markup untuk tampilan disusun di blok catch, bukan
            // diselipkan ke dalam pesan exception seperti sebelumnya.
            if (!$bankId) {
                throw new \Exception('Bank ID tidak ditemukan');
            }

            $soalExam = new SoalExam();
            $soalArray = $soalExam->getSoalByBankId($bankId);
            
            // Pass variable and include view
            ob_start();
            include __DIR__ . '/../../View/admin/bank-soal/partials/soal_list.php';
            $html = ob_get_clean();
            
            self::jsonSuccess([
                'html' => $html,
                'data' => $soalArray // also pass data for things like question count update
            ]);

        } catch (\Exception $e) {
            // Endpoint ini dipakai untuk menyuntik HTML, jadi balasan error pun
            // menyertakan 'html' agar pemanggil bisa menampilkannya apa adanya.
            self::jsonError($e->getMessage(), 400, [
                'html' => '<div class="text-red-500 font-bold p-4 text-center">' . htmlspecialchars($e->getMessage()) . '</div>'
            ]);
        }
    }

    public function deleteSoal()
    {
        try {
            self::requireAuth();

            $soalId = $_POST['id'] ?? null;

            if (!$soalId) {
                throw new \Exception('ID soal tidak ditemukan');
            }

            // Akses data lewat Model (dulu controller menjalankan SELECT + DELETE sendiri)
            $soalExam = new SoalExam();
            $imageUrl = $soalExam->getImageUrlById($soalId);

            // Buang berkas gambarnya bila ada, lalu hapus barisnya
            $this->deleteSoalImage($imageUrl);

            if (!$soalExam->deleteSoal($soalId)) {
                throw new \Exception('Gagal menghapus soal');
            }

            self::jsonSuccess([], 'Soal berhasil dihapus');

        } catch (\Exception $e) {
            error_log("Error in deleteSoal: " . $e->getMessage());
            self::jsonError($e->getMessage());
        }
    }

    public function getBankDetails()
    {
        try {
            // Sebelumnya method ini TIDAK memeriksa autentikasi sama sekali
            // padahal mengembalikan data bank soal.
            self::requireAuth();

            $bankId = $_GET['id'] ?? null;

            if (!$bankId) {
                throw new \Exception('Bank ID tidak ditemukan');
            }

            $bankSoal = new BankSoal();
            $bank = $bankSoal->getBankById($bankId);

            if (!$bank) {
                throw new \Exception('Bank soal tidak ditemukan');
            }

            self::jsonSuccess([
                'bank' => [
                    'id' => $bank['id'],
                    'nama' => $bank['nama'],
                    'jumlah_soal' => $bank['jumlah_soal'] ?? 0,
                    'jumlah_pg' => $bank['jumlah_pg'] ?? 0,
                    'jumlah_essay' => $bank['jumlah_essay'] ?? 0
                ]
            ]);

        } catch (\Exception $e) {
            self::jsonError($e->getMessage());
        }
    }

    public function createBank()
    {
        try {
            self::requireAuth();
            
            $nama = $_POST['nama'] ?? '';
            $deskripsi = $_POST['deskripsi'] ?? '';
            $token = $_POST['token'] ?? '';
            $durasi = isset($_POST['durasi']) ? (int)$_POST['durasi'] : 45;
            $poin_per_soal = isset($_POST['poin_per_soal']) ? (int)$_POST['poin_per_soal'] : 10;
            
            if (empty($nama)) throw new \Exception('Nama bank soal wajib diisi');
            
            $bankSoal = new BankSoal();
            if ($bankSoal->save($nama, $deskripsi, $token, $durasi, $poin_per_soal)) {
                $newId = $bankSoal->getLastInsertId();
                self::jsonSuccess(['id' => $newId], 'Bank soal berhasil dibuat');
            } else {
                throw new \Exception('Gagal menyimpan bank soal');
            }
        } catch (\Exception $e) {
            self::jsonError($e->getMessage());
        }
    }

    public function updateBank()
    {
        try {
            self::requireAuth();
            
            $id = $_POST['id'] ?? null;
            $nama = $_POST['nama'] ?? '';
            $deskripsi = $_POST['deskripsi'] ?? '';
            $token = $_POST['token'] ?? '';
            $durasi = isset($_POST['durasi']) ? (int)$_POST['durasi'] : 45;
            $poin_per_soal = isset($_POST['poin_per_soal']) ? (int)$_POST['poin_per_soal'] : 10;
            
            if (!$id) throw new \Exception('ID Bank tidak valid');
            if (empty($nama)) throw new \Exception('Nama bank soal wajib diisi');
            
            $bankSoal = new BankSoal();
            if ($bankSoal->updateBank($id, $nama, $deskripsi, $token, $durasi, $poin_per_soal)) {
                self::jsonSuccess([], 'Bank soal berhasil diperbarui');
            } else {
                throw new \Exception('Gagal memperbarui bank soal');
            }
        } catch (\Exception $e) {
            self::jsonError($e->getMessage());
        }
    }

    public function deleteBank()
    {
        try {
            self::requireAuth();
            
            $id = $_POST['id'] ?? null;
            if (!$id) throw new \Exception('ID Bank tidak valid');
            
            $bankSoal = new BankSoal();
            if ($bankSoal->deleteBank($id)) {
                self::jsonSuccess([], 'Bank soal berhasil dihapus');
            } else {
                throw new \Exception('Gagal menghapus bank soal');
            }
        } catch (\Exception $e) {
            self::jsonError($e->getMessage());
        }
    }

    public function activateBank()
    {
        try {
            self::requireAuth();
            
            $id = $_POST['id'] ?? $_POST['bank_id'] ?? null;
            if (!$id) throw new \Exception('ID Bank tidak valid');
            
            $bankSoal = new BankSoal();
            if ($bankSoal->setActiveBank($id)) {
                self::jsonSuccess([], 'Bank soal berhasil diaktifkan');
            } else {
                throw new \Exception('Gagal mengaktifkan bank soal');
            }
        } catch (\Exception $e) {
            self::jsonError($e->getMessage());
        }
    }

    public function deactivateBank()
    {
        try {
            self::requireAuth();
            
            $id = $_POST['id'] ?? $_POST['bank_id'] ?? null;
            if (!$id) throw new \Exception('ID Bank tidak valid');
            
            $bankSoal = new BankSoal();
            if ($bankSoal->deactivateBank($id)) {
                self::jsonSuccess([], 'Bank soal berhasil dinonaktifkan');
            } else {
                throw new \Exception('Gagal menonaktifkan bank soal');
            }
        } catch (\Exception $e) {
            self::jsonError($e->getMessage());
        }
    }
}
