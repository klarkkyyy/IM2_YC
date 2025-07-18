<?php
ob_start(); // Start output buffering
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database connection only when needed
$unread_count = 0;
$notifications = [];
$conn = null;

if (isset($_SESSION['User_id']) && $_SESSION['User_type'] === 'Client') {
    require 'database.php';
    
    try {
        $client_id = $_SESSION['User_id'];
        
        // Count unread updates
        $unread_query = "SELECT COUNT(*) FROM project_updates u 
                        JOIN projects p ON u.project_id = p.project_id
                        WHERE p.client_id = ? AND u.is_read = 0";
        $stmt = mysqli_prepare($conn, $unread_query);
        mysqli_stmt_bind_param($stmt, "i", $client_id);
        mysqli_stmt_execute($stmt);
        $unread_result = mysqli_stmt_get_result($stmt);
        $unread_count = mysqli_fetch_row($unread_result)[0];
        
        // Get recent updates
        $updates_query = "SELECT u.*, p.project_name 
                         FROM project_updates u
                         JOIN projects p ON u.project_id = p.project_id
                         WHERE p.client_id = ?
                         ORDER BY u.update_date DESC LIMIT 5";
        $stmt = mysqli_prepare($conn, $updates_query);
        mysqli_stmt_bind_param($stmt, "i", $client_id);
        mysqli_stmt_execute($stmt);
        $updates_result = mysqli_stmt_get_result($stmt);
        $notifications = mysqli_fetch_all($updates_result, MYSQLI_ASSOC);
        
    } catch (Exception $e) {
        error_log("Database error: " . $e->getMessage());
    }
}
ob_end_clean(); // Clean the buffer before output
?>

<nav class="navbar">
    <div class="navbar-center">
        <a href="Index.php">
            <img src="logo.png" alt="Logo" class="logo-centered">
        </a>
        <div class="nav-links">
            <a href="Projects.php">Projects</a>
            <a href="rentals.php">Rentals</a>
            <a href="Apply.php">Apply</a>
        </div>
    </div>
    <div class="navbar-right">
        <div class="nav-links">
            <a href="Contact.php">Contacts</a>
            <?php if (isset($_SESSION['User_id']) && $_SESSION['User_type'] === 'Client'): ?>
                <div class="notification-badge">
                    <a href="#" id="notification-toggle">Updates</a>
                    <?php if ($unread_count > 0): ?>
                        <span class="notification-count"><?= $unread_count ?></span>
                    <?php endif; ?>
                    <div class="notification-dropdown" id="notification-dropdown">
                        <div class="notification-header">
                            <span>Project Updates</span>
                            <span class="mark-all-read">Mark all as read</span>
                        </div>
                        <?php if (!empty($notifications)): ?>
                            <?php foreach ($notifications as $update): ?>
                                <div class="notification-item <?= $update['is_read'] ? '' : 'unread' ?>" 
                                     data-id="<?= $update['update_id'] ?>">
                                    <strong><?= htmlspecialchars($update['project_name']) ?></strong>
                                    <p><?= htmlspecialchars($update['title']) ?></p>
                                    <small><?= date('M j, Y g:i a', strtotime($update['update_date'])) ?></small>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="notification-item">
                                <p>No updates available</p>
                            </div>
                        <?php endif; ?>
                        <div class="notification-item" style="text-align: center;">
                            <a href="project_updates.php">View All Updates</a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <a href="logout.php">Logout</a>
        </div>
    </div>
</nav>