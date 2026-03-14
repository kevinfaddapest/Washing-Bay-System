<?php
if (session_status() == PHP_SESSION_NONE) session_start();
include('config.php');

// Only admin can access
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$msg = "";

// Log activity function
function logActivity($user_id, $username, $action, $details) {
    global $conn;
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
    $stmt = $conn->prepare("INSERT INTO activity_log (user_id, username, action, details, ip_address) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $user_id, $username, $action, $details, $ip_address);
    $stmt->execute();
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $u = trim($_POST['username']);
    $p = $_POST['password'];
    $r = trim($_POST['role']);

    // Validate role
    if (!in_array($r, ['admin', 'staff'])) {
        $msg = "❌ Invalid role selected!";
    } else {
        // Hash password securely
        $hash = password_hash($p, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $u, $hash, $r);

        if ($stmt->execute()) {
            // Log success
            logActivity($_SESSION['user_id'], $_SESSION['username'], "Add User", "Added new user: $u with role: $r");
            $msg = "✅ User '$u' added securely!";
        } else {
            // Log failure
            logActivity($_SESSION['user_id'], $_SESSION['username'], "Failed Add User", "Failed to add user: $u. Error: " . $stmt->error);
            $msg = "❌ Error: " . htmlspecialchars($stmt->error);
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add User</title>
<link rel="stylesheet" href="style.css">
<style>
body { font-family: 'Poppins', sans-serif; background: #f5f7fa; margin: 0; }
header { background-color: #023020; color: white; padding: 20px; display: flex; justify-content: space-between; align-items: center; }
header h1 { margin: 0; font-size: 1.4rem; }
header nav a { color: white; margin-right: 15px; text-decoration: none; font-weight: 600; padding: 6px 12px; border-radius: 5px; transition: 0.3s; }
header nav a:hover { background: rgba(255,255,255,0.2); }
main { padding: 20px; }
form.form-card { max-width: 500px; margin: 20px auto; background: #fff; padding: 25px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
form label { display: block; margin: 15px 0 5px; }
form input, form select { width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd; }
form button { margin-top: 20px; padding: 12px 25px; background: #0078D7; color: white; border: none; border-radius: 8px; cursor: pointer; transition: 0.3s; }
form button:hover { background: #005ea6; }
.alert { text-align: center; padding: 10px; margin-bottom: 20px; border-radius: 5px; background: #f0f0f0; }
</style>
</head>
<body>
<header>
<h1>Add Secure User</h1>
<nav>
    <a href="dashboard.php">🏠 Dashboard</a>
    <a href="users.php">👥 Manage Users</a>
    <a href="logout.php">🚪 Logout</a>
</nav>
</header>

<main>
<?php if($msg) echo "<p class='alert'>" . htmlspecialchars($msg) . "</p>"; ?>
<form method="POST" class="form-card">
    <label>Username:</label>
    <input type="text" name="username" required>

    <label>Password:</label>
    <input type="password" name="password" required>

    <label>Role:</label>
    <select name="role" required>
        <option value="staff">Staff</option>
        <option value="admin">Admin</option>
    </select>

    <button type="submit">Create User</button>
</form>
</main>
</body>
</html>
