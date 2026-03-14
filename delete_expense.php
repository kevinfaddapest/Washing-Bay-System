<?php
include('config.php');
if (session_status() === PHP_SESSION_NONE) session_start();

// Set JSON header
header('Content-Type: application/json; charset=utf-8');

// ✅ Check authorization
if (!isset($_SESSION['username'])) {
    echo json_encode(['error' => true, 'message' => 'Unauthorized']);
    exit;
}

// ✅ Only admins can delete
$role = strtolower($_SESSION['role'] ?? '');
if ($role !== 'admin') {
    echo json_encode(['error' => true, 'message' => 'Unauthorized — Admin only']);
    exit;
}

$user = $_SESSION['username'];
$user_id = $_SESSION['user_id'] ?? 0;

// ✅ Input data
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
if ($id <= 0) {
    echo json_encode(['error' => true, 'message' => 'Invalid expense ID']);
    exit;
}

// ✅ Function to log activity
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

// ✅ Fetch expense for logging
$stmt_fetch = $conn->prepare("SELECT * FROM expenses WHERE id = ?");
if (!$stmt_fetch) {
    echo json_encode(['error' => true, 'message' => 'Database error']);
    exit;
}
$stmt_fetch->bind_param("i", $id);
$stmt_fetch->execute();
$res = $stmt_fetch->get_result();
$expense = $res->fetch_assoc();
$stmt_fetch->close();

if (!$expense) {
    logActivity($user_id, $user, "Failed Expense Delete", "Expense ID $id not found");
    echo json_encode(['error' => true, 'message' => 'Expense not found']);
    exit;
}

// ✅ Delete expense
$stmt = $conn->prepare("DELETE FROM expenses WHERE id = ?");
if (!$stmt) {
    logActivity($user_id, $user, "Failed Expense Delete", "Prepare failed for deleting ID $id");
    echo json_encode(['error' => true, 'message' => 'Database error']);
    exit;
}

$stmt->bind_param("i", $id);
$success = $stmt->execute();
$affected = $stmt->affected_rows;
$stmt->close();

if ($success && $affected > 0) {
    logActivity($user_id, $user, "Delete Expense", "Deleted expense: " . json_encode($expense));
    echo json_encode(['error' => false, 'message' => 'Expense deleted successfully']);
} else {
    logActivity($user_id, $user, "Failed Expense Delete", "Failed to delete expense ID $id. Error: " . $conn->error);
    echo json_encode(['error' => true, 'message' => 'Failed to delete expense']);
}

$conn->close();
exit;
?>
