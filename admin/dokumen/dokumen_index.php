<?php
session_start();
$conn = new mysqli("localhost", "root", "", "jejaring_db");
if ($conn->connect_error) die("Koneksi gagal: " . $conn->connect_error);

// pencarian
$search = $_GET['search'] ?? '';
if ($search) {
    $stmt = $conn->prepare("SELECT * FROM dokumen WHERE judul LIKE ? ORDER BY tanggal DESC");
    $param = "%$search%";
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
/* Global Styling */
body {
    background: #f8f9fa;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    color: #333;
}

.container {
    max-width: 1200px;
    padding: 15px 0;
}

/* Header */
.page-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #222;
    text-align: center;
    margin: 15px 0;
}

/* Top Controls */
.top-controls {
    display: flex;
    gap: 10px;
    margin: 0 20px 20px;
    align-items: center;
    flex-wrap: wrap;
}

.btn {
    border: none;
    border-radius: 8px;
    padding: 7px 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
}

.btn-add {
    background: #28a745;
    color: white;
}

.btn-add:hover {
    background: #218838;
}

.search-input {
    flex: 1;
    max-width: 180px;
    position: relative;
}

.search-input input {
    width: 100%;
    padding: 7px 28px 7px 28px;
    border: 1px solid #ccc;
    border-radius: 8px;
    font-size: 13px;
    background: white;
}

.search-input::before {
    content: "🔍";
    position: absolute;
    left: 8px;
    top: 50%;
    transform: translateY(-50%);
    color: #888;
    font-size: 14px;
}

.btn-reset {
    background: #6c757d;
    color: white;
    padding: 7px 12px;
}

.btn-reset:hover {
    background: #5a6268;
}

/* Cards Grid */
.cards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 16px;
    margin-top: 20px;
    padding: 0 20px 40px;
}

.document-card {
    background: white;
    border-radius: 16px;
    padding: 16px;
    box-shadow: 0 6px 16px rgba(0,0,0,0.1);
    border: 1px solid #e0e0e0;
    transition: all 0.2s ease;
    position: relative;
}

.document-card:hover {
    box-shadow: 0 10px 20px rgba(0,0,0,0.15);
    transform: translateY(-5px);
}

.card-header {
    margin-bottom: 12px;
    position: relative;
    padding-right: 80px;
}

.document-title {
    font-size: 1rem;
    font-weight: 600;
    color: #111;
    margin: 0;
    line-height: 1.3;
}

.card-top-actions {
    position: absolute;
    top: 0;
    right: 0;
    display: flex;
    gap: 4px;
}

.card-info {
    margin-bottom: 16px;
}

.file-extension {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 8px;
}

.file-icon {
    font-size: 20px;
}

.file-ext {
    background: #e9ecef;
    color: #495057;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 600;
}

.upload-date {
    font-size: 13px;
    color: #666;
}

.card-top-actions .btn-view,
.card-top-actions .btn-download {
    background: #2196F3;
    color: white;
    border: none;
    border-radius: 4px;
    padding: 6px 8px;
    font-size: 12px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.card-top-actions .btn-view:hover,
.card-top-actions .btn-download:hover {
    background: #1976d2;
}

.info-value {
    color: #333;
    flex: 1;
}

.file-type-badge {
    background: #e9ecef;
    color: #495057;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 600;
    display: inline-block;
}

.info-value {
    color: #333;
    flex: 1;
}

.card-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.btn-sm {
    padding: 6px 12px;
    font-size: 12px;
    border-radius: 6px;
}

.btn-view {
    background: #17a2b8;
    color: white;
}

.btn-view:hover {
    background: #138496;
}

.btn-download {
    background: #6c757d;
    color: white;
}

.btn-download:hover {
    background: #5a6268;
}

.btn-edit {
    background: #ffc107;
    color: #212529;
}

.btn-edit:hover {
    background: #e0a800;
}

.btn-delete {
    background: #dc3545;
    color: white;
}

.btn-delete:hover {
    background: #c82333;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #666;
}

.empty-icon {
    font-size: 48px;
    margin-bottom: 20px;
    opacity: 0.5;
}

/* Add Panel */
.add-panel {
    display: none;
    position: absolute;
    top: 100%;
    left: 0;
    width: 350px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.15);
    border: 1px solid #e9ecef;
    z-index: 1000;
    margin-top: 5px;
}

.add-panel-header {
    padding: 15px 20px;
    border-bottom: 1px solid #e9ecef;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.add-panel-title {
    font-weight: 600;
    color: #333;
}

.close-btn {
    background: none;
    border: none;
    font-size: 18px;
    cursor: pointer;
    color: #666;
    padding: 0;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.add-panel-body {
    padding: 20px;
}

.form-group {
    margin-bottom: 15px;
}

.form-label {
    display: block;
    margin-bottom: 5px;
    font-weight: 500;
    color: #333;
    font-size: 14px;
}

.form-control {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 14px;
    background: white;
    box-sizing: border-box;
}

.form-control:focus {
    outline: none;
    border-color: #28a745;
    box-shadow: 0 0 0 2px rgba(40, 167, 69, 0.1);
}

.file-help {
    font-size: 12px;
    color: #666;
    margin-top: 5px;
}

.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-top: 20px;
}

.btn-save {
    background: #28a745;
    color: white;
}

.btn-save:hover {
    background: #218838;
}

/* Modal */
.modal {
    backdrop-filter: blur(4px);
    background: rgba(0,0,0,0.5);
}

.modal-content {
    background: white;
    border-radius: 12px;
    border: none;
    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
}

.modal-header {
    background: #f8f9fa;
    border-bottom: 1px solid #e9ecef;
    border-radius: 12px 12px 0 0;
    padding: 20px;
}

.modal-title {
    font-weight: 600;
    color: #333;
    margin: 0;
}

.modal-body {
    padding: 20px;
}

/* Responsive */
@media (max-width: 768px) {
    .top-controls {
        flex-direction: column;
        align-items: stretch;
        padding: 0 20px;
    }
    
    .search-input {
        max-width: none;
    }
    
    .cards-grid {
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 12px;
    }
    
    .add-panel {
        width: calc(100vw - 40px);
        left: 50%;
        transform: translateX(-50%);
    }
}

@media (max-width: 480px) {
    .document-card {
        padding: 12px;
    }
    
    .card-actions {
        flex-direction: column;
        gap: 4px;
    }
    
    .btn-sm {
        width: 100%;
        justify-content: center;
        font-size: 10px;
        padding: 4px 6px;
    }
    
    .card-header {
        padding-right: 60px;
    }
    
    .card-top-actions {
        gap: 2px;
    }
}
</style>

<div class="container">
    <h1 class="page-title">Manajemen Dokumen</h1>
    
    <!-- Top Controls -->
    <div class="top-controls">
        <div style="position: relative;">
            <button onclick="toggleAdd()" class="btn btn-add">+ Tambah Dokumen</button>
            
            <!-- Add Panel -->
            <div id="addPanel" class="add-panel">
                <div class="add-panel-header">
                    <span class="add-panel-title">Tambah Dokumen</span>
                    <button type="button" class="close-btn" onclick="toggleAdd()">×</button>
                </div>
                <div class="add-panel-body">
                    <form action="dokumen_tambah.php" method="POST" enctype="multipart/form-data" id="tambahForm">
                        <div class="form-group">
                            <label class="form-label">Judul</label>
                            <input type="text" name="judul" class="form-control" placeholder="Masukkan judul dokumen" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">File</label>
                            <input type="file" name="file" id="fileInput" class="form-control" accept=".pdf,.doc,.docx,.xls,.xlsx" required>
                            <div class="file-help">Format: PDF, DOC, DOCX, XLS, XLSX</div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Jenis File</label>
                            <input type="text" name="jenis" id="jenisFile" class="form-control" placeholder="Akan terisi otomatis" readonly style="background: #f8f9fa;">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Tanggal Upload</label>
                            <input type="text" name="tanggal" id="tanggalUpload" class="form-control" readonly style="background: #f8f9fa;" value="<?= date('d M Y') ?>">
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn btn-save">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="search-input">
            <form method="GET" style="display: flex; gap: 10px; align-items: center;">
                <input type="text" name="search" placeholder="Cari..." value="<?= htmlspecialchars($search) ?>">
                <?php if ($search): ?>
                <button type="button" onclick="window.location='?'" class="btn btn-reset">Reset</button>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <!-- Documents Grid -->
    <div class="cards-grid">
        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="document-card">
                    <div class="card-header">
                        <h3 class="document-title"><?= htmlspecialchars($row['judul']) ?></h3>
                        <div class="card-top-actions">
                            <button type="button" class="btn-view" onclick="openView('<?= htmlspecialchars($row['file_path'],ENT_QUOTES) ?>')" title="Lihat">
                                👁️
                            </button>
                            <a href="<?= htmlspecialchars($row['file_path']) ?>" class="btn-download" download title="Download">
                                ⬇️
                            </a>
                        </div>
                    </div>
                    
                    <div class="card-info">
                        <div class="file-extension">
                            <?php 
                            $ext = strtoupper(pathinfo($row['file_path'], PATHINFO_EXTENSION));
                            $icon = '📄'; // default
                            if($ext == 'PDF') $icon = '📄';
                            elseif(in_array($ext, ['DOC', 'DOCX'])) $icon = '📝';
                            elseif(in_array($ext, ['XLS', 'XLSX'])) $icon = '📊';
                            ?>
                            <span class="file-icon"><?= $icon ?></span>
                            <span class="file-ext"><?= $ext ?></span>
                        </div>
                        <div class="upload-date">
                            <?= date('d M Y', strtotime($row['tanggal'])) ?>
                        </div>
                    </div>
                    
                    <div class="card-actions">
                        <button class="btn btn-sm btn-edit" onclick="openEdit(<?= $row['id'] ?>,'<?= htmlspecialchars($row['judul'],ENT_QUOTES) ?>')">
                            ✏️ Edit
                        </button>
                        <a href="dokumen_delete.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-delete" onclick="return confirm('Yakin hapus?')">
                            🗑️ Delete
                        </a>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="empty-state" style="grid-column: 1 / -1;">
                <div class="empty-icon">📂</div>
                <h3>Tidak ada dokumen</h3>
                <p>Belum ada dokumen yang ditambahkan. Klik "Tambah Dokumen" untuk mulai.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- View Modal -->
<div class="modal" id="viewModal" tabindex="-1" style="display:none;align-items:center;justify-content:center;position:fixed;top:0;left:0;width:100%;height:100%;">
    <div class="modal-dialog modal-xl modal-dialog-centered" style="max-width:90%;width:90%;">
        <div class="modal-content" style="max-height:90vh;">
            <div class="modal-header">
                <h5 class="modal-title">Lihat Dokumen</h5>
                <button type="button" class="close-btn" onclick="closeView()">×</button>
            </div>
            <div class="modal-body" style="height:80vh;">
                <iframe id="viewFrame" src="" style="width:100%;height:100%;border:none;border-radius:8px;"></iframe>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal" id="editModal" tabindex="-1" style="display:none;align-items:center;justify-content:center;position:fixed;top:0;left:0;width:100%;height:100%;">
    <div class="modal-dialog modal-dialog-centered" style="max-width:500px;width:90%;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Dokumen</h5>
                <button type="button" class="close-btn" onclick="closeModal()">×</button>
            </div>
            <div class="modal-body">
                <form action="dokumen_update.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id" id="editId">
                    <div class="form-group">
                        <label class="form-label">Judul</label>
                        <input type="text" name="judul" id="editJudul" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">File Baru (opsional)</label>
                        <input type="file" name="file" class="form-control" accept=".pdf,.doc,.docx,.xls,.xlsx">
                        <div class="file-help">Kosongkan jika tidak ingin mengganti file</div>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn btn-reset" onclick="closeModal()">Batal</button>
                        <button type="submit" class="btn btn-save">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function toggleAdd(){
    let panel = document.getElementById("addPanel");
    panel.style.display = (panel.style.display === "block") ? "none" : "block";
}

function openEdit(id, judul){
    document.getElementById('editId').value = id;
    document.getElementById('editJudul').value = judul;
    document.getElementById('editModal').style.display = 'flex';
    
    // Setup auto-fill for edit form
    const editFileInput = document.getElementById('editFileInput');
    const editJenisFile = document.getElementById('editJenisFile');
    const editForm = document.getElementById('editForm');
    
    if (editFileInput && editJenisFile) {
        // Clear previous values
        editJenisFile.value = '';
        
        // Remove previous event listeners
        editFileInput.removeEventListener('change', handleEditFileChange);
        editFileInput.addEventListener('change', handleEditFileChange);
    }
    
    // Handle edit form submission with AJAX
    if (editForm) {
        editForm.removeEventListener('submit', handleEditFormSubmit);
        editForm.addEventListener('submit', handleEditFormSubmit);
    }
}

function handleEditFileChange() {
    const editJenisFile = document.getElementById('editJenisFile');
    if (this.files && this.files[0]) {
        const fileName = this.files[0].name;
        const extension = fileName.split('.').pop().toUpperCase();
        editJenisFile.value = extension;
    } else {
        editJenisFile.value = '';
    }
}

function handleEditFormSubmit(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    
    // Show loading state
    submitBtn.textContent = 'Menyimpan...';
    submitBtn.disabled = true;
    
    fetch('dokumen_update.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        if (data.includes('berhasil') || data.includes('success')) {
            alert('✅ Dokumen berhasil diperbarui!');
            closeModal();
            location.reload();
        } else {
            alert('❌ Gagal memperbarui dokumen. Silakan coba lagi.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('❌ Terjadi kesalahan. Silakan coba lagi.');
    })
    .finally(() => {
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
    });
}

function closeModal(){
    document.getElementById('editModal').style.display = 'none';
}

function openView(filePath){
    let ext = filePath.split('.').pop().toLowerCase();
    let frame = document.getElementById('viewFrame');
    if(ext === 'pdf'){
        frame.src = "view.php?file=" + encodeURIComponent(filePath);
    } else {
        frame.src = "https://docs.google.com/gview?url=" + window.location.origin + "/" + filePath + "&embedded=true";
    }
    document.getElementById('viewModal').style.display = 'flex';
}

function closeView(){
    document.getElementById('viewModal').style.display = 'none';
    document.getElementById('viewFrame').src = "";
}

function shareWhatsApp(title) {
    // Function removed as WhatsApp button is no longer needed
}

// Close panels when clicking outside
window.addEventListener('click', function(e){
    let panel = document.getElementById("addPanel");
    let btn = e.target.closest('.btn-add');
    if (panel.style.display === "block" && !panel.contains(e.target) && !btn) {
        panel.style.display = "none";
    }
});

// Auto-fill file type when file is selected
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('fileInput');
    const jenisFile = document.getElementById('jenisFile');
    const tambahForm = document.getElementById('tambahForm');
    
    if (fileInput && jenisFile) {
        fileInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const fileName = this.files[0].name;
                const extension = fileName.split('.').pop().toUpperCase();
                jenisFile.value = extension;
            } else {
                jenisFile.value = '';
            }
        });
    }
    
    // Handle form submission with AJAX
    if (tambahForm) {
        tambahForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            
            // Show loading state
            submitBtn.textContent = 'Menyimpan...';
            submitBtn.disabled = true;
            
            fetch('dokumen_tambah.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                // Check if upload was successful (you may need to modify this based on your PHP response)
                if (data.includes('berhasil') || data.includes('success')) {
                    alert('✅ Dokumen berhasil ditambahkan!');
                    
                    // Reset form
                    tambahForm.reset();
                    jenisFile.value = '';
                    
                    // Close panel
                    toggleAdd();
                    
                    // Reload page to show new document
                    location.reload();
                } else {
                    alert('❌ Gagal menambahkan dokumen. Silakan coba lagi.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('❌ Terjadi kesalahan. Silakan coba lagi.');
            })
            .finally(() => {
                // Reset button state
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            });
        });
    }
});

// Close modals when clicking outside
window.onclick = function(e){
    if(e.target == document.getElementById('editModal')) closeModal();
    if(e.target == document.getElementById('viewModal')) closeView();
}
</script>

<?php
echo "</main></body></html>";
?>