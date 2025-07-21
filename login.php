<?php
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
            session_start();
            $_SESSION['User_id'] = $user['UserID'];
            $_SESSION['Username'] = $user['Username'];
            $_SESSION['User_type'] = $user['UserType'];
            
            // Redirect based on user type
            if ($_SESSION['User_type'] === 'Admin') {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: client_home.php");  // Changed to client_home.php
            }
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
  <title>Login - Construction Solution</title>
  <link rel="stylesheet" href="style.css" />
  <style>
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
    .navbar {
      background-color: #004AAD;
      padding: 1rem 0;
      display: flex;
      justify-content: center;
      align-items: center;
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
  </style>
</head>
<body>
  <div class="main-content">
    <div class="login-container">
      <h2>Login</h2>
      <form action="login.php" method="POST">
        <div class="input-group">
          <label for="username">Username</label>
          <input type="text" id="username" name="username" required value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"/>
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
      </form>
    </div>
  </div>
</body>
</html>