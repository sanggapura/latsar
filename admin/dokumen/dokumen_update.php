<?php
$conn = new mysqli("localhost", "root", "", "jejaring_db");
if ($conn->connect_error) die("Koneksi gagal: " . $conn->connect_error);

$id     = $_POST['id'] ?? 0;
$judul  = $_POST['judul'] ?? '';

// ambil data lama
$stmt = $conn->prepare("SELECT file_path, jenis FROM dokumen WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$old = $res->fetch_assoc();
$file_path = $old['file_path'];
$jenis     = $old['jenis'];

// kalau ada file baru → upload dan ganti
if (!empty($_FILES['file']['name'])) {
    $uploadDir = "uploads/";
    $fileName  = time() . "_" . basename($_FILES['file']['name']);
    $target    = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES['file']['tmp_name'], $target)) {
        // hapus file lama
        if (file_exists($file_path)) {
            unlink($file_path);
        }
        $file_path = $target;

        // deteksi jenis otomatis
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        if (in_array($ext, ['doc', 'docx'])) {
            $jenis = 'word';
        } elseif (in_array($ext, ['xls', 'xlsx'])) {
            $jenis = 'excel';
        } elseif ($ext === 'pdf') {
            $jenis = 'pdf';
        } else {
            $jenis = 'lainnya';
        }
    }
}

// update DB
$stmt = $conn->prepare("UPDATE dokumen SET judul=?, jenis=?, file_path=? WHERE id=?");
$stmt->bind_param("sssi", $judul, $jenis, $file_path, $id);
$stmt->execute();

header("Location: dokumen_index.php");
exit;
