<?php
include "../../views/header.php";
include "db.php";

// Display notification messages
$message = '';
$messageType = '';

if (isset($_GET['success'])) {
    switch($_GET['success']) {
        case 'deleted':
            $message = 'Data mitra berhasil dihapus!';
            $messageType = 'success';
            break;
    }
}

if (isset($_GET['error'])) {
    switch($_GET['error']) {
        case 'invalid_id':
            $message = 'ID tidak valid!';
            $messageType = 'danger';
            break;
        case 'not_found':
            $message = 'Data tidak ditemukan!';
            $messageType = 'warning';
            break;
        case 'delete_failed':
            $message = 'Gagal menghapus data!';
            $messageType = 'danger';
            break;
    }
}

// File download handler
if (isset($_GET['id']) && isset($_GET['file'])) {
    $id = intval($_GET['id']);
    $fileField = $_GET['file'];

    if (!in_array($fileField, ['file1','file2','file3'])) {
        die("Akses ditolak.");
    }

    $stmt = $conn->prepare("SELECT $fileField FROM tahapan_kerjasama WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($filename);
    $stmt->fetch();
    $stmt->close();

    if ($filename) {
        $filePath = __DIR__ . "/upload/" . $filename;
        if (file_exists($filePath)) {
            if (!isset($_GET['download'])) {
                header("Content-Type: application/octet-stream");
                readfile($filePath);
                exit;
            } else {
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

function getFileIcon($filename) {
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    $icons = [
        "pdf" => '<i class="bi bi-file-earmark-pdf text-danger" style="font-size:40px;"></i>',
        "doc" => '<i class="bi bi-file-earmark-word text-primary" style="font-size:40px;"></i>',
        "docx" => '<i class="bi bi-file-earmark-word text-primary" style="font-size:40px;"></i>',
        "xls" => '<i class="bi bi-file-earmark-excel text-success" style="font-size:40px;"></i>',
        "xlsx" => '<i class="bi bi-file-earmark-excel text-success" style="font-size:40px;"></i>',
        "ppt" => '<i class="bi bi-file-earmark-ppt text-warning" style="font-size:40px;"></i>',
        "pptx" => '<i class="bi bi-file-earmark-ppt text-warning" style="font-size:40px;"></i>'
    ];
    return $icons[$ext] ?? '<i class="bi bi-file-earmark-text text-secondary" style="font-size:40px;"></i>';
}

function getBadgeClass($type, $value) {
    $badges = [
        'jenis' => [
            "Kementerian/Lembaga" => "success", "Pemerintah Daerah" => "primary", 
            "Mitra Pembangunan" => "warning", "Asosiasi" => "info", 
            "Perusahaan" => "secondary", "Universitas" => "dark", "Job Portal" => "primary"
        ],
        'status' => [
            "Signed" => "success", "Not Available" => "secondary", 
            "Drafting/In Progress" => "warning"
        ]
    ];
    return $badges[$type][$value] ?? "secondary";
}
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

<style>
body { background-color: #f5f7fa; font-family: 'Segoe UI', sans-serif; }
.main-container { background: white; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.08); margin: 20px auto; padding: 20px; max-width: 1400px; }
.page-title { color: #222; font-weight: 600; font-size: 20px; text-align: center; margin: 15px 0; }
.controls-section { background: #f8f9ff; border-radius: 10px; padding: 20px; margin-bottom: 20px; }
.btn-primary-custom { background: #2c5aa0; border: none; border-radius: 8px; padding: 7px 14px; color: white; }
.btn-primary-custom:hover { background: #1e3d6f; color: white; }
.form-control-custom, .form-select-custom { border: 2px solid #dee2e6; border-radius: 8px; padding: 7px 15px; font-size: 13px; }
.elegant-table { background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 3px 15px rgba(0,0,0,0.08); }
.elegant-table thead th { background: #2c5aa0; color: white; padding: 15px 12px; text-align: center; }
.elegant-table tbody tr:hover { background: #f0f7ff; }
.elegant-table tbody td { padding: 12px; text-align: center; border-bottom: 1px solid #f0f0f0; }
.badge-custom { padding: 6px 12px; border-radius: 20px; font-size: 0.8rem; color: white; }
.modal-header-custom { background: #2c5aa0; color: white; }
.modal-content-custom { border-radius: 15px; border: none; }
.file-card { background: #f8f9ff; border: 2px solid #e9ecef; border-radius: 10px; padding: 15px; width: 160px; }
.file-card:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(0,0,0,0.1); }
.btn-action { border-radius: 6px; padding: 6px 10px; margin: 2px; font-size: 0.8rem; }
.modal-xl-custom { max-width: 90%; width: 90%; }
.marked-indicator { color: #0d6efd; font-size: 1.2rem; margin-left: 8px; animation: pulse 2s infinite; }
@keyframes pulse { 0% { opacity: 1; } 50% { opacity: 0.6; } 100% { opacity: 1; } }
</style>

<div class="main-container">
    <h2 class="page-title">Daftar Mitra</h2>
    
    <!-- Notification Messages -->
    <?php if ($message): ?>
        <div class="alert alert-<?= $messageType; ?> alert-dismissible fade show" role="alert">
            <i class="bi bi-<?= $messageType == 'success' ? 'check-circle' : ($messageType == 'danger' ? 'exclamation-triangle' : 'info-circle'); ?> me-2"></i>
            <?= htmlspecialchars($message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <div class="controls-section">
        <div class="row align-items-center">
            <div class="col-md-4">
                <input type="text" id="searchInput" class="form-control form-control-custom" placeholder="Cari nama mitra...">
            </div>
            <div class="col-md-4">
                <select id="filterJenis" class="form-select form-select-custom">
                    <option value="">Semua Jenis Mitra</option>
                    <option value="Kementerian/Lembaga">Kementerian/Lembaga</option>
                    <option value="Pemerintah Daerah">Pemerintah Daerah</option>
                    <option value="Mitra Pembangunan">Mitra Pembangunan</option>
                </select>
            </div>
            <div class="col-md-4 text-end">
                <a href="create.php" class="btn btn-primary-custom">Tambahkan Mitra</a>
            </div>
        </div>
    </div>

    <table class="table elegant-table" id="mitraTable">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Mitra</th>
                <th>Jenis Mitra</th>
                <th>Status Kesepahaman</th>
                <th>Tanggal Kesepahaman</th>
                <th>Status PKS</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $result = $conn->query("SELECT * FROM tahapan_kerjasama ORDER BY id DESC");
        if ($result && $result->num_rows > 0):
            $no = 1;
            while ($row = $result->fetch_assoc()):
                // Safe way to check tandai field - might not exist in older records
                $isTandai = isset($row['tandai']) ? (int)$row['tandai'] : 0;
        ?>
            <tr onclick="showModal<?= $row['id']; ?>()" style="cursor:pointer;" 
                data-nama="<?= strtolower($row['nama_mitra']); ?>"
                data-jenis="<?= $row['jenis_mitra']; ?>">
                <td><?= $no++; ?></td>
                <td>
                    <?= htmlspecialchars($row['nama_mitra']); ?>
                    <?php if ($isTandai == 1): ?>
                        <i class="bi bi-exclamation-circle-fill marked-indicator" title="Data Ditandai"></i>
                    <?php endif; ?>
                </td>
                <td><span class="badge bg-<?= getBadgeClass('jenis', $row['jenis_mitra']); ?>"><?= htmlspecialchars($row['jenis_mitra']); ?></span></td>
                <td><span class="badge bg-<?= getBadgeClass('status', $row['status_kesepahaman']); ?>"><?= htmlspecialchars($row['status_kesepahaman'] ?: 'Tidak Ada'); ?></span></td>
                <td><?= htmlspecialchars($row['tanggal_kesepahaman'] ?: '-'); ?></td>
                <td><span class="badge bg-<?= getBadgeClass('status', $row['status_pks']); ?>"><?= htmlspecialchars($row['status_pks'] ?: 'Tidak Ada'); ?></span></td>
            </tr>

            <!-- Modal Detail -->
            <div class="modal fade" id="detailModal<?= $row['id']; ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-xl-custom modal-dialog-scrollable">
                    <div class="modal-content modal-content-custom">
                        <div class="modal-header modal-header-custom">
                            <h5 class="modal-title">
                                Detail Mitra: <?= htmlspecialchars($row['nama_mitra']); ?>
                                <?php if ($isTandai == 1): ?>
                                    <i class="bi bi-exclamation-circle-fill text-warning ms-2" title="Data Ditandai"></i>
                                <?php endif; ?>
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <?php if ($isTandai == 1): ?>
                                <div class="alert alert-info d-flex align-items-center mb-3">
                                    <i class="bi bi-info-circle-fill me-2"></i>
                                    <div>Data ini telah ditandai sebagai prioritas atau perhatian khusus</div>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Basic Info -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <p><strong>Nama Mitra:</strong> <?= htmlspecialchars($row['nama_mitra']); ?></p>
                                    <p><strong>Jenis Mitra:</strong> <?= htmlspecialchars($row['jenis_mitra']); ?></p>
                                    <p><strong>Sumber Usulan:</strong> <?= htmlspecialchars($row['sumber_usulan']); ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Status Kesepahaman:</strong> <?= htmlspecialchars($row['status_kesepahaman']); ?></p>
                                    <p><strong>Status PKS:</strong> <?= htmlspecialchars($row['status_pks']); ?></p>
                                </div>
                            </div>

                            <!-- Kesepahaman Info -->
                            <h6 class="text-primary">Informasi Kesepahaman</h6>
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <p><strong>Nomor:</strong> <?= htmlspecialchars($row['nomor_kesepahaman']); ?></p>
                                    <p><strong>Tanggal:</strong> <?= htmlspecialchars($row['tanggal_kesepahaman']); ?></p>
                                    <p><strong>Status Pelaksanaan:</strong> <?= htmlspecialchars($row['status_pelaksanaan_kesepahaman']); ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Ruang Lingkup:</strong> <?= nl2br(htmlspecialchars($row['ruanglingkup_kesepahaman'])); ?></p>
                                    <p><strong>Rencana Kolaborasi:</strong> <?= nl2br(htmlspecialchars($row['rencana_kolaborasi_kesepahaman'])); ?></p>
                                </div>
                            </div>

                            <!-- PKS Info -->
                            <h6 class="text-success">Informasi PKS</h6>
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <p><strong>Nomor:</strong> <?= htmlspecialchars($row['nomor_pks']); ?></p>
                                    <p><strong>Tanggal:</strong> <?= htmlspecialchars($row['tanggal_pks']); ?></p>
                                    <p><strong>Status Pelaksanaan:</strong> <?= htmlspecialchars($row['status_pelaksanaan_pks']); ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Ruang Lingkup:</strong> <?= nl2br(htmlspecialchars($row['ruanglingkup_pks'])); ?></p>
                                    <p><strong>Status/Progres:</strong> <?= nl2br(htmlspecialchars($row['status_progres_pks'])); ?></p>
                                </div>
                            </div>
                            
                            <!-- Files -->
                            <h6 class="text-info">File Terlampir</h6>
                            <div class="d-flex flex-wrap gap-3 mb-3">
                                <?php for ($i=1;$i<=3;$i++): ?>
                                    <?php if (!empty($row["file$i"])): ?>
                                        <?php $filename = htmlspecialchars($row["file$i"]); ?>
                                        <div class="file-card text-center">
                                            <?= getFileIcon($filename); ?>
                                            <small class="d-block my-2 text-truncate"><?= basename($filename); ?></small>
                                            <div class="d-flex gap-1">
                                                <a href="?id=<?= $row['id']; ?>&file=file<?= $i ?>" target="_blank" 
                                                   class="btn btn-info btn-action btn-sm">Lihat</a>
                                                <a href="?id=<?= $row['id']; ?>&file=file<?= $i ?>&download=1" 
                                                   class="btn btn-success btn-action btn-sm">Download</a>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                <?php endfor; ?>
                                <?php if (empty($row['file1']) && empty($row['file2']) && empty($row['file3'])): ?>
                                    <p class="text-muted">Tidak ada file terlampir</p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <a href="edit.php?id=<?= $row['id']; ?>" class="btn btn-warning btn-action">Edit</a>
                            <a href="export_excel.php?id=<?= $row['id']; ?>" class="btn btn-success btn-action">Export Excel</a>
                            <a href="delete.php?id=<?= $row['id']; ?>" onclick="return confirmDelete('<?= htmlspecialchars($row['nama_mitra']); ?>')" class="btn btn-danger btn-action">Hapus</a>
                            <button type="button" class="btn btn-secondary btn-action" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                function showModal<?= $row['id']; ?>() {
                    new bootstrap.Modal(document.getElementById('detailModal<?= $row['id']; ?>')).show();
                }
            </script>

        <?php endwhile; else: ?>
            <tr><td colspan="6" class="text-center text-muted py-5">Belum ada data mitra</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function applyFilters() {
    let searchValue = document.getElementById('searchInput').value.toLowerCase();
    let filterValue = document.getElementById('filterJenis').value;
    
    document.querySelectorAll('#mitraTable tbody tr').forEach(function(row) {
        let nama = row.getAttribute('data-nama');
        let jenis = row.getAttribute('data-jenis');
        
        if (nama && jenis) {
            let matchesSearch = nama.includes(searchValue);
            let matchesFilter = true;
            
            if (filterValue === 'Mitra Pembangunan') {
                matchesFilter = ['Asosiasi', 'Perusahaan', 'Universitas', 'Job Portal'].includes(jenis);
            } else {
                matchesFilter = (filterValue === '' || jenis === filterValue);
            }
            
            row.style.display = (matchesSearch && matchesFilter) ? '' : 'none';
        }
    });
}

function confirmDelete(namaMitra) {
    return confirm(`Yakin ingin menghapus data mitra "${namaMitra}"?\n\nTindakan ini tidak dapat dibatalkan dan akan menghapus semua file terkait.`);
}

// Auto hide alerts after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
});

document.getElementById('searchInput').addEventListener('keyup', applyFilters);
document.getElementById('filterJenis').addEventListener('change', applyFilters);
</script>