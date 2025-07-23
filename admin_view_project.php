<?php
session_start();
if (!isset($_SESSION['User_id']) || $_SESSION['User_type'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

require 'database.php';

$projectId = $_GET['id'] ?? 0;

// Enhanced project query with more details
$query = "SELECT p.*, pp.*, c.CompanyName AS ClientName, 
                 u.FullName AS ClientContact, c.ContactInfo,
                 DATEDIFF(p.EndDate, p.StartDate) AS DurationDays
          FROM project p
          LEFT JOIN projectproposal pp ON p.ProposalID = pp.ProposalID
          LEFT JOIN application a ON p.ApplicationID = a.ApplicationID
          LEFT JOIN client c ON a.ClientID = c.ClientID
          LEFT JOIN user u ON c.UserID = u.UserID
          WHERE p.ProjectID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $projectId);
$stmt->execute();
$project = $stmt->get_result()->fetch_assoc();

if (!$project) {
    die("Project not found");
}

// Calculate progress percentage
$progress = 0;
if ($project['Status'] === 'Completed') {
    $progress = 100;
} elseif ($project['Status'] === 'Ongoing') {
    $daysPassed = min(max(0, time() - strtotime($project['StartDate'])), 
                   strtotime($project['EndDate']) - strtotime($project['StartDate']));
    $progress = round(($daysPassed / ($project['DurationDays'] * 86400)) * 100);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Project - <?= htmlspecialchars($project['ConstructionType']) ?></title>
    <link rel="stylesheet" href="admin_styles.css">
</head>
<body>
    <div class="admin-container">
        <?php include 'admin_sidebar.php'; ?>
        
        <div class="main-content">
            <div class="header">
                <h1><?= htmlspecialchars($project['ConstructionType']) ?> Project</h1>
                <div>
                    <a href="admin_edit_project.php?id=<?= $projectId ?>" class="btn btn-warning">Edit Project</a>
                    <a href="admin_projects.php" class="btn btn-secondary">Back to Projects</a>
                </div>
            </div>
            
            <div class="project-detail-card">
                <div class="detail-grid">
                    <div>
                        <div class="detail-group">
                            <span class="detail-label">Client</span>
                            <div class="detail-value">
                                <?= htmlspecialchars($project['ClientName']) ?>
                                <small style="display: block; color: #666;">
                                    <?= htmlspecialchars($project['ClientContact']) ?>
                                </small>
                            </div>
                        </div>
                        
                        <div class="detail-group">
                            <span class="detail-label">Project Location</span>
                            <div class="detail-value"><?= htmlspecialchars($project['ProjectLocation']) ?></div>
                        </div>
                        
                        <div class="detail-group">
                            <span class="detail-label">Terrain Type</span>
                            <div class="detail-value"><?= htmlspecialchars($project['Terrain'] ?? 'Not specified') ?></div>
                        </div>
                    </div>
                    
                    <div>
                        <div class="detail-group">
                            <span class="detail-label">Status</span>
                            <div class="detail-value status-indicator status-<?= strtolower($project['Status']) ?>">
                                <span class="status-dot"></span>
                                <?= htmlspecialchars($project['Status']) ?>
                            </div>
                        </div>
                        
                        <div class="detail-group">
                            <span class="detail-label">Budget</span>
                            <div class="detail-value">₱<?= number_format($project['EstimatedBudget'], 2) ?></div>
                        </div>
                        
                        <div class="detail-group">
                            <span class="detail-label">Current Balance</span>
                            <div class="detail-value">₱<?= number_format($project['CurrentBalance'], 2) ?></div>
                        </div>
                    </div>
                </div>
                
                <div class="detail-group">
                    <span class="detail-label">Project Description</span>
                    <div class="detail-value" style="min-height: 60px; padding: 15px;">
                        <?= nl2br(htmlspecialchars($project['Description'] ?? 'No description available')) ?>
                    </div>
                </div>
            </div>
            
            <h2 class="section-title">Project Timeline</h2>
            <div class="project-detail-card">
                <div class="detail-grid">
                    <div>
                        <div class="detail-group">
                            <span class="detail-label">Start Date</span>
                            <div class="detail-value">
                                <?= date('F j, Y', strtotime($project['StartDate'])) ?>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <div class="detail-group">
                            <span class="detail-label">End Date</span>
                            <div class="detail-value">
                                <?= $project['EndDate'] ? date('F j, Y', strtotime($project['EndDate'])) : 'Ongoing' ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="detail-group">
                    <span class="detail-label">Progress</span>
                    <div class="timeline-bar">
                        <div class="timeline-progress" style="width: <?= $progress ?>%"></div>
                    </div>
                    <div style="text-align: right; margin-top: 5px; color: #666;">
                        <?= $progress ?>% complete
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>