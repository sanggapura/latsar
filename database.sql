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
  nomor_telp VARCHAR(50) DEFAULT NULL,
  alamat_email VARCHAR(191) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_nama_perusahaan (nama_perusahaan),
  INDEX idx_alamat_email (alamat_email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table Scedule
CREATE TABLE schedules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    start DATETIME NOT NULL,
    end DATETIME NOT NULL
);

CREATE TABLE tahapan_kerjasama (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_mitra VARCHAR(255) NOT NULL,
    jenis_mitra ENUM(
        'Kementerian/Lembaga',
        'Pemerintah Daerah',
        'Asosiasi',
        'Perusahaan',
        'Universitas',
        'Job Portal'
    ) NOT NULL,
    sumber_usulan VARCHAR(255),

    -- Kesepahaman
    status_kesepahaman ENUM('Signed', 'Not Available', 'Drafting/In Progress'),
    nomor_kesepahaman TEXT,
    tanggal_kesepahaman DATE,
    ruanglingkup_kesepahaman TEXT,
    status_pelaksanaan_kesepahaman ENUM('Implemented', 'In Progress', 'Not Yet'),
    rencana_pertemuan_kesepahaman DATE,
    rencana_kolaborasi_kesepahaman TEXT,
    status_progres_kesepahaman TEXT,
    tindaklanjut_kesepahaman TEXT,
    keterangan_kesepahaman TEXT,

    -- PKS
    status_pks ENUM('Signed', 'Not Available', 'Drafting/In Progress'),
    nomor_pks TEXT,
    tanggal_pks DATE,
    ruanglingkup_pks TEXT,
    status_pelaksanaan_pks ENUM('Implemented', 'In Progress', 'Not Yet'),
    rencana_pertemuan_pks DATE,
    status_progres_pks TEXT,
    tindaklanjut_pks TEXT,
    keterangan_pks TEXT,

    -- Upload file (max 3)
    file1 VARCHAR(255),
    file2 VARCHAR(255),

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

    -- table dokumen
CREATE TABLE dokumen (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(255) NOT NULL,
    jenis ENUM('word','excel','pdf') NOT NULL,
    tanggal DATE NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);



-- Optional seed admin user (replace hash later):
-- INSERT INTO users (username, email, password) VALUES ('Admin', 'admin@example.com', '$2y$10$replace_with_real_hash');

