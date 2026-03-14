<!-- select_period.php -->
<?php
include('config.php');
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Select Period - AUTO Detail Car Wash Report</title>
</head>
<body>
    <h2>Select Period for Revenue Report</h2>
    <form method="post" action="export_report.php">
        <label>From Date:</label>
        <input type="date" name="from_date" required>
        <label>To Date:</label>
        <input type="date" name="to_date" required>
        <button type="submit">Generate PDF Report</button>
    </form>
</body>
</html>
