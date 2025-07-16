<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['UserType'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

require 'database.php';

// Check and sanitize the ID
$id = $_GET['id'] ?? null;
if ($id === null || !is_numeric($id)) {
    header("Location: admin_equipment.php?error=InvalidID");
    exit();
}

// Check if equipment exists
$stmt = $conn->prepare("SELECT * FROM equipment WHERE EquipmentID = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $stmt->close();
    $conn->close();
    header("Location: admin_equipment.php?error=NotFound");
    exit();
}
$stmt->close();

// Perform the delete
$stmt = $conn->prepare("DELETE FROM equipment WHERE EquipmentID = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();

$conn->close();

header("Location: admin_equipment.php?deleted=1");
exit();
?>
