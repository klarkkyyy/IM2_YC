<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['UserType'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

require 'database.php';

// Initialize variables
$search = '';
$role_filter = '';
$page = 1;
$perPage = 10;
$totalItems = 0;

try {
    // Handle filters and pagination
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $role_filter = isset($_GET['role']) ? $_GET['role'] : '';
    $page = max(1, isset($_GET['page']) ? (int)$_GET['page'] : 1);
    $offset = ($page - 1) * $perPage;

    // Base query
    $baseQuery = "SELECT UserID, Username, Email, FullName, UserType, 
                 DATE_FORMAT(RegistrationDate, '%M %e, %Y') AS FormattedDate 
                 FROM user";

    $whereClause = "";
    $params = [];
    $types = "";

    // Build WHERE conditions
    $conditions = [];
    if (!empty($search)) {
        $conditions[] = "(Username LIKE ? OR Email LIKE ? OR FullName LIKE ?)";
        $searchTerm = "%$search%";
        array_push($params, $searchTerm, $searchTerm, $searchTerm);
        $types .= "sss";
    }

    if (!empty($role_filter) && in_array($role_filter, ['Admin', 'Client'])) {
        $conditions[] = "UserType = ?";
        $params[] = $role_filter;
        $types .= "s";
    }

    if (!empty($conditions)) {
        $whereClause = " WHERE " . implode(" AND ", $conditions);
    }

    // Count query for pagination
    $countQuery = "SELECT COUNT(*) FROM user" . $whereClause;
    $stmt = $conn->prepare($countQuery);

    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $totalItems = $stmt->get_result()->fetch_row()[0];
    $stmt->close();

    $totalPages = max(1, ceil($totalItems / $perPage));

    // Main data query with sorting
    $sort = isset($_GET['sort']) ? $_GET['sort'] : 'RegistrationDate';
    $order = isset($_GET['order']) && strtoupper($_GET['order']) === 'ASC' ? 'ASC' : 'DESC';

    $validSortColumns = ['Username', 'Email', 'FullName', 'UserType', 'RegistrationDate'];
    $sort = in_array($sort, $validSortColumns) ? $sort : 'RegistrationDate';

    $query = $baseQuery . $whereClause . " ORDER BY $sort $order LIMIT ?, ?";
    $stmt = $conn->prepare($query);

    if (!empty($params)) {
        $stmt->bind_param($types . "ii", ...$params, $offset, $perPage);
    } else {
        $stmt->bind_param("ii", $offset, $perPage);
    }

    $stmt->execute();
    $users = $stmt->get_result();
} catch (Exception $e) {
    die("Database error: " . htmlspecialchars($e->getMessage()));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Admin - User Management</title>
    <style>
        /* (CSS unchanged - left out here for brevity) */
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container">
        <h1>User Management</h1>

        <!-- Filters Section -->
        <form method="GET" class="filters">
            <div class="filter-group">
                <label for="search">Search:</label>
                <input type="text" name="search" id="search" placeholder="Username, email or name"
                       value="<?= htmlspecialchars($search) ?>">
            </div>

            <div class="filter-group">
                <label for="role">Role:</label>
                <select name="role" id="role">
                    <option value="">All Roles</option>
                    <option value="Admin" <?= $role_filter === 'Admin' ? 'selected' : '' ?>>Admin</option>
                    <option value="Client" <?= $role_filter === 'Client' ? 'selected' : '' ?>>Client</option>
                </select>
            </div>

            <button type="submit">Apply Filters</button>
            <?php if (!empty($search) || !empty($role_filter)): ?>
                <a href="admin_users.php" class="btn">Clear Filters</a>
            <?php endif; ?>
        </form>

        <!-- Users Table -->
        <div class="users-list">
            <?php if ($totalItems > 0): ?>
                <h2>All Users (<?= $totalItems ?> found)</h2>

                <table class="users-table">
                    <thead>
                    <tr>
                        <th class="sortable" onclick="sortTable('Username')">Username</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th class="sortable" onclick="sortTable('UserType')">Role</th>
                        <th class="sortable" onclick="sortTable('RegistrationDate')">Registered</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php while ($user = $users->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($user['Username']) ?></td>
                            <td><?= htmlspecialchars($user['FullName']) ?></td>
                            <td><?= htmlspecialchars($user['Email']) ?></td>
                            <td>
                                <span class="role-badge role-<?= strtolower($user['UserType']) ?>">
                                    <?= htmlspecialchars($user['UserType']) ?>
                                </span>
                            </td>
                            <td><?= $user['FormattedDate'] ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="edit_user.php?id=<?= $user['UserID'] ?>" class="btn btn-edit">Edit</a>
                                    <a href="change_role.php?id=<?= $user['UserID'] ?>" class="btn btn-change-role">
                                        <?= $user['UserType'] === 'Admin' ? 'Demote' : 'Promote' ?>
                                    </a>
                                    <a href="delete_user.php?id=<?= $user['UserID'] ?>"
                                       class="btn btn-delete"
                                       onclick="return confirm('Are you sure? This cannot be undone.')">
                                        Delete
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?= $page-1 ?>&role=<?= htmlspecialchars($role_filter) ?>&search=<?= urlencode($search) ?>&sort=<?= htmlspecialchars($sort) ?>&order=<?= htmlspecialchars($order) ?>">Prev</a>
                        <?php endif; ?>

                        <?php
                        $start = max(1, $page - 2);
                        $end = min($totalPages, $page + 2);
                        for ($i = $start; $i <= $end; $i++):
                        ?>
                            <a href="?page=<?= $i ?>&role=<?= htmlspecialchars($role_filter) ?>&search=<?= urlencode($search) ?>&sort=<?= htmlspecialchars($sort) ?>&order=<?= htmlspecialchars($order) ?>"
                               class="<?= $i == $page ? 'active' : '' ?>">
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>

                        <?php if ($page < $totalPages): ?>
                            <a href="?page=<?= $page+1 ?>&role=<?= htmlspecialchars($role_filter) ?>&search=<?= urlencode($search) ?>&sort=<?= htmlspecialchars($sort) ?>&order=<?= htmlspecialchars($order) ?>">Next</a>
                            <a href="?page=<?= $totalPages ?>&role=<?= htmlspecialchars($role_filter) ?>&search=<?= urlencode($search) ?>&sort=<?= htmlspecialchars($sort) ?>&order=<?= htmlspecialchars($order) ?>">Last</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="no-results">
                    <p>No users found<?= !empty($search) || !empty($role_filter) ? ' matching your filters' : '' ?>.</p>
                </div>
            <?php endif; ?>

            <a href="register.php" class="add-user">Add New User</a>
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
    if (isset($users)) {
        $users->close();
    }
    $conn->close();
    include 'footer.php';
    ?>
</body>
</html>
