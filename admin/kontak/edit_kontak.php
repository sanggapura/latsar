<?php
// Koneksi ke database
$conn = new mysqli("localhost", "root", "", "jejaring_db");
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Validasi ID
$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) {
    die("ID tidak valid.");
}
$id = intval($id);

// Proses form HANYA jika ini adalah request AJAX POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['ajax'])) {
    // Ambil dan bersihkan data input
    $nama_perusahaan = trim($conn->real_escape_string($_POST['nama_perusahaan']));
    $nama_pic        = trim($conn->real_escape_string($_POST['nama_pic']));
    $nomor_telp      = trim($conn->real_escape_string($_POST['nomor_telp']));
    $alamat_email    = isset($_POST['alamat_email']) && $_POST['alamat_email'] !== "" 
                       ? trim($conn->real_escape_string($_POST['alamat_email']))
                       : NULL;

    // --- VALIDASI DUPLIKAT BARU ---
    // Cek apakah kombinasi Nama PIC dan No. Telp sudah digunakan oleh kontak LAIN
    $checkStmt = $conn->prepare("SELECT id FROM kontak_mitra WHERE nama_pic = ? AND nomor_telp = ? AND id != ?");
    $checkStmt->bind_param("ssi", $nama_pic, $nomor_telp, $id);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {
        // Jika sudah ada, kirim pesan error dan hentikan proses
        header('Content-Type: application/json');
        echo json_encode(["success" => false, "error" => "Kombinasi Nama PIC dan Nomor Telepon sudah terdaftar."]);
        $checkStmt->close();
        exit;
    }
    $checkStmt->close();
    // --- AKHIR VALIDASI ---

    // Jika tidak ada duplikat, lanjutkan proses UPDATE
    $sql = "UPDATE kontak_mitra 
            SET nama_perusahaan=?, nama_pic=?, nomor_telp=?, alamat_email=?
            WHERE id=?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $nama_perusahaan, $nama_pic, $nomor_telp, $alamat_email, $id);

    header('Content-Type: application/json');
    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => $stmt->error]);
    }
    $stmt->close();
    exit;
}

// Ambil data kontak yang akan diedit untuk ditampilkan di form
$result = $conn->query("SELECT * FROM kontak_mitra WHERE id=$id");
$row = $result->fetch_assoc();
if (!$row) {
    die("Kontak tidak ditemukan.");
}
?>
<style>
  /* Styling untuk form di dalam popup */
  .form-container { max-width: 100%; margin: auto; background: #fff; padding: 18px; border-radius: 12px; }
  h2 { text-align:center; margin-bottom:15px; font-size:18px; color:#333; }
  label { font-weight:600; display:block; margin-bottom:6px; color:#444; }
  input { width:100%; padding:8px 10px; margin-bottom:12px; border:1px solid #ccc; border-radius:8px; font-size:14px; }
  .btn { display:inline-block; padding:8px 16px; border:none; border-radius:8px; cursor:pointer; font-size:14px; }
  .btn.save { background:#4CAF50; color:#fff; }
  .btn.save:hover { background:#43a047; }
</style>

<div class="form-container">
    <form id="editForm">
      <div class="mb-3">
        <label class="form-label">Nama Perusahaan <span class="text-danger">*</span></label>
        <input type="text" name="nama_perusahaan" class="form-control" value="<?= htmlspecialchars($row['nama_perusahaan']) ?>" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Nama PIC <span class="text-danger">*</span></label>
        <input type="text" name="nama_pic" class="form-control" value="<?= htmlspecialchars($row['nama_pic']) ?>" required>
      </div>

      <div class="mb-3">
        <label class="form-label">No. Telp <span class="text-danger">*</span></label>
        <input type="text" name="nomor_telp" class="form-control" value="<?= htmlspecialchars($row['nomor_telp']) ?>" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Email (opsional)</label>
        <input type="email" name="alamat_email" class="form-control" value="<?= htmlspecialchars($row['alamat_email']) ?>">
      </div>
      
      <div class="text-end">
        <button type="submit" class="btn save"><i class="bi bi-save-fill"></i> Simpan Perubahan</button>
      </div>
    </form>
</div>

