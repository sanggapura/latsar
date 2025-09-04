<?php
// =================================================================
// FILE: edit.php (VERSI FINAL - DENGAN SWASTA & ASOSIASI)
// =================================================================

include "db.php"; 
error_reporting(E_ALL);
ini_set('display_errors', 1);

$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) {
    header("Location: index.php?error=invalid_id");
    exit();
}
$id = intval($id);

// Ambil data lama dari database
$stmt = $conn->prepare("SELECT * FROM tahapan_kerjasama WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if (!$data) {
    header("Location: index.php?error=not_found");
    exit();
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty(trim($_POST['nama_mitra']))) { $errors['nama_mitra'] = "Nama Mitra wajib diisi"; }
    if (empty(trim($_POST['jenis_mitra']))) { $errors['jenis_mitra'] = "Jenis Mitra wajib dipilih"; }

    if (count($errors) === 0) {
        $sql = "UPDATE tahapan_kerjasama SET 
                nama_mitra=?, jenis_mitra=?, sumber_usulan=?, tandai=?,
                status_kesepahaman=?, nomor_kesepahaman=?, tanggal_kesepahaman=?, ruanglingkup_kesepahaman=?, status_pelaksanaan_kesepahaman=?,
                rencana_pertemuan_kesepahaman=?, rencana_kolaborasi_kesepahaman=?, status_progres_kesepahaman=?, tindaklanjut_kesepahaman=?, keterangan_kesepahaman=?,
                status_pks=?, nomor_pks=?, tanggal_pks=?, ruanglingkup_pks=?, status_pelaksanaan_pks=?,
                rencana_pertemuan_pks=?, status_progres_pks=?, tindaklanjut_pks=?, keterangan_pks=?
                WHERE id=?";

        $stmt_update = $conn->prepare($sql);
        $tandai = isset($_POST['tandai']) ? 1 : 0;

        foreach ($_POST as $key => $value) {
            if (empty($value)) { $_POST[$key] = null; }
        }

        $stmt_update->bind_param(
            "sssisssssssssssssssssssi",
            $_POST['nama_mitra'], $_POST['jenis_mitra'], $_POST['sumber_usulan'], $tandai,
            $_POST['status_kesepahaman'], $_POST['nomor_kesepahaman'], $_POST['tanggal_kesepahaman'],
            $_POST['ruanglingkup_kesepahaman'], $_POST['status_pelaksanaan_kesepahaman'],
            $_POST['rencana_pertemuan_kesepahaman'], $_POST['rencana_kolaborasi_kesepahaman'],
            $_POST['status_progres_kesepahaman'], $_POST['tindaklanjut_kesepahaman'], $_POST['keterangan_kesepahaman'],
            $_POST['status_pks'], $_POST['nomor_pks'], $_POST['tanggal_pks'], $_POST['ruanglingkup_pks'],
            $_POST['status_pelaksanaan_pks'], $_POST['rencana_pertemuan_pks'], $_POST['status_progres_pks'],
            $_POST['tindaklanjut_pks'], $_POST['keterangan_pks'],
            $id
        );

        if ($stmt_update->execute()) {
            header("Location: index.php?success=updated");
            exit();
        } else {
            $errors['db_error'] = "Gagal memperbarui data: " . htmlspecialchars($stmt_update->error);
        }
    }
}

$pksDataExists = !empty($data['status_pks']) || !empty($data['nomor_pks']) || !empty($data['tanggal_pks']);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Mitra - <?= htmlspecialchars($data['nama_mitra'] ?? '') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --bs-blue-dark: #0a3d62; --bs-blue-light: #3c6382; --bs-orange: #f39c12; --bs-gray: #f5f7fa; }
        body { font-family: 'Poppins', sans-serif; background-color: var(--bs-gray); }
        .form-card { background-color: white; border-radius: 1rem; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08); }
        .form-card-header { background: linear-gradient(135deg, var(--bs-blue-dark) 0%, var(--bs-blue-light) 100%); color: white; }
        .form-section-title { font-weight: 600; color: var(--bs-blue-dark); border-bottom: 2px solid var(--bs-blue-light); }
    </style>
</head>
<body>
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-11">
                <div class="card form-card">
                    <div class="card-header form-card-header text-center p-4">
                        <h2 class="mb-1"><i class="bi bi-pencil-square"></i> Edit Mitra Kerjasama</h2>
                        <p class="mb-0">Perbarui informasi untuk: <strong><?= htmlspecialchars($data['nama_mitra'] ?? '') ?></strong></p>
                    </div>
                    <div class="card-body p-4 p-md-5">
                        <form method="POST" action="">
                            <h5 class="form-section-title pb-2 mb-4"><i class="bi bi-building"></i> Informasi Dasar Mitra</h5>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6"><label class="form-label">Nama Mitra <span class="text-danger">*</span></label><input type="text" name="nama_mitra" class="form-control" value="<?= htmlspecialchars($data['nama_mitra'] ?? '') ?>" required></div>
                                <div class="col-md-6"><label class="form-label">Jenis Mitra <span class="text-danger">*</span></label>
                                    <select name="jenis_mitra" class="form-select" required>
                                        <option value="">-- Pilih Jenis --</option>
                                        <option value="Kementerian/Lembaga" <?= ($data['jenis_mitra'] ?? '') == 'Kementerian/Lembaga' ? 'selected' : '' ?>>Kementerian/Lembaga</option>
                                        <option value="Pemerintah Daerah" <?= ($data['jenis_mitra'] ?? '') == 'Pemerintah Daerah' ? 'selected' : '' ?>>Pemerintah Daerah</option>
                                        <option value="Swasta/Perusahaan" <?= ($data['jenis_mitra'] ?? '') == 'Swasta/Perusahaan' ? 'selected' : '' ?>>Swasta/Perusahaan</option>
                                        <option value="Job Portal" <?= ($data['jenis_mitra'] ?? '') == 'Job Portal' ? 'selected' : '' ?>>Job Portal</option>
                                        <option value="Universitas" <?= ($data['jenis_mitra'] ?? '') == 'Universitas' ? 'selected' : '' ?>>Universitas</option>
                                        <option value="Asosiasi/Komunitas" <?= ($data['jenis_mitra'] ?? '') == 'Asosiasi/Komunitas' ? 'selected' : '' ?>>Asosiasi/Komunitas</option>
                                    </select>
                                </div>
                                <div class="col-md-6"><label class="form-label">Sumber Usulan</label><input type="text" name="sumber_usulan" class="form-control" value="<?= htmlspecialchars($data['sumber_usulan'] ?? '') ?>"></div>
                                <div class="col-md-6 d-flex align-items-end"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="tandai" value="1" id="tandaiCheck" <?= ($data['tandai'] ?? 0) ? 'checked' : '' ?>><label class="form-check-label" for="tandaiCheck">Tandai sebagai prioritas</label></div></div>
                            </div>
                            
                            <h5 class="form-section-title mt-5"><i class="bi bi-handshake"></i> Tahap Kesepahaman (MoU)</h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="mb-3"><label class="form-label">Status Kesepahaman</label><select name="status_kesepahaman" class="form-select"><option value="">-- Pilih Status --</option><option value="Signed" <?= ($data['status_kesepahaman'] ?? '') == 'Signed' ? 'selected' : '' ?>>Signed</option><option value="Not Available" <?= ($data['status_kesepahaman'] ?? '') == 'Not Available' ? 'selected' : '' ?>>Not Available</option><option value="Drafting/In Progress" <?= ($data['status_kesepahaman'] ?? '') == 'Drafting/In Progress' ? 'selected' : '' ?>>Drafting/In Progress</option></select></div>
                                    <div class="mb-3"><label class="form-label">Nomor</label><input type="text" name="nomor_kesepahaman" class="form-control" value="<?= htmlspecialchars($data['nomor_kesepahaman'] ?? '') ?>"></div>
                                    <div class="mb-3"><label class="form-label">Tanggal</label><input type="date" name="tanggal_kesepahaman" class="form-control" value="<?= htmlspecialchars($data['tanggal_kesepahaman'] ?? '') ?>"></div>
                                    <div class="mb-3"><label class="form-label">Status Pelaksanaan</label><select name="status_pelaksanaan_kesepahaman" class="form-select"><option value="">-- Pilih Status --</option><option value="Implemented" <?= ($data['status_pelaksanaan_kesepahaman'] ?? '') == 'Implemented' ? 'selected' : '' ?>>Implemented</option><option value="In Progress" <?= ($data['status_pelaksanaan_kesepahaman'] ?? '') == 'In Progress' ? 'selected' : '' ?>>In Progress</option><option value="Not Yet" <?= ($data['status_pelaksanaan_kesepahaman'] ?? '') == 'Not Yet' ? 'selected' : '' ?>>Not Yet</option></select></div>
                                    <div class="mb-3"><label class="form-label">Rencana Pertemuan</label><input type="date" name="rencana_pertemuan_kesepahaman" class="form-control" value="<?= htmlspecialchars($data['rencana_pertemuan_kesepahaman'] ?? '') ?>"></div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3"><label class="form-label">Ruang Lingkup</label><textarea name="ruanglingkup_kesepahaman" class="form-control" rows="2"><?= htmlspecialchars($data['ruanglingkup_kesepahaman'] ?? '') ?></textarea></div>
                                    <div class="mb-3"><label class="form-label">Rencana Kolaborasi</label><textarea name="rencana_kolaborasi_kesepahaman" class="form-control" rows="2"><?= htmlspecialchars($data['rencana_kolaborasi_kesepahaman'] ?? '') ?></textarea></div>
                                    <div class="mb-3"><label class="form-label">Status/Progres</label><textarea name="status_progres_kesepahaman" class="form-control" rows="2"><?= htmlspecialchars($data['status_progres_kesepahaman'] ?? '') ?></textarea></div>
                                    <div class="mb-3"><label class="form-label">Tindak Lanjut</label><textarea name="tindaklanjut_kesepahaman" class="form-control" rows="2"><?= htmlspecialchars($data['tindaklanjut_kesepahaman'] ?? '') ?></textarea></div>
                                    <div class="mb-3"><label class="form-label">Keterangan</label><input type="text" name="keterangan_kesepahaman" class="form-control" value="<?= htmlspecialchars($data['keterangan_kesepahaman'] ?? '') ?>"></div>
                                </div>
                            </div>
                            
                            <div class="text-center mt-4" id="lanjutPksButtonContainer" <?= $pksDataExists ? 'style="display: none;"' : '' ?>><button type="button" class="btn btn-warning" id="btnLanjutPks"><i class="bi bi-arrow-down-circle"></i> Lanjut isi PKS</button></div>
                            <div id="pksSection" <?= !$pksDataExists ? 'style="display: none;"' : '' ?>>
                                <h5 class="form-section-title mt-5"><i class="bi bi-file-earmark-text"></i> Tahap PKS</h5>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="mb-3"><label class="form-label">Status PKS</label><select name="status_pks" class="form-select"><option value="">-- Pilih Status --</option><option value="Signed" <?= ($data['status_pks'] ?? '') == 'Signed' ? 'selected' : '' ?>>Signed</option><option value="Not Available" <?= ($data['status_pks'] ?? '') == 'Not Available' ? 'selected' : '' ?>>Not Available</option><option value="Drafting/In Progress" <?= ($data['status_pks'] ?? '') == 'Drafting/In Progress' ? 'selected' : '' ?>>Drafting/In Progress</option></select></div>
                                        <div class="mb-3"><label class="form-label">Nomor</label><input type="text" name="nomor_pks" class="form-control" value="<?= htmlspecialchars($data['nomor_pks'] ?? '') ?>"></div>
                                        <div class="mb-3"><label class="form-label">Tanggal</label><input type="date" name="tanggal_pks" class="form-control" value="<?= htmlspecialchars($data['tanggal_pks'] ?? '') ?>"></div>
                                        <div class="mb-3"><label class="form-label">Status Pelaksanaan PKS</label><select name="status_pelaksanaan_pks" class="form-select"><option value="">-- Pilih Status --</option><option value="Implemented" <?= ($data['status_pelaksanaan_pks'] ?? '') == 'Implemented' ? 'selected' : '' ?>>Implemented</option><option value="In Progress" <?= ($data['status_pelaksanaan_pks'] ?? '') == 'In Progress' ? 'selected' : '' ?>>In Progress</option><option value="Not Yet" <?= ($data['status_pelaksanaan_pks'] ?? '') == 'Not Yet' ? 'selected' : '' ?>>Not Yet</option></select></div>
                                        <div class="mb-3"><label class="form-label">Rencana Pertemuan</label><input type="date" name="rencana_pertemuan_pks" class="form-control" value="<?= htmlspecialchars($data['rencana_pertemuan_pks'] ?? '') ?>"></div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3"><label class="form-label">Ruang Lingkup</label><textarea name="ruanglingkup_pks" class="form-control" rows="2"><?= htmlspecialchars($data['ruanglingkup_pks'] ?? '') ?></textarea></div>
                                        <div class="mb-3"><label class="form-label">Status/Progres</label><textarea name="status_progres_pks" class="form-control" rows="2"><?= htmlspecialchars($data['status_progres_pks'] ?? '') ?></textarea></div>
                                        <div class="mb-3"><label class="form-label">Tindak Lanjut</label><textarea name="tindaklanjut_pks" class="form-control" rows="2"><?= htmlspecialchars($data['tindaklanjut_pks'] ?? '') ?></textarea></div>
                                        <div class="mb-3"><label class="form-label">Keterangan</label><input type="text" name="keterangan_pks" class="form-control" value="<?= htmlspecialchars($data['keterangan_pks'] ?? '') ?>"></div>
                                    </div>
                                </div>
                            </div>
                            
                            <hr class="my-5">
                            <div class="d-flex justify-content-end gap-2"><a href="index.php" class="btn btn-secondary">Batal</a><button type="submit" class="btn btn-primary"><i class="bi bi-save-fill me-2"></i>Update Data</button></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        if(document.getElementById('btnLanjutPks')) {
            document.getElementById('btnLanjutPks').addEventListener('click', function() {
                this.parentElement.style.display = 'none';
                document.getElementById('pksSection').style.display = 'block';
                document.getElementById('pksSection').scrollIntoView({ behavior: 'smooth' });
            });
        }
    </script>
</body>
</html>