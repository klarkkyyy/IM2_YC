<?php
require 'database.php';

$projectId = $_GET['project_id'] ?? 0;

$query = "SELECT e.EquipmentID, e.EquipmentName, pe.start_date, pe.end_date, pe.status
          FROM project_equipment pe
          JOIN equipment e ON pe.equipment_id = e.EquipmentID
          WHERE pe.project_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $projectId);
$stmt->execute();
$equipment = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

if (count($equipment) > 0): ?>
    <table class="data-table" style="width: 100%;">
        <thead>
            <tr>
                <th>Equipment ID</th>
                <th>Name</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($equipment as $item): ?>
            <tr>
                <td><?= $item['EquipmentID'] ?></td>
                <td><?= htmlspecialchars($item['EquipmentName']) ?></td>
                <td><?= date('M j, Y', strtotime($item['start_date'])) ?></td>
                <td><?= $item['end_date'] ? date('M j, Y', strtotime($item['end_date'])) : 'N/A' ?></td>
                <td><?= $item['status'] ?></td>
                <td>
                    <button class="btn btn-sm btn-danger" onclick="unassignEquipment(<?= $projectId ?>, <?= $item['EquipmentID'] ?>)">Unassign</button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No equipment assigned to this project.</p>
<?php endif; ?>