<?php
session_start();
$conn = new mysqli("localhost", "root", "", "jejaring_db");
if ($conn->connect_error) die("Koneksi gagal: " . $conn->connect_error);

$result = $conn->query("SELECT * FROM kontak_mitra ORDER BY id DESC");

// panggil header
include __DIR__ . "/../../views/header.php";
?>

<style>
  body {
    font-family: "Segoe UI", sans-serif;
    background: #f5f7fa;
    color: #333;
  }

  h2 {
    text-align: center;
    margin: 15px 0;
    color: #222;
    font-size: 20px;
    font-weight: 600;
  }

  /* Bar atas: kiri */
  .top-bar {
    display: flex;
    justify-content: flex-start; /* pindah ke kiri */
    align-items: center;
    gap: 10px;
    margin: 0 20px 20px;
  }

  .btn {
    display: inline-block;
    padding: 7px 14px;
    border-radius: 8px;
    text-decoration: none;
    font-size: 13px;
    transition: 0.2s;
  }
  .btn.add { background: #4CAF50; color: #fff; }
  .btn.add:hover { background: #45a049; }
  .btn.edit { background: #2196F3; color: #fff; }
  .btn.edit:hover { background: #1976d2; }
  .btn.delete { background: #f44336; color: #fff; }
  .btn.delete:hover { background: #d32f2f; }

  /* Search box dengan ikon */
  .search-box {
    position: relative;
    display: inline-block;
  }
  .search-box input {
    padding: 7px 28px 7px 28px; /* ruang untuk ikon */
    border: 1px solid #ccc;
    border-radius: 8px;
    font-size: 13px;
    width: 180px;
  }
  .search-box i {
    position: absolute;
    top: 50%;
    left: 8px;
    transform: translateY(-50%);
    color: #888;
    font-size: 14px;
  }

  /* Card layout */
  .card-container {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 16px;
    padding: 0 20px 40px;
  }

  .contact-card {
    background: #fff;
    border: 1px solid #e0e0e0;
    border-radius: 16px;
    padding: 20px;
    box-shadow: 0 6px 16px rgba(0,0,0,0.1);
    transition: transform 0.2s, box-shadow 0.2s;
  }
  .contact-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.15);
  }

  .contact-card h3 {
    margin: 0 0 8px;
    color: #111;
    font-size: 18px;
  }
  .contact-card p {
    margin: 4px 0;
    font-size: 14px;
    color: #555;
  }

  .card-actions {
    margin-top: 12px;
  }
</style>

<h2>Kontak Mitra</h2>

<div class="top-bar">
  <a href="tambah_kontak.php" class="btn add">+ Tambah Kontak</a>
  <div class="search-box">
    <i class="fas fa-search"></i>
    <input type="text" id="searchInput" placeholder="Cari...">
  </div>
</div>

<div class="card-container" id="kontakContainer">
  <?php while($row = $result->fetch_assoc()): ?>
    <div class="contact-card">
      <h3><?= htmlspecialchars($row['nama_perusahaan']) ?></h3>
      <p><strong>PIC:</strong> <?= htmlspecialchars($row['nama_pic']) ?></p>
      <p><strong>No. Telp:</strong> <?= htmlspecialchars($row['no_telp']) ?></p>
      <p><strong>Email:</strong> <?= htmlspecialchars($row['email']) ?></p>
      <div class="card-actions">
        <a href="edit_kontak.php?id=<?= $row['id'] ?>" class="btn edit">Edit</a>
        <a href="delete_kontak.php?id=<?= $row['id'] ?>" class="btn delete"
           onclick="return confirm('Yakin hapus kontak ini?')">Delete</a>
      </div>
    </div>
  <?php endwhile; ?>
</div>

<script>
  const searchInput = document.getElementById("searchInput");
  const cards = document.querySelectorAll(".contact-card");

  searchInput.addEventListener("keyup", function() {
    let filter = searchInput.value.toLowerCase();
    cards.forEach(card => {
      let text = card.innerText.toLowerCase();
      card.style.display = text.includes(filter) ? "block" : "none";
    });
  });
</script>

<!-- Font Awesome untuk ikon search -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<?php
// tutup main + body + html dari header.php
echo "</main></body></html>";
?>
