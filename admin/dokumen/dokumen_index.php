<?php
session_start();
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

// panggil header
include __DIR__ . "/../../views/header.php";
?>

<h2 style="margin:20px 0;">📂 Daftar Dokumen</h2>

<div class="top-bar" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:15px;">
    <form class="search-box" method="GET" style="display:flex;gap:6px;">
        <input type="text" name="search" placeholder="Cari judul..." 
               value="<?= htmlspecialchars($search) ?>" 
               style="padding:6px 10px; border:1px solid #ccc; border-radius:4px;">
        <button type="submit" 
                style="padding:6px 12px; background:#007bff; color:#fff; border:none; border-radius:4px; cursor:pointer;">
            Search
        </button>
    </form>
    <a href="dokumen_tambah.php" class="btn btn-add" 
       style="background:#28a745; color:#fff; padding:6px 12px; border-radius:4px; text-decoration:none;">
       + Tambah Dokumen
    </a>
</div>

<table style="width:100%; border-collapse:collapse; background:#fff; border-radius:8px; overflow:hidden;
              box-shadow:0 2px 6px rgba(0,0,0,0.1);">
    <tr>
        <th style="padding:10px; background:#007bff; color:#fff;">Judul</th>
        <th style="padding:10px; background:#007bff; color:#fff;">Jenis</th>
        <th style="padding:10px; background:#007bff; color:#fff;">Tanggal</th>
        <th style="padding:10px; background:#007bff; color:#fff;">File</th>
        <th style="padding:10px; background:#007bff; color:#fff;">Aksi</th>
    </tr>
    <?php while($row = $result->fetch_assoc()): ?>
        <tr style="border-bottom:1px solid #ddd;">
            <td style="padding:10px;"><?= htmlspecialchars($row['judul']) ?></td>
            <td style="padding:10px;">
                <?php if ($row['jenis'] == 'word'): ?>
                    <img src="icons/word.png" class="icon" style="width:20px;vertical-align:middle;margin-right:5px;"> Word
                <?php elseif ($row['jenis'] == 'excel'): ?>
                    <img src="icons/excel.png" class="icon" style="width:20px;vertical-align:middle;margin-right:5px;"> Excel
                <?php elseif ($row['jenis'] == 'pdf'): ?>
                    <img src="icons/pdf.png" class="icon" style="width:20px;vertical-align:middle;margin-right:5px;"> PDF
                <?php endif; ?>
            </td>
            <td style="padding:10px;"><?= $row['tanggal'] ?></td>
            <td style="padding:10px;">
                <a href="<?= $row['file_path'] ?>" class="btn btn-download" download
                   style="background:#17a2b8; color:#fff; padding:6px 12px; border-radius:4px; text-decoration:none;">
                   Download
                </a>
            </td>
            <td style="padding:10px;">
                <button class="btn btn-edit" 
                        style="background:#ffc107; color:black; padding:6px 12px; border:none; border-radius:4px; cursor:pointer;"
                        onclick="openEditModal(<?= $row['id'] ?>,'<?= htmlspecialchars($row['judul'],ENT_QUOTES) ?>')">
                    Edit
                </button>
                <a href="dokumen_delete.php?id=<?= $row['id'] ?>" 
                   class="btn btn-delete" 
                   style="background:#dc3545; color:#fff; padding:6px 12px; border-radius:4px; text-decoration:none;"
                   onclick="return confirm('Yakin ingin menghapus dokumen ini?')">
                   Delete
                </a>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

<!-- Modal Edit -->
<div class="modal" id="editModal" 
     style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; 
            background:rgba(0,0,0,0.6); justify-content:center; align-items:center;">
    <div class="modal-content" style="background:#fff; padding:20px; border-radius:8px; width:400px;">
        <span class="close" onclick="document.getElementById('editModal').style.display='none'"
              style="float:right; cursor:pointer; font-weight:bold;">&times;</span>
        <div class="modal-header" style="font-size:18px; margin-bottom:10px;">Edit Dokumen</div>
        <form action="dokumen_update.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" id="editId">
            <label>Judul:</label><br>
            <input type="text" name="judul" id="editJudul" required style="width:100%; padding:6px; margin-top:5px;"><br><br>
            <label>File (opsional, biarkan kosong jika tidak ganti):</label><br>
            <input type="file" name="file" style="margin-top:5px;"><br><br>
            <button type="submit" class="btn btn-add" 
                    style="background:#28a745; color:#fff; padding:6px 12px; border:none; border-radius:4px; cursor:pointer;">
                Simpan
            </button>
        </form>
    </div>
</div>

<script>
function openEditModal(id, judul) {
    document.getElementById('editId').value = id;
    document.getElementById('editJudul').value = judul;
    document.getElementById('editModal').style.display = 'flex';
}
</script>

<?php
// tutup main + body + html dari header.php
echo "</main></body></html>";
?>
