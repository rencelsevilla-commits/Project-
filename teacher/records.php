<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
check_role('teacher');

// Kunin ang mga gradong inilagay ng mismong naka-login na teacher
$teacher_id = $_SESSION['user_id'];
$sql = "SELECT g.id, s.full_name AS student_name, g.subject, g.quarter, g.grade, g.created_at 
        FROM grades g 
        JOIN students s ON g.student_id = s.id 
        WHERE g.teacher_id = ? 
        ORDER BY g.id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute([$teacher_id]);
$records = $stmt->fetchAll();

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container">
    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h2>My Encoded Grades</h2>
            <a href="grades.php" class="btn">+ Encode New Grade</a>
        </div>
        <br>
        <table>
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Subject</th>
                    <th>Quarter</th>
                    <th>Grade</th>
                    <th>Date Recorded</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($records) > 0): ?>
                    <?php foreach ($records as $r): ?>
                        <tr>
                            <td><?= htmlspecialchars($r['student_name']) ?></td>
                            <td><?= htmlspecialchars($r['subject']) ?></td>
                            <td><?= htmlspecialchars($r['quarter']) ?></td>
                            <td><strong><?= htmlspecialchars($r['grade']) ?></strong></td>
                            <td><?= date('M d, Y', strtotime($r['created_at'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5">Wala ka pang naii-encode na grado.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>