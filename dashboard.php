<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: Index.php");
  exit();
}
echo "Welcome, " . $_SESSION['username'];
?>
