<?php
session_start();
if (!isset($_SESSION['UserID']) || $_SESSION['UserType'] !== 'admin') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>
  <?php include 'navbar.php'; ?>

  <section class="hero">
    <h1 class="index">Welcome to the Admin Dashboard</h1>
    <p class="index">
      You are logged in as an admin. Here you can manage users, update project information, and oversee system operations.
    </p>
  </section>

  <section class="content">
    <div class="white-box">
      <h2>Admin Functions</h2>
      <p>Here you can add admin-specific functions such as:</p>
      <ul>
        <li>✔ Manage Users</li>
        <li>✔ Edit Projects</li>
        <li>✔ View System Logs</li>
        <li>✔ Send Notifications</li>
      </ul>
    </div>
  </section>

  <?php include 'footer.php'; ?>
</body>
</html>
