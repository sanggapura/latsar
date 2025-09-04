<?php
session_start();
$conn = new mysqli("localhost", "root", "", "jejaring_db");
if ($conn->connect_error) die("Koneksi gagal: " . $conn->connect_error);

$result = $conn->query("SELECT * FROM kontak_mitra ORDER BY nama_perusahaan ASC");

include __DIR__ . "/../../views/header.php";
?>
<style>
    :root { --bs-blue-dark: #0a3d62; --bs-blue-light: #3c6382; --bs-gray: #f5f7fa; }
    .main-container { background-color: white; border-radius: 1rem; box-shadow: 0 10px 30px rgba(0,0,0,0.08); padding: 2rem; margin-top: -4rem; position: relative; z-index: 2; }
    .page-title { color: var(--bs-blue-dark); font-weight: 700; }
    .controls-section { background-color: var(--bs-gray); border-radius: 0.75rem; padding: 1.5rem; }
    .table thead th { background-color: var(--bs-blue-dark); color: white; text-align: center; vertical-align: middle; }
    .table tbody tr:hover { background-color: #e9ecef; }
    .action-buttons .btn { margin: 0 2px; }
    .modal-header { background: linear-gradient(135deg, var(--bs-blue-dark) 0%, var(--bs-blue-light) 100%); color: white; }
</style>

<div class="container my-5">
    <div class="main-container">
        <div class="text-center mb-4">
            <h1 class="page-title"><i class="bi bi-person-rolodex"></i> Manajemen Kontak Mitra</h1>
        </div>
        <div class="controls-section mb-4">
            <div class="row g-3 align-items-center">
                <div class="col-md-8"><input type="text" id="searchInput" class="form-control" placeholder="Cari berdasarkan nama perusahaan, PIC, atau email..."></div>
                <div class="col-md-4 text-end">
                    <button type="button" class="btn btn-primary w-100" onclick="openTambah()">
                        <i class="bi bi-plus-circle"></i> Tambah Kontak Baru
                    </button>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Perusahaan</th>
                        <th>Nama PIC</th>
                        <th>Nomor Telepon</th>
                        <th>Email</th>
                        <th style="width: 15%;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="contactTableBody">
                    <?php if ($result->num_rows > 0): $no = 1; while($row = $result->fetch_assoc()): ?>
                        <tr class="contact-item" 
                            data-searchable="<?= strtolower(htmlspecialchars($row['nama_perusahaan'] . ' ' . $row['nama_pic'] . ' ' . $row['alamat_email'])) ?>"
                            data-nama-perusahaan="<?= htmlspecialchars($row['nama_perusahaan']) ?>"
                            data-nama-pic="<?= htmlspecialchars($row['nama_pic']) ?>"
                            data-nomor-telp="<?= htmlspecialchars($row['nomor_telp']) ?>"
                            data-alamat-email="<?= htmlspecialchars($row['alamat_email']) ?>">
                            <td class="text-center fw-bold"><?= $no++ ?></td>
                            <td><?= htmlspecialchars($row['nama_perusahaan']) ?></td>
                            <td><?= htmlspecialchars($row['nama_pic']) ?></td>
                            <td class="text-center"><?= htmlspecialchars($row['nomor_telp'] ?: '-') ?></td>
                            <td><?= htmlspecialchars($row['alamat_email'] ?: '-') ?></td>
                            <td class="text-center action-buttons">
                                <button class="btn btn-sm btn-success" onclick="shareWA(this)" title="Salin Info WA"><i class="bi bi-whatsapp"></i></button>
                                <button class="btn btn-sm btn-primary" onclick="openEdit(<?= $row['id'] ?>)" title="Edit"><i class="bi bi-pencil-fill"></i></button>
                                <a href="delete_kontak.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Anda yakin ingin menghapus kontak ini?')" title="Hapus"><i class="bi bi-trash-fill"></i></a>
                            </td>
                        </tr>
                    <?php endwhile; else: ?>
                        <tr><td colspan="6" class="text-center p-5 text-muted">Belum ada data kontak.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="contactModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" id="modalContent">
            </div>
    </div>
</div>

<script>
    const searchInput = document.getElementById('searchInput');
    const contactItems = document.querySelectorAll('.contact-item');
    const contactModal = new bootstrap.Modal(document.getElementById('contactModal'));
    const modalContent = document.getElementById('modalContent');

    // Fungsi Pencarian
    searchInput.addEventListener('keyup', function() {
        const filter = searchInput.value.toLowerCase();
        contactItems.forEach(item => {
            item.style.display = item.getAttribute('data-searchable').includes(filter) ? '' : 'none';
        });
    });

    // Fungsi untuk membuka modal Tambah
    function openTambah() {
        modalContent.innerHTML = '<div class="modal-body text-center"><div class="spinner-border text-primary"></div><p>Memuat...</p></div>';
        contactModal.show();
        fetch('tambah_kontak.php?ajax=1')
            .then(response => response.text())
            .then(html => {
                modalContent.innerHTML = html;
                document.getElementById('tambahForm').addEventListener('submit', handleFormSubmit);
            });
    }
    
    // Fungsi untuk membuka modal Edit
    function openEdit(id) {
        modalContent.innerHTML = '<div class="modal-body text-center"><div class="spinner-border text-primary"></div><p>Memuat...</p></div>';
        contactModal.show();
        fetch(`edit_kontak.php?id=${id}&ajax=1`)
            .then(response => response.text())
            .then(html => {
                modalContent.innerHTML = html;
                document.getElementById('editForm').addEventListener('submit', handleFormSubmit);
            });
    }

    // Fungsi untuk menangani submit form (Tambah & Edit)
    function handleFormSubmit(event) {
        event.preventDefault();
        const form = event.target;
        const formData = new FormData(form);

        fetch(form.action, { method: 'POST', body: formData })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    alert('Data berhasil disimpan!');
                    contactModal.hide();
                    location.reload();
                } else {
                    alert('Gagal menyimpan data: ' + (data.error || 'Terjadi kesalahan'));
                }
            });
    }
    
    // Fungsi tombol Salin Info WA
    function shareWA(el){
        const row = el.closest('tr');
        const nama = row.getAttribute('data-nama-perusahaan');
        const pic = row.getAttribute('data-nama-pic');
        const telp = row.getAttribute('data-nomor-telp');
        const email = row.getAttribute('data-alamat-email');
        const nomorWA = telp.replace(/^0/, '62').replace(/[^0-9]/g, '');
        const linkWA = `https://wa.me/${nomorWA}`;

        let text = `*${nama}*\n- PIC: ${pic}\n- WA: ${linkWA}`;
        if(email && email !== '-') text += `\n- Email: ${email}`;

        navigator.clipboard.writeText(text).then(() => {
            alert("✅ Info kontak berhasil disalin ke clipboard!");
        }).catch(err => {
            alert("❌ Gagal menyalin info.");
        });
    }
</script>

<?php include __DIR__ . "/../../views/footer.php"; ?>