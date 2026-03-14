<?php
include('config.php');
if (session_status() === PHP_SESSION_NONE) session_start();

// FIXED: use username instead of user
if (!isset($_SESSION['username'])) exit;

$search = $_GET['search'] ?? '';
$from_date = $_GET['from_date'] ?? '';
$to_date = $_GET['to_date'] ?? '';
$limit = $_GET['limit'] ?? 10;

$where = [];

// Restrict staff to their own logs
if ($_SESSION['role'] === 'staff') {
    $where[] = "username='" . $conn->real_escape_string($_SESSION['username']) . "'";
}

// 🔍 Search Filter
if ($search !== '') {
    $s = $conn->real_escape_string($search);
    $where[] = "(
        username LIKE '%$s%' OR
        action LIKE '%$s%' OR
        details LIKE '%$s%' OR
        ip_address LIKE '%$s%' OR
        created_at LIKE '%$s%'
    )";
}

// 📅 Date Filters
if ($from_date !== '') $where[] = "DATE(created_at) >= '$from_date'";
if ($to_date !== '') $where[] = "DATE(created_at) <= '$to_date'";

$whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

if (strtolower($limit) === 'all') {
    $query = "
        SELECT id, user_id, username, action, details, ip_address, created_at 
        FROM activity_log 
        $whereSQL 
        ORDER BY created_at DESC
    ";
} else {
    $limit = (int)$limit;
    $query = "
        SELECT id, user_id, username, action, details, ip_address, created_at 
        FROM activity_log 
        $whereSQL 
        ORDER BY created_at DESC 
        LIMIT $limit
    ";
}

$res = $conn->query($query);
$records = [];

if ($res) {
    while ($row = $res->fetch_assoc()) {
        $records[] = $row;
    }
}

echo json_encode(['records' => $records]);
?>
