<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
check_role('admin');

// Query para makuha ang estudyante at ang nakakabit na pangalan ng magulang
$sql = "SELECT s.*, u.full_name AS parent_name 
        FROM students s 
        LEFT JOIN users u ON s.parent_id = u.id 
        ORDER BY s.id DESC";

$students = $pdo->query($sql)->fetchAll();

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container">
    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h2>Students List</h2>
            <a href="add_student.php" class="btn">+ Add New Student</a>
        </div>
        <br>
        <table>
            <thead>
                <tr>
                    <th>LRN</th>
                    <th>Student Name</th>
                    <th>Grade & Section</th>
                    <th>Linked Parent</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $st): ?>
                    <tr>
                        <td><?= htmlspecialchars($st['lrn_number']) ?></td>
                        <td><?= htmlspecialchars($st['full_name']) ?></td>
                        <td><?= htmlspecialchars($st['grade_level'] . ' - ' . $st['section']) ?></td>
                        <td><?= htmlspecialchars($st['parent_name'] ?? 'Unassigned') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>