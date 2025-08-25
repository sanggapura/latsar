<?php
$conn = new mysqli("localhost", "root", "", "latsar_db");
if ($conn->connect_error) die("Koneksi gagal: " . $conn->connect_error);

// search
$search = $_GET['search'] ?? '';
if ($search) {
    $stmt = $conn->prepare("SELECT * FROM dokumen WHERE judul LIKE ? ORDER BY tanggal DESC");
    $param = "%" . $search . "%";
    $stmt->bind_param("s", $param);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query("SELECT * FROM dokumen ORDER BY tanggal DESC");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Daftar Dokumen</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f4f6f9; }
        h2 { color: #333; }
        .top-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; }
        .search-box input[type=text] { padding: 5px 10px; width: 200px; }
        .search-box button { padding: 5px 10px; background: #007bff; color: #fff; border: none; cursor: pointer; }
        .search-box button:hover { background: #0056b3; }
        a.btn { padding: 6px 12px; text-decoration: none; border-radius: 4px; font-size: 14px; }
        .btn-add { background: #28a745; color: white; }
        .btn-edit { background: #ffc107; color: black; }
        .btn-delete { background: #dc3545; color: white; }
        .btn-download { background: #17a2b8; color: white; }
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
        th, td { padding: 10px; border-bottom: 1px solid #ddd; text-align: left; }
        th { background: #007bff; color: white; }
        tr:hover { background: #f1f1f1; }
        .icon { width: 20px; vertical-align: middle; margin-right: 5px; }
    </style>
</head>
<body>
    <h2>📂 Daftar Dokumen</h2>

    <div class="top-bar">
        <form class="search-box" method="GET">
            <input type="text" name="search" placeholder="Cari judul..." value="<?= htmlspecialchars($search) ?>">
            <button type="submit">Search</button>
        </form>
        <a href="dokumen_tambah.php" class="btn btn-add">+ Tambah Dokumen</a>
    </div>

    <table>
        <tr>
            <th>Judul</th>
            <th>Jenis</th>
            <th>Tanggal</th>
            <th>File</th>
            <th>Aksi</th>
        </tr>
        <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['judul']) ?></td>
                <td>
                    <?php if ($row['jenis'] == 'word'): ?>
                        <img src="icons/word.png" class="icon"> Word
                    <?php elseif ($row['jenis'] == 'excel'): ?>
                        <img src="icons/excel.png" class="icon"> Excel
                    <?php elseif ($row['jenis'] == 'pdf'): ?>
                        <img src="icons/pdf.png" class="icon"> PDF
                    <?php endif; ?>
                </td>
                <td><?= $row['tanggal'] ?></td>
                <td><a href="<?= $row['file_path'] ?>" class="btn btn-download" download>Download</a></td>
                <td>
                    <a href="dokumen_edit.php?id=<?= $row['id'] ?>" class="btn btn-edit">Edit</a>
                    <a href="dokumen_delete.php?id=<?= $row['id'] ?>" class="btn btn-delete" onclick="return confirm('Yakin ingin menghapus dokumen ini?')">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
