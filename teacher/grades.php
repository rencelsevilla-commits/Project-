<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
check_role('teacher');

$students = $pdo->query("SELECT * FROM students")->fetchAll();
$msg = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = $_POST['student_id'];
    $subject = $_POST['subject'];
    $quarter = $_POST['quarter'];
    $grade = $_POST['grade'];
    $teacher_id = $_SESSION['user_id'];

    $stmt = $pdo->prepare("INSERT INTO grades (student_id, teacher_id, subject, quarter, grade) VALUES (?, ?, ?, ?, ?)");
    if ($stmt->execute([$student_id, $teacher_id, $subject, $quarter, $grade])) {
        $msg = "Grado na-encode na nang matagumpay!";
    }
}

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container">
    <div class="card" style="max-width: 500px; margin: auto;">
        <h2>Input Student Grade</h2>
        <a href="dashboard.php" class="btn btn-secondary">Dashboard</a>
        <br><br>
        <?php if($msg) echo "<p style='color:green;'>$msg</p>"; ?>
        <form method="POST">
            <div class="form-group">
                <label>Pumili ng Estudyante</label>
                <select name="student_id" required>
                    <option value="">-- Select Student --</option>
                    <?php foreach($students as $s): ?>
                        <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['full_name']) ?> (<?= htmlspecialchars($s['section']) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Subject</label>
                <input type="text" name="subject" required>
            </div>
            <div class="form-group">
                <label>Quarter</label>
                <select name="quarter" required>
                    <option value="1st Quarter">1st Quarter</option>
                    <option value="2nd Quarter">2nd Quarter</option>
                    <option value="3rd Quarter">3rd Quarter</option>
                    <option value="4th Quarter">4th Quarter</option>
                </select>
            </div>
            <div class="form-group">
                <label>Grade</label>
                <input type="number" step="0.01" name="grade" required>
            </div>
            <button type="submit" class="btn">Save Grade</button>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>