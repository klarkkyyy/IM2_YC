<?php
// admin_projects.php
session_start();
if (!isset($_SESSION['User_id']) || $_SESSION['User_type'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

require 'database.php';

// Fetch all projects
$query = "SELECT p.project_id, p.project_name, p.description, p.status, 
                 p.start_date, p.end_date, u.FullName as client_name
          FROM projects p
          LEFT JOIN user u ON p.client_id = u.UserID";
$result = mysqli_query($conn, $query);
$projects = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Management</title>
    <link rel="stylesheet" href="admin_styles.css">
    <style>
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .status-in-progress {
            background-color: #cce5ff;
            color: #004085;
        }
        
        .status-completed {
            background-color: #d4edda;
            color: #155724;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <?php include 'admin_sidebar.php'; ?>
        
        <div class="main-content">
            <div class="header">
                <h1>Project Management</h1>
                <a href="add_project.php" class="btn btn-primary">Add New Project</a>
            </div>
            
            <div class="filters">
                <select>
                    <option>All Statuses</option>
                    <option>Pending</option>
                    <option>In Progress</option>
                    <option>Completed</option>
                </select>
                <input type="text" placeholder="Search projects...">
                <button class="btn btn-primary">Filter</button>
            </div>
            
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Project ID</th>
                        <th>Project Name</th>
                        <th>Client</th>
                        <th>Status</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($projects as $project): ?>
                    <tr>
                        <td><?= htmlspecialchars($project['project_id']) ?></td>
                        <td><?= htmlspecialchars($project['project_name']) ?></td>
                        <td><?= htmlspecialchars($project['client_name'] ?? 'N/A') ?></td>
                        <td>
                            <span class="status-badge status-<?= strtolower(str_replace(' ', '-', $project['status'])) ?>">
                                <?= htmlspecialchars($project['status']) ?>
                            </span>
                        </td>
                        <td><?= date('M j, Y', strtotime($project['start_date'])) ?></td>
                        <td><?= $project['end_date'] ? date('M j, Y', strtotime($project['end_date'])) : 'Ongoing' ?></td>
                        <td>
                            <a href="view_project.php?id=<?= $project['project_id'] ?>" class="btn btn-primary btn-sm">View</a>
                            <a href="edit_project.php?id=<?= $project['project_id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>