<?php
// =================================================================
// FILE: delete_file.php (BARU)
// Berfungsi untuk menghapus file dokumen dari server dan database.
// =================================================================
include "db.php";
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 1. Validasi input dari URL
$id = $_GET['id'] ?? null;
$fileKey = $_GET['file_key'] ?? null;

// Keamanan: Pastikan file_key adalah salah satu dari kolom yang diizinkan
$allowedFileKeys = ['file1', 'file2', 'file3'];
if (!$id || !is_numeric($id) || !in_array($fileKey, $allowedFileKeys)) {
    // Redirect dengan pesan error jika input tidak valid
    header("Location: index.php?error=invalid_request");
    exit();
}
$id = intval($id);

// 2. Ambil nama file dari database sebelum dihapus
$stmt = $conn->prepare("SELECT `$fileKey` FROM tahapan_kerjasama WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if ($data && !empty($data[$fileKey])) {
    $fileName = $data[$fileKey];
    $filePath = __DIR__ . '/uploads/' . $fileName;

    // 3. Hapus file fisik dari folder 'uploads' jika ada
    if (file_exists($filePath)) {
        @unlink($filePath); // Gunakan @ untuk menekan error jika file tidak bisa dihapus
    }
}

// 4. Update database, set kolom file menjadi NULL
$stmt_update = $conn->prepare("UPDATE tahapan_kerjasama SET `$fileKey` = NULL WHERE id = ?");
$stmt_update->bind_param("i", $id);

if ($stmt_update->execute()) {
    // Berhasil, redirect kembali ke halaman utama dengan pesan sukses
    header("Location: index.php?success=file_deleted");
    exit();
} else {
    // Gagal, redirect dengan pesan error
    header("Location: index.php?error=db_update_failed");
    exit();
}
