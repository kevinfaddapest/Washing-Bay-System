<?php
include('config.php');

$sql = "SELECT service_name, price FROM service_types";
$result = $conn->query($sql);

$services = [];
while ($row = $result->fetch_assoc()) {
    $services[] = $row;
}

echo json_encode($services);
?>
