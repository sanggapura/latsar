<?php
session_start();
$conn = new mysqli("localhost", "root", "", "latsar_db");
if ($conn->connect_error) die("Koneksi gagal: " . $conn->connect_error);

$result = $conn->query("SELECT * FROM kontak_mitra ORDER BY id DESC");

// panggil header
include __DIR__ . "/../../views/header.php";
?>

<h2 style="text-align:center; margin-top:20px;">Daftar Kontak Mitra (Admin)</h2>
<p style="text-align:center;">
  <a href="tambah_kontak.php" class="btn add">+ Tambah Kontak</a>
  <a href="../dashboard.php" class="btn back">← Kembali</a>
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
      <a href="delete_kontak.php?id=<?= $row['id'] ?>" class="btn delete"
         onclick="return confirm('Yakin hapus kontak ini?')">Delete</a>
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

<?php
// tutup main + body + html dari header.php
echo "</main></body></html>";
?>
