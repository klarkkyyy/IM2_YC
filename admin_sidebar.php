<style>
    /* Sidebar Styles */
        .sidebar {
            width: 250px;
            background-color: var(--primary-color);
            color: white;
            padding: 20px 0;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
            height: fit-content;
            min-height: 100%;
        }
        
        .sidebar-header {
            padding: 0 20px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar-nav {
            padding: 20px 0;
        }
        
        .nav-item {
            padding: 10px 20px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .nav-item:hover, .nav-item.active {
            background-color: var(--secondary-color);
        }
        
        .nav-item a {
            color: white;
            text-decoration: none;
            display: block;
        }
</style>

<div class="sidebar">
            <div class="sidebar-header">
                <h2>Admin Panel</h2>
                <p>Welcome, <?php echo htmlspecialchars($_SESSION['Username']); ?></p>
            </div>
            <div class="sidebar-nav">
                <div class="nav-item">
                    <a href="admin_dashboard.php">Dashboard</a>
                </div>
                <div class="nav-item">
                    <a href="admin_users.php">User Management</a>
                </div>
                <div class="nav-item">
                    <a href="admin_projects.php">Project Management</a>
                </div>
                <div class="nav-item">
                    <a href="admin_equipment.php">Equipment Management</a>
                </div>
                <div class="nav-item">
                    <a href="admin_reports.php">Reports</a>
                </div>
                <div class="nav-item">
                    <a href="logout.php">Logout</a>
                </div>
            </div>
        </div>