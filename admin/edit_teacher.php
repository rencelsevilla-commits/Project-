<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
check_role('admin');

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: teachers.php");
    exit();
}

$msg = '';

// Fetch teacher details
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? AND role = 'teacher'");
$stmt->execute([$id]);
$teacher = $stmt->fetch();

if (!$teacher) {
    echo "Teacher not found.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = trim($_POST['full_name']);
    $username = trim($_POST['username']);
    $new_password = $_POST['new_password'];

    if (!empty($new_password)) {
        $hashed = password_hash($new_password, PASSWORD_BCRYPT);
        $update = $pdo->prepare("UPDATE users SET full_name = ?, username = ?, password = ? WHERE id = ?");
        $update->execute([$full_name, $username, $hashed, $id]);
    } else {
        $update = $pdo->prepare("UPDATE users SET full_name = ?, username = ? WHERE id = ?");
        $update->execute([$full_name, $username, $id]);
    }

    $msg = "Teacher updated successfully!";
    // Refresh local copy
    $teacher['full_name'] = $full_name;
    $teacher['username'] = $username;
}

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container">
    <div class="card" style="max-width: 500px; margin: auto;">
        <h2>Edit Teacher Account</h2>
        <a href="teachers.php" class="btn btn-secondary">Back to List</a>
        <br><br>
        <?php if($msg) echo "<p style='color:green;'>$msg</p>"; ?>
        <form method="POST">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="full_name" value="<?= htmlspecialchars($teacher['full_name']) ?>" required>
            </div>
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" value="<?= htmlspecialchars($teacher['username']) ?>" required>
            </div>
            <div class="form-group">
                <label>New Password (Iwanang blanko kung ayaw palitan)</label>
                <input type="password" name="new_password">
            </div>
            <button type="submit" class="btn">Update Account</button>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>