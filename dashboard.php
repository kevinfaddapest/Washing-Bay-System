<?php
include('config.php'); 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect if user not logged in
if (!isset($_SESSION['username']) || !isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user = $_SESSION['username'];  // FIXED
$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// =======================
// LOG ACTIVITY
// =======================
function logActivity($user_id, $username, $action, $details = '') {
    global $conn;
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
    $stmt = $conn->prepare("INSERT INTO activity_log (user_id, username, action, details, ip_address) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $user_id, $username, $action, $details, $ip_address);
    $stmt->execute();
    $stmt->close();
}

// Log dashboard access
$details = ($role === 'admin') ? "Admin dashboard accessed" : "Staff dashboard accessed";
logActivity($user_id, $user, "Dashboard Access", $details);

// =======================
// ADMIN DASHBOARD DATA
// =======================
if ($role == 'admin') {
    // REVENUE
    $today_rev = (float)($conn->query("SELECT SUM(price) AS total FROM services WHERE DATE(date)=CURDATE()")->fetch_assoc()['total'] ?? 0);
    $week_rev = (float)($conn->query("SELECT SUM(price) AS total FROM services WHERE date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)")->fetch_assoc()['total'] ?? 0);
    $month_rev = (float)($conn->query("SELECT SUM(price) AS total FROM services WHERE MONTH(date)=MONTH(CURDATE())")->fetch_assoc()['total'] ?? 0);
    $quarter_rev = (float)($conn->query("SELECT SUM(price) AS total FROM services WHERE QUARTER(date)=QUARTER(CURDATE())")->fetch_assoc()['total'] ?? 0);
    $year_rev = (float)($conn->query("SELECT SUM(price) AS total FROM services WHERE YEAR(date)=YEAR(CURDATE())")->fetch_assoc()['total'] ?? 0);

    // EXPENSES
    $today_exp = (float)($conn->query("SELECT SUM(amount) AS total FROM expenses WHERE DATE(date)=CURDATE()")->fetch_assoc()['total'] ?? 0);
    $week_exp = (float)($conn->query("SELECT SUM(amount) AS total FROM expenses WHERE date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)")->fetch_assoc()['total'] ?? 0);
    $month_exp = (float)($conn->query("SELECT SUM(amount) AS total FROM expenses WHERE MONTH(date)=MONTH(CURDATE())")->fetch_assoc()['total'] ?? 0);
    $quarter_exp = (float)($conn->query("SELECT SUM(amount) AS total FROM expenses WHERE QUARTER(date)=QUARTER(CURDATE())")->fetch_assoc()['total'] ?? 0);
    $year_exp = (float)($conn->query("SELECT SUM(amount) AS total FROM expenses WHERE YEAR(date)=YEAR(CURDATE())")->fetch_assoc()['total'] ?? 0);

   // VEHICLES = TOTAL RECORDS
    $today_veh = (int)($conn->query("SELECT COUNT(*) AS v FROM services WHERE DATE(date)=CURDATE()")->fetch_assoc()['v'] ?? 0);
    $week_veh = (int)($conn->query("SELECT COUNT(*) AS v FROM services WHERE date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)")->fetch_assoc()['v'] ?? 0);
    $month_veh = (int)($conn->query("SELECT COUNT(*) AS v FROM services WHERE MONTH(date)=MONTH(CURDATE())")->fetch_assoc()['v'] ?? 0);
    $quarter_veh = (int)($conn->query("SELECT COUNT(*) AS v FROM services WHERE QUARTER(date)=QUARTER(CURDATE())")->fetch_assoc()['v'] ?? 0);
    $year_veh = (int)($conn->query("SELECT COUNT(*) AS v FROM services WHERE YEAR(date)=YEAR(CURDATE())")->fetch_assoc()['v'] ?? 0);
    // VEHICLES PER YEAR (last 5 years)
$vehicles_per_year = [];
$result = $conn->query("
    SELECT YEAR(date) AS yr, COUNT(*) AS total 
    FROM services 
    GROUP BY YEAR(date) 
    ORDER BY YEAR(date) ASC
");
while ($row = $result->fetch_assoc()) {
    $vehicles_per_year[$row['yr']] = (int)$row['total'];
}

// Prepare labels and data for JS
$vehicle_year_labels = json_encode(array_keys($vehicles_per_year));
$vehicle_year_data = json_encode(array_values($vehicles_per_year));
   }

// ======================= 
// STAFF DASHBOARD DATA                        
// ======================= 
if ($role != 'admin') {
    $user_today = (float)($conn->query("SELECT SUM(price) AS total FROM services WHERE added_by='$user' AND DATE(date)=CURDATE()")->fetch_assoc()['total'] ?? 0);
    $user_week = (float)($conn->query("SELECT SUM(price) AS total FROM services WHERE added_by='$user' AND date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)")->fetch_assoc()['total'] ?? 0);
    $user_month = (float)($conn->query("SELECT SUM(price) AS total FROM services WHERE added_by='$user' AND MONTH(date)=MONTH(CURDATE())")->fetch_assoc()['total'] ?? 0); 
    $user_quarter = (float)($conn->query("SELECT SUM(price) AS total FROM services WHERE added_by='$user' AND QUARTER(date)=QUARTER(CURDATE())")->fetch_assoc()['total'] ?? 0);                        
    $user_year = (float)($conn->query("SELECT SUM(price) AS total FROM services WHERE added_by='$user' AND YEAR(date)=YEAR(CURDATE())")->fetch_assoc()['total'] ?? 0); 

    $user_total = (int)($conn->query("SELECT COUNT(*) AS total FROM services WHERE added_by='$user'")->fetch_assoc()['total'] ?? 0);
    $user_vehicles = (int)($conn->query("SELECT COUNT(*) AS v FROM services WHERE added_by='$user'")->fetch_assoc()['v'] ?? 0);
    $user_latest = $conn->query("SELECT * FROM services WHERE added_by='$user' ORDER BY date DESC LIMIT 5");
} 
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Dashboard - AUTO Detail Car Wash</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="/assets/Chart/src/controllers/controller.bar.js"></script>

<style>
/* ---- Simple Dashboard Styles ---- */
body { font-family: 'Poppins', sans-serif; margin:0; background: linear-gradient(180deg, #f4f8ff, #ffffff);}
header { background-color:#023020; color:white; padding:20px; display:flex; justify-content:space-between; align-items:center;}
header h1{ font-size:1.3rem;}
header nav a{ color:white; margin-right:15px; text-decoration:none; font-weight:bold; padding:6px 12px; border-radius:5px; transition:0.3s;}
header nav a:hover{ background: rgba(255,255,255,0.2);}
main{ padding:20px;}
.dashboard-container{ width:95%; margin:20px auto;}
.summary-cards{ display:flex; flex-wrap:wrap; gap:20px; justify-content:center;}
.card{ background:white; flex:1 1 220px; padding:20px; text-align:center; border-radius:10px; box-shadow:0 2px 10px rgba(0,0,0,0.1); transition:0.3s;}
.card:hover{ transform:translateY(-5px);}
.card h3{ color:#0078D7;}
.card p{ font-size:20px; font-weight:bold; color:#333;}
.card a{ display:block; color:#0078D7; text-decoration:none; margin-top:8px; font-weight:600;}
.card a:hover{ text-decoration:underline;}
hr{ border:none; height:2px; background:black; margin:40px 0;}
.chart-container{ max-width:900px; margin:40px auto; background:white; border-radius:10px; padding:20px; box-shadow:0 2px 10px rgba(0,0,0,0.1);}
footer{ text-align:center; padding:15px; background:#0078D7; color:white; margin-top:50px;}
table{ width:100%; border-collapse:collapse; margin-top:20px;}
table th, table td{ border:1px solid #ddd; padding:8px; text-align:center;}
table th{ background:#0078D7; color:white;}
</style>

</head>
<body>

<header>
<h1><?= htmlspecialchars($user); ?></h1>
<nav>
<a href="dashboard.php">🏠 Home</a>
<a href="add_service.php">➕ New Record</a>
<a href="view_services.php">📋 All Records</a>
<?php if($role=='admin'): ?>
<a href="backup_system.php">🔥 Backup</a> 
<a href="view_activity.php">✅ Logs</a> 
<a href="view_expenses.php">💰 Expenses</a>
<a href="reports.php">📊 Reports</a>
<a href="users.php">👥 Users</a>
<?php endif; ?>
<a href="logout.php">🚪 Logout</a>
</nav>
</header>

<main>

<h2 style="text-align:center;">🚗 Welcome to AUTO Detail Car Wash Dashboard</h2>
<p style="text-align:center;">Monitor daily performance, expenses, and record management.</p>

<?php if($role=='admin'): ?>

<div class="dashboard-container">
<hr>
<h3 style="text-align:center; color:#023020;">💵 Revenue Overview</h3>
<div class="summary-cards">
<div class="card"><h3>Daily</h3><p>UGX <?= number_format($today_rev,0) ?></p><a href="view_services.php?period=daily">View Details</a></div>
<div class="card"><h3>Weekly</h3><p>UGX <?= number_format($week_rev,0) ?></p><a href="view_services.php?period=weekly">View Details</a></div>
<div class="card"><h3>Monthly</h3><p>UGX <?= number_format($month_rev,0) ?></p><a href="view_services.php?period=monthly">View Details</a></div>
<div class="card"><h3>Quarterly</h3><p>UGX <?= number_format($quarter_rev,0) ?></p><a href="view_services.php?period=quarterly">View Details</a></div>
<div class="card"><h3>Annual</h3><p>UGX <?= number_format($year_rev,0) ?></p><a href="view_services.php?period=annual">View Details</a></div>
</div>

<hr>

<h3 style="text-align:center; color:#7a1a1a;">💸 Expenses Overview</h3>
<div class="summary-cards">
<div class="card"><h3>Daily</h3><p>UGX <?= number_format($today_exp,0) ?></p><a href="view_expenses.php?period=daily">View Details</a></div>
<div class="card"><h3>Weekly</h3><p>UGX <?= number_format($week_exp,0) ?></p><a href="view_expenses.php?period=weekly">View Details</a></div>
<div class="card"><h3>Monthly</h3><p>UGX <?= number_format($month_exp,0) ?></p><a href="view_expenses.php?period=monthly">View Details</a></div>
<div class="card"><h3>Quarterly</h3><p>UGX <?= number_format($quarter_exp,0) ?></p><a href="view_expenses.php?period=quarterly">View Details</a></div>
<div class="card"><h3>Annual</h3><p>UGX <?= number_format($year_exp,0) ?></p><a href="view_expenses.php?period=annual">View Details</a></div>
</div>

<hr>

<h3 style="text-align:center; color:#023020;">🚘 Vehicles Serviced Overview</h3>
<div class="summary-cards">
<div class="card"><h3>Daily</h3><p><?= $today_veh ?></p></div>
<div class="card"><h3>Weekly</h3><p><?= $week_veh ?></p></div>
<div class="card"><h3>Monthly</h3><p><?= $month_veh ?></p></div>
<div class="card"><h3>Quarterly</h3><p><?= $quarter_veh ?></p></div>
<div class="card"><h3>Annual</h3><p><?= $year_veh ?></p></div>
</div>

<hr>
<div class="chart-container">
<h3 style="text-align:center; color:#023020;">🚗 Revenue Vs Expenses</h3>
<canvas id="revenueChart"></canvas>
</div>
<hr>
<div class="chart-container">
<h3 style="text-align:center; color:#023020;">🚗 Vehicles Serviced Per Year</h3>
<canvas id="vehiclesYearChart"></canvas>
</div>
<hr>
<script>
const ctx = document.getElementById('revenueChart').getContext('2d');
const revenueChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Daily','Weekly','Monthly','Quarterly','Annual'],
        datasets: [
            { label:'Revenue', data:[<?= $today_rev ?>,<?= $week_rev ?>,<?= $month_rev ?>,<?= $quarter_rev ?>,<?= $year_rev ?>], backgroundColor:'#0078D7' },
            { label:'Expenses', data:[<?= $today_exp ?>,<?= $week_exp ?>,<?= $month_exp ?>,<?= $quarter_exp ?>,<?= $year_exp ?>], backgroundColor:'#7a1a1a' }
        ]
    },
    options: { responsive:true, plugins:{ legend:{ position:'top' } }, scales:{ y:{ beginAtZero:true } } }
});
</script>
<script>
const ctxYear = document.getElementById('vehiclesYearChart').getContext('2d');
const vehiclesYearChart = new Chart(ctxYear, {
    type: 'bar',
    data: {
        labels: <?= $vehicle_year_labels ?>,
        datasets: [{
            label: 'Vehicles Serviced',
            data: <?= $vehicle_year_data ?>,
            backgroundColor: '#28a745'
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'top' } },
        scales: {
            y: { beginAtZero: true, title: { display: true, text: 'Number of Vehicles' } },
            x: { title: { display: true, text: 'Year' } }
        }
    }
});
</script>

</div>

<?php else: ?>

<div class="dashboard-container">
<hr>
<h3 style="text-align:center; color:#023020;">💵 Your Revenue</h3>
<div class="summary-cards">
<div class="card"><h3>Daily</h3><p>UGX <?= number_format($user_today,0) ?></p></div>
<div class="card"><h3>Weekly</h3><p>UGX <?= number_format($user_week,0) ?></p></div>
<div class="card"><h3>Monthly</h3><p>UGX <?= number_format($user_month,0) ?></p></div>
<div class="card"><h3>Quarterly</h3><p>UGX <?= number_format($user_quarter,0) ?></p></div>
<div class="card"><h3>Annual</h3><p>UGX <?= number_format($user_year,0) ?></p></div>
</div>

<hr>
<h3 style="text-align:center; color:#023020;">🚘 Vehicles Serviced & Total Records</h3>
<div class="summary-cards">
<div class="card"><h3>Total Records</h3><p><?= $user_total ?></p></div>
<div class="card"><h3>Unique Vehicles</h3><p><?= $user_vehicles ?></p></div>
</div>

<hr>

<h3 style="text-align:center; color:#023020;">📝 Latest Services</h3>
<table>
<tr><th>Date</th><th>Vehicle Number</th><th>Service</th><th>Price (UGX)</th></tr>
<?php while($row = $user_latest->fetch_assoc()): ?>
<tr>
<td><?= htmlspecialchars($row['date']); ?></td>
<td><?= htmlspecialchars($row['vehicle_number']); ?></td>
<td><?= htmlspecialchars($row['service_type']); ?></td>
<td><?= number_format((float)$row['price'],0); ?></td>
</tr>
<?php endwhile; ?>
</table>

</div>

<?php endif; ?>

</main>

<footer>
&copy; <?= date('Y'); ?> AUTO Detail Car Wash. All Rights Reserved.
</footer>

</body>
</html>
