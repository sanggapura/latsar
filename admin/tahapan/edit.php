<?php
include "db.php";

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: index.php");
    exit();
}

// Ambil data lama
$stmt = $conn->prepare("SELECT * FROM tahapan_kerjasama WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
if (!$data) {
    echo "Data tidak ditemukan!";
    exit();
}

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

        // Upload file (max 3) - kalau kosong, tetap pakai file lama
        $allowed_ext = ['pdf','doc','docx','xls','xlsx'];
        $files = [];
        for ($i=1; $i<=3; $i++) {
            if (!empty($_FILES["file$i"]['name'])) {
                $ext = strtolower(pathinfo($_FILES["file$i"]['name'], PATHINFO_EXTENSION));
                if (in_array($ext, $allowed_ext)) {
                    $new_name = time() . "_$i." . $ext;
                    $target = "uploads/" . $new_name;
                    move_uploaded_file($_FILES["file$i"]['tmp_name'], $target);
                    $files[$i] = $target;

                    // Hapus file lama kalau ada
                    if (!empty($data["file$i"]) && file_exists($data["file$i"])) {
                        unlink($data["file$i"]);
                    }
                } else {
                    $errors["file$i"] = "Format file tidak valid (hanya pdf, word, excel)";
                    $files[$i] = $data["file$i"];
                }
            } else {
                $files[$i] = $data["file$i"]; // tetap pakai lama
            }
        }

        if (count($errors) === 0) {
            $sql = "UPDATE tahapan_kerjasama SET 
                nama_mitra=?, jenis_mitra=?, sumber_usulan=?, status_mou=?, nomor_mou=?, tanggal_mou=?, ruang_lingkup_mou=?, 
                status_pelaksanaan=?, rencana_pertemuan=?, rencana_kolaborasi=?, status_progres=?, tindak_lanjut=?, 
                status_pks=?, ruanglingkup_pks=?, nomor_kb_pks=?, tanggal_kb_pks=?, keterangan=?, file1=?, file2=?, file3=? 
                WHERE id=?";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssssssssssssssssssi", 
                $nama_mitra, $jenis_mitra, $sumber_usulan, $status_mou, $nomor_mou, $tanggal_mou, $ruang_lingkup_mou, 
                $status_pelaksanaan, $rencana_pertemuan, $rencana_kolaborasi, $status_progres, $tindak_lanjut, 
                $status_pks, $ruanglingkup_pks, $nomor_kb_pks, $tanggal_kb_pks, $keterangan, 
                $files[1], $files[2], $files[3], $id
            );
            $stmt->execute();

            header("Location: index.php");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Edit Data Mitra</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
  <div class="row">
    <!-- Form -->
    <div class="col-md-8">
      <div class="card shadow-sm">
        <div class="card-body">
          <h3 class="mb-4">Form Edit Mitra</h3>
          <form method="POST" enctype="multipart/form-data">

            <!-- Nama Mitra -->
            <div class="mb-3">
              <label class="form-label">Nama Mitra</label>
              <input type="text" name="nama_mitra" 
                     class="form-control <?php echo isset($errors['nama_mitra']) ? 'is-invalid' : ''; ?>" 
                     value="<?php echo htmlspecialchars($_POST['nama_mitra'] ?? $data['nama_mitra']); ?>">
              <div class="invalid-feedback"><?php echo $errors['nama_mitra'] ?? ''; ?></div>
            </div>

            <!-- Jenis Mitra -->
            <div class="mb-3">
              <label class="form-label">Jenis Mitra</label>
              <select name="jenis_mitra" class="form-select <?php echo isset($errors['jenis_mitra']) ? 'is-invalid' : ''; ?>">
                <option value="">--Pilih--</option>
                <?php
                $options = ["Kementerian/Lembaga","Pemerintah Daerah","Mitra Pembangunan","Swasta/Perusahaan"];
                foreach($options as $opt){
                    $sel = (($data['jenis_mitra']==$opt) || (($_POST['jenis_mitra']??'')==$opt)) ? "selected":"";
                    echo "<option $sel>$opt</option>";
                }
                ?>
              </select>
              <div class="invalid-feedback"><?php echo $errors['jenis_mitra'] ?? ''; ?></div>
            </div>

            <!-- Sumber Usulan -->
            <div class="mb-3">
              <label class="form-label">Sumber Usulan</label>
              <input type="text" name="sumber_usulan" class="form-control" 
                     value="<?php echo htmlspecialchars($_POST['sumber_usulan'] ?? $data['sumber_usulan']); ?>">
            </div>

            <!-- Status MoU -->
            <div class="mb-3">
              <label class="form-label">Status MoU</label>
              <select name="status_mou" class="form-select">
                <option value="">--Pilih--</option>
                <?php
                $options = ["Signed","Not Available","Drafting/In Progress"];
                foreach($options as $opt){
                    $sel = (($data['status_mou']==$opt) || (($_POST['status_mou']??'')==$opt)) ? "selected":"";
                    echo "<option $sel>$opt</option>";
                }
                ?>
              </select>
            </div>

            <div class="mb-3">
              <label class="form-label">Nomor MoU</label>
              <input type="text" name="nomor_mou" maxlength="500" class="form-control" 
                     value="<?php echo htmlspecialchars($_POST['nomor_mou'] ?? $data['nomor_mou']); ?>">
            </div>

            <div class="mb-3">
              <label class="form-label">Tanggal MoU</label>
              <input type="date" name="tanggal_mou" class="form-control" 
                     value="<?php echo htmlspecialchars($_POST['tanggal_mou'] ?? $data['tanggal_mou']); ?>">
            </div>

            <div class="mb-3">
              <label class="form-label">Ruang Lingkup MoU</label>
              <textarea name="ruang_lingkup_mou" rows="3" class="form-control"><?php echo htmlspecialchars($_POST['ruang_lingkup_mou'] ?? $data['ruang_lingkup_mou']); ?></textarea>
            </div>

            <!-- Status Pelaksanaan -->
            <div class="mb-3">
              <label class="form-label">Status Pelaksanaan</label>
              <select name="status_pelaksanaan" class="form-select">
                <option value="">--Pilih--</option>
                <?php
                $options = ["Implemented","In Progress","Not Yet"];
                foreach($options as $opt){
                    $sel = (($data['status_pelaksanaan']==$opt) || (($_POST['status_pelaksanaan']??'')==$opt)) ? "selected":"";
                    echo "<option $sel>$opt</option>";
                }
                ?>
              </select>
            </div>

            <div class="mb-3">
              <label class="form-label">Rencana Pertemuan</label>
              <input type="date" name="rencana_pertemuan" class="form-control" 
                     value="<?php echo htmlspecialchars($_POST['rencana_pertemuan'] ?? $data['rencana_pertemuan']); ?>">
            </div>

            <div class="mb-3">
              <label class="form-label">Rencana Kolaborasi</label>
              <textarea name="rencana_kolaborasi" rows="3" class="form-control"><?php echo htmlspecialchars($_POST['rencana_kolaborasi'] ?? $data['rencana_kolaborasi']); ?></textarea>
            </div>

            <div class="mb-3">
              <label class="form-label">Status / Progres</label>
              <textarea name="status_progres" rows="3" class="form-control"><?php echo htmlspecialchars($_POST['status_progres'] ?? $data['status_progres']); ?></textarea>
            </div>

            <div class="mb-3">
              <label class="form-label">Tindak Lanjut</label>
              <textarea name="tindak_lanjut" rows="3" class="form-control"><?php echo htmlspecialchars($_POST['tindak_lanjut'] ?? $data['tindak_lanjut']); ?></textarea>
            </div>

            <!-- Status PKS -->
            <div class="mb-3">
              <label class="form-label">Status PKS</label>
              <select name="status_pks" class="form-select">
                <option value="">--Pilih--</option>
                <?php
                $options = ["Signed","Not Available","Drafting/In Progress"];
                foreach($options as $opt){
                    $sel = (($data['status_pks']==$opt) || (($_POST['status_pks']??'')==$opt)) ? "selected":"";
                    echo "<option $sel>$opt</option>";
                }
                ?>
              </select>
            </div>

            <div class="mb-3">
              <label class="form-label">Ruang Lingkup PKS</label>
              <textarea name="ruanglingkup_pks" rows="3" class="form-control"><?php echo htmlspecialchars($_POST['ruanglingkup_pks'] ?? $data['ruanglingkup_pks']); ?></textarea>
            </div>

            <div class="mb-3">
              <label class="form-label">Nomor KB/PKS</label>
              <input type="text" name="nomor_kb_pks" maxlength="500" class="form-control" 
                     value="<?php echo htmlspecialchars($_POST['nomor_kb_pks'] ?? $data['nomor_kb_pks']); ?>">
            </div>

            <div class="mb-3">
              <label class="form-label">Tanggal KB/PKS</label>
              <input type="date" name="tanggal_kb_pks" class="form-control" 
                     value="<?php echo htmlspecialchars($_POST['tanggal_kb_pks'] ?? $data['tanggal_kb_pks']); ?>">
            </div>

            <div class="mb-3">
              <label class="form-label">Keterangan</label>
              <input type="text" name="keterangan" class="form-control" 
                     value="<?php echo htmlspecialchars($_POST['keterangan'] ?? $data['keterangan']); ?>">
            </div>

            <!-- Upload -->
            <div class="mb-3">
              <label class="form-label">Upload File (max 3)</label>
              <?php for($i=1;$i<=3;$i++): ?>
                <input type="file" name="file<?php echo $i; ?>" class="form-control mb-2 <?php echo isset($errors["file$i"]) ? 'is-invalid':''; ?>" accept=".pdf,.doc,.docx,.xls,.xlsx">
                <?php if (!empty($data["file$i"])): ?>
                  <small class="text-muted">File lama: <a href="<?php echo $data["file$i"]; ?>" target="_blank">Lihat</a></small><br>
                <?php endif; ?>
              <?php endfor; ?>
              <div class="invalid-feedback"><?php echo $errors['file1'] ?? $errors['file2'] ?? $errors['file3'] ?? ''; ?></div>
            </div>

            <button type="submit" class="btn btn-primary">Update</button>
          </form>
        </div>
      </div>
    </div>

  </div>
</div>
</body>
</html>
