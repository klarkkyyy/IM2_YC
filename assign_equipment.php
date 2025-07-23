<?php
require 'database.php';

$projectId = $_POST['project_id'];
$equipmentIds = json_decode($_POST['equipment_ids']);
$startDate = $_POST['start_date'];
$endDate = $_POST['end_date'] ?? null;

try {
    $conn->begin_transaction();
    
    foreach ($equipmentIds as $equipmentId) {
        // Check if equipment is already assigned
        $checkQuery = "SELECT * FROM project_equipment 
                      WHERE project_id = ? AND equipment_id = ? 
                      AND ((start_date <= ? AND (end_date IS NULL OR end_date >= ?))";
        $checkStmt = $conn->prepare($checkQuery);
        $checkStmt->bind_param("iiss", $projectId, $equipmentId, $endDate, $startDate);
        $checkStmt->execute();
        
        if ($checkStmt->get_result()->num_rows > 0) {
            throw new Exception("Equipment ID $equipmentId is already assigned during this period");
        }
        
        // Assign equipment
        $assignQuery = "INSERT INTO project_equipment (project_id, equipment_id, start_date, end_date, status)
                       VALUES (?, ?, ?, ?, 'Reserved')";
        $assignStmt = $conn->prepare($assignQuery);
        $assignStmt->bind_param("iiss", $projectId, $equipmentId, $startDate, $endDate);
        $assignStmt->execute();
        
        // Update equipment availability
        $updateQuery = "UPDATE equipment SET Availability = 'Unavailable' WHERE EquipmentID = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("i", $equipmentId);
        $updateStmt->execute();
    }
    
    // Log activity
    $activityQuery = "INSERT INTO recent_activity (ActivityDate, ActivityType, Username, Details)
                     VALUES (NOW(), 'Equipment Assignment', ?, ?)";
    $activityStmt = $conn->prepare($activityQuery);
    $username = $_SESSION['Username'] ?? 'System';
    $details = "Assigned " . count($equipmentIds) . " equipment to project $projectId";
    $activityStmt->bind_param("ss", $username, $details);
    $activityStmt->execute();
    
    $conn->commit();
    
    echo json_encode(['success' => true, 'message' => 'Equipment assigned successfully']);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}