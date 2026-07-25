<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
check_role('admin');

// Quick Statistics
$teachers_count = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'teacher'")->fetchColumn();
$parents_count = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'parent'")->fetchColumn();
$students_count = $pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();

// Fetch Users (Teachers & Parents)
$users_list = $pdo->query("SELECT id, full_name, username, role FROM users WHERE role != 'admin' ORDER BY id DESC LIMIT 10")->fetchAll();

// Fetch Students safely
try {
    $students_list = $pdo->query("SELECT id, student_number, full_name, grade_level FROM students ORDER BY id DESC LIMIT 10")->fetchAll();
} catch (PDOException $e) {
    // Fallback if student_number column name is student_id in DB
    $students_list = $pdo->query("SELECT id, SELECT id, lrn_number AS student_number, full_name, grade_level FROM students ORDER BY id DESC LIMIT 10")->fetchAll();
}

// Handle Alert Messages
$alert_msg = '';
$alert_class = '';
if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'deleted') {
        $alert_msg = "Account/Record deleted successfully!";
        $alert_class = "alert-success";
    } elseif ($_GET['msg'] === 'cannot_delete_self') {
        $alert_msg = "You cannot delete your own admin account!";
        $alert_class = "alert-error";
    } elseif ($_GET['msg'] === 'error') {
        $alert_msg = "An error occurred while attempting to delete.";
        $alert_class = "alert-error";
    }
}

include '../includes/header.php';
include '../includes/navbar.php';
?>

<style>
    .dashboard-wrapper { max-width: 1140px; margin: 32px auto; padding: 0 20px; }

    .welcome-banner {
        background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
        color: #ffffff; padding: 28px 32px; border-radius: 16px; margin-bottom: 28px;
        box-shadow: 0 10px 20px -5px rgba(79, 70, 229, 0.25);
    }
    .welcome-banner h1 { font-size: 1.5rem; font-weight: 700; margin-bottom: 6px; }
    .welcome-banner p { color: #e0e7ff; font-size: 0.95rem; margin: 0; }

    .alert { padding: 12px 16px; border-radius: 8px; font-size: 0.875rem; margin-bottom: 24px; font-weight: 500; }
    .alert-success { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }
    .alert-error { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }

    .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin-bottom: 28px; }
    .stat-card {
        background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px 24px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04); display: flex; align-items: center; justify-content: space-between;
    }
    .stat-info h4 { font-size: 0.8rem; font-weight: 600; text-transform: uppercase; color: #64748b; margin-bottom: 6px; }
    .stat-info .value { font-size: 2rem; font-weight: 700; color: #0f172a; line-height: 1; }
    .stat-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; }
    .bg-indigo { background: #e0e7ff; color: #4338ca; }
    .bg-emerald { background: #dcfce7; color: #15803d; }
    .bg-amber { background: #fef3c7; color: #b45309; }

    .panel { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.04); margin-bottom: 24px; }
    .panel-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; flex-wrap: wrap; gap: 10px; }
    .panel-title { font-size: 1.05rem; font-weight: 700; color: #0f172a; margin: 0; }

    .custom-table { width: 100%; border-collapse: collapse; }
    .custom-table th { text-align: left; padding: 10px 12px; font-size: 0.75rem; text-transform: uppercase; color: #64748b; border-bottom: 1px solid #e2e8f0; background: #f8fafc; }
    .custom-table td { padding: 12px; font-size: 0.875rem; border-bottom: 1px solid #f1f5f9; color: #334155; }

    .role-badge { padding: 3px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; text-transform: capitalize; display: inline-block; }
    .role-teacher { background: #e0e7ff; color: #3730a3; }
    .role-parent { background: #dcfce7; color: #166534; }

    /* Action Buttons */
    .btn-action-edit {
        color: #2563eb; background: #eff6ff; border: 1px solid #bfdbfe;
        padding: 5px 12px; border-radius: 6px; font-size: 0.75rem; font-weight: 600;
        text-decoration: none; transition: all 0.2s ease; margin-right: 4px; display: inline-block;
    }
    .btn-action-edit:hover { background: #2563eb; color: #ffffff; }

    .btn-action-delete {
        color: #ef4444; background: #fef2f2; border: 1px solid #fecaca;
        padding: 5px 12px; border-radius: 6px; font-size: 0.75rem; font-weight: 600;
        text-decoration: none; transition: all 0.2s ease; display: inline-block;
    }
    .btn-action-delete:hover { background: #ef4444; color: #ffffff; }

    .btn-add-sm { background-color: #4f46e5; color: #ffffff; padding: 6px 12px; border-radius: 6px; font-size: 0.8rem; font-weight: 600; text-decoration: none; }
    .btn-add-sm:hover { background-color: #4338ca; }
</style>

<div class="dashboard-wrapper">
    <div class="welcome-banner">
        <h1>Welcome back, Administrator! 👋</h1>
        <p>Manage user accounts, edit details, and maintain student records.</p>
    </div>

    <?php if ($alert_msg): ?>
        <div class="alert <?= $alert_class ?>"><?= htmlspecialchars($alert_msg) ?></div>
    <?php endif; ?>

    <!-- Stats Row -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-info">
                <h4>Teachers</h4>
                <div class="value"><?= number_format($teachers_count) ?></div>
            </div>
            <div class="stat-icon bg-indigo">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-info">
                <h4>Parents</h4>
                <div class="value"><?= number_format($parents_count) ?></div>
            </div>
            <div class="stat-icon bg-emerald">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-info">
                <h4>Students</h4>
                <div class="value"><?= number_format($students_count) ?></div>
            </div>
            <div class="stat-icon bg-amber">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            </div>
        </div>
    </div>

    <!-- User Accounts Table -->
    <div class="panel">
        <div class="panel-header">
            <h3 class="panel-title">User Accounts (Teachers & Parents)</h3>
            <div>
                <a href="add_teacher.php" class="btn-add-sm">+ Add Teacher</a>
                <a href="add_parent.php" class="btn-add-sm" style="background-color: #10b981;">+ Add Parent</a>
            </div>
        </div>
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Full Name</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($users_list) > 0): ?>
                    <?php foreach ($users_list as $user): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($user['full_name']) ?></strong></td>
                            <td><?= htmlspecialchars($user['username']) ?></td>
                            <td>
                                <span class="role-badge role-<?= htmlspecialchars($user['role']) ?>">
                                    <?= htmlspecialchars($user['role']) ?>
                                </span>
                            </td>
                            <td style="text-align: right;">
                                <a href="edit_user.php?id=<?= $user['id'] ?>" class="btn-action-edit">Edit</a>
                                <a href="delete_user.php?type=user&id=<?= $user['id'] ?>" class="btn-action-delete" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="4" style="text-align: center; color: #94a3b8; padding: 24px;">No accounts found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Student Records Table -->
    <div class="panel">
        <div class="panel-header">
            <h3 class="panel-title">Student Records</h3>
            <a href="add_student.php" class="btn-add-sm" style="background-color: #f59e0b;">+ Add Student</a>
        </div>
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Student LRN</th>
                    <th>Full Name</th>
                    <th>Grade Level</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($students_list) > 0): ?>
                    <?php foreach ($students_list as $student): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($student['lrn_number']) ?></strong></td>
                            <td><?= htmlspecialchars($student['full_name']) ?></td>
                            <td><?= htmlspecialchars($student['grade_level']) ?></td>
                            <td style="text-align: right;">
                                <a href="edit_student.php?id=<?= $student['id'] ?>" class="btn-action-edit">Edit</a>
                                <a href="delete_user.php?type=student&id=<?= $student['id'] ?>" class="btn-action-delete" onclick="return confirm('Are you sure you want to delete this student record?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="4" style="text-align: center; color: #94a3b8; padding: 24px;">No student records found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>