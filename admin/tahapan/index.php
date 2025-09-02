<?php
include "../../views/header.php";
include "db.php";

// Menampilkan pesan notifikasi
$message = '';
$messageType = '';

if (isset($_GET['success'])) {
    // ... (Logika notifikasi tidak berubah)
}
if (isset($_GET['error'])) {
    // ... (Logika notifikasi tidak berubah)
}

// =================================================================
// FUNGSI BARU YANG LEBIH PINTAR UNTUK MENCOCOKKAN SEMUA JENIS MITRA
// =================================================================
function getBadgeClass($type, $value) {
    if ($type === 'jenis') {
        if (empty($value)) return "secondary";
        $value_lower = strtolower($value); // Ubah ke huruf kecil agar tidak case-sensitive

        if (str_contains($value_lower, 'kementerian') || str_contains($value_lower, 'lembaga')) return 'success';
        if (str_contains($value_lower, 'pemerintah')) return 'primary';
        if (str_contains($value_lower, 'swasta') || str_contains($value_lower, 'perusahaan')) return 'dark';
        if (str_contains($value_lower, 'portal')) return 'secondary';
        if (str_contains($value_lower, 'universitas')) return 'warning';
        if (str_contains($value_lower, 'asosiasi') || str_contains($value_lower, 'komunitas')) return 'info';
        
        return 'secondary'; // Default untuk jenis lain
    }

    if ($type === 'status') {
         if (empty($value)) return "secondary";
         $status_map = [
            "Signed" => "success", "Not Available" => "secondary", "Drafting/In Progress" => "warning", 
            "Implemented" => "success", "In Progress" => "warning", "Not Yet" => "secondary"
         ];
         return $status_map[$value] ?? 'secondary';
    }

    return 'secondary';
}

function formatDate($date) {
    return $date ? date('d M Y', strtotime($date)) : '-';
}

function formatValue($value) {
    return htmlspecialchars($value ?: '-');
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Mitra Kerjasama</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root { --bs-blue-dark: #0a3d62; --bs-blue-light: #3c6382; --bs-gray: #f5f7fa; }
        body { font-family: 'Poppins', sans-serif; background-color: var(--bs-gray); }
        .main-container { background-color: white; border-radius: 1rem; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08); padding: 2rem; margin-top: -4rem; position: relative; z-index: 2; }
        .page-title { color: var(--bs-blue-dark); font-weight: 700; }
        .stats-card { background: linear-gradient(135deg, var(--bs-blue-dark) 0%, var(--bs-blue-light) 100%); color: white; border-radius: 0.75rem; padding: 1.5rem; text-align: center; }
        .controls-section { background-color: var(--bs-gray); border-radius: 0.75rem; padding: 1.5rem; }
        .table thead th { background-color: var(--bs-blue-dark); color: white; text-align: center; vertical-align: middle; }
        .table tbody tr:hover { transform: scale(1.015); box-shadow: 0 5px 15px rgba(0,0,0,0.1); background-color: #e9ecef; }
        .modal-header { background: linear-gradient(135deg, var(--bs-blue-dark) 0%, var(--bs-blue-light) 100%); color: white; }
        .detail-section { background-color: var(--bs-gray); border-radius: 0.5rem; padding: 1rem; margin-bottom: 1rem; }
        .detail-section h6 { font-weight: 700; color: var(--bs-blue-dark); border-bottom: 1px solid #ccc; padding-bottom: 0.5rem; margin-bottom: 1rem; }
        .detail-label { font-weight: 600; color: #555; }
    </style>
</head>
<body>
<div class="container my-5">
    <div class="main-container">
        <div class="text-center mb-4">
            <h1 class="page-title"><i class="bi bi-diagram-3-fill"></i> Manajemen Tahapan Kerjasama</h1>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-<?= $messageType; ?> alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <?php
        $stats = $conn->query("SELECT COUNT(*) as total, SUM(tandai) as prioritas FROM tahapan_kerjasama")->fetch_assoc();
        ?>
        <div class="row mb-4">
            <div class="col-md-6 mb-3 mb-md-0"><div class="stats-card"><h3><?= $stats['total'] ?? 0 ?></h3><p class="mb-0">Total Mitra</p></div></div>
            <div class="col-md-6"><div class="stats-card"><h3><?= $stats['prioritas'] ?? 0 ?></h3><p class="mb-0">Mitra Prioritas</p></div></div>
        </div>

        <div class="controls-section mb-4">
            <div class="row g-3 align-items-center">
                <div class="col-md-4"><input type="text" id="searchInput" class="form-control" placeholder="Cari nama mitra..."></div>
                <div class="col-md-4">
                    <select id="filterJenis" class="form-select">
                        <option value="">Semua Jenis Mitra</option>
                        <option value="Kementerian/Lembaga">Kementerian/Lembaga</option>
                        <option value="Pemerintah Daerah">Pemerintah Daerah</option>
                        <option value="Swasta/Perusahaan">Swasta/Perusahaan</option>
                        <option value="Job Portal">Job Portal</option>
                        <option value="Universitas">Universitas</option>
                        <option value="Asosiasi/Komunitas">Asosiasi/Komunitas</option>
                    </select>
                </div>
                <div class="col-md-4 text-end"><a href="create.php" class="btn btn-primary w-100"><i class="bi bi-plus-circle"></i> Tambah Mitra Baru</a></div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                 <thead>
                    <tr><th>No</th><th>Nama Mitra</th><th>Jenis</th><th>Status MoU</th><th>Status PKS</th><th>Aksi</th></tr>
                </thead>
                <tbody id="mitraTableBody">
                <?php
                $result = $conn->query("SELECT * FROM tahapan_kerjasama ORDER BY tandai DESC, id DESC");
                if ($result && $result->num_rows > 0):
                    $no = 1;
                    while ($row = $result->fetch_assoc()):
                ?>
                    <tr data-bs-toggle="modal" data-bs-target="#detailModal<?= $row['id'] ?>" style="cursor:pointer;" data-nama="<?= strtolower(htmlspecialchars($row['nama_mitra'])) ?>" data-jenis="<?= htmlspecialchars($row['jenis_mitra']) ?>">
                        <td class="text-center fw-bold"><?= $no++ ?></td>
                        <td>
                            <div class="fw-bold"><?= htmlspecialchars($row['nama_mitra']) ?></div>
                            <small class="text-muted"><?= htmlspecialchars($row['sumber_usulan'] ?: 'Sumber tidak diketahui') ?></small>
                            <?php if ($row['tandai']): ?><span class="badge bg-warning text-dark ms-2">Prioritas</span><?php endif; ?>
                        </td>
                        <td class="text-center">
                            <?php 
                                $jenis_mitra = $row['jenis_mitra'];
                                $badge_class = getBadgeClass('jenis', $jenis_mitra);
                                $display_text = !empty($jenis_mitra) ? htmlspecialchars($jenis_mitra) : 'N/A';
                            ?>
                            <span class="badge rounded-pill text-bg-<?= $badge_class ?>"><?= $display_text ?></span>
                        </td>
                        <td class="text-center"><span class="badge rounded-pill text-bg-<?= getBadgeClass('status', $row['status_kesepahaman']) ?>"><?= htmlspecialchars($row['status_kesepahaman'] ?: 'N/A') ?></span></td>
                        <td class="text-center"><span class="badge rounded-pill text-bg-<?= getBadgeClass('status', $row['status_pks']) ?>"><?= htmlspecialchars($row['status_pks'] ?: 'N/A') ?></span></td>
                        <td class="text-center" onclick="event.stopPropagation();">
                            <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil-fill"></i></a>
                            <a href="delete.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Anda yakin ingin menghapus data ini?')"><i class="bi bi-trash-fill"></i></a>
                        </td>
                    </tr>
                <?php endwhile; else: ?>
                    <tr><td colspan="6" class="text-center p-5">Belum ada data mitra.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php
if ($result && $result->num_rows > 0) {
    $result->data_seek(0);
    while($row = $result->fetch_assoc()):
?>
<div class="modal fade" id="detailModal<?= $row['id'] ?>" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-info-circle-fill"></i> Detail Mitra: <?= htmlspecialchars($row['nama_mitra']) ?></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="detail-section"><h6><i class="bi bi-building"></i> Informasi Dasar</h6><div class="row"><div class="col-md-4"><p><span class="detail-label">Jenis Mitra:</span><br><?= formatValue($row['jenis_mitra']) ?></p></div><div class="col-md-4"><p><span class="detail-label">Sumber Usulan:</span><br><?= formatValue($row['sumber_usulan']) ?></p></div><div class="col-md-4"><p><span class="detail-label">Prioritas:</span><br><?= $row['tandai'] ? 'Ya' : 'Tidak' ?></p></div></div></div>
                <div class="detail-section"><h6><i class="bi bi-handshake"></i> Tahap Kesepahaman (MoU)</h6><div class="row"><div class="col-md-4"><p><span class="detail-label">Status:</span><br><?= formatValue($row['status_kesepahaman']) ?></p></div><div class="col-md-4"><p><span class="detail-label">Nomor:</span><br><?= formatValue($row['nomor_kesepahaman']) ?></p></div><div class="col-md-4"><p><span class="detail-label">Tanggal:</span><br><?= formatDate($row['tanggal_kesepahaman']) ?></p></div><div class="col-md-4"><p><span class="detail-label">Status Pelaksanaan:</span><br><?= formatValue($row['status_pelaksanaan_kesepahaman']) ?></p></div><div class="col-md-4"><p><span class="detail-label">Rencana Pertemuan:</span><br><?= formatDate($row['rencana_pertemuan_kesepahaman']) ?></p></div><div class="col-md-12"><p><span class="detail-label">Ruang Lingkup:</span><br><?= nl2br(formatValue($row['ruanglingkup_kesepahaman'])) ?></p></div><div class="col-md-12"><p><span class="detail-label">Rencana Kolaborasi:</span><br><?= nl2br(formatValue($row['rencana_kolaborasi_kesepahaman'])) ?></p></div><div class="col-md-12"><p><span class="detail-label">Status/Progres:</span><br><?= nl2br(formatValue($row['status_progres_kesepahaman'])) ?></p></div><div class="col-md-12"><p><span class="detail-label">Tindak Lanjut:</span><br><?= nl2br(formatValue($row['tindaklanjut_kesepahaman'])) ?></p></div><div class="col-md-12"><p><span class="detail-label">Keterangan:</span><br><?= formatValue($row['keterangan_kesepahaman']) ?></p></div></div></div>
                <div class="detail-section"><h6><i class="bi bi-file-earmark-text"></i> Tahap PKS</h6><div class="row"><div class="col-md-4"><p><span class="detail-label">Status:</span><br><?= formatValue($row['status_pks']) ?></p></div><div class="col-md-4"><p><span class="detail-label">Nomor:</span><br><?= formatValue($row['nomor_pks']) ?></p></div><div class="col-md-4"><p><span class="detail-label">Tanggal:</span><br><?= formatDate($row['tanggal_pks']) ?></p></div><div class="col-md-4"><p><span class="detail-label">Status Pelaksanaan:</span><br><?= formatValue($row['status_pelaksanaan_pks']) ?></p></div><div class="col-md-4"><p><span class="detail-label">Rencana Pertemuan:</span><br><?= formatDate($row['rencana_pertemuan_pks']) ?></p></div><div class="col-md-12"><p><span class="detail-label">Ruang Lingkup:</span><br><?= nl2br(formatValue($row['ruanglingkup_pks'])) ?></p></div><div class="col-md-12"><p><span class="detail-label">Status/Progres:</span><br><?= nl2br(formatValue($row['status_progres_pks'])) ?></p></div><div class="col-md-12"><p><span class="detail-label">Tindak Lanjut:</span><br><?= nl2br(formatValue($row['tindaklanjut_pks'])) ?></p></div><div class="col-md-12"><p><span class="detail-label">Keterangan:</span><br><?= formatValue($row['keterangan_pks']) ?></p></div></div></div>
            </div>
            <div class="modal-footer"><a href="export_excel.php?id=<?= $row['id'] ?>" class="btn btn-success"><i class="bi bi-file-earmark-excel-fill"></i> Export Excel</a><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button></div>
        </div>
    </div>
</div>
<?php
    endwhile;
}
?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const filterJenis = document.getElementById('filterJenis');
        const tableBody = document.getElementById('mitraTableBody');
        const rows = tableBody.getElementsByTagName('tr');
        function filterTable() {
            const searchFilter = searchInput.value.toLowerCase();
            const jenisFilter = filterJenis.value;
            for (let i = 0; i < rows.length; i++) {
                if(rows[i].children.length < 2) continue;
                const nama = rows[i].dataset.nama || '';
                const jenis = rows[i].dataset.jenis || '';
                const showByName = nama.includes(searchFilter);
                const showByJenis = jenisFilter === '' || jenis === jenisFilter;
                rows[i].style.display = (showByName && showByJenis) ? '' : 'none';
            }
        }
        searchInput.addEventListener('keyup', filterTable);
        filterJenis.addEventListener('change', filterTable);
    });
</script>
</body>
</html>