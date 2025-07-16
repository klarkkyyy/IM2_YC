<?php
// admin_users.php
session_start();
if (!isset($_SESSION['User_id']) || $_SESSION['User_type'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

require 'database.php';

// Fetch all users
$query = "SELECT UserID, Username, Email, FullName, UserType, RegistrationDate FROM user";
$result = mysqli_query($conn, $query);
$users = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <!-- Use the same styles as admin_dashboard.php -->
    <link rel="stylesheet" href="admin_styles.css">
</head>
<body>
    <div class="admin-container">
        <!-- Include the same sidebar -->
        <?php include 'admin_sidebar.php'; ?>
        
        <div class="main-content">
            <div class="header">
                <h1>User Management</h1>
                <a href="add_user.php" class="btn btn-primary">Add New User</a>
            </div>
            
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>User Type</th>
                        <th>Registration Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['UserID']) ?></td>
                        <td><?= htmlspecialchars($user['Username']) ?></td>
                        <td><?= htmlspecialchars($user['FullName']) ?></td>
                        <td><?= htmlspecialchars($user['Email']) ?></td>
                        <td>
                            <span class="badge <?= strtolower($user['UserType']) ?>">
                                <?= htmlspecialchars($user['UserType']) ?>
                            </span>
                        </td>
                        <td><?= date('M j, Y', strtotime($user['RegistrationDate'])) ?></td>
                        <td>
                            <a href="edit_user.php?id=<?= $user['UserID'] ?>" class="btn btn-primary btn-sm">Edit</a>
                            <a href="delete_user.php?id=<?= $user['UserID'] ?>" 
                               class="btn btn-danger btn-sm" 
                               onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>