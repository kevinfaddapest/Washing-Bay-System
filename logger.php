<?php
// logger.php
function logActivity($user_id, $username, $action, $details = null) {
    // Database connection
    $conn = new mysqli('localhost', 'root', '', 'carwash_db');
    if ($conn->connect_error) {
        die("Database connection failed: " . $conn->connect_error);
    }

    $ip = $_SERVER['REMOTE_ADDR'];
    $stmt = $conn->prepare("INSERT INTO activity_log (user_id, username, action, details, ip_address) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $user_id, $username, $action, $details, $ip);
    $stmt->execute();
    $stmt->close();
    $conn->close();
}
?>
