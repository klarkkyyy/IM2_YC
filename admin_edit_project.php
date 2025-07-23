<?php
session_start();
if (!isset($_SESSION['User_id']) || $_SESSION['User_type'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

require 'database.php';

$projectId = $_GET['id'] ?? 0;

// Fetch project details
$query = "SELECT p.*, pp.*, c.CompanyName AS ClientName 
          FROM project p
          LEFT JOIN projectproposal pp ON p.ProposalID = pp.ProposalID
          LEFT JOIN application a ON p.ApplicationID = a.ApplicationID
          LEFT JOIN client c ON a.ClientID = c.ClientID
          WHERE p.ProjectID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $projectId);
$stmt->execute();
$project = $stmt->get_result()->fetch_assoc();

if (!$project) {
    die("Project not found");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process updates here
    // ... (your existing update logic)
    
    // After successful update:
    header("Location: admin_view_project.php?id=$projectId");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Project - <?= htmlspecialchars($project['ConstructionType']) ?></title>
    <link rel="stylesheet" href="admin_styles.css">
</head>
<body>
    <div class="admin-container">
        <?php include 'admin_sidebar.php'; ?>
        
        <div class="main-content">
            <div class="header">
                <h1>Edit Project</h1>
                <a href="admin_view_project.php?id=<?= $projectId ?>" class="btn btn-secondary">Back to View</a>
            </div>
            
            <div class="project-detail-card">
                <form method="POST" action="admin_edit_project.php?id=<?= $projectId ?>">
                    <div class="detail-grid">
                        <div>
                            <div class="form-group">
                                <label class="detail-label">Construction Type</label>
                                <select name="construction_type" class="form-control" required>
                                    <option value="Residential" <?= $project['ConstructionType'] === 'Residential' ? 'selected' : '' ?>>Residential</option>
                                    <option value="Commercial" <?= $project['ConstructionType'] === 'Commercial' ? 'selected' : '' ?>>Commercial</option>
                                    <option value="Flood Control" <?= $project['ConstructionType'] === 'Flood Control' ? 'selected' : '' ?>>Flood Control</option>
                                    <option value="Road Construction" <?= $project['ConstructionType'] === 'Road Construction' ? 'selected' : '' ?>>Road Construction</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label class="detail-label">Project Location</label>
                                <input type="text" name="project_location" class="form-control" 
                                       value="<?= htmlspecialchars($project['ProjectLocation']) ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label class="detail-label">Terrain Type</label>
                                <input type="text" name="terrain" class="form-control" 
                                       value="<?= htmlspecialchars($project['Terrain'] ?? '') ?>">
                            </div>
                        </div>
                        
                        <div>
                            <div class="form-group">
                                <label class="detail-label">Status</label>
                                <select name="status" class="form-control" required>
                                    <option value="Ongoing" <?= $project['Status'] === 'Ongoing' ? 'selected' : '' ?>>Ongoing</option>
                                    <option value="Completed" <?= $project['Status'] === 'Completed' ? 'selected' : '' ?>>Completed</option>
                                    <option value="Cancelled" <?= $project['Status'] === 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label class="detail-label">Estimated Budget (₱)</label>
                                <input type="number" step="0.01" name="estimated_budget" class="form-control" 
                                       value="<?= htmlspecialchars($project['EstimatedBudget']) ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label class="detail-label">Current Balance (₱)</label>
                                <input type="number" step="0.01" name="current_balance" class="form-control" 
                                       value="<?= htmlspecialchars($project['CurrentBalance']) ?>" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="detail-label">Project Description</label>
                        <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($project['Description'] ?? '') ?></textarea>
                    </div>
                    
                    <div class="detail-grid" style="margin-top: 20px;">
                        <div>
                            <div class="form-group">
                                <label class="detail-label">Start Date</label>
                                <input type="date" name="start_date" class="form-control" 
                                       value="<?= htmlspecialchars($project['StartDate']) ?>" required>
                            </div>
                        </div>
                        
                        <div>
                            <div class="form-group">
                                <label class="detail-label">End Date</label>
                                <input type="date" name="end_date" class="form-control" 
                                       value="<?= htmlspecialchars($project['EndDate'] ?? '') ?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-actions" style="margin-top: 30px;">
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                        <a href="admin_view_project.php?id=<?= $projectId ?>" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>