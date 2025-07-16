<?php
// admin_users.php
session_start();
if (!isset($_SESSION['User_id']) || $_SESSION['User_type'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

require 'database.php';

// Display recent activities with SELECT
$activities = [];
$activitySql = "SELECT ActivityDate, ActivityType, Username, Details FROM recent_activity ORDER BY ActivityDate DESC LIMIT 10";
$activityResult = mysqli_query($conn, $activitySql);

if ($activityResult && mysqli_num_rows($activityResult) > 0) {
    while ($row = mysqli_fetch_assoc($activityResult)) {
        $activities[] = $row;
    }
}


// Counting users that are clients with COUNT(*)
$totalClients = 0;
$sql = "SELECT COUNT(*) AS total FROM user WHERE UserType = 'Client'";
$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $totalClients = $row['total'];
}

// Count available equipments with SELECT
$totalAvailableEquipment = 0;
$equipmentQuery = "SELECT COUNT(*) AS total FROM equipment WHERE Availability = 'Available'";
$equipmentResult = mysqli_query($conn, $equipmentQuery);

if ($equipmentResult && mysqli_num_rows($equipmentResult) > 0) {
    $row = mysqli_fetch_assoc($equipmentResult);
    $totalAvailableEquipment = $row['total'];
}

// Retrieve users
$users = [];
$userQuery = "SELECT UserID, Email, FullName, Username, UserType FROM user";
$userResult = mysqli_query($conn, $userQuery);

if ($userResult && mysqli_num_rows($userResult) > 0) {
    while ($row = mysqli_fetch_assoc($userResult)) {
        $users[] = $row;
    }
}
//update to database
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $UserID = $_POST['UserID'];
    $Email = $_POST['Email'];
    $FullName = $_POST['FullName'];
    $Username = $_POST['Username'];
    $UserType = $_POST['UserType'];

    $stmt = $conn->prepare("UPDATE user SET Email=?, FullName=?, Username=?, UserType=? WHERE UserID=?");
    $stmt->bind_param("ssssi", $Email, $FullName, $Username, $UserType, $UserID);
    $stmt->execute();

    header("Location: admin_users.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
<style>
        :root {
            --primary-color: #004AAD;
            --secondary-color: #003080;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
        }
        
        html, body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        
        body {
            flex: 1;
        }
        .admin-container {
            display: flex;
            gap: 20px;
            flex: 1;
        }

        
        
        
        /* Main Content Styles */
        .main-content {
            flex: 1;
            padding: 20px;
        }
        
        .header {
            background-color: white;
            padding: 15px 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .card {
            background-color: white;
            border-radius: 5px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .card h3 {
            margin-top: 0;
            color: var(--dark-color);
        }
        
        .card .value {
            font-size: 2rem;
            font-weight: bold;
            margin: 10px 0;
        }
        
        .card .success {
            color: var(--success-color);
        }
        
        .card .danger {
            color: var(--danger-color);
        }
        
        .card .warning {
            color: var(--warning-color);
        }
        
        /* Table Styles */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            border-radius: 5px;
            overflow: hidden;
        }
        
        .data-table th, .data-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        .data-table th {
            background-color: var(--primary-color);
            color: white;
        }
        
        .data-table tr:hover {
            background-color: #f5f5f5;
        }
        
        .btn {
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }
        
        .btn-danger {
            background-color: var(--danger-color);
            color: white;
        }
        
        .btn-success {
            background-color: var(--success-color);
            color: white;
        }
        
        .btn-sm {
            padding: 5px 10px;
            font-size: 0.8rem;
        }

        footer {
            background-color: #004AAD;
            color: white;
            display: flex;
            justify-content: space-between;
            padding: 20px;
            margin-bottom: auto;
        }

        .footer-section {
            flex: 1;
            padding: 10px;
        }

        .footer-section h3 {
            margin-top: 0;
            font-size: 1.2em;
        }

        .footer-section p {
            margin: 5px 0;
        }

        .social-icons {
            display: flex;
            justify-content: center;
            margin-top: 10px;
        }

        .social-icons a {
            color: white;
            margin: 0 10px;
            text-decoration: none;
            display: flex;
            align-items: center;
        }

        .social-icons a img {
            height: 20px;
            margin-right: 5px;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <?php include 'admin_sidebar.php'; ?>
        
        <div class="main-content">
            <div class="header">
                <h1>User Management</h1>
                <button class="btn btn-primary" onclick="openAddModal()">Add New User</button>
            </div>

            <table class="data-table">
            <div id="editModal" style="display:none; position: fixed; top: 0; left: 0; width:100%; height:100%; background: rgba(0, 0, 0, 0.5); justify-content: center; align-items: center;">
            <div style="background: white; padding: 20px; border-radius: 8px; width: 400px; max-width: 90%;">
                        <h2>Edit User</h2>
                        <form id="editUserForm" method="POST" action="">
                            <input type="hidden" name="UserID" id="editUserID">
                            
                            <label for="editEmail">Email:</label>
                            <input type="email" name="Email" id="editEmail" required style="width: 100%; margin-bottom: 10px;">
                            
                            <label for="editFullName">Full Name:</label>
                            <input type="text" name="FullName" id="editFullName" required style="width: 100%; margin-bottom: 10px;">
                            
                            <label for="editUsername">Username:</label>
                            <input type="text" name="Username" id="editUsername" required style="width: 100%; margin-bottom: 10px;">
                            
                            <label for="editUserType">User Type:</label>
                            <select name="UserType" id="editUserType" required style="width: 100%; margin-bottom: 15px;">
                                <option value="Client">Client</option>
                                <option value="Admin">Admin</option>
                            </select>
                            
                            <div style="display: flex; justify-content: space-between;">
                                <button type="submit" class="btn btn-success">Update</button>
                                <button type="button" onclick="closeEditModal()" class="btn btn-danger">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div id="addModal" style="display:none; position: fixed; top: 0; left: 0; width:100%; height:100%; background: rgba(0, 0, 0, 0.5); justify-content: center; align-items: center;">
                <div style="background: white; padding: 20px; border-radius: 8px; width: 400px; max-width: 90%;">
                    <h2>Add New User</h2>
                    <form id="addUserForm" method="POST" action="add_user.php">
                        <label for="addEmail">Email:</label>
                        <input type="email" name="Email" id="addEmail" required style="width: 100%; margin-bottom: 10px;">

                        <label for="addFullName">Full Name:</label>
                        <input type="text" name="FullName" id="addFullName" required style="width: 100%; margin-bottom: 10px;">

                        <label for="addUsername">Username:</label>
                        <input type="text" name="Username" id="addUsername" required style="width: 100%; margin-bottom: 10px;">

                        <label for="addUserType">User Type:</label>
                        <select name="UserType" id="addUserType" required style="width: 100%; margin-bottom: 10px;">
                            <option value="Client">Client</option>
                            <option value="Admin">Admin</option>
                        </select>

                        <label for="addPassword">Password:</label>
                        <input type="password" name="Password" id="addPassword" required style="width: 100%; margin-bottom: 15px;">

                        <div style="display: flex; justify-content: space-between;">
                            <button type="submit" class="btn btn-success">Add</button>
                            <button type="button" onclick="closeAddModal()" class="btn btn-danger">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
                <thead>
                    <tr>
                        <th>UserID</th>
                        <th>Email</th>
                        <th>Full Name</th>
                        <th>Username</th>
                        <th>User Type</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['UserID']) ?></td>
                        <td><?= htmlspecialchars($user['Email']) ?></td>
                        <td><?= htmlspecialchars($user['FullName']) ?></td>
                        <td><?= htmlspecialchars($user['Username']) ?></td>
                        <td><?= htmlspecialchars($user['UserType']) ?></td>
                        <td>
                            <button class="btn btn-sm btn-primary" onclick="openEditModal(
                                '<?= $user['UserID'] ?>',
                                '<?= addslashes($user['Email']) ?>',
                                '<?= addslashes($user['FullName']) ?>',
                                '<?= addslashes($user['Username']) ?>',
                                '<?= $user['UserType'] ?>'
                            )">Edit</button>
                            <a href="delete_user.php?id=<?= $user['UserID'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <?php include 'footer.php'; ?>
    <script>
    function openEditModal(userID, email, fullName, username, userType) {
        document.getElementById('editUserID').value = userID;
        document.getElementById('editEmail').value = email;
        document.getElementById('editFullName').value = fullName;
        document.getElementById('editUsername').value = username;
        document.getElementById('editUserType').value = userType;

        document.getElementById('editModal').style.display = 'flex';
    }

    function closeEditModal() {
        document.getElementById('editModal').style.display = 'none';
    }

    function openAddModal() {
        document.getElementById('addModal').style.display = 'flex';
    }

    function closeAddModal() {
        document.getElementById('addModal').style.display = 'none';
    }

    </script>
</body>

</html>
