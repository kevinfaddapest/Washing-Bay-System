<?php
include('config.php');
if (session_status() == PHP_SESSION_NONE) session_start();

$id = $_GET['id'] ?? '';
if ($id) {
    $id = (int)$id;
    
    if ($_SESSION['role'] === 'admin') {
        $res = $conn->query("SELECT * FROM services WHERE id=$id");
    } else {
        $user = $_SESSION['user']; // staff username
        $res = $conn->query("SELECT * FROM services WHERE id=$id AND added_by='$user'");
    }

    if ($res && $res->num_rows > 0) {
        echo json_encode($res->fetch_assoc());
    } else {
        echo json_encode(['error' => 'Record not found or access denied']);
    }
}
?>
