<?php
session_start();
require 'database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['User_id'])) {
    $update_id = intval($_POST['update_id']);
    $query = "UPDATE project_updates SET is_read = 1 WHERE update_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $update_id);
    mysqli_stmt_execute($stmt);
}
?>