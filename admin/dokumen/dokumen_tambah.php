<?php
// Koneksi ke database
$conn = new mysqli("localhost", "root", "", "jejaring_db");
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Proses HANYA jika ini adalah request AJAX POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    // Ambil dan bersihkan data
    $judul = trim($_POST['judul'] ?? '');
    $tanggal = date('Y-m-d'); // Tanggal otomatis saat ini

    // Validasi dasar
    if (empty($judul) || !isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(["success" => false, "error" => "Judul dan file wajib diisi."]);
        exit;
    }

    // Proses upload file
    $targetDir = __DIR__ . "/uploads/";
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $fileName = time() . "_" . basename(preg_replace("/[^a-zA-Z0-9.\-\_]/", "_", $_FILES["file"]["name"]));
    $targetFile = $targetDir . $fileName;
    
    // Tentukan jenis file (sesuai struktur baru)
    $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    
    // Pindahkan file yang diunggah
    if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
        // Simpan ke database dengan struktur baru
        $stmt = $conn->prepare("INSERT INTO dokumen (judul, jenis, tanggal, file_path) VALUES (?, ?, ?, ?)");
        // 'jenis' sekarang menyimpan ekstensi file langsung
        $stmt->bind_param("ssss", $judul, $ext, $tanggal, $fileName);
        
        if ($stmt->execute()) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "error" => "Gagal menyimpan ke database: " . $stmt->error]);
        }
        $stmt->close();
    } else {
        echo json_encode(["success" => false, "error" => "Gagal mengunggah file."]);
    }
    exit;
}
?>
<!-- HTML Form untuk ditampilkan di Modal -->
<div class="form-container">
    <form id="tambahForm" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">Judul Dokumen <span class="text-danger">*</span></label>
            <input type="text" name="judul" class="form-control" placeholder="Contoh: Laporan Rapat Q3" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Pilih File <span class="text-danger">*</span></label>
            <input type="file" name="file" class="form-control" accept=".pdf,.doc,.docx,.xls,.xlsx" required>
        </div>
        <div class="text-end">
            <button type="submit" class="btn btn-primary"><i class="bi bi-save-fill"></i> Simpan Dokumen</button>
        </div>
    </form>
</div>

