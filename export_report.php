<?php
require_once('tcpdf_min/tcpdf.php');
include('config.php');

if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

// Get filters from POST
$from_date = $_POST['from_date'] ?? '';
$to_date   = $_POST['to_date'] ?? '';

$where = [];

// Staff access control
if ($_SESSION['role'] == 'staff') {
    $where[] = "added_by='" . $conn->real_escape_string($_SESSION['user']) . "'";
}

// Date filters
if ($from_date != '') $where[] = "DATE(date) >= '$from_date'";
if ($to_date != '')   $where[] = "DATE(date) <= '$to_date'";

$whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

// Fetch records
$result = $conn->query("SELECT * FROM services $whereSQL ORDER BY date DESC");

// Calculate grand total
$totalResult = $conn->query("SELECT SUM(price) as grand_total FROM services $whereSQL");
$grand_total = $totalResult->fetch_assoc()['grand_total'] ?? 0;

// Create PDF
class CustomPDF extends TCPDF {

    // Header
    public function Header() {
        date_default_timezone_set('Africa/Kampala');
        $downloadDate = date('d M Y, H:i:s'); 

        // Car image (circular)
        $imgFile = 'assets/images/car wash7.jpg';
        if(file_exists($imgFile)){
            $x = 10; $y = 10; $size = 35;
            $this->SetFillColor(255,255,255);
            $this->Ellipse($x + $size/2, $y + $size/2, $size/2, $size/2, 0, 0, 360, 'F', [], []);
            $this->Image($imgFile, $x, $y, $size, $size, '', '', '', false, 300, '', false, false, 0, false, false, false);
        }
         
        // Right-aligned header text
        $headerHTML = '
        <div style="text-align:right; font-size:10px; line-height:1.4;">
            <strong style="font-size:18px; color:#0a58ca;">AUTO DETAIL</strong><br>
            Car Wash &amp; Auto Care Services<br>
            Entebbe Town – Katabi, Uganda<br>
            <strong>Tel:</strong> 0703414971, 0752668813, 0700667769<br>
            <strong>Email:</strong> makangaautocentre@gmail.com<br>
            <em>Downloaded on: '.$downloadDate.'</em>
        </div>
        <hr width="100%">
        ';
        $this->writeHTMLCell(0, 0, 55, 10, $headerHTML, 0, 0, false, true, 'R', true);
    }

    // Footer
    public function Footer() {
        $this->SetY(-15);
        $this->SetFont('helvetica','I',9);
        $pageText = 'AUTO DETAIL Car Wash & Auto Care Services - Page '.$this->getAliasNumPage().' of '.$this->getAliasNbPages();
        $this->Cell(0,10, $pageText, 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
}

// Instantiate PDF
$pdf = new CustomPDF();
$pdf->SetMargins(10, 50, 10);
$pdf->SetHeaderMargin(10);
$pdf->SetFooterMargin(20);
$pdf->SetAutoPageBreak(true, 25);
$pdf->AddPage();

// ===== Period Text =====
$pdf->SetFont('helvetica','',12);
$periodText = "All Records";
if($from_date && $to_date) $periodText = "Records from $from_date to $to_date";
elseif($from_date) $periodText = "Records from $from_date onwards";
elseif($to_date) $periodText = "Records up to $to_date";
$pdf->Cell(0,8,$periodText,0,1,'C');
$pdf->Ln(5);

// ===== Services Table =====
$pdf->SetFont('helvetica','B',12);
$pdf->Cell(0,7,'Report',0,1);
$pdf->SetFont('helvetica','',10);

$html = '<table border="1" cellpadding="4">
<tr style="background-color:#0078D7;color:white;">
<th>S.No</th><th>Customer</th><th>Contact</th><th>Vehicle No</th>
<th>Vehicle Type</th><th>Service</th><th>Price</th><th>Payment Status</th><th>Added By</th>
</tr>';

// Table Data
$sn = 1; // Initialize serial number
while($row = $result->fetch_assoc()){
    $html .= '<tr>
        <td>'.$sn.'</td>
        <td>'.htmlspecialchars($row['customer_name']).'</td>
        <td>'.htmlspecialchars($row['contact']).'</td>
        <td>'.htmlspecialchars($row['vehicle_number']).'</td>
        <td>'.htmlspecialchars($row['vehicle_type']).'</td>
        <td>'.htmlspecialchars($row['service_type']).'</td>
        <td>'.number_format($row['price'],2).'</td>
	<td>'.htmlspecialchars($row['payment_status']).'</td>
        <td>'.htmlspecialchars($row['added_by']).'</td>
    </tr>';
    $sn++; // Increment serial number
}

// Add grand total row
$html .= '<tr style="font-weight:bold; background-color:#f0f0f0;">
    <td colspan="6" align="right">Grand Total</td>
    <td>'.number_format($grand_total,2).'</td>
    <td></td>
</tr>';

$html .= '</table>';

// Write HTML to PDF
$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('carwash_records.pdf', 'D');
exit;
?>
