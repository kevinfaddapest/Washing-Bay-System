<?php 
include('config.php');

// Start session if not started
if (session_status() == PHP_SESSION_NONE) session_start();

// Redirect to dashboard if already logged in
if (isset($_SESSION['user_id']) && isset($_SESSION['username'])) {
    header("Location: dashboard.php");
    exit;
}

// Function to log activity
function logActivity($user_id, $username, $action, $details = '') {
    global $conn;
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
    $stmt = $conn->prepare("INSERT INTO activity_log (user_id, username, action, details, ip_address) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $user_id, $username, $action, $details, $ip_address);
    $stmt->execute();
    $stmt->close();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Fetch user securely
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {

        $user = $res->fetch_assoc();
        $dbPassword = $user['password'];
        $valid = false;

        // Modern hash check
        if (password_verify($password, $dbPassword)) {
            $valid = true;
        }
        // Legacy MD5 check
        elseif ($dbPassword === md5($password)) {
            $valid = true;
            // Auto-update MD5 to strong hash
            $newHash = password_hash($password, PASSWORD_DEFAULT);
            $update = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $update->bind_param("si", $newHash, $user['id']);
            $update->execute();
        }

        if ($valid) {
            // Set consistent session variables
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role']     = $user['role'];

            // Log successful login
            logActivity($user['id'], $user['username'], "Login Success", "User logged in successfully");

            header("Location: dashboard.php");
            exit;

        } else {
            $error = "❌ Invalid password!";
            logActivity(0, $username, "Login Failed", "Invalid password attempt");
        }

    } else {
        $error = "❌ User not found!";
        logActivity(0, $username, "Login Failed", "Username not found");
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - AUTO Detail Car Wash</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="login-box">
    <h2>🚗 AUTO Detail Car Wash Login</h2>
    <?php if($error) echo "<p class='alert'>$error</p>"; ?>
    
    <form method="POST" autocomplete="off">

        <label>Username</label>
        <input type="text" name="username" required>

        <label>Password</label>
        <input type="password" name="password" required>

        <button type="submit">Login</button>

    </form>
</div>
</body>
</html>
