-- Migration: Create bank_soal table and modify soal table
-- Date: 2026-01-09
-- Description: Add hierarchical structure for question banks

-- Create bank_soal table
CREATE TABLE IF NOT EXISTS bank_soal (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama VARCHAR(255) NOT NULL,
    deskripsi TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add bank_soal_id to soal table
ALTER TABLE soal 
ADD COLUMN bank_soal_id INT DEFAULT NULL AFTER id,
ADD INDEX idx_bank_soal_id (bank_soal_id),
ADD CONSTRAINT fk_soal_bank_soal 
    FOREIGN KEY (bank_soal_id) 
    REFERENCES bank_soal(id) 
    ON DELETE CASCADE 
    ON UPDATE CASCADE;

-- Insert default bank for existing questions
INSERT INTO bank_soal (nama, deskripsi) 
VALUES ('Bank Soal Default', 'Bank soal untuk pertanyaan yang sudah ada');

-- Update existing questions to belong to default bank
UPDATE soal SET bank_soal_id = 1 WHERE bank_soal_id IS NULL;
