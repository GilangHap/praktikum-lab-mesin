<?php
require '../vendor/autoload.php'; // Autoload dari Composer
require 'includes/db.php'; // Koneksi ke database

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

// Membuat instance spreadsheet baru
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Menambahkan Header
$sheet->setCellValue('A1', 'ID');
$sheet->setCellValue('B1', 'Nama');
$sheet->setCellValue('C1', 'Email');
$sheet->setCellValue('D1', 'Status');
$sheet->setCellValue('E1', 'Komentar');

// Styling untuk header
$headerStyle = [
    'font' => [
        'bold' => true,
        'color' => ['argb' => 'FFFFFFFF'],
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['argb' => 'FF0073CF'],
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['argb' => 'FF000000'],
        ],
    ],
    'alignment' => [
        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
    ],
];
$sheet->getStyle('A1:E1')->applyFromArray($headerStyle);

// Mengambil filter dari parameter URL
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$sql = "SELECT id, nama, email, status, comment FROM pendaftaran";
if ($filter == 'acc') {
    $sql .= " WHERE status = 'acc'";
} elseif ($filter == 'rejected') {
    $sql .= " WHERE status = 'rejected'";
} elseif ($filter == 'none') {
    $sql .= " WHERE status IS NULL";
}
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $rowNumber = 2; // Baris dimulai dari 2 karena 1 untuk header
    while ($row = $result->fetch_assoc()) {
        $sheet->setCellValue('A' . $rowNumber, $row['id']);
        $sheet->setCellValue('B' . $rowNumber, $row['nama']);
        $sheet->setCellValue('C' . $rowNumber, $row['email']);
        $sheet->setCellValue('D' . $rowNumber, $row['status']);
        if ($row['status'] == 'rejected') {
            $sheet->setCellValue('E' . $rowNumber, $row['comment']);
        }

        // Styling untuk setiap baris data
        $sheet->getStyle('A' . $rowNumber . ':E' . $rowNumber)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ]);

        $rowNumber++;
    }
} else {
    $sheet->setCellValue('A2', 'Tidak ada data ditemukan.');
    $sheet->mergeCells('A2:E2');
    $sheet->getStyle('A2:E2')->applyFromArray([
        'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
        ],
        'font' => [
            'italic' => true,
        ],
    ]);
}

// Menutup koneksi database
$conn->close();

// Menyesuaikan lebar kolom secara otomatis
foreach (range('A', 'E') as $columnID) {
    $sheet->getColumnDimension($columnID)->setAutoSize(true);
}

// Menyimpan file spreadsheet
$writer = new Xlsx($spreadsheet);
$filename = 'Data_Pendaftaran.xlsx';

// Mengatur header untuk download file
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $filename . '"');
$writer->save('php://output');
exit();
?>
