<?php
session_start();
$conn = new mysqli("localhost", "root", "", "jejaring_db");
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

<style>
/* Modern CSS Variables */
:root {
    --primary-color: #2563eb;
    --secondary-color: #64748b;
    --success-color: #10b981;
    --warning-color: #f59e0b;
    --danger-color: #ef4444;
    --text-primary: #1e293b;
    --text-secondary: #64748b;
    --bg-primary: #ffffff;
    --bg-secondary: #f8fafc;
    --border-color: #e2e8f0;
    --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
    --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
    --border-radius: 8px;
    --spacing-xs: 4px;
    --spacing-sm: 8px;
    --spacing-md: 16px;
    --spacing-lg: 24px;
    --spacing-xl: 32px;
}

* {
    box-sizing: border-box;
}

.document-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: var(--spacing-lg);
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    color: var(--text-primary);
    background: var(--bg-secondary);
    min-height: 100vh;
}

.header {
    margin-bottom: var(--spacing-xl);
}

.header h2 {
    font-size: 1.875rem;
    font-weight: 600;
    color: var(--text-primary);
    margin: 0 0 var(--spacing-sm) 0;
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
}

.header-subtitle {
    color: var(--text-secondary);
    font-size: 0.875rem;
    margin: 0;
}

.controls {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: var(--spacing-lg);
    gap: var(--spacing-md);
    flex-wrap: wrap;
}

.search-form {
    display: flex;
    gap: var(--spacing-sm);
    align-items: center;
    flex: 1;
    max-width: 400px;
}

.search-input {
    flex: 1;
    padding: var(--spacing-sm) var(--spacing-md);
    border: 1px solid var(--border-color);
    border-radius: var(--border-radius);
    font-size: 0.875rem;
    background: var(--bg-primary);
    transition: all 0.2s ease;
    outline: none;
}

.search-input:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgb(37 99 235 / 0.1);
}

.btn {
    padding: var(--spacing-sm) var(--spacing-md);
    border: none;
    border-radius: var(--border-radius);
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: var(--spacing-xs);
    transition: all 0.2s ease;
    outline: none;
}

.btn-primary {
    background: var(--primary-color);
    color: white;
}

.btn-primary:hover {
    background: #1d4ed8;
    transform: translateY(-1px);
    box-shadow: var(--shadow-md);
}

.btn-success {
    background: var(--success-color);
    color: white;
}

.btn-success:hover {
    background: #059669;
    transform: translateY(-1px);
    box-shadow: var(--shadow-md);
}

.btn-warning {
    background: var(--warning-color);
    color: white;
}

.btn-warning:hover {
    background: #d97706;
    transform: translateY(-1px);
    box-shadow: var(--shadow-md);
}

.btn-danger {
    background: var(--danger-color);
    color: white;
}

.btn-danger:hover {
    background: #dc2626;
    transform: translateY(-1px);
    box-shadow: var(--shadow-md);
}

.btn-secondary {
    background: var(--secondary-color);
    color: white;
}

.btn-secondary:hover {
    background: #475569;
    transform: translateY(-1px);
    box-shadow: var(--shadow-md);
}

.table-container {
    background: var(--bg-primary);
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--border-color);
    overflow: hidden;
}

.table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.875rem;
}

.table th {
    background: var(--bg-secondary);
    padding: var(--spacing-md);
    text-align: left;
    font-weight: 600;
    color: var(--text-primary);
    border-bottom: 1px solid var(--border-color);
}

.table td {
    padding: var(--spacing-md);
    border-bottom: 1px solid var(--border-color);
    vertical-align: middle;
}

.table tr:hover {
    background: #f8fafc;
}

.file-type {
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
    font-weight: 500;
}

.file-icon {
    width: 20px;
    height: 20px;
    flex-shrink: 0;
}

.actions {
    display: flex;
    gap: var(--spacing-sm);
    flex-wrap: wrap;
}

.date-text {
    color: var(--text-secondary);
    font-size: 0.8125rem;
}

.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1000;
    align-items: center;
    justify-content: center;
    padding: var(--spacing-md);
}

.modal-content {
    background: var(--bg-primary);
    border-radius: var(--border-radius);
    padding: var(--spacing-xl);
    width: 100%;
    max-width: 500px;
    box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
    position: relative;
}

.modal-header {
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: var(--spacing-lg);
    color: var(--text-primary);
}

.modal-close {
    position: absolute;
    top: var(--spacing-md);
    right: var(--spacing-md);
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
    color: var(--text-secondary);
    padding: var(--spacing-xs);
}

.modal-close:hover {
    color: var(--text-primary);
}

.form-group {
    margin-bottom: var(--spacing-md);
}

.form-label {
    display: block;
    margin-bottom: var(--spacing-xs);
    font-weight: 500;
    color: var(--text-primary);
    font-size: 0.875rem;
}

.form-input {
    width: 100%;
    padding: var(--spacing-sm) var(--spacing-md);
    border: 1px solid var(--border-color);
    border-radius: var(--border-radius);
    font-size: 0.875rem;
    background: var(--bg-primary);
    transition: all 0.2s ease;
    outline: none;
}

.form-input:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgb(37 99 235 / 0.1);
}

.empty-state {
    text-align: center;
    padding: var(--spacing-xl);
    color: var(--text-secondary);
}

@media (max-width: 768px) {
    .controls {
        flex-direction: column;
        align-items: stretch;
    }
    
    .search-form {
        max-width: none;
    }
    
    .actions {
        flex-direction: column;
    }
    
    .table-container {
        overflow-x: auto;
    }
    
    .table {
        min-width: 600px;
    }
}
</style>

<div class="document-container">
    <div class="header">
        <h2>📂 Manajemen Dokumen</h2>
        <p class="header-subtitle">Kelola dan atur dokumen Anda dengan mudah</p>
    </div>

    <div class="controls">
        <form class="search-form" method="GET">
            <input type="text" 
                   name="search" 
                   class="search-input"
                   placeholder="Cari dokumen..." 
                   value="<?= htmlspecialchars($search) ?>">
            <button type="submit" class="btn btn-primary">
                🔍 Cari
            </button>
            <?php if ($search): ?>
                <a href="?" class="btn btn-secondary">Reset</a>
            <?php endif; ?>
        </form>
        
        <a href="dokumen_tambah.php" class="btn btn-success">
            ➕ Tambah Dokumen
        </a>
    </div>

    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Judul Dokumen</th>
                    <th>Jenis File</th>
                    <th>Tanggal</th>
                    <th>File</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <div style="font-weight: 500;">
                                    <?= htmlspecialchars($row['judul']) ?>
                                </div>
                            </td>
                            <td>
                                <div class="file-type">
                                    <?php if ($row['jenis'] == 'word'): ?>
                                        <img src="icons/word.png" class="file-icon" alt="Word">
                                        Word Document
                                    <?php elseif ($row['jenis'] == 'excel'): ?>
                                        <img src="icons/excel.png" class="file-icon" alt="Excel">
                                        Excel Spreadsheet
                                    <?php elseif ($row['jenis'] == 'pdf'): ?>
                                        <img src="icons/pdf.png" class="file-icon" alt="PDF">
                                        PDF Document
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="date-text">
                                <?= date('d M Y', strtotime($row['tanggal'])) ?>
                            </td>
                            <td>
                                <a href="<?= htmlspecialchars($row['file_path']) ?>" 
                                   class="btn btn-secondary" 
                                   download>
                                   ⬇️ Download
                                </a>
                            </td>
                            <td>
                                <div class="actions">
                                    <button class="btn btn-warning"
                                            onclick="openEditModal(<?= $row['id'] ?>,'<?= htmlspecialchars($row['judul'],ENT_QUOTES) ?>')">
                                        ✏️ Edit
                                    </button>
                                    <a href="dokumen_delete.php?id=<?= $row['id'] ?>" 
                                       class="btn btn-danger"
                                       onclick="return confirm('Apakah Anda yakin ingin menghapus dokumen ini?')">
                                       🗑️ Hapus
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">
                            <div class="empty-state">
                                <p>📄 Tidak ada dokumen yang ditemukan</p>
                                <?php if ($search): ?>
                                    <p>Coba gunakan kata kunci yang berbeda</p>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal" id="editModal">
    <div class="modal-content">
        <button class="modal-close" onclick="closeModal()">&times;</button>
        <div class="modal-header">Edit Dokumen</div>
        <form action="dokumen_update.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" id="editId">
            
            <div class="form-group">
                <label class="form-label">Judul Dokumen</label>
                <input type="text" 
                       name="judul" 
                       id="editJudul" 
                       class="form-input"
                       required>
            </div>
            
            <div class="form-group">
                <label class="form-label">File Baru (opsional)</label>
                <input type="file" 
                       name="file" 
                       class="form-input"
                       accept=".pdf,.doc,.docx,.xls,.xlsx">
                <small style="color: var(--text-secondary); font-size: 0.75rem;">
                    Biarkan kosong jika tidak ingin mengubah file
                </small>
            </div>
            
            <div style="display: flex; gap: var(--spacing-sm); justify-content: flex-end; margin-top: var(--spacing-lg);">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">
                    Batal
                </button>
                <button type="submit" class="btn btn-success">
                    💾 Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditModal(id, judul) {
    document.getElementById('editId').value = id;
    document.getElementById('editJudul').value = judul;
    document.getElementById('editModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    document.getElementById('editModal').style.display = 'none';
    document.body.style.overflow = 'auto';
}

// Close modal when clicking outside
document.getElementById('editModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeModal();
    }
});
</script>

<?php
// tutup main + body + html dari header.php
echo "</main></body></html>";
?>