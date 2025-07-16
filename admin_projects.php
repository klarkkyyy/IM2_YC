<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['UserType'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

require 'database.php';

// Initialize variables
$search = '';
$status_filter = '';
$page = 1;
$perPage = 10;
$totalItems = 0;

try {
    // Handle filters and pagination
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $status_filter = isset($_GET['status']) ? $_GET['status'] : '';
    $page = max(1, isset($_GET['page']) ? (int)$_GET['page'] : 1);
    $offset = ($page - 1) * $perPage;

    // Base query with aliasing
    $baseQuery = "SELECT 
                    p.project_id,
                    p.project_name,
                    p.description,
                    p.status AS project_status,
                    p.start_date,
                    p.end_date,
                    u.FullName as ClientName
                  FROM projects p
                  LEFT JOIN user u ON p.client_id = u.UserID";

    $whereClause = "";
    $params = [];
    $types = "";

    $conditions = [];
    if (!empty($search)) {
        $conditions[] = "(p.project_name LIKE ? OR p.description LIKE ?)";
        $searchTerm = "%$search%";
        array_push($params, $searchTerm, $searchTerm);
        $types .= "ss";
    }

    if (!empty($status_filter) && in_array($status_filter, ['Pending', 'In Progress', 'Completed'])) {
        $conditions[] = "p.status = ?";
        $params[] = $status_filter;
        $types .= "s";
    }

    if (!empty($conditions)) {
        $whereClause = " WHERE " . implode(" AND ", $conditions);
    }

    // Count query
    $countQuery = "SELECT COUNT(*) FROM projects p" . $whereClause;
    $stmt = $conn->prepare($countQuery);

    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $totalItems = $stmt->get_result()->fetch_row()[0];
    $stmt->close();

    $totalPages = max(1, ceil($totalItems / $perPage));

    // Main data query with sorting
    $sort = isset($_GET['sort']) ? $_GET['sort'] : 'start_date';
    $order = isset($_GET['order']) && strtoupper($_GET['order']) === 'ASC' ? 'ASC' : 'DESC';

    $validSortColumns = ['project_name', 'project_status', 'start_date', 'end_date'];
    $sort = in_array($sort, $validSortColumns) ? $sort : 'start_date';

    $query = $baseQuery . $whereClause . " ORDER BY $sort $order LIMIT ?, ?";
    $stmt = $conn->prepare($query);

    if (!empty($params)) {
        $stmt->bind_param($types . "ii", ...$params, $offset, $perPage);
    } else {
        $stmt->bind_param("ii", $offset, $perPage);
    }

    $stmt->execute();
    $projects = $stmt->get_result();
} catch (Exception $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Admin - Project Management</title>
    <style>
        /* Main Layout */
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
        .filters {
            display: flex;
            gap: 15px;
            margin: 25px 0;
            flex-wrap: wrap;
            align-items: center;
        }
        .filter-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .filter-group label {
            font-weight: bold;
            white-space: nowrap;
        }
        .filter-group input,
        .filter-group select {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .filter-group button {
            padding: 8px 15px;
            background: #004AAD;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .projects-table {
            width: 100%;
            border-collapse: collapse;
            margin: 25px 0;
        }
        .projects-table th,
        .projects-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .projects-table th {
            background-color: #004AAD;
            color: white;
            font-weight: bold;
            position: sticky;
            top: 0;
        }
        .projects-table th.sortable:hover {
            background-color: #003080;
            cursor: pointer;
        }
        .projects-table tr:hover {
            background-color: #f9f9f9;
        }
        .status-badge {
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 500;
            display: inline-block;
        }
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        .status-in-progress {
            background: #cce5ff;
            color: #004085;
        }
        .status-completed {
            background: #d4edda;
            color: #155724;
        }
        .action-buttons {
            display: flex;
            gap: 8px;
        }
        .btn {
            padding: 6px 12px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s;
        }
        .btn-view {
            background: #004AAD;
            color: white;
        }
        .btn-edit {
            background: #ffc107;
            color: #212529;
        }
        .btn-delete {
            background: #dc3545;
            color: white;
        }
        .btn:hover {
            opacity: 0.9;
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
        }
        .pagination a.active {
            background: #004AAD;
            color: white;
            border-color: #004AAD;
        }
        .add-project {
            display: inline-block;
            background: #004AAD;
            color: white;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
            margin-top: 15px;
        }
        .add-project:hover {
            background: #003080;
        }
        @media (max-width: 768px) {
            .filters {
                flex-direction: column;
                align-items: flex-start;
            }
            .projects-table {
                font-size: 14px;
            }
            .projects-table th,
            .projects-table td {
                padding: 8px 10px;
            }
            .action-buttons {
                flex-direction: column;
                gap: 5px;
            }
        }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container">
    <h1>Project Management</h1>

    <form method="GET" class="filters">
        <div class="filter-group">
            <label for="search">Search:</label>
            <input type="text" name="search" id="search" placeholder="Project name or description"
                   value="<?= htmlspecialchars($search) ?>">
        </div>
        <div class="filter-group">
            <label for="status">Status:</label>
            <select name="status" id="status">
                <option value="">All Statuses</option>
                <option value="Pending" <?= $status_filter === 'Pending' ? 'selected' : '' ?>>Pending</option>
                <option value="In Progress" <?= $status_filter === 'In Progress' ? 'selected' : '' ?>>In Progress</option>
                <option value="Completed" <?= $status_filter === 'Completed' ? 'selected' : '' ?>>Completed</option>
            </select>
        </div>
        <button type="submit">Apply Filters</button>
        <?php if (!empty($search) || !empty($status_filter)) : ?>
            <a href="admin_projects.php" class="btn">Clear Filters</a>
        <?php endif; ?>
    </form>

    <div class="projects-list">
        <?php if ($totalItems > 0): ?>
            <h2>All Projects (<?= $totalItems ?> found)</h2>
            <table class="projects-table">
                <thead>
                <tr>
                    <th class="sortable" onclick="sortTable('project_name')">Project Name</th>
                    <th>Client</th>
                    <th class="sortable" onclick="sortTable('project_status')">Status</th>
                    <th class="sortable" onclick="sortTable('start_date')">Start Date</th>
                    <th class="sortable" onclick="sortTable('end_date')">End Date</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php while ($project = $projects->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($project['project_name']) ?></td>
                        <td><?= htmlspecialchars($project['ClientName'] ?? 'N/A') ?></td>
                        <td>
                            <span class="status-badge status-<?= strtolower(str_replace(' ', '-', $project['project_status'])) ?>">
                                <?= htmlspecialchars($project['project_status']) ?>
                            </span>
                        </td>
                        <td><?= $project['start_date'] ? date('M d, Y', strtotime($project['start_date'])) : 'N/A' ?></td>
                        <td><?= $project['end_date'] ? date('M d, Y', strtotime($project['end_date'])) : 'Ongoing' ?></td>
                        <td>
                            <div class="action-buttons">
                                <a href="view_project.php?id=<?= $project['project_id'] ?>" class="btn btn-view">View</a>
                                <a href="edit_project.php?id=<?= $project['project_id'] ?>" class="btn btn-edit">Edit</a>
                                <a href="delete_project.php?id=<?= $project['project_id'] ?>"
                                   class="btn btn-delete"
                                   onclick="return confirm('Are you sure you want to delete this project?')">
                                    Delete
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>

            <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=1&status=<?= htmlspecialchars($status_filter) ?>&search=<?= urlencode($search) ?>&sort=<?= htmlspecialchars($sort) ?>&order=<?= htmlspecialchars($order) ?>">First</a>
                        <a href="?page=<?= $page-1 ?>&status=<?= htmlspecialchars($status_filter) ?>&search=<?= urlencode($search) ?>&sort=<?= htmlspecialchars($sort) ?>&order=<?= htmlspecialchars($order) ?>">Prev</a>
                    <?php endif; ?>

                    <?php
                    $start = max(1, $page - 2);
                    $end = min($totalPages, $page + 2);
                    for ($i = $start; $i <= $end; $i++): ?>
                        <a href="?page=<?= $i ?>&status=<?= htmlspecialchars($status_filter) ?>&search=<?= urlencode($search) ?>&sort=<?= htmlspecialchars($sort) ?>&order=<?= htmlspecialchars($order) ?>"
                           class="<?= $i == $page ? 'active' : '' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($page < $totalPages): ?>
                        <a href="?page=<?= $page+1 ?>&status=<?= htmlspecialchars($status_filter) ?>&search=<?= urlencode($search) ?>&sort=<?= htmlspecialchars($sort) ?>&order=<?= htmlspecialchars($order) ?>">Next</a>
                        <a href="?page=<?= $totalPages ?>&status=<?= htmlspecialchars($status_filter) ?>&search=<?= urlencode($search) ?>&sort=<?= htmlspecialchars($sort) ?>&order=<?= htmlspecialchars($order) ?>">Last</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="no-results">
                <p>No projects found<?= !empty($search) || !empty($status_filter) ? ' matching your filters' : '' ?>.</p>
            </div>
        <?php endif; ?>

        <a href="add_project.php" class="add-project">Add New Project</a>
    </div>
</div>

<script>
    function sortTable(column) {
        const url = new URL(window.location.href);
        const currentSort = url.searchParams.get('sort');
        const currentOrder = url.searchParams.get('order');

        if (currentSort === column) {
            url.searchParams.set('order', currentOrder === 'ASC' ? 'DESC' : 'ASC');
        } else {
            url.searchParams.set('sort', column);
            url.searchParams.set('order', 'ASC');
        }

        window.location.href = url.toString();
    }
</script>

<?php
if (isset($projects)) {
    $projects->close();
}
$conn->close();
include 'footer.php';
?>
</body>
</html>
