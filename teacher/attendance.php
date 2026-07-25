<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
check_role('teacher');

$students = $pdo->query("SELECT * FROM students")->fetchAll();
$msg = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = $_POST['student_id'];
    $date = $_POST['date'];
    $status = $_POST['status'];
    $remarks = trim($_POST['remarks']);

    $stmt = $pdo->prepare("INSERT INTO attendance (student_id, date, status, remarks) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$student_id, $date, $status, $remarks])) {
        $msg = "Attendance recorded!";
    }
}

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container">
    <div class="card" style="max-width: 500px; margin: auto;">
        <h2>Record Student Attendance</h2>
        <a href="dashboard.php" class="btn btn-secondary">Dashboard</a>
        <br><br>
        <?php if($msg) echo "<p style='color:green;'>$msg</p>"; ?>
        <form method="POST">
            <div class="form-group">
                <label>Student</label>
                <select name="student_id" required>
                    <option value="">-- Select Student --</option>
                    <?php foreach($students as $s): ?>
                        <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['full_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Date</label>
                <input type="date" name="date" value="<?= date('Y-m-d') ?>" required>
            </div>
            <div class="form-group">
                <label>Status</label>
                <select name="status" required>
                    <option value="Present">Present</option>
                    <option value="Absent">Absent</option>
                    <option value="Late">Late</option>
                </select>
            </div>
            <div class="form-group">
                <label>Remarks (Optional)</label>
                <input type="text" name="remarks" placeholder="e.g. Sick leave">
            </div>
            <button type="submit" class="btn">Save Attendance</button>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>