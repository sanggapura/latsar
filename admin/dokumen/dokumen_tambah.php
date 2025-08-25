<?php
$conn = new mysqli("localhost", "root", "", "latsar_db");
if ($conn->connect_error) die("Koneksi gagal: " . $conn->connect_error);

if ($_POST) {
    $judul = $_POST['judul'];
    $jenis = $_POST['jenis'];
    $tanggal = $_POST['tanggal'];

    $stmt = $conn->prepare("INSERT INTO dokumen (judul, jenis, tanggal) VALUES (?,?,?)");
    $stmt->bind_param("sss", $judul, $jenis, $tanggal);
    $stmt->execute();
    header("Location: dokumen_index.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Tambah Dokumen</title>
</head>
<body>
<h2>Tambah Dokumen</h2>
<form method="POST">
    <label>Judul</label><br>
    <input type="text" name="judul" required><br><br>

    <label>Jenis</label><br>
    <select name="jenis" required>
        <option value="word">Word</option>
        <option value="excel">Excel</option>
        <option value="pdf">PDF</option>
    </select><br><br>

    <label>Tanggal</label><br>
    <input type="date" name="tanggal" required><br><br>

    <button type="submit">Simpan</button>
    <a href="dokumen_index.php">Kembali</a>
</form>
</body>
</html>
