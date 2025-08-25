<?php
$conn = new mysqli("localhost", "root", "", "latsar_db");
if ($conn->connect_error) die("Koneksi gagal: " . $conn->connect_error);

$id = $_GET['id'] ?? 0;
$conn->query("DELETE FROM kontak_mitra WHERE id=$id");
header("Location: daftar_kontak.php");
exit;
