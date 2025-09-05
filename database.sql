-- Database schema for Portal Jemari 5.0 PaskerID

CREATE TABLE IF NOT EXISTS users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) NOT NULL,
  email VARCHAR(191) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS partners (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  email VARCHAR(191) NOT NULL,
  phone VARCHAR(50) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tambahkan tabel kontak_mitra ke database schema
CREATE TABLE IF NOT EXISTS kontak_mitra (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nama_perusahaan VARCHAR(200) NOT NULL,
  nama_pic VARCHAR(150) NOT NULL,
  nomor_telp VARCHAR(50) NOT NULL,
  alamat_email VARCHAR(191) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_nama_perusahaan (nama_perusahaan)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table Scedule
CREATE TABLE `jadwal_acara` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `judul_acara` VARCHAR(255) NOT NULL COMMENT 'Judul atau nama dari acara/kegiatan.',
  `tanggal_acara` DATE NOT NULL COMMENT 'Tanggal pelaksanaan acara.',
  `jam_acara` TIME NOT NULL COMMENT 'Waktu (jam) pelaksanaan acara.',
  `tempat` VARCHAR(255) NOT NULL COMMENT 'Lokasi atau tempat acara diadakan.',
  `agenda` TEXT DEFAULT NULL COMMENT 'Agenda atau topik pembahasan utama dalam acara.',
  `keterangan` TEXT DEFAULT NULL COMMENT 'Catatan atau keterangan tambahan terkait acara.',
  `created_at` TIMESTAMP NOT NULL DEFAULT current_timestamp() COMMENT 'Waktu data jadwal ini dibuat.',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tahapan Kerjasama
CREATE TABLE `tahapan_kerjasama` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_mitra` varchar(255) NOT NULL,
  `jenis_mitra` varchar(100) NOT NULL,
  `sumber_usulan` varchar(255) DEFAULT NULL,
  `tandai` tinyint(1) DEFAULT 0,
  `status_kesepahaman` varchar(50) DEFAULT NULL,
  `nomor_kesepahaman` varchar(255) DEFAULT NULL,
  `tanggal_kesepahaman` date DEFAULT NULL,
  `ruanglingkup_kesepahaman` text DEFAULT NULL,
  `status_pelaksanaan_kesepahaman` varchar(50) DEFAULT NULL,
  `rencana_pertemuan_kesepahaman` date DEFAULT NULL,
  `rencana_kolaborasi_kesepahaman` text DEFAULT NULL,
  `status_progres_kesepahaman` text DEFAULT NULL,
  `tindaklanjut_kesepahaman` text DEFAULT NULL,
  `keterangan_kesepahaman` varchar(500) DEFAULT NULL,
  `status_pks` varchar(50) DEFAULT NULL,
  `nomor_pks` varchar(255) DEFAULT NULL,
  `tanggal_pks` date DEFAULT NULL,
  `ruanglingkup_pks` text DEFAULT NULL,
  `status_pelaksanaan_pks` varchar(50) DEFAULT NULL,
  `rencana_pertemuan_pks` date DEFAULT NULL,
  `status_progres_pks` text DEFAULT NULL,
  `tindaklanjut_pks` text DEFAULT NULL,
  `keterangan_pks` varchar(500) DEFAULT NULL,
  `file1` varchar(255) DEFAULT NULL,
  `file2` varchar(255) DEFAULT NULL,
  `file3` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_nama_mitra` (`nama_mitra`),
  KEY `idx_jenis_mitra` (`jenis_mitra`),
  KEY `idx_tandai` (`tandai`),
  KEY `idx_status_kesepahaman` (`status_kesepahaman`),
  KEY `idx_status_pks` (`status_pks`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


    -- table dokumen
CREATE TABLE `dokumen` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `judul` VARCHAR(255) NOT NULL COMMENT 'Judul dokumen yang diberikan oleh pengguna',
  `jenis` VARCHAR(10) NOT NULL COMMENT 'Jenis file dari ekstensi, misal: "pdf", "docx"',
  `tanggal` DATE NOT NULL COMMENT 'Tanggal saat dokumen diunggah',
  `file_path` VARCHAR(255) NOT NULL COMMENT 'Nama file unik yang disimpan di server',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



-- Optional seed admin user (replace hash later):
-- INSERT INTO users (username, email, password) VALUES ('Admin', 'admin@example.com', '$2y$10$replace_with_real_hash');

