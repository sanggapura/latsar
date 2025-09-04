<?php
// =================================================================
// FILE: export_excel.php (VERSI LENGKAP DENGAN PERBAIKAN BARIS BARU)
// =================================================================
include "db.php";

// 1. Validasi Keamanan & Ambil Data
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    die("Error: ID tidak valid.");
}

$stmt = $conn->prepare("SELECT * FROM tahapan_kerjasama WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if (!$row) {
    die("Error: Data tidak ditemukan.");
}

// 2. Tentukan Kolom yang Akan Diekspor Berdasarkan Kondisi
$pksDataExists = !empty(trim($row['status_pks'] ?? '')) || !empty(trim($row['nomor_pks'] ?? '')) || !empty(trim($row['tanggal_pks'] ?? ''));

$fieldMappings = [
    'id' => 'ID',
    'nama_mitra' => 'Nama Mitra',
    'jenis_mitra' => 'Jenis Mitra',
    'sumber_usulan' => 'Sumber Usulan',
    'tandai' => 'Prioritas',
    'status_kesepahaman' => 'Status Kesepahaman',
    'nomor_kesepahaman' => 'Nomor Kesepahaman',
    'tanggal_kesepahaman' => 'Tanggal Kesepahaman',
    'ruanglingkup_kesepahaman' => 'Ruang Lingkup Kesepahaman',
    'status_pelaksanaan_kesepahaman' => 'Status Pelaksanaan Kesepahaman',
    'rencana_pertemuan_kesepahaman' => 'Rencana Pertemuan Kesepahaman',
    'rencana_kolaborasi_kesepahaman' => 'Rencana Kolaborasi Kesepahaman',
    'status_progres_kesepahaman' => 'Status/Progres Kesepahaman',
    'tindaklanjut_kesepahaman' => 'Tindak Lanjut Kesepahaman',
    'keterangan_kesepahaman' => 'Keterangan Kesepahaman',
];

// Jika data PKS ada, tambahkan kolom PKS ke dalam daftar
if ($pksDataExists) {
    $pksMappings = [
        'status_pks' => 'Status PKS',
        'nomor_pks' => 'Nomor PKS',
        'tanggal_pks' => 'Tanggal PKS',
        'ruanglingkup_pks' => 'Ruang Lingkup PKS',
        'status_pelaksanaan_pks' => 'Status Pelaksanaan PKS',
        'rencana_pertemuan_pks' => 'Rencana Pertemuan PKS',
        'status_progres_pks' => 'Status/Progres PKS',
        'tindaklanjut_pks' => 'Tindak Lanjut PKS',
        'keterangan_pks' => 'Keterangan PKS'
    ];
    $fieldMappings = array_merge($fieldMappings, $pksMappings);
}


// 3. Buat File Excel
$sanitized_name = preg_replace('/[^a-zA-Z0-9_-]/', '_', $row['nama_mitra']);
$filename = "mitra_" . $sanitized_name . "_" . date('Ymd') . ".xls";

header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Pragma: no-cache");
header("Expires: 0");

/**
 * Memformat nilai sel dengan benar, termasuk menangani baris baru.
 *
 * @param mixed $value Nilai dari database.
 * @param string $field Nama kolom.
 * @return string Nilai yang sudah diformat.
 */
function formatCellValue($value, $field) {
    if ($value === null || $value === '') return '-';
    
    // Daftar field yang berpotensi memiliki banyak baris (textarea)
    $multiline_fields = [
        'ruanglingkup_kesepahaman', 'rencana_kolaborasi_kesepahaman', 
        'status_progres_kesepahaman', 'tindaklanjut_kesepahaman',
        'ruanglingkup_pks', 'status_progres_pks', 'tindaklanjut_pks'
    ];

    switch (true) {
        case $field === 'tandai':
            return $value == 1 ? 'Ya' : 'Tidak';
            
        case in_array($field, ['tanggal_kesepahaman', 'tanggal_pks', 'rencana_pertemuan_kesepahaman', 'rencana_pertemuan_pks']):
            return $value ? date('d-m-Y', strtotime($value)) : '-';
            
        case in_array($field, $multiline_fields):
            // **PERBAIKAN**: Ganti baris baru (\n) menjadi tag <br> yang dipahami Excel.
            // Fungsi htmlspecialchars tetap digunakan untuk keamanan data.
            return nl2br(htmlspecialchars($value));
            
        default:
            return htmlspecialchars($value);
    }
}

// Mulai output HTML untuk Excel
echo '<html xmlns:x="urn:schemas-microsoft-com:office:excel"><head><meta charset="UTF-8"><style>.header { background-color: #0a3d62; color: white; font-weight: bold; text-align: center; } td { padding: 5px; border: 1px solid #ccc; vertical-align: top; }</style></head><body>';
echo '<table border="1" style="border-collapse: collapse; width: 100%;">';

// Judul
echo '<tr><td colspan="' . count($fieldMappings) . '" class="header" style="font-size: 16px;">DETAIL DATA MITRA: ' . strtoupper(htmlspecialchars($row['nama_mitra'])) . '</td></tr>';
echo '<tr><td colspan="' . count($fieldMappings) . '" style="text-align:center;">Diekspor pada: ' . date('d F Y H:i:s') . '</td></tr>';
echo '<tr><td colspan="' . count($fieldMappings) . '">&nbsp;</td></tr>'; // Baris kosong sebagai pemisah

// Header Kolom
echo '<tr>';
foreach ($fieldMappings as $label) {
    echo '<th class="header">' . htmlspecialchars($label) . '</th>';
}
echo '</tr>';

// Baris Data
echo '<tr>';
foreach ($fieldMappings as $field => $label) {
    $value = formatCellValue($row[$field] ?? '', $field);
    // Tambahkan style untuk wrap text pada sel yang mungkin multi-baris
    $style = in_array($field, ['ruanglingkup_kesepahaman', 'rencana_kolaborasi_kesepahaman', 'status_progres_kesepahaman', 'tindaklanjut_kesepahaman', 'ruanglingkup_pks', 'status_progres_pks', 'tindaklanjut_pks']) 
             ? 'style="white-space: pre-wrap;"' 
             : '';
    echo "<td {$style}>" . $value . "</td>";
}
echo '</tr>';

echo '</table></body></html>';

$stmt->close();
$conn->close();
?>