<?php
$conn = new mysqli("localhost", "root", "", "latsar_db");
if ($conn->connect_error) die("Koneksi gagal: " . $conn->connect_error);

$id = $_GET['id'];
$dokumen = $conn->query("SELECT * FROM dokumen WHERE id=$id")->fetch_assoc();

if ($_POST) {
    $judul = $_POST['judul'];
    $jenis = $_POST['jenis'];
    $tanggal = $_POST['tanggal'];

    $stmt = $conn->prepare("UPDATE dokumen SET judul=?, jenis=?, tanggal=? WHERE id=?");
    $stmt->bind_param("sssi", $judul, $jenis, $tanggal, $id);
    $stmt->execute();
    header("Location: dokumen_index.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Dokumen</title>
</head>
<body>
<h2>Edit Dokumen</h2>
<form method="POST">
    <label>Judul</label><br>
    <input type="text" name="judul" value="<?= htmlspecialchars($dokumen['judul']) ?>" required><br><br>

    <label>Jenis</label><br>
    <select name="jenis" required>
        <option value="word" <?= $dokumen['jenis']=='word'?'selected':'' ?>>Word</option>
        <option value="excel" <?= $dokumen['jenis']=='excel'?'selected':'' ?>>Excel</option>
        <option value="pdf" <?= $dokumen['jenis']=='pdf'?'selected':'' ?>>PDF</option>
    </select><br><br>

    <label>Tanggal</label><br>
    <input type="date" name="tanggal" value="<?= $dokumen['tanggal'] ?>" required><br><br>

    <button type="submit">Update</button>
    <a href="dokumen_index.php">Kembali</a>
</form>
</body>
</html>
