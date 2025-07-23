<?php
session_start();
require 'database.php';

if (!isset($_SESSION['User_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['User_id'];

$stmt = $conn->prepare("SELECT ClientID FROM client WHERE UserID = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$stmt->bind_result($clientId);

$hasClient = $stmt->fetch();
$stmt->close();

if (!$hasClient) {
    echo "<p style='color:red; text-align:center;'>No client found for this user ID. Please contact admin.</p>";
    $clientId = null;  // prevent further errors
}

$sql = "SELECT p.ProjectID, p.Status, p.StartDate, p.EndDate
        FROM project p
        INNER JOIN application a ON p.ApplicationID = a.ApplicationID
        INNER JOIN client c ON a.ClientID = c.ClientID
        WHERE c.UserID = ?";

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
    <?php include 'navbar.php'; ?> 

    <section class="content">
        <h1>Your Projects</h1>

        <?php if ($result && $result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Project ID</th>
                        <th>Status</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['ProjectID']) ?></td>
                            <td><?= htmlspecialchars($row['Status']) ?></td>
                            <td><?= htmlspecialchars($row['StartDate']) ?></td>
                            <td><?= htmlspecialchars($row['EndDate']) ?></td>
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
