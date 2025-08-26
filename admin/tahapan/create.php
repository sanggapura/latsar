<?php
include "db.php";

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // validasi sederhana
    if (empty($_POST['nama_mitra'])) $errors['nama_mitra'] = "Nama Mitra wajib diisi";
    if (empty($_POST['jenis_mitra'])) $errors['jenis_mitra'] = "Jenis Mitra wajib dipilih";

    if (count($errors) === 0) {
        $nama_mitra          = $_POST['nama_mitra'];
        $jenis_mitra         = $_POST['jenis_mitra'];
        $sumber_usulan       = $_POST['sumber_usulan'];
        $status_mou          = $_POST['status_mou'];
        $nomor_mou           = $_POST['nomor_mou'];
        $tanggal_mou         = $_POST['tanggal_mou'];
        $ruang_lingkup_mou   = $_POST['ruang_lingkup_mou'];
        $status_pelaksanaan  = $_POST['status_pelaksanaan'];
        $rencana_pertemuan   = $_POST['rencana_pertemuan'];
        $rencana_kolaborasi  = $_POST['rencana_kolaborasi'];
        $status_progres      = $_POST['status_progres'];
        $tindak_lanjut       = $_POST['tindak_lanjut'];
        $status_pks          = $_POST['status_pks'];
        $ruanglingkup_pks    = $_POST['ruanglingkup_pks'];
        $nomor_kb_pks        = $_POST['nomor_kb_pks'];
        $tanggal_kb_pks      = $_POST['tanggal_kb_pks'];
        $keterangan          = $_POST['keterangan'];

        $sql = "INSERT INTO tahapan_kerjasama 
                (nama_mitra, jenis_mitra, sumber_usulan, status_mou, nomor_mou, tanggal_mou, ruang_lingkup_mou, status_pelaksanaan, 
                rencana_pertemuan, rencana_kolaborasi, status_progres, tindak_lanjut, status_pks, ruanglingkup_pks, nomor_kb_pks, 
                tanggal_kb_pks, keterangan)
                VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "sssssssssssssssss", 
            $nama_mitra, $jenis_mitra, $sumber_usulan, $status_mou, $nomor_mou, $tanggal_mou, $ruang_lingkup_mou, $status_pelaksanaan, 
            $rencana_pertemuan, $rencana_kolaborasi, $status_progres, $tindak_lanjut, $status_pks, $ruanglingkup_pks, $nomor_kb_pks, 
            $tanggal_kb_pks, $keterangan
        );
        $stmt->execute();

        header("Location: /latsar/admin/tahapan/index.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Input Data Mitra</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
  <div class="row">
    <!-- Form -->
    <div class="col-md-8">
      <div class="card shadow-sm">
        <div class="card-body">
          <h3 class="mb-4">Form Input Mitra</h3>
          <form method="POST">

            <!-- Nama Mitra -->
            <div class="mb-3">
              <label class="form-label">Nama Mitra</label>
              <input type="text" name="nama_mitra" 
                     class="form-control <?php echo isset($errors['nama_mitra']) ? 'is-invalid' : ''; ?>" 
                     value="<?php echo $_POST['nama_mitra'] ?? ''; ?>">
              <div class="invalid-feedback"><?php echo $errors['nama_mitra'] ?? ''; ?></div>
            </div>

            <!-- Jenis Mitra -->
            <div class="mb-3">
              <label class="form-label">Jenis Mitra</label>
              <select name="jenis_mitra" class="form-select <?php echo isset($errors['jenis_mitra']) ? 'is-invalid' : ''; ?>">
                <option value="">--Pilih--</option>
                <option <?php if(($_POST['jenis_mitra'] ?? '')=="Kementerian/Lembaga") echo "selected"; ?>>Kementerian/Lembaga</option>
                <option <?php if(($_POST['jenis_mitra'] ?? '')=="Pemerintah Daerah") echo "selected"; ?>>Pemerintah Daerah</option>
                <option <?php if(($_POST['jenis_mitra'] ?? '')=="Mitra Pembangunan") echo "selected"; ?>>Mitra Pembangunan</option>
                <option <?php if(($_POST['jenis_mitra'] ?? '')=="Swasta/Perusahaan") echo "selected"; ?>>Swasta/Perusahaan</option>
              </select>
              <div class="invalid-feedback"><?php echo $errors['jenis_mitra'] ?? ''; ?></div>
            </div>

            <!-- Sumber Usulan -->
            <div class="mb-3">
              <label class="form-label">Sumber Usulan</label>
              <input type="text" name="sumber_usulan" class="form-control" value="<?php echo $_POST['sumber_usulan'] ?? ''; ?>">
            </div>

            <!-- Status MoU + Nomor + Tanggal + Ruang Lingkup -->
            <div class="mb-3">
              <label class="form-label">Status MoU</label>
              <select name="status_mou" class="form-select">
                <option value="">--Pilih--</option>
                <option>Signed</option>
                <option>Not Available</option>
                <option>Drafting/In Progress</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Nomor MoU</label>
              <input type="text" name="nomor_mou" maxlength="500" class="form-control">
            </div>
            <div class="mb-3">
              <label class="form-label">Tanggal MoU</label>
              <input type="date" name="tanggal_mou" class="form-control">
            </div>
            <div class="mb-3">
              <label class="form-label">Ruang Lingkup MoU</label>
              <textarea name="ruang_lingkup_mou" rows="3" class="form-control"></textarea>
            </div>

            <!-- Status Pelaksanaan + Rencana Pertemuan + Kolaborasi + Progres + Tindak Lanjut -->
            <div class="mb-3">
              <label class="form-label">Status Pelaksanaan</label>
              <select name="status_pelaksanaan" class="form-select">
                <option value="">--Pilih--</option>
                <option>Implemented</option>
                <option>In Progress</option>
                <option>Not Yet</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Rencana Pertemuan</label>
              <input type="date" name="rencana_pertemuan" class="form-control">
            </div>
            <div class="mb-3">
              <label class="form-label">Rencana Kolaborasi</label>
              <textarea name="rencana_kolaborasi" rows="3" class="form-control"></textarea>
            </div>
            <div class="mb-3">
              <label class="form-label">Status / Progres</label>
              <textarea name="status_progres" rows="3" class="form-control"></textarea>
            </div>
            <div class="mb-3">
              <label class="form-label">Tindak Lanjut</label>
              <textarea name="tindak_lanjut" rows="3" class="form-control"></textarea>
            </div>

            <!-- Status PKS + Ruang Lingkup + Nomor + Tanggal -->
            <div class="mb-3">
              <label class="form-label">Status PKS</label>
              <select name="status_pks" class="form-select">
                <option value="">--Pilih--</option>
                <option>Signed</option>
                <option>Not Available</option>
                <option>Drafting/In Progress</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Ruang Lingkup PKS</label>
              <textarea name="ruanglingkup_pks" rows="3" class="form-control"></textarea>
            </div>
            <div class="mb-3">
              <label class="form-label">Nomor KB/PKS</label>
              <input type="text" name="nomor_kb_pks" maxlength="500" class="form-control">
            </div>
            <div class="mb-3">
              <label class="form-label">Tanggal KB/PKS</label>
              <input type="date" name="tanggal_kb_pks" class="form-control">
            </div>

            <!-- Keterangan -->
            <div class="mb-3">
              <label class="form-label">Keterangan</label>
              <input type="text" name="keterangan" class="form-control">
            </div>

            <button type="submit" class="btn btn-primary">Simpan</button>
          </form>
        </div>
      </div>
    </div>

    <!-- Panduan -->
    <div class="col-md-4">
      <div class="card border-info shadow-sm">
        <div class="card-header bg-info text-white">
          Panduan Pengisian
        </div>
        <div class="card-body small">
          <ul>
            <li><b>Nama Mitra:</b> Isi nama lengkap mitra (WAJIB).</li>
            <li><b>Jenis Mitra:</b> Pilih salah satu kategori (WAJIB).</li>
            <li><b>Sumber Usulan:</b> Tulis asal/usulan kerja sama.</li>
            <li><b>Status MoU:</b> Pilih status terkini MoU.</li>
            <li><b>Nomor MoU:</b> Isi nomor dokumen.</li>
            <li><b>Tanggal MoU:</b> Isi sesuai format date.</li>
            <li><b>Ruang Lingkup MoU:</b> Jelaskan cakupan MoU.</li>
            <li><b>Status Pelaksanaan:</b> Pilih Implemented/In Progress/Not Yet.</li>
            <li><b>Rencana Pertemuan:</b> Isi tanggal rencana pertemuan.</li>
            <li><b>Rencana Kolaborasi:</b> Uraikan rencana kolaborasi.</li>
            <li><b>Status/Progres:</b> Tulis status terbaru.</li>
            <li><b>Tindak Lanjut:</b> Isi langkah berikutnya.</li>
            <li><b>Status PKS:</b> Pilih status PKS.</li>
            <li><b>Ruang Lingkup PKS:</b> Jelaskan isi PKS.</li>
            <li><b>Nomor KB/PKS:</b> Isi jika ada.</li>
            <li><b>Tanggal KB/PKS:</b> Isi tanggal penandatanganan.</li>
            <li><b>Keterangan:</b> Catatan tambahan.</li>
            <li>Kolom merah = ada kesalahan input.</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>
