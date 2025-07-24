<?php
require 'database.php';

$email = $fullname = $username = '';
$password = $confirm = '';
$emailError = $fullnameError = $usernameError = $passwordError = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $fullname = trim($_POST["fullname"]);
    $username = trim($_POST["username"]);
    $password = $_POST["password"];
    $confirm = $_POST["confirm"];
    $usertype = $_POST["usertype"];
    $hasError = false;

    // Check if passwords match
    if ($password !== $confirm) {
        $passwordError = "Passwords do not match.";
        $hasError = true;
    }

    // Check if email or username already exists
    $check = $conn->prepare("SELECT * FROM user WHERE Username = ? OR Email = ?");
    $check->bind_param("ss", $username, $email);
    $check->execute();
    $result = $check->get_result();

    while ($row = $result->fetch_assoc()) {
        if ($row["Username"] === $username) {
            $usernameError = "Username is already taken.";
            $hasError = true;
        }
        if ($row["Email"] === $email) {
            $emailError = "Email is already in use.";
            $hasError = true;
        }
    }

    if (!$hasError) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO user (Email, FullName, Username, Password, UserType) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $email, $fullname, $username, $hashed_password, $usertype);

        if ($stmt->execute()) {
    $stmt = $conn->prepare("INSERT INTO user (Email, FullName, Username, Password, UserType) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $email, $fullname, $username, $hashed_password, $usertype);

if ($stmt->execute()) {
    $newUserId = $conn->insert_id;

    // Insert into client table if user is a client
    if ($usertype === 'Client') {
        $clientStmt = $conn->prepare("INSERT INTO client (ClientID) VALUES (?)");
        $clientStmt->bind_param("i", $newUserId);
        $clientStmt->execute();
        $clientStmt->close();
    }

    header("Location: login.php");
    exit();
} else {
            $usernameError = "Something went wrong. Please try again.";
        }

        $stmt->close();
    }

    $check->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Register</title>
  <link rel="stylesheet" href="style.css" />
  <style>
    .error {
      color: red;
      font-size: 14px;
      margin-top: 5px;
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
  </style>
</head>
<body>
  <div class="main-content">
    <div class="login-container">
      <h2>Register</h2>
      <form action="register.php" method="POST">
        <div class="input-group">
          <label for="fullname">Full Name</label>
          <input type="text" id="fullname" name="fullname" value="<?= htmlspecialchars($fullname) ?>" required />
          <?php if (!empty($fullnameError)): ?>
            <div class="error"><?= $fullnameError ?></div>
          <?php endif; ?>
        </div>

        <div class="input-group">
          <label for="username">Username</label>
          <input type="text" id="username" name="username" value="<?= htmlspecialchars($username) ?>" required />
          <?php if (!empty($usernameError)): ?>
            <div class="error"><?= $usernameError ?></div>
          <?php endif; ?>
        </div>

        <div class="input-group">
          <label for="email">Email</label>
          <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required />
          <?php if (!empty($emailError)): ?>
            <div class="error"><?= $emailError ?></div>
          <?php endif; ?>
        </div>

        <div class="input-group">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" required />
        </div>

        <div class="input-group">
          <label for="confirm">Confirm Password</label>
          <input type="password" id="confirm" name="confirm" required />
          <?php if (!empty($passwordError)): ?>
            <div class="error"><?= $passwordError ?></div>
          <?php endif; ?>
        </div>

        <input type="hidden" name="usertype" value="Client" />

        <button type="submit">Register</button>
      </form>
    </div>
  </div>

</body>
</html>
