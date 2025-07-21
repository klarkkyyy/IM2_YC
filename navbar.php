<?php
session_start();
$isClient = isset($_SESSION['User_id']) && $_SESSION['User_type'] === 'Client';
?>

<nav class="navbar">
    <div class="navbar-center">
        <a href="Index.php">
            <img src="logo.png" alt="Logo" class="logo-centered">
        </a>
        <div class="nav-links">
            <a href="Projects.php">Projects</a>
            <a href="rentals.php">Rentals</a>
            <a href="project.php">Apply</a>
        </div>
    </div>
    <div class="navbar-right">
        <div class="nav-links">
            <a href="Contact.php">Contacts</a>

            <?php if ($isClient): ?>
                <a href="client_profile.php">My Profile</a>
            <?php else: ?>
                <a href="login.php">Login</a>
            <?php endif; ?>
        </div>
    </div>
</nav>
