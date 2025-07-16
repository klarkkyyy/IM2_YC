<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['UserType'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

include 'database.php'; // Move this up to ensure DB is accessible anytime

// Initialize error/success messages
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and validate inputs
    $name = trim($conn->real_escape_string($_POST['name']));
    $description = trim($conn->real_escape_string($_POST['description']));
    $availability = in_array($_POST['availability'], ['Available', 'Unavailable']) 
                  ? $_POST['availability'] : 'Available';
    $dailyPrice = filter_var($_POST['daily_price'], FILTER_VALIDATE_FLOAT, ['options' => ['min_range' => 0]]);
    $weeklyPrice = filter_var($_POST['weekly_price'], FILTER_VALIDATE_FLOAT, ['options' => ['min_range' => 0]]);
    $monthlyPrice = filter_var($_POST['monthly_price'], FILTER_VALIDATE_FLOAT, ['options' => ['min_range' => 0]]);

    if (empty($name) || empty($description) || 
        $dailyPrice === false || $weeklyPrice === false || $monthlyPrice === false) {
        $error = "Please fill all fields with valid data";
    } else {
        $stmt = $conn->prepare("INSERT INTO equipment 
                               (EquipmentName, Description, Availability, DailyPrice, WeeklyPrice, MonthlyPrice) 
                               VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssddd", $name, $description, $availability, $dailyPrice, $weeklyPrice, $monthlyPrice);

        if ($stmt->execute()) {
            $success = "Equipment added successfully!";
            $_POST = array(); // Clear form data
        } else {
            $error = "Error: " . $conn->error;
        }
        $stmt->close();
    }
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Add New Equipment</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        label {
            display: block;
            margin: 15px 0 5px;
            font-weight: bold;
        }
        input, textarea, select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        textarea {
            height: 100px;
            resize: vertical;
        }
        button {
            background: #004AAD;
            color: white;
            border: none;
            padding: 12px 20px;
            margin-top: 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        .error { color: red; margin-bottom: 10px; }
        .success { color: green; margin-bottom: 10px; }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="container">
        <h1>Add New Equipment</h1>

        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php elseif ($success): ?>
            <div class="success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <label>Equipment Name:</label>
            <input type="text" name="name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
            
            <label>Description:</label>
            <textarea name="description" required><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
            
            <label>Availability:</label>
            <select name="availability" required>
                <option value="Available" <?= ($_POST['availability'] ?? '') == 'Available' ? 'selected' : '' ?>>Available</option>
                <option value="Unavailable" <?= ($_POST['availability'] ?? '') == 'Unavailable' ? 'selected' : '' ?>>Unavailable</option>
            </select>
            
            <label>Daily Price (₱):</label>
            <input type="number" name="daily_price" step="0.01" min="0" value="<?= htmlspecialchars($_POST['daily_price'] ?? '') ?>" required>
            
            <label>Weekly Price (₱):</label>
            <input type="number" name="weekly_price" step="0.01" min="0" value="<?= htmlspecialchars($_POST['weekly_price'] ?? '') ?>" required>
            
            <label>Monthly Price (₱):</label>
            <input type="number" name="monthly_price" step="0.01" min="0" value="<?= htmlspecialchars($_POST['monthly_price'] ?? '') ?>" required>
            
            <button type="submit">Add Equipment</button>
        </form>
    </div>
    
    <?php include 'footer.php'; ?>
</body>
</html>
