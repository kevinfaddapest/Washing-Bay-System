<?php
require 'vendor/autoload.php'; // PhpSpreadsheet autoload
include('config.php');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Get filters from POST
$search = $_POST['search'] ?? '';
$from_date = $_POST['from_date'] ?? '';
$to_date = $_POST['to_date'] ?? '';

$where = [];

if ($_SESSION['role']=='staff') {
    $where[] = "added_by='" . $conn->real_escape_string($_SESSION['user']) . "'";
}

if($search != '') {
    $s = $conn->real_escape_string($search);
    $where[] = "(customer_name LIKE '%$s%' OR vehicle_number LIKE '%$s%' OR service_type LIKE '%$s%')";
}

if($from_date != '') $where[] = "DATE(date) >= '$from_date'";
if($to_date != '') $where[] = "DATE(date) <= '$to_date'";

$whereSQL = $where ? 'WHERE '.implode(' AND ', $where) : '';

$result = $conn->query("SELECT * FROM services $whereSQL ORDER BY date DESC");

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Car Wash Records');

// Header row
$headers = ['ID','Customer','Contact','Vehicle No','Vehicle Type','Service','Price','Added By','Date'];
$sheet->fromArray($headers, NULL, 'A1');

// Data rows
$rowNum = 2;
while($row = $result->fetch_assoc()){
    $sheet->setCellValue("A$rowNum", $row['id']);
    $sheet->setCellValue("B$rowNum", $row['customer_name']);
    $sheet->setCellValue("C$rowNum", $row['contact']);
    $sheet->setCellValue("D$rowNum", $row['vehicle_number']);
    $sheet->setCellValue("E$rowNum", $row['vehicle_type']);
    $sheet->setCellValue("F$rowNum", $row['service_type']);
    $sheet->setCellValue("G$rowNum", $row['price']);
    $sheet->setCellValue("H$rowNum", $row['added_by']);
    $sheet->setCellValue("I$rowNum", $row['date']);
    $rowNum++;
}

// Output Excel file
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="carwash_records.xlsx"');
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
