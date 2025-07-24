<?php
// apply.php
session_start();
require 'database.php';

$equipmentName = $_GET['equipment'] ?? 'Unknown Equipment';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['User_id'])) {
        echo "You must be logged in to apply.";
        exit();
    }

    $clientID = $_SESSION['User_id'];
    $applicationType = "Equipment Rental";
    $equipmentName = $_POST['equipment_name'] ?? '';
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $company = $_POST['company'] ?? '';
    $status = "Pending";
    $submissionDate = date('Y-m-d H:i:s');

    $description = "Equipment: $equipmentName\nName: $name\nEmail: $email\nPhone: $phone\nCompany: $company";

    $clientCheck = $conn->prepare("SELECT ClientID FROM client WHERE UserID = ?");
    $clientCheck->bind_param("i", $_SESSION['User_id']);
    $clientCheck->execute();
    $clientResult = $clientCheck->get_result();

    if ($clientRow = $clientResult->fetch_assoc()) {
        $clientID = $clientRow['ClientID'];
    } else {
        // Insert this user as a new client
        $insertClient = $conn->prepare("INSERT INTO client (UserID, CompanyName, ContactInfo) VALUES (?, ?, ?)");
        $insertClient->bind_param("iss", $_SESSION['User_id'], $company, $phone);
        $insertClient->execute();
        $clientID = $conn->insert_id;
    }
    $stmt = $conn->prepare("INSERT INTO application (ClientID, ApplicationType, Description, SubmissionDate, Status) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $clientID, $applicationType, $description, $submissionDate, $status);
    $equipmentId = $_POST['equipment_id'];
    $update = $conn->prepare("UPDATE equipment SET Availability = 'Unavailable' WHERE EquipmentID = ?");
    $update->bind_param("i", $equipmentId);
    $update->execute();
    if ($stmt->execute()) {
        header("Location: thank_you.php");
        exit();
    } else {
        echo "Application failed: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Construction Solutions - Apply Now</title>
  <link rel="stylesheet" href="style.css" />
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      background-color: #f4f4f4;
    }
    .header {
      background: url('Background.jpg') no-repeat center center/cover;
      position: relative;
      height: 40vh;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      text-align: center;
      color: white;
    }
    .header::before {
      content: "";
      position: absolute;
      inset: 0;
      background-color: rgba(0, 0, 0, 0.5);
      z-index: 0;
    }
    .header h1 {
      font-size: 3rem;
      margin: 0;
      position: relative;
      z-index: 1;
    }
    .header p {
      font-size: 1.25rem;
      margin: 1rem 0;
      position: relative;
      z-index: 1;
    }
    .application-form {
      max-width: 700px;
      margin: 2rem auto;
      padding: 2rem;
      background-color: white;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
      border-radius: 8px;
    }
    .application-form h2 {
      font-size: 1.5rem;
      color: #333;
      margin-bottom: 1.5rem;
    }
    .application-form label {
      display: block;
      margin-bottom: 0.5rem;
      color: #333;
    }
    .application-form input {
      width: 100%;
      padding: 0.75rem;
      margin-bottom: 1rem;
      border: 1px solid #ccc;
      border-radius: 5px;
    }
    .application-form .purpose-buttons {
      display: flex;
      justify-content: center;
      gap: 2rem;
      margin-top: 1.5rem;
    }
    .application-form .purpose-buttons a {
      text-decoration: none;
    }
    .application-form .purpose-buttons button {
      background-color: #004AAD;
      color: white;
      border: none;
      padding: 0.8rem 1.5rem;
      border-radius: 30px;
      font-size: 1rem;
      cursor: pointer;
      transition: background 0.3s ease;
    }
    .application-form .purpose-buttons button:hover {
      background-color: #003080;
    }
    footer {
      background-color: #004AAD;
      color: white;
      display: flex;
      justify-content: space-between;
      padding: 20px;
    }
    .footer-section {
      flex: 1;
      padding: 10px;
    }
    .footer-section h3 {
      margin-top: 0;
      font-size: 1.2em;
    }
    .footer-section p {
      margin: 5px 0;
    }
    .social-icons {
      display: flex;
      justify-content: center;
      margin-top: 10px;
    }
    .social-icons a {
      color: white;
      margin: 0 10px;
      text-decoration: none;
      display: flex;
      align-items: center;
    }
    .forlogo {
      display: flex;
      height: 120px;
      width: 100px;
    }
    .social-icons a img {
      height: 20px;
      margin-right: 5px;
    }
    @media (max-width: 768px) {
      .navbar {
        flex-direction: column;
      }
      .application-form .purpose-buttons {
        flex-direction: column;
        gap: 1rem;
      }
    }
    .navbar-center {
      display: flex;
      align-items: center;
      gap: 2rem;
      flex-wrap: wrap;
      justify-content: flex-start;
    }
    .rent-button-wrapper {
      display: flex;
      justify-content: center;
      margin-top: 10px;
    }
    .navbar-right {
      margin-left: auto;
      display: flex;
      align-items: center;
      margin-right: 30px;
    }
  </style>
</head>
<body>
  <?php include 'navbar.php'; ?>
  <main>
  <section class="header">
    <h1 class="apply">Start Your Project or Equipment Rental Today</h1>
    <p class="apply">Ready to bring your plans to life? Submit your application below to start a new construction project or to rent the equipment you need.</p>
  </section>
  <div class="application-form">
    <h2>Application Form</h2>
<?php
include 'database.php';
if (isset($_GET['equipment'])) {
  $equipmentName = $_GET['equipment'];
  $stmt = $conn->prepare("SELECT * FROM equipment WHERE EquipmentName = ?");
  $stmt->bind_param("s", $equipmentName);
  $stmt->execute();
  $result = $stmt->get_result();
  if ($row = $result->fetch_assoc()) {
    ?>
      <div class="equipment-preview">
        <div class="rental-card <?= strtolower($row['Availability']) ?>" style="margin: 20px 0;">
          <div class="status-label">
            <span class="dot <?= $row['Availability'] === 'Available' ? 'green' : 'red' ?>"></span> <?= $row['Availability'] ?>
          </div>
          <img src="<?= htmlspecialchars($row['ImagePath']) ?>" alt="<?= htmlspecialchars($row['EquipmentName']) ?>" style="width: 100%; height: 150px; object-fit: contain;">
          <div class="card-body">
            <h2 class="title" style="text-align: center; color: orange;"><?= htmlspecialchars($row['EquipmentName']) ?></h2>
            <p><?= nl2br(htmlspecialchars($row['Description'])) ?></p>
            <p class="price" style="font-weight: bold;">
              Daily: ₱<?= number_format($row['DailyPrice']) ?><br>
              Weekly: ₱<?= number_format($row['WeeklyPrice']) ?><br>
              Monthly: ₱<?= number_format($row['MonthlyPrice']) ?>
            </p>
        </div>
      </div>
    </div>
    <?php
  } else {
    echo "<p style='color: red;'>Equipment not found.</p>";
  }
}
?>
  <div class="form-details">
  </div>
    <form method="POST" action="">
      <input type="hidden" name="equipment_name" value="<?= htmlspecialchars($equipmentName) ?>">
      <input type="hidden" name="equipment_id" value="<?= htmlspecialchars($row['EquipmentID']) ?>">
      <label for="name">Name:</label>
      <input type="text" id="name" name="name" required>

      <label for="email">Email:</label>
      <input type="email" id="email" name="email" required>

      <label for="phone">Phone:</label>
      <input type="tel" id="phone" name="phone" required>

      <label for="company">Company Name:</label>
      <input type="text" id="company" name="company">

      <div class="purpose-buttons">
        <button type="submit">Submit Application</button>
      </div>
    </form>
  </div>
    </main>
  <?php include 'footer.php'; ?>
</body>
</html>
