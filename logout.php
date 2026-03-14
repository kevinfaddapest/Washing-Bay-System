<?php
include('config.php');  // Database connection
session_start();

$ip_address = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';

// Get user info BEFORE clearing session
$user_id  = $_SESSION['user_id']  ?? 0;
$username = $_SESSION['username'] ?? 'Unknown';

// Function to log activity
function logActivity($user_id, $username, $action, $details = '', $ip_address = 'Unknown') {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO activity_log (user_id, username, action, details, ip_address) 
                            VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $user_id, $username, $action, $details, $ip_address);
    $stmt->execute();
    $stmt->close();
}

// Log logout activity only if user was logged in
if ($user_id && $username !== 'Unknown') {
    logActivity($user_id, $username, "Logout", "User logged out", $ip_address);
}

// Destroy session safely
session_unset();
session_destroy();

// Redirect to login page
header("Location: index.php");
exit;
?>
