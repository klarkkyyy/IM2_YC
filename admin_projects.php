<?php
session_start();
if (!isset($_SESSION['User_id']) || $_SESSION['User_type'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

require 'database.php';

// Enhanced error handling function
function executeQuery($conn, $query) {
    $result = mysqli_query($conn, $query);
    if (!$result) {
        die("Query failed: " . mysqli_error($conn) . "<br>Full Query: " . $query);
    }
    return $result;
}

// Fetch all projects with proper joins and error handling
$query = "SELECT 
            p.ProjectID, 
            p.StartDate, 
            p.EndDate, 
            p.Status, 
            pp.ConstructionType, 
            pp.ProjectLocation,
            pp.Description,
            c.CompanyName AS ClientName,
            u.FullName AS ClientContact
          FROM project p
          JOIN projectproposal pp ON p.ProposalID = pp.ProposalID
          JOIN application a ON p.ApplicationID = a.ApplicationID
          JOIN client c ON a.ClientID = c.ClientID
          JOIN user u ON c.UserID = u.UserID
          ORDER BY p.StartDate DESC";

$result = executeQuery($conn, $query);
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
        :root {
            --primary-color: #004AAD;
            --secondary-color: #003080;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
        }
        
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
            display: inline-block;
            min-width: 80px;
            text-align: center;
        }
        
        .status-ongoing {
            background-color: #cce5ff;
            color: #004085;
        }
        
        .status-completed {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-cancelled {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .card {
            background-color: white;
            border-radius: 5px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .form-control {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-bottom: 10px;
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
        
        .btn-warning {
            background-color: var(--warning-color);
            color: var(--dark-color);
        }
        
        .btn-sm {
            padding: 5px 10px;
            font-size: 0.8rem;
        }
        
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
        
        .filters {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        
        /* Footer Styles */
        footer {
            background-color: var(--primary-color);
            color: white;
            padding: 20px;
            text-align: center;
            margin-top: auto;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <?php include 'admin_sidebar.php'; ?>
        
        <div class="main-content">
            <div class="header">
                <h1>Project Management</h1>
                <a href="admin_add_project.php" class="btn btn-primary">Add New Project</a>
            </div>
            
            <div class="card">
                <div class="filters">
                    <div>
                        <label>Status:</label>
                        <select class="form-control" id="status-filter">
                            <option value="">All Statuses</option>
                            <option value="Ongoing">Ongoing</option>
                            <option value="Completed">Completed</option>
                            <option value="Cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div>
                        <label>Construction Type:</label>
                        <select class="form-control" id="type-filter">
                            <option value="">All Types</option>
                            <option value="Residential">Residential</option>
                            <option value="Commercial">Commercial</option>
                            <option value="Flood Control">Flood Control</option>
                            <option value="Road Construction">Road Construction</option>
                        </select>
                    </div>
                    <div>
                        <label>Search:</label>
                        <input type="text" class="form-control" id="search-filter" placeholder="Search projects...">
                    </div>
                    <div>
                        <label>&nbsp;</label>
                        <button class="btn btn-primary" style="width: 100%;" onclick="filterProjects()">Filter</button>
                    </div>
                </div>
                
                <table class="data-table" id="projects-table">
                    <thead>
                        <tr>
                            <th>Project ID</th>
                            <th>Project Name</th>
                            <th>Client</th>
                            <th>Location</th>
                            <th>Status</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($projects as $project): ?>
                        <tr>
                            <td><?= htmlspecialchars($project['ProjectID']) ?></td>
                            <td><?= htmlspecialchars($project['ConstructionType'] ?? 'N/A') ?> Project</td>
                            <td>
                                <?= htmlspecialchars($project['ClientName'] ?? 'N/A') ?>
                                <?php if (!empty($project['ClientContact'])): ?>
                                    <br><small><?= htmlspecialchars($project['ClientContact']) ?></small>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($project['ProjectLocation'] ?? 'N/A') ?></td>
                            <td>
                                <span class="status-badge status-<?= strtolower($project['Status']) ?>">
                                    <?= htmlspecialchars($project['Status']) ?>
                                </span>
                            </td>
                            <td><?= date('M j, Y', strtotime($project['StartDate'])) ?></td>
                            <td><?= $project['EndDate'] ? date('M j, Y', strtotime($project['EndDate'])) : 'Ongoing' ?></td>
                            <td>
                            <a href="admin_view_project.php?id=<?= $project['ProjectID'] ?>" class="btn btn-primary btn-sm">View</a>
                            <a href="admin_edit_project.php?id=<?= $project['ProjectID'] ?>" class="btn btn-warning btn-sm">Edit</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <script>
        // Simple client-side filtering
        function filterProjects() {
            const statusFilter = document.getElementById('status-filter').value.toLowerCase();
            const typeFilter = document.getElementById('type-filter').value.toLowerCase();
            const searchFilter = document.getElementById('search-filter').value.toLowerCase();
            
            const rows = document.querySelectorAll('#projects-table tbody tr');
            
            rows.forEach(row => {
                const status = row.querySelector('.status-badge').textContent.toLowerCase();
                const type = row.cells[1].textContent.toLowerCase();
                const rowText = row.textContent.toLowerCase();
                
                const statusMatch = !statusFilter || status.includes(statusFilter);
                const typeMatch = !typeFilter || type.includes(typeFilter);
                const searchMatch = !searchFilter || rowText.includes(searchFilter);
                
                row.style.display = (statusMatch && typeMatch && searchMatch) ? '' : 'none';
            });
        }
        
        // Initialize filters
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('search-filter').addEventListener('keyup', filterProjects);
        });
    </script>
    
    <?php include 'footer.php'; ?>
</body>
</html>