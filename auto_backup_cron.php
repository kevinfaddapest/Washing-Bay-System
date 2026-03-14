<?php
include('config.php');

// ============================
// AUTOMATIC DATABASE BACKUP
// Saves to D:\Bay BackUps
// ============================

// Backup directory (Drive D)
$backupDir = 'D:\\Bay BackUps';

// Create directory if not exists
if (!is_dir($backupDir)) {
    mkdir($backupDir, 0755, true);
}

// Backup file name
$date = date('Y-m-d_H-i-s');
$backupFile = $backupDir . '\\carwash_auto_backup_' . $date . '.sql';

// Database credentials (you can reuse from config.php if defined)
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '@#Pest@#5045';
$db_name = 'carwash_db';

// Path to mysqldump
$mysqldump = 'D:\\wamp64\\bin\\mysql\\mysql8.4.7\\bin\\mysqldump.exe';

// Build command
$command = "\"$mysqldump\" --user={$db_user} --password=\"{$db_pass}\" --host={$db_host} --routines --events --single-transaction {$db_name} > \"$backupFile\" 2>&1";

// Execute
exec($command, $output, $return_var);

// Logging to database (system user ID = 0)
$action = '';
$details = '';

if ($return_var === 0 && file_exists($backupFile) && filesize($backupFile) > 0) {
    $action = "Automatic Database Backup Success";
    $details = "Auto backup saved to $backupFile";
} else {
    $action = "Automatic Database Backup Failed";
    $details = "Error: " . implode("\n", $output);
}

// Insert into activity_log
$stmt = $conn->prepare("INSERT INTO activity_log (user_id, username, action, details, ip_address) VALUES (?, ?, ?, ?, ?)");
$system_user_id = 0;
$system_username = "SYSTEM";
$ip = "CRON_JOB";

$stmt->bind_param("issss", $system_user_id, $system_username, $action, $details, $ip);
$stmt->execute();
$stmt->close();

$conn->close();
?>