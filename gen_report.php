<?php
include('config.php'); 
require_once('tcpdf_min/tcpdf.php');
error_reporting(E_ERROR | E_PARSE);

if (session_status() === PHP_SESSION_NONE) session_start();

// Authorization//
if (!isset($_SESSION['username'])) {
    http_response_code(403);
    exit(json_encode(['error'=>'Unauthorized']));
}

$user = $_SESSION['username'];
$user_id = $_SESSION['user_id'] ?? 0;
$role = strtolower($_SESSION['role'] ?? 'staff');

// Activity logging//
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

// Get filters//
$search     = $_REQUEST['search'] ?? '';
$from_date  = $_REQUEST['from_date'] ?? '';
$to_date    = $_REQUEST['to_date'] ?? '';
$limit      = $_REQUEST['limit'] ?? 'All';

// ===== Services Query =====//
$whereService = [];
$paramsService = [];
$typesService = "";

// Staff restriction//
if ($role === 'staff') {
    $whereService[] = "added_by = ?";
    $paramsService[] = $user;
    $typesService .= "s";
}

// Search filter//
if (!empty($search)) {
    $whereService[] = "(customer_name LIKE ? OR vehicle_number LIKE ? OR vehicle_type LIKE ? OR service_type LIKE ?)";
    $search_like = "%$search%";
    for ($i=0; $i<4; $i++){ $paramsService[] = $search_like; $typesService .= "s"; }
}

// Date filters//
if (!empty($from_date)) { $whereService[] = "DATE(date) >= ?"; $paramsService[] = $from_date; $typesService .= "s"; }
if (!empty($to_date)) { $whereService[] = "DATE(date) <= ?"; $paramsService[] = $to_date; $typesService .= "s"; }

$whereSQLService = $whereService ? "WHERE ".implode(" AND ", $whereService) : "";
$limitSQLService = "";
if (strtolower($limit)!=='all' && is_numeric($limit)) { $limitSQLService = "LIMIT ?"; $paramsService[] = (int)$limit; $typesService .= "i"; }

$sqlService = "SELECT * FROM services $whereSQLService ORDER BY date DESC $limitSQLService";
$stmtService = $conn->prepare($sqlService);
if($typesService) { $stmtService->bind_param($typesService, ...$paramsService); }
$stmtService->execute();
$resultService = $stmtService->get_result();
$services = [];
$totalServices = 0;
while($row = $resultService->fetch_assoc()){ 
    $services[] = $row; 
    $totalServices += (float)$row['price']; 
}

// ===== Expenses Query =====//
$whereExpense = [];
$paramsExpense = [];
$typesExpense = "";

// Staff restriction//
if ($role === 'staff') {
    $whereExpense[] = "added_by = ?";
    $paramsExpense[] = $user;
    $typesExpense .= "s";
}

// Search filter//
if (!empty($search)) {
    $whereExpense[] = "(expense_name LIKE ? OR category LIKE ? OR added_by LIKE ?)";
    $search_like = "%$search%";
    for ($i=0; $i<3; $i++){ $paramsExpense[] = $search_like; $typesExpense .= "s"; }
}

// Date filters//
if (!empty($from_date)) { $whereExpense[] = "DATE(date) >= ?"; $paramsExpense[] = $from_date; $typesExpense .= "s"; }
if (!empty($to_date)) { $whereExpense[] = "DATE(date) <= ?"; $paramsExpense[] = $to_date; $typesExpense .= "s"; }

$whereSQLExpense = $whereExpense ? "WHERE ".implode(" AND ", $whereExpense) : "";
$limitSQLExpense = "";
if (strtolower($limit)!=='all' && is_numeric($limit)) { $limitSQLExpense = "LIMIT ?"; $paramsExpense[] = (int)$limit; $typesExpense .= "i"; }

$sqlExpense = "SELECT * FROM expenses $whereSQLExpense ORDER BY date DESC $limitSQLExpense";
$stmtExpense = $conn->prepare($sqlExpense);
if($typesExpense) { $stmtExpense->bind_param($typesExpense, ...$paramsExpense); }
$stmtExpense->execute();
$resultExpense = $stmtExpense->get_result();
$expenses = [];
$totalExpenses = 0;
while($row = $resultExpense->fetch_assoc()){ 
    $expenses[] = $row; 
    $totalExpenses += (float)$row['amount']; 
}

// Log activity//
$filters_used = "Search='$search', From='$from_date', To='$to_date', Limit='$limit'";
logActivity($user_id, $user, "Generate General Report PDF", "Filters: $filters_used, Services: ".count($services).", Expenses: ".count($expenses));

// ===== PDF Generation =====//
class CustomPDF extends TCPDF {

    // Header//
    public function Header() {
        if ($this->page != 1) return;
        date_default_timezone_set('Africa/Kampala');
        $downloadDate = date('d M Y, H:i:s'); 

        // Car image (circular)//
        $imgFile = 'assets/images/car wash7.jpg';
        if(file_exists($imgFile)){
            $x = 10; $y = 10; $size = 35;
            $this->SetFillColor(255,255,255);
            $this->Ellipse($x + $size/2, $y + $size/2, $size/2, $size/2, 0, 0, 360, 'F', [], []);
            $this->Image($imgFile, $x, $y, $size, $size, '', '', '', false, 300, '', false, false, 0, false, false, false);
        }
         
        // Right-aligned header text//
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
        $this->writeHTMLCell(0, 0, 20, 10, $headerHTML, 0, 0, false, true, 'R', true);

       $this->Ln(5);
    }

    // Footer//
    public function Footer() {
        $this->SetY(-15);
        $this->SetFont('helvetica','I',9);
        $pageText = 'AUTO DETAIL Car Wash & Auto Care Services - Page '.$this->getAliasNumPage().' of '.$this->getAliasNbPages();
        $this->Cell(0,10, $pageText, 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
}

// Instantiate PDF//
$pdf = new CustomPDF();
$pdf->SetMargins(10, 40, 10);
$pdf->SetHeaderMargin(5);
$pdf->SetFooterMargin(20);
$pdf->SetAutoPageBreak(true, 25);
$pdf->AddPage();

// ===== Period Text =====//
$pdf->SetFont('helvetica','',12);
$periodText = "All Records";
if($from_date && $to_date) $periodText = "Records from $from_date to $to_date";
elseif($from_date) $periodText = "Records from $from_date onwards";
elseif($to_date) $periodText = "Records up to $to_date";
$pdf->Cell(0,8,$periodText,0,1,'C');
$pdf->Ln(5);

// ===== Services Table =====//
$pdf->SetFont('helvetica','B',12);
$pdf->Cell(0,7,'Services',0,1);
$pdf->SetFont('helvetica','',10);


$html = '<table border="1" cellpadding="4">
<tr style="background-color:#0078D7;color:white;font-weight:bold;">
<th width="5%">#</th>
<th width="15%">Customer</th>
<th width="13%">Vehicle No</th>
<th width="17%">Vehicle Type</th>
<th width="15%">Service</th>
<th width="13%">Price (UGX)</th>
<th width="20%">Date</th>
</tr>';

$sn = 1;
if($services){
    foreach($services as $r){
        $html .= '<tr>
            <td align="center">'.$sn.'</td>
            <td>'.htmlspecialchars($r['customer_name']).'</td>
            <td>'.htmlspecialchars($r['vehicle_number']).'</td>
            <td>'.htmlspecialchars($r['vehicle_type']).'</td>
            <td>'.htmlspecialchars($r['service_type']).'</td>
            <td align="right">'.number_format($r['price']).'</td>
            <td>'.htmlspecialchars($r['date']).'</td>
        </tr>';
        $sn++;
    }
}else{
    $html .= '<tr><td colspan="7" align="center">No records found</td></tr>';
}

$html .= '<tr style="font-weight:bold;background-color:#f0f0f0;">
    <td colspan="5" align="center">Total Services</td>
    <td align="right">'.number_format($totalServices).'</td>
    <td></td>
</tr>';
$html .= '</table>';
$pdf->writeHTML($html,true,false,true,false,'');

// ===== Expenses Table =====//
$pdf->Ln(5);
$pdf->SetFont('helvetica','B',12);
$pdf->Cell(0,7,'Expenses',0,1);
$pdf->SetFont('helvetica','',10);

$html = '<table border="1" cellpadding="4">
<tr style="background-color:#dc3545;color:white;font-weight:bold;">
<th width="5%">#</th>
<th width="35%">Expense Name</th>
<th width="30%">Category</th>
<th width="15%">Amount (UGX)</th>
<th width="15%">Date</th>
</tr>';

$sn = 1;
if($expenses){
    foreach($expenses as $r){
        $html .= '<tr>
            <td align="center">'.$sn.'</td>
            <td>'.htmlspecialchars($r['expense_name']).'</td>
            <td>'.htmlspecialchars($r['category']).'</td>
            <td align="right">'.number_format($r['amount']).'</td>
            <td>'.htmlspecialchars($r['date']).'</td>
        </tr>';
        $sn++;
    }
}else{
    $html .= '<tr><td colspan="5" align="center">No records found</td></tr>';
}

$html .= '<tr style="font-weight:bold;background-color:#f0f0f0;">
    <td colspan="3" align="center">Total Expenses</td>
    <td align="right">'.number_format($totalExpenses).'</td>
    <td></td>
</tr>';
$html .= '</table>';
$pdf->writeHTML($html,true,false,true,false,'');

// ===== Summary =====//
$pdf->Ln(5);
$pdf->SetFont('helvetica','B',12);
$pdf->Cell(0,7,'Summary',0,1);
$pdf->SetFont('helvetica','',11);

$netProfit = $totalServices - $totalExpenses;
$partner1Share = $netProfit * 0.6; 
$partner2Share = $netProfit * 0.4; 

$html = '<table border="1" cellpadding="4">
<tr><td>Total Services Amount</td><td align="right">'.number_format($totalServices).'</td></tr>
<tr><td>Total Expenses Amount</td><td align="right">'.number_format($totalExpenses).'</td></tr>
<tr><td>Net Profit (Services - Expenses)</td><td align="right">'.number_format($netProfit).'</td></tr>
<tr><td>Number of Vehicles</td><td align="right">'.count($services).'</td></tr>
<tr><td>Number of Expenses</td><td align="right">'.count($expenses).'</td></tr>
<tr><td style="font-weight:bold;">Mr Derrick Makanga (60%)</td><td align="right">'.number_format($partner1Share).'</td></tr>
<tr><td style="font-weight:bold;">Mr Frank Kato (40%)</td><td align="right">'.number_format($partner2Share).'</td></tr>
</table>';

$pdf->writeHTML($html,true,false,true,false,'');

$pdf->Output('general_report.pdf','I');
$conn->close();
exit;
?>
