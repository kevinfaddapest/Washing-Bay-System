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

// Function to log activity (tracking PDF generation)
function logActivity($user_id, $username, $action, $details = '') {
    global $conn;
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
    $stmt = $conn->prepare("INSERT INTO activity_log (user_id, username, action, details, ip_address) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $user_id, $username, $action, $details, $ip_address);
    $stmt->execute();
    $stmt->close();
}

// ✅ Get filters
$search     = $_REQUEST['search'] ?? '';
$from_date  = $_REQUEST['from_date'] ?? '';
$to_date    = $_REQUEST['to_date'] ?? '';
$limit      = $_REQUEST['limit'] ?? 'All';

// ✅ Build WHERE clause
$where = [];
if ($_SESSION['role'] == 'staff') {
    $where[] = "username='" . $conn->real_escape_string($_SESSION['username']) . "'";
}

if ($search != '') {
    $s = $conn->real_escape_string($search);
    $where[] = "(username LIKE '%$s%' OR action LIKE '%$s%' OR details LIKE '%$s%' OR ip_address LIKE '%$s%' OR created_at LIKE '%$s%')";
}

if ($from_date != '') $where[] = "DATE(created_at) >= '$from_date'";
if ($to_date != '') $where[] = "DATE(created_at) <= '$to_date'";

$whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

// ✅ Limit
$limitSQL = '';
if (strtolower($limit) != 'all' && is_numeric($limit)) {
    $limitSQL = "LIMIT " . intval($limit);
}

// ✅ Fetch filtered data
$sql = "SELECT * FROM activity_log $whereSQL ORDER BY created_at DESC $limitSQL";
$result = $conn->query($sql);

// ✅ Total records
$totalRecords = $result ? $result->num_rows : 0;

// ✅ Log activity before PDF generation
$filters_used = "Search='$search', From='$from_date', To='$to_date', Limit='$limit'";
logActivity($user_id, $user, "Generate Activity PDF", "Generated activity PDF with filters: $filters_used, Total Records: $totalRecords");

// ✅ PDF setup
$pdf = new TCPDF();
$pdf->AddPage();
$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 10, 'Makanga & Partners Washing Bay - Activity Logs', 0, 1, 'C');
$pdf->SetFont('helvetica', '', 12);

$periodText = "All Records";
if ($from_date && $to_date) {
    $periodText = "Records from $from_date to $to_date";
} elseif ($from_date) {
    $periodText = "Records from $from_date onwards";
} elseif ($to_date) {
    $periodText = "Records up to $to_date";
}
$pdf->Cell(0, 8, $periodText, 0, 1, 'C');
$pdf->Ln(5);

// ✅ Table header
$pdf->SetFont('helvetica', '', 10);
$html = '<table border="1" cellpadding="4" width="100%">
<tr style="background-color:#0078D7;color:white;font-weight:bold;">
<th width="5%">#</th>
<th width="13%">Username</th>
<th width="20%">Action</th>
<th width="42%">Details</th>
<th width="12%">IP Address</th>
<th width="12%">Date/Time</th>
</tr>';

$sn = 1;
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $html .= '<tr>
            <td align="center">'.$sn.'</td>
            <td>'.htmlspecialchars($row['username']).'</td>
            <td>'.htmlspecialchars($row['action']).'</td>
            <td>'.htmlspecialchars($row['details']).'</td>
            <td align="center">'.htmlspecialchars($row['ip_address']).'</td>
            <td>'.htmlspecialchars($row['created_at']).'</td>
        </tr>';
        $sn++;
    }
} else {
    $html .= '<tr><td colspan="6" align="center">No records found</td></tr>';
}

$html .= '</table>';

$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('activity_logs.pdf', 'I');
exit;
?>
