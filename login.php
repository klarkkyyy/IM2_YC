<?php
require 'database.php';

$usernameError = '';
$passwordError = '';
$loginFailed = false;

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
    session_start();
    $_SESSION['UserID'] = $user['UserID'];
    $_SESSION['Username'] = $user['Username'];+
    header("Location: dashboard.php");
    exit();
  } else {
    $passwordError = 'Incorrect password.';
  }
} else {
  $usernameError = 'Account not found.';
}

}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Rentals - Construction Solution</title>
  <style>
    /* same CSS as before — unchanged */
    html, body {
      margin: 0;
      padding: 0;
      height: 100%;
    }
    body {
      font-family: Arial, sans-serif;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      background-color: #f0f0f0;
      background-image: url('1.jpg');
      background-size: cover;
      background-position: center;
    }
    .navbar {
      background-color: #004AAD;
      padding: 1rem 0;
      display: flex;
      justify-content: center;
      align-items: center;
    }
    .navbar-center {
      display: flex;
      align-items: center;
      gap: 2rem;
      flex-wrap: wrap;
      justify-content: center;
    }
    .logo-centered {
      height: 70px;
    }
    .nav-links {
      display: flex;
      gap: 1.5rem;
      flex-wrap: wrap;
    }
    .nav-links a {
      color: white;
      text-decoration: none;
      font-weight: bold;
      font-size: 1rem;
    }
    .nav-links a:hover {
      text-decoration: underline;
    }
    .main-content {
      flex: 1;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 20px;
    }
    .login-container {
      background-color: rgba(255, 255, 255, 0.9);
      padding: 30px 50px;
      box-shadow: 0 0 50px rgba(0, 0, 0, 0.2);
      border-radius: 10px;
      text-align: center;
      width: 500px;
      max-width: 90%;
    }
    h2 {
      margin-bottom: 20px;
      color: black;
      font-size: 32px;
    }
    .input-group {
      margin-bottom: 15px;
      text-align: left;
    }
    label {
      display: block;
      margin-bottom: 5px;
      color: black;
      font-weight: bold;
    }
    input {
      width: 95%;
      padding: 10px;
      border: 1px solid #ddd;
      border-radius: 5px;
      font-size: 16px;
    }
    .error {
      color: red;
      font-size: 14px;
      margin-top: 5px;
    }
    .login-container button {
      width: 100%;
      padding: 15px;
      background-color: #004AAD;
      color: #ffffff;
      border: none;
      border-radius: 8px;
      font-size: 24px;
      font-weight: bold;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }
    .login-container button:hover {
      background-color: #00307d;
    }
    footer {
      background-color: #004AAD;
      color: white;
      display: flex;
      justify-content: space-between;
      flex-wrap: wrap;
      padding: 20px;
      width: 100%;
    }
    .footer-section {
      flex: 1;
      padding: 10px;
    }
    .footer-logo {
      width: 175px;
      height: auto;
    }
    .social-icons {
      display: flex;
      justify-content: center;
      margin-top: 10px;
      flex-wrap: wrap;
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

  <div class="main-content">
    <div class="login-container">
      <h2>Login</h2>
      <form action="login.php" method="POST">
        <div class="input-group">
          <label for="username">Username</label>
          <input type="text" id="username" name="username" required />
          <?php if (!empty($usernameError)): ?>
            <div class="error"><?php echo $usernameError; ?></div>
          <?php endif; ?>
        </div>
        <div class="input-group">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" required />
          <?php if (!empty($passwordError)): ?>
            <div class="error"><?php echo $passwordError; ?></div>
          <?php endif; ?>
        </div>
        <button type="submit">Login</button>
        <p style="margin-top: 20px; font-size: 14px; color: #333;">
          Don't have an account yet?
          <a href="register.php" style="color: #004AAD; text-decoration: underline; font-weight: bold;">
            Register Here
          </a>
        </p>
      </form>
    </div>
  </div>

</body>
</html>
