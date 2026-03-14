<?php
include('config.php');
if (!isset($_GET['service'])) exit;
$service = $_GET['service'];
$q = $conn->query("SELECT price FROM service_types WHERE service_name='$service'");
if ($q->num_rows > 0) {
    $row = $q->fetch_assoc();
    echo $row['price'];
}
?>
