<?php
include('config.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if current user is DaPEST
$isDaPEST = isset($_SESSION['username']) && $_SESSION['username'] === 'DaPEST';

// ======= Log Activity Function =======
function logActivity($user_id, $username, $action, $details = '') {
    global $conn;
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
    $stmt = $conn->prepare("INSERT INTO activity_log (user_id, username, action, details, ip_address) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $user_id, $username, $action, $details, $ip);
    $stmt->execute();
    $stmt->close();
}

// Log page view
logActivity($_SESSION['user_id'], $_SESSION['username'], "Viewed Users Page");

// Handle delete (only if DaPEST)
if ($isDaPEST && isset($_GET['delete'])) {
    $id = (int)$_GET['delete']; // sanitize
    $userToDelete = $conn->query("SELECT username FROM users WHERE id=$id")->fetch_assoc();
    if ($userToDelete) {
        $conn->query("DELETE FROM users WHERE id=$id");
        logActivity($_SESSION['user_id'], $_SESSION['username'], "Deleted User", "Deleted user: " . $userToDelete['username']);
    }
    header("Location: users.php");
    exit;
}

// Fetch users
$users = $conn->query("SELECT * FROM users");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Users</title>
<style>
body { font-family: 'Poppins', sans-serif; background: #f5f7fa; margin: 0; }
header { background-color: #023020; color: white; padding: 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; }
header h1 { margin: 0; font-size: 1.4rem; }
header nav a { color: white; margin: 5px 10px; text-decoration: none; font-weight: 600; padding: 6px 12px; border-radius: 5px; transition: 0.3s; }
header nav a:hover { background: rgba(255,255,255,0.2); }
main { padding: 20px; }

/* Table wrapper for responsiveness */
.table-wrapper { overflow-x: auto; width: 100%; }

/* Table styles */
table {
    width: 100%;
    border-collapse: collapse;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    background: #fff;
    min-width: 500px;
}
th, td {
    padding: 12px 15px;
    text-align: center;
    white-space: nowrap;
}
th {
    background: linear-gradient(45deg, #0078D7, #00aaff);
    color: #fff;
    font-weight: 600;
    text-transform: uppercase;
}
tr:nth-child(even) { background: #f3f6f9; }
tr:hover { background: #e1f0ff; }

/* Action buttons */
.actions { display: flex; justify-content: center; gap: 5px; flex-wrap: nowrap; }
button { padding: 6px 12px; border-radius: 6px; border: none; cursor: pointer; transition: 0.2s; font-weight: 600; white-space: nowrap; }
button.edit { background-color: #058565; color: #fff; }
button.edit:hover { background-color: #09967a; }
button.delete { background-color: #dc3545; color: #fff; }
button.delete:hover { background-color: #c82333; }

/* Responsive adjustments */
@media (max-width: 600px) {
    header { flex-direction: column; align-items: flex-start; }
    table { font-size: 14px; }
    button { padding: 4px 8px; font-size: 12px; }
}
</style>
</head>
<body>
<header>
<h1>AUTO Detail Car Wash - Manage Users</h1>
<nav>
    <a href="dashboard.php">🏠 Dashboard</a>
    <?php if ($isDaPEST): ?>
        <a href="add_user.php">➕ Add User</a>
        <a href="reports.php">📊 Reports</a>
    <?php endif; ?>
    <a href="logout.php">🚪 Logout</a>
</nav>
</header>

<main>
<div class="table-wrapper">
<table>
<tr><th>#</th><th>Username</th><th>Role</th><th>Actions</th></tr>
<?php
$serial = 1;
while($u = $users->fetch_assoc()):
?>
<tr>
<td><?= $serial ?></td>
<td><?= htmlspecialchars($u['username']) ?></td>
<td><?= ucfirst($u['role']) ?></td>
<td>
    <div class="actions">
        <?php if ($isDaPEST): ?>
            <a href="edit_user.php?id=<?= $u['id'] ?>"><button class="edit">✏️ Edit</button></a>
            <a href="?delete=<?= $u['id'] ?>" onclick="return confirm('Delete user?')"><button class="delete">🗑️ Delete</button></a>
        <?php else: ?>
            <em>Restricted</em>
        <?php endif; ?>
    </div>
</td>
</tr>
<?php $serial++; endwhile; ?>
</table>
</div>
</main>
</body>
</html>
