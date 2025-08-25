<?php
$conn = new mysqli("localhost", "root", "", "latsar_db");
if ($conn->connect_error) die("Koneksi gagal: " . $conn->connect_error);

$id = $_GET['id'];
$conn->query("DELETE FROM dokumen WHERE id=$id");
header("Location: dokumen_index.php");
exit;
