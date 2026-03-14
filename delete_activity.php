<?php
include('config.php');
if (session_status() === PHP_SESSION_NONE) session_start();

// ✅ Check login
if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized: Please login.']);
    exit;
}

// ✅ Check role
if ($_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Permission denied: Only admins can delete logs.']);
    exit;
}

// ✅ Get ID
$id = $_POST['id'] ?? 0;
$id = intval($id);

if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid log ID.']);
    exit;
}

// ✅ Fetch log content before deletion
$logRow = $conn->query("SELECT * FROM activity_log WHERE id = $id")->fetch_assoc();
if (!$logRow) {
    echo json_encode(['success' => false, 'message' => 'Log entry not found.']);
    exit;
}

// ✅ Delete the record
$stmt = $conn->prepare("DELETE FROM activity_log WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {

    // ✅ Log deletion action with full content
    $user_id = $_SESSION['user_id'] ?? 0;
    $username = $_SESSION['username'] ?? 'Unknown';
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';

    $action = "Deleted Activity Log";
    $details = "Deleted log ID: $id | Full content: " . json_encode($logRow);

    $logStmt = $conn->prepare("INSERT INTO activity_log (user_id, username, action, details, ip_address) VALUES (?, ?, ?, ?, ?)");
    $logStmt->bind_param("issss", $user_id, $username, $action, $details, $ip_address);
    $logStmt->execute();
    $logStmt->close();

    echo json_encode(['success' => true, 'message' => 'Activity log deleted successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete the activity log.']);
}

$stmt->close();
?>
