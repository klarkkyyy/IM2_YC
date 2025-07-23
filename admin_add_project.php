<?php
session_start();
if (!isset($_SESSION['User_id']) || $_SESSION['User_type'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

require 'database.php';

// Initialize variables
$errors = [];
$success = false;

// Form submission handling
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $constructionType = $_POST['construction_type'] ?? '';
    $projectLocation = $_POST['project_location'] ?? '';
    $terrain = $_POST['terrain'] ?? '';
    $estimatedBudget = $_POST['estimated_budget'] ?? 0;
    $startDate = $_POST['start_date'] ?? '';
    $endDate = $_POST['end_date'] ?? null;
    $clientId = $_POST['client_id'] ?? null;
    $description = $_POST['description'] ?? '';

    // Validate inputs
    if (empty($constructionType)) {
        $errors[] = "Construction type is required";
    }
    if (empty($projectLocation)) {
        $errors[] = "Project location is required";
    }
    if (empty($startDate)) {
        $errors[] = "Start date is required";
    }
    if (!is_numeric($estimatedBudget) || $estimatedBudget <= 0) {
        $errors[] = "Valid estimated budget is required";
    }
    if (empty($clientId)) {
        $errors[] = "Client selection is required";
    }

    // If no errors, proceed with database operations
    if (empty($errors)) {
        try {
            $conn->begin_transaction();

            // 1. Create project proposal
            $proposalQuery = "INSERT INTO projectproposal 
                            (ConstructionType, ProjectLocation, Terrain, EstimatedBudget)
                            VALUES (?, ?, ?, ?)";
            $proposalStmt = $conn->prepare($proposalQuery);
            if (!$proposalStmt) {
                throw new Exception("Prepare failed (proposal): " . $conn->error);
            }
            $proposalStmt->bind_param("sssd", $constructionType, $projectLocation, $terrain, $estimatedBudget);
            if (!$proposalStmt->execute()) {
                throw new Exception("Execute failed (proposal): " . $proposalStmt->error);
            }
            $proposalId = $conn->insert_id;

            // 2. Create application - using ClientID from client table
            $applicationQuery = "INSERT INTO application 
                               (ClientID, ApplicationType, Description, SubmissionDate, Status)
                               VALUES (?, 'Project', ?, NOW(), 'Approved')";
            $applicationStmt = $conn->prepare($applicationQuery);
            if (!$applicationStmt) {
                throw new Exception("Prepare failed (application): " . $conn->error);
            }
            $applicationStmt->bind_param("is", $clientId, $description);
            if (!$applicationStmt->execute()) {
                throw new Exception("Execute failed (application): " . $applicationStmt->error);
            }
            $applicationId = $conn->insert_id;

            // 3. Create project
            $projectQuery = "INSERT INTO project 
                           (ApplicationID, ProposalID, StartDate, EndDate, Status, CurrentBalance, IsFullyPaid)
                           VALUES (?, ?, ?, ?, 'Ongoing', ?, 0)";
            $projectStmt = $conn->prepare($projectQuery);
            if (!$projectStmt) {
                throw new Exception("Prepare failed (project): " . $conn->error);
            }
            $projectStmt->bind_param("iissd", $applicationId, $proposalId, $startDate, $endDate, $estimatedBudget);
            if (!$projectStmt->execute()) {
                throw new Exception("Execute failed (project): " . $projectStmt->error);
            }
            $projectId = $conn->insert_id;

            // Log activity
            $activityQuery = "INSERT INTO recent_activity 
                            (ActivityDate, ActivityType, Username, Details)
                            VALUES (NOW(), 'Project Creation', ?, ?)";
            $activityStmt = $conn->prepare($activityQuery);
            if (!$activityStmt) {
                throw new Exception("Prepare failed (activity): " . $conn->error);
            }
            $username = $_SESSION['Username'];
            $details = "Created new project: $constructionType at $projectLocation";
            $activityStmt->bind_param("ss", $username, $details);
            if (!$activityStmt->execute()) {
                throw new Exception("Execute failed (activity): " . $activityStmt->error);
            }

            $conn->commit();
            $success = true;
        } catch (Exception $e) {
            $conn->rollback();
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
}

// Fetch clients for dropdown - properly joining user and client tables
$clients = [];
$clientsQuery = "SELECT c.ClientID, u.FullName, c.CompanyName 
                FROM client c
                JOIN user u ON c.UserID = u.UserID
                WHERE u.UserType = 'Client'";
                
$clientsResult = mysqli_query($conn, $clientsQuery);

if (!$clientsResult) {
    $errors[] = "Database error: " . mysqli_error($conn);
} elseif (mysqli_num_rows($clientsResult) === 0) {
    $errors[] = "No clients found in database. Please create clients first.";
} else {
    $clients = mysqli_fetch_all($clientsResult, MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Project</title>
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
        
        .admin-container {
            display: flex;
            min-height: 100vh;
        }
        
        .main-content {
            flex-grow: 1;
            padding: 30px;
            background-color: #fff;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }
        
        .header h1 {
            margin: 0;
            font-size: 26px;
            color: #2c3e50;
        }
        
        .card {
            background-color: white;
            border-radius: 5px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }
        
        .form-control {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            outline: none;
        }
        
        .btn {
            padding: 8px 16px;
            border-radius: 4px;
            color: #fff;
            font-size: 14px;
            transition: background-color 0.3s ease;
            text-decoration: none;
            display: inline-block;
            border: none;
            cursor: pointer;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: var(--secondary-color);
        }
        
        .btn-secondary {
            background-color: #6c757d;
        }
        
        .btn-secondary:hover {
            background-color: #5a6268;
        }
        
        .alert {
            padding: 10px 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
        }

        .alert-warning {
            background-color: #fff3cd;
            color: #856404;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 15px;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <?php include 'admin_sidebar.php'; ?>
        
        <div class="main-content">
            <div class="header">
                <h1>Add New Project</h1>
                <a href="admin_projects.php" class="btn btn-secondary">Back to Projects</a>
            </div>
            
            <?php if ($success): ?>
                <div class="alert alert-success">
                    Project created successfully! <a href="admin_projects.php">View all projects</a>
                </div>
            <?php elseif (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <div class="card">
                <form method="POST" action="admin_add_project.php">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="construction_type">Construction Type*</label>
                            <select id="construction_type" name="construction_type" class="form-control" required>
                                <option value="">Select Type</option>
                                <option value="Residential">Residential</option>
                                <option value="Commercial">Commercial</option>
                                <option value="Flood Control">Flood Control</option>
                                <option value="Road Construction">Road Construction</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="client_id">Client*</label>
                            <?php if (!empty($clients)): ?>
                                <select id="client_id" name="client_id" class="form-control" required>
                                    <option value="">Select Client</option>
                                    <?php foreach ($clients as $client): ?>
                                        <option value="<?= htmlspecialchars($client['ClientID']) ?>">
                                            <?= htmlspecialchars($client['CompanyName'] . ' (' . $client['FullName'] . ')') ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            <?php else: ?>
                                <div class="alert alert-warning">
                                    No clients available. <a href="admin_users.php">Create clients first</a>.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="project_location">Project Location*</label>
                            <input type="text" id="project_location" name="project_location" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="terrain">Terrain Type</label>
                            <input type="text" id="terrain" name="terrain" class="form-control">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="estimated_budget">Estimated Budget (₱)*</label>
                            <input type="number" id="estimated_budget" name="estimated_budget" class="form-control" step="0.01" min="0" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="description">Project Description</label>
                            <textarea id="description" name="description" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="start_date">Start Date*</label>
                            <input type="date" id="start_date" name="start_date" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="end_date">Estimated End Date</label>
                            <input type="date" id="end_date" name="end_date" class="form-control">
                        </div>
                    </div>
                    
                    <div class="form-group" style="margin-top: 20px;">
                        <button type="submit" class="btn btn-primary">Create Project</button>
                        <a href="admin_projects.php" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        // Set minimum end date to be after start date
        document.getElementById('start_date').addEventListener('change', function() {
            const endDateField = document.getElementById('end_date');
            if (this.value) {
                endDateField.min = this.value;
            }
        });
        
        // Set today's date as default for start date
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('start_date').min = today;
        });
    </script>
</body>
</html>