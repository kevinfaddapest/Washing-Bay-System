<?php
include('config.php');
if (session_status() == PHP_SESSION_NONE) session_start();

// Ensure user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Function to log activities
function logActivity($user_id, $username, $action, $details) {
    global $conn;
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
    $stmt = $conn->prepare("INSERT INTO activity_log (user_id, username, action, details, ip_address) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $user_id, $username, $action, $details, $ip_address);
    $stmt->execute();
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $expense_name = trim($_POST['expense_name'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $amount = floatval($_POST['amount'] ?? 0);
    $date = $_POST['date'] ?? date('Y-m-d');

    $added_by_id = $_SESSION['user_id'];
    $added_by_name = $_SESSION['username'];

    $receipt_number = null;
    $receipt_file_name = null;

    if ($amount >= 25000) {

        $receipt_number = trim($_POST['receipt_number'] ?? '');

        if (empty($receipt_number)) {
            $error = "Receipt number is required for expenses 25,000 UGX or more.";
        }

        if (!isset($_FILES['receipt_file'])) {
            $error = "Receipt file not received. Check form enctype.";
        }

        elseif ($_FILES['receipt_file']['error'] !== UPLOAD_ERR_OK) {

            $upload_errors = [
                1 => "File exceeds upload_max_filesize",
                2 => "File exceeds MAX_FILE_SIZE in form",
                3 => "Partial upload",
                4 => "No file uploaded",
                6 => "Missing temp folder",
                7 => "Failed to write file to disk",
                8 => "Upload stopped by extension",
            ];

            $err_code = $_FILES['receipt_file']['error'];
            $error = $upload_errors[$err_code] ?? "Unknown upload error";
        }

        else {

            // File size limit (5MB)
            $max_size = 5 * 1024 * 1024;

            if ($_FILES['receipt_file']['size'] > $max_size) {
                $error = "Receipt must be less than 5MB.";
            }

            else {

                // Allowed extensions
                $allowed_ext = ['jpg','jpeg','png','pdf'];
                $file_ext = strtolower(pathinfo($_FILES['receipt_file']['name'], PATHINFO_EXTENSION));

                if (!in_array($file_ext, $allowed_ext)) {
                    $error = "Receipt must be a PDF, JPG, or PNG file.";
                }

                else {

                    $upload_dir = "uploads/";

                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }

                    $receipt_file_name = time() . "_" . uniqid() . "." . $file_ext;

                    if (!move_uploaded_file($_FILES['receipt_file']['tmp_name'], $upload_dir . $receipt_file_name)) {
                        $error = "Failed to upload receipt file.";
                    }
                }
            }
        }
    }

    if (!isset($error)) {

        $stmt = $conn->prepare("
            INSERT INTO expenses 
            (expense_name, category, amount, date, added_by, receipt_number, receipt_file) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "ssdssss",
            $expense_name,
            $category,
            $amount,
            $date,
            $added_by_name,
            $receipt_number,
            $receipt_file_name
        );

        if ($stmt->execute()) {

            $details = "Added expense: $expense_name, Category: $category, Amount: $amount UGX, Date: $date";

            if ($amount >= 25000) {
                $details .= ", Receipt Number: $receipt_number, File: $receipt_file_name";
            }

            logActivity($added_by_id, $added_by_name, "Add Expense", $details);

            $stmt->close();

            header("Location: view_expenses.php?success=Expense added successfully");
            exit;
        }

        else {
            $error = "Failed to add expense: " . $stmt->error;
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add Expense - AUTO Detail Car Wash</title>
<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
body {
    font-family: "Poppins", Arial, sans-serif;
    background-color: gray;
    margin: 0;
    color: #333;
}

/* Header */
header {
    background:;
    color: #fff;
    padding: 15px 25px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
header h1 {
    margin: 0;
    font-size: 1.5em;
}
header a {
    color: white;
    text-decoration: none;
    font-weight: bold;
    background: rgba(255,255,255,0.2);
    padding: 8px 15px;
    border-radius: 6px;
    transition: 0.3s;
}
header a:hover {
    background: rgba(255,255,255,0.35);
}

/* Main form container */
main {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: calc(50vh - 20px);
    padding: 20px;
}

.form-card {
    background: #fff;
    width: 100%;
    max-width: 450px;
    padding: 30px 35px;
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    animation: fadeIn 0.5s ease;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}
.form-card h2 {
    text-align: center;
    color: #0078D7;
    margin-bottom: 25px;
}
.form-card label {
    font-weight: 500;
    margin-bottom: 5px;
    display: block;
}
.form-card input {
    width: 100%;
    padding: 10px 12px;
    margin-bottom: 18px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 14px;
    transition: border-color 0.2s, box-shadow 0.2s;
}
.form-card input:focus {
    border-color: #0078D7;
    box-shadow: 0 0 4px rgba(0,120,215,0.3);
    outline: none;
}
.form-card button {
    width: 100%;
    padding: 12px;
    border: none;
    background: green;
    color: white;
    font-weight: bold;
    font-size: 15px;
    border-radius: 6px;
    cursor: pointer;
    transition: background 0.3s;
}
.form-card button:hover {
    background: black;
}
.error-msg {
    color: red;
    text-align: center;
    margin-bottom: 15px;
}
.success-msg {
    color: green;
    text-align: center;
    margin-bottom: 15px;
}
#back{
background-color:blue;
}

#back:hover{
background-color:gray;
}
</style>
</head>
<body>

<header>
    <h1><i class="fa-solid fa-wallet"></i> Add Expense</h1>
    <a href="view_expenses.php" id="back"><i class="fa-solid fa-arrow-left"></i> Back</a>
</header>

<main>
    <div class="form-card">
        <h2><i class="fa-solid fa-receipt"></i> New Expense</h2>

        <?php if(isset($error)): ?>
            <p class="error-msg"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="POST" action="" enctype="multipart/form-data">
            <label for="expense_name">Expense Name</label>
            <input type="text" id="expense_name" name="expense_name" placeholder="e.g. Soap, Water Bill" required>

            <label for="category">Category</label>
            <input type="text" id="category" name="category" placeholder="e.g. Supplies, Utilities" required>

            <label for="amount">Amount (UGX)</label>
            <input type="number" id="amount" name="amount" step="0.01" placeholder="e.g. 20000" required>

            <label for="date">Date</label>
            <input type="date" id="date" name="date" value="<?= date('Y-m-d') ?>" required>

            <!-- Receipt fields -->
            <div id="receipt_fields" style="display:none;">
                <label for="receipt_number">Receipt Number</label>
                <input type="text" id="receipt_number" name="receipt_number" placeholder="Enter receipt number">

                <label for="receipt_file">Upload Receipt</label>
                <input type="file" id="receipt_file" name="receipt_file" accept=".jpg,.jpeg,.png,.pdf">
            </div>

            <button type="submit"><i class="fa-solid fa-plus-circle"></i> Add Expense</button>
        </form>
    </div>
</main>

<script>
const amountInput = document.getElementById('amount');
const receiptFields = document.getElementById('receipt_fields');
const receiptNumber = document.getElementById('receipt_number');
const receiptFile = document.getElementById('receipt_file');

amountInput.addEventListener('input', function() {
    if (parseFloat(this.value) >= 25000) {
        receiptFields.style.display = 'block';
        receiptNumber.required = true;
        receiptFile.required = true;
    } else {
        receiptFields.style.display = 'none';
        receiptNumber.required = false;
        receiptFile.required = false;
    }
});
</script>

</body>
</html>