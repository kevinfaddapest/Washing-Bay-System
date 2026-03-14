<?php
ob_start();
include('config.php');

if (session_status() === PHP_SESSION_NONE) session_start();
header('Content-Type: application/json; charset=utf-8');

// ===== AUTH =====
if (!isset($_SESSION['username'])) {
    echo json_encode(['error' => true, 'message' => 'Unauthorized — Please log in']);
    exit;
}

if (strtolower($_SESSION['role'] ?? '') !== 'admin') {
    echo json_encode(['error' => true, 'message' => 'Unauthorized — Admin only']);
    exit;
}

$user    = $_SESSION['username'];
$user_id = $_SESSION['user_id'] ?? 0;

// ===== INPUT =====
$id             = intval($_POST['id'] ?? 0);
$expense_name   = trim($_POST['expense_name'] ?? '');
$category       = trim($_POST['category'] ?? '');
$amount         = floatval($_POST['amount'] ?? 0);
$date           = $_POST['date'] ?? date('Y-m-d');
$receipt_number = trim($_POST['receipt_number'] ?? '');

// ===== VALIDATION =====
if ($id <= 0 || $expense_name === '' || $category === '' || $amount <= 0) {
    echo json_encode(['error' => true, 'message' => 'Invalid input data']);
    exit;
}

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
    $date = date('Y-m-d');
}

// ===== FETCH CURRENT RECEIPT =====
$stmt = $conn->prepare("SELECT receipt_file FROM expenses WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($current_receipt);
$stmt->fetch();
$stmt->close();

$receipt_file = $current_receipt;

// ===== RECEIPT RULE =====
if ($amount >= 25000 && $receipt_number === '') {
    echo json_encode([
        'error' => true,
        'message' => 'Receipt number required for expenses ≥ 25,000 UGX'
    ]);
    exit;
}

// ===== FILE UPLOAD =====
if (!empty($_FILES['receipt_file']['name'])) {

    $allowed = ['image/jpeg', 'image/png', 'application/pdf'];

    if (!in_array($_FILES['receipt_file']['type'], $allowed)) {
        echo json_encode([
            'error' => true,
            'message' => 'Receipt must be PDF, JPG, or PNG'
        ]);
        exit;
    }

    $upload_dir = 'uploads/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $receipt_file = time() . '_' . basename($_FILES['receipt_file']['name']);
    move_uploaded_file(
        $_FILES['receipt_file']['tmp_name'],
        $upload_dir . $receipt_file
    );
}

// ===== ACTIVITY LOG =====
function logActivity($user_id, $username, $action, $details = '') {
    global $conn;
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';

    $stmt = $conn->prepare(
        "INSERT INTO activity_log (user_id, username, action, details, ip_address)
         VALUES (?, ?, ?, ?, ?)"
    );
    if ($stmt) {
        $stmt->bind_param("issss", $user_id, $username, $action, $details, $ip);
        $stmt->execute();
        $stmt->close();
    }
}

// ===== UPDATE =====
$stmt = $conn->prepare(
    "UPDATE expenses SET
        expense_name = ?,
        category = ?,
        amount = ?,
        date = ?,
        receipt_number = ?,
        receipt_file = ?
     WHERE id = ?"
);

$stmt->bind_param(
    "ssdsssi",
    $expense_name,
    $category,
    $amount,
    $date,
    $receipt_number,
    $receipt_file,
    $id
);

if ($stmt->execute()) {

    logActivity(
        $user_id,
        $user,
        "Update Expense",
        "Updated expense ID $id | Amount: $amount UGX"
    );

    echo json_encode([
        'error' => false,
        'message' => 'Expense updated successfully'
    ]);
    exit;

} else {

    logActivity(
        $user_id,
        $user,
        "Failed Expense Update",
        $stmt->error
    );

    echo json_encode([
        'error' => true,
        'message' => 'Failed to update expense'
    ]);
    exit;
}
