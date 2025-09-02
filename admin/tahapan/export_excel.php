<?php
include "db.php";

// Security validation
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    die("Error: Invalid ID parameter");
}

// Get data with prepared statement for security
$stmt = $conn->prepare("SELECT * FROM tahapan_kerjasama WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if (!$row) {
    die("Error: Data not found");
}

// Sanitize filename
$sanitized_name = preg_replace('/[^a-zA-Z0-9_-]/', '_', $row['nama_mitra']);
$filename = "mitra_" . $sanitized_name . "_" . $row['id'] . "_" . date('Y-m-d') . ".xls";

// Set headers for Excel download
header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Pragma: no-cache");
header("Expires: 0");

// Define field mappings with proper labels
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

// Function to format cell value
function formatCellValue($value, $field) {
    if ($value === null || $value === '') {
        return '-';
    }
    
    // Special formatting for specific fields
    switch ($field) {
        case 'tandai':
            return $value == 1 ? 'Ya' : 'Tidak';
        case 'tanggal_kesepahaman':
        case 'tanggal_pks':
        case 'rencana_pertemuan_kesepahaman':
        case 'rencana_pertemuan_pks':
            return $value ? date('d-m-Y', strtotime($value)) : '-';
        default:
            return htmlspecialchars($value);
    }
}

// Start Excel content
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>Data Mitra</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->
    <style>
        .header { background-color: #2c5aa0; color: white; font-weight: bold; text-align: center; padding: 8px; }
        .data { padding: 5px; border: 1px solid #ccc; vertical-align: top; }
        .priority { background-color: #fff3cd; }
        .date { text-align: center; }
        .text { text-align: left; }
        .center { text-align: center; }
    </style>
</head>
<body>
    <table border="1" style="border-collapse: collapse; width: 100%; font-family: Arial, sans-serif; font-size: 10px;">
        <!-- Title Row -->
        <tr>
            <td colspan="<?= count($fieldMappings); ?>" class="header" style="font-size: 14px; padding: 15px;">
                DETAIL DATA MITRA: <?= strtoupper(htmlspecialchars($row['nama_mitra'])); ?>
                <br><small>Exported on: <?= date('d F Y H:i:s'); ?></small>
            </td>
        </tr>
        
        <!-- Header Row -->
        <tr>
            <?php foreach ($fieldMappings as $field => $label): ?>
                <th class="header"><?= htmlspecialchars($label); ?></th>
            <?php endforeach; ?>
        </tr>
        
        <!-- Data Row -->
        <tr <?= ($row['tandai'] == 1) ? 'class="priority"' : ''; ?>>
            <?php foreach ($fieldMappings as $field => $label): ?>
                <?php 
                $value = formatCellValue($row[$field] ?? '', $field);
                $cellClass = 'data';
                
                // Add specific classes based on field type
                if (strpos($field, 'tanggal') !== false || strpos($field, 'rencana_pertemuan') !== false) {
                    $cellClass .= ' date';
                } elseif (in_array($field, ['id', 'tandai'])) {
                    $cellClass .= ' center';
                } else {
                    $cellClass .= ' text';
                }
                ?>
                <td class="<?= $cellClass; ?>"><?= $value; ?></td>
            <?php endforeach; ?>
        </tr>
        
        <!-- Summary Information -->
        <tr>
            <td colspan="<?= count($fieldMappings); ?>" style="padding: 10px; background-color: #f8f9fa; font-size: 9px;">
                <strong>Informasi Export:</strong><br>
                • Data diekspor pada: <?= date('d F Y H:i:s'); ?><br>
                • Total field: <?= count($fieldMappings); ?><br>
                • Status Prioritas: <?= ($row['tandai'] == 1) ? 'Ya (Ditandai)' : 'Tidak'; ?><br>
                • ID Record: <?= $row['id']; ?>
            </td>
        </tr>
    </table>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>