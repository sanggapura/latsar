<?php
$conn = new mysqli("localhost", "root", "", "jejaring_db");
if ($conn->connect_error) die("Koneksi gagal: " . $conn->connect_error);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $nama_perusahaan = $conn->real_escape_string($_POST['nama_perusahaan']);
  $nama_pic = $conn->real_escape_string($_POST['nama_pic']);
  $no_telp = $conn->real_escape_string($_POST['no_telp']);
  $email = $conn->real_escape_string($_POST['email']);

  $sql = "INSERT INTO kontak_mitra (nama_perusahaan, nama_pic, no_telp, email)
          VALUES ('$nama_perusahaan','$nama_pic','$no_telp','$email')";
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
<head><meta charset="UTF-8"><title>Tambah Kontak</title></head>
<body>
  <h2>Tambah Kontak</h2>
  <form method="POST">
    <label>Nama Perusahaan:</label><br>
    <input type="text" name="nama_perusahaan" required><br><br>
    <label>Nama PIC:</label><br>
    <input type="text" name="nama_pic" required><br><br>
    <label>No Telp:</label><br>
    <input type="text" name="no_telp" required><br><br>
    <label>Email:</label><br>
    <input type="email" name="email" required><br><br>
    <button type="submit">Simpan</button>
  </form>
</body>
</html>
