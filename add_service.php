<?php
// ALWAYS start session before anything else
if (session_status() == PHP_SESSION_NONE) session_start();

include('config.php');
include('session_check.php'); // now safe to include

// Redirect if not logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$msg = "";

// Set Uganda timezone
date_default_timezone_set('Africa/Kampala');
$current_date = date('Y-m-d H:i:s'); // current date & time in Uganda

// Function to log activities
function logActivity($user_id, $username, $action, $details) {
    global $conn;
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
    $stmt = $conn->prepare("INSERT INTO activity_log (user_id, username, action, details, ip_address) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $user_id, $username, $action, $details, $ip_address);
    $stmt->execute();
    $stmt->close();
}

// Fetch customers for dropdown
$customer_query = $conn->query("SELECT * FROM customers ORDER BY customer_name ASC");

// Fetch service types for dropdown
$services = $conn->query("SELECT * FROM service_types");

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_id     = intval($_POST['customer_id'] ?? 0);
    $vehicle_number  = trim($_POST['vehicle_number'] ?? '');
    $vehicle_type    = trim($_POST['vehicle_type'] ?? '');
    $service_type    = trim($_POST['service_type'] ?? '');
    $price           = floatval($_POST['price'] ?? 0);
    $payment_status  = trim($_POST['payment_status'] ?? '');

    $added_by_id     = $_SESSION['user_id'];
    $added_by_name   = $_SESSION['username'];

    // Fetch customer details
    $stmt = $conn->prepare("SELECT customer_name, contact FROM customers WHERE id=?");
    $stmt->bind_param("i", $customer_id);
    $stmt->execute();
    $stmt->bind_result($customer_name, $contact);
    $stmt->fetch();
    $stmt->close();

    // Insert service record with current Uganda date
    $stmt = $conn->prepare("INSERT INTO services 
        (customer_name, contact, vehicle_number, vehicle_type, service_type, price, payment_status, added_by, date)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssdsss", 
        $customer_name, $contact, $vehicle_number, 
        $vehicle_type, $service_type, $price, 
        $payment_status, $added_by_name, $current_date
    );

    if ($stmt->execute()) {
        $details = "Added service for $customer_name, Vehicle: $vehicle_number, Type: $vehicle_type, Service: $service_type, Price: $price UGX, Payment: $payment_status, Date: $current_date";
        logActivity($added_by_id, $added_by_name, "Add Service", $details);
        $msg = "✅ Record saved successfully!";
    } else {
        $details = "Failed to add service for $customer_name. Error: " . $stmt->error;
        logActivity($added_by_id, $added_by_name, "Failed Service Add", $details);
        $msg = "❌ Error: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Service</title>
    <link rel="stylesheet" href="style.css">
    <script>
    function fetchPrice() {
        let service = document.getElementById('service_type').value;
        if (!service) return;
        fetch('get_price.php?service=' + encodeURIComponent(service))
            .then(res => res.text())
            .then(data => document.getElementById('price').value = data);
    }

    function updateContact() {
        let select = document.getElementById('customer_id');
        let contactInput = document.getElementById('contact');
        let selectedOption = select.options[select.selectedIndex];
        contactInput.value = selectedOption.getAttribute('data-contact') || '';
    }
    </script>
</head>
<body>
<header>
    <h1>Add Car Wash Record</h1>
    <nav>
        <a href="dashboard.php">Dashboard</a>
        <a href="view_services.php">View Records</a>
        <?php if(isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
            <a href="reports.php">Reports</a>
        <?php endif; ?>
        <a href="logout.php">Logout</a>
    </nav>
</header>

<main>
<?php if($msg) echo "<p class='alert'>$msg</p>"; ?>

<form method="POST" class="form-card">
    <h2>🚗AUTO Detail Car Wash</h2>

    <label>Handled By:</label>
    <select name="customer_id" id="customer_id" onchange="updateContact()" required>
        <option value="">Select Operator</option>
        <?php while($c = $customer_query->fetch_assoc()): ?>
            <option value="<?= $c['id'] ?>" data-contact="<?= htmlspecialchars($c['contact']) ?>">
                <?= htmlspecialchars($c['customer_name']) ?>
            </option>
        <?php endwhile; ?>
    </select>

    <label>Contact:</label>
    <input type="text" name="contact" id="contact" readonly required>

    <label>Vehicle Number Plate:</label>
    <input type="text" name="vehicle_number" required>

    <label>Vehicle Type:</label>
    <input type="text" name="vehicle_type" required>

    <label>Service Type:</label>
    <select name="service_type" id="service_type" onchange="fetchPrice()" required>
        <option value="">Select Service</option>
        <?php while($s = $services->fetch_assoc()): ?>
            <option value="<?= htmlspecialchars($s['service_name']) ?>">
                <?= htmlspecialchars($s['service_name']) ?>
            </option>
        <?php endwhile; ?>
    </select>

    <label>Price:</label>
    <input type="number" name="price" id="price" readonly required>

    <label>Payment Status:</label>
    <select name="payment_status" id="payment_status" required>
        <option value="">Select</option>
        <option value="nopay">No pay</option>
        <option value="paid">Paid</option>
    </select>

    <button type="submit">Save Record</button>
</form>
</main>
</body>
</html>