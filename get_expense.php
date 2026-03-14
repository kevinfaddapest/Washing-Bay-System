<?php
include('config.php');
if (session_status() == PHP_SESSION_NONE) session_start();

// ✅ Check authorization
if (!isset($_SESSION['username'])) {
    echo json_encode(['error'=>true,'message'=>'Unauthorized']);
    exit;
}

// ✅ Only admins can view expenses
$role = strtolower($_SESSION['role'] ?? '');
if ($role !== 'admin') {
    echo json_encode(['error'=>true,'message'=>'Unauthorized — Admin only']);
    exit;
}

$user = $_SESSION['username'];
$user_id = $_SESSION['user_id'] ?? 0;

// ✅ Input validation
$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    echo json_encode(['error'=>true,'message'=>'Invalid expense ID']);
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

// ✅ Fetch expense
$stmt = $conn->prepare("SELECT * FROM expenses WHERE id=? LIMIT 1");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    logActivity($user_id, $user, "Failed Expense View", "Expense ID $id not found");
    echo json_encode(['error'=>true,'message'=>'Expense not found']);
    exit;
}

$expense = $result->fetch_assoc();
$stmt->close();

// ✅ Log successful view
logActivity($user_id, $user, "View Expense", "Viewed expense ID $id");

// ✅ Return expense
echo json_encode($expense);

$conn->close();
?>
