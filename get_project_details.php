<?php
require 'database.php';

$projectId = $_GET['id'] ?? 0;

$query = "SELECT p.*, pp.*, c.CompanyName AS ClientName, 
                 u.FullName AS ManagerName, u.Email AS ManagerEmail,
                 (SELECT COUNT(*) FROM projectassign WHERE ProjectID = p.ProjectID) AS TeamSize,
                 (SELECT COUNT(*) FROM project_equipment WHERE project_id = p.ProjectID) AS EquipmentCount
          FROM project p
          LEFT JOIN projectproposal pp ON p.ProposalID = pp.ProposalID
          LEFT JOIN application a ON p.ApplicationID = a.ApplicationID
          LEFT JOIN client c ON a.ClientID = c.ClientID
          LEFT JOIN projectassign pa ON p.ProjectID = pa.ProjectID AND pa.Role = 'Project Manager'
          LEFT JOIN employee e ON pa.AssigneeEmployeeID = e.EmployeeID
          LEFT JOIN user u ON e.UserID = u.UserID
          WHERE p.ProjectID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $projectId);
$stmt->execute();
$project = $stmt->get_result()->fetch_assoc();

// Get project updates
$updatesQuery = "SELECT pu.*, u.FullName 
                 FROM projectupdate pu
                 LEFT JOIN employee e ON pu.EmployeeID = e.EmployeeID
                 LEFT JOIN user u ON e.UserID = u.UserID
                 WHERE pu.ProjectID = ?
                 ORDER BY pu.UpdateID DESC";
$updatesStmt = $conn->prepare($updatesQuery);
$updatesStmt->bind_param("i", $projectId);
$updatesStmt->execute();
$updates = $updatesStmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get assigned equipment
$equipmentQuery = "SELECT e.EquipmentID, e.EquipmentName, pe.start_date, pe.end_date, pe.status
                   FROM project_equipment pe
                   JOIN equipment e ON pe.equipment_id = e.EquipmentID
                   WHERE pe.project_id = ?";
$equipmentStmt = $conn->prepare($equipmentQuery);
$equipmentStmt->bind_param("i", $projectId);
$equipmentStmt->execute();
$equipment = $equipmentStmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get team members
$teamQuery = "SELECT u.FullName, e.Role, pa.AssignmentDate, pa.LaborersAssigned
              FROM projectassign pa
              JOIN employee e ON pa.AssigneeEmployeeID = e.EmployeeID
              JOIN user u ON e.UserID = u.UserID
              WHERE pa.ProjectID = ?";
$teamStmt = $conn->prepare($teamQuery);
$teamStmt->bind_param("i", $projectId);
$teamStmt->execute();
$team = $teamStmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<div class="project-details">
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px;">
        <div>
            <h3><?= htmlspecialchars($project['ConstructionType'] ?? 'N/A') ?> Project</h3>
            <p><strong>Location:</strong> <?= htmlspecialchars($project['ProjectLocation'] ?? 'N/A') ?></p>
            <p><strong>Description:</strong> <?= htmlspecialchars($project['Description'] ?? 'No description available') ?></p>
            <p><strong>Terrain:</strong> <?= htmlspecialchars($project['Terrain'] ?? 'Not specified') ?></p>
            
            <div style="margin-top: 20px;">
                <h4>Timeline</h4>
                <p><strong>Start Date:</strong> <?= date('M j, Y', strtotime($project['StartDate'])) ?></p>
                <p><strong>End Date:</strong> <?= $project['EndDate'] ? date('M j, Y', strtotime($project['EndDate'])) : 'Ongoing' ?></p>
                <div class="progress-container">
                    <div class="progress-bar" style="width: <?= $project['Status'] == 'Completed' ? 100 : ($project['Status'] == 'Cancelled' ? 0 : 50) ?>%"></div>
                </div>
            </div>
            
            <div style="margin-top: 20px;">
                <h4>Financials</h4>
                <p><strong>Estimated Budget:</strong> ₱<?= number_format($project['EstimatedBudget'] ?? 0, 2) ?></p>
                <p><strong>Current Balance:</strong> ₱<?= number_format($project['CurrentBalance'] ?? 0, 2) ?></p>
                <p><strong>Payment Status:</strong> <?= $project['IsFullyPaid'] ? 'Fully Paid' : 'Pending Payment' ?></p>
            </div>
        </div>
        
        <div>
            <div class="card" style="margin-bottom: 20px;">
                <h4>Project Team</h4>
                <p><strong>Project Manager:</strong> <?= htmlspecialchars($project['ManagerName'] ?? 'Not assigned') ?></p>
                <p><strong>Team Members:</strong> <?= $project['TeamSize'] ?? 0 ?></p>
                <button class="btn btn-sm btn-primary" onclick="viewTeamMembers(<?= $projectId ?>)">View Team</button>
            </div>
            
            <div class="card" style="margin-bottom: 20px;">
                <h4>Equipment</h4>
                <p><strong>Assigned Equipment:</strong> <?= $project['EquipmentCount'] ?? 0 ?></p>
                <button class="btn btn-sm btn-primary" onclick="openAssignEquipmentModal(<?= $projectId ?>)">Assign Equipment</button>
                <button class="btn btn-sm btn-secondary" onclick="viewAssignedEquipment(<?= $projectId ?>)">View Equipment</button>
            </div>
            
            <div class="card">
                <h4>Project Status</h4>
                <span class="status-badge status-<?= strtolower($project['Status']) ?>">
                    <?= htmlspecialchars($project['Status']) ?>
                </span>
                <button class="btn btn-sm btn-warning" style="margin-top: 10px;" onclick="updateProjectStatus(<?= $projectId ?>)">Update Status</button>
            </div>
        </div>
    </div>
    
    <div style="margin-top: 30px;">
        <h4>Recent Updates</h4>
        <?php if (count($updates) > 0): ?>
            <div class="updates-list">
                <?php foreach ($updates as $update): ?>
                <div class="update-item" style="border-bottom: 1px solid #eee; padding: 10px 0;">
                    <p><strong><?= htmlspecialchars($update['FullName']) ?></strong> - <?= date('M j, Y', strtotime($update['SubmittedDate'])) ?></p>
                    <p><span class="badge"><?= htmlspecialchars($update['Status']) ?></span></p>
                    <p><?= htmlspecialchars($update['Description']) ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>No updates available for this project.</p>
        <?php endif; ?>
        <button class="btn btn-primary" style="margin-top: 15px;" onclick="addProjectUpdate(<?= $projectId ?>)">Add Update</button>
    </div>
</div>