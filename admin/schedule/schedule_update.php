<?php
// Set header sebagai JSON di paling atas untuk memastikan output selalu JSON
header('Content-Type: application/json');

// Fungsi untuk mengirim response error yang terstandardisasi
function deliver_json_error($message, $statusCode = 500) {
    http_response_code($statusCode);
    echo json_encode(['success' => false, 'error' => $message]);
    exit;
}

try {
    // Hanya izinkan metode request POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        deliver_json_error("Metode request tidak valid, harus POST.", 405); // 405 Method Not Allowed
    }
    
    // Koneksi ke database
    $conn = new mysqli("localhost", "root", "", "jejaring_db");
    if ($conn->connect_error) {
        throw new Exception("Koneksi database gagal.");
    }

    // Ambil dan validasi data dari POST
    $id = $_POST['id'] ?? 0;
    if (!filter_var($id, FILTER_VALIDATE_INT) || $id <= 0) {
        deliver_json_error("ID jadwal tidak valid.", 400); // 400 Bad Request
    }

    $judul = trim($_POST['judul_acara'] ?? '');
    $tanggal = trim($_POST['tanggal_acara'] ?? '');
    $jam = trim($_POST['jam_acara'] ?? '');
    $tempat = trim($_POST['tempat'] ?? '');
    $agenda = trim($_POST['agenda'] ?? null);
    $keterangan = trim($_POST['keterangan'] ?? null);

    // Pastikan kolom wajib diisi
    if (empty($judul) || empty($tanggal) || empty($jam) || empty($tempat)) {
        deliver_json_error("Judul, Tanggal, Jam, dan Tempat wajib diisi.", 400);
    }

    // Siapkan dan eksekusi query UPDATE
    $stmt = $conn->prepare("UPDATE jadwal_acara SET judul_acara=?, tanggal_acara=?, jam_acara=?, tempat=?, agenda=?, keterangan=? WHERE id=?");
    if ($stmt === false) {
        throw new Exception("Gagal menyiapkan query: " . $conn->error);
    }

    $stmt->bind_param("ssssssi", $judul, $tanggal, $jam, $tempat, $agenda, $keterangan, $id);
    
    if (!$stmt->execute()) {
        throw new Exception("Gagal mengeksekusi query: " . $stmt->error);
    }

    // Berikan response sukses
    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Jadwal berhasil diperbarui.']);
    } else {
        // Ini terjadi jika pengguna mengklik simpan tanpa mengubah apa pun
        echo json_encode(['success' => true, 'message' => 'Tidak ada perubahan yang disimpan.']);
    }
    
    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    // Tangkap semua jenis error dan kirim sebagai JSON
    deliver_json_error($e->getMessage());
}
?>

