<?php
include "db.php"; 

error_reporting(E_ALL);
ini_set('display_errors', 1);

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['nama_mitra'])) $errors['nama_mitra'] = "Nama Mitra wajib diisi";
    if (empty($_POST['jenis_mitra'])) $errors['jenis_mitra'] = "Jenis Mitra wajib dipilih";

    if (count($errors) === 0) {
        $sql = "INSERT INTO tahapan_kerjasama 
            (nama_mitra, jenis_mitra, sumber_usulan,
             status_kesepahaman, nomor_kesepahaman, tanggal_kesepahaman, ruanglingkup_kesepahaman, status_pelaksanaan_kesepahaman,
             rencana_pertemuan_kesepahaman, rencana_kolaborasi_kesepahaman, status_progres_kesepahaman, tindaklanjut_kesepahaman, keterangan_kesepahaman,
             status_pks, nomor_pks, tanggal_pks, ruanglingkup_pks, status_pelaksanaan_pks,
             rencana_pertemuan_pks, status_progres_pks, tindaklanjut_pks, keterangan_pks, tandai)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "sssssssssssssssssssssss",
            $_POST['nama_mitra'], $_POST['jenis_mitra'], $_POST['sumber_usulan'],
            $_POST['status_kesepahaman'], $_POST['nomor_kesepahaman'], $_POST['tanggal_kesepahaman'], $_POST['ruanglingkup_kesepahaman'], $_POST['status_pelaksanaan_kesepahaman'],
            $_POST['rencana_pertemuan_kesepahaman'], $_POST['rencana_kolaborasi_kesepahaman'], $_POST['status_progres_kesepahaman'], $_POST['tindaklanjut_kesepahaman'], $_POST['keterangan_kesepahaman'],
            $_POST['status_pks'], $_POST['nomor_pks'], $_POST['tanggal_pks'], $_POST['ruanglingkup_pks'], $_POST['status_pelaksanaan_pks'],
            $_POST['rencana_pertemuan_pks'], $_POST['status_progres_pks'], $_POST['tindaklanjut_pks'], $_POST['keterangan_pks'],
            isset($_POST['tandai']) ? 1 : 0
        );
        $stmt->execute();

        header("Location: index.php");
        exit();
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
    function showPKS() {
      let pksForm = document.getElementById("formPKS");
      pksForm.style.display = "block";
      pksForm.scrollIntoView();
    }
  </script>
</head>
<body class="bg-light">
<div class="container py-4">
  <h3 class="mb-4">Form Input Mitra</h3>
  <form method="POST">

    <!-- Nama Mitra -->
    <div class="mb-3">
      <label class="form-label">Nama Mitra</label>
      <input type="text" name="nama_mitra" class="form-control <?php echo isset($errors['nama_mitra']) ? 'is-invalid' : ''; ?>">
      <div class="invalid-feedback"><?php echo $errors['nama_mitra'] ?? ''; ?></div>
    </div>

    <!-- Jenis Mitra -->
    <div class="mb-3 col-md-6">
      <label class="form-label">Jenis Mitra</label>
      <select name="jenis_mitra" class="form-select <?php echo isset($errors['jenis_mitra']) ? 'is-invalid' : ''; ?>">
        <option value="">--Pilih--</option>
        <option>Kementerian/Lembaga</option>
        <option>Pemerintah Daerah</option>
        <option>Asosiasi</option>
        <option>Perusahaan</option>
        <option>Universitas</option>
        <option>Job Portal</option>
      </select>
      <div class="invalid-feedback"><?php echo $errors['jenis_mitra'] ?? ''; ?></div>
    </div>

    <!-- Sumber Usulan -->
    <div class="mb-3 col-md-6">
      <label class="form-label">Sumber Usulan</label>
      <input type="text" name="sumber_usulan" class="form-control">
    </div>

    <!-- ================= KESPAHAAMAN ================= -->
    <h5 class="mt-4">Kesepahaman</h5>

    <div class="mb-3 col-md-6">
      <label class="form-label">Status Kesepahaman</label>
      <select name="status_kesepahaman" class="form-select">
        <option value="">--Pilih--</option>
        <option>Signed</option>
        <option>Not Available</option>
        <option>Drafting/In Progress</option>
      </select>
    </div>

    <div class="mb-3 col-md-6">
      <label class="form-label">Nomor Kesepahaman</label>
      <input type="text" name="nomor_kesepahaman" class="form-control">
    </div>

    <div class="mb-3 col-md-4">
      <label class="form-label">Tanggal Kesepahaman</label>
      <input type="date" name="tanggal_kesepahaman" class="form-control">
    </div>

    <div class="mb-3">
      <label class="form-label">Ruang Lingkup Kesepahaman</label>
      <textarea name="ruanglingkup_kesepahaman" rows="3" class="form-control"></textarea>
    </div>

    <div class="mb-3 col-md-6">
      <label class="form-label">Status Pelaksanaan</label>
      <select name="status_pelaksanaan_kesepahaman" class="form-select">
        <option value="">--Pilih--</option>
        <option>Implemented</option>
        <option>In Progress</option>
        <option>Not Yet</option>
      </select>
    </div>

    <div class="mb-3 col-md-4">
      <label class="form-label">Rencana Pertemuan</label>
      <input type="date" name="rencana_pertemuan_kesepahaman" class="form-control">
    </div>

    <div class="mb-3">
      <label class="form-label">Rencana Kolaborasi</label>
      <textarea name="rencana_kolaborasi_kesepahaman" rows="3" class="form-control"></textarea>
    </div>
    <div class="mb-3">
      <label class="form-label">Status/Progres</label>
      <textarea name="status_progres_kesepahaman" rows="3" class="form-control"></textarea>
    </div>
    <div class="mb-3">
      <label class="form-label">Tindak Lanjut</label>
      <textarea name="tindaklanjut_kesepahaman" rows="3" class="form-control"></textarea>
    </div>
    <div class="mb-3 col-md-6">
      <label class="form-label">Keterangan</label>
      <input type="text" name="keterangan_kesepahaman" class="form-control">
    </div>

    <!-- Checkbox Tandai -->
    <div class="form-check mb-3">
      <input class="form-check-input" type="checkbox" id="tandai" name="tandai" value="1">
      <label class="form-check-label" for="tandai">Tandai</label>
    </div>

    <!-- Tombol Lanjutkan ke PKS -->
    <button type="button" class="btn btn-success mb-3" onclick="showPKS()">Lanjutkan ke PKS</button>

    <!-- ================= PKS ================= -->
    <div id="formPKS" style="display:none;">
      <h5 class="mt-4">PKS</h5>

      <div class="mb-3 col-md-6">
        <label class="form-label">Status PKS</label>
        <select name="status_pks" class="form-select">
          <option value="">--Pilih--</option>
          <option>Signed</option>
          <option>Not Available</option>
          <option>Drafting/In Progress</option>
        </select>
      </div>

      <div class="mb-3 col-md-6">
        <label class="form-label">Nomor PKS</label>
        <input type="text" name="nomor_pks" class="form-control">
      </div>

      <div class="mb-3 col-md-4">
        <label class="form-label">Tanggal PKS</label>
        <input type="date" name="tanggal_pks" class="form-control">
      </div>

      <div class="mb-3">
        <label class="form-label">Ruang Lingkup PKS</label>
        <textarea name="ruanglingkup_pks" rows="3" class="form-control"></textarea>
      </div>

      <div class="mb-3 col-md-6">
        <label class="form-label">Status Pelaksanaan PKS</label>
        <select name="status_pelaksanaan_pks" class="form-select">
          <option value="">--Pilih--</option>
          <option>Implemented</option>
          <option>In Progress</option>
          <option>Not Yet</option>
        </select>
      </div>

      <div class="mb-3 col-md-4">
        <label class="form-label">Rencana Pertemuan PKS</label>
        <input type="date" name="rencana_pertemuan_pks" class="form-control">
      </div>

      <div class="mb-3">
        <label class="form-label">Status/Progres PKS</label>
        <textarea name="status_progres_pks" rows="3" class="form-control"></textarea>
      </div>
      <div class="mb-3">
        <label class="form-label">Tindak Lanjut PKS</label>
        <textarea name="tindaklanjut_pks" rows="3" class="form-control"></textarea>
      </div>
      <div class="mb-3 col-md-6">
        <label class="form-label">Keterangan PKS</label>
        <input type="text" name="keterangan_pks" class="form-control">
      </div>
    </div>

    <!-- Tombol Simpan -->
    <button type="submit" class="btn btn-primary">Simpan</button>
  </form>
</div>
</body>
</html>
