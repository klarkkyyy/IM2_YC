<?php
session_start();
require 'database.php'; // Make sure this file exists in the same folder

// Check if user is logged in
if (!isset($_SESSION['User_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['User_id'];

// Fetch projects assigned to this user
$sql = "SELECT * FROM project WHERE ApplicationID IN (
            SELECT ApplicationID 
            FROM application 
            WHERE ClientID = (SELECT ClientID FROM client WHERE UserID = ?)
        )";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("SQL Prepare failed: " . $conn->error);
}

$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Client Projects</title>
    <link rel="stylesheet" href="client_interface.css"> 
</head>
<body>
    <?php include 'client_navbar.php'; ?> 

    <section class="content">
        <h1>Your Projects</h1>

        <?php if ($result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Project ID</th>
                        <th>Project Name</th>
                        <th>Description</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['Project_id']); ?></td>
                            <td><?php echo htmlspecialchars($row['Project_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['Description']); ?></td>
                            <td><?php echo htmlspecialchars($row['Status']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No projects found.</p>
        <?php endif; ?>

    </section>
</body>
</html>
