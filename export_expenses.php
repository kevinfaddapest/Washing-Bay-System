<?php
include('config.php'); 
require_once('tcpdf_min/tcpdf.php');
error_reporting(E_ERROR | E_PARSE);

if (session_status() === PHP_SESSION_NONE) session_start();

// Authorization
if (!isset($_SESSION['username'])) {
    http_response_code(403);
    exit(json_encode(['error'=>'Unauthorized']));
}

$user = $_SESSION['username'];
$user_id = $_SESSION['user_id'] ?? 0;
$role = strtolower($_SESSION['role'] ?? 'staff');

// Activity logging
function logActivity($user_id, $username, $action, $details = '') {
    global $conn;
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
    $stmt = $conn->prepare("INSERT INTO activity_log (user_id, username, action, details, ip_address) VALUES (?, ?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("issss", $user_id, $username, $action, $details, $ip_address);
        $stmt->execute();
        $stmt->close();
    }
}

// Get filters
$search     = $_REQUEST['search'] ?? '';
$from_date  = $_REQUEST['from_date'] ?? '';
$to_date    = $_REQUEST['to_date'] ?? '';
$limit      = $_REQUEST['limit'] ?? 'All';

$where = [];
$params = [];
$types = "";

// Staff restriction
if ($role === 'staff') {
    $where[] = "added_by = ?";
    $params[] = $user;
    $types .= "s";
}

// Search filter
if (!empty($search)) {
    $where[] = "(expense_name LIKE ? OR category LIKE ? OR added_by LIKE ? OR date LIKE ?)";
    $search_like = "%$search%";
    for ($i = 0; $i < 4; $i++) {
        $params[] = $search_like;
        $types .= "s";
    }
}

// Date filters
if (!empty($from_date)) {
    $where[] = "DATE(date) >= ?";
    $params[] = $from_date;
    $types .= "s";
}
if (!empty($to_date)) {
    $where[] = "DATE(date) <= ?";
    $params[] = $to_date;
    $types .= "s";
}

$whereSQL = $where ? "WHERE ".implode(" AND ", $where) : "";
$limitSQL = "";
if (strtolower($limit) !== 'all' && is_numeric($limit)) {
    $limitSQL = "LIMIT ?";
    $params[] = (int)$limit;
    $types .= "i";
}

// Prepare query
$sql = "SELECT * FROM expenses $whereSQL ORDER BY date DESC $limitSQL";
$stmt = $conn->prepare($sql);
if ($types) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// Fetch data
$records = [];
$grand_total = 0;
while ($row = $result->fetch_assoc()) {
    $records[] = $row;
    $grand_total += (float)$row['amount'];
}

// Log activity
$filters_used = "Search='$search', From='$from_date', To='$to_date', Limit='$limit'";
logActivity($user_id, $user, "Generate Expense PDF", "Generated expense PDF with filters: $filters_used, Total Records: ".count($records));

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
$pdf->Cell(0,7,'Expenses',0,1);
$pdf->SetFont('helvetica','',10);

$html = '<table border="1" cellpadding="4">
<tr style="background-color:#0078D7;color:white;font-weight:bold;">
<th width="5%">#</th>
<th width="35%">Expense Name</th>
<th width="15%">Category</th>
<th width="15%">Amount (UGX)</th>
<th width="16%">Added By</th>
<th width="16%">Date</th>
</tr>';

$sn = 1;
if ($records) {
    foreach ($records as $row) {
        $html .= '<tr>
            <td align="center">'.$sn.'</td>
            <td>'.htmlspecialchars($row['expense_name']).'</td>
            <td>'.htmlspecialchars($row['category']).'</td>
            <td align="right">'.number_format($row['amount']).'</td>
            <td>'.htmlspecialchars($row['added_by']).'</td>
            <td>'.htmlspecialchars($row['date']).'</td>
        </tr>';
        $sn++;
    }
} else {
    $html .= '<tr><td colspan="6" align="center">No records found</td></tr>';
}

$html .= '<tr style="font-weight:bold;background-color:#f0f0f0;">
    <td colspan="3" align="right">Grand Total</td>
    <td align="right">'.number_format($grand_total).'</td>
    <td colspan="2"></td>
</tr>';
$html .= '</table>';

$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('carwash_expenses.pdf', 'I');

$conn->close();
exit;
?>
