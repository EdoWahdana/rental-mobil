<?php 

require_once "../vendor/autoload.php";
include('includes/config.php');

// setlocale(LC_ALL, 'en_US');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

if(isset($_POST['submit'])) {

    // Ambil data bulan dan tahun
$bulan = $_POST['bulan'];
$tahun = $_POST['tahun'];
$periode = $tahun . "-" . $bulan . "%";
// SQL Query untuk periode
$query = "SELECT * FROM v_booking WHERE PostingDate LIKE '$periode'";
$prep = $dbh->prepare($query);
$prep->execute();
$result = $prep->fetchAll(PDO::FETCH_ASSOC);

$spreadsheet = new Spreadsheet();
$excel_writer = new Xlsx($spreadsheet);

$header = [
    'No.',
    'Nama', 
    'Email', 
    'No. Hp', 
    'Mobil', 
    'Harga per Hari', 
    'Tanggal Peminjaman', 
    'Tanggal Pengembalian', 
    'Pesan', 
    'Tanggal Pemesanan', 
    'Total Pembayaran'
];

$spreadsheet->setActiveSheetIndex(0);
$activeSheet = $spreadsheet->getActiveSheet()->fromArray($header);

// Set zoom level
$activeSheet->getSheetView()->setZoomScale(75);

// Set width column
$activeSheet->getDefaultColumnDimension()->setWidth(30);
$activeSheet->getColumnDimension('A')->setWidth(5);
$activeSheet->getColumnDimension('B')->setWidth(30);
$activeSheet->getColumnDimension('C')->setWidth(30);
$activeSheet->getColumnDimension('D')->setWidth(20);
$activeSheet->getColumnDimension('E')->setWidth(30);
$activeSheet->getColumnDimension('F')->setWidth(20);
$activeSheet->getColumnDimension('G')->setWidth(20);
$activeSheet->getColumnDimension('H')->setWidth(20);
$activeSheet->getColumnDimension('I')->setWidth(20);
$activeSheet->getColumnDimension('J')->setWidth(20);
$activeSheet->getColumnDimension('K')->setWidth(20);
$activeSheet->getColumnDimension('L')->setWidth(20);

// Style untuk header tabel
$headerStyle = [
    'font' => [
        'bold' => true
    ],
    'alignment' => [
        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
    ],
    'borders' => [
        'allborders' => [
            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
        ],
    ],
];
$activeSheet->getStyle('A1:L1')->applyFromArray($headerStyle);
// $activeSheet->getStyle('A1')->getAlignment()->setWrapText(TRUE);
// $activeSheet->getStyle('E1')->getAlignment()->setWrapText(TRUE);


if($prep->rowCount() > 0) {

    $i = 2;
	foreach ($result as $key => $value) {
		// Isi kolom tabel dengan data dari database
		$activeSheet->setCellValue('A'.$i, $i-1);
		$activeSheet->setCellValue('B'.$i, $value['FullName']);
		$activeSheet->setCellValue('C'.$i, $value['EmailId']);
		$activeSheet->setCellValue('D'.$i, $value['ContactNo']);
		$activeSheet->setCellValue('E'.$i, $value['VehiclesTitle']);
		$activeSheet->setCellValue('F'.$i, $value['PricePerDay']);
		$activeSheet->setCellValue('G'.$i, $value['FromDate']);
		$activeSheet->setCellValue('H'.$i, $value['ToDate']);
		$activeSheet->setCellValue('I'.$i, $value['message']);
		$activeSheet->setCellValue('J'.$i, $value['PostingDate']);
		$activeSheet->setCellValue('K'.$i, $value['TotalPay']);
		
		// Style Number Format
		$activeSheet->getStyle('F'.$i)->getNumberFormat()->setFormatCode('#,##0');
		$activeSheet->getStyle('K'.$i)->getNumberFormat()->setFormatCode('#,##0');
		
		$i++;
	}
}

$filename = "Laporan.xlsx";

header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename='. $filename);
header('Cache-Control: max-age=0');

ob_end_clean();
$excel_writer->save('php://output');

}