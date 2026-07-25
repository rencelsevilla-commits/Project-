<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
check_role('parent');

$parent_id = $_SESSION['user_id'];

// SQL Query: Kukunin lang ang grado ng anak na nakakabit sa parent_id na ito
$sql = "SELECT s.full_name AS student_name, g.subject, g.quarter, g.grade 
        FROM grades g
        JOIN students s ON g.student_id = s.id
        WHERE s.parent_id = ?";

$stmt = $pdo->prepare($sql);
$stmt->execute([$parent_id]);
$grades = $stmt->fetchAll(PDO::FETCH_ASSOC);

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container">
    <div class="card">
        <h2>Mga Grado ng Iyong Anak</h2>
        <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        <br><br>

        <?php if(count($grades) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Pangalan ng Anak</th>
                        <th>Subject</th>
                        <th>Quarter</th>
                        <th>Grado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($grades as $g): ?>
                        <tr>
                            <td><?= htmlspecialchars($g['student_name']) ?></td>
                            <td><?= htmlspecialchars($g['subject']) ?></td>
                            <td><?= htmlspecialchars($g['quarter']) ?></td>
                            <td><strong><?= htmlspecialchars($g['grade']) ?></strong></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Wala pang nai-encode na grado para sa iyong anak.</p>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>