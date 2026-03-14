<?php
include('config.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['username'])) {
    http_response_code(403);
    exit(json_encode(['error' => 'Unauthorized']));
}

// Get inputs
$search = $_GET['search'] ?? '';
$from_date = $_GET['from_date'] ?? '';
$to_date = $_GET['to_date'] ?? '';
$limit = $_GET['limit'] ?? 10;

$where = [];
$params = [];
$types = "";

// Restrict staff to their own records
if ($_SESSION['role'] === 'staff') {
    $where[] = "added_by = ?";
    $params[] = $_SESSION['username'];
    $types .= "s";
}

// Search filter
if (!empty($search)) {
    $search_like = '%' . $search . '%';
    $where[] = "("
        . "customer_name LIKE ? OR "
        . "contact LIKE ? OR "
        . "vehicle_number LIKE ? OR "
        . "vehicle_type LIKE ? OR "
        . "service_type LIKE ? OR "
        . "payment_status LIKE ? OR "
        . "added_by LIKE ? OR "
        . "date LIKE ?"
        . ")";
    // Add 8 parameters for the search
    for ($i = 0; $i < 8; $i++) {
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

$whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

// Handle limit
if (strtolower($limit) === "all") {
    $query = "SELECT * FROM services $whereSQL ORDER BY date DESC";
} else {
    $limit = (int)$limit;
    if ($limit <= 0) $limit = 10;
    $query = "SELECT * FROM services $whereSQL ORDER BY date DESC LIMIT ?";
    $params[] = $limit;
    $types .= "i";
}

// Prepare and execute statement
$stmt = $conn->prepare($query);
if ($types) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

$records = [];
$grand_total = 0;

while ($row = $result->fetch_assoc()) {
    $records[] = $row;
    $grand_total += (float)$row['price'];
}

header('Content-Type: application/json');
echo json_encode([
    'records' => $records,
    'grand_total' => $grand_total
]);
?>
