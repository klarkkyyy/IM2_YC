<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Fallback values in case session keys are missing
$fullName = isset($_SESSION['FullName']) ? $_SESSION['FullName'] : 'Guest';
$email = isset($_SESSION['Email']) ? $_SESSION['Email'] : 'Not set';
$username = isset($_SESSION['Username']) ? $_SESSION['Username'] : 'Not set';
$userType = isset($_SESSION['User_type']) ? $_SESSION['User_type'] : 'Client';
?>
<nav class="navbar">
  <div class="logo-welcome">
    <a href="client_home.php">
      <img src="logo.png" alt="Company Logo" class="logo-centered">
    </a>
    <div class="welcome-message">Welcome, <?= htmlspecialchars($fullName) ?>!</div>
  </div>

  <div class="nav-links">
    <a href="client_projects.php">Projects</a>
    <a href="client_rentals.php">Rentals</a>
    <a href="project.php">Apply</a>
    <a href="contact.php">Contacts</a>
    <a href="client_updates.php"><i class="fa fa-bell fa-icon"></i></a>

    <div class="dropdown">
      <a href="#" id="profile-icon" title="My Profile">
        <i class="fa fa-user fa-icon"></i>
      </a>
      <div class="dropdown-content">
        <p><strong>Full Name:</strong> <?= htmlspecialchars($fullName) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($email) ?></p>
        <p><strong>Username:</strong> <?= htmlspecialchars($username) ?></p>
        <p><strong>User Type:</strong> <?= htmlspecialchars($userType) ?></p>
        <a href="logout.php" class="logout-button">Logout</a>
      </div>
    </div>
  </div>
</nav>
