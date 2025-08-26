<?php
include "db.php";

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$result = $conn->query("SELECT * FROM tahapan_kerjasama WHERE id = $id");
$row = $result->fetch_assoc();

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=mitra_".$row['id'].".xls");

// Kolom yang ditampilkan hanya sampai 'keterangan'
$allowed_fields = [
    'id','nama_mitra','jenis_mitra','sumber_usulan','status_mou','nomor_mou','tanggal_mou',
    'ruang_lingkup_mou','status_pelaksanaan','rencana_pertemuan','rencana_kolaborasi',
    'status_progres','tindak_lanjut','status_pks','ruanglingkup_pks','nomor_kb_pks',
    'tanggal_kb_pks','keterangan'
];

echo "<table border='1' style='border-collapse:collapse; font-family:Arial; font-size:12px;'>";

// Header
echo "<tr style='background:#cce5ff; font-weight:bold;'>";
foreach ($row as $field => $value) {
    if (in_array($field, $allowed_fields)) {
        echo "<th style='padding:5px; text-align:left; vertical-align:top;'>"
            .htmlspecialchars(strtoupper(str_replace("_"," ",$field)))."</th>";
    }
}
echo "</tr>";

// Data
echo "<tr>";
foreach ($row as $field => $value) {
    if (in_array($field, $allowed_fields)) {
        echo "<td style='padding:5px; text-align:left; vertical-align:top;'>"
            .nl2br(htmlspecialchars($value))."</td>";
    }
}
echo "</tr>";

echo "</table>";
