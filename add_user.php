<?php
require 'database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $Email = $_POST['Email'];
    $FullName = $_POST['FullName'];
    $Username = $_POST['Username'];
    $UserType = $_POST['UserType'];
    $Password = password_hash($_POST['Password'], PASSWORD_DEFAULT); // Securely hash password

    $stmt = $conn->prepare("INSERT INTO user (Email, FullName, Username, UserType, Password) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $Email, $FullName, $Username, $UserType, $Password);
    $stmt->execute();
}

header("Location: admin_users.php");
exit();
