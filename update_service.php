<?php
include('config.php');
if (session_status() == PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$user = $_SESSION['username'];
$user_id = $_SESSION['user_id'] ?? 0;

$data = $_POST;
$id = (int)($data['id'] ?? 0);

if (!$id) {
    header("Location: view_services.php?error=invalid_id");
    exit;
}

$customer_name  = trim($data['customer_name'] ?? '');
$contact        = trim($data['contact'] ?? '');
$vehicle_number = trim($data['vehicle_number'] ?? '');
$vehicle_type   = trim($data['vehicle_type'] ?? '');
$service_type   = trim($data['service_type'] ?? '');
$price          = (float)($data['price'] ?? 0);
$payment_status = trim($data['payment_status'] ?? '');

// Activity logging function
function logActivity($user_id, $username, $action, $details = '') {
    global $conn;
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
    $stmt = $conn->prepare("INSERT INTO activity_log (user_id, username, action, details, ip_address) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $user_id, $username, $action, $details, $ip_address);
    $stmt->execute();
    $stmt->close();
}

// Prepare update
if ($_SESSION['role'] === 'admin') {
    $stmt = $conn->prepare("
        UPDATE services 
        SET customer_name=?, contact=?, vehicle_number=?, vehicle_type=?, 
            service_type=?, price=?, payment_status=? 
        WHERE id=?
    ");
    $stmt->bind_param(
        "sssssdsi",
        $customer_name,
        $contact,
        $vehicle_number,
        $vehicle_type,
        $service_type,
        $price,
        $payment_status,
        $id
    );
    $action = "Update Service";
} else {
    $stmt = $conn->prepare("
        UPDATE services 
        SET customer_name=?, contact=?, vehicle_number=?, vehicle_type=?, 
            service_type=?, price=?, payment_status=? 
        WHERE id=? AND added_by=?
    ");
    $stmt->bind_param(
        "sssssdssi",
        $customer_name,
        $contact,
        $vehicle_number,
        $vehicle_type,
        $service_type,
        $price,
        $payment_status,
        $id,
        $user
    );
    $action = "Update Own Service";
}

// Execute and log
if ($stmt->execute() && $stmt->affected_rows > 0) {
    $details = "Updated service ID $id: customer='$customer_name', vehicle='$vehicle_number', service='$service_type', price=$price, payment_status='$payment_status'";
    logActivity($user_id, $user, $action, $details);

    header("Location: view_services.php?success=1");
    exit;
} else {
    $error_detail = $stmt->error ?: "No rows affected (access denied or ID not found)";
    logActivity($user_id, $user, "Failed $action", "Failed to update service ID $id. Details: $error_detail");

    header("Location: view_services.php?error=access_denied");
    exit;
}

$stmt->close();
$conn->close();
?>
