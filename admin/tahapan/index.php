<?php
include "../../views/header.php";
include "db.php";

// ====== BAGIAN HANDLER DOWNLOAD ======
if (isset($_GET['id']) && isset($_GET['file'])) {
    $id = intval($_GET['id']);
    $fileField = $_GET['file']; // contoh: file1, file2, file3

    if (!in_array($fileField, ['file1','file2','file3'])) {
        die("Akses ditolak.");
    }

    // Ambil data file dari DB
    $stmt = $conn->prepare("SELECT $fileField FROM tahapan_kerjasama WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($filename);
    $stmt->fetch();
    $stmt->close();

    if ($filename) {
        $filePath = __DIR__ . "latsar/admin/tahapan/upload/" . $filename;

        if (file_exists($filePath)) {
            if (!isset($_GET['download'])) {
                // Mode view
                header("Content-Type: application/octet-stream");
                readfile($filePath);
                exit;
            } else {
                // Mode download
                header("Content-Description: File Transfer");
                header("Content-Type: application/octet-stream");
                header("Content-Disposition: attachment; filename=\"" . basename($filename) . "\"");
                header("Content-Length: " . filesize($filePath));
                flush();
                readfile($filePath);
                exit;
            }
        } else {
            die("⚠ File tidak ditemukan di server.");
        }
    } else {
        die("⚠ File tidak ada di database.");
    }
}
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center">
        <h3>📑 Daftar Mitra</h3>
        <a href="create.php" class="btn btn-success">
            ➕ Tambahkan Mitra
        </a>
    </div>

    <!-- Search & Filter -->
    <div class="row mt-3 mb-3">
        <div class="col-md-6">
            <input type="text" id="searchInput" class="form-control" placeholder="🔍 Cari nama mitra...">
        </div>
        <div class="col-md-4">
            <select id="filterJenis" class="form-select">
                <option value="">-- Semua Jenis Mitra --</option>
                <option value="Kementerian/Lembaga">Kementerian/Lembaga</option>
                <option value="Pemerintah Daerah">Pemerintah Daerah</option>
                <option value="Mitra Pembangunan">Mitra Pembangunan</option>
                <option value="Swasta/Perusahaan">Swasta/Perusahaan</option>
            </select>
        </div>
    </div>

    <!-- Tabel Ringkas -->
    <table class="table table-bordered table-hover mt-2" id="mitraTable">
        <thead class="table-light">
            <tr>
                <th>No</th>
                <th>Nama Mitra</th>
                <th>Jenis Mitra</th>
                <th>Tanggal MoU</th>
            </tr>
        </thead>
        <tbody>
        <?php
        function getFileIcon($filename) {
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            switch($ext) {
                case "pdf": return '<i class="bi bi-file-earmark-pdf text-danger" style="font-size:40px;"></i>';
                case "doc":
                case "docx": return '<i class="bi bi-file-earmark-word text-primary" style="font-size:40px;"></i>';
                case "xls":
                case "xlsx": return '<i class="bi bi-file-earmark-excel text-success" style="font-size:40px;"></i>';
                case "ppt":
                case "pptx": return '<i class="bi bi-file-earmark-ppt text-warning" style="font-size:40px;"></i>';
                default: return '<i class="bi bi-file-earmark-text text-secondary" style="font-size:40px;"></i>';
            }
        }

        $result = $conn->query("SELECT * FROM tahapan_kerjasama ORDER BY id DESC");
        if ($result && $result->num_rows > 0):
            $no = 1;
            while ($row = $result->fetch_assoc()):
                switch($row['jenis_mitra']){
                    case "Kementerian/Lembaga": $badge = "success"; break;
                    case "Pemerintah Daerah": $badge = "primary"; break;
                    case "Mitra Pembangunan": $badge = "warning"; break;
                    case "Swasta/Perusahaan": $badge = "secondary"; break;
                    default: $badge = "dark";
                }
        ?>
            <tr style="cursor:pointer;" 
                data-bs-toggle="modal" 
                data-bs-target="#detailModal<?= $row['id']; ?>"
                data-nama="<?= strtolower($row['nama_mitra']); ?>"
                data-jenis="<?= $row['jenis_mitra']; ?>">
                <td><?= $no++; ?></td>
                <td><?= htmlspecialchars($row['nama_mitra']); ?></td>
                <td><span class="badge bg-<?= $badge; ?>"><?= htmlspecialchars($row['jenis_mitra']); ?></span></td>
                <td><?= htmlspecialchars($row['tanggal_mou']); ?></td>
            </tr>

            <!-- Modal Detail -->
            <div class="modal fade" id="detailModal<?= $row['id']; ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header bg-light">
                            <h5 class="modal-title">Detail Mitra: <?= htmlspecialchars($row['nama_mitra']); ?></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p><strong>Jenis Mitra:</strong> <?= htmlspecialchars($row['jenis_mitra']); ?></p>
                            <p><strong>Sumber Usulan:</strong> <?= htmlspecialchars($row['sumber_usulan']); ?></p>
                            <p><strong>Status MoU:</strong> <?= htmlspecialchars($row['status_mou']); ?></p>
                            <p><strong>Nomor MoU:</strong> <?= htmlspecialchars($row['nomor_mou']); ?></p>
                            <p><strong>Tanggal MoU:</strong> <?= htmlspecialchars($row['tanggal_mou']); ?></p>
                            <p><strong>Ruang Lingkup MoU:</strong><br><?= nl2br(htmlspecialchars($row['ruang_lingkup_mou'])); ?></p>
                            <p><strong>Status Pelaksanaan:</strong> <?= htmlspecialchars($row['status_pelaksanaan']); ?></p>
                            <p><strong>Rencana Pertemuan:</strong> <?= htmlspecialchars($row['rencana_pertemuan']); ?></p>
                            <p><strong>Rencana Kolaborasi:</strong><br><?= nl2br(htmlspecialchars($row['rencana_kolaborasi'])); ?></p>
                            <p><strong>Status/Progres:</strong><br><?= nl2br(htmlspecialchars($row['status_progres'])); ?></p>
                            <p><strong>Tindak Lanjut:</strong><br><?= nl2br(htmlspecialchars($row['tindak_lanjut'])); ?></p>
                            <p><strong>Status PKS:</strong> <?= htmlspecialchars($row['status_pks']); ?></p>
                            <p><strong>Ruang Lingkup PKS:</strong><br><?= nl2br(htmlspecialchars($row['ruanglingkup_pks'])); ?></p>
                            <p><strong>Nomor KB/PKS:</strong> <?= htmlspecialchars($row['nomor_kb_pks']); ?></p>
                            <p><strong>Tanggal KB/PKS:</strong> <?= htmlspecialchars($row['tanggal_kb_pks']); ?></p>
                            <p><strong>Keterangan:</strong><br><?= nl2br(htmlspecialchars($row['keterangan'])); ?></p>
                            <p><strong>File:</strong></p>
                            <div class="d-flex flex-wrap gap-3">
                                <?php for ($i=1;$i<=3;$i++): ?>
                                    <?php if (!empty($row["file$i"])): ?>
                                        <?php $filename = htmlspecialchars($row["file$i"]); ?>
                                        <div class="card p-2 text-center" style="width:150px;">
                                            <div>
                                                <?= getFileIcon($filename); ?>
                                            </div>
                                            <small class="d-block mt-1 text-truncate"><?= basename($filename); ?></small>
                                            <div class="mt-2 d-flex justify-content-center gap-2">
                                                <a href="?id=<?= $row['id']; ?>&file=file<?= $i ?>" target="_blank" class="btn btn-info btn-sm">👁 Lihat</a>
                                                <a href="?id=<?= $row['id']; ?>&file=file<?= $i ?>&download=1" class="btn btn-success btn-sm">📥</a>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                <?php endfor; ?>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <a href="edit.php?id=<?= $row['id']; ?>" class="btn btn-primary btn-sm">✏ Edit</a>
                            <a href="export_excel.php?id=<?= $row['id']; ?>" class="btn btn-primary btn-sm">📊 Export Excel</a>
                            <a href="delete.php?id=<?= $row['id']; ?>" onclick="return confirm('Yakin hapus?')" class="btn btn-primary btn-sm">🗑 Hapus</a>
                            <button type="button" class="btn btn-primary btn-sm" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; else: ?>
            <tr><td colspan="4" class="text-center">Belum ada data</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('searchInput').addEventListener('keyup', function() {
    let searchValue = this.value.toLowerCase();
    document.querySelectorAll('#mitraTable tbody tr').forEach(function(row) {
        let nama = row.getAttribute('data-nama');
        row.style.display = nama.includes(searchValue) ? '' : 'none';
    });
});
document.getElementById('filterJenis').addEventListener('change', function() {
    let filterValue = this.value;
    document.querySelectorAll('#mitraTable tbody tr').forEach(function(row) {
        let jenis = row.getAttribute('data-jenis');
        row.style.display = (filterValue === '' || jenis === filterValue) ? '' : 'none';
    });
});
</script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
