<?php
include "../../views/header.php";
include "db.php";

// Display notification messages
$message = '';
$messageType = '';

if (isset($_GET['success'])) {
    switch($_GET['success']) {
        case 'created':
            $message = 'Data mitra berhasil ditambahkan!';
            $messageType = 'success';
            break;
        case 'updated':
            $message = 'Data mitra berhasil diperbarui!';
            $messageType = 'success';
            break;
        case 'deleted':
            $message = 'Data mitra berhasil dihapus!';
            $messageType = 'success';
            break;
    }
}

if (isset($_GET['error'])) {
    switch($_GET['error']) {
        case 'invalid_id':
            $message = 'ID tidak valid atau tidak ditemukan!';
            $messageType = 'danger';
            break;
        case 'not_found':
            $message = 'Data tidak ditemukan!';
            $messageType = 'warning';
            break;
        case 'delete_failed':
            $message = 'Gagal menghapus data! Silakan coba lagi.';
            $messageType = 'danger';
            break;
        case 'unknown':
            $message = 'Terjadi kesalahan yang tidak diketahui!';
            $messageType = 'danger';
            break;
    }
}

// File download handler with enhanced security
if (isset($_GET['id']) && isset($_GET['file'])) {
    $id = intval($_GET['id']);
    $fileField = $_GET['file'];

    // Validate file field parameter
    if (!in_array($fileField, ['file1','file2','file3'])) {
        die("Error: Invalid file parameter.");
    }

    // Get file info securely
    $stmt = $conn->prepare("SELECT $fileField FROM tahapan_kerjasama WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    if ($row && !empty($row[$fileField])) {
        $filename = $row[$fileField];
        $filePath = __DIR__ . "/upload/" . $filename;
        
        // Security: validate file path and prevent directory traversal
        $realPath = realpath($filePath);
        $uploadDir = realpath(__DIR__ . "/upload/");
        
        if ($realPath && $uploadDir && strpos($realPath, $uploadDir) === 0 && file_exists($realPath)) {
            // Determine if it's a download or view request
            $isDownload = isset($_GET['download']);
            
            // Get file info
            $fileSize = filesize($realPath);
            $mimeType = mime_content_type($realPath);
            
            if ($isDownload) {
                // Force download
                header("Content-Description: File Transfer");
                header("Content-Type: application/octet-stream");
                header("Content-Disposition: attachment; filename=\"" . basename($filename) . "\"");
                header("Content-Transfer-Encoding: binary");
                header("Expires: 0");
                header("Cache-Control: must-revalidate");
                header("Pragma: public");
                header("Content-Length: " . $fileSize);
            } else {
                // View in browser
                header("Content-Type: " . $mimeType);
                header("Content-Length: " . $fileSize);
                header("Content-Disposition: inline; filename=\"" . basename($filename) . "\"");
            }
            
            // Clean output buffer and read file
            ob_clean();
            flush();
            readfile($realPath);
            exit;
        } else {
            die("Error: File tidak ditemukan atau tidak dapat diakses.");
        }
    } else {
        die("Error: File tidak ada dalam database.");
    }
}

// Helper functions
function getFileIcon($filename) {
    if (empty($filename)) return '';
    
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    $icons = [
        "pdf" => '<i class="bi bi-file-earmark-pdf text-danger" style="font-size:32px;"></i>',
        "doc" => '<i class="bi bi-file-earmark-word text-primary" style="font-size:32px;"></i>',
        "docx" => '<i class="bi bi-file-earmark-word text-primary" style="font-size:32px;"></i>',
        "xls" => '<i class="bi bi-file-earmark-excel text-success" style="font-size:32px;"></i>',
        "xlsx" => '<i class="bi bi-file-earmark-excel text-success" style="font-size:32px;"></i>',
        "ppt" => '<i class="bi bi-file-earmark-ppt text-warning" style="font-size:32px;"></i>',
        "pptx" => '<i class="bi bi-file-earmark-ppt text-warning" style="font-size:32px;"></i>'
    ];
    return $icons[$ext] ?? '<i class="bi bi-file-earmark-text text-secondary" style="font-size:32px;"></i>';
}

function getBadgeClass($type, $value) {
    if (empty($value)) return "secondary";
    
    $badges = [
        'jenis' => [
            "Kementerian/Lembaga" => "success", 
            "Pemerintah Daerah" => "primary", 
            "Asosiasi" => "info", 
            "Perusahaan" => "warning", 
            "Universitas" => "dark", 
            "Job Portal" => "info"
        ],
        'status' => [
            "Signed" => "success", 
            "Not Available" => "secondary", 
            "Drafting/In Progress" => "warning",
            "Implemented" => "success",
            "In Progress" => "warning",
            "Not Yet" => "secondary"
        ]
    ];
    return $badges[$type][$value] ?? "secondary";
}

function formatDate($date) {
    return $date ? date('d/m/Y', strtotime($date)) : '-';
}

function truncateText($text, $length = 50) {
    if (strlen($text) <= $length) return $text;
    return substr($text, 0, $length) . '...';
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Mitra - Sistem Kerjasama</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <style>
        body { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .main-container { 
            background: white; 
            border-radius: 20px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.1); 
            margin: 20px auto; 
            padding: 30px; 
            max-width: 1400px; 
        }
        .page-title { 
            color: #2c3e50; 
            font-weight: 700; 
            font-size: 28px; 
            text-align: center; 
            margin-bottom: 30px;
            position: relative;
        }
        .page-title::after {
            content: '';
            display: block;
            width: 80px;
            height: 4px;
            background: linear-gradient(90deg, #667eea, #764ba2);
            margin: 10px auto;
            border-radius: 2px;
        }
        .controls-section { 
            background: linear-gradient(135deg, #f8f9ff 0%, #e9ecff 100%);
            border-radius: 15px; 
            padding: 25px; 
            margin-bottom: 25px; 
            border: 1px solid #e3e8ff;
        }
        .btn-primary-custom { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none; 
            border-radius: 10px; 
            padding: 10px 20px; 
            color: white; 
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-primary-custom:hover { 
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
            color: white; 
        }
        .form-control-custom, .form-select-custom { 
            border: 2px solid #e9ecef; 
            border-radius: 10px; 
            padding: 10px 15px; 
            font-size: 14px;
            transition: border-color 0.3s ease;
        }
        .form-control-custom:focus, .form-select-custom:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .elegant-table { 
            background: white; 
            border-radius: 15px; 
            overflow: hidden; 
            box-shadow: 0 5px 20px rgba(0,0,0,0.08); 
        }
        .elegant-table thead th { 
            background: linear-gradient(135deg, #2c5aa0 0%, #1e3d6f 100%);
            color: white; 
            padding: 18px 15px; 
            text-align: center; 
            font-weight: 600;
            border: none;
        }
        .elegant-table tbody tr {
            transition: all 0.3s ease;
        }
        .elegant-table tbody tr:hover { 
            background: linear-gradient(135deg, #f0f7ff 0%, #e6f3ff 100%);
            transform: scale(1.01);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .elegant-table tbody td { 
            padding: 15px 12px; 
            text-align: center; 
            border-bottom: 1px solid #f0f0f0; 
            vertical-align: middle;
        }
        .badge-custom { 
            padding: 8px 15px; 
            border-radius: 25px; 
            font-size: 0.85rem; 
            font-weight: 600;
        }
        .modal-header-custom { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white; 
        }
        .modal-content-custom { 
            border-radius: 20px; 
            border: none; 
            overflow: hidden;
        }
        .file-card { 
            background: linear-gradient(135deg, #f8f9ff 0%, #e9ecff 100%);
            border: 2px solid #e9ecef; 
            border-radius: 15px; 
            padding: 20px; 
            width: 180px; 
            transition: all 0.3s ease;
            text-align: center;
        }
        .file-card:hover { 
            transform: translateY(-5px); 
            box-shadow: 0 10px 25px rgba(0,0,0,0.15); 
            border-color: #667eea;
        }
        .btn-action { 
            border-radius: 8px; 
            padding: 8px 15px; 
            margin: 2px; 
            font-size: 0.85rem; 
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-action:hover {
            transform: translateY(-1px);
        }
        .modal-xl-custom { 
            max-width: 95%; 
            width: 95%; 
        }
        .marked-indicator { 
            color: #ff6b35; 
            font-size: 1.3rem; 
            margin-left: 10px; 
            animation: pulse 2s infinite; 
            filter: drop-shadow(0 0 3px rgba(255, 107, 53, 0.5));
        }
        @keyframes pulse { 
            0% { opacity: 1; transform: scale(1); } 
            50% { opacity: 0.7; transform: scale(1.1); } 
            100% { opacity: 1; transform: scale(1); } 
        }
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            margin-bottom: 20px;
        }
        .info-section {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
        }
        .detail-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 5px;
        }
        .detail-value {
            color: #6c757d;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
<div class="main-container">
    <h1 class="page-title">
        <i class="bi bi-people-fill me-3"></i>
        Sistem Manajemen Mitra Kerjasama
    </h1>
    
    <!-- Notification Messages -->
    <?php if ($message): ?>
        <div class="alert alert-<?= $messageType; ?> alert-dismissible fade show" role="alert">
            <i class="bi bi-<?= $messageType == 'success' ? 'check-circle-fill' : ($messageType == 'danger' ? 'exclamation-triangle-fill' : 'info-circle-fill'); ?> me-2"></i>
            <?= htmlspecialchars($message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <!-- Statistics -->
    <?php
    $stats = $conn->query("SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN tandai = 1 THEN 1 ELSE 0 END) as prioritas,
        SUM(CASE WHEN status_kesepahaman = 'Signed' THEN 1 ELSE 0 END) as kesepahaman_signed,
        SUM(CASE WHEN status_pks = 'Signed' THEN 1 ELSE 0 END) as pks_signed
        FROM tahapan_kerjasama")->fetch_assoc();
    ?>
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stats-card">
                <h3><?= $stats['total']; ?></h3>
                <p class="mb-0">Total Mitra</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <h3><?= $stats['prioritas']; ?></h3>
                <p class="mb-0">Mitra Prioritas</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <h3><?= $stats['kesepahaman_signed']; ?></h3>
                <p class="mb-0">Kesepahaman Signed</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <h3><?= $stats['pks_signed']; ?></h3>
                <p class="mb-0">PKS Signed</p>
            </div>
        </div>
    </div>
    
    <!-- Controls Section -->
    <div class="controls-section">
        <div class="row align-items-center">
            <div class="col-md-3">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" id="searchInput" class="form-control form-control-custom border-start-0" 
                           placeholder="Cari nama mitra...">
                </div>
            </div>
            <div class="col-md-3">
                <select id="filterJenis" class="form-select form-select-custom">
                    <option value="">Semua Jenis Mitra</option>
                    <option value="Kementerian/Lembaga">Kementerian/Lembaga</option>
                    <option value="Pemerintah Daerah">Pemerintah Daerah</option>
                    <option value="Asosiasi">Asosiasi</option>
                    <option value="Perusahaan">Perusahaan</option>
                    <option value="Universitas">Universitas</option>
                    <option value="Job Portal">Job Portal</option>
                </select>
            </div>
            <div class="col-md-3">
                <select id="filterStatus" class="form-select form-select-custom">
                    <option value="">Semua Status</option>
                    <option value="prioritas">Hanya Prioritas</option>
                    <option value="kesepahaman_signed">Kesepahaman Signed</option>
                    <option value="pks_signed">PKS Signed</option>
                </select>
            </div>
            <div class="col-md-3 text-end">
                <a href="create.php" class="btn btn-primary-custom">
                    <i class="bi bi-plus-circle me-2"></i>Tambah Mitra Baru
                </a>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="table-responsive">
        <table class="table elegant-table mb-0" id="mitraTable">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 25%;">Nama Mitra</th>
                    <th style="width: 15%;">Jenis Mitra</th>
                    <th style="width: 15%;">Status Kesepahaman</th>
                    <th style="width: 12%;">Tanggal</th>
                    <th style="width: 15%;">Status PKS</th>
                    <th style="width: 13%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $result = $conn->query("SELECT * FROM tahapan_kerjasama ORDER BY tandai DESC, id DESC");
            if ($result && $result->num_rows > 0):
                $no = 1;
                while ($row = $result->fetch_assoc()):
                    $isTandai = isset($row['tandai']) ? (int)$row['tandai'] : 0;
            ?>
                <tr onclick="showModal<?= $row['id']; ?>()" style="cursor:pointer;" 
                    data-nama="<?= strtolower($row['nama_mitra']); ?>"
                    data-jenis="<?= $row['jenis_mitra']; ?>"
                    data-prioritas="<?= $isTandai; ?>"
                    data-kesepahaman="<?= $row['status_kesepahaman']; ?>"
                    data-pks="<?= $row['status_pks']; ?>"
                    <?= $isTandai ? 'class="table-warning"' : ''; ?>>
                    <td class="fw-bold"><?= $no++; ?></td>
                    <td class="text-start">
                        <div class="d-flex align-items-center">
                            <div>
                                <div class="fw-bold"><?= htmlspecialchars($row['nama_mitra']); ?></div>
                                <small class="text-muted"><?= htmlspecialchars($row['sumber_usulan'] ?: 'Tidak ada sumber'); ?></small>
                            </div>
                            <?php if ($isTandai == 1): ?>
                                <i class="bi bi-star-fill marked-indicator" title="Mitra Prioritas"></i>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td>
                        <span class="badge badge-custom bg-<?= getBadgeClass('jenis', $row['jenis_mitra']); ?>">
                            <?= htmlspecialchars($row['jenis_mitra']); ?>
                        </span>
                    </td>
                    <td>
                        <span class="badge badge-custom bg-<?= getBadgeClass('status', $row['status_kesepahaman']); ?>">
                            <?= htmlspecialchars($row['status_kesepahaman'] ?: 'Belum Ada'); ?>
                        </span>
                    </td>
                    <td><?= formatDate($row['tanggal_kesepahaman']); ?></td>
                    <td>
                        <span class="badge badge-custom bg-<?= getBadgeClass('status', $row['status_pks']); ?>">
                            <?= htmlspecialchars($row['status_pks'] ?: 'Belum Ada'); ?>
                        </span>
                    </td>
                    <td onclick="event.stopPropagation();">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-info btn-action btn-sm" onclick="showModal<?= $row['id']; ?>()">
                                <i class="bi bi-eye"></i>
                            </button>
                            <a href="edit.php?id=<?= $row['id']; ?>" class="btn btn-warning btn-action btn-sm">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <a href="export_excel.php?id=<?= $row['id']; ?>" class="btn btn-success btn-action btn-sm">
                                <i class="bi bi-file-excel"></i>
                            </a>
                            <a href="delete.php?id=<?= $row['id']; ?>" 
                               onclick="return confirmDelete('<?= htmlspecialchars($row['nama_mitra']); ?>')" 
                               class="btn btn-danger btn-action btn-sm">
                                <i class="bi bi-trash"></i>
                            </a>
                        </div>
                    </td>
                </tr>

                <!-- Modal Detail -->
                <div class="modal fade" id="detailModal<?= $row['id']; ?>" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-xl-custom modal-dialog-scrollable">
                        <div class="modal-content modal-content-custom">
                            <div class="modal-header modal-header-custom">
                                <h5 class="modal-title">
                                    <i class="bi bi-building me-2"></i>
                                    Detail Mitra: <?= htmlspecialchars($row['nama_mitra']); ?>
                                    <?php if ($isTandai == 1): ?>
                                        <i class="bi bi-star-fill text-warning ms-2" title="Mitra Prioritas"></i>
                                    <?php endif; ?>
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <?php if ($isTandai == 1): ?>
                                    <div class="alert alert-warning d-flex align-items-center mb-4">
                                        <i class="bi bi-star-fill me-2"></i>
                                        <div><strong>Mitra Prioritas:</strong> Data ini ditandai sebagai prioritas atau perhatian khusus</div>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Basic Info -->
                                <div class="info-section">
                                    <h6 class="text-primary mb-3"><i class="bi bi-info-circle me-2"></i>Informasi Dasar</h6>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="detail-label">Nama Mitra:</div>
                                            <div class="detail-value"><?= htmlspecialchars($row['nama_mitra']); ?></div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="detail-label">Jenis Mitra:</div>
                                            <div class="detail-value">
                                                <span class="badge bg-<?= getBadgeClass('jenis', $row['jenis_mitra']); ?>">
                                                    <?= htmlspecialchars($row['jenis_mitra']); ?>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="detail-label">Sumber Usulan:</div>
                                            <div class="detail-value"><?= htmlspecialchars($row['sumber_usulan'] ?: '-'); ?></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Kesepahaman Info -->
                                <div class="info-section">
                                    <h6 class="text-info mb-3"><i class="bi bi-handshake me-2"></i>Informasi Kesepahaman</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="detail-label">Status:</div>
                                            <div class="detail-value">
                                                <span class="badge bg-<?= getBadgeClass('status', $row['status_kesepahaman']); ?>">
                                                    <?= htmlspecialchars($row['status_kesepahaman'] ?: 'Belum Ada'); ?>
                                                </span>
                                            </div>
                                            <div class="detail-label">Nomor:</div>
                                            <div class="detail-value"><?= htmlspecialchars($row['nomor_kesepahaman'] ?: '-'); ?></div>
                                            <div class="detail-label">Tanggal:</div>
                                            <div class="detail-value"><?= formatDate($row['tanggal_kesepahaman']); ?></div>
                                            <div class="detail-label">Status Pelaksanaan:</div>
                                            <div class="detail-value">
                                                <span class="badge bg-<?= getBadgeClass('status', $row['status_pelaksanaan_kesepahaman']); ?>">
                                                    <?= htmlspecialchars($row['status_pelaksanaan_kesepahaman'] ?: 'Belum Ada'); ?>
                                                </span>
                                            </div>
                                            <div class="detail-label">Rencana Pertemuan:</div>
                                            <div class="detail-value"><?= formatDate($row['rencana_pertemuan_kesepahaman']); ?></div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="detail-label">Ruang Lingkup:</div>
                                            <div class="detail-value"><?= nl2br(htmlspecialchars($row['ruanglingkup_kesepahaman'] ?: '-')); ?></div>
                                            <div class="detail-label">Rencana Kolaborasi:</div>
                                            <div class="detail-value"><?= nl2br(htmlspecialchars($row['rencana_kolaborasi_kesepahaman'] ?: '-')); ?></div>
                                            <div class="detail-label">Status/Progres:</div>
                                            <div class="detail-value"><?= nl2br(htmlspecialchars($row['status_progres_kesepahaman'] ?: '-')); ?></div>
                                            <div class="detail-label">Tindak Lanjut:</div>
                                            <div class="detail-value"><?= nl2br(htmlspecialchars($row['tindaklanjut_kesepahaman'] ?: '-')); ?></div>
                                            <div class="detail-label">Keterangan:</div>
                                            <div class="detail-value"><?= htmlspecialchars($row['keterangan_kesepahaman'] ?: '-'); ?></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- PKS Info -->
                                <div class="info-section">
                                    <h6 class="text-success mb-3"><i class="bi bi-file-earmark-text me-2"></i>Informasi PKS</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="detail-label">Status PKS:</div>
                                            <div class="detail-value">
                                                <span class="badge bg-<?= getBadgeClass('status', $row['status_pks']); ?>">
                                                    <?= htmlspecialchars($row['status_pks'] ?: 'Belum Ada'); ?>
                                                </span>
                                            </div>
                                            <div class="detail-label">Nomor:</div>
                                            <div class="detail-value"><?= htmlspecialchars($row['nomor_pks'] ?: '-'); ?></div>
                                            <div class="detail-label">Tanggal:</div>
                                            <div class="detail-value"><?= formatDate($row['tanggal_pks']); ?></div>
                                            <div class="detail-label">Status Pelaksanaan:</div>
                                            <div class="detail-value">
                                                <span class="badge bg-<?= getBadgeClass('status', $row['status_pelaksanaan_pks']); ?>">
                                                    <?= htmlspecialchars($row['status_pelaksanaan_pks'] ?: 'Belum Ada'); ?>
                                                </span>
                                            </div>
                                            <div class="detail-label">Rencana Pertemuan:</div>
                                            <div class="detail-value"><?= formatDate($row['rencana_pertemuan_pks']); ?></div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="detail-label">Ruang Lingkup:</div>
                                            <div class="detail-value"><?= nl2br(htmlspecialchars($row['ruanglingkup_pks'] ?: '-')); ?></div>
                                            <div class="detail-label">Status/Progres:</div>
                                            <div class="detail-value"><?= nl2br(htmlspecialchars($row['status_progres_pks'] ?: '-')); ?></div>
                                            <div class="detail-label">Tindak Lanjut:</div>
                                            <div class="detail-value"><?= nl2br(htmlspecialchars($row['tindaklanjut_pks'] ?: '-')); ?></div>
                                            <div class="detail-label">Keterangan:</div>
                                            <div class="detail-value"><?= htmlspecialchars($row['keterangan_pks'] ?: '-'); ?></div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Files Section -->
                                <div class="info-section">
                                    <h6 class="text-secondary mb-3"><i class="bi bi-paperclip me-2"></i>File Terlampir</h6>
                                    <div class="d-flex flex-wrap gap-3">
                                        <?php 
                                        $hasFiles = false;
                                        for ($i = 1; $i <= 3; $i++): 
                                            if (!empty($row["file$i"])): 
                                                $hasFiles = true;
                                                $filename = htmlspecialchars($row["file$i"]);
                                        ?>
                                            <div class="file-card">
                                                <?= getFileIcon($filename); ?>
                                                <div class="mt-2 mb-3">
                                                    <small class="d-block fw-bold text-truncate" title="<?= $filename; ?>">
                                                        <?= basename($filename); ?>
                                                    </small>
                                                    <small class="text-muted">File <?= $i; ?></small>
                                                </div>
                                                <div class="d-grid gap-1">
                                                    <a href="?id=<?= $row['id']; ?>&file=file<?= $i ?>" target="_blank" 
                                                       class="btn btn-info btn-action btn-sm">
                                                        <i class="bi bi-eye me-1"></i>Lihat
                                                    </a>
                                                    <a href="?id=<?= $row['id']; ?>&file=file<?= $i ?>&download=1" 
                                                       class="btn btn-success btn-action btn-sm">
                                                        <i class="bi bi-download me-1"></i>Download
                                                    </a>
                                                </div>
                                            </div>
                                        <?php 
                                            endif; 
                                        endfor; 
                                        
                                        if (!$hasFiles): ?>
                                            <div class="text-center w-100 py-4">
                                                <i class="bi bi-file-x text-muted" style="font-size: 3rem;"></i>
                                                <p class="text-muted mt-2">Tidak ada file terlampir</p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer bg-light">
                                <a href="edit.php?id=<?= $row['id']; ?>" class="btn btn-warning btn-action">
                                    <i class="bi bi-pencil me-1"></i>Edit
                                </a>
                                <a href="export_excel.php?id=<?= $row['id']; ?>" class="btn btn-success btn-action">
                                    <i class="bi bi-file-excel me-1"></i>Export Excel
                                </a>
                                <a href="delete.php?id=<?= $row['id']; ?>" 
                                   onclick="return confirmDelete('<?= htmlspecialchars($row['nama_mitra']); ?>')" 
                                   class="btn btn-danger btn-action">
                                    <i class="bi bi-trash me-1"></i>Hapus
                                </a>
                                <button type="button" class="btn btn-secondary btn-action" data-bs-dismiss="modal">
                                    <i class="bi bi-x-circle me-1"></i>Tutup
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    function showModal<?= $row['id']; ?>() {
                        const modal = new bootstrap.Modal(document.getElementById('detailModal<?= $row['id']; ?>'));
                        modal.show();
                    }
                </script>

            <?php endwhile; else: ?>
                <tr>
                    <td colspan="7" class="text-center text-muted py-5">
                        <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                        <div class="mt-3">
                            <h5>Belum ada data mitra</h5>
                            <p>Silakan tambahkan mitra baru untuk memulai</p>
                            <a href="create.php" class="btn btn-primary-custom">
                                <i class="bi bi-plus-circle me-2"></i>Tambah Mitra Pertama
                            </a>
                        </div>
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Footer Info -->
    <div class="text-center mt-4 text-muted">
        <small>
            <i class="bi bi-info-circle me-1"></i>
            Total <?= $stats['total']; ?> mitra terdaftar | 
            Klik pada baris untuk melihat detail lengkap
        </small>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function applyFilters() {
    const searchValue = document.getElementById('searchInput').value.toLowerCase();
    const filterJenis = document.getElementById('filterJenis').value;
    const filterStatus = document.getElementById('filterStatus').value;
    
    document.querySelectorAll('#mitraTable tbody tr').forEach(function(row) {
        // Skip if this is the "no data" row
        if (row.children.length === 1) return;
        
        const nama = row.getAttribute('data-nama') || '';
        const jenis = row.getAttribute('data-jenis') || '';
        const prioritas = row.getAttribute('data-prioritas') || '0';
        const kesepahaman = row.getAttribute('data-kesepahaman') || '';
        const pks = row.getAttribute('data-pks') || '';
        
        let show = true;
        
        // Search filter
        if (searchValue && !nama.includes(searchValue)) {
            show = false;
        }
        
        // Jenis filter
        if (filterJenis && jenis !== filterJenis) {
            show = false;
        }
        
        // Status filter
        if (filterStatus) {
            switch (filterStatus) {
                case 'prioritas':
                    if (prioritas !== '1') show = false;
                    break;
                case 'kesepahaman_signed':
                    if (kesepahaman !== 'Signed') show = false;
                    break;
                case 'pks_signed':
                    if (pks !== 'Signed') show = false;
                    break;
            }
        }
        
        row.style.display = show ? '' : 'none';
    });
    
    // Update visible row numbers
    updateRowNumbers();
}

function updateRowNumbers() {
    let visibleCount = 1;
    document.querySelectorAll('#mitraTable tbody tr').forEach(function(row) {
        if (row.style.display !== 'none' && row.children.length > 1) {
            row.children[0].textContent = visibleCount++;
        }
    });
}

function confirmDelete(namaMitra) {
    return confirm(`⚠️ PERINGATAN PENGHAPUSAN DATA ⚠️\n\n` +
                  `Anda akan menghapus data mitra:\n"${namaMitra}"\n\n` +
                  `Tindakan ini akan:\n` +
                  `• Menghapus semua data mitra dari database\n` +
                  `• Menghapus semua file terkait dari server\n` +
                  `• TIDAK DAPAT DIBATALKAN\n\n` +
                  `Apakah Anda yakin ingin melanjutkan?`);
}

function resetFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('filterJenis').value = '';
    document.getElementById('filterStatus').value = '';
    applyFilters();
}

// Auto hide alerts after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    // Alert auto-hide
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            if (alert.parentNode) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }
        }, 5000);
    });
    
    // Initialize row numbers
    updateRowNumbers();
    
    // Add keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl/Cmd + K for search focus
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            document.getElementById('searchInput').focus();
        }
        
        // Escape to reset filters
        if (e.key === 'Escape') {
            resetFilters();
        }
    });
});

// Event listeners
document.getElementById('searchInput').addEventListener('input', applyFilters);
document.getElementById('filterJenis').addEventListener('change', applyFilters);
document.getElementById('filterStatus').addEventListener('change', applyFilters);

// Add loading state for better UX
function showLoading() {
    const tbody = document.querySelector('#mitraTable tbody');
    tbody.innerHTML = '<tr><td colspan="7" class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>';
}

// Enhanced row click handling
document.addEventListener('click', function(e) {
    const row = e.target.closest('tr[onclick]');
    if (row && !e.target.closest('.btn-group')) {
        // Extract modal function from onclick attribute
        const onclickAttr = row.getAttribute('onclick');
        if (onclickAttr) {
            eval(onclickAttr);
        }
    }
});

// Add export all functionality
function exportAllData() {
    if (confirm('Export semua data mitra ke Excel?')) {
        window.location.href = 'export_all.php';
    }
}

// Print functionality
function printTable() {
    window.print();
}

// Add these buttons dynamically
document.addEventListener('DOMContentLoaded', function() {
    const controlsSection = document.querySelector('.controls-section .row');
    const lastCol = controlsSection.querySelector('.col-md-3:last-child');
    
    // Add utility buttons
    const utilityButtons = document.createElement('div');
    utilityButtons.className = 'mt-2';
    utilityButtons.innerHTML = `
        <button onclick="resetFilters()" class="btn btn-outline-secondary btn-sm me-2">
            <i class="bi bi-arrow-clockwise me-1"></i>Reset Filter
        </button>
        <button onclick="printTable()" class="btn btn-outline-info btn-sm">
            <i class="bi bi-printer me-1"></i>Print
        </button>
    `;
    lastCol.appendChild(utilityButtons);
});
</script>

<!-- Print styles -->
<style media="print">
    body { background: white !important; }
    .main-container { box-shadow: none !important; }
    .controls-section, .btn, .modal { display: none !important; }
    .elegant-table { box-shadow: none !important; }
    .marked-indicator { color: #000 !important; }
</style>

</body>
</html>