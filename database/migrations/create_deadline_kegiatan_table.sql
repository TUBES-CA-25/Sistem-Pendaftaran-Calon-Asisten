CREATE TABLE IF NOT EXISTS deadline_kegiatan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    jenis VARCHAR(50) NOT NULL UNIQUE,
    tanggal DATE NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Seed defaults
INSERT IGNORE INTO deadline_kegiatan (jenis, tanggal) VALUES 
('kelengkapan_berkas', '2026-02-01'),
('tes_tertulis', '2026-02-05'),
('tahap_wawancara', '2026-02-15'),
('pengumuman', '2026-02-28');
