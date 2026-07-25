<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
check_role('admin');

$success_msg = '';
$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = trim($_POST['full_name']);
    $username = trim($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    try {
        $stmt = $pdo->prepare("INSERT INTO users (full_name, username, password, role) VALUES (?, ?, ?, 'teacher')");
        if ($stmt->execute([$full_name, $username, $password])) {
            $success_msg = "Teacher account created successfully!";
        } else {
            $error_msg = "Failed to create teacher account.";
        }
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            $error_msg = "Username <strong>'" . htmlspecialchars($username) . "'</strong> is already taken.";
        } else {
            $error_msg = "An error occurred: " . $e->getMessage();
        }
    }
}

include '../includes/header.php';
include '../includes/navbar.php';
?>

<style>
    .form-wrapper {
        max-width: 520px;
        margin: 40px auto;
        padding: 0 20px;
    }
    .form-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 32px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }
    .form-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
    }
    .form-header h2 {
        font-size: 1.25rem;
        font-weight: 700;
        color: #0f172a;
    }
    .btn-back {
        color: #64748b;
        font-size: 0.875rem;
        text-decoration: none;
        font-weight: 500;
    }
    .btn-back:hover { color: #0f172a; }
    .form-group { margin-bottom: 20px; }
    .form-group label {
        display: block;
        font-size: 0.875rem;
        font-weight: 600;
        color: #334155;
        margin-bottom: 6px;
    }
    .form-group input {
        width: 100%;
        padding: 10px 14px;
        font-size: 0.95rem;
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        background-color: #f8fafc;
        box-sizing: border-box;
    }
    .form-group input:focus {
        outline: none;
        border-color: #4f46e5;
        background-color: #ffffff;
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15);
    }
    .btn-submit {
        width: 100%;
        padding: 12px;
        background-color: #4f46e5;
        color: #ffffff;
        font-weight: 600;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: background-color 0.2s;
    }
    .btn-submit:hover { background-color: #4338ca; }
    .alert { padding: 12px 16px; border-radius: 8px; font-size: 0.875rem; margin-bottom: 20px; }
    .alert-success { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }
    .alert-error { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
</style>

<div class="form-wrapper">
    <div class="form-card">
        <div class="form-header">
            <h2>Add New Teacher</h2>
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
                <label for="full_name">Full Name</label>
                <input type="text" id="full_name" name="full_name" placeholder="e.g. Prof. Sarah Jenkins" required>
            </div>
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="e.g. sjenkins" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter account password" required>
            </div>
            <button type="submit" class="btn-submit">Create Teacher Account</button>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>