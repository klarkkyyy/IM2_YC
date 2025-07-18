<?php
ob_start(); // Start output buffering at the VERY TOP
session_start();

require 'database.php';

$usernameError = '';
$passwordError = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $query = "SELECT * FROM user WHERE Username = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);
        if (password_verify($password, $user['Password'])) {
            // Only set session variables here (no duplicate session_start())
            $_SESSION['User_id'] = $user['UserID'];
            $_SESSION['Username'] = $user['Username'];
            $_SESSION['User_type'] = $user['UserType'];
            
            // Redirect based on user type
            if ($_SESSION['User_type'] === 'Admin') {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: client_dashboard.php");
            }
            exit();
        } else {
            $passwordError = 'Incorrect password.';
        }
    } else {
        $usernameError = 'Account not found.';
    }
}
ob_end_flush(); // Send output buffer and turn off buffering
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login - Construction Solution</title>
  <link rel="stylesheet" href="style.css" />
  <style>
    /* Your existing CSS styles remain unchanged */
    html, body {
      margin: 0;
      padding: 0;
      height: 100%;
    }
    body {
    font-family: Arial, sans-serif;
    min-height: 100vh;
    background: url('Background.jpg') no-repeat center center fixed;
    background-size: cover;
    margin: 0;
    padding: 0;
    display: flex;
    flex-direction: column;
    }
    /* ... rest of your CSS ... */
  </style>
</head>
<body>

  <div class="main-content">
    <div class="login-container">
      <h2>Login</h2>
      <form action="login.php" method="POST">
        <div class="input-group">
          <label for="username">Username</label>
          <input type="text" id="username" name="username" required 
                 value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"/>
          <?php if (!empty($usernameError)): ?>
            <div class="error"><?= htmlspecialchars($usernameError) ?></div>
          <?php endif; ?>
        </div>
        <div class="input-group">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" required />
          <?php if (!empty($passwordError)): ?>
            <div class="error"><?= htmlspecialchars($passwordError) ?></div>
          <?php endif; ?>
        </div>
        <button type="submit">Login</button>
        
        <p style="margin-top: 20px; font-size: 14px; color: #333;">
          Don't have an account? 
          <a href="register.php" style="color: #004AAD; font-weight: bold;">Register here</a>
        </p>
      </form>
    </div>
  </div>

  <?php include 'footer.php'; ?>
</body>
</html>