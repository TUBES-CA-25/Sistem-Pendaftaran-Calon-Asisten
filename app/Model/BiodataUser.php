<?php

namespace App\Model;
use App\Core\Model;
use PDO;
class BiodataUser extends Model
{
    protected static $table = 'mahasiswa';
    protected static $tabelJurusan = 'jurusan';
    protected static $tabelKelas = 'kelas';
    protected $id;
    protected $idUser;
    protected $idJurusan;
    protected $jurusan;
    protected $kelas;
    protected $stambuk;
    protected $idKelas;
    protected $namaLengkap;
    protected $alamat;
    protected $jenisKelamin;
    protected $tempatLahir;
    protected $tanggalLahir;
    protected $noHp;

    public function __construct(
        $idUser = null,
        $jurusan = null,
        $stambuk = null,
        $kelas = null,
        $namaLengkap = null,
        $alamat = null,
        $jenisKelamin = null,
        $tempatLahir = null,
        $tanggalLahir = null,
        $noHp = null
    ) {
        if ($stambuk == null) {
            $this->idUser = $idUser;
            $this->jurusan = $jurusan;
            $this->kelas = $kelas;
            $this->namaLengkap = $namaLengkap;
            $this->alamat = $alamat;
            $this->jenisKelamin = $jenisKelamin;
            $this->tempatLahir = $tempatLahir;
            $this->tanggalLahir = $tanggalLahir;
            $this->noHp = $noHp;
        } else {
            $this->idUser = $idUser;
            $this->jurusan = $jurusan;
            $this->stambuk = $stambuk;
            $this->kelas = $kelas;
            $this->namaLengkap = $namaLengkap;
            $this->alamat = $alamat;
            $this->jenisKelamin = $jenisKelamin;
            $this->tempatLahir = $tempatLahir;
            $this->tanggalLahir = $tanggalLahir;
            $this->noHp = $noHp;
        }
    }

    public function save(BiodataUser $biodata)
    {
        try {
            $query = "INSERT INTO " . static::$table . " 
                (id_user, id_jurusan, stambuk, id_kelas, nama_lengkap, alamat, jenis_kelamin, tempat_lahir, tanggal_lahir, no_telp) 
                VALUES 
                (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = self::getDB()->prepare($query);

            // Ambil ID Jurusan
            $idJurusanData = $this->getIdJurusan($biodata->jurusan);
            if (!$idJurusanData) {
                error_log("Error: Jurusan tidak ditemukan - " . $biodata->jurusan);
                return false;
            }
            $biodata->idJurusan = $idJurusanData['id'];

            // Ambil ID Kelas
            $jenisKelamin = ucfirst($biodata->jenisKelamin);
            $idKelasData = $this->getIdKelas($biodata->kelas);
            if (!$idKelasData) {
                error_log("Error: Kelas tidak ditemukan - " . $biodata->kelas);
                return false;
            }
            $biodata->idKelas = $idKelasData['id']; // Pastikan variabel ini konsisten

            // Binding Parameter
            $stmt->bindParam(1, $biodata->idUser);
            $stmt->bindParam(2, $biodata->idJurusan);
            $stmt->bindParam(3, $biodata->stambuk);
            $stmt->bindParam(4, $biodata->idKelas);
            $stmt->bindParam(5, $biodata->namaLengkap);
            $stmt->bindParam(6, $biodata->alamat);
            $stmt->bindParam(7, $jenisKelamin);
            $stmt->bindParam(8, $biodata->tempatLahir);
            $stmt->bindParam(9, $biodata->tanggalLahir);
            $stmt->bindParam(10, $biodata->noHp);

            // Eksekusi Query
            if ($stmt->execute()) {
                return true; // Berhasil
            } else {
                error_log("Error: Gagal menyimpan biodata");
                return false; // Gagal
            }
        } catch (\PDOException $e) {
            error_log("SQL Error: " . $e->getMessage());
            return false;
        }
    }
    private function getIdJurusan($namaJurusan)
    {
        $query = "SELECT id FROM " . static::$tabelJurusan . " WHERE nama = ?";
        $stmt = self::getDB()->prepare($query);
        $stmt->bindParam(1, $namaJurusan);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $result = [
            "id" => $result['id']
        ];
        return $result;
    }
    private function getIdKelas($namaKelas)
    {
        $query = "SELECT id FROM " . static::$tabelKelas . " WHERE nama = ?";
        $stmt = self::getDB()->prepare($query);
        $stmt->bindParam(1, $namaKelas);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $result = [
            "id" => $result['id']
        ];
        return $result;
    }
    public function isEmpty($idUser)
    {

        $query = "SELECT * FROM mahasiswa WHERE id_user = ?";
        $stmt = self::getDB()->prepare($query);
        $stmt->bindParam(1, $idUser);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            return true;
        }

        return (
            $result['id_jurusan'] === null || $result['stambuk'] === null ||
            $result['id_kelas'] === null || $result['nama_lengkap'] === null ||
            $result['alamat'] === null || $result['jenis_kelamin'] === null ||
            $result['tempat_lahir'] === null || $result['tanggal_lahir'] === null ||
            $result['no_telp'] === null
        );
    }


    public function getBiodata($id)
    {
        $query = "
        SELECT 
            s.stambuk,
            k.nama AS kelas,
            j.nama AS jurusan,
            s.nama_lengkap,
            s.alamat,
            s.jenis_kelamin,
            s.tempat_lahir,
            s.tanggal_lahir,
            s.no_telp
        FROM " . static::$table . " s
        JOIN kelas k ON k.id = s.id_kelas
        JOIN jurusan j ON j.id = s.id_jurusan
        WHERE s.id_user = ?";

        $stmt = self::getDB()->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        $stmt = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($stmt) {
            $stmt = [
                "stambuk" => $stmt["stambuk"],
                "kelas" => $stmt["kelas"],
                "jurusan" => $stmt["jurusan"],
                "namaLengkap" => $stmt["nama_lengkap"],
                "alamat" => $stmt["alamat"],
                "jenisKelamin" => $stmt["jenis_kelamin"],
                "tempatLahir" => $stmt["tempat_lahir"],
                "tanggalLahir" => $stmt["tanggal_lahir"],
                "noHp" => $stmt["no_telp"]
            ];
            return $stmt;
        }
        return null;
    }

    public function updateBiodata(BiodataUser $biodata)
{
    // Mulai dengan query update tanpa kolom no_telp
    $sql = "UPDATE " . static::$table . " 
            SET nama_lengkap = ?, id_jurusan = ?, id_kelas = ?, alamat = ?, jenis_kelamin = ?, tempat_lahir = ?, tanggal_lahir = ?";

    // Mendapatkan idKelas dan idJurusan
    $idKelas = $this->getIdKelas($biodata->kelas);
    $idJurusan = $this->getIdJurusan($biodata->jurusan);

    if (!$idKelas || !$idJurusan) {
        throw new \Exception("ID Kelas atau Jurusan tidak valid.");
    }

    // Cek apakah no_telp perlu diperbarui
    $sqlCheckNoTelp = "SELECT no_telp FROM " . static::$table . " WHERE id_user = ?";
    $stmtCheck = self::getDB()->prepare($sqlCheckNoTelp);
    $stmtCheck->bindParam(1, $biodata->idUser);
    $stmtCheck->execute();

    $existingNoTelp = $stmtCheck->fetchColumn();

    // Tambahkan kolom no_telp ke query jika diperlukan
    if ($existingNoTelp !== $biodata->noHp) {
        $sql .= ", no_telp = ?";
    }

    // Tambahkan kondisi WHERE ke query
    $sql .= " WHERE id_user = ?";

    // Persiapkan statement
    $stmt = self::getDB()->prepare($sql);

    // Bind parameter sesuai dengan query
    $stmt->bindParam(1, $biodata->namaLengkap);
    $stmt->bindParam(2, $idJurusan['id']);
    $stmt->bindParam(3, $idKelas['id']);
    $stmt->bindParam(4, $biodata->alamat);
    $stmt->bindParam(5, $biodata->jenisKelamin);
    $stmt->bindParam(6, $biodata->tempatLahir);
    $stmt->bindParam(7, $biodata->tanggalLahir);
    if ($existingNoTelp !== $biodata->noHp) {
        $stmt->bindParam(8, $biodata->noHp);  
        $stmt->bindParam(9, $biodata->idUser); 
    } else {
        $stmt->bindParam(8, $biodata->idUser); 
    }
    try {
        return $stmt->execute();
    } catch (\Exception $e) {
        error_log("SQL Error: " . $sql . " Params: " . json_encode([$biodata->namaLengkap, $idJurusan['id'], $idKelas['id'], $biodata->alamat, $biodata->jenisKelamin, $biodata->tempatLahir, $biodata->tanggalLahir, $biodata->noHp, $biodata->idUser]));
        throw new \Exception("Gagal mengupdate biodata: " . $e->getMessage());
    }
}
}
