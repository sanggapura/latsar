<?php
$conn = new mysqli("localhost", "root", "", "latsar_db");
if ($conn->connect_error) die("Koneksi gagal: " . $conn->connect_error);

$result = $conn->query("SELECT * FROM kontak_mitra ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Daftar Kontak Mitra (Admin)</title>
  <style>
    table { width: 100%; border-collapse: collapse; margin:20px auto; }
    th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
    th { background: #27ae60; color: white; }
    a.btn { padding:6px 12px; border-radius:4px; text-decoration:none; margin-right:5px; }
    .add { background:#3498db; color:white; }
    .edit { background:#f39c12; color:white; }
    .delete { background:#e74c3c; color:white; }
    .back { background:#7f8c8d; color:white; }
    #searchInput { padding:8px; width:250px; margin:10px; }
    #searchBtn { padding:8px 12px; background:#27ae60; color:white; border:none; cursor:pointer; }
  </style>
</head>
<body>
  <h2 style="text-align:center;">Daftar Kontak Mitra (Admin)</h2>
  <p style="text-align:center;">
    <a href="tambah_kontak.php" class="btn add">+ Tambah Kontak</a>
    <a href="dashboard.php" class="btn back">← Kembali</a>
  </p>

  <!-- Search -->
  <div style="text-align:center;">
    <input type="text" id="searchInput" placeholder="Cari kontak...">
  </div>

  <table id="kontakTable">
    <tr>
      <th>ID</th>
      <th>Nama Perusahaan</th>
      <th>Nama PIC</th>
      <th>No Telp</th>
      <th>Email</th>
      <th>Aksi</th>
    </tr>
    <?php while($row = $result->fetch_assoc()): ?>
    <tr>
      <td><?= $row['id'] ?></td>
      <td><?= htmlspecialchars($row['nama_perusahaan']) ?></td>
      <td><?= htmlspecialchars($row['nama_pic']) ?></td>
      <td><?= htmlspecialchars($row['no_telp']) ?></td>
      <td><?= htmlspecialchars($row['email']) ?></td>
      <td>
        <a href="edit_kontak.php?id=<?= $row['id'] ?>" class="btn edit">Edit</a>
        <a href="delete_kontak.php?id=<?= $row['id'] ?>" class="btn delete" onclick="return confirm('Yakin hapus kontak ini?')">Delete</a>
      </td>
    </tr>
    <?php endwhile; ?>
  </table>

  <script>
    const searchInput = document.getElementById("searchInput");
    const table = document.getElementById("kontakTable");
    const rows = table.getElementsByTagName("tr");

    searchInput.addEventListener("keyup", function() {
      let filter = searchInput.value.toLowerCase();
      for (let i = 1; i < rows.length; i++) {
        let rowText = rows[i].innerText.toLowerCase();
        rows[i].style.display = rowText.includes(filter) ? "" : "none";
      }
    });
  </script>
</body>
</html>
