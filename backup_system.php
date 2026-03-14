<?php
if (session_status() == PHP_SESSION_NONE) session_start();
include('config.php');

// Only admins
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Function to log activities
function logActivity($user_id, $username, $action, $details) {
    global $conn;
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
    $stmt = $conn->prepare("INSERT INTO activity_log (user_id, username, action, details, ip_address) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $user_id, $username, $action, $details, $ip);
    $stmt->execute();
    $stmt->close();
}

// Backup folder
$backupDir = __DIR__ . '/backups';
if (!is_dir($backupDir)) mkdir($backupDir, 0755, true);

// Backup file
$date = date('Y-m-d_H-i-s');
$backupFile = "$backupDir/backup_$date.sql";

// Database credentials
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '@#Pest@#5045';  
$db_name = 'carwash_db';

// Path to mysqldump
$mysqldump = 'D:\\wamp64\\bin\\mysql\\mysql8.4.7\\bin\\mysqldump.exe';

// Command
$command = "\"$mysqldump\" --user={$db_user} --password=\"{$db_pass}\" --host={$db_host} {$db_name} > \"{$backupFile}\" 2>&1";

// Execute
$output = [];
$return_var = null;
exec($command, $output, $return_var);

// Check success
$backupSuccess = true;
$errorMessage = '';

if ($return_var !== 0 || !file_exists($backupFile) || filesize($backupFile) === 0) {
    $backupSuccess = false;
    $errorMessage = "Backup failed:\n" . implode("\n", $output);
    logActivity($_SESSION['user_id'], $_SESSION['username'], "Database Backup Failed", $errorMessage);
} else {
    logActivity($_SESSION['user_id'], $_SESSION['username'], "Database Backup Success", "Backup saved to $backupFile");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>AUTO Detail Car Wash - Backup Status</title>
<style>
body {
    font-family: "Poppins", sans-serif;
    background: #f5f7fa;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
}
.container {
    text-align: center;
    background: #fff;
    padding: 40px 60px;
    border-radius: 12px;
    box-shadow: 0 6px 18px rgba(0,0,0,0.1);
}
h1 { font-size: 2em; margin-bottom: 20px; }
.success { color: #28a745; }
.error { color: #dc3545; }
button, .dashboard-btn {
    margin-top: 15px;
    padding: 12px 25px;
    font-size: 16px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: 0.3s;
}
button { background: #0078D7; color: #fff; }
button:hover { background: #005ea6; }
.dashboard-btn {
    display: inline-block;
    background: linear-gradient(135deg, #6a11cb, #2575fc);
    color: #fff;
    text-decoration: none;
}
.dashboard-btn:hover { background: linear-gradient(135deg, #2575fc, #6a11cb); }
a.download-link { text-decoration: none; }
pre { text-align: left; background: #f0f0f0; padding: 15px; border-radius: 8px; }
</style>
</head>
<body>
<div class="container">
<?php if ($backupSuccess): ?>
    <h1 class="success">✅ Backup Successful!</h1>
    <p>Your database has been backed up successfully on <strong><?php echo date('F j, Y, H:i:s'); ?></strong>.</p>
    <a class="download-link" href="<?php echo 'backups/' . basename($backupFile); ?>" download>
        <button>Download Backup</button>
    </a>
    <br>
    <a class="dashboard-btn" href="dashboard.php">← Back to Dashboard</a>
<?php else: ?>
    <h1 class="error">❌ Backup Failed</h1>
    <p>Please check the following error:</p>
    <pre><?php echo htmlspecialchars($errorMessage); ?></pre>
    <a class="dashboard-btn" href="dashboard.php">← Back to Dashboard</a>
<?php endif; ?>
</div>
</body>
</html>
