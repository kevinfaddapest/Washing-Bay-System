<?php
include('config.php');
if (session_status() === PHP_SESSION_NONE) session_start();

// ======= Admin-only access =======
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// ======= Log Activity Function =======
function logActivity($user_id, $username, $action, $details = '') {
    global $conn;
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
    $stmt = $conn->prepare("INSERT INTO activity_log (user_id, username, action, details, ip_address) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $user_id, $username, $action, $details, $ip_address);
    $stmt->execute();
    $stmt->close();
}

// ======= Fetch User Data =======
$id = intval($_GET['id']);
$userData = $conn->query("SELECT * FROM users WHERE id=$id")->fetch_assoc();
if (!$userData) {
    header("Location: users.php");
    exit;
}

// ======= Handle POST (Update User) =======
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $role = $_POST['role'] ?? '';
    $old_data = json_encode($userData);

    if (!empty($_POST['password'])) {
        $hashedPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET username=?, password=?, role=? WHERE id=?");
        $stmt->bind_param("sssi", $username, $hashedPassword, $role, $id);
        $stmt->execute();
        $stmt->close();
        $details = "Updated user ID: $id. Old data: $old_data. New data: username='$username', role='$role', password=changed";
    } else {
        $stmt = $conn->prepare("UPDATE users SET username=?, role=? WHERE id=?");
        $stmt->bind_param("ssi", $username, $role, $id);
        $stmt->execute();
        $stmt->close();
        $details = "Updated user ID: $id. Old data: $old_data. New data: username='$username', role='$role'";
    }

    // Log activity
    logActivity($_SESSION['user_id'], $_SESSION['username'], "Update User", $details);

    header("Location: users.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit User</title>
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
</style>
</head>
<body>
<header>
<h1>Edit User</h1>
<nav>
    <a href="dashboard.php">🏠 Dashboard</a>
    <a href="users.php">👥 Manage Users</a>
    <a href="logout.php">🚪 Logout</a>
</nav>
</header>

<main>
<form method="POST" class="form-card">
    <label>Username:</label>
    <input type="text" name="username" value="<?= htmlspecialchars($userData['username']) ?>" required>

    <label>New Password (optional):</label>
    <input type="password" name="password">

    <label>Role:</label>
    <select name="role">
        <option value="staff" <?= $userData['role']=='staff'?'selected':'' ?>>Staff</option>
        <option value="admin" <?= $userData['role']=='admin'?'selected':'' ?>>Admin</option>
    </select>

    <button type="submit">Update User</button>
</form>
</main>
</body>
</html>
