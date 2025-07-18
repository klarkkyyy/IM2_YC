<?php
session_start();
require 'database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['User_id'])) {
    $client_id = intval($_POST['client_id']);
    $query = "UPDATE project_updates u
              JOIN projects p ON u.project_id = p.project_id
              SET u.is_read = 1
              WHERE p.client_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $client_id);
    mysqli_stmt_execute($stmt);
}
?>