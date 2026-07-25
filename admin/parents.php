<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
check_role('admin');

$stmt = $pdo->query("SELECT id, full_name, username, created_at FROM users WHERE role = 'parent' ORDER BY id DESC");
$parents = $stmt->fetchAll();

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container">
    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h2>Parents Management</h2>
            <a href="add_parent.php" class="btn">+ Add New Parent</a>
        </div>
        <br>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Username</th>
                    <th>Date Created</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($parents as $p): ?>
                    <tr>
                        <td><?= $p['id'] ?></td>
                        <td><?= htmlspecialchars($p['full_name']) ?></td>
                        <td><?= htmlspecialchars($p['username']) ?></td>
                        <td><?= date('M d, Y', strtotime($p['created_at'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>