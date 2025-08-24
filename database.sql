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

CREATE TABLE IF NOT EXISTS kontak_mitra (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nama_perusahaan VARCHAR(200) NOT NULL,
  nama_pic VARCHAR(150) NOT NULL,
  nomor_telp VARCHAR(50) DEFAULT NULL,
  alamat_email VARCHAR(191) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_nama_perusahaan (nama_perusahaan),
  INDEX idx_alamat_email (alamat_email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
-- Optional seed admin user (replace hash later):
-- INSERT INTO users (username, email, password) VALUES ('Admin', 'admin@example.com', '$2y$10$replace_with_real_hash');

