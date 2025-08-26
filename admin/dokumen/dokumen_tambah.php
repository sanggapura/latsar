<?php
$conn = new mysqli("localhost", "root", "", "jejaring_db");
if ($conn->connect_error) die("Koneksi gagal: " . $conn->connect_error);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul   = $_POST['judul'];
    $tanggal = $_POST['tanggal'];

    // upload file
    $targetDir = "uploads/";
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $fileName   = basename($_FILES["file"]["name"]);
    $targetFile = $targetDir . time() . "_" . $fileName; // supaya unik
    $fileType   = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // tentukan jenis otomatis dari ekstensi
    if (in_array($fileType, ["doc", "docx"])) {
        $jenis = "word";
    } elseif (in_array($fileType, ["xls", "xlsx"])) {
        $jenis = "excel";
    } elseif ($fileType === "pdf") {
        $jenis = "pdf";
    } else {
        die("Format file tidak diizinkan. Hanya doc, docx, xls, xlsx, pdf");
    }

    if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
        $stmt = $conn->prepare("INSERT INTO dokumen (judul, jenis, tanggal, file_path) VALUES (?,?,?,?)");
        $stmt->bind_param("ssss", $judul, $jenis, $tanggal, $targetFile);
        $stmt->execute();
        header("Location: dokumen_index.php");
        exit;
    } else {
        echo "Gagal upload file.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Tambah Dokumen</title>
</head>
<body>
<h2>Tambah Dokumen</h2>
<form method="POST" enctype="multipart/form-data">
    <label>Judul</label><br>
    <input type="text" name="judul" required><br><br>

    <label>Tanggal</label><br>
    <input type="date" name="tanggal" required><br><br>

    <label>Upload File</label><br>
    <input type="file" name="file" required><br><br>

    <button type="submit">Simpan</button>
    <a href="dokumen_index.php">Kembali</a>
</form>
</body>
</html>
