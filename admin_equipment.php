<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['UserType'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

require 'database.php';

// Initialize variables
$search = '';
$page = 1;
$perPage = 10;
$offset = 0;
$totalItems = 0;
$totalPages = 1;

try {
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $page = max(1, isset($_GET['page']) ? (int)$_GET['page'] : 1);
    $offset = ($page - 1) * $perPage;

    $baseQuery = "SELECT * FROM equipment";
    $whereClause = "";
    $params = [];
    $types = "";

    if (!empty($search)) {
        $whereClause = " WHERE EquipmentName LIKE ? OR Description LIKE ?";
        $searchTerm = "%$search%";
        $params = [$searchTerm, $searchTerm];
        $types = "ss";
    }

    $countQuery = "SELECT COUNT(*) FROM equipment" . $whereClause;
    $stmt = $conn->prepare($countQuery);
    
    if (!empty($search)) {
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    $totalItems = $stmt->get_result()->fetch_row()[0];
    $stmt->close();

    $totalPages = max(1, ceil($totalItems / $perPage));

    $query = $baseQuery . $whereClause . " LIMIT ?, ?";
    $stmt = $conn->prepare($query);
    
    if (!empty($search)) {
        $stmt->bind_param($types . "ii", ...$params, $offset, $perPage);
    } else {
        $stmt->bind_param("ii", $offset, $perPage);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
} catch (Exception $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin - Equipment Management</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
            line-height: 1.6;
        }
        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 5px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        .search-form {
            margin: 25px 0;
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }
        .search-form input {
            padding: 10px 15px;
            width: min(300px, 100%);
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        .search-form button {
            padding: 10px 20px;
            background: #004AAD;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            white-space: nowrap;
        }
        .clear-search {
            color: #004AAD;
            margin-left: 10px;
            text-decoration: none;
            white-space: nowrap;
        }
        .equipment-table {
            width: 100%;
            border-collapse: collapse;
            margin: 25px 0;
            overflow-x: auto;
            display: block;
        }
        .equipment-table th,
        .equipment-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            min-width: 120px;
        }
        .equipment-table th {
            background-color: #004AAD;
            color: white;
            font-weight: bold;
            position: sticky;
            top: 0;
        }
        .equipment-table tr:hover {
            background-color: #f9f9f9;
        }
        .status-badge {
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 500;
            display: inline-block;
        }
        .status-badge.available {
            background: #d4edda;
            color: #155724;
        }
        .status-badge.unavailable {
            background: #f8d7da;
            color: #721c24;
        }
        .action-links {
            display: flex;
            gap: 10px;
        }
        .action-links a {
            color: #004AAD;
            text-decoration: none;
            font-weight: 500;
        }
        .action-links a:hover {
            text-decoration: underline;
        }
        .pagination {
            display: flex;
            gap: 8px;
            margin: 30px 0;
            justify-content: center;
            flex-wrap: wrap;
        }
        .pagination a {
            padding: 8px 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            text-decoration: none;
            color: #333;
            min-width: 40px;
            text-align: center;
        }
        .pagination a.active {
            background: #004AAD;
            color: white;
            border-color: #004AAD;
        }
        .btn {
            display: inline-block;
            background: #004AAD;
            color: white;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
            margin-top: 15px;
            transition: background 0.3s;
        }
        .btn:hover {
            background: #003080;
        }
        @media (max-width: 768px) {
            .equipment-table {
                font-size: 14px;
            }
            .equipment-table th,
            .equipment-table td {
                padding: 8px 10px;
            }
        }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container">
    <h1>Equipment Management</h1>

    <form method="GET" class="search-form">
        <input type="text" name="search" placeholder="Search equipment..."
               value="<?= htmlspecialchars($search) ?>">
        <button type="submit">Search</button>
        <?php if (!empty($search)) : ?>
            <a href="admin_equipment.php" class="clear-search">Clear Search</a>
        <?php endif; ?>
    </form>

    <div class="equipment-list">
        <?php if ($totalItems > 0): ?>
            <h2>All Equipment (<?= $totalItems ?> items)</h2>
            <table class="equipment-table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['EquipmentID']) ?></td>
                        <td><?= htmlspecialchars($row['EquipmentName'] ?? 'Unknown') ?></td>
                        <td>
                            <span class="status-badge <?= strtolower($row['Availability'] ?? 'unknown') ?>">
                                <?= htmlspecialchars($row['Availability'] ?? 'Unknown') ?>
                            </span>
                        </td>
                        <td class="action-links">
                            <a href="edit_equipment.php?id=<?= $row['EquipmentID'] ?>">Edit</a>
                            <a href="delete_equipment.php?id=<?= $row['EquipmentID'] ?>"
                               onclick="return confirm('Are you sure you want to delete this equipment?')">
                                Delete
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>

            <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=1<?= !empty($search) ? '&search=' . urlencode($search) : '' ?>">First</a>
                        <a href="?page=<?= $page - 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>">Prev</a>
                    <?php endif; ?>

                    <?php
                    $start = max(1, $page - 2);
                    $end = min($totalPages, $page + 2);
                    for ($i = $start; $i <= $end; $i++): ?>
                        <a href="?page=<?= $i ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>"
                           class="<?= $i == $page ? 'active' : '' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($page < $totalPages): ?>
                        <a href="?page=<?= $page + 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>">Next</a>
                        <a href="?page=<?= $totalPages ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>">Last</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="no-results">
                <p>No equipment found<?= !empty($search) ? ' matching your search' : '' ?>.</p>
            </div>
        <?php endif; ?>

        <a href="add_equipment.php" class="btn">Add New Equipment</a>
    </div>
</div>

<?php
$stmt->close();
$result->close();
$conn->close();
include 'footer.php';
?>
</body>
</html>
