<?php
// Koneksi ke database
$conn = new mysqli("localhost", "root", "", "jejaring_db");
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Proses form HANYA jika ini adalah request AJAX POST
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_GET['ajax'])) {
    // Ambil dan bersihkan data input
    $nama_perusahaan = trim($conn->real_escape_string($_POST['nama_perusahaan']));
    $nama_pic        = trim($conn->real_escape_string($_POST['nama_pic']));
    $no_telp         = trim($conn->real_escape_string($_POST['nomor_telp']));
    $email           = isset($_POST['alamat_email']) && $_POST['alamat_email'] !== "" 
                       ? trim($conn->real_escape_string($_POST['alamat_email'])) 
                       : NULL;

    // --- VALIDASI DUPLIKAT BARU (HANYA NOMOR TELEPON) ---
    $checkStmt = $conn->prepare("SELECT nama_pic FROM kontak_mitra WHERE nomor_telp = ?");
    $checkStmt->bind_param("s", $no_telp);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {
        $existingContact = $checkResult->fetch_assoc();
        $existingPicName = $existingContact['nama_pic'];
        
        header('Content-Type: application/json');
        echo json_encode([
            "success" => false, 
            "error" => "Nomor telepon '$no_telp' sudah terdaftar atas nama PIC: $existingPicName."
        ]);
        $checkStmt->close();
        exit;
    }
    $checkStmt->close();
    // --- AKHIR VALIDASI ---

    // Jika tidak ada duplikat, lanjutkan proses INSERT
    $sql = "INSERT INTO kontak_mitra (nama_perusahaan, nama_pic, nomor_telp, alamat_email) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $nama_perusahaan, $nama_pic, $no_telp, $email);

    header('Content-Type: application/json');
    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => $stmt->error]);
    }
    $stmt->close();
    exit;
}
?>
<!-- HTML FORM (TIDAK BERUBAH) -->
<style>
  .form-container { max-width: 100%; margin: auto; background: #fff; padding: 18px; border-radius: 12px; }
  h2 { text-align:center; margin-bottom:15px; font-size:18px; color:#333; }
  label { font-weight:600; display:block; margin-bottom:6px; color:#444; }
  input { width:100%; padding:8px 10px; margin-bottom:12px; border:1px solid #ccc; border-radius:8px; font-size:14px; }
  .btn.save { background:#4CAF50; color:#fff; }
</style>
<div class="form-container">
    <form id="tambahForm">
      <div class="mb-3">
        <label class="form-label">Nama Perusahaan <span class="text-danger">*</span></label>
        <input type="text" name="nama_perusahaan" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Nama PIC <span class="text-danger">*</span></label>
        <input type="text" name="nama_pic" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">No Telp <span class="text-danger">*</span></label>
        <input type="text" name="nomor_telp" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Email (opsional)</label>
        <input type="email" name="alamat_email" class="form-control" placeholder="contoh@mail.com">
      </div>
      <div class="text-end">
        <button type="submit" class="btn save"><i class="bi bi-save-fill"></i> Simpan</button>
      </div>
    </form>
</div>

