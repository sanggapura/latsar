<?php
// Set header sebagai JSON di paling atas untuk memastikan output selalu JSON
header('Content-Type: application/json');

// Fungsi untuk mengirim response error yang terstandardisasi
function deliver_json_error($message, $statusCode = 500) {
    // Set kode status HTTP yang sesuai
    http_response_code($statusCode);
    // Kirim response dalam format JSON
    echo json_encode(['error' => $message]);
    exit;
}

try {
    // Koneksi ke database
    $conn = new mysqli("localhost", "root", "", "jejaring_db");
    if ($conn->connect_error) {
        // Lemparkan Exception jika koneksi gagal
        throw new Exception("Koneksi database gagal: " . $conn->connect_error);
    }

    // Validasi ID yang diterima dari GET
    $id = $_GET['id'] ?? 0;
    if (!filter_var($id, FILTER_VALIDATE_INT) || $id <= 0) {
        // Kirim error jika ID tidak valid
        deliver_json_error("ID jadwal tidak valid.", 400); // 400 Bad Request
    }

    // Ambil data dari tabel 'jadwal_acara'
    $stmt = $conn->prepare("SELECT * FROM jadwal_acara WHERE id = ?");
    if ($stmt === false) {
        throw new Exception("Gagal menyiapkan query: " . $conn->error);
    }
    
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();

    if ($data) {
        // Jika data ditemukan, kirim sebagai JSON
        echo json_encode($data);
    } else {
        // Jika tidak ada data, kirim error 'Not Found'
        deliver_json_error("Jadwal dengan ID $id tidak ditemukan.", 404); // 404 Not Found
    }

    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    // Tangkap semua jenis error (koneksi, query, dll) dan kirim sebagai JSON
    deliver_json_error($e->getMessage());
}
?>

