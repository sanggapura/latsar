<?php
include "db.php"; 

error_reporting(E_ALL);
ini_set('display_errors', 1);

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validasi input utama
    if (empty(trim($_POST['nama_mitra']))) {
        $errors['nama_mitra'] = "Nama Mitra wajib diisi";
    }
    if (empty(trim($_POST['jenis_mitra']))) {
        $errors['jenis_mitra'] = "Jenis Mitra wajib dipilih";
    }

    // Jika lolos validasi
    if (count($errors) === 0) {
        $sql = "INSERT INTO tahapan_kerjasama 
            (nama_mitra, jenis_mitra, sumber_usulan, tandai,
             status_kesepahaman, nomor_kesepahaman, tanggal_kesepahaman, ruanglingkup_kesepahaman, status_pelaksanaan_kesepahaman,
             rencana_pertemuan_kesepahaman, rencana_kolaborasi_kesepahaman, status_progres_kesepahaman, tindaklanjut_kesepahaman, keterangan_kesepahaman,
             status_pks, nomor_pks, tanggal_pks, ruanglingkup_pks, status_pelaksanaan_pks,
             rencana_pertemuan_pks, status_progres_pks, tindaklanjut_pks, keterangan_pks)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

        $stmt = $conn->prepare($sql);

        $tandai = isset($_POST['tandai']) ? 1 : 0;

        // Bind parameter (23 kolom total)
        $stmt->bind_param(
            "sssisssssssssssssssssss", 
            $_POST['nama_mitra'],                 
            $_POST['jenis_mitra'],                
            $_POST['sumber_usulan'],              
            $tandai,                              
            $_POST['status_kesepahaman'],         
            $_POST['nomor_kesepahaman'],          
            $_POST['tanggal_kesepahaman'],        
            $_POST['ruanglingkup_kesepahaman'],   
            $_POST['status_pelaksanaan_kesepahaman'],
            $_POST['rencana_pertemuan_kesepahaman'],
            $_POST['rencana_kolaborasi_kesepahaman'],
            $_POST['status_progres_kesepahaman'], 
            $_POST['tindaklanjut_kesepahaman'],   
            $_POST['keterangan_kesepahaman'],     
            $_POST['status_pks'],                 
            $_POST['nomor_pks'],                  
            $_POST['tanggal_pks'],                
            $_POST['ruanglingkup_pks'],           
            $_POST['status_pelaksanaan_pks'],     
            $_POST['rencana_pertemuan_pks'],      
            $_POST['status_progres_pks'],         
            $_POST['tindaklanjut_pks'],           
            $_POST['keterangan_pks']              
        );

        if ($stmt->execute()) {
            header("Location: index.php");
            exit();
        } else {
            echo "<div class='alert alert-danger'>Terjadi kesalahan: " . htmlspecialchars($stmt->error) . "</div>";
        }
    }
}
?>

<?php include __DIR__ . "/../../views/header.php"; ?>

<div class="container mt-4">
    <h2>Tambah Tahapan Kerjasama</h2>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $err): ?>
                    <li><?= htmlspecialchars($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" class="row g-3">
        <!-- Mitra -->
        <div class="col-md-6">
            <label class="form-label">Nama Mitra</label>
            <input type="text" name="nama_mitra" class="form-control" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Jenis Mitra</label>
            <select name="jenis_mitra" class="form-select" required>
                <option value="">-- Pilih --</option>
                <option value="Kementerian/Lembaga">Kementerian/Lembaga</option>
                <option value="Perguruan Tinggi">Perguruan Tinggi</option>
                <option value="Pemerintah Daerah">Pemerintah Daerah</option>
                <option value="BUMN/BUMD">BUMN/BUMD</option>
                <option value="Swasta">Swasta</option>
                <option value="Asosiasi">Asosiasi</option>
            </select>
        </div>
        <div class="col-md-12">
            <label class="form-label">Sumber Usulan</label>
            <input type="text" name="sumber_usulan" class="form-control">
        </div>

        <!-- Tandai -->
        <div class="col-md-12">
            <label class="form-check-label">
                <input type="checkbox" name="tandai" class="form-check-input"> Tandai (ada kekurangan)
            </label>
        </div>

        <hr>

        <!-- Kesepahaman -->
        <h5>Kesepahaman</h5>
        <div class="col-md-6">
            <label class="form-label">Status</label>
            <input type="text" name="status_kesepahaman" class="form-control">
        </div>
        <div class="col-md-6">
            <label class="form-label">Nomor</label>
            <input type="text" name="nomor_kesepahaman" class="form-control">
        </div>
        <div class="col-md-6">
            <label class="form-label">Tanggal</label>
            <input type="date" name="tanggal_kesepahaman" class="form-control">
        </div>
        <div class="col-md-6">
            <label class="form-label">Ruang Lingkup</label>
            <textarea name="ruanglingkup_kesepahaman" class="form-control"></textarea>
        </div>
        <div class="col-md-6">
            <label class="form-label">Status Pelaksanaan</label>
            <input type="text" name="status_pelaksanaan_kesepahaman" class="form-control">
        </div>
        <div class="col-md-6">
            <label class="form-label">Rencana Pertemuan</label>
            <input type="date" name="rencana_pertemuan_kesepahaman" class="form-control">
        </div>
        <div class="col-md-6">
            <label class="form-label">Rencana Kolaborasi</label>
            <textarea name="rencana_kolaborasi_kesepahaman" class="form-control"></textarea>
        </div>
        <div class="col-md-6">
            <label class="form-label">Status Progres</label>
            <textarea name="status_progres_kesepahaman" class="form-control"></textarea>
        </div>
        <div class="col-md-6">
            <label class="form-label">Tindak Lanjut</label>
            <textarea name="tindaklanjut_kesepahaman" class="form-control"></textarea>
        </div>
        <div class="col-md-6">
            <label class="form-label">Keterangan</label>
            <input type="text" name="keterangan_kesepahaman" class="form-control">
        </div>

        <hr>

        <!-- PKS -->
        <h5>PKS</h5>
        <div class="col-md-6">
            <label class="form-label">Status</label>
            <input type="text" name="status_pks" class="form-control">
        </div>
        <div class="col-md-6">
            <label class="form-label">Nomor</label>
            <input type="text" name="nomor_pks" class="form-control">
        </div>
        <div class="col-md-6">
            <label class="form-label">Tanggal</label>
            <input type="date" name="tanggal_pks" class="form-control">
        </div>
        <div class="col-md-6">
            <label class="form-label">Ruang Lingkup</label>
            <textarea name="ruanglingkup_pks" class="form-control"></textarea>
        </div>
        <div class="col-md-6">
            <label class="form-label">Status Pelaksanaan</label>
            <input type="text" name="status_pelaksanaan_pks" class="form-control">
        </div>
        <div class="col-md-6">
            <label class="form-label">Rencana Pertemuan</label>
            <input type="date" name="rencana_pertemuan_pks" class="form-control">
        </div>
        <div class="col-md-6">
            <label class="form-label">Status Progres</label>
            <textarea name="status_progres_pks" class="form-control"></textarea>
        </div>
        <div class="col-md-6">
            <label class="form-label">Tindak Lanjut</label>
            <textarea name="tindaklanjut_pks" class="form-control"></textarea>
        </div>
        <div class="col-md-6">
            <label class="form-label">Keterangan</label>
            <input type="text" name="keterangan_pks" class="form-control">
        </div>

        <div class="col-md-12 mt-3">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="index.php" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>

<?php include __DIR__ . "/../../views/footer.php"; ?>
