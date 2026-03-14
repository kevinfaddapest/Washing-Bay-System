<?php
include('config.php');
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['username'], $_SESSION['role'])) {
    echo json_encode(['error' => true, 'message' => 'Unauthorized']);
    exit;
}

$user     = $_SESSION['username'];
$user_id  = $_SESSION['user_id'] ?? 0;
$role     = $_SESSION['role'];

$id = intval($_POST['id'] ?? 0);
if ($id <= 0) {
    echo json_encode(['error' => true, 'message' => 'Invalid service ID']);
    exit;
}

// Fetch service (for permission + logging)
$stmt = $conn->prepare("SELECT * FROM services WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$service = $result->fetch_assoc();
$stmt->close();

if (!$service) {
    echo json_encode(['error' => true, 'message' => 'Service not found']);
    exit;
}

// Permission check
if ($role !== 'admin' && $service['added_by'] !== $user) {
    echo json_encode(['error' => true, 'message' => 'Access denied']);
    exit;
}

// Delete
$stmt = $conn->prepare("DELETE FROM services WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    logActivity(
        $user_id,
        $user,
        $role === 'admin' ? 'Admin Delete Service' : 'Delete Own Service',
        json_encode($service)
    );
    echo json_encode(['error' => false, 'message' => 'Service deleted successfully']);
} else {
    echo json_encode(['error' => true, 'message' => 'Delete failed']);
}

$stmt->close();
?>
