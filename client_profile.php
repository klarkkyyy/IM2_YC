<?php
// client_profile.php
session_start();
if (!isset($_SESSION['User_id']) || $_SESSION['User_type'] !== 'Client') {
    header("Location: login.php");
    exit();
}

require 'database.php';

$userId = $_SESSION['User_id'];
$query = "SELECT Full_name, Email, Username, User_type FROM users WHERE User_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Profile</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .profile-container {
            max-width: 600px;
            margin: 80px auto;
            padding: 30px;
            border-radius: 15px;
            background-color: #f9f9f9;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .profile-container h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .profile-container p {
            font-size: 18px;
            margin: 10px 0;
        }

        .profile-container button {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }

        .profile-container button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="profile-container">
        <h2>My Profile</h2>
        <p><strong>Full Name:</strong> <?php echo htmlspecialchars($user['Full_name']); ?></p>
        <p><strong>Email Address:</strong> <?php echo htmlspecialchars($user['Email']); ?></p>
        <p><strong>Username:</strong> <?php echo htmlspecialchars($user['Username']); ?></p>
        <p><strong>User Type:</strong> <?php echo htmlspecialchars($user['User_type']); ?></p>
        
        <form action="change_password.php" method="get">
            <button type="submit">Change Password</button>
        </form>
    </div>
</body>
</html>
