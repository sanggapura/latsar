<?php include __DIR__ . "/../header.php"; ?>

<style>
.form-container {
    max-width: 600px;
    margin: 0 auto;
    background: #fff;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.form-header {
    text-align: center;
    margin-bottom: 30px;
}

.form-header h2 {
    color: #1d71b8;
    font-size: 24px;
    margin-bottom: 10px;
}

.form-header p {
    color: #666;
    font-size: 14px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    font-weight: bold;
    color: #333;
    margin-bottom: 8px;
    font-size: 14px;
}

.form-group input {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid #e1e8ed;
    border-radius: 8px;
    font-size: 14px;
    transition: border-color 0.3s;
    box-sizing: border-box;
}

.form-group input:focus {
    outline: none;
    border-color: #1d71b8;
    box-shadow: 0 0 0 3px rgba(29, 113, 184, 0.1);
}

.required {
    color: #e74c3c;
}

.form-actions {
    display: flex;
    gap: 15px;
    justify-content: center;
    margin-top: 30px;
}

.btn-update {
    background: #3498db;
    color: white;
    padding: 12px 25px;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-update:hover {
    background: #2980b9;
    transform: translateY(-2px);
}

.btn-cancel {
    background: #95a5a6;
    color: white;
    padding: 12px 25px;
    text-decoration: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: bold;
    transition: all 0.3s;
}

.btn-cancel:hover {
    background: #7f8c8d;
    transform: translateY(-2px);
}

.back-link {
    display: inline-block;
    margin-bottom: 20px;
    color: #1d71b8;
    text-decoration: none;
    font-weight: bold;
}

.back-link:hover {
    text-decoration: underline;
}
</style>

<a href="index.php?action=contacts" class="back-link">← Kembali ke Daftar Kontak</a>

<div class="form-container">
    <div class="form-header">
        <h2>✏️ Edit Kontak Mitra</h2>
        <p>Perbarui informasi kontak perusahaan</p>
    </div>
    
    <form action="index.php?action=update_kontak" method="POST">
        <input type="hidden" name="id" value="<?= htmlspecialchars($kontak['id']) ?>">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
        
        <div class="form-group">
            <label for="nama_perusahaan">🏢 Nama Perusahaan <span class="required">*</span></label>
            <input type="text" id="nama_perusahaan" name="nama_perusahaan" value="<?= htmlspecialchars($kontak['nama_perusahaan']) ?>" placeholder="Masukkan nama perusahaan" required>
        </div>
        
        <div class="form-group">
            <label for="nama_pic">👤 Nama PIC (Person In Charge) <span class="required">*</span></label>
            <input type="text" id="nama_pic" name="nama_pic" value="<?= htmlspecialchars($kontak['nama_pic']) ?>" placeholder="Masukkan nama penanggung jawab" required>
        </div>
        
        <div class="form-group">
            <label for="nomor_telp">📱 Nomor Telepon</label>
            <input type="tel" id="nomor_telp" name="nomor_telp" value="<?= htmlspecialchars($kontak['nomor_telp']) ?>" placeholder="Masukkan nomor telepon (opsional)">
        </div>
        
        <div class="form-group">
            <label for="alamat_email">📧 Alamat Email <span class="required">*</span></label>
            <input type="email" id="alamat_email" name="alamat_email" value="<?= htmlspecialchars($kontak['alamat_email']) ?>" placeholder="Masukkan alamat email" required>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn-update">💾 Update Kontak</button>
            <a href="index.php?action=contacts" class="btn-cancel">❌ Batal</a>
        </div>
    </form>
</div>

<?php include __DIR__ . "/../footer.php"; ?>