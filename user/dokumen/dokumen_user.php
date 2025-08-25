<?php
$conn = new mysqli("localhost", "root", "", "latsar_db");
if ($conn->connect_error) die("Koneksi gagal: " . $conn->connect_error);

$where = "";
if (!empty($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
    $where = "WHERE judul LIKE '%$search%' OR jenis LIKE '%$search%'";
}
$result = $conn->query("SELECT * FROM dokumen $where ORDER BY tanggal DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Daftar Dokumen (User)</title>
<style>
    body { font-family: Arial; padding:20px; }
    table { width:100%; border-collapse: collapse; margin-top:15px; }
    th, td { border:1px solid #ccc; padding:8px; text-align:left; }
    th { background:#27ae60; color:#fff; }
    .btn { padding:6px 12px; border-radius:4px; text-decoration:none; }
    .back { background:#7f8c8d; color:white; }
    .icon { font-weight:bold; padding:3px 8px; border-radius:4px; color:white; }
    .word { background:#2980b9; }
    .excel { background:#27ae60; }
    .pdf { background:#c0392b; }
</style>
</head>
<body>
<h2>Daftar Dokumen (User)</h2>

<!-- Form Search -->
<form method="GET" style="margin-top:10px;">
    <input type="text" name="search" placeholder="Cari dokumen..." value="<?= $_GET['search'] ?? '' ?>">
    <button type="submit" class="btn back">Cari</button>
    <a href="dokumen_user.php" class="btn back">Kembali</a>
</form>

<!-- Tabel Dokumen -->
<table>
    <tr>
        <th>ID</th>
        <th>Judul</th>
        <th>Jenis</th>
        <th>Tanggal</th>
    </tr>
    <?php while($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?= $row['id'] ?></td>
        <td><?= htmlspecialchars($row['judul']) ?></td>
        <td>
            <?php if($row['jenis']=="word"): ?>
                <span class="icon word">W</span> Word
            <?php elseif($row['jenis']=="excel"): ?>
                <span class="icon excel">X</span> Excel
            <?php else: ?>
                <span class="icon pdf">P</span> PDF
            <?php endif; ?>
        </td>
        <td><?= $row['tanggal'] ?></td>
    </tr>
    <?php endwhile; ?>
</table>
</body>
</html>
