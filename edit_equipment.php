<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['UserType'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

require 'database.php';

// Check and sanitize the ID
$id = $_GET['id'] ?? null;
if ($id === null || !is_numeric($id)) {
    header("Location: admin_equipment.php?error=InvalidID");
    exit();
}

// Fetch equipment details
$stmt = $conn->prepare("SELECT * FROM equipment WHERE EquipmentID = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$equipment = $result->fetch_assoc();
$stmt->close();

if (!$equipment) {
    header("Location: admin_equipment.php?error=NotFound");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['Name']);
    $category = trim($_POST['Category']);
    $description = trim($_POST['Description']);
    $rate = $_POST['RentalRate'];

    // Simple validation
    if (!empty($name) && !empty($category) && is_numeric($rate)) {
        $stmt = $conn->prepare("
            UPDATE equipment
            SET Name = ?, Category = ?, Description = ?, RentalRate = ?
            WHERE EquipmentID = ?
        ");
        $stmt->bind_param("sssdi", $name, $category, $description, $rate, $id);
        $stmt->execute();
        $stmt->close();

        $conn->close();

        header("Location: admin_equipment.php?updated=1");
        exit();
    } else {
        $error = "Please fill in all required fields correctly.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Equipment</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 700px;
            margin: 30px auto;
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }

        h1 {
            color: #004AAD;
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="number"],
        textarea {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        button {
            margin-top: 20px;
            padding: 10px 20px;
            background: #004AAD;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background: #003080;
        }

        .btn-cancel {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }

        .btn-cancel:hover {
            background: #5a6268;
        }

        .error {
            color: #dc3545;
            margin-top: 15px;
            font-weight: bold;
        }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container">
    <h1>Edit Equipment</h1>

    <?php if (!empty($error)): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post">
        <label for="Name">Equipment Name:</label>
        <input type="text" name="Name" id="Name" required
               value="<?= htmlspecialchars($equipment['Name']) ?>">

        <label for="Category">Category:</label>
        <input type="text" name="Category" id="Category" required
               value="<?= htmlspecialchars($equipment['Category']) ?>">

        <label for="Description">Description:</label>
        <textarea name="Description" id="Description" rows="5"><?= htmlspecialchars($equipment['Description']) ?></textarea>

        <label for="RentalRate">Rental Rate:</label>
        <input type="number" name="RentalRate" id="RentalRate" step="0.01" required
               value="<?= htmlspecialchars($equipment['RentalRate']) ?>">

        <button type="submit">Update Equipment</button>
        <a href="admin_equipment.php" class="btn-cancel">Cancel</a>
    </form>
</div>

<?php include 'footer.php'; ?>
</body>
</html>
