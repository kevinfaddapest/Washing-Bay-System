<?php
include('config.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Only admins
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

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
logActivity($_SESSION['user_id'], $_SESSION['username'], "Viewed Reports Page");

// ======= FUNCTIONS =======
function getTotal($conn, $table, $column, $condition) {
    $sql = "SELECT SUM($column) AS total FROM $table WHERE $condition";
    $res = $conn->query($sql);
    $r = $res->fetch_assoc();
    return (float)($r['total'] ?? 0);
}

// ======= PERIOD DEFINITIONS (FIXED TO MATCH DASHBOARD) =======
$periods = [
    'daily'     => "DATE(date)=CURDATE()",
    'weekly'    => "YEARWEEK(date,1)=YEARWEEK(CURDATE(),1)",
    'monthly'   => "YEAR(date)=YEAR(CURDATE()) AND MONTH(date)=MONTH(CURDATE())",
    'quarterly' => "YEAR(date)=YEAR(CURDATE()) AND QUARTER(date)=QUARTER(CURDATE())",
    'annual'    => "YEAR(date)=YEAR(CURDATE())"
];

// ======= FETCH DATA =======
$revenue = $expense = $profit = $vehicles = [];

foreach ($periods as $label => $where) {
    $revenue[$label] = getTotal($conn, 'services', 'price', $where);
    $expense[$label] = getTotal($conn, 'expenses', 'amount', $where);
    $profit[$label]  = $revenue[$label] - $expense[$label];

    // ✅ VEHICLES = TOTAL RECORDS (MATCH DASHBOARD)
    $vehicles[$label] = (int)($conn->query("
        SELECT COUNT(*) AS v FROM services WHERE $where
    ")->fetch_assoc()['v'] ?? 0);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Reports - AUTO Detail Car Wash</title>
<link rel="stylesheet" href="style.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
/* Styles consistent with other pages */
body { font-family: 'Poppins', sans-serif; background: #f5f7fa; margin: 0; }
header { background-color: #023020; color: white; padding: 20px; display: flex; justify-content: space-between; align-items: center; }
header h1 { margin: 0; font-size: 1.4rem; }
header nav a { color: white; margin-right: 15px; text-decoration: none; font-weight: 600; padding: 6px 12px; border-radius: 5px; transition: 0.3s; }
header nav a:hover { background: rgba(255,255,255,0.2); }
main { padding: 20px; }
section { background: white; padding: 20px; border-radius: 10px; margin-bottom: 40px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
h2 { color: #023020; text-align: center; margin-bottom: 20px; }
table { width: 100%; border-collapse: collapse; text-align: center; }
th, td { border: 1px solid #ddd; padding: 10px; }
th { background: #0078D7; color: white; }
tr:nth-child(even) { background: #f9f9f9; }
hr { border: 1px solid black; margin: 40px 0; color: black; }
.view-link { color: #0078D7; text-decoration: none; font-weight: 600; }
.view-link:hover { text-decoration: underline; }
.chart-container { width: 90%; margin: 40px auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
footer { text-align: center; padding: 15px; background: #0078D7; color: white; margin-top: 50px; }
</style>
</head>
<body>
<header>
  <h1>📊 AUTO Detail Car Wash - Reports</h1>
  <nav>
    <a href="dashboard.php">🏠 Dashboard</a>
    <a href="view_services.php">📋 Records</a>
    <a href="view_expenses.php">💰 Expenses</a>
    <a href="users.php">👥 Users</a>
    <a href="logout.php">🚪 Logout</a>
  </nav>
</header>

<main>
<hr>
<section>
  <h2>💵 Revenue, Expenses, and Profit Summary</h2>
  <table>
    <thead>
      <tr>
        <th>Period</th>
        <th>Revenue (UGX)</th>
        <th>Expenses (UGX)</th>
        <th>Profit (UGX)</th>
        <th>Vehicles</th>
        <th>Details</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($periods as $p => $where): ?>
      <tr>
        <td><?= ucfirst($p); ?></td>
        <td><?= number_format($revenue[$p], 0); ?></td>
        <td><?= number_format($expense[$p], 0); ?></td>
        <td style="color:<?= $profit[$p] >= 0 ? 'green':'red'; ?>">
          <?= number_format($profit[$p], 0); ?>
        </td>
        <td><?= $vehicles[$p]; ?></td>
        <td>
          <a class="view-link" href="view_services.php?period=<?= $p; ?>">View Revenue</a> |
          <a class="view-link" href="view_expenses.php?period=<?= $p; ?>">View Expenses</a> |
          <a class="view-link" href="view_services.php?type=vehicles&period=<?= $p; ?>">View Vehicles</a>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</section>

<hr>
<div class="chart-container">
  <h2>📈 Revenue vs Expenses (Annual Trend)</h2>
  <canvas id="revExpChart"></canvas>
</div>
<hr>
</main>

<footer>
  <p>© <?= date('Y'); ?> AUTO Detail Car Wash | Designed for simplicity & performance 🚘</p>
</footer>

<script>
const ctx = document.getElementById('revExpChart');
if(ctx){
  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: ['Daily','Weekly','Monthly','Quarterly','Annual'],
      datasets: [{
        label: 'Revenue',
        data: [<?= implode(',', $revenue); ?>],
        backgroundColor: '#0078D7'
      },{
        label: 'Expenses',
        data: [<?= implode(',', $expense); ?>],
        backgroundColor: '#D9534F'
      }]
    },
    options: {
      responsive: true,
      scales: { y: { beginAtZero: true } }
    }
  });
}
</script>

</body>
</html>
