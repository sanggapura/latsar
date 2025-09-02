<?php
// =================================================================
// BAGIAN 1: LOGIKA PHP UNTUK MEMPROSES DAN MENYIMPAN DATA FORMULIR
// =================================================================
include "db.php"; // Harap pastikan path ke file db.php Anda sudah benar.

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

    // Jika tidak ada error, lanjutkan proses
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
        
        // Mengisi nilai null jika kosong untuk menghindari error
        foreach ($_POST as $key => $value) {
            if (empty($value)) {
                $_POST[$key] = null;
            }
        }

        $stmt->bind_param(
            "sssisssssssssssssssssss", 
            $_POST['nama_mitra'], $_POST['jenis_mitra'], $_POST['sumber_usulan'], $tandai,
            $_POST['status_kesepahaman'], $_POST['nomor_kesepahaman'], $_POST['tanggal_kesepahaman'],
            $_POST['ruanglingkup_kesepahaman'], $_POST['status_pelaksanaan_kesepahaman'],
            $_POST['rencana_pertemuan_kesepahaman'], $_POST['rencana_kolaborasi_kesepahaman'],
            $_POST['status_progres_kesepahaman'], $_POST['tindaklanjut_kesepahaman'], $_POST['keterangan_kesepahaman'],
            $_POST['status_pks'], $_POST['nomor_pks'], $_POST['tanggal_pks'], $_POST['ruanglingkup_pks'],
            $_POST['status_pelaksanaan_pks'], $_POST['rencana_pertemuan_pks'], $_POST['status_progres_pks'],
            $_POST['tindaklanjut_pks'], $_POST['keterangan_pks']
        );

        if ($stmt->execute()) {
            header("Location: index.php?success=created");
            exit();
        } else {
            $errors['db_error'] = "Terjadi kesalahan saat menyimpan: " . htmlspecialchars($stmt->error);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Mitra Baru</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --bs-blue-dark: #0a3d62;
            --bs-blue-light: #3c6382;
            --bs-orange: #f39c12;
            --bs-gray: #f5f7fa;
            --bs-gray-dark: #6c757d;
            --bs-border-color: #dee2e6;
        }
        html { scroll-behavior: smooth; }
        body { font-family: 'Poppins', sans-serif; background-color: var(--bs-gray); }
        .form-card { background-color: white; border-radius: 1rem; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08); border: none; overflow: hidden; }
        .form-card-header { background: linear-gradient(135deg, var(--bs-blue-dark) 0%, var(--bs-blue-light) 100%); padding: 2rem; color: white; }
        .form-card-header h2 { font-weight: 700; margin-bottom: 0.5rem; }
        .form-section-title { font-weight: 600; color: var(--bs-blue-dark); margin-bottom: 1.5rem; border-bottom: 2px solid var(--bs-blue-light); padding-bottom: 0.5rem; }
        .form-label { font-weight: 500; color: var(--bs-gray-dark); }
        .form-control, .form-select { border-radius: 0.5rem; border: 1px solid var(--bs-border-color); padding: 0.75rem 1rem; }
        .form-control:focus, .form-select:focus { border-color: var(--bs-blue-light); box-shadow: 0 0 0 0.25rem rgba(60, 99, 130, 0.25); }
        .btn-primary { background-color: var(--bs-blue-light); border-color: var(--bs-blue-light); border-radius: 0.5rem; padding: 0.75rem 1.5rem; font-weight: 600; }
        .btn-primary:hover { background-color: var(--bs-blue-dark); border-color: var(--bs-blue-dark); }
        .btn-secondary { border-radius: 0.5rem; padding: 0.75rem 1.5rem; font-weight: 600; }
        .btn-warning { background-color: var(--bs-orange); border-color: var(--bs-orange); color: white; }
        .btn-warning:hover { background-color: #e67e22; border-color: #e67e22; }
    </style>
</head>
<body>

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-11">
                <div class="card form-card">
                    <div class="form-card-header text-center">
                        <h2><i class="bi bi-person-plus-fill"></i> Tambah Mitra Kerjasama Baru</h2>
                        <p class="mb-0">Lengkapi semua informasi yang diperlukan di bawah ini.</p>
                    </div>

                    <div class="card-body p-4 p-md-5">
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    <?php foreach ($errors as $err): ?>
                                        <li><?= htmlspecialchars($err) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <h5 class="form-section-title"><i class="bi bi-building"></i> Informasi Dasar Mitra</h5>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6"><label class="form-label">Nama Mitra <span class="text-danger">*</span></label><input type="text" name="nama_mitra" class="form-control" placeholder="Contoh: PT Teknologi Maju" required></div>
                                <div class="col-md-6"><label class="form-label">Jenis Mitra <span class="text-danger">*</span></label><select name="jenis_mitra" class="form-select" required><option value="">-- Pilih Jenis --</option><option value="Kementerian/Lembaga">Kementerian/Lembaga</option><option value="Pemerintah Daerah">Pemerintah Daerah</option><option value="BUMN/BUMD">BUMN/BUMD</option><option value="Swasta">Swasta</option><option value="Asosiasi">Asosiasi</option><option value="Perguruan Tinggi">Perguruan Tinggi</option></select></div>
                                <div class="col-md-6"><label class="form-label">Sumber Usulan</label><input type="text" name="sumber_usulan" class="form-control" placeholder="Opsional"></div>
                                <div class="col-md-6 d-flex align-items-end"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="tandai" value="1" id="tandaiCheck"><label class="form-check-label" for="tandaiCheck">Tandai sebagai prioritas</label></div></div>
                            </div>

                            <h5 class="form-section-title mt-5"><i class="bi bi-handshake"></i> Tahap Kesepahaman (MoU)</h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="mb-3"><label class="form-label">Status Kesepahaman</label><select name="status_kesepahaman" class="form-select"><option value="">-- Pilih Status --</option><option value="Signed">Signed</option><option value="Not Available">Not Available</option><option value="Drafting/In Progress">Drafting/In Progress</option></select></div>
                                    <div class="mb-3"><label class="form-label">Nomor</label><input type="text" name="nomor_kesepahaman" class="form-control"></div>
                                    <div class="mb-3"><label class="form-label">Tanggal</label><input type="date" name="tanggal_kesepahaman" class="form-control"></div>
                                    <div class="mb-3"><label class="form-label">Status Pelaksanaan</label><select name="status_pelaksanaan_kesepahaman" class="form-select"><option value="">-- Pilih Status --</option><option value="Implemented">Implemented</option><option value="In Progress">In Progress</option><option value="Not Yet">Not Yet</option></select></div>
                                    <div class="mb-3"><label class="form-label">Rencana Pertemuan</label><input type="date" name="rencana_pertemuan_kesepahaman" class="form-control"></div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3"><label class="form-label">Ruang Lingkup</label><textarea name="ruanglingkup_kesepahaman" class="form-control" rows="2"></textarea></div>
                                    <div class="mb-3"><label class="form-label">Rencana Kolaborasi</label><textarea name="rencana_kolaborasi_kesepahaman" class="form-control" rows="2"></textarea></div>
                                    <div class="mb-3"><label class="form-label">Status/Progres</label><textarea name="status_progres_kesepahaman" class="form-control" rows="2"></textarea></div>
                                    <div class="mb-3"><label class="form-label">Tindak Lanjut</label><textarea name="tindaklanjut_kesepahaman" class="form-control" rows="2"></textarea></div>
                                    <div class="mb-3"><label class="form-label">Keterangan</label><input type="text" name="keterangan_kesepahaman" class="form-control"></div>
                                </div>
                            </div>

                            <div class="text-center mt-4" id="lanjutPksButtonContainer">
                                <button type="button" class="btn btn-warning" id="btnLanjutPks"><i class="bi bi-arrow-down-circle"></i> Lanjut isi PKS (Perjanjian Kerja Sama)</button>
                            </div>

                            <div id="pksSection" style="display: none;">
                                <h5 class="form-section-title mt-5"><i class="bi bi-file-earmark-text"></i> Tahap Perjanjian Kerja Sama (PKS)</h5>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="mb-3"><label class="form-label">Status PKS</label><select name="status_pks" class="form-select"><option value="">-- Pilih Status --</option><option value="Signed">Signed</option><option value="Not Available">Not Available</option><option value="Drafting/In Progress">Drafting/In Progress</option></select></div>
                                        <div class="mb-3"><label class="form-label">Nomor</label><input type="text" name="nomor_pks" class="form-control"></div>
                                        <div class="mb-3"><label class="form-label">Tanggal</label><input type="date" name="tanggal_pks" class="form-control"></div>
                                        <div class="mb-3"><label class="form-label">Status Pelaksanaan PKS</label><select name="status_pelaksanaan_pks" class="form-select"><option value="">-- Pilih Status --</option><option value="Implemented">Implemented</option><option value="In Progress">In Progress</option><option value="Not Yet">Not Yet</option></select></div>
                                        <div class="mb-3"><label class="form-label">Rencana Pertemuan</label><input type="date" name="rencana_pertemuan_pks" class="form-control"></div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3"><label class="form-label">Ruang Lingkup</label><textarea name="ruanglingkup_pks" class="form-control" rows="2"></textarea></div>
                                        <div class="mb-3"><label class="form-label">Status/Progres</label><textarea name="status_progres_pks" class="form-control" rows="2"></textarea></div>
                                        <div class="mb-3"><label class="form-label">Tindak Lanjut</label><textarea name="tindaklanjut_pks" class="form-control" rows="2"></textarea></div>
                                        <div class="mb-3"><label class="form-label">Keterangan</label><input type="text" name="keterangan_pks" class="form-control"></div>
                                    </div>
                                </div>
                            </div>
                            
                            <hr class="my-5">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="index.php" class="btn btn-secondary">Batal</a>
                                <button type="submit" class="btn btn-primary"><i class="bi bi-save-fill me-2"></i>Simpan Mitra</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btnLanjutPks = document.getElementById('btnLanjutPks');
            const pksSection = document.getElementById('pksSection');
            const btnContainer = document.getElementById('lanjutPksButtonContainer');

            btnLanjutPks.addEventListener('click', function() {
                // Sembunyikan tombol
                btnContainer.style.display = 'none';
                
                // Tampilkan form PKS
                pksSection.style.display = 'block';

                // Scroll ke bagian PKS
                pksSection.scrollIntoView({ behavior: 'smooth' });
            });
        });
    </script>
</body>
</html>