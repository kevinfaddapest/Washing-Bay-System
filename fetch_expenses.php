<?php
include('config.php');

if (session_status() === PHP_SESSION_NONE) session_start();

// Authorization check
if (!isset($_SESSION['username'])) {
    http_response_code(403);
    exit(json_encode(['error' => 'Unauthorized']));
}

$user = $_SESSION['username'];
$user_id = $_SESSION['user_id'] ?? 0;
$role = strtolower($_SESSION['role'] ?? 'staff');

// Function to log activity
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

// Get inputs
$search = trim($_GET['search'] ?? '');
$from_date = trim($_GET['from_date'] ?? '');
$to_date = trim($_GET['to_date'] ?? '');
$limit = trim($_GET['limit'] ?? 10);

$where = [];
$params = [];
$types = "";

// Staff restriction: only see own records
if ($role === 'staff') {
    $where[] = "added_by = ?";
    $params[] = $user;
    $types .= "s";
}

// Search filter
if ($search !== '') {
    $search_like = "%$search%";
    $where[] = "(expense_name LIKE ? OR category LIKE ? OR added_by LIKE ? OR date LIKE ?)";
    for ($i = 0; $i < 4; $i++) {
        $params[] = $search_like;
        $types .= "s";
    }
}

// Date filters (ensure valid YYYY-MM-DD)
if ($from_date && preg_match('/^\d{4}-\d{2}-\d{2}$/', $from_date)) {
    $where[] = "DATE(date) >= ?";
    $params[] = $from_date;
    $types .= "s";
}
if ($to_date && preg_match('/^\d{4}-\d{2}-\d{2}$/', $to_date)) {
    $where[] = "DATE(date) <= ?";
    $params[] = $to_date;
    $types .= "s";
}

$whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$query = "SELECT * FROM expenses $whereSQL ORDER BY date DESC";

// Handle limit
$limitSQL = '';
if (strtolower($limit) !== 'all') {
    $limit = (int)$limit;
    if ($limit <= 0) $limit = 10;
    $limitSQL = " LIMIT $limit"; // ✅ safe since cast to int
}
$query .= $limitSQL;

// Prepare statement
$stmt = $conn->prepare($query);
if ($stmt && $params) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

// Process results
$records = [];
$grand_total = 0;
while ($row = $result->fetch_assoc()) {
    $records[] = [
        'id'           => $row['id'],
        'expense_name' => $row['expense_name'],
        'category'     => $row['category'],
        'amount'       => $row['amount'],
        'added_by'     => $row['added_by'],
        'date'         => $row['date'],
        'receipt_number'  => $row['receipt_number'],
        'created_at'   => date('d-m-Y H:i', strtotime($row['created_at']))
    ];
    $grand_total += (float)$row['amount'];
}

// Log activity
logActivity($user_id, $user, "View Expenses", "Fetched " . count($records) . " records");

// Return JSON
header('Content-Type: application/json');
echo json_encode([
    'records' => $records,
    'grand_total' => number_format($grand_total, 2)
]);

$stmt->close();
$conn->close();
exit;
