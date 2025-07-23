<?php
session_start();
require 'database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $projectName = $_POST['project_name'];
    $constructionType = $_POST['construction_type'];
    $startDate = $_POST['start_date'];
    $endDate = $_POST['end_date'] ?? null;
    $location = $_POST['location'];
    $budget = $_POST['budget'];
    $managerId = $_POST['manager_id'] ?? null;
    $status = $_POST['status'];
    $description = $_POST['description'] ?? '';
    
    try {
        $conn->begin_transaction();
        
        // Create proposal
        $proposalQuery = "INSERT INTO projectproposal (ConstructionType, ProjectLocation, EstimatedBudget, Description)
                         VALUES (?, ?, ?, ?)";
        $proposalStmt = $conn->prepare($proposalQuery);
        $proposalStmt->bind_param("ssds", $constructionType, $location, $budget, $description);
        $proposalStmt->execute();
        $proposalId = $conn->insert_id;
        
        // Create application (simplified for demo)
        $applicationQuery = "INSERT INTO application (ClientID, ApplicationType, Description, SubmissionDate, Status)
                           VALUES (1, 'Project', ?, NOW(), 'Approved')";
        $applicationStmt = $conn->prepare($applicationQuery);
        $applicationStmt->bind_param("s", $description);
        $applicationStmt->execute();
        $applicationId = $conn->insert_id;
        
        // Create project
        $projectQuery = "INSERT INTO project (ApplicationID, ProposalID, StartDate, EndDate, Status, CurrentBalance)
                        VALUES (?, ?, ?, ?, ?, ?)";
        $projectStmt = $conn->prepare($projectQuery);
        $currentBalance = $budget;
        $projectStmt->bind_param("iisssd", $applicationId, $proposalId, $startDate, $endDate, $status, $currentBalance);
        $projectStmt->execute();
        $projectId = $conn->insert_id;
        
        // Assign manager if selected
        if ($managerId) {
            // Get employee ID from user ID
            $employeeQuery = "SELECT EmployeeID FROM employee WHERE UserID = ?";
            $employeeStmt = $conn->prepare($employeeQuery);
            $employeeStmt->bind_param("i", $managerId);
            $employeeStmt->execute();
            $employeeId = $employeeStmt->get_result()->fetch_assoc()['EmployeeID'];
            
            if ($employeeId) {
                $assignQuery = "INSERT INTO projectassign (ProjectID, AssigneeEmployeeID, Role, AssignmentDate)
                              VALUES (?, ?, 'Project Manager', NOW())";
                $assignStmt = $conn->prepare($assignQuery);
                $assignStmt->bind_param("ii", $projectId, $employeeId);
                $assignStmt->execute();
            }
        }
        
        // Log activity
        $activityQuery = "INSERT INTO recent_activity (ActivityDate, ActivityType, Username, Details)
                         VALUES (NOW(), 'Project Creation', ?, ?)";
        $activityStmt = $conn->prepare($activityQuery);
        $username = $_SESSION['Username'] ?? 'System';
        $details = "Created new project: $projectName";
        $activityStmt->bind_param("ss", $username, $details);
        $activityStmt->execute();
        
        $conn->commit();
        
        header("Location: admin_projects.php?success=1");
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        header("Location: admin_projects.php?error=" . urlencode($e->getMessage()));
        exit();
    }
}