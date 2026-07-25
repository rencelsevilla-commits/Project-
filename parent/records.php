<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
check_role('parent');

$parent_id = $_SESSION['user_id'];

// Query Attendance for parent's children only
$sql = "SELECT s.full_name AS student_name, a.date, a.status, a.remarks 
        FROM attendance a
        JOIN students s ON a.student_id = s.id
        WHERE s.parent_id = ?
        ORDER BY a.date DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute([$parent_id]);
$attendance_logs = $stmt->fetchAll();

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container">
    <div class="card">
        <h2>Student Attendance History</h2>
        <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        <br><br>

        <?php if(count($attendance_logs) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($attendance_logs as $log): ?>
                        <tr>
                            <td><?= htmlspecialchars($log['student_name']) ?></td>
                            <td><?= htmlspecialchars($log['date']) ?></td>
                            <td><strong><?= htmlspecialchars($log['status']) ?></strong></td>
                            <td><?= htmlspecialchars($log['remarks'] ?? 'N/A') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No attendance records available.</p>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>