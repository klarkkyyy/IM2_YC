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
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Client Home - Yosech Construction</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    .navbar {
      background-color: #004AAD;
      padding: 1rem 2rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
    }

    .logo-welcome {
      display: flex;
      align-items: center;
      gap: 1.5rem;
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
      flex-wrap: wrap;
    }

    .nav-links a {
      color: white;
      text-decoration: none;
      font-weight: bold;
      padding: 0.5rem 0;
      position: relative;
      display: flex;
      align-items: center;
      gap: 0.4rem;
    }

    .nav-links a:hover {
      text-decoration: underline;
    }

    .nav-links a.active {
      font-weight: bolder;
      text-decoration: underline;
    }

    .fa-icon {
      font-size: 1.2rem;
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
      <div class="welcome-message">Welcome, <?= htmlspecialchars($welcomeName) ?>!</div>
    </div>

    <div class="nav-links">
      <a href="client_projects.php">Projects</a>
      <a href="client_rentals.php">Rentals</a>
      <a href="project.php">Apply</a>
      <a href="contact.php">Contacts</a>
      <a href="client_updates.php"><i class="fa fa-bell fa-icon"></i></a>
      <a href="client_profile.php" title="My Profile"><i class="fa fa-user fa-icon"></i></a>
    </div>
  </nav>

  <!-- Hero Section -->
  <section class="hero">
    <h1 class="index">Welcome to Yosech Construction</h1>
    <p class="index">
      Yosech Construction is a trusted construction firm based in Dipolog City, delivering high-quality road infrastructure, residential and commercial buildings, flood control <br>
      projects, and reliable equipment rentals. With decades of experience, we blend traditional craftsmanship with modern techniques to create structures that stand the test <br>
      of time while embracing sustainability and cultural authenticity.
    </p>
    <a href="client_rentals.php" class="rentals-button">Browse Rentals</a>
  </section>

  <!-- Content Section -->
  <section class="content">
    <div class="content-left">
      <div class="blue-bg">
        <h2>147</h2>
        <p>Completed Projects</p>
      </div>
      <div class="red-bg">
        <h2>25+</h2>
        <p>Years of Experience</p>
      </div>
    </div>
    <div class="content-right">
      <div class="white-box">
        <h2>We Construct and Manage Places and Infrastructures</h2>

        <div class="service">
          <img src="Shape.png" alt="General Contract">
          <div>
            <h3>Road Construction & Infrastructure Projects</h3>
            <p>We build durable roads and vital infrastructure that connect communities.</p>
          </div>
        </div>

        <div class="service">
          <img src="Shape2.png" alt="Project Planning">
          <div>
            <h3>Residential & Commercial Building Construction</h3>
            <p>From homes to businesses, we create safe and modern spaces for living and working.</p>
          </div>
        </div>

        <div class="service">
          <img src="Shape3.png" alt="Refurbishment">
          <div>
            <h3>Flood Control & Drainage Solutions</h3>
            <p>Our projects protect communities with reliable flood control and drainage systems.</p>
          </div>
        </div>

        <div class="service">
          <img src="Shape2.png" alt="Heavy Equipment Rentals">
          <div>
            <h3>Heavy Equipment Rentals</h3>
            <p>Rent high-quality construction equipment for your projects with ease and confidence.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Additional Content Section -->
  <section class="additional-content">
    <div>
      <h3 class="highlighted-title" style="text-align: center;">Construction</h3>
      <p class="white-paragraph">
        Our project managers and superintendents are very experienced in completing highly specialized, multi-faceted projects.
        Their ability to foresee and therefore prevent problems is invaluable. Construction mastery aids in the design process
        of the CM/GC and Design-Build delivery methods because we know what is feasible and have a large repertoire of means
        and methods. Yoztech is uniquely qualified in tilt-up concrete construction. We know what is feasible and we have the
        means and methods to accomplish the goal.
      </p>
    </div>

    <div>
      <h3 class="highlighted-title" style="text-align: center;">Engineering</h3>
      <p class="white-paragraph">
        Our engineers and designers are qualified to solve the challenges presented in today's design and construction market.
        We have experience in a variety of projects and delivery methods. We know what is feasible and we have the means and
        methods to accomplish the goal.
      </p>
    </div>

    <div>
      <h3 class="highlighted-title" style="text-align: center;">Innovation</h3>
      <p class="white-paragraph">
        We are committed to continually learning and improving. This pursuit of knowledge drives our team to innovative solutions.
        We are innovative and will draw on all of our resources to find solutions to meet owner needs, even if that means inventing
        something new.
      </p>
    </div>
  </section>

  <?php include 'footer.php'; ?>

  <script>
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
