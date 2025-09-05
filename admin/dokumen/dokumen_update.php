<?php
// Set header sebagai JSON di paling atas dan matikan output error PHP ke browser
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

// Validasi ID
$id = $_POST['id'] ?? 0;
if ($id <= 0) {
    echo json_encode(["success" => false, "error" => "ID dokumen tidak valid."]);
    exit;
}

// Validasi Judul
$judul = trim($_POST['judul'] ?? '');
if (empty($judul)) {
    echo json_encode(["success" => false, "error" => "Judul tidak boleh kosong."]);
    exit;
}

// Ambil data lama untuk referensi
$stmt = $conn->prepare("SELECT file_path, jenis FROM dokumen WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$oldData = $res->fetch_assoc();

if (!$oldData) {
    echo json_encode(["success" => false, "error" => "Dokumen dengan ID tersebut tidak ditemukan."]);
    $stmt->close();
    exit;
}

$file_path = $oldData['file_path'];
$jenis = $oldData['jenis'];

// Cek apakah ada file baru yang diunggah
if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
    $targetDir = __DIR__ . "/uploads/";
    
    // Hapus file lama jika ada dan bukan direktori
    $oldFilePath = $targetDir . $file_path;
    if (!empty($file_path) && file_exists($oldFilePath) && !is_dir($oldFilePath)) {
        unlink($oldFilePath);
    }
    
    // Proses file baru
    $safe_filename = preg_replace("/[^a-zA-Z0-9.\-\_]/", "_", basename($_FILES["file"]["name"]));
    $newFileName = time() . "_" . $safe_filename;
    $targetFile = $targetDir . $newFileName;
    
    if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
        $file_path = $newFileName; // Update nama file untuk disimpan di DB
        $jenis = strtolower(pathinfo($newFileName, PATHINFO_EXTENSION)); // Update jenis file
    } else {
        echo json_encode(["success" => false, "error" => "Gagal mengunggah file baru."]);
        exit;
    }
}

// Update data di database
$stmt_update = $conn->prepare("UPDATE dokumen SET judul=?, jenis=?, file_path=? WHERE id=?");
$stmt_update->bind_param("sssi", $judul, $jenis, $file_path, $id);

if ($stmt_update->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "error" => "Gagal memperbarui data di database: " . $stmt_update->error]);
}

$stmt->close();
$stmt_update->close();
$conn->close();
?>

