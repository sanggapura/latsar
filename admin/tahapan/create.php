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

        if (!$stmt) {
            echo "<div class='alert alert-danger'>Error preparing statement: " . htmlspecialchars($conn->error) . "</div>";
        } else {
            $tandai = isset($_POST['tandai']) ? 1 : 0;
            
            // Handle empty date fields - convert to NULL if empty
            $tanggal_kesepahaman = !empty($_POST['tanggal_kesepahaman']) ? $_POST['tanggal_kesepahaman'] : null;
            $rencana_pertemuan_kesepahaman = !empty($_POST['rencana_pertemuan_kesepahaman']) ? $_POST['rencana_pertemuan_kesepahaman'] : null;
            $tanggal_pks = !empty($_POST['tanggal_pks']) ? $_POST['tanggal_pks'] : null;
            $rencana_pertemuan_pks = !empty($_POST['rencana_pertemuan_pks']) ? $_POST['rencana_pertemuan_pks'] : null;

            // Bind parameters dengan tipe data yang benar
            $stmt->bind_param(
                "sssissssssssssssssssss", // 23 karakter: s-s-s-i-s-s-s-s-s-s-s-s-s-s-s-s-s-s-s-s-s-s-s
                $_POST['nama_mitra'],                          // 1
                $_POST['jenis_mitra'],                         // 2
                $_POST['sumber_usulan'] ?? '',                 // 3
                $tandai,                                       // 4 (integer)
                $_POST['status_kesepahaman'] ?? '',            // 5
                $_POST['nomor_kesepahaman'] ?? '',             // 6
                $tanggal_kesepahaman,                          // 7
                $_POST['ruanglingkup_kesepahaman'] ?? '',      // 8
                $_POST['status_pelaksanaan_kesepahaman'] ?? '', // 9
                $rencana_pertemuan_kesepahaman,                // 10
                $_POST['rencana_kolaborasi_kesepahaman'] ?? '', // 11
                $_POST['status_progres_kesepahaman'] ?? '',    // 12
                $_POST['tindaklanjut_kesepahaman'] ?? '',      // 13
                $_POST['keterangan_kesepahaman'] ?? '',        // 14
                $_POST['status_pks'] ?? '',                    // 15
                $_POST['nomor_pks'] ?? '',                     // 16
                $tanggal_pks,                                  // 17
                $_POST['ruanglingkup_pks'] ?? '',              // 18
                $_POST['status_pelaksanaan_pks'] ?? '',        // 19
                $rencana_pertemuan_pks,                        // 20
                $_POST['status_progres_pks'] ?? '',            // 21
                $_POST['tindaklanjut_pks'] ?? '',              // 22
                $_POST['keterangan_pks'] ?? ''                 // 23
            );

            if ($stmt->execute()) {
                header("Location: index.php?success=created");
                exit();
            } else {
                echo "<div class='alert alert-danger'>Terjadi kesalahan saat menyimpan: " . htmlspecialchars($stmt->error) . "</div>";
                echo "<div class='alert alert-info'>Debug info - MySQL Error: " . htmlspecialchars($conn->error) . "</div>";
            }
            $stmt->close();
        }
    }
}

include __DIR__ . "/../../views/header.php";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Data Mitra</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fa; }
        .card { border: none; box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075); }
        .form-label { font-weight: 600; }
        .required::after { content: " *"; color: #dc3545; }
    </style>
</head>
<body>
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Form Input Mitra</h4>
                </div>
                <div class="card-body">
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <h6><i class="bi bi-exclamation-triangle me-2"></i>Terdapat kesalahan:</h6>
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?= htmlspecialchars($error); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" novalidate>
                        <!-- Informasi Dasar Mitra -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-secondary mb-3"><i class="bi bi-building me-2"></i>Informasi Dasar Mitra</h5>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label required">Nama Mitra</label>
                                    <input type="text" name="nama_mitra" class="form-control <?php echo isset($errors['nama_mitra']) ? 'is-invalid' : ''; ?>" 
                                           value="<?= htmlspecialchars($_POST['nama_mitra'] ?? ''); ?>" required>
                                    <div class="invalid-feedback"><?php echo $errors['nama_mitra'] ?? ''; ?></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label required">Jenis Mitra</label>
                                    <select name="jenis_mitra" class="form-select <?php echo isset($errors['jenis_mitra']) ? 'is-invalid' : ''; ?>" required>
                                        <option value="">--Pilih Jenis Mitra--</option>
                                        <?php 
                                        $opsi = ['Kementerian/Lembaga','Pemerintah Daerah','Asosiasi','Perusahaan','Universitas','Job Portal'];
                                        foreach ($opsi as $o) {
                                            $sel = (($_POST['jenis_mitra'] ?? '') == $o) ? 'selected' : '';
                                            echo "<option $sel value=\"$o\">$o</option>";
                                        }
                                        ?>
                                    </select>
                                    <div class="invalid-feedback"><?php echo $errors['jenis_mitra'] ?? ''; ?></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Sumber Usulan</label>
                                    <input type="text" name="sumber_usulan" class="form-control" 
                                           value="<?= htmlspecialchars($_POST['sumber_usulan'] ?? ''); ?>"
                                           placeholder="Masukkan sumber usulan (opsional)">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check mt-4">
                                        <input class="form-check-input" type="checkbox" name="tandai" id="tandaiPrioritas" value="1" <?= isset($_POST['tandai']) ? 'checked' : ''; ?>>
                                        <label class="form-check-label fw-bold text-primary" for="tandaiPrioritas">
                                            <i class="bi bi-exclamation-circle-fill me-1"></i>
                                            Tandai sebagai prioritas/perhatian khusus
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Kesepahaman Section -->
                        <div class="card mb-4">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0"><i class="bi bi-handshake me-2"></i>Tahap Kesepahaman</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Status Kesepahaman</label>
                                            <select name="status_kesepahaman" class="form-select">
                                                <option value="">--Pilih Status--</option>
                                                <?php 
                                                $opsi = ['Signed','Not Available','Drafting/In Progress'];
                                                foreach ($opsi as $o) {
                                                    $sel = (($_POST['status_kesepahaman'] ?? '') == $o) ? 'selected' : '';
                                                    echo "<option $sel value=\"$o\">$o</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Nomor Kesepahaman</label>
                                            <input type="text" name="nomor_kesepahaman" class="form-control" 
                                                   value="<?= htmlspecialchars($_POST['nomor_kesepahaman'] ?? ''); ?>"
                                                   placeholder="Contoh: MOU/001/2024">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Tanggal Kesepahaman</label>
                                            <input type="date" name="tanggal_kesepahaman" class="form-control" 
                                                   value="<?= htmlspecialchars($_POST['tanggal_kesepahaman'] ?? ''); ?>">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Status Pelaksanaan</label>
                                            <select name="status_pelaksanaan_kesepahaman" class="form-select">
                                                <option value="">--Pilih Status--</option>
                                                <?php 
                                                $opsi = ['Implemented','In Progress','Not Yet'];
                                                foreach ($opsi as $o) {
                                                    $sel = (($_POST['status_pelaksanaan_kesepahaman'] ?? '') == $o) ? 'selected' : '';
                                                    echo "<option $sel value=\"$o\">$o</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Rencana Pertemuan</label>
                                            <input type="date" name="rencana_pertemuan_kesepahaman" class="form-control" 
                                                   value="<?= htmlspecialchars($_POST['rencana_pertemuan_kesepahaman'] ?? ''); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Ruang Lingkup Kesepahaman</label>
                                            <textarea name="ruanglingkup_kesepahaman" rows="3" class="form-control" 
                                                      placeholder="Deskripsikan ruang lingkup kesepahaman..."><?= htmlspecialchars($_POST['ruanglingkup_kesepahaman'] ?? ''); ?></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Rencana Kolaborasi</label>
                                            <textarea name="rencana_kolaborasi_kesepahaman" rows="3" class="form-control" 
                                                      placeholder="Deskripsikan rencana kolaborasi..."><?= htmlspecialchars($_POST['rencana_kolaborasi_kesepahaman'] ?? ''); ?></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Status/Progres</label>
                                            <textarea name="status_progres_kesepahaman" rows="3" class="form-control" 
                                                      placeholder="Update progres terkini..."><?= htmlspecialchars($_POST['status_progres_kesepahaman'] ?? ''); ?></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Tindak Lanjut</label>
                                            <textarea name="tindaklanjut_kesepahaman" rows="2" class="form-control" 
                                                      placeholder="Rencana tindak lanjut..."><?= htmlspecialchars($_POST['tindaklanjut_kesepahaman'] ?? ''); ?></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Keterangan</label>
                                            <input type="text" name="keterangan_kesepahaman" class="form-control" 
                                                   value="<?= htmlspecialchars($_POST['keterangan_kesepahaman'] ?? ''); ?>"
                                                   placeholder="Keterangan tambahan">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tombol Toggle PKS -->
                        <div class="text-center mb-3">
                            <button type="button" id="btnPKS" class="btn btn-success btn-lg" onclick="togglePKS()">
                                <i class="bi bi-plus-circle me-2"></i>Lanjutkan ke PKS
                            </button>
                            <p class="text-muted mt-2 small">Klik tombol di atas untuk melanjutkan mengisi data PKS (opsional)</p>
                        </div>

                        <!-- PKS Section -->
                        <div id="formPKS" style="display:none;">
                            <div class="card mb-4">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0"><i class="bi bi-file-earmark-text me-2"></i>PKS (Perjanjian Kerjasama Spesifik)</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Status PKS</label>
                                                <select name="status_pks" class="form-select">
                                                    <option value="">--Pilih Status--</option>
                                                    <?php 
                                                    $opsi = ['Signed','Not Available','Drafting/In Progress'];
                                                    foreach ($opsi as $o) {
                                                        $sel = (($_POST['status_pks'] ?? '') == $o) ? 'selected' : '';
                                                        echo "<option $sel value=\"$o\">$o</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Nomor PKS</label>
                                                <input type="text" name="nomor_pks" class="form-control" 
                                                       value="<?= htmlspecialchars($_POST['nomor_pks'] ?? ''); ?>"
                                                       placeholder="Contoh: PKS/001/2024">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Tanggal PKS</label>
                                                <input type="date" name="tanggal_pks" class="form-control" 
                                                       value="<?= htmlspecialchars($_POST['tanggal_pks'] ?? ''); ?>">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Status Pelaksanaan PKS</label>
                                                <select name="status_pelaksanaan_pks" class="form-select">
                                                    <option value="">--Pilih Status--</option>
                                                    <?php 
                                                    $opsi = ['Implemented','In Progress','Not Yet'];
                                                    foreach ($opsi as $o) {
                                                        $sel = (($_POST['status_pelaksanaan_pks'] ?? '') == $o) ? 'selected' : '';
                                                        echo "<option $sel value=\"$o\">$o</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Rencana Pertemuan PKS</label>
                                                <input type="date" name="rencana_pertemuan_pks" class="form-control" 
                                                       value="<?= htmlspecialchars($_POST['rencana_pertemuan_pks'] ?? ''); ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Ruang Lingkup PKS</label>
                                                <textarea name="ruanglingkup_pks" rows="3" class="form-control"
                                                          placeholder="Deskripsikan ruang lingkup PKS..."><?= htmlspecialchars($_POST['ruanglingkup_pks'] ?? ''); ?></textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Status/Progres PKS</label>
                                                <textarea name="status_progres_pks" rows="3" class="form-control"
                                                          placeholder="Update progres PKS..."><?= htmlspecialchars($_POST['status_progres_pks'] ?? ''); ?></textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Tindak Lanjut PKS</label>
                                                <textarea name="tindaklanjut_pks" rows="2" class="form-control"
                                                          placeholder="Rencana tindak lanjut PKS..."><?= htmlspecialchars($_POST['tindaklanjut_pks'] ?? ''); ?></textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Keterangan PKS</label>
                                                <input type="text" name="keterangan_pks" class="form-control" 
                                                       value="<?= htmlspecialchars($_POST['keterangan_pks'] ?? ''); ?>"
                                                       placeholder="Keterangan tambahan PKS">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex gap-2 justify-content-between">
                            <a href="index.php" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-1"></i>Kembali
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-save me-2"></i>Simpan Data Mitra
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function togglePKS() {
    const pksForm = document.getElementById("formPKS");
    const btn = document.getElementById("btnPKS");
    
    if (pksForm.style.display === "none" || pksForm.style.display === "") {
        pksForm.style.display = "block";
        btn.innerHTML = '<i class="bi bi-dash-circle me-2"></i>Sembunyikan PKS';
        btn.className = "btn btn-warning btn-lg";
        pksForm.scrollIntoView({behavior: 'smooth', block: 'start'});
    } else {
        pksForm.style.display = "none";
        btn.innerHTML = '<i class="bi bi-plus-circle me-2"></i>Lanjutkan ke PKS';
        btn.className = "btn btn-success btn-lg";
    }
}

// Form validation
(function() {
    'use strict';
    window.addEventListener('load', function() {
        var forms = document.getElementsByClassName('needs-validation');
        var validation = Array.prototype.filter.call(forms, function(form) {
            form.addEventListener('submit', function(event) {
                if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    }, false);
})();
</script>
</body>
</html>