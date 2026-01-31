-- =========================================
-- MIGRATION: Menambahkan kolom image_url pada tabel soal
-- =========================================
-- Deskripsi: Kolom ini akan menyimpan URL/path gambar soal secara terpisah dari deskripsi
-- Tanggal: 2026-01-31
-- =========================================

-- Cek apakah kolom sudah ada, jika belum maka tambahkan
SET @dbname = DATABASE();
SET @tablename = 'soal';
SET @columnname = 'image_url';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  "SELECT 'Column image_url already exists.' AS message;",
  "ALTER TABLE `soal` ADD COLUMN `image_url` VARCHAR(500) NULL AFTER `deskripsi`;"
));
PREPARE alterStatement FROM @preparedStatement;
EXECUTE alterStatement;
DEALLOCATE PREPARE alterStatement;

-- Verifikasi
SELECT
    COLUMN_NAME,
    DATA_TYPE,
    IS_NULLABLE,
    COLUMN_DEFAULT
FROM
    INFORMATION_SCHEMA.COLUMNS
WHERE
    TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'soal'
    AND COLUMN_NAME = 'image_url';

-- Jika kolom berhasil ditambahkan, akan menampilkan informasi kolom image_url
