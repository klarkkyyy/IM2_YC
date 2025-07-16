<?php
// admin_reports.php
session_start();
if (!isset($_SESSION['User_id']) || $_SESSION['User_type'] !== 'Admin') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports</title>
    <link rel="stylesheet" href="admin_styles.css">
    <style>
        .report-card {
            background-color: white;
            border-radius: 5px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .report-card h3 {
            margin-top: 0;
            color: var(--dark-color);
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }
        
        .report-options {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }
        
        .report-option {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .report-option:hover {
            background-color: #f8f9fa;
            border-color: var(--primary-color);
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <?php include 'admin_sidebar.php'; ?>
        
        <div class="main-content">
            <div class="header">
                <h1>Reports</h1>
            </div>
            
            <div class="report-card">
                <h3>Financial Reports</h3>
                <div class="report-options">
                    <div class="report-option">
                        <h4>Revenue Report</h4>
                        <p>Monthly and yearly revenue breakdown</p>
                    </div>
                    <div class="report-option">
                        <h4>Equipment Rental Report</h4>
                        <p>Most rented equipment and revenue</p>
                    </div>
                    <div class="report-option">
                        <h4>Project Cost Report</h4>
                        <p>Cost analysis by project</p>
                    </div>
                </div>
            </div>
            
            <div class="report-card">
                <h3>Operational Reports</h3>
                <div class="report-options">
                    <div class="report-option">
                        <h4>Project Status Report</h4>
                        <p>Overview of all projects by status</p>
                    </div>
                    <div class="report-option">
                        <h4>Equipment Utilization</h4>
                        <p>Usage statistics for equipment</p>
                    </div>
                    <div class="report-option">
                        <h4>User Activity</h4>
                        <p>Client and admin activity logs</p>
                    </div>
                </div>
            </div>
            
            <div class="report-card">
                <h3>Custom Report</h3>
                <form>
                    <div style="margin-bottom: 15px;">
                        <label>Report Type:</label>
                        <select style="width: 100%; padding: 8px;">
                            <option>Financial Summary</option>
                            <option>Project Timeline</option>
                            <option>Equipment Availability</option>
                        </select>
                    </div>
                    
                    <div style="margin-bottom: 15px;">
                        <label>Date Range:</label>
                        <div style="display: flex; gap: 10px;">
                            <input type="date" style="flex: 1; padding: 8px;">
                            <span>to</span>
                            <input type="date" style="flex: 1; padding: 8px;">
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Generate Report</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>