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
    $whereClause = "WHERE nama_perusahaan LIKE ? OR nama_pic LIKE ? OR alamat_email LIKE ?";
    $params = [$searchTerm, $searchTerm, $searchTerm];
    $types = 'sss';
}

$countSql = "SELECT COUNT(*) as total FROM kontak_mitra $whereClause";
$countStmt = $conn->prepare($countSql);
if (!empty($params)) {
    $countStmt->bind_param($types, ...$params);
}
$countStmt->execute();
$total_rows = $countStmt->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $limit);

$sql = "SELECT * FROM kontak_mitra $whereClause ORDER BY nama_perusahaan ASC LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$final_params = $params;
$final_params[] = $limit;
$final_params[] = $offset;
$final_types = $types . 'ii'; 
$stmt->bind_param($final_types, ...$final_params);
$stmt->execute();
$result = $stmt->get_result();
// --- AKHIR LOGIKA PAGINASI DAN PENCARIAN ---

include __DIR__ . "/../../views/header.php";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Kontak Mitra</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { 
            --bs-blue-dark: #0a3d62; 
            --bs-blue-light: #3c6382; 
            --bs-gray: #f5f7fa;
            --bs-success: #28a745;
            --bs-danger: #dc3545;
        }
        .main-container { background-color: white; border-radius: 1rem; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08); padding: 2rem; margin-top: -4rem; position: relative; z-index: 2; }
        .page-title { color: var(--bs-blue-dark); font-weight: 700; }
        .controls-section { background-color: var(--bs-gray); border-radius: 0.75rem; padding: 1.5rem; }
        .table thead th { background-color: var(--bs-blue-dark); color: white; text-align: center; vertical-align: middle; }
        .table tbody tr:hover { transform: scale(1.01); box-shadow: 0 5px 15px rgba(0,0,0,0.1); background-color: #e9ecef; }
        .action-buttons .btn { margin: 0 2px; }
        .btn-wa { background-color: #25D366; color: white; }
        .btn-wa:hover { background-color: #1DAE54; color: white; }
        .modal-header { background: linear-gradient(135deg, var(--bs-blue-dark) 0%, var(--bs-blue-light) 100%); color: white; }
        .pagination .page-link { color: var(--bs-blue-dark); }
        .pagination .page-item.active .page-link { background-color: var(--bs-blue-dark); border-color: var(--bs-blue-dark); }
        
        /* Styling untuk Notifikasi Toast */
        .toast-notification {
            position: fixed; top: 20px; right: 20px; color: white; padding: 15px 25px;
            border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); z-index: 1060; 
            opacity: 0; transform: translateY(-20px); transition: opacity 0.3s ease, transform 0.3s ease; font-weight: 500;
        }
        .toast-notification.success { background-color: var(--bs-success); }
        .toast-notification.show { opacity: 1; transform: translateY(0); }

        /* CSS untuk Error Bubble di Form */
        .error-bubble {
            background-color: var(--bs-danger); color: white; padding: 8px 12px; border-radius: 6px;
            font-size: 13px; margin-bottom: 8px; position: relative; animation: fadeIn 0.3s;
        }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
        /* Membuat panah kecil di bawah bubble */
        .error-bubble::after {
            content: ''; position: absolute; bottom: -8px; left: 20px;
            border-width: 8px 8px 0; border-style: solid; border-color: var(--bs-danger) transparent transparent transparent;
        }
        .form-control.is-invalid {
            border-color: var(--bs-danger) !important;
            box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25) !important;
        }
    </style>
</head>
<body>
<div class="container my-5">
    <div class="main-container">
        <div class="text-center mb-4"><h1 class="page-title"><i class="bi bi-person-rolodex"></i> Manajemen Kontak Mitra</h1></div>
        <div class="controls-section mb-4">
            <div class="row g-3 align-items-center">
                <div class="col-md-8">
                     <form action="" method="GET" class="d-flex">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" name="search" class="form-control" placeholder="Cari berdasarkan nama perusahaan, PIC, atau email..." value="<?= htmlspecialchars($search) ?>">
                            <button class="btn btn-outline-secondary" type="submit">Cari</button>
                        </div>
                    </form>
                </div>
                <div class="col-md-4 text-end"><button class="btn btn-primary w-100" onclick="openTambah()"><i class="bi bi-plus-circle"></i> Tambah Kontak Baru</button></div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead><tr><th>No</th><th>Nama Perusahaan</th><th>Nama PIC</th><th>Nomor Telepon</th><th>Email</th><th>Aksi</th></tr></thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): $no = $offset + 1; while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td class="text-center fw-bold"><?= $no++ ?></td>
                        <td class="nama-perusahaan"><?= htmlspecialchars($row['nama_perusahaan']) ?></td>
                        <td class="nama-pic"><?= htmlspecialchars($row['nama_pic']) ?></td>
                        <td class="nomor-telp"><?= htmlspecialchars($row['nomor_telp']) ?></td>
                        <td class="alamat-email"><?= htmlspecialchars($row['alamat_email'] ?: '-') ?></td>
                        <td class="text-center action-buttons">
                            <button class="btn btn-sm btn-wa" title="Salin Info WA" onclick="shareWA(event, this)"><i class="fab fa-whatsapp"></i></button>
                            <button class="btn btn-sm btn-outline-primary" title="Edit Kontak" onclick="openEdit(event, <?= $row['id'] ?>)"><i class="bi bi-pencil-fill"></i></button>
                            <button class="btn btn-sm btn-outline-danger" title="Hapus Kontak" data-delete-url="delete_kontak.php?id=<?= $row['id'] ?>" data-contact-name="<?= htmlspecialchars($row['nama_perusahaan']) ?>" onclick="openDeleteConfirm(event, this)"><i class="bi bi-trash-fill"></i></button>
                        </td>
                    </tr>
                    <?php endwhile; else: ?>
                    <tr><td colspan="6" class="text-center p-5"><?= !empty($search) ? 'Kontak dengan kata kunci "'.htmlspecialchars($search).'" tidak ditemukan.' : 'Belum ada data kontak. Silakan tambahkan kontak baru.' ?></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center mt-4">
                <?php if($total_pages > 1): ?>
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
<div class="modal fade" id="deleteConfirmModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header bg-danger text-white"><h5 class="modal-title"><i class="bi bi-exclamation-triangle-fill"></i> Konfirmasi Hapus</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div><div class="modal-body">Apakah Anda yakin ingin menghapus kontak <strong id="contactNameToDelete"></strong>?</div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><a href="#" id="confirmDeleteBtn" class="btn btn-danger"><i class="bi bi-trash-fill"></i> Ya, Hapus</a></div></div></div></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const formModal = new bootstrap.Modal(document.getElementById('formModal'));
    const deleteConfirmModal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
    const modalBody = document.getElementById('modalBodyContent');
    const modalTitle = document.getElementById('formModalLabel');

    function showToast(message) {
        const toast = document.createElement('div');
        toast.className = 'toast-notification success';
        toast.textContent = `✅ ${message}`;
        document.body.appendChild(toast);
        setTimeout(() => toast.classList.add('show'), 10);
        setTimeout(() => {
            toast.classList.remove('show');
            toast.addEventListener('transitionend', () => toast.remove());
        }, 3000);
    }

    function setupFormListeners(form) {
        const telpInput = form.querySelector('input[name="nomor_telp"]');
        if (telpInput) {
            telpInput.addEventListener('input', () => {
                const errorBubble = form.querySelector('.error-bubble');
                if (errorBubble) errorBubble.style.display = 'none';
                telpInput.classList.remove('is-invalid');
            });
        }
    }

    function openTambah() {
        modalTitle.innerHTML = '<i class="bi bi-person-plus-fill"></i> Tambah Kontak Baru';
        modalBody.innerHTML = '<p class="text-center">Memuat form...</p>';
        formModal.show();
        fetch("tambah_kontak.php?ajax=1").then(response => response.text()).then(html => {
            modalBody.innerHTML = html;
            const form = document.getElementById('tambahForm');
            setupFormListeners(form);
            form.addEventListener('submit', handleFormSubmit);
        });
    }

    function openEdit(event, id) {
        event.stopPropagation();
        modalTitle.innerHTML = '<i class="bi bi-pencil-square"></i> Edit Kontak';
        modalBody.innerHTML = '<p class="text-center">Memuat form...</p>';
        formModal.show();
        fetch(`edit_kontak.php?id=${id}&ajax=1`).then(response => response.text()).then(html => {
            modalBody.innerHTML = html;
            const form = document.getElementById('editForm');
            setupFormListeners(form);
            form.addEventListener('submit', (e) => handleFormSubmit(e, id));
        });
    }
    
    function openDeleteConfirm(event, element) {
        event.stopPropagation();
        document.getElementById('contactNameToDelete').textContent = element.getAttribute('data-contact-name');
        document.getElementById('confirmDeleteBtn').setAttribute('href', element.getAttribute('data-delete-url'));
        deleteConfirmModal.show();
    }

    function handleFormSubmit(event, id = null) {
        event.preventDefault();
        const form = event.target;
        const formData = new FormData(form);
        const url = id ? `edit_kontak.php?id=${id}&ajax=1` : 'tambah_kontak.php?ajax=1';
        
        const telpInput = form.querySelector('input[name="nomor_telp"]');
        let errorBubble = form.querySelector('.error-bubble');

        // Sembunyikan error lama sebelum mengirim
        if (errorBubble) errorBubble.style.display = 'none';
        if (telpInput) telpInput.classList.remove('is-invalid');

        fetch(url, { method: 'POST', body: formData })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                formModal.hide();
                showToast(id ? 'Kontak berhasil diperbarui!' : 'Kontak berhasil ditambahkan!');
                setTimeout(() => location.reload(), 1500); 
            } else {
                // Tampilkan bubble error
                if (telpInput) {
                    // Jika bubble tidak ada di HTML, buat secara dinamis
                    if (!errorBubble) {
                        errorBubble = document.createElement('div');
                        errorBubble.className = 'error-bubble';
                        // Masukkan bubble sebelum input nomor telepon
                        telpInput.parentNode.insertBefore(errorBubble, telpInput);
                    }
                    
                    errorBubble.innerHTML = `❌ ${data.error}`;
                    errorBubble.style.display = 'block';
                    telpInput.classList.add('is-invalid');
                } else {
                    // Fallback jika input telp tidak ditemukan
                    alert(data.error); 
                }
            }
        })
        .catch(error => console.error('Error:', error));
    }

    function shareWA(event, element) {
        event.stopPropagation();
        const row = element.closest('tr');
        const perusahaan = row.querySelector('.nama-perusahaan').textContent.trim();
        const pic = row.querySelector('.nama-pic').textContent.trim();
        const telp = row.querySelector('.nomor-telp').textContent.trim();
        const email = row.querySelector('.alamat-email').textContent.trim();
        let nomorWA = telp.replace(/^0/, '62');
        let linkWA = `https://wa.me/${nomorWA}`;
        let textToCopy = `*Kontak Mitra: ${perusahaan}*\nPIC: ${pic}\nWA: ${linkWA}`;
        if (email && email !== '-') {
            textToCopy += `\nEmail: ${email}`;
        }
        navigator.clipboard.writeText(textToCopy).then(() => showToast('Info kontak berhasil disalin!'));
    }
</script>
</body>
</html>

