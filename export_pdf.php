<?php
include('config.php'); 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once('tcpdf_min/tcpdf.php');
error_reporting(E_ERROR | E_PARSE);

// ✅ User info
$user = $_SESSION['username'] ?? 'Unknown';
$user_id = $_SESSION['user_id'] ?? 0;

// Function to log activity
function logActivity($user_id, $username, $action, $details = '') {
    global $conn;
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
    $stmt = $conn->prepare("INSERT INTO activity_log (user_id, username, action, details, ip_address) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $user_id, $username, $action, $details, $ip_address);
    $stmt->execute();
    $stmt->close();
}

// ✅ Get filters (support GET/POST)
$search     = $_REQUEST['search'] ?? '';
$from_date  = $_REQUEST['from_date'] ?? '';
$to_date    = $_REQUEST['to_date'] ?? '';
$limit      = $_REQUEST['limit'] ?? 'All';

// ✅ Build WHERE clause
$where = [];
if ($_SESSION['role'] == 'staff') {
    $where[] = "added_by='" . $conn->real_escape_string($_SESSION['user']) . "'";
}

if ($search != '') {
    $s = $conn->real_escape_string($search);
    $where[] = "(customer_name LIKE '%$s%' OR vehicle_number LIKE '%$s%' OR vehicle_type LIKE '%$s%' OR service_type LIKE '%$s%' OR payment_status LIKE '%$s%' OR added_by LIKE '%$s%' OR date LIKE '%$s%')";
}

if ($from_date != '') $where[] = "DATE(date) >= '$from_date'";
if ($to_date != '') $where[] = "DATE(date) <= '$to_date'";

$whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$limitSQL = '';
if (strtolower($limit) != 'all' && is_numeric($limit)) {
    $limitSQL = "LIMIT " . intval($limit);
}

// ✅ Fetch filtered data
$sql = "SELECT * FROM services $whereSQL ORDER BY date DESC $limitSQL";
$result = $conn->query($sql);

// ✅ Calculate filtered grand total
$totalResult = $conn->query("SELECT SUM(price) AS grand_total FROM services $whereSQL");
$grand_total = $totalResult->fetch_assoc()['grand_total'] ?? 0;

// ✅ Log activity before PDF generation
$filters_used = "Search='$search', From='$from_date', To='$to_date', Limit='$limit'";
$records_count = $result ? $result->num_rows : 0;
logActivity($user_id, $user, "Generate Service PDF", "Generated service PDF with filters: $filters_used, Total Records: $records_count");

// ✅ PDF setup
class CustomPDF extends TCPDF {

    // Header
     public function Header() {
        if ($this->page != 1) return;
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
        <div style="text-align:right; font-size:10px; line-height:1.9;">
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
$pdf->SetMargins(10, 40, 10);
$pdf->SetHeaderMargin(5);
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
$pdf->Cell(0,7,'Services',0,1);
$pdf->SetFont('helvetica','',10);

$html = '<table border="1" cellpadding="4">
<tr style="background-color:#0078D7;color:white;font-weight:bold;">
<th width="5%">#</th>
<th width="12%">Handled By</th>
<th width="12%">Contact</th>
<th width="11%">Vehicle No</th>
<th width="12%">Type</th>
<th width="14%">Service</th>
<th width="8%">Price</th>
<th width="11%">Pay Status</th>
<th width="13%">Added By</th>
</tr>';

$sn = 1;
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $html .= '<tr>
            <td align="center">'.$sn.'</td>
            <td>'.htmlspecialchars($row['customer_name']).'</td>
            <td>'.htmlspecialchars($row['contact']).'</td>
            <td>'.htmlspecialchars($row['vehicle_number']).'</td>
            <td>'.htmlspecialchars($row['vehicle_type']).'</td>
            <td>'.htmlspecialchars($row['service_type']).'</td>
            <td align="right">'.number_format($row['price']).'</td>
            <td align="center">'.htmlspecialchars($row['payment_status']).'</td>
            <td>'.htmlspecialchars($row['added_by']).'</td>
        </tr>';
        $sn++;
    }
} else {
    $html .= '<tr><td colspan="9" align="center">No records found</td></tr>';
}

$html .= '<tr style="font-weight:bold;background-color:#f0f0f0;">
    <td colspan="6" align="right">Grand Total</td>
    <td align="center" colspan="2">'.number_format($grand_total).'</td>
    <td colspan="1"></td>
</tr>';
$html .= '</table>';

$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('carwash_services.pdf', 'I');
exit;
?>
