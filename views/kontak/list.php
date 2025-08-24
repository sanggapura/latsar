<?php include __DIR__ . "/../header.php"; ?>

<style>
.kontak-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
}

.kontak-header h2 {
    margin: 0;
    color: #1d71b8;
    font-size: 28px;
}

.btn-add {
    background: #27ae60;
    color: white;
    padding: 12px 20px;
    text-decoration: none;
    border-radius: 8px;
    font-weight: bold;
    transition: 0.3s;
}

.btn-add:hover {
    background: #219150;
    transform: translateY(-2px);
}

.kontak-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 25px;
    margin-bottom: 30px;
}

.kontak-card {
    background: #fff;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    border-left: 5px solid #1d71b8;
    transition: transform 0.3s, box-shadow 0.3s;
}

.kontak-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.company-name {
    font-size: 18px;
    font-weight: bold;
    color: #1d71b8;
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 2px solid #e8f4f8;
}

.contact-info {
    margin-bottom: 12px;
}

.contact-label {
    font-weight: bold;
    color: #666;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 5px;
}

.contact-value {
    color: #333;
    font-size: 15px;
    margin-bottom: 8px;
}

.contact-actions {
    margin-top: 20px;
    padding-top: 15px;
    border-top: 1px solid #eee;
    display: flex;
    gap: 10px;
}

.btn-edit {
    background: #3498db;
    color: white;
    padding: 8px 15px;
    text-decoration: none;
    border-radius: 5px;
    font-size: 13px;
    font-weight: bold;
    transition: 0.2s;
}

.btn-edit:hover {
    background: #2980b9;
}

.btn-delete {
    background: #e74c3c;
    color: white;
    padding: 8px 15px;
    border: none;
    border-radius: 5px;
    font-size: 13px;
    font-weight: bold;
    cursor: pointer;
    transition: 0.2s;
}

.btn-delete:hover {
    background: #c0392b;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #666;
}

.empty-state i {
    font-size: 64px;
    color: #bdc3c7;
    margin-bottom: 20px;
}

@media (max-width: 1024px) {
    .kontak-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .kontak-grid {
        grid-template-columns: 1fr;
    }
    
    .kontak-header {
        flex-direction: column;
        gap: 15px;
        text-align: center;
    }
}
</style>

<div class="kontak-header">
    <h2>📞 Kontak Mitra Perusahaan</h2>
    <a href="index.php?action=create_kontak" class="btn-add">+ Tambah Kontak Baru</a>
</div>

<div class="kontak-grid">
    <?php 
    $count = 0;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): 
        $count++;
    ?>
        <div class="kontak-card">
            <div class="company-name">
                🏢 <?= htmlspecialchars($row['nama_perusahaan']) ?>
            </div>
            
            <div class="contact-info">
                <div class="contact-label">👤 Nama PIC</div>
                <div class="contact-value"><?= htmlspecialchars($row['nama_pic']) ?></div>
            </div>
            
            <div class="contact-info">
                <div class="contact-label">📱 Nomor Telepon</div>
                <div class="contact-value">
                    <?php if ($row['nomor_telp']): ?>
                        <a href="tel:<?= htmlspecialchars($row['nomor_telp']) ?>" style="color: #27ae60; text-decoration: none;">
                            <?= htmlspecialchars($row['nomor_telp']) ?>
                        </a>
                    <?php else: ?>
                        <span style="color: #999; font-style: italic;">Tidak ada</span>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="contact-info">
                <div class="contact-label">📧 Alamat Email</div>
                <div class="contact-value">
                    <a href="mailto:<?= htmlspecialchars($row['alamat_email']) ?>" style="color: #3498db; text-decoration: none;">
                        <?= htmlspecialchars($row['alamat_email']) ?>
                    </a>
                </div>
            </div>
            
            <div class="contact-actions">
                <a href="index.php?action=edit_kontak&id=<?= (int)$row['id'] ?>" class="btn-edit">✏️ Edit</a>
                <form style="display: inline;" method="POST" action="index.php?action=delete_kontak" onsubmit="return confirm('Yakin ingin menghapus kontak <?= htmlspecialchars($row['nama_perusahaan']) ?>?')">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                    <input type="hidden" name="id" value="<?= (int)$row['id'] ?>">
                    <button type="submit" class="btn-delete">🗑️ Hapus</button>
                </form>
            </div>
        </div>
    <?php endwhile; ?>
</div>

<?php if ($count === 0): ?>
    <div class="empty-state">
        <i>📞</i>
        <h3>Belum ada kontak mitra</h3>
        <p>Silakan tambahkan kontak mitra perusahaan pertama Anda.</p>
        <a href="index.php?action=create_kontak" class="btn-add">+ Tambah Kontak Pertama</a>
    </div>
<?php endif; ?>

<?php include __DIR__ . "/../footer.php"; ?>