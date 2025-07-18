<?php
session_start();
if (!isset($_SESSION['User_id']) || $_SESSION['User_type'] !== 'Client') {
    header("Location: login.php");
    exit();
}

require 'database.php';

$client_id = $_SESSION['User_id'];

// Get all projects with updates for this client
$projects_query = "SELECT p.project_id, p.project_name, 
                  MAX(u.update_date) as last_update,
                  COUNT(u.update_id) as update_count
                  FROM projects p
                  LEFT JOIN project_updates u ON p.project_id = u.project_id
                  WHERE p.client_id = ?
                  GROUP BY p.project_id
                  ORDER BY last_update DESC";
$stmt = mysqli_prepare($conn, $projects_query);
mysqli_stmt_bind_param($stmt, "i", $client_id);
mysqli_stmt_execute($stmt);
$projects_result = mysqli_stmt_get_result($stmt);
$projects = mysqli_fetch_all($projects_result, MYSQLI_ASSOC);

// Mark all updates as read when viewing this page
$mark_read_query = "UPDATE project_updates u
                   JOIN projects p ON u.project_id = p.project_id
                   SET u.is_read = TRUE
                   WHERE p.client_id = ?";
$stmt = mysqli_prepare($conn, $mark_read_query);
mysqli_stmt_bind_param($stmt, "i", $client_id);
mysqli_stmt_execute($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Updates</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .update-container {
            max-width: 1000px;
            margin: 2rem auto;
            padding: 20px;
        }
        
        .project-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            overflow: hidden;
        }
        
        .project-header {
            background: #004AAD;
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .update-item {
            padding: 20px;
            border-bottom: 1px solid #eee;
        }
        
        .update-item:last-child {
            border-bottom: none;
        }
        
        .update-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        
        .update-date {
            color: #666;
            font-size: 0.9rem;
        }
        
        .progress-bar {
            height: 10px;
            background: #e0e0e0;
            border-radius: 5px;
            margin: 10px 0;
            overflow: hidden;
        }
        
        .progress-fill {
            height: 100%;
            background: #004AAD;
            width: 0%;
            transition: width 0.5s ease;
        }
        
        .image-gallery {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 15px;
        }
        
        .update-image {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 5px;
            cursor: pointer;
        }
        
        .no-updates {
            text-align: center;
            padding: 40px 20px;
            color: #666;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="update-container">
        <h1>Project Updates</h1>
        
        <?php if (empty($projects)): ?>
            <div class="no-updates">
                <h3>No projects with updates found</h3>
                <p>You'll see updates here when your projects have progress reports.</p>
            </div>
        <?php else: ?>
            <?php foreach ($projects as $project): ?>
                <div class="project-card">
                    <div class="project-header">
                        <h2><?= htmlspecialchars($project['project_name']) ?></h2>
                        <span><?= $project['update_count'] ?> update(s)</span>
                    </div>
                    
                    <?php 
                    // Get updates for this specific project
                    $updates_query = "SELECT * FROM project_updates 
                                     WHERE project_id = ? 
                                     ORDER BY update_date DESC";
                    $stmt = mysqli_prepare($conn, $updates_query);
                    mysqli_stmt_bind_param($stmt, "i", $project['project_id']);
                    mysqli_stmt_execute($stmt);
                    $updates_result = mysqli_stmt_get_result($stmt);
                    $updates = mysqli_fetch_all($updates_result, MYSQLI_ASSOC);
                    
                    if (empty($updates)): ?>
                        <div class="no-updates">
                            <p>No updates available for this project yet.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($updates as $update): ?>
                            <div class="update-item">
                                <div class="update-header">
                                    <h3><?= htmlspecialchars($update['title']) ?></h3>
                                    <span class="update-date">
                                        <?= date('M j, Y g:i a', strtotime($update['update_date'])) ?>
                                    </span>
                                </div>
                                
                                <?php if ($update['progress_percentage'] !== null): ?>
                                    <div class="progress-container">
                                        <div>Progress: <?= $update['progress_percentage'] ?>%</div>
                                        <div class="progress-bar">
                                            <div class="progress-fill" 
                                                 style="width: <?= $update['progress_percentage'] ?>%">
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                                <p><?= nl2br(htmlspecialchars($update['description'])) ?></p>
                                
                                <?php if (!empty($update['images'])): 
                                    $images = explode(',', $update['images']); ?>
                                    <div class="image-gallery">
                                        <?php foreach ($images as $image): ?>
                                            <img src="<?= htmlspecialchars($image) ?>" 
                                                 alt="Update image" 
                                                 class="update-image"
                                                 onclick="openModal('<?= htmlspecialchars($image) ?>')">
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    
    <!-- Image Modal -->
    <div id="imageModal" class="modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.8); z-index:1000; text-align:center;">
        <span style="position:absolute; top:20px; right:30px; color:white; font-size:35px; cursor:pointer;" onclick="closeModal()">&times;</span>
        <img id="modalImage" style="max-height:90%; max-width:90%; margin-top:5%;">
    </div>
    
    <script>
        function openModal(imageSrc) {
            document.getElementById('modalImage').src = imageSrc;
            document.getElementById('imageModal').style.display = 'block';
        }
        
        function closeModal() {
            document.getElementById('imageModal').style.display = 'none';
        }
    </script>
    
    <?php include 'footer.php'; ?>
</body>
</html>