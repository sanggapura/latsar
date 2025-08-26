<?php
$conn = new mysqli("localhost", "root", "", "jejaring_db");
if ($conn->connect_error) die("Koneksi gagal: " . $conn->connect_error);

$id = $_GET['id'] ?? null;
if (!$id) die("ID tidak ada.");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['ajax'])) {
    $nama_perusahaan = $conn->real_escape_string($_POST['nama_perusahaan']);
    $nama_pic        = $conn->real_escape_string($_POST['nama_pic']);
    $nomor_telp      = $conn->real_escape_string($_POST['nomor_telp']);
    $alamat_email    = $conn->real_escape_string($_POST['alamat_email']);

    $sql = "UPDATE kontak_mitra 
            SET nama_perusahaan='$nama_perusahaan',
                nama_pic='$nama_pic',
                nomor_telp='$nomor_telp',
                alamat_email='$alamat_email'
            WHERE id=$id";

    header('Content-Type: application/json');
    if ($conn->query($sql)) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => $conn->error]);
    }
    exit;
}

$result = $conn->query("SELECT * FROM kontak_mitra WHERE id=$id");
$row = $result->fetch_assoc();
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
    <h2>Edit Kontak</h2>
    <form id="editForm">
      <label>Nama Perusahaan</label>
      <input type="text" name="nama_perusahaan" value="<?= htmlspecialchars($row['nama_perusahaan']) ?>" required>

      <label>Nama PIC</label>
      <input type="text" name="nama_pic" value="<?= htmlspecialchars($row['nama_pic']) ?>" required>

      <label>No. Telp</label>
      <input type="text" name="nomor_telp" value="<?= htmlspecialchars($row['nomor_telp']) ?>">

      <label>Email</label>
      <input type="email" name="alamat_email" value="<?= htmlspecialchars($row['alamat_email']) ?>" required>

      <button type="submit" class="btn save">💾 Simpan</button>
    </form>
  </div>
</div>
