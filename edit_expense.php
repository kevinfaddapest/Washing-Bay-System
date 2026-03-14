<?php
include('config.php');
if(session_status() == PHP_SESSION_NONE) session_start();

// Authorization check
if(!isset($_SESSION['user']) || $_SESSION['role']!='admin'){
    exit("Unauthorized");
}

// User info
$user = $_SESSION['user'];
$user_id = $_SESSION['user_id'] ?? 0;

// Input data
$id = intval($_POST['id'] ?? 0);
$expense_name = $_POST['expense_name'] ?? '';
$category = $_POST['category'] ?? '';
$amount = floatval($_POST['amount'] ?? 0);
$date = $_POST['date'] ?? '';

// Validate input
if($id <= 0 || empty($expense_name) || empty($category) || $amount <= 0 || empty($date)){
    $_SESSION['error'] = "Invalid input data";
    header("Location: view_expenses.php");
    exit;
}

// Function to log activity
function logActivity($user_id, $username, $action, $details=''){
    global $conn;
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
    $stmt = $conn->prepare("INSERT INTO activity_log (user_id, username, action, details, ip_address) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $user_id, $username, $action, $details, $ip_address);
    $stmt->execute();
    $stmt->close();
}

// Fetch current expense details for logging
$stmt_fetch = $conn->prepare("SELECT * FROM expenses WHERE id=?");
$stmt_fetch->bind_param("i", $id);
$stmt_fetch->execute();
$result = $stmt_fetch->get_result();
$old_expense = $result->fetch_assoc();
$stmt_fetch->close();

if (!$old_expense) {
    logActivity($user_id, $user, "Failed Expense Update", "Expense ID $id not found");
    $_SESSION['error'] = "Expense not found";
    header("Location: view_expenses.php");
    exit;
}

// Prepare and execute update
$stmt = $conn->prepare("UPDATE expenses SET expense_name=?, category=?, amount=?, date=? WHERE id=?");
$stmt->bind_param("ssdsi", $expense_name, $category, $amount, $date, $id);

if($stmt->execute() && $stmt->affected_rows > 0){
    // Log success with old and new details
    $details = "Updated expense ID $id. Before: " . json_encode($old_expense) . 
               " After: " . json_encode([
                   'expense_name' => $expense_name,
                   'category' => $category,
                   'amount' => $amount,
                   'date' => $date
               ]);
    logActivity($user_id, $user, "Update Expense", $details);

    $_SESSION['success'] = "Expense updated successfully!";
}else{
    // Log failure
    $details = "Failed to update expense ID $id. Error: ".$stmt->error . ". Old data: " . json_encode($old_expense);
    logActivity($user_id, $user, "Failed Expense Update", $details);

    $_SESSION['error'] = "Error updating expense!";
}

$stmt->close();

// Redirect to view_expenses.php
header("Location: view_expenses.php");
exit;
?>
