<?php
// Set header sebagai JSON dan matikan output error PHP ke browser
header('Content-Type: application/json');
error_reporting(0);
ini_set('display_errors', 0);

// Koneksi ke database
$conn = new mysqli("localhost", "root", "", "jejaring_db");
if ($conn->connect_error) {
    echo json_encode(["success" => false, "error" => "Koneksi database gagal."]);
    exit;
}

// Hanya proses request POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["success" => false, "error" => "Metode request tidak valid."]);
    exit;
}

// Ambil data dari POST dan bersihkan
$judul = trim($_POST['judul_acara'] ?? '');
$tanggal = trim($_POST['tanggal_acara'] ?? '');
$jam = trim($_POST['jam_acara'] ?? '');
$tempat = trim($_POST['tempat'] ?? '');
$agenda = trim($_POST['agenda'] ?? null); // Boleh null
$keterangan = trim($_POST['keterangan'] ?? null); // Boleh null

// Validasi data yang wajib diisi
if (empty($judul) || empty($tanggal) || empty($jam) || empty($tempat)) {
    echo json_encode(["success" => false, "error" => "Judul, Tanggal, Jam, dan Tempat wajib diisi."]);
    exit;
}

// Siapkan query SQL untuk tabel baru 'jadwal_acara'
$sql = "INSERT INTO jadwal_acara (judul_acara, tanggal_acara, jam_acara, tempat, agenda, keterangan) VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    echo json_encode(["success" => false, "error" => "Gagal menyiapkan statement SQL: " . $conn->error]);
    exit;
}

// Bind parameter ke statement
$stmt->bind_param("ssssss", $judul, $tanggal, $jam, $tempat, $agenda, $keterangan);

// Eksekusi statement dan berikan respons
if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Jadwal berhasil ditambahkan."]);
} else {
    echo json_encode(["success" => false, "error" => "Gagal menyimpan jadwal ke database: " . $stmt->error]);
}

// Tutup koneksi
$stmt->close();
$conn->close();

?>
