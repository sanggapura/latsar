<?php
if (!isset($_GET['file']) || empty($_GET['file'])) {
    die("❌ File tidak ditemukan.");
}

$file = basename($_GET['file']); 
$path = __DIR__ . "/uploads/" . $file;

if (!file_exists($path)) {
    die("❌ File tidak ada di server.");
}

header("Content-Description: File Transfer");
header("Content-Type: application/octet-stream");
header("Content-Disposition: attachment; filename=\"" . basename($path) . "\"");
header("Expires: 0");
header("Cache-Control: must-revalidate");
header("Pragma: public");
header("Content-Length: " . filesize($path));

flush();
readfile($path);
exit;
