<?php
session_start();
if (!isset($_SESSION['User_id'])) {
    header("Location: login.php");
    exit();
}

require 'database.php';
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->error);
}

// Initialize variables
$welcomeName = $_SESSION['Username'];
$projectCount = 0;
$activeRentals = 0;

try {
    // You can add your database queries here for stats like $projectCount if needed.
} catch (Exception $e) {
    error_log($e->getMessage());
    // Continue with default values if queries fail
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Client Home - Yosech Construction</title>
  <link rel="stylesheet" href="style.css" />
  <style>
    /* Keep all navbar styles consistent with the original design */
    .navbar {
      background-color: #004AAD;
      padding: 1rem 2rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .logo-welcome {
      display: flex;
      align-items: center;
      gap: 2rem;
    }

    .logo-centered {
      height: 40px;
    }

    .welcome-message {
      color: white;
      font-weight: bold;
      font-size: 1.1rem;
    }

    .nav-links {
      display: flex;
      gap: 1.5rem;
      align-items: center;
    }

    .nav-links a {
      color: white;
      text-decoration: none;
      font-weight: bold;
      padding: 0.5rem 0;
      position: relative;
    }

    .nav-links a:hover {
      text-decoration: underline;
    }

    .nav-links a.active {
      font-weight: bolder;
      text-decoration: underline;
    }

    .logout-btn {
      color: white;
      padding: 0.5rem 1rem;
      border-radius: 4px;
      text-decoration: none;
      font-weight: bold;
      transition: background 0.3s ease;
    }

    .logout-btn:hover {
      text-decoration: none;
    }
  </style>
</head>
<body>
  <!-- Navbar -->
  <nav class="navbar">
    <div class="logo-welcome">
      <a href="client_home.php">
        <img src="logo.png" alt="Company Logo" class="logo-centered">
      </a>
      <div class="welcome-message">Welcome, <?= htmlspecialchars($welcomeName) ?></div>
    </div>

    <div class="nav-links">
      <a href="client_projects.php">Projects</a>
      <a href="client_rentals.php">Rentals</a>
      <a href="project.php">Apply</a>
      <a href="client_updates.php">Updates</a>
      <a href="contact.php">Contacts</a>
      <a href="logout.php" class="logout-btn">Logout</a>
    </div>
  </nav>

  <!-- Hero Section -->
  <section class="hero">
    <h1 class="index">Welcome to Yosech Construction</h1>
    <p class="index">Yosech Construction is a trusted construction firm based in Dipolog City, delivering high-quality road infrastructure, residential and commercial buildings, flood control projects, and reliable equipment rentals.</p>
    <a href="client_rentals.php" class="rentals-button">Browse Rentals</a>
  </section>

  <?php include 'footer.php'; ?>

  <script>
    // Highlight the active tab based on current page
    document.addEventListener('DOMContentLoaded', function() {
      const currentPage = location.pathname.split('/').pop();
      document.querySelectorAll('.nav-links a').forEach(link => {
        if (link.getAttribute('href') === 'logout.php') return;

        if (link.getAttribute('href') === currentPage) {
          link.classList.add('active');
        }
      });
    });
  </script>
</body>
</html>
