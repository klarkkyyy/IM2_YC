<?php
session_start();
if (!isset($_SESSION['User_id'])) {
    header("Location: login.php");
    exit();
}

require 'database.php';
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$updates = [];
$projects = [];
$rentals = [];

try {
    // Get user's full name for welcome message
    $userQuery = "SELECT FullName FROM user WHERE UserID = ?";
    $stmt = $conn->prepare($userQuery);
    $stmt->bind_param("i", $_SESSION['User_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $welcomeName = $user['FullName'] ?? $_SESSION['Username'];
    $stmt->close();

    // Fetch client's projects from database
    $projectsQuery = "SELECT p.ProjectID, p.ProjectName, p.StartDate, p.EndDate, p.Status, 
                             pp.ConstructionType, pp.ProjectLocation 
                      FROM project p
                      JOIN application a ON p.ApplicationID = a.ApplicationID
                      JOIN projectproposal pp ON p.ProposalID = pp.ProposalID
                      JOIN client c ON a.ClientID = c.ClientID
                      JOIN user u ON c.UserID = u.UserID
                      WHERE u.UserID = ?";
    $stmt = $conn->prepare($projectsQuery);
    $stmt->bind_param("i", $_SESSION['User_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $projects[] = $row;
    }
    $stmt->close();

    // Fetch equipment rentals from database
    $rentalsQuery = "SELECT e.EquipmentName, e.Description, e.ImagePath, e.Availability, 
                            er.RentalDuration, er.DeliveryLocation
                     FROM equipmentrental er
                     JOIN equipment e ON er.EquipmentID = e.EquipmentID
                     JOIN application a ON er.ApplicationID = a.ApplicationID
                     JOIN client c ON a.ClientID = c.ClientID
                     JOIN user u ON c.UserID = u.UserID
                     WHERE u.UserID = ?";
    $stmt = $conn->prepare($rentalsQuery);
    $stmt->bind_param("i", $_SESSION['User_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $rentals[] = $row;
    }
    $stmt->close();

    // Fetch project updates from database
    $updatesQuery = "SELECT pu.Status, pu.Description, pu.SubmittedBy, pu.UpdateDate, p.ProjectName
                     FROM projectupdate pu
                     JOIN project p ON pu.ProjectID = p.ProjectID
                     JOIN application a ON p.ApplicationID = a.ApplicationID
                     JOIN client c ON a.ClientID = c.ClientID
                     JOIN user u ON c.UserID = u.UserID
                     WHERE u.UserID = ?
                     ORDER BY pu.UpdateDate DESC";
    $stmt = $conn->prepare($updatesQuery);
    $stmt->bind_param("i", $_SESSION['User_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $updates[] = $row;
    }
    $stmt->close();

} catch (Exception $e) {
    error_log($e->getMessage());
    die("An error occurred. Please try again later.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Project Updates</title>
  <link rel="stylesheet" href="style.css" />
  <style>
    /* Client Interface Specific Styles */
    .client-updates {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
        position: relative;
    }

    .dashboard-background {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-image: url('Background.jpg') !important;
        background-size: cover;
        background-position: center;
        opacity: 0.2;
        z-index: -1;
    }

    .dashboard-content {
        background-color: rgba(255, 255, 255, 0.9);
        padding: 20px;
        border-radius: 8px;
    }

    .welcome-message {
        text-align: center;
        color: white;
        font-size: 1.5rem;
        margin: 0 auto;
        padding: 0 20px;
        position: relative;
        z-index: 1;
        font-weight: bold;
    }

    .updates-list {
        list-style-type: none;
        padding: 0;
    }

    .update-item {
        padding: 15px;
        margin-bottom: 15px;
        background-color: #f9f9f9;
        border-left: 4px solid #004AAD;
        border-radius: 4px;
    }

    .update-date {
        font-size: 0.9em;
        color: #666;
        margin-bottom: 5px;
    }

    .update-title {
        font-weight: bold;
        margin-bottom: 5px;
        color: #004AAD;
    }

    h2 {
        color: #004AAD;
        border-bottom: 2px solid #004AAD;
        padding-bottom: 5px;
        margin-bottom: 15px;
    }
    
    /* Dropdown styles */
    .dropdown {
        position: relative;
        display: inline-block;
    }
    
    .dropdown-content {
        display: none;
        position: absolute;
        background-color: #004AAD;
        min-width: 200px;
        box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
        z-index: 1;
        border-radius: 0 0 5px 5px;
    }
    
    .dropdown-content a {
        color: white;
        padding: 12px 16px;
        text-decoration: none;
        display: block;
    }
    
    .dropdown-content a:hover {
        background-color: #003080;
    }
    
    .dropdown:hover .dropdown-content {
        display: block;
    }
    
    .dropdown:hover .dropbtn {
        background-color: #003080;
    }
    
    /* Active tab indicator */
    .active-tab {
        background-color: #003080;
    }
  </style>
</head>
<body>
  <div class="dashboard-background"></div>
  
  <nav class="navbar">
    <div class="navbar-center">
        <a href="index.php">
            <img src="logo.png" alt="Logo" class="logo-centered">
        </a>
        <div class="nav-links">
            <a href="projects.php">Projects</a>
            <a href="rentals.php">Rentals</a>
            <a href="project.php">Apply</a>
            <a href="contact.php">Contacts</a>
            <div class="dropdown">
                <a href="client_updates.php" class="dropbtn active-tab">Updates</a>
                <div class="dropdown-content">
                    <a href="client_home.php">Your Projects</a>
                    <a href="client_home.php#equipment">Your Equipment Rentals</a>
                    <a href="client_updates.php">Project Updates</a>
                </div>
            </div>
        </div>
    </div>
    <div class="navbar-right">
        <div class="welcome-message">Welcome, <?= htmlspecialchars($welcomeName) ?>!</div>
        <div class="nav-links">
            <a href="logout.php">Logout</a>
        </div>
    </div>
</nav>

  <div class="client-updates">
    <div class="dashboard-content">
      <h2>Project Updates</h2>
      <?php if (count($updates) > 0): ?>
        <ul class="updates-list">
          <?php foreach ($updates as $update): ?>
            <li class="update-item">
              <div class="update-date"><?= htmlspecialchars($update['UpdateDate']) ?></div>
              <div class="update-title">Status: <?= htmlspecialchars($update['Status']) ?></div>
              <div class="update-description"><?= htmlspecialchars($update['Description']) ?></div>
              <div class="update-submitted">Submitted by: <?= htmlspecialchars($update['SubmittedBy']) ?></div>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php else: ?>
        <p>No updates available for your projects.</p>
      <?php endif; ?>
    </div>
  </div>

  <?php include 'footer.php'; ?>

  <script>
    // Highlight active tab based on current page
    document.addEventListener('DOMContentLoaded', function() {
        const currentPage = location.pathname.split('/').pop();
        const navLinks = document.querySelectorAll('.nav-links a, .dropdown-content a');
        
        navLinks.forEach(link => {
            if (link.getAttribute('href') === currentPage) {
                link.classList.add('active-tab');
            }
        });
    });
  </script>
</body>
</html>