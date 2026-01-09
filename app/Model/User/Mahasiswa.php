<?php

namespace App\Model\User;
use App\Core\Model;

class Mahasiswa extends Model
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
    protected $id_kelas;
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

    public function getAll()
    {
        $query = "SELECT * FROM " . static::$table;
        $stmt = self::getDB()->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll();

        $data = [];
        foreach ($result as $stmt) {
            $berkas = $this->getBerkasMahasiswa($stmt['id']);
            $presentasi = $this->getPresentasiMahasiswa($stmt['id']);

            $data[] = [
                'id' => $stmt['id'],
                'idUser' => $stmt['id_user'],
                'nama_lengkap' => $stmt['nama_lengkap'],
                'stambuk' => $stmt['stambuk'],
                'jurusan' => $this->getJurusan($stmt['id_jurusan'])['nama'] ?? null,
                'kelas' => $this->getKelas($stmt['id_kelas'])['nama'] ?? null,
                'alamat' => $stmt['alamat'],
                'notelp' => $stmt['no_telp'],
                'tempat_lahir' => $stmt['tempat_lahir'],
                'tanggal_lahir' => $stmt['tanggal_lahir'],
                'jenis_kelamin' => $stmt['jenis_kelamin'],
                'judul_presentasi' => $presentasi['judul'] ?? null,
                'berkas' => [
                    'foto' => $berkas['foto'],
                    'cv' => $berkas['cv'],
                    'transkrip_nilai' => $berkas['transkrip_nilai'],
                    'surat_pernyataan' => $berkas['surat_pernyataan'],
                    'accepted' => $berkas['accepted'] ?? null
                ],
                'presentasi' => [
                    'judul' => $presentasi['judul'] ?? null,
                    'makalah' => $presentasi['makalah'] ?? null,
                    'ppt' => $presentasi['ppt'] ?? null,
                    'is_accepted' => $presentasi['is_accepted'] ?? null,
                    'is_revisi' => $presentasi['is_revisi'] ?? null
                ]
            ];
        }

        return $data;
    }
    public function deleteMahasiswaById($mahasiswaId)
    {
        $query = "DELETE FROM " . static::$table . " WHERE id = :id";
        $stmt = self::getDB()->prepare($query);
        $stmt->bindParam(':id', $mahasiswaId);
        $stmt->execute();
    }

    public function updateBiodataMahasiswaById(
        $id,
        $nama,
        $stambuk,
        $jurusan,
        $jenisKelamin,
        $kelas,
        $alamat,
        $tempatLahir,
        $tanggalLahir,
    ) {

        $query = "UPDATE " . static::$table .
            " SET nama_lengkap = :nama, 
            stambuk = :stambuk, 
            id_jurusan = :jurusan, 
            jenis_kelamin = :jenis_kelamin, 
            id_kelas = :kelas, 
            alamat = :alamat, 
            tempat_lahir = :tempat_lahir, 
            tanggal_lahir = :tanggal_lahir 
            WHERE id = :id";
        $stmt = self::getDB()->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':nama', $nama);
        $stmt->bindParam(':stambuk', $stambuk);
        $stmt->bindParam(':jurusan', $jurusan);
        $stmt->bindParam(':jenis_kelamin', $jenisKelamin);
        $stmt->bindParam(':kelas', $kelas);
        $stmt->bindParam(':alamat', $alamat);
        $stmt->bindParam(':tempat_lahir', $tempatLahir);
        $stmt->bindParam(':tanggal_lahir', $tanggalLahir);
        $stmt->execute();
    }

    public function getBerkasMahasiswa($mahasiswaId)
    {
        $query = "SELECT foto, cv, transkrip_nilai, surat_pernyataan, accepted FROM berkas_mahasiswa WHERE id_mahasiswa = :mahasiswa_id";
        $stmt = self::getDB()->prepare($query);
        $stmt->bindParam(':mahasiswa_id', $mahasiswaId);
        $stmt->execute();
        $result = $stmt->fetch();

        return $result ?: [
            'foto' => null,
            'cv' => null,
            'transkrip_nilai' => null,
            'surat_pernyataan' => null,
            'accepted' => null
        ];
    }

    /**
     * Get presentasi data for mahasiswa
     */
    public function getPresentasiMahasiswa($mahasiswaId)
    {
        $query = "SELECT judul, makalah, ppt, is_accepted, is_revisi FROM presentasi WHERE id_mahasiswa = :mahasiswa_id";
        $stmt = self::getDB()->prepare($query);
        $stmt->bindParam(':mahasiswa_id', $mahasiswaId);
        $stmt->execute();
        $result = $stmt->fetch();

        return $result ?: [
            'judul' => null,
            'makalah' => null,
            'ppt' => null,
            'is_accepted' => null,
            'is_revisi' => null
        ];
    }

    private function getJurusan($id)
    {
        $query = "SELECT nama FROM jurusan WHERE id = :id";
        $stmt = self::getDB()->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    private function getKelas($id)
    {
        $query = "SELECT nama FROM kelas WHERE id = :id";
        $stmt = self::getDB()->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }
    public function getMahasiswaId($id)
    {
        $query = "SELECT * FROM " . static::$table . " WHERE id_user = :id";
        $stmt = self::getDB()->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Get mahasiswa detail by mahasiswa ID
     */
    public function getMahasiswaById($mahasiswaId)
    {
        $query = "SELECT * FROM " . static::$table . " WHERE id = :id";
        $stmt = self::getDB()->prepare($query);
        $stmt->bindParam(':id', $mahasiswaId);
        $stmt->execute();
        $result = $stmt->fetch();

        if (!$result) {
            return null;
        }

        $berkas = $this->getBerkasMahasiswa($result['id']);
        $presentasi = $this->getPresentasiMahasiswa($result['id']);

        return [
            'id' => $result['id'],
            'idUser' => $result['id_user'],
            'nama_lengkap' => $result['nama_lengkap'],
            'stambuk' => $result['stambuk'],
            'jurusan' => $this->getJurusan($result['id_jurusan'])['nama'] ?? null,
            'id_jurusan' => $result['id_jurusan'],
            'kelas' => $this->getKelas($result['id_kelas'])['nama'] ?? null,
            'id_kelas' => $result['id_kelas'],
            'alamat' => $result['alamat'],
            'notelp' => $result['no_telp'],
            'tempat_lahir' => $result['tempat_lahir'],
            'tanggal_lahir' => $result['tanggal_lahir'],
            'jenis_kelamin' => $result['jenis_kelamin'],
            'judul_presentasi' => $presentasi['judul'] ?? null,
            'berkas' => [
                'foto' => $berkas['foto'],
                'cv' => $berkas['cv'],
                'transkrip_nilai' => $berkas['transkrip_nilai'],
                'surat_pernyataan' => $berkas['surat_pernyataan'],
                'accepted' => $berkas['accepted'] ?? null
            ],
            'presentasi' => [
                'judul' => $presentasi['judul'] ?? null,
                'makalah' => $presentasi['makalah'] ?? null,
                'ppt' => $presentasi['ppt'] ?? null,
                'is_accepted' => $presentasi['is_accepted'] ?? null,
                'is_revisi' => $presentasi['is_revisi'] ?? null
            ]
        ];
    }
}