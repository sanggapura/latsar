<?php
// cek file param
if (!isset($_GET['file']) || empty($_GET['file'])) {
    die("File tidak ditemukan.");
}

$file = basename($_GET['file']); // amankan agar tidak bisa akses luar folder
$path = __DIR__ . "/uploads/" . $file;

if (!file_exists($path)) {
    die("File tidak ada di server.");
}

// deteksi mime type
$ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
$mime = "application/octet-stream";

switch ($ext) {
    case "pdf":  $mime = "application/pdf"; break;
    case "jpg": case "jpeg": $mime = "image/jpeg"; break;
    case "png":  $mime = "image/png"; break;
    case "gif":  $mime = "image/gif"; break;
    case "doc":  $mime = "application/msword"; break;
    case "docx": $mime = "application/vnd.openxmlformats-officedocument.wordprocessingml.document"; break;
    case "xls":  $mime = "application/vnd.ms-excel"; break;
    case "xlsx": $mime = "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"; break;
    case "ppt":  $mime = "application/vnd.ms-powerpoint"; break;
    case "pptx": $mime = "application/vnd.openxmlformats-officedocument.presentationml.presentation"; break;
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Preview Dokumen</title>
  <style>
    body { margin:0; padding:0; font-family:sans-serif; background:#f0f2f5; }
    .viewer {
      width:100%;
      height:100vh;
      display:flex;
      justify-content:center;
      align-items:center;
    }
    iframe, embed, img {
      width:90%;
      height:90%;
      border:none;
      box-shadow:0 4px 12px rgba(0,0,0,0.2);
      border-radius:12px;
      background:#fff;
    }
  </style>
</head>
<body>
<div class="viewer">
  <?php if ($ext === "pdf"): ?>
    <embed src="uploads/<?= htmlspecialchars($file) ?>" type="<?= $mime ?>">
  <?php elseif (in_array($ext, ["jpg","jpeg","png","gif"])): ?>
    <img src="uploads/<?= htmlspecialchars($file) ?>" alt="Gambar">
  <?php else: ?>
    <!-- Untuk Word/Excel/PowerPoint: buka via Google Docs Viewer -->
    <iframe src="https://docs.google.com/gview?url=<?= urlencode("http://".$_SERVER['HTTP_HOST']."/latsar/admin/dokumen/uploads/".$file) ?>&embedded=true"></iframe>
  <?php endif; ?>
</div>
</body>
</html>
