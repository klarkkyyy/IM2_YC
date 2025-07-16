<?php

session_start();
if (!isset($_SESSION['User_id']) || $_SESSION['User_type'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

require 'database.php';

// Display recent activities with SELECT
$activities = [];
$activitySql = "SELECT ActivityDate, ActivityType, Username, Details FROM recent_activity ORDER BY ActivityDate DESC LIMIT 10";
$activityResult = mysqli_query($conn, $activitySql);

if ($activityResult && mysqli_num_rows($activityResult) > 0) {
    while ($row = mysqli_fetch_assoc($activityResult)) {
        $activities[] = $row;
    }
}


// Counting users that are clients with COUNT(*)
$totalClients = 0;
$sql = "SELECT COUNT(*) AS total FROM user WHERE UserType = 'Client'";
$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $totalClients = $row['total'];
}

// Count available equipments with SELECT
$totalAvailableEquipment = 0;
$equipmentQuery = "SELECT COUNT(*) AS total FROM equipment WHERE Availability = 'Available'";
$equipmentResult = mysqli_query($conn, $equipmentQuery);

if ($equipmentResult && mysqli_num_rows($equipmentResult) > 0) {
    $row = mysqli_fetch_assoc($equipmentResult);
    $totalAvailableEquipment = $row['total'];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        :root {
            --primary-color: #004AAD;
            --secondary-color: #003080;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
        }
        
        html, body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        
        body {
            flex: 1;
        }
        .admin-container {
            display: flex;
            gap: 20px;
            flex: 1;
        }

        
        
        
        /* Main Content Styles */
        .main-content {
            flex: 1;
            padding: 20px;
        }
        
        .header {
            background-color: white;
            padding: 15px 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .card {
            background-color: white;
            border-radius: 5px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .card h3 {
            margin-top: 0;
            color: var(--dark-color);
        }
        
        .card .value {
            font-size: 2rem;
            font-weight: bold;
            margin: 10px 0;
        }
        
        .card .success {
            color: var(--success-color);
        }
        
        .card .danger {
            color: var(--danger-color);
        }
        
        .card .warning {
            color: var(--warning-color);
        }
        
        /* Table Styles */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            border-radius: 5px;
            overflow: hidden;
        }
        
        .data-table th, .data-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        .data-table th {
            background-color: var(--primary-color);
            color: white;
        }
        
        .data-table tr:hover {
            background-color: #f5f5f5;
        }
        
        .btn {
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }
        
        .btn-danger {
            background-color: var(--danger-color);
            color: white;
        }
        
        .btn-success {
            background-color: var(--success-color);
            color: white;
        }
        
        .btn-sm {
            padding: 5px 10px;
            font-size: 0.8rem;
        }

        footer {
            background-color: #004AAD;
            color: white;
            display: flex;
            justify-content: space-between;
            padding: 20px;
            margin-bottom: auto;
        }

        .footer-section {
            flex: 1;
            padding: 10px;
        }

        .footer-section h3 {
            margin-top: 0;
            font-size: 1.2em;
        }

        .footer-section p {
            margin: 5px 0;
        }

        .social-icons {
            display: flex;
            justify-content: center;
            margin-top: 10px;
        }

        .social-icons a {
            color: white;
            margin: 0 10px;
            text-decoration: none;
            display: flex;
            align-items: center;
        }

        .social-icons a img {
            height: 20px;
            margin-right: 5px;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <?php include 'admin_sidebar.php'; ?>
        
        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <h1>Dashboard Overview</h1>
                <div class="user-info">
                    <span><?php echo date('F j, Y'); ?></span>
                </div>
            </div>
            
            <!-- Dashboard Cards -->
            <div class="dashboard-cards">
                <div class="card">
                    <h3>Total Users</h3>
                    <div class="value"><?php echo $totalClients; ?></div>
                </div>
                <div class="card">
                    <h3>Active Projects</h3>
                    <div class="value">24</div>
                </div>
                <div class="card">
                    <h3>Equipment Available</h3>
                    <div class="value"><?php echo $totalAvailableEquipment; ?></div>
                </div>
            </div>
            
            <!-- Recent Activity Table -->
            <h2>Recent Activity</h2>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Activity</th>
                        <th>User</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>2023-06-15</td>
                        <td>New Project Created</td>
                        <td>John Doe</td>
                        <td>Road Construction Project</td>
                    </tr>
                    <tr>
                        <td>2023-06-14</td>
                        <td>Equipment Rental</td>
                        <td>Jane Smith</td>
                        <td>Excavator rented for 1 week</td>
                    </tr>
                    <tr>
                        <td>2023-06-13</td>
                        <td>User Registered</td>
                        <td>New Client</td>
                        <td>Client account created</td>
                    </tr>
                    <tr>
                        <td>2023-06-12</td>
                        <td>Project Completed</td>
                        <td>Admin</td>
                        <td>Residential Building Project</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>