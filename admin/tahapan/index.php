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
        $filePath = __DIR__ . "/upload/" . $filename;

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
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

<style>
    body {
    background-color: #f5f7fa;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    color: #333;
}

.main-container {
    background: white;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
    margin: 20px auto;
    padding: 20px;
    max-width: 1400px;
}

.page-title {
    color: #222;
    font-weight: 600;
    font-size: 20px;
    text-align: center;
    margin: 15px 0;
}

.controls-section {
    background: #f8f9ff;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 20px;
    border: 1px solid #e9ecef;
}

.btn-primary-custom {
    background-color: #2c5aa0;
    border: none;
    border-radius: 8px;
    padding: 7px 14px;
    font-weight: 500;
    font-size: 13px;
    color: white;
    transition: all 0.3s ease;
}

.btn-primary-custom:hover {
    background-color: #1e3d6f;
    color: white;
    transform: translateY(-1px);
}

.form-control-custom {
    border: 2px solid #dee2e6;
    border-radius: 8px;
    padding: 7px 15px;
    font-size: 13px;
    transition: all 0.3s ease;
}

.form-control-custom:focus {
    border-color: #2c5aa0;
    box-shadow: 0 0 0 0.2rem rgba(44, 90, 160, 0.25);
}

.form-select-custom {
    border: 2px solid #dee2e6;
    border-radius: 8px;
    padding: 7px 15px;
    font-size: 13px;
    transition: all 0.3s ease;
}

.form-select-custom:focus {
    border-color: #2c5aa0;
    box-shadow: 0 0 0 0.2rem rgba(44, 90, 160, 0.25);
}

.elegant-table {
    background: white;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 3px 15px rgba(0, 0, 0, 0.08);
}

.elegant-table thead th {
    background-color: #2c5aa0;
    color: white;
    font-weight: 500;
    padding: 15px 12px;
    border: none;
    text-align: center;
    font-size: 14px;
}

.elegant-table tbody tr {
    border: none;
    transition: all 0.2s ease;
}

.elegant-table tbody tr:hover {
    background-color: #f0f7ff;
}

.elegant-table tbody td {
    padding: 12px;
    border: none;
    border-bottom: 1px solid #f0f0f0;
    vertical-align: middle;
    text-align: center;
    font-size: 14px;
}

.badge-custom {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
    color: white;
}

.badge-success-custom {
    background-color: #198754;
}

.badge-primary-custom {
    background-color: #2c5aa0;
}

.badge-warning-custom {
    background-color: #fd7e14;
}

.badge-secondary-custom {
    background-color: #6c757d;
}

.badge-info-custom {
    background-color: #0dcaf0;
}

.badge-dark-custom {
    background-color: #212529;
}

.modal-header-custom {
    background-color: #2c5aa0;
    color: white;
}

.modal-content-custom {
    border-radius: 15px;
    border: none;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
}

.file-card {
    background: #f8f9ff;
    border: 2px solid #e9ecef;
    border-radius: 10px;
    transition: all 0.3s ease;
    padding: 15px;
    width: 160px;
}

.file-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    border-color: #2c5aa0;
}

.btn-action {
    border-radius: 6px;
    padding: 6px 10px;
    margin: 2px;
    font-size: 0.8rem;
    font-weight: 500;
    transition: all 0.3s ease;
    border: none;
}

.btn-action:hover {
    transform: translateY(-1px);
}

/* Modal size fix */
.modal-xl-custom {
    max-width: 90%;
    width: 90%;
}

@media (max-width: 768px) {
    .modal-xl-custom {
        max-width: 95%;
        width: 95%;
    }
}
</style>

<div class="main-container">
    <h2 class="page-title">Daftar Mitra</h2>
    
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
                <a href="create.php" class="btn btn-primary-custom">
                    Tambahkan Mitra
                </a>
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
                // Badge untuk jenis mitra
                switch($row['jenis_mitra']){
                    case "Kementerian/Lembaga": $badge = "success-custom"; break;
                    case "Pemerintah Daerah": $badge = "primary-custom"; break;
                    case "Mitra Pembangunan": $badge = "warning-custom"; break;
                    case "Asosiasi": $badge = "info-custom"; break;
                    case "Perusahaan": $badge = "secondary-custom"; break;
                    case "Universitas": $badge = "dark-custom"; break;
                    case "Job Portal": $badge = "primary-custom"; break;
                    default: $badge = "secondary-custom";
                }
                
                // Badge untuk status kesepahaman
                switch($row['status_kesepahaman']){
                    case "Signed": $badge_kesepahaman = "success-custom"; break;
                    case "Not Available": $badge_kesepahaman = "secondary-custom"; break;
                    case "Drafting/In Progress": $badge_kesepahaman = "warning-custom"; break;
                    default: $badge_kesepahaman = "secondary-custom";
                }
                
                // Badge untuk status PKS
                switch($row['status_pks']){
                    case "Signed": $badge_pks = "success-custom"; break;
                    case "Not Available": $badge_pks = "secondary-custom"; break;
                    case "Drafting/In Progress": $badge_pks = "warning-custom"; break;
                    default: $badge_pks = "secondary-custom";
                }
        ?>
            <tr onclick="showModal<?= $row['id']; ?>()" style="cursor:pointer;" 
                data-nama="<?= strtolower($row['nama_mitra']); ?>"
                data-jenis="<?= $row['jenis_mitra']; ?>">
                <td><?= $no++; ?></td>
                <td><?= htmlspecialchars($row['nama_mitra']); ?></td>
                <td><span class="badge badge-custom badge-<?= $badge; ?>"><?= htmlspecialchars($row['jenis_mitra']); ?></span></td>
                <td><span class="badge badge-custom badge-<?= $badge_kesepahaman; ?>"><?= htmlspecialchars($row['status_kesepahaman'] ?: 'Tidak Ada'); ?></span></td>
                <td><?= htmlspecialchars($row['tanggal_kesepahaman'] ?: '-'); ?></td>
                <td><span class="badge badge-custom badge-<?= $badge_pks; ?>"><?= htmlspecialchars($row['status_pks'] ?: 'Tidak Ada'); ?></span></td>
            </tr>

            <!-- Modal Detail -->
            <div class="modal fade" id="detailModal<?= $row['id']; ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-xl-custom modal-dialog-scrollable">
                    <div class="modal-content modal-content-custom">
                        <div class="modal-header modal-header-custom">
                            <h5 class="modal-title">Detail Mitra: <?= htmlspecialchars($row['nama_mitra']); ?></h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="fw-bold text-muted">Nama Mitra:</label>
                                        <p><?= htmlspecialchars($row['nama_mitra']); ?></p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="fw-bold text-muted">Jenis Mitra:</label>
                                        <p><?= htmlspecialchars($row['jenis_mitra']); ?></p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="fw-bold text-muted">Sumber Usulan:</label>
                                        <p><?= htmlspecialchars($row['sumber_usulan']); ?></p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="fw-bold text-muted">Status Kesepahaman:</label>
                                        <p><?= htmlspecialchars($row['status_kesepahaman']); ?></p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="fw-bold text-muted">Status PKS:</label>
                                        <p><?= htmlspecialchars($row['status_pks']); ?></p>
                                    </div>
                                </div>
                            </div>
                            
                            <hr class="my-4">
                            <h6 class="text-primary">Informasi Kesepahaman</h6>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="fw-bold text-muted">Nomor Kesepahaman:</label>
                                        <p><?= htmlspecialchars($row['nomor_kesepahaman']); ?></p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="fw-bold text-muted">Tanggal Kesepahaman:</label>
                                        <p><?= htmlspecialchars($row['tanggal_kesepahaman']); ?></p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="fw-bold text-muted">Status Pelaksanaan Kesepahaman:</label>
                                        <p><?= htmlspecialchars($row['status_pelaksanaan_kesepahaman']); ?></p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="fw-bold text-muted">Rencana Pertemuan:</label>
                                        <p><?= htmlspecialchars($row['rencana_pertemuan_kesepahaman']); ?></p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="fw-bold text-muted">Ruang Lingkup Kesepahaman:</label>
                                        <p><?= nl2br(htmlspecialchars($row['ruanglingkup_kesepahaman'])); ?></p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="fw-bold text-muted">Rencana Kolaborasi:</label>
                                        <p><?= nl2br(htmlspecialchars($row['rencana_kolaborasi_kesepahaman'])); ?></p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="fw-bold text-muted">Status/Progres Kesepahaman:</label>
                                        <p><?= nl2br(htmlspecialchars($row['status_progres_kesepahaman'])); ?></p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="fw-bold text-muted">Tindak Lanjut Kesepahaman:</label>
                                        <p><?= nl2br(htmlspecialchars($row['tindaklanjut_kesepahaman'])); ?></p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="fw-bold text-muted">Keterangan Kesepahaman:</label>
                                        <p><?= htmlspecialchars($row['keterangan_kesepahaman']); ?></p>
                                    </div>
                                </div>
                            </div>
                            
                            <hr class="my-4">
                            <h6 class="text-success">Informasi PKS</h6>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="fw-bold text-muted">Nomor PKS:</label>
                                        <p><?= htmlspecialchars($row['nomor_pks']); ?></p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="fw-bold text-muted">Tanggal PKS:</label>
                                        <p><?= htmlspecialchars($row['tanggal_pks']); ?></p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="fw-bold text-muted">Status Pelaksanaan PKS:</label>
                                        <p><?= htmlspecialchars($row['status_pelaksanaan_pks']); ?></p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="fw-bold text-muted">Rencana Pertemuan PKS:</label>
                                        <p><?= htmlspecialchars($row['rencana_pertemuan_pks']); ?></p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="fw-bold text-muted">Ruang Lingkup PKS:</label>
                                        <p><?= nl2br(htmlspecialchars($row['ruanglingkup_pks'])); ?></p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="fw-bold text-muted">Status/Progres PKS:</label>
                                        <p><?= nl2br(htmlspecialchars($row['status_progres_pks'])); ?></p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="fw-bold text-muted">Tindak Lanjut PKS:</label>
                                        <p><?= nl2br(htmlspecialchars($row['tindaklanjut_pks'])); ?></p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="fw-bold text-muted">Keterangan PKS:</label>
                                        <p><?= htmlspecialchars($row['keterangan_pks']); ?></p>
                                    </div>
                                </div>
                            </div>
                            
                            <hr class="my-4">
                            
                            <div class="mb-3">
                                <label class="fw-bold text-muted">File Terlampir:</label>
                                <div class="d-flex flex-wrap gap-3 mt-3">
                                    <?php for ($i=1;$i<=3;$i++): ?>
                                        <?php if (!empty($row["file$i"])): ?>
                                            <?php $filename = htmlspecialchars($row["file$i"]); ?>
                                            <div class="file-card text-center">
                                                <div class="mb-2">
                                                    <?= getFileIcon($filename); ?>
                                                </div>
                                                <small class="d-block mb-3 text-truncate" title="<?= basename($filename); ?>">
                                                    <?= basename($filename); ?>
                                                </small>
                                                <div class="d-flex justify-content-center gap-2">
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
                        </div>
                        <div class="modal-footer">
                            <a href="edit.php?id=<?= $row['id']; ?>" class="btn btn-warning btn-action">Edit</a>
                            <a href="export_excel.php?id=<?= $row['id']; ?>" class="btn btn-success btn-action">Export Excel</a>
                            <a href="delete.php?id=<?= $row['id']; ?>" onclick="return confirm('Yakin hapus data ini?')" class="btn btn-danger btn-action">Hapus</a>
                            <button type="button" class="btn btn-secondary btn-action" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                function showModal<?= $row['id']; ?>() {
                    var modal = new bootstrap.Modal(document.getElementById('detailModal<?= $row['id']; ?>'));
                    modal.show();
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
// Search functionality
document.getElementById('searchInput').addEventListener('keyup', function() {
    applyFilters();
});

// Filter functionality
document.getElementById('filterJenis').addEventListener('change', function() {
    applyFilters();
});

// Combined search and filter
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
                // Ketika Mitra Pembangunan dipilih, tampilkan semua subcategory
                matchesFilter = ['Asosiasi', 'Perusahaan', 'Universitas', 'Job Portal'].includes(jenis);
            } else {
                // Normal filter logic
                matchesFilter = (filterValue === '' || jenis === filterValue);
            }
            
            row.style.display = (matchesSearch && matchesFilter) ? '' : 'none';
        }
    });
}
</script>