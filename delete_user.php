<?php
require 'database.php';

if (isset($_GET['id'])) {
    $userID = intval($_GET['id']);

    $stmt = $conn->prepare("DELETE FROM user WHERE UserID = ?");
    $stmt->bind_param("i", $userID);
    $stmt->execute();
}

header("Location: admin_users.php");
exit();