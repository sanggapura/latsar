<?php
session_start();
// Koneksi ke database
$conn = new mysqli("localhost", "root", "", "jejaring_db");
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// --- LOGIKA PAGINASI DAN PENCARIAN ---
$limit = 20;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$search = $_GET['search'] ?? '';
$whereClause = '';
$params = [];
$types = '';

if (!empty($search)) {
    $searchTerm = "%" . $search . "%";
    $whereClause = "WHERE judul LIKE ?";
    $params[] = $searchTerm;
    $types .= 's';
}

// Query untuk menghitung total data
$countSql = "SELECT COUNT(*) as total FROM dokumen $whereClause";
$countStmt = $conn->prepare($countSql);
if (!empty($params)) {
    $countStmt->bind_param($types, ...$params);
}
$countStmt->execute();
$total_rows = $countStmt->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $limit);

// Query utama untuk mengambil data dengan limit dan offset
$sql = "SELECT * FROM dokumen $whereClause ORDER BY tanggal DESC LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);

$final_params = $params;
$final_params[] = $limit;
$final_params[] = $offset;
$final_types = $types . 'ii';

$stmt->bind_param($final_types, ...$final_params);
$stmt->execute();
$result = $stmt->get_result();
// --- AKHIR LOGIKA ---

include __DIR__ . "/../../views/header.php";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Dokumen</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { 
            --bs-blue-dark: #0a3d62; 
            --bs-blue-light: #3c6382; 
            --bs-gray: #f5f7fa;
            --bs-success: #28a745;
        }
        .main-container { background-color: white; border-radius: 1rem; box-shadow: 0 10px 30px rgba(0,0,0,0.08); padding: 2rem; margin-top: -4rem; position: relative; z-index: 2; }
        .page-title { color: var(--bs-blue-dark); font-weight: 700; }
        .controls-section { background-color: var(--bs-gray); border-radius: 0.75rem; padding: 1.5rem; }
        .table thead th { background-color: var(--bs-blue-dark); color: white; text-align: center; vertical-align: middle; }
        .table tbody tr:hover { transform: scale(1.01); box-shadow: 0 5px 15px rgba(0,0,0,0.1); background-color: #e9ecef; }
        .action-buttons .btn { margin: 0 2px; }
        .modal-header { background: linear-gradient(135deg, var(--bs-blue-dark) 0%, var(--bs-blue-light) 100%); color: white; }
        .pagination .page-link { color: var(--bs-blue-dark); }
        .pagination .page-item.active .page-link { background-color: var(--bs-blue-dark); border-color: var(--bs-blue-dark); }
        .file-type-badge { display: inline-flex; align-items: center; gap: 5px; font-weight: 500; }
        
        /* Gaya untuk Notifikasi Toast */
        .toast-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: var(--bs-success);
            color: white;
            padding: 15px 25px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            z-index: 1060;
            opacity: 0;
            transform: translateY(-20px);
            transition: opacity 0.3s ease, transform 0.3s ease;
            font-weight: 500;
        }
        .toast-notification.show {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>
<body>
<div class="container my-5">
    <div class="main-container">
        <div class="text-center mb-4">
            <h1 class="page-title"><i class="bi bi-folder-fill"></i> Manajemen Dokumen</h1>
        </div>
        <div class="controls-section mb-4">
            <div class="row g-3 align-items-center">
                <div class="col-md-8">
                    <form action="" method="GET" class="d-flex">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" name="search" class="form-control" placeholder="Cari berdasarkan judul dokumen..." value="<?= htmlspecialchars($search) ?>">
                            <button class="btn btn-outline-secondary" type="submit">Cari</button>
                        </div>
                    </form>
                </div>
                <div class="col-md-4 text-end">
                    <button class="btn btn-primary w-100" onclick="openTambah()">
                        <i class="bi bi-plus-circle"></i> Tambah Dokumen Baru
                    </button>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Judul Dokumen</th>
                        <th>Jenis File</th>
                        <th>Tanggal Upload</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php $no = $offset + 1; while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td class="text-center fw-bold"><?= $no++ ?></td>
                            <td><?= htmlspecialchars($row['judul']) ?></td>
                            <td class="text-center">
                                <?php 
                                $ext = strtoupper($row['jenis']);
                                $badge_class = 'secondary';
                                $icon = 'bi-file-earmark-text';
                                if (in_array($ext, ['DOC', 'DOCX'])) { $badge_class = 'primary'; $icon = 'bi-file-earmark-word-fill'; }
                                elseif (in_array($ext, ['XLS', 'XLSX'])) { $badge_class = 'success'; $icon = 'bi-file-earmark-excel-fill'; }
                                elseif ($ext == 'PDF') { $badge_class = 'danger'; $icon = 'bi-file-earmark-pdf-fill'; }
                                ?>
                                <span class="badge text-bg-<?= $badge_class ?> file-type-badge">
                                    <i class="bi <?= $icon ?>"></i> <?= $ext ?>
                                </span>
                            </td>
                            <td class="text-center"><?= date('d M Y', strtotime($row['tanggal'])) ?></td>
                            <td class="text-center action-buttons">
                                <button class="btn btn-sm btn-outline-info" title="Lihat" onclick="openView('uploads/<?= htmlspecialchars($row['file_path']) ?>')"><i class="bi bi-eye-fill"></i></button>
                                <a href="uploads/<?= htmlspecialchars($row['file_path']) ?>" download class="btn btn-sm btn-outline-secondary" title="Download"><i class="bi bi-download"></i></a>
                                <button class="btn btn-sm btn-outline-primary" title="Edit" onclick="openEdit(<?= $row['id'] ?>, '<?= htmlspecialchars($row['judul'], ENT_QUOTES) ?>')"><i class="bi bi-pencil-fill"></i></button>
                                <button class="btn btn-sm btn-outline-danger" title="Hapus" onclick="openDeleteConfirm(<?= $row['id'] ?>, '<?= htmlspecialchars($row['judul'], ENT_QUOTES) ?>')"><i class="bi bi-trash-fill"></i></button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center p-5">Tidak ada dokumen ditemukan.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center mt-4">
                <?php if ($total_pages > 1): ?>
                    <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>"><a class="page-link" href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>">Previous</a></li>
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?= ($page == $i) ? 'active' : '' ?>"><a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>"><?= $i ?></a></li>
                    <?php endfor; ?>
                    <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>"><a class="page-link" href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>">Next</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</div>

<!-- Modals -->
<div class="modal fade" id="formModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="formModalLabel"></h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div><div class="modal-body" id="modalBodyContent"></div></div></div></div>
<div class="modal fade" id="deleteConfirmModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header bg-danger text-white"><h5 class="modal-title"><i class="bi bi-exclamation-triangle-fill"></i> Konfirmasi Hapus</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div><div class="modal-body">Apakah Anda yakin ingin menghapus dokumen <strong id="docNameToDelete"></strong>?</div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><a href="#" id="confirmDeleteBtn" class="btn btn-danger">Ya, Hapus</a></div></div></div></div>
<div class="modal fade" id="viewModal" tabindex="-1"><div class="modal-dialog modal-xl modal-dialog-centered"><div class="modal-content" style="height: 90vh;"><div class="modal-header"><h5 class="modal-title">Pratinjau Dokumen</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div><div class="modal-body"><iframe id="viewFrame" src="" width="100%" height="100%" frameborder="0"></iframe></div></div></div></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const formModal = new bootstrap.Modal(document.getElementById('formModal'));
    const deleteConfirmModal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
    const viewModal = new bootstrap.Modal(document.getElementById('viewModal'));
    
    function showToast(message) {
        const toast = document.createElement('div');
        toast.className = 'toast-notification';
        toast.textContent = '✅ ' + message;
        document.body.appendChild(toast);
        setTimeout(() => toast.classList.add('show'), 10);
        setTimeout(() => {
            toast.classList.remove('show');
            toast.addEventListener('transitionend', () => toast.remove());
        }, 3000);
    }

    function handleDocFormSubmit(event) {
        event.preventDefault();
        const form = event.target;
        const formData = new FormData(form);
        
        fetch(form.action, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                formModal.hide();
                const isEditing = form.action.includes('update');
                showToast(isEditing ? 'Dokumen berhasil diperbarui!' : 'Dokumen berhasil ditambahkan!');
                
                setTimeout(() => {
                    location.reload();
                }, 1500); 
            } else {
                alert('Error: ' + (data.error || 'Terjadi kesalahan.'));
            }
        })
        .catch(error => {
            console.error('Submit error:', error);
            alert('Terjadi kesalahan koneksi.');
        });
    }

    function openTambah() {
        document.getElementById('formModalLabel').innerHTML = '<i class="bi bi-plus-circle"></i> Tambah Dokumen Baru';
        const modalBody = document.getElementById('modalBodyContent');
        modalBody.innerHTML = `
            <form id="docForm" action="dokumen_tambah.php" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">Judul Dokumen <span class="text-danger">*</span></label>
                    <input type="text" name="judul" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">File <span class="text-danger">*</span></label>
                    <input type="file" name="file" class="form-control" accept=".pdf,.doc,.docx,.xls,.xlsx" required>
                </div>
                <div class="text-end">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        `;
        document.getElementById('docForm').addEventListener('submit', handleDocFormSubmit);
        formModal.show();
    }

    function openEdit(id, judul) {
        document.getElementById('formModalLabel').innerHTML = '<i class="bi bi-pencil-square"></i> Edit Dokumen';
        const modalBody = document.getElementById('modalBodyContent');
        modalBody.innerHTML = `
            <form id="docForm" action="dokumen_update.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" value="${id}">
                <div class="mb-3">
                    <label class="form-label">Judul Dokumen</label>
                    <input type="text" name="judul" class="form-control" value="${judul}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Ganti File (Opsional)</label>
                    <input type="file" name="file" class="form-control" accept=".pdf,.doc,.docx,.xls,.xlsx">
                    <small class="form-text text-muted">Kosongkan jika tidak ingin mengganti file.</small>
                </div>
                <div class="text-end">
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        `;
        document.getElementById('docForm').addEventListener('submit', handleDocFormSubmit);
        formModal.show();
    }

    function openDeleteConfirm(id, docName) {
        document.getElementById('docNameToDelete').textContent = docName;
        document.getElementById('confirmDeleteBtn').href = `dokumen_delete.php?id=${id}`;
        deleteConfirmModal.show();
    }

    function openView(filePath) {
        const viewFrame = document.getElementById('viewFrame');
        const fullUrl = `${window.location.protocol}//${window.location.host}${window.location.pathname.substring(0, window.location.pathname.lastIndexOf('/'))}/${filePath}`;
        
        let viewUrl;
        if (filePath.toLowerCase().endsWith('.pdf')) {
            viewUrl = filePath;
        } else {
            viewUrl = `https://docs.google.com/gview?url=${encodeURIComponent(fullUrl)}&embedded=true`;
        }
        
        viewFrame.src = viewUrl;
        viewModal.show();
    }
</script>
</body>
</html>

