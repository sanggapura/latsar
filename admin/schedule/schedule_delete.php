<?php
// Set header sebagai JSON di paling atas
header('Content-Type: application/json');

// Fungsi untuk mengirim response error yang terstandardisasi
function deliver_json_error($message, $statusCode = 500) {
    http_response_code($statusCode);
    echo json_encode(['success' => false, 'error' => $message]);
    exit;
}

try {
    // Hanya izinkan request POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        deliver_json_error("Metode request tidak valid, harus POST.", 405); // 405 Method Not Allowed
    }
    
    // Koneksi ke database
    $conn = new mysqli("localhost", "root", "", "jejaring_db");
    if ($conn->connect_error) {
        throw new Exception("Koneksi database gagal: " . $conn->connect_error);
    }

    // Validasi ID dari POST
    $id = $_POST['id'] ?? 0;
    if (!filter_var($id, FILTER_VALIDATE_INT) || $id <= 0) {
        deliver_json_error("ID jadwal yang akan dihapus tidak valid.", 400);
    }

    // Siapkan dan eksekusi query DELETE
    $stmt = $conn->prepare("DELETE FROM jadwal_acara WHERE id = ?");
    if ($stmt === false) {
        throw new Exception("Gagal menyiapkan query: " . $conn->error);
    }

    $stmt->bind_param("i", $id);
    $stmt->execute();

    // Berikan response sukses jika ada baris yang terpengaruh
    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true]);
    } else {
        // Jika tidak ada baris yang terhapus, berarti data tidak ditemukan
        deliver_json_error('Jadwal tidak ditemukan atau sudah dihapus sebelumnya.', 404);
    }

    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    // Tangkap semua jenis error dan kirim sebagai JSON
    deliver_json_error($e->getMessage());
}
?>

