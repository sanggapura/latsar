<?php
$conn = new mysqli("localhost", "root", "", "C_db");
if ($conn->connect_error) die("Koneksi gagal: " . $conn->connect_error);

$id = $_GET['id'] ?? 0;
$result = $conn->query("SELECT * FROM kontak_mitra WHERE id=$id");
$kontak = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $nama_perusahaan = $conn->real_escape_string($_POST['nama_perusahaan']);
  $nama_pic = $conn->real_escape_string($_POST['nama_pic']);
  $no_telp = $conn->real_escape_string($_POST['no_telp']);
  $email = $conn->real_escape_string($_POST['email']);

  $sql = "UPDATE kontak_mitra SET 
          nama_perusahaan='$nama_perusahaan',
          nama_pic='$nama_pic',
          no_telp='$no_telp',
          email='$email'
          WHERE id=$id";
  if ($conn->query($sql)) {
    header("Location: daftar_kontak.php");
    exit;
  } else {
    echo "Error: " . $conn->error;
  }
}
?>
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><title>Edit Kontak</title></head>
<body>
  <h2>Edit Kontak</h2>
  <form method="POST">
    <label>Nama Perusahaan:</label><br>
    <input type="text" name="nama_perusahaan" value="<?= htmlspecialchars($kontak['nama_perusahaan']) ?>" required><br><br>
    <label>Nama PIC:</label><br>
    <input type="text" name="nama_pic" value="<?= htmlspecialchars($kontak['nama_pic']) ?>" required><br><br>
    <label>No Telp:</label><br>
    <input type="text" name="no_telp" value="<?= htmlspecialchars($kontak['no_telp']) ?>" required><br><br>
    <label>Email:</label><br>
    <input type="email" name="email" value="<?= htmlspecialchars($kontak['email']) ?>" required><br><br>
    <button type="submit">Update</button>
  </form>
</body>
</html>
