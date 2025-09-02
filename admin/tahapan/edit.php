<?php
include "db.php";

$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) {
    header("Location: index.php?error=invalid_id");
    exit();
}

$id = intval($id);

// Ambil data lama
$stmt = $conn->prepare("SELECT * FROM tahapan_kerjasama WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if (!$data) {
    header("Location: index.php?error=not_found");
    exit();
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validasi input utama
    if (empty(trim($_POST['nama_mitra']))) {
        $errors['nama_mitra'] = "Nama Mitra wajib diisi";
    }
    if (empty(trim($_POST['jenis_mitra']))) {
        $errors['jenis_mitra'] = "Jenis Mitra wajib dipilih";
    }

    // Handle file uploads if any
    $uploadDir = __DIR__ . "/upload/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $allowedExt = ['pdf','doc','docx','xls','xlsx'];
    $files = [];
    
    for ($i = 1; $i <= 3; $i++) {
        $files[$i] = $data["file$i"]; // Keep old file by default
        
        if (!empty($_FILES["file$i"]['name'])) {
            $ext = strtolower(pathinfo($_FILES["file$i"]['name'], PATHINFO_EXTENSION));
            if ($stmt->execute()) {
                $success = true;
                header("Location: index.php?success=updated");
                exit();
            } else {
                $errors['general'] = "Gagal memperbarui data: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $errors['general'] = "Error preparing statement: " . $conn->error;
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
    <title>Edit Data Mitra</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fa; }
        .card { border: none; box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075); }
        .form-label { font-weight: 600; }
        .required::after { content: " *"; color: #dc3545; }
        .file-preview { background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 5px; padding: 10px; margin-bottom: 10px; }
    </style>
</head>
<body>
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Edit Data Mitra</h4>
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
                    
                    <form method="POST" enctype="multipart/form-data" novalidate>
                        <!-- Informasi Dasar Mitra -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-secondary mb-3"><i class="bi bi-building me-2"></i>Informasi Dasar Mitra</h5>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label required">Nama Mitra</label>
                                    <input type="text" name="nama_mitra" class="form-control <?php echo isset($errors['nama_mitra']) ? 'is-invalid' : ''; ?>" 
                                           value="<?= htmlspecialchars($_POST['nama_mitra'] ?? $data['nama_mitra']); ?>" required>
                                    <div class="invalid-feedback"><?php echo $errors['nama_mitra'] ?? ''; ?></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label required">Jenis Mitra</label>
                                    <select name="jenis_mitra" class="form-select <?php echo isset($errors['jenis_mitra']) ? 'is-invalid' : ''; ?>" required>
                                        <option value="">--Pilih Jenis Mitra--</option>
                                        <?php
                                        $options = ["Kementerian/Lembaga","Pemerintah Daerah","Asosiasi","Perusahaan","Universitas","Job Portal"];
                                        foreach($options as $opt){
                                            $current = $_POST['jenis_mitra'] ?? $data['jenis_mitra'];
                                            $sel = ($current == $opt) ? "selected" : "";
                                            echo "<option $sel value=\"$opt\">$opt</option>";
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
                                           value="<?= htmlspecialchars($_POST['sumber_usulan'] ?? $data['sumber_usulan']); ?>"
                                           placeholder="Masukkan sumber usulan (opsional)">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check mt-4">
                                        <?php $tandai = $_POST['tandai'] ?? $data['tandai'] ?? 0; ?>
                                        <input class="form-check-input" type="checkbox" name="tandai" id="tandaiPrioritas" value="1" <?= $tandai ? 'checked' : ''; ?>>
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
                                                    $current = $_POST['status_kesepahaman'] ?? $data['status_kesepahaman'];
                                                    $sel = ($current == $o) ? 'selected' : '';
                                                    echo "<option $sel value=\"$o\">$o</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Nomor Kesepahaman</label>
                                            <input type="text" name="nomor_kesepahaman" class="form-control" 
                                                   value="<?= htmlspecialchars($_POST['nomor_kesepahaman'] ?? $data['nomor_kesepahaman']); ?>"
                                                   placeholder="Contoh: MOU/001/2024">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Tanggal Kesepahaman</label>
                                            <input type="date" name="tanggal_kesepahaman" class="form-control" 
                                                   value="<?= htmlspecialchars($_POST['tanggal_kesepahaman'] ?? $data['tanggal_kesepahaman']); ?>">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Status Pelaksanaan</label>
                                            <select name="status_pelaksanaan_kesepahaman" class="form-select">
                                                <option value="">--Pilih Status--</option>
                                                <?php 
                                                $opsi = ['Implemented','In Progress','Not Yet'];
                                                foreach ($opsi as $o) {
                                                    $current = $_POST['status_pelaksanaan_kesepahaman'] ?? $data['status_pelaksanaan_kesepahaman'];
                                                    $sel = ($current == $o) ? 'selected' : '';
                                                    echo "<option $sel value=\"$o\">$o</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Rencana Pertemuan</label>
                                            <input type="date" name="rencana_pertemuan_kesepahaman" class="form-control" 
                                                   value="<?= htmlspecialchars($_POST['rencana_pertemuan_kesepahaman'] ?? $data['rencana_pertemuan_kesepahaman']); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Ruang Lingkup Kesepahaman</label>
                                            <textarea name="ruanglingkup_kesepahaman" rows="3" class="form-control" 
                                                      placeholder="Deskripsikan ruang lingkup kesepahaman..."><?= htmlspecialchars($_POST['ruanglingkup_kesepahaman'] ?? $data['ruanglingkup_kesepahaman']); ?></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Rencana Kolaborasi</label>
                                            <textarea name="rencana_kolaborasi_kesepahaman" rows="3" class="form-control" 
                                                      placeholder="Deskripsikan rencana kolaborasi..."><?= htmlspecialchars($_POST['rencana_kolaborasi_kesepahaman'] ?? $data['rencana_kolaborasi_kesepahaman']); ?></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Status/Progres</label>
                                            <textarea name="status_progres_kesepahaman" rows="3" class="form-control" 
                                                      placeholder="Update progres terkini..."><?= htmlspecialchars($_POST['status_progres_kesepahaman'] ?? $data['status_progres_kesepahaman']); ?></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Tindak Lanjut</label>
                                            <textarea name="tindaklanjut_kesepahaman" rows="2" class="form-control" 
                                                      placeholder="Rencana tindak lanjut..."><?= htmlspecialchars($_POST['tindaklanjut_kesepahaman'] ?? $data['tindaklanjut_kesepahaman']); ?></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Keterangan</label>
                                            <input type="text" name="keterangan_kesepahaman" class="form-control" 
                                                   value="<?= htmlspecialchars($_POST['keterangan_kesepahaman'] ?? $data['keterangan_kesepahaman']); ?>"
                                                   placeholder="Keterangan tambahan">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- PKS Section -->
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
                                                    $current = $_POST['status_pks'] ?? $data['status_pks'];
                                                    $sel = ($current == $o) ? 'selected' : '';
                                                    echo "<option $sel value=\"$o\">$o</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Nomor PKS</label>
                                            <input type="text" name="nomor_pks" class="form-control" 
                                                   value="<?= htmlspecialchars($_POST['nomor_pks'] ?? $data['nomor_pks']); ?>"
                                                   placeholder="Contoh: PKS/001/2024">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Tanggal PKS</label>
                                            <input type="date" name="tanggal_pks" class="form-control" 
                                                   value="<?= htmlspecialchars($_POST['tanggal_pks'] ?? $data['tanggal_pks']); ?>">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Status Pelaksanaan PKS</label>
                                            <select name="status_pelaksanaan_pks" class="form-select">
                                                <option value="">--Pilih Status--</option>
                                                <?php 
                                                $opsi = ['Implemented','In Progress','Not Yet'];
                                                foreach ($opsi as $o) {
                                                    $current = $_POST['status_pelaksanaan_pks'] ?? $data['status_pelaksanaan_pks'];
                                                    $sel = ($current == $o) ? 'selected' : '';
                                                    echo "<option $sel value=\"$o\">$o</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Rencana Pertemuan PKS</label>
                                            <input type="date" name="rencana_pertemuan_pks" class="form-control" 
                                                   value="<?= htmlspecialchars($_POST['rencana_pertemuan_pks'] ?? $data['rencana_pertemuan_pks']); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Ruang Lingkup PKS</label>
                                            <textarea name="ruanglingkup_pks" rows="3" class="form-control"
                                                      placeholder="Deskripsikan ruang lingkup PKS..."><?= htmlspecialchars($_POST['ruanglingkup_pks'] ?? $data['ruanglingkup_pks']); ?></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Status/Progres PKS</label>
                                            <textarea name="status_progres_pks" rows="3" class="form-control"
                                                      placeholder="Update progres PKS..."><?= htmlspecialchars($_POST['status_progres_pks'] ?? $data['status_progres_pks']); ?></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Tindak Lanjut PKS</label>
                                            <textarea name="tindaklanjut_pks" rows="2" class="form-control"
                                                      placeholder="Rencana tindak lanjut PKS..."><?= htmlspecialchars($_POST['tindaklanjut_pks'] ?? $data['tindaklanjut_pks']); ?></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Keterangan PKS</label>
                                            <input type="text" name="keterangan_pks" class="form-control" 
                                                   value="<?= htmlspecialchars($_POST['keterangan_pks'] ?? $data['keterangan_pks']); ?>"
                                                   placeholder="Keterangan tambahan PKS">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- File Upload Section -->
                        <div class="card mb-4">
                            <div class="card-header bg-secondary text-white">
                                <h5 class="mb-0"><i class="bi bi-file-earmark-arrow-up me-2"></i>File Dokumen (Maksimal 3 File)</h5>
                            </div>
                            <div class="card-body">
                                <p class="text-muted mb-3">Format yang diizinkan: PDF, DOC, DOCX, XLS, XLSX</p>
                                <?php for($i = 1; $i <= 3; $i++): ?>
                                    <div class="mb-3">
                                        <label class="form-label">File <?= $i; ?></label>
                                        <input type="file" name="file<?= $i; ?>" class="form-control <?php echo isset($errors["file$i"]) ? 'is-invalid':''; ?>" 
                                               accept=".pdf,.doc,.docx,.xls,.xlsx">
                                        <?php if (isset($errors["file$i"])): ?>
                                            <div class="invalid-feedback"><?= htmlspecialchars($errors["file$i"]); ?></div>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($data["file$i"])): ?>
                                            <div class="file-preview mt-2">
                                                <small class="text-success">
                                                    <i class="bi bi-check-circle me-1"></i>
                                                    File saat ini: <strong><?= htmlspecialchars($data["file$i"]); ?></strong>
                                                </small>
                                                <div class="mt-1">
                                                    <a href="?id=<?= $id; ?>&file=file<?= $i; ?>" target="_blank" class="btn btn-sm btn-outline-info">
                                                        <i class="bi bi-eye me-1"></i>Lihat
                                                    </a>
                                                    <a href="?id=<?= $id; ?>&file=file<?= $i; ?>&download=1" class="btn btn-sm btn-outline-success">
                                                        <i class="bi bi-download me-1"></i>Download
                                                    </a>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endfor; ?>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex gap-2 justify-content-between">
                            <a href="index.php" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-1"></i>Kembali
                            </a>
                            <button type="submit" class="btn btn-warning btn-lg">
                                <i class="bi bi-save me-2"></i>Update Data Mitra
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

// File size validation
document.querySelectorAll('input[type="file"]').forEach(function(input) {
    input.addEventListener('change', function() {
        const maxSize = 10 * 1024 * 1024; // 10MB
        if (this.files[0] && this.files[0].size > maxSize) {
            alert('File terlalu besar! Maksimal ukuran file adalah 10MB.');
            this.value = '';
        }
    });
});
</script>
</body>
</html> (in_array($ext, $allowedExt)) {
                $newName = "file_" . $id . "_" . $i . "_" . time() . "." . $ext;
                $targetPath = $uploadDir . $newName;
                
                if (move_uploaded_file($_FILES["file$i"]['tmp_name'], $targetPath)) {
                    // Delete old file if exists
                    if (!empty($data["file$i"]) && file_exists($uploadDir . $data["file$i"])) {
                        unlink($uploadDir . $data["file$i"]);
                    }
                    $files[$i] = $newName;
                } else {
                    $errors["file$i"] = "Gagal mengupload file $i";
                }
            } else {
                $errors["file$i"] = "Format file tidak valid (hanya pdf, doc, docx, xls, xlsx)";
            }
        }
    }

    // Jika lolos validasi
    if (count($errors) === 0) {
        // Handle empty date fields
        $tanggal_kesepahaman = !empty($_POST['tanggal_kesepahaman']) ? $_POST['tanggal_kesepahaman'] : null;
        $rencana_pertemuan_kesepahaman = !empty($_POST['rencana_pertemuan_kesepahaman']) ? $_POST['rencana_pertemuan_kesepahaman'] : null;
        $tanggal_pks = !empty($_POST['tanggal_pks']) ? $_POST['tanggal_pks'] : null;
        $rencana_pertemuan_pks = !empty($_POST['rencana_pertemuan_pks']) ? $_POST['rencana_pertemuan_pks'] : null;
        
        $tandai = isset($_POST['tandai']) ? 1 : 0;

        $sql = "UPDATE tahapan_kerjasama SET 
                nama_mitra=?, jenis_mitra=?, sumber_usulan=?, tandai=?,
                status_kesepahaman=?, nomor_kesepahaman=?, tanggal_kesepahaman=?, ruanglingkup_kesepahaman=?, status_pelaksanaan_kesepahaman=?,
                rencana_pertemuan_kesepahaman=?, rencana_kolaborasi_kesepahaman=?, status_progres_kesepahaman=?, tindaklanjut_kesepahaman=?, keterangan_kesepahaman=?,
                status_pks=?, nomor_pks=?, tanggal_pks=?, ruanglingkup_pks=?, status_pelaksanaan_pks=?,
                rencana_pertemuan_pks=?, status_progres_pks=?, tindaklanjut_pks=?, keterangan_pks=?,
                file1=?, file2=?, file3=?
                WHERE id=?";

        $stmt = $conn->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param(
                "sssisssssssssssssssssssssi",
                $_POST['nama_mitra'],
                $_POST['jenis_mitra'],
                $_POST['sumber_usulan'] ?? '',
                $tandai,
                $_POST['status_kesepahaman'] ?? '',
                $_POST['nomor_kesepahaman'] ?? '',
                $tanggal_kesepahaman,
                $_POST['ruanglingkup_kesepahaman'] ?? '',
                $_POST['status_pelaksanaan_kesepahaman'] ?? '',
                $rencana_pertemuan_kesepahaman,
                $_POST['rencana_kolaborasi_kesepahaman'] ?? '',
                $_POST['status_progres_kesepahaman'] ?? '',
                $_POST['tindaklanjut_kesepahaman'] ?? '',
                $_POST['keterangan_kesepahaman'] ?? '',
                $_POST['status_pks'] ?? '',
                $_POST['nomor_pks'] ?? '',
                $tanggal_pks,
                $_POST['ruanglingkup_pks'] ?? '',
                $_POST['status_pelaksanaan_pks'] ?? '',
                $rencana_pertemuan_pks,
                $_POST['status_progres_pks'] ?? '',
                $_POST['tindaklanjut_pks'] ?? '',
                $_POST['keterangan_pks'] ?? '',
                $files[1],
                $files[2],
                $files[3],
                $id
            );

            if