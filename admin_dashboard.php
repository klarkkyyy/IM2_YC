<?php
// admin_dashboard.php
session_start();
if (!isset($_SESSION['User_id']) || $_SESSION['User_type'] !== 'Admin') {
    header("Location: login.php");
    exit();
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
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }
        
        .admin-container {
            display: flex;
            min-height: 100vh;
        }
        
        /* Sidebar Styles */
        .sidebar {
            width: 250px;
            background-color: var(--primary-color);
            color: white;
            padding: 20px 0;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        }
        
        .sidebar-header {
            padding: 0 20px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar-nav {
            padding: 20px 0;
        }
        
        .nav-item {
            padding: 10px 20px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .nav-item:hover, .nav-item.active {
            background-color: var(--secondary-color);
        }
        
        .nav-item a {
            color: white;
            text-decoration: none;
            display: block;
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
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h2>Admin Panel</h2>
                <p>Welcome, <?php echo htmlspecialchars($_SESSION['Username']); ?></p>
            </div>
            <div class="sidebar-nav">
                <div class="nav-item active">
                    <a href="admin_dashboard.php">Dashboard</a>
                </div>
                <div class="nav-item">
                    <a href="admin_users.php">User Management</a>
                </div>
                <div class="nav-item">
                    <a href="admin_projects.php">Project Management</a>
                </div>
                <div class="nav-item">
                    <a href="admin_equipment.php">Equipment Management</a>
                </div>
                <div class="nav-item">
                    <a href="admin_reports.php">Reports</a>
                </div>
                <div class="nav-item">
                    <a href="logout.php">Logout</a>
                </div>
            </div>
        </div>
        
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
                    <div class="value">147</div>
                    <p>5 new this week</p>
                </div>
                <div class="card">
                    <h3>Active Projects</h3>
                    <div class="value">24</div>
                    <p>3 completed this month</p>
                </div>
                <div class="card">
                    <h3>Equipment Available</h3>
                    <div class="value">58</div>
                    <p>12 currently rented</p>
                </div>
                <div class="card">
                    <h3>Revenue</h3>
                    <div class="value success">₱1,245,000</div>
                    <p>15% increase from last month</p>
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
</body>
</html>