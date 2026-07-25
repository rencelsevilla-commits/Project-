<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
check_role('admin');

// Kunin ang lahat ng may role na 'teacher'
$stmt = $pdo->query("SELECT id, full_name, username, created_at FROM users WHERE role = 'teacher' ORDER BY id DESC");
$teachers = $stmt->fetchAll();

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container">
    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h2>Teachers Management</h2>
            <a href="add_teacher.php" class="btn">+ Add New Teacher</a>
        </div>
        <br>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Username</th>
                    <th>Date Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($teachers) > 0): ?>
                    <?php foreach ($teachers as $t): ?>
                        <tr>
                            <td><?= $t['id'] ?></td>
                            <td><?= htmlspecialchars($t['full_name']) ?></td>
                            <td><?= htmlspecialchars($t['username']) ?></td>
                            <td><?= date('M d, Y', strtotime($t['created_at'])) ?></td>
                            <td>
                                <a href="edit_teacher.php?id=<?= $t['id'] ?>" class="btn">Edit</a>
                                <a href="delete_teacher.php?id=<?= $t['id'] ?>" class="btn btn-danger" onclick="return confirm('Sigurado ka bang gusto mong burahin ang account na ito?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5">Walang nahanap na teacher account.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>