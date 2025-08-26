<?php
session_start();
$conn = new mysqli("localhost", "root", "", "jejaring_db");
if ($conn->connect_error) die("Koneksi gagal: " . $conn->connect_error);

// pencarian
$search = $_GET['search'] ?? '';
if ($search) {
    $stmt = $conn->prepare("SELECT * FROM dokumen WHERE judul LIKE ? ORDER BY tanggal DESC");
    $param = "%$search%";
    $stmt->bind_param("s", $param);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query("SELECT * FROM dokumen ORDER BY tanggal DESC");
}

// panggil header
include __DIR__ . "/../../views/header.php";
?>

<div class="container mt-4">
  <!-- Judul + tombol tambah -->
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3>📂 Manajemen Dokumen</h3>
    <a href="dokumen_tambah.php" class="btn btn-success">➕ Tambah Dokumen</a>
  </div>

  <!-- Form pencarian -->
  <form class="row g-2 mb-3" method="GET">
    <div class="col-auto">
      <input type="text" name="search" class="form-control" placeholder="Cari dokumen..." value="<?= htmlspecialchars($search) ?>">
    </div>
    <div class="col-auto">
      <button type="submit" class="btn btn-primary">🔍 Cari</button>
    </div>
    <?php if ($search): ?>
    <div class="col-auto">
      <a href="?" class="btn btn-secondary">Reset</a>
    </div>
    <?php endif; ?>
  </form>

  <!-- Tabel -->
  <table class="table table-bordered table-hover mt-2">
    <thead class="table-light">
      <tr>
        <th>Judul</th>
        <th>Jenis</th>
        <th>Tanggal</th>
        <th>File</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
    <?php if ($result->num_rows > 0): ?>
      <?php while($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($row['judul']) ?></td>
          <td><?= ucfirst($row['jenis']) ?></td>
          <td><?= date('d-m-Y', strtotime($row['tanggal'])) ?></td>
          <td>
            <a href="<?= htmlspecialchars($row['file_path']) ?>" class="btn btn-sm btn-secondary" download>⬇ Download</a>
          </td>
          <td>
            <div class="d-flex gap-2 flex-wrap">
              <button class="btn btn-sm btn-warning" onclick="openEdit(<?= $row['id'] ?>,'<?= htmlspecialchars($row['judul'],ENT_QUOTES) ?>')">✏ Edit</button>
              <a href="dokumen_delete.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus?')">🗑 Hapus</a>
            </div>
          </td>
        </tr>
      <?php endwhile; ?>
    <?php else: ?>
      <tr><td colspan="5" class="text-center">Tidak ada dokumen</td></tr>
    <?php endif; ?>
    </tbody>
  </table>
</div>

<!-- Modal Edit -->
<div class="modal" id="editModal" tabindex="-1" style="display:none;">
  <div class="modal-dialog">
    <div class="modal-content p-3">
      <div class="modal-header">
        <h5 class="modal-title">✏ Edit Dokumen</h5>
        <button type="button" class="btn-close" onclick="closeModal()"></button>
      </div>
      <div class="modal-body">
        <form action="dokumen_update.php" method="POST" enctype="multipart/form-data">
          <input type="hidden" name="id" id="editId">
          <div class="mb-3">
            <label class="form-label">Judul</label>
            <input type="text" name="judul" id="editJudul" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">File Baru (opsional)</label>
            <input type="file" name="file" class="form-control" accept=".pdf,.doc,.docx,.xls,.xlsx">
          </div>
          <div class="d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-secondary" onclick="closeModal()">Batal</button>
            <button type="submit" class="btn btn-success">Simpan</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
function openEdit(id, judul){
  document.getElementById('editId').value = id;
  document.getElementById('editJudul').value = judul;
  document.getElementById('editModal').style.display = 'flex';
  document.getElementById('editModal').classList.add("show");
}
function closeModal(){
  document.getElementById('editModal').style.display = 'none';
  document.getElementById('editModal').classList.remove("show");
}
window.onclick = function(e){
  if(e.target == document.getElementById('editModal')) closeModal();
}
</script>

<?php
echo "</main></body></html>";
?>
