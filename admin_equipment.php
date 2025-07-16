<?php
// admin_equipment.php
session_start();
if (!isset($_SESSION['User_id']) || $_SESSION['User_type'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

require 'database.php';

// Fetch all equipment
$query = "SELECT * FROM equipment";
$result = mysqli_query($conn, $query);
$equipment = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Equipment Management</title>
    <link rel="stylesheet" href="admin_styles.css">
    <style>
        .availability-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .available {
            background-color: #d4edda;
            color: #155724;
        }
        
        .unavailable {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <?php include 'admin_sidebar.php'; ?>
        
        <div class="main-content">
            <div class="header">
                <h1>Equipment Management</h1>
                <a href="add_equipment.php" class="btn btn-primary">Add New Equipment</a>
            </div>
            
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Equipment ID</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Daily Rate</th>
                        <th>Weekly Rate</th>
                        <th>Monthly Rate</th>
                        <th>Availability</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($equipment as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['EquipmentID']) ?></td>
                        <td><?= htmlspecialchars($item['EquipmentName']) ?></td>
                        <td><?= htmlspecialchars($item['Category']) ?></td>
                        <td>₱<?= number_format($item['DailyPrice'], 2) ?></td>
                        <td>₱<?= number_format($item['WeeklyPrice'], 2) ?></td>
                        <td>₱<?= number_format($item['MonthlyPrice'], 2) ?></td>
                        <td>
                            <span class="availability-badge <?= strtolower($item['Availability']) ?>">
                                <?= htmlspecialchars($item['Availability']) ?>
                            </span>
                        </td>
                        <td>
                            <a href="edit_equipment.php?id=<?= $item['EquipmentID'] ?>" class="btn btn-primary btn-sm">Edit</a>
                            <a href="delete_equipment.php?id=<?= $item['EquipmentID'] ?>" 
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Are you sure you want to delete this equipment?')">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>