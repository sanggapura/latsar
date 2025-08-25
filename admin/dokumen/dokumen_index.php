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
<html>
<head>
    <title>Daftar Dokumen</title>
    <style>
        body { font-family: Arial; background:#f8f9fa; padding:20px; }
        .container { max-width:900px; margin:auto; background:white; padding:20px; border-radius:8px; box-shadow:0 2px 6px rgba(0,0,0,0.1); }
        h2 { margin-top:0; }
        table { width:100%; border-collapse: collapse; margin-top:15px; }
        th, td { border:1px solid #ccc; padding:8px; text-align:left; }
        th { background:#3498db; color:white; }
        .btn { padding:6px 12px; border-radius:5px; text-decoration:none; margin-right:5px; }
        .btn-primary { background:#3498db; color:white; }
        .btn-warning { background:#f39c12; color:white; }
        .btn-danger { background:#e74c3c; color:white; }
        .btn-secondary { background:#7f8c8d; color:white; }
        .icon { font-weight:bold; padding:3px 8px; border-radius:4px; color:white; }
        .word { background:#2980b9; }
        .excel { background:#27ae60; }
        .pdf { background:#c0392b; }
    </style>
</head>
<body>
<div class="container">
    <h2>Daftar Dokumen</h2>
    <form method="GET">
        <input type="text" name="search" placeholder="Cari dokumen..." value="<?= $_GET['search'] ?? '' ?>">
        <button type="submit" class="btn btn-secondary">Cari</button>
        <a href="dokumen_index.php" class="btn btn-secondary">Reset</a>
        <a href="dokumen_tambah.php" class="btn btn-primary">+ Tambah Dokumen</a>
    </form>

    <table>
        <tr>
            <th>ID</th>
            <th>Judul</th>
            <th>Jenis</th>
            <th>Tanggal</th>
            <th>Aksi</th>
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
            <td>
                <a href="dokumen_edit.php?id=<?= $row['id'] ?>" class="btn btn-warning">Edit</a>
                <a href="dokumen_delete.php?id=<?= $row['id'] ?>" class="btn btn-danger" onclick="return confirm('Yakin hapus dokumen ini?')">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>
</body>
</html>
