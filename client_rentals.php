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

    // Fetch equipment rentals from database
    $rentalsQuery = "SELECT e.EquipmentID, e.EquipmentName, e.Description, e.ImagePath, 
                            e.Availability, e.DailyPrice, e.WeeklyPrice, e.MonthlyPrice,
                            er.RentalStartDate, er.RentalEndDate, er.RentalStatus
                     FROM equipmentrental er
                     JOIN equipment e ON er.EquipmentID = e.EquipmentID
                     JOIN application a ON er.ApplicationID = a.ApplicationID
                     JOIN client c ON a.ClientID = c.ClientID
                     JOIN user u ON c.UserID = u.UserID
                     WHERE u.UserID = ?
                     ORDER BY er.RentalStartDate DESC";
    $stmt = $conn->prepare($rentalsQuery);
    $stmt->bind_param("i", $_SESSION['User_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $rentals[] = $row;
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
  <title>Your Equipment Rentals</title>
  <link rel="stylesheet" href="style.css" />
  <style>
    /* Client Interface Specific Styles */
    .client-rentals {
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

    .rentals-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 20px;
    }

    .rental-card {
        border: 1px solid #ddd;
        padding: 15px;
        border-radius: 5px;
        background-color: white;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    .rental-card img {
        width: 100%;
        height: 200px;
        object-fit: cover;
        border-radius: 4px;
        margin-bottom: 10px;
    }

    .status-label {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
    }

    .dot {
        height: 12px;
        width: 12px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 8px;
    }

    .green {
        background-color: #28a745;
    }

    .red {
        background-color: #dc3545;
    }

    .yellow {
        background-color: #ffc107;
    }

    .price {
        font-weight: bold;
        margin: 10px 0;
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
            <a href="client_rentals.php" class="active">Rentals</a>
            <a href="project.php">Apply</a>
            <a href="contact.php">Contacts</a>
            <div class="dropdown">
                <a href="client_updates.php" class="dropbtn">Updates</a>
                <div class="dropdown-content">
                    <a href="client_home.php">Your Projects</a>
                    <a href="client_rentals.php">Your Equipment Rentals</a>
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

  <div class="client-rentals">
    <div class="dashboard-content">
      <h2>Your Equipment Rentals</h2>
      
      <?php if (count($rentals) > 0): ?>
        <div class="rentals-grid">
          <?php foreach ($rentals as $rental): 
              $statusClass = strtolower($rental['RentalStatus']) === 'active' ? 'green' : 
                            (strtolower($rental['RentalStatus']) === 'pending' ? 'yellow' : 'red');
          ?>
            <div class="rental-card">
              <div class="status-label">
                <span class="dot <?= $statusClass ?>"></span>
                <?= htmlspecialchars($rental['RentalStatus']) ?>
              </div>
              
              <?php if (!empty($rental['ImagePath'])): ?>
                <img src="<?= htmlspecialchars($rental['ImagePath']) ?>" alt="<?= htmlspecialchars($rental['EquipmentName']) ?>">
              <?php endif; ?>
              
              <h3><?= htmlspecialchars($rental['EquipmentName']) ?></h3>
              <p><?= htmlspecialchars($rental['Description']) ?></p>
              
              <div class="price">
                <p>Daily: ₱<?= number_format($rental['DailyPrice'], 2) ?></p>
                <p>Weekly: ₱<?= number_format($rental['WeeklyPrice'], 2) ?></p>
                <p>Monthly: ₱<?= number_format($rental['MonthlyPrice'], 2) ?></p>
              </div>
              
              <p><strong>Rental Period:</strong><br>
                <?= date('M d, Y', strtotime($rental['RentalStartDate'])) ?> - 
                <?= date('M d, Y', strtotime($rental['RentalEndDate'])) ?>
              </p>
              
              <a href="rental_details.php?id=<?= $rental['EquipmentID'] ?>" class="rent-button">View Details</a>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <p>You don't have any equipment rentals yet.</p>
        <a href="rentals.php" class="rent-button">Browse Available Equipment</a>
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