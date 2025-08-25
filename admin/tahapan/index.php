<?php include "../../views/header.php"; ?>
<?php include "db.php"; ?>
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
                            <p><strong>File:</strong><br>
                                <?php for ($i=1;$i<=3;$i++): ?>
                                    <?php if (!empty($row["file$i"])): ?>
                                        <a href="upload/<?= htmlspecialchars($row["file$i"]); ?>" target="_blank">📥 File<?= $i ?></a><br>
                                    <?php endif; ?>
                                <?php endfor; ?>
                            </p>
                        </div>
                        <div class="modal-footer">
                            <a href="edit.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-warning">✏ Edit</a>
                            <a href="delete.php?id=<?= $row['id']; ?>" onclick="return confirm('Yakin hapus?')" class="btn btn-sm btn-danger">🗑 Hapus</a>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
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
