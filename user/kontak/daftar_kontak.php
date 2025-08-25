<?php
$conn = new mysqli("localhost", "root", "", "latsar_db");
if ($conn->connect_error) die("Koneksi gagal: " . $conn->connect_error);

$result = $conn->query("SELECT * FROM kontak_mitra ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Daftar Kontak Mitra (User)</title>
  <style>
    table { width: 100%; border-collapse: collapse; margin:20px auto; }
    th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
    th { background: #27ae60; color: white; }
    a.btn { padding:6px 12px; border-radius:4px; text-decoration:none; margin-right:5px; }
    .back { background:#7f8c8d; color:white; }
    #searchBox { padding:6px; width:250px; }
    #searchBtn { padding:6px 12px; background:#2ecc71; color:white; border:none; border-radius:4px; cursor:pointer; }
  </style>
</head>
<body>
  <h2 style="text-align:center;">Daftar Kontak Mitra (User)</h2>
  <p style="text-align:center;">
    <a href="index.php" class="btn back">← Kembali</a>
  </p>

  <!-- Form Search -->
  <div style="text-align:center; margin-bottom:15px;">
    <input type="text" id="searchBox" placeholder="Cari kontak...">
    <button id="searchBtn">Cari</button>
  </div>

  <table id="contactTable">
    <tr>
      <th>ID</th>
      <th>Nama Perusahaan</th>
      <th>Nama PIC</th>
      <th>No Telp</th>
      <th>Email</th>
    </tr>
    <?php while($row = $result->fetch_assoc()): ?>
    <tr>
      <td><?= $row['id'] ?></td>
      <td><?= htmlspecialchars($row['nama_perusahaan']) ?></td>
      <td><?= htmlspecialchars($row['nama_pic']) ?></td>
      <td><?= htmlspecialchars($row['no_telp']) ?></td>
      <td><?= htmlspecialchars($row['email']) ?></td>
    </tr>
    <?php endwhile; ?>
  </table>

  <script>
    const searchBox = document.getElementById('searchBox');
    const table = document.getElementById('contactTable');
    const rows = table.getElementsByTagName('tr');

    searchBox.addEventListener('keyup', function() {
      let filter = searchBox.value.toLowerCase();
      for (let i = 1; i < rows.length; i++) {
        let cells = rows[i].getElementsByTagName('td');
        let match = false;
        for (let j = 0; j < cells.length; j++) {
          if (cells[j]) {
            let text = cells[j].textContent || cells[j].innerText;
            if (text.toLowerCase().indexOf(filter) > -1) {
              match = true;
              break;
            }
          }
        }
        rows[i].style.display = match ? "" : "none";
      }
    });
  </script>
</body>
</html>
