<?php
$conn = new mysqli("localhost", "root", "", "jejaring_db");
if ($conn->connect_error) die("Koneksi gagal: " . $conn->connect_error);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_GET['ajax'])) {
  $nama_perusahaan = $conn->real_escape_string($_POST['nama_perusahaan']);
  $nama_pic        = $conn->real_escape_string($_POST['nama_pic']);
  $no_telp         = $conn->real_escape_string($_POST['nomor_telp']);
  $email           = isset($_POST['alamat_email']) && $_POST['alamat_email'] !== "" 
                       ? $conn->real_escape_string($_POST['alamat_email']) 
                       : NULL;

  $sql = "INSERT INTO kontak_mitra (nama_perusahaan, nama_pic, nomor_telp, alamat_email)
          VALUES ('$nama_perusahaan','$nama_pic','$no_telp'," . 
          ($email !== NULL ? "'$email'" : "NULL") . ")";

  header('Content-Type: application/json');
  if ($conn->query($sql)) {
    echo json_encode(["success" => true]);
  } else {
    echo json_encode(["success" => false, "error" => $conn->error]);
  }
  exit;
}
?>

<style>
  .popup-wrapper {
    animation: fadeIn 0.3s ease;
  }
  .form-container {
    max-width: 330px;
    margin: auto;
    background: #fff;
    padding: 18px;
    border-radius: 12px;
    box-shadow: 0 6px 16px rgba(0,0,0,0.1);
    animation: slideUp 0.35s ease;
  }
  @keyframes fadeIn {
    from {opacity:0;} to {opacity:1;}
  }
  @keyframes slideUp {
    from {transform: translateY(40px); opacity:0;}
    to {transform: translateY(0); opacity:1;}
  }
  h2 { text-align:center; margin-bottom:15px; font-size:18px; color:#333; }
  label { font-weight:600; display:block; margin-bottom:6px; color:#444; }
  input {
    width:100%; padding:8px 10px; margin-bottom:12px;
    border:1px solid #ccc; border-radius:8px; font-size:14px;
  }
  .btn { display:inline-block; padding:8px 16px; border:none; border-radius:8px; cursor:pointer; font-size:14px; }
  .btn.save { background:#4CAF50; color:#fff; }
  .btn.save:hover { background:#43a047; }
</style>

<div class="popup-wrapper">
  <div class="form-container">
    <h2>Tambah Kontak</h2>
    <form id="tambahForm">
      <label>Nama Perusahaan</label>
      <input type="text" name="nama_perusahaan" required>

      <label>Nama PIC</label>
      <input type="text" name="nama_pic" required>

      <label>No Telp</label>
      <input type="text" name="nomor_telp" required>

      <label>Email (opsional)</label>
      <input type="email" name="alamat_email" placeholder="contoh@mail.com">

      <button type="submit" class="btn save">💾 Simpan</button>
    </form>
  </div>
</div>
