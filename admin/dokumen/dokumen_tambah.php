<?php
session_start();
$conn = new mysqli("localhost", "root", "", "jejaring_db");
if ($conn->connect_error) die("Koneksi gagal: " . $conn->connect_error);

if (isset($_GET['ajax'])) {
?>
  <h3 style="margin-bottom:10px;">Tambah Dokumen</h3>
  <form id="tambahForm" method="POST" enctype="multipart/form-data">
    <label>Judul Dokumen</label>
    <input type="text" name="judul" required class="form-control">

    <label>Tanggal Upload</label>
    <input type="date" name="tanggal" required class="form-control">

    <label>File</label>
    <input type="file" name="file" accept=".pdf,.doc,.docx,.xls,.xlsx" required class="form-control">

    <button type="submit" class="btn btn-success mt-3">Simpan</button>
  </form>
<?php
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $judul   = $_POST['judul'] ?? '';
  $tanggal = $_POST['tanggal'] ?? '';

  // cek input
  if (empty($judul) || empty($tanggal) || !isset($_FILES['file'])) {
    echo json_encode(["success" => false, "error" => "Semua field wajib diisi"]);
    exit;
  }

  // tentukan jenis berdasarkan ekstensi file
  $ext = strtolower(pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION));
  if (in_array($ext, ['doc','docx'])) {
      $jenis = 'word';
  } elseif (in_array($ext, ['xls','xlsx'])) {
      $jenis = 'excel';
  } elseif ($ext === 'pdf') {
      $jenis = 'pdf';
  } else {
      echo json_encode(["success" => false, "error" => "Format file tidak didukung"]);
      exit;
  }

  // simpan file
  $targetDir = __DIR__ . "/uploads/";
  if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

  $fileName   = time() . "_" . basename($_FILES["file"]["name"]);
  $targetFile = $targetDir . $fileName;

  if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
    $file_path = "uploads/" . $fileName;
    $stmt = $conn->prepare("INSERT INTO dokumen (judul, jenis, tanggal, file_path) VALUES (?,?,?,?)");
    $stmt->bind_param("ssss", $judul, $jenis, $tanggal, $file_path);
    if ($stmt->execute()) {
      echo json_encode(["success" => true]);
    } else {
      echo json_encode(["success" => false, "error" => $stmt->error]);
    }
  } else {
    echo json_encode(["success" => false, "error" => "Upload gagal"]);
  }
}
?>
