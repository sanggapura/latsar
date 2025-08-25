<?php include "db.php"; 

$id = $_GET['id'];
// hapus file
$res = $conn->query("SELECT file1, file2, file3 FROM tahapan_kerjasama WHERE id=$id");
if ($row = $res->fetch_assoc()) {
    for ($i=1;$i<=3;$i++) {
        if (!empty($row["file$i"]) && file_exists("upload/".$row["file$i"])) {
            unlink("upload/".$row["file$i"]);
        }
    }
}
$conn->query("DELETE FROM tahapan_kerjasama WHERE id=$id");
header("Location: index.php");
?>
