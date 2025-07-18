<?php
session_start();
if (!isset($_SESSION['User_id']) || $_SESSION['User_type'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

require 'database.php';

$error = '';
$success = '';

// Get projects for dropdown
$projects_query = "SELECT project_id, project_name FROM projects";
$projects_result = mysqli_query($conn, $projects_query);
$projects = mysqli_fetch_all($projects_result, MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $project_id = intval($_POST['project_id']);
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $progress = isset($_POST['progress']) ? intval($_POST['progress']) : null;
    
    // Handle file upload
    $uploaded_images = [];
    if (!empty($_FILES['images']['name'][0])) {
        $upload_dir = 'uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
            $file_name = basename($_FILES['images']['name'][$key]);
            $file_path = $upload_dir . uniqid() . '_' . $file_name;
            
            if (move_uploaded_file($tmp_name, $file_path)) {
                $uploaded_images[] = $file_path;
            }
        }
    }
    
    // Validate input
    if (empty($title) {
        $error = 'Title is required';
    } elseif (empty($description)) {
        $error = 'Description is required';
    } else {
        // Insert update
        $images_str = !empty($uploaded_images) ? implode(',', $uploaded_images) : null;
        $query = "INSERT INTO project_updates (project_id, title, description, progress_percentage, images) 
                  VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "issis", $project_id, $title, $description, $progress, $images_str);
        
        if (mysqli_stmt_execute($stmt)) {
            $success = 'Update added successfully!';
            $_POST = []; // Clear form
        } else {
            $error = 'Error adding update: ' . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Project Update</title>
    <link rel="stylesheet" href="admin_styles.css">
    <style>
        .form-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        .form-group input[type="text"],
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .form-group textarea {
            height: 150px;
        }
        
        .file-upload {
            border: 2px dashed #ddd;
            padding: 20px;
            text-align: center;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        
        .preview-images {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 15px;
        }
        
        .preview-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <?php include 'admin_sidebar.php'; ?>
        
        <div class="main-content">
            <div class="header">
                <h1>Add Project Update</h1>
                <a href="admin_projects.php" class="btn btn-primary">Back to Projects</a>
            </div>
            
            <div class="form-container">
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php elseif ($success): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                <?php endif; ?>
                
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="project_id">Project</label>
                        <select name="project_id" id="project_id" required>
                            <option value="">Select a project</option>
                            <?php foreach ($projects as $project): ?>
                                <option value="<?= $project['project_id'] ?>" 
                                    <?= isset($_POST['project_id']) && $_POST['project_id'] == $project['project_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($project['project_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="title">Update Title</label>
                        <input type="text" name="title" id="title" required
                               value="<?= htmlspecialchars($_POST['title'] ?? '') ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea name="description" id="description" required><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="progress">Progress Percentage (optional)</label>
                        <input type="number" name="progress" id="progress" min="0" max="100"
                               value="<?= htmlspecialchars($_POST['progress'] ?? '') ?>">
                    </div>
                    
                    <div class="form-group">
                        <label>Images (optional)</label>
                        <div class="file-upload">
                            <input type="file" name="images[]" id="images" multiple accept="image/*">
                            <p>Drag & drop images here or click to browse</p>
                            <div class="preview-images" id="preview-container"></div>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Add Update</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Image preview functionality
        document.getElementById('images').addEventListener('change', function(e) {
            const previewContainer = document.getElementById('preview-container');
            previewContainer.innerHTML = '';
            
            if (this.files) {
                Array.from(this.files).forEach(file => {
                    if (file.type.match('image.*')) {
                        const reader = new FileReader();
                        
                        reader.onload = function(e) {
                            const img = document.createElement('img');
                            img.src = e.target.result;
                            img.className = 'preview-image';
                            previewContainer.appendChild(img);
                        }
                        
                        reader.readAsDataURL(file);
                    }
                });
            }
        });
    </script>
</body>
</html>