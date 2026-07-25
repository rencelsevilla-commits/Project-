<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
check_role('admin');

$success_msg = '';
$error_msg = '';

if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit;
}

$student_id = intval($_GET['id']);

// Fetch student data
$stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
$stmt->execute([$student_id]);
$student = $stmt->fetch();

if (!$student) {
    header("Location: dashboard.php");
    exit;
}

// Fetch all parents for the dropdown
$parents = $pdo->query("SELECT id, full_name FROM users WHERE role = 'parent' ORDER BY full_name ASC")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $lrn_number = trim($_POST['lrn_number']);
    $full_name = trim($_POST['full_name']);
    $grade_level = trim($_POST['grade_level']);
    $parent_id = !empty($_POST['parent_id']) ? $_POST['parent_id'] : null;

    try {
        $update_stmt = $pdo->prepare("UPDATE students SET lrn_number = ?, full_name = ?, grade_level = ?, parent_id = ? WHERE id = ?");
        $update_stmt->execute([$lrn_number, $full_name, $grade_level, $parent_id, $student_id]);
        
        $success_msg = "Student record updated successfully!";
        // Refresh local array
        $student['lrn_number'] = $lrn_number;
        $student['full_name'] = $full_name;
        $student['grade_level'] = $grade_level;
        $student['parent_id'] = $parent_id;
    } catch (PDOException $e) {
        $error_msg = "Error updating record: " . $e->getMessage();
    }
}

include '../includes/header.php';
include '../includes/navbar.php';
?>

<style>
    .form-wrapper { max-width: 540px; margin: 40px auto; padding: 0 20px; }
    .form-card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 32px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); }
    .form-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
    .form-header h2 { font-size: 1.25rem; font-weight: 700; color: #0f172a; }
    .btn-back { color: #64748b; font-size: 0.875rem; text-decoration: none; font-weight: 500; }
    .btn-back:hover { color: #0f172a; }
    .form-group { margin-bottom: 20px; }
    .form-group label { display: block; font-size: 0.875rem; font-weight: 600; color: #334155; margin-bottom: 6px; }
    .form-group input, .form-group select { width: 100%; padding: 10px 14px; font-size: 0.95rem; border-radius: 8px; border: 1px solid #cbd5e1; background-color: #f8fafc; box-sizing: border-box; }
    .form-group input:focus, .form-group select:focus { outline: none; border-color: #4f46e5; background-color: #ffffff; box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15); }
    .btn-submit { width: 100%; padding: 12px; background-color: #4f46e5; color: #ffffff; font-weight: 600; border: none; border-radius: 8px; cursor: pointer; transition: background-color 0.2s; }
    .btn-submit:hover { background-color: #4338ca; }
    .alert { padding: 12px 16px; border-radius: 8px; font-size: 0.875rem; margin-bottom: 20px; }
    .alert-success { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }
    .alert-error { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
</style>

<div class="form-wrapper">
    <div class="form-card">
        <div class="form-header">
            <h2>Edit Student Record</h2>
            <a href="dashboard.php" class="btn-back">&larr; Back to Dashboard</a>
        </div>

        <?php if ($success_msg): ?>
            <div class="alert alert-success"><?= $success_msg ?></div>
        <?php endif; ?>
        <?php if ($error_msg): ?>
            <div class="alert alert-error"><?= $error_msg ?></div>
        <?php endif; ?>

        <form method="POST" autocomplete="off">
            <div class="form-group">
                <label for="lrn_number">Student LRN</label>
                <input type="text" id="lrn_number" name="lrn_number" value="<?= htmlspecialchars($student['lrn_number']) ?>" required>
            </div>
            <div class="form-group">
                <label for="full_name">Full Name</label>
                <input type="text" id="full_name" name="full_name" value="<?= htmlspecialchars($student['full_name']) ?>" required>
            </div>
            <div class="form-group">
                <label for="grade_level">Grade / Level</label>
                <input type="text" id="grade_level" name="grade_level" value="<?= htmlspecialchars($student['grade_level']) ?>" required>
            </div>
            <div class="form-group">
                <label for="parent_id">Assign Parent (Optional)</label>
                <select id="parent_id" name="parent_id">
                    <option value="">-- Select Parent --</option>
                    <?php foreach ($parents as $parent): ?>
                        <option value="<?= $parent['id'] ?>" <?= ($student['parent_id'] == $parent['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($parent['full_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn-submit">Save Changes</button>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>