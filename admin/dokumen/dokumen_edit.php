<?php
$conn = new mysqli("localhost", "root", "", "latsar_db");
if ($conn->connect_error) die("Koneksi gagal: " . $conn->connect_error);

$id = $_GET['id'] ?? 0;
$stmt = $conn->prepare("SELECT * FROM dokumen WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$dokumen = $stmt->get_result()->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = $_POST['judul'];
    $tanggal = $_POST['tanggal'];

    // jika ganti file
    if (!empty($_FILES['file']['name'])) {
        $targetDir = "uploads/";
        $fileName = time() . "_" . basename($_FILES["file"]["name"]);
        $targetFile = $targetDir . $fileName;
        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        if (in_array($fileType, ["doc","docx"])) $jenis = "word";
        elseif (in_array($fileType, ["xls","xlsx"])) $jenis = "excel";
        elseif ($fileType == "pdf") $jenis = "pdf";
        else die("Format tidak didukung!");

        move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile);

        $stmt = $conn->prepare("UPDATE dokumen SET judul=?, jenis=?, tanggal=?, file_path=? WHERE id=?");
        $stmt->bind_param("ssssi", $judul, $jenis, $tanggal, $targetFile, $id);
    } else {
        $stmt = $conn->prepare("UPDATE dokumen SET judul=?, tanggal=? WHERE id=?");
        $stmt->bind_param("ssi", $judul, $tanggal, $id);
    }

    $stmt->execute();
    header("Location: dokumen_index.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head><title>Edit Dokumen</title></head>
<body>
<h2>Edit Dokumen</h2>
<form method="POST" enctype="multipart/form-data">
    <label>Judul</label><br>
    <input type="text" name="judul" value="<?= htmlspecialchars($dokumen['judul']) ?>" required><br><br>

    <label>Tanggal</label><br>
    <input type="date" name="tanggal" value="<?= $dokumen['tanggal'] ?>" required><br><br>

    <label>Ganti File (opsional)</label><br>
    <input type="file" name="file"><br><br>

    <button type="submit">Simpan Perubahan</button>
    <a href="dokumen_index.php">Kembali</a>
</form>
</body>
</html>
