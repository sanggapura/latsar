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

        $stmt->bind_param(
            "sssissssssssssssssssss",
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

include __DIR__ . "/../../views/header.php";
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Input Data Mitra</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script>
    function togglePKS() {
      let pksForm = document.getElementById("formPKS");
      let btn = document.getElementById("btnPKS");
      
      if (pksForm.style.display === "none" || pksForm.style.display === "") {
        pksForm.style.display = "block";
        btn.textContent = "Sembunyikan PKS";
        btn.className = "btn btn-secondary";
        pksForm.scrollIntoView({behavior: 'smooth'});
      } else {
        pksForm.style.display = "none";
        btn.textContent = "Lanjutkan ke PKS";
        btn.className = "btn btn-success";
      }
    }
  </script>
</head>
<body class="bg-light">
<div class="container py-4">
  <h3 class="mb-4">Form Input Mitra</h3>
  <form method="POST" novalidate>

    <!-- Nama Mitra -->
    <div class="mb-3">
      <label class="form-label">Nama Mitra <span class="text-danger">*</span></label>
      <input type="text" name="nama_mitra" class="form-control <?php echo isset($errors['nama_mitra']) ? 'is-invalid' : ''; ?>" 
             value="<?= htmlspecialchars($_POST['nama_mitra'] ?? ''); ?>" required>
      <div class="invalid-feedback"><?php echo $errors['nama_mitra'] ?? ''; ?></div>
    </div>

    <!-- Jenis Mitra -->
    <div class="mb-3">
      <label class="form-label">Jenis Mitra <span class="text-danger">*</span></label>
      <select name="jenis_mitra" class="form-select <?php echo isset($errors['jenis_mitra']) ? 'is-invalid' : ''; ?>" required>
        <option value="">--Pilih--</option>
        <?php 
          $opsi = ['Kementerian/Lembaga','Pemerintah Daerah','Asosiasi','Perusahaan','Universitas','Job Portal'];
          foreach ($opsi as $o) {
              $sel = (($_POST['jenis_mitra'] ?? '') == $o) ? 'selected' : '';
              echo "<option $sel>$o</option>";
          }
        ?>
      </select>
      <div class="invalid-feedback"><?php echo $errors['jenis_mitra'] ?? ''; ?></div>
    </div>

    <!-- Sumber Usulan -->
    <div class="mb-3">
      <label class="form-label">Sumber Usulan</label>
      <input type="text" name="sumber_usulan" class="form-control" value="<?= htmlspecialchars($_POST['sumber_usulan'] ?? ''); ?>">
    </div>

    <!-- ================= KESEPAHAMAN ================= -->
    <h5 class="mt-4">Kesepahaman</h5>
    <div class="mb-3">
      <label class="form-label">Status Kesepahaman</label>
      <select name="status_kesepahaman" class="form-select">
        <option value="">--Pilih--</option>
        <?php 
          $opsi = ['Signed','Not Available','Drafting/In Progress'];
          foreach ($opsi as $o) {
              $sel = (($_POST['status_kesepahaman'] ?? '') == $o) ? 'selected' : '';
              echo "<option $sel>$o</option>";
          }
        ?>
      </select>
    </div>
    <div class="mb-3">
      <label class="form-label">Nomor Kesepahaman</label>
      <input type="text" name="nomor_kesepahaman" class="form-control" value="<?= htmlspecialchars($_POST['nomor_kesepahaman'] ?? ''); ?>">
    </div>
    <div class="mb-3">
      <label class="form-label">Tanggal Kesepahaman</label>
      <input type="date" name="tanggal_kesepahaman" class="form-control" value="<?= htmlspecialchars($_POST['tanggal_kesepahaman'] ?? ''); ?>">
    </div>
    <div class="mb-3">
      <label class="form-label">Ruang Lingkup Kesepahaman</label>
      <textarea name="ruanglingkup_kesepahaman" rows="3" class="form-control"><?= htmlspecialchars($_POST['ruanglingkup_kesepahaman'] ?? ''); ?></textarea>
    </div>
    <div class="mb-3">
      <label class="form-label">Status Pelaksanaan</label>
      <select name="status_pelaksanaan_kesepahaman" class="form-select">
        <option value="">--Pilih--</option>
        <?php 
          $opsi = ['Implemented','In Progress','Not Yet'];
          foreach ($opsi as $o) {
              $sel = (($_POST['status_pelaksanaan_kesepahaman'] ?? '') == $o) ? 'selected' : '';
              echo "<option $sel>$o</option>";
          }
        ?>
      </select>
    </div>
    <div class="mb-3">
      <label class="form-label">Rencana Pertemuan</label>
      <input type="date" name="rencana_pertemuan_kesepahaman" class="form-control" value="<?= htmlspecialchars($_POST['rencana_pertemuan_kesepahaman'] ?? ''); ?>">
    </div>
    <div class="mb-3">
      <label class="form-label">Rencana Kolaborasi</label>
      <textarea name="rencana_kolaborasi_kesepahaman" rows="3" class="form-control"><?= htmlspecialchars($_POST['rencana_kolaborasi_kesepahaman'] ?? ''); ?></textarea>
    </div>
    <div class="mb-3">
      <label class="form-label">Status/Progres</label>
      <textarea name="status_progres_kesepahaman" rows="3" class="form-control"><?= htmlspecialchars($_POST['status_progres_kesepahaman'] ?? ''); ?></textarea>
    </div>
    <div class="mb-3">
      <label class="form-label">Tindak Lanjut</label>
      <textarea name="tindaklanjut_kesepahaman" rows="3" class="form-control"><?= htmlspecialchars($_POST['tindaklanjut_kesepahaman'] ?? ''); ?></textarea>
    </div>
    <div class="mb-3">
      <label class="form-label">Keterangan</label>
      <input type="text" name="keterangan_kesepahaman" class="form-control" value="<?= htmlspecialchars($_POST['keterangan_kesepahaman'] ?? ''); ?>">
    </div>

    <!-- Tombol Lanjutkan ke PKS dan Checkbox Prioritas -->
    <div class="d-flex align-items-center gap-4 mb-3">
      <button type="button" id="btnPKS" class="btn btn-success" onclick="togglePKS()">Lanjutkan ke PKS</button>
      
      <div class="form-check">
        <input class="form-check-input" type="checkbox" name="tandai" id="tandaiPrioritas" value="1" <?= isset($_POST['tandai']) ? 'checked' : ''; ?>>
        <label class="form-check-label" for="tandaiPrioritas">
          <i class="bi bi-exclamation-circle-fill text-primary me-1"></i>
          Tandai sebagai prioritas/perhatian khusus
        </label>
      </div>
    </div>

    <!-- ================= PKS ================= -->
    <div id="formPKS" style="display:none;">
      <h5 class="mt-4">PKS</h5>
      <div class="mb-3">
        <label class="form-label">Status PKS</label>
        <select name="status_pks" class="form-select">
          <option value="">--Pilih--</option>
          <?php 
            $opsi = ['Signed','Not Available','Drafting/In Progress'];
            foreach ($opsi as $o) {
                $sel = (($_POST['status_pks'] ?? '') == $o) ? 'selected' : '';
                echo "<option $sel>$o</option>";
            }
          ?>
        </select>
      </div>
      <div class="mb-3">
        <label class="form-label">Nomor PKS</label>
        <input type="text" name="nomor_pks" class="form-control" value="<?= htmlspecialchars($_POST['nomor_pks'] ?? ''); ?>">
      </div>
      <div class="mb-3">
        <label class="form-label">Tanggal PKS</label>
        <input type="date" name="tanggal_pks" class="form-control" value="<?= htmlspecialchars($_POST['tanggal_pks'] ?? ''); ?>">
      </div>
      <div class="mb-3">
        <label class="form-label">Ruang Lingkup PKS</label>
        <textarea name="ruanglingkup_pks" rows="3" class="form-control"><?= htmlspecialchars($_POST['ruanglingkup_pks'] ?? ''); ?></textarea>
      </div>
      <div class="mb-3">
        <label class="form-label">Status Pelaksanaan PKS</label>
        <select name="status_pelaksanaan_pks" class="form-select">
          <option value="">--Pilih--</option>
          <?php 
            $opsi = ['Implemented','In Progress','Not Yet'];
            foreach ($opsi as $o) {
                $sel = (($_POST['status_pelaksanaan_pks'] ?? '') == $o) ? 'selected' : '';
                echo "<option $sel>$o</option>";
            }
          ?>
        </select>
      </div>
      <div class="mb-3">
        <label class="form-label">Rencana Pertemuan PKS</label>
        <input type="date" name="rencana_pertemuan_pks" class="form-control" value="<?= htmlspecialchars($_POST['rencana_pertemuan_pks'] ?? ''); ?>">
      </div>
      <div class="mb-3">
        <label class="form-label">Status/Progres PKS</label>
        <textarea name="status_progres_pks" rows="3" class="form-control"><?= htmlspecialchars($_POST['status_progres_pks'] ?? ''); ?></textarea>
      </div>
      <div class="mb-3">
        <label class="form-label">Tindak Lanjut PKS</label>
        <textarea name="tindaklanjut_pks" rows="3" class="form-control"><?= htmlspecialchars($_POST['tindaklanjut_pks'] ?? ''); ?></textarea>
      </div>
      <div class="mb-3">
        <label class="form-label">Keterangan PKS</label>
        <input type="text" name="keterangan_pks" class="form-control" value="<?= htmlspecialchars($_POST['keterangan_pks'] ?? ''); ?>">
      </div>
    </div>

    <div class="d-flex gap-2">
      <button type="submit" class="btn btn-primary">Simpan</button>
      <a href="index.php" class="btn btn-secondary">Kembali</a>
    </div>
  </form>
</div>

<!-- Include Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
</body>
</html>
