<?php
if (session_status() == PHP_SESSION_NONE) session_start();

include('config.php');

// Require login
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Role protection
function requireRole($role) {
    global $conn;

    if (!isset($_SESSION['role']) || $_SESSION['role'] !== $role) {

        $ip = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
        $stmt = $conn->prepare("
            INSERT INTO activity_log (user_id, username, action, details, ip_address) 
            VALUES (?, ?, ?, ?, ?)
        ");

        $zero = 0;
        $stmt->bind_param(
            "issss",
            $zero,
            $_SESSION['username'] ?? 'Unknown',
            "Unauthorized Access",
            "Attempted to access page requiring role: $role",
            $ip
        );

        $stmt->execute();
        $stmt->close();

        die("❌ Access denied. Requires role: $role.");
    }
}
?>
