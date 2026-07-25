<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
check_role('teacher');

// Fetch total count and list of students
$students_count = $pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();
$students = $pdo->query("SELECT * FROM students ORDER BY full_name ASC")->fetchAll();

include '../includes/header.php';
include '../includes/navbar.php';
?>

<style>
    .dashboard-wrapper { 
        max-width: 1140px; 
        margin: 32px auto; 
        padding: 0 20px; 
    }
    
    .welcome-banner {
        background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
        color: #ffffff; 
        padding: 28px 32px; 
        border-radius: 16px; 
        margin-bottom: 28px;
        box-shadow: 0 10px 20px -5px rgba(2, 132, 199, 0.25);
        display: flex; 
        justify-content: space-between; 
        align-items: center; 
        flex-wrap: wrap; 
        gap: 16px;
    }
    
    .welcome-banner h1 { font-size: 1.5rem; font-weight: 700; margin-bottom: 6px; }
    .welcome-banner p { color: #e0f2fe; font-size: 0.95rem; margin: 0; }
    
    .banner-actions { display: flex; gap: 10px; flex-wrap: wrap; }
    
    .btn-action-light {
        background-color: #ffffff;
        color: #0369a1;
        padding: 10px 18px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.875rem;
        text-decoration: none;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.08);
    }
    
    .btn-action-light:hover { 
        background-color: #f0f9ff; 
        transform: translateY(-1px); 
    }

    .stats-grid { 
        display: grid; 
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); 
        gap: 20px; 
        margin-bottom: 28px; 
    }
    
    .stat-card {
        background: #ffffff; 
        border: 1px solid #e2e8f0; 
        border-radius: 12px; 
        padding: 20px 24px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04); 
        display: flex; 
        align-items: center; 
        justify-content: space-between;
    }
    
    .stat-info h4 { font-size: 0.8rem; font-weight: 600; text-transform: uppercase; color: #64748b; margin-bottom: 6px; }
    .stat-info .value { font-size: 2rem; font-weight: 700; color: #0f172a; line-height: 1; }
    
    .stat-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; }
    .bg-sky { background: #e0f2fe; color: #0284c7; }
    
    .panel { 
        background: #ffffff; 
        border: 1px solid #e2e8f0; 
        border-radius: 12px; 
        padding: 24px; 
        box-shadow: 0 1px 3px rgba(0,0,0,0.04); 
    }
    
    .panel-header { 
        display: flex; 
        justify-content: space-between; 
        align-items: center; 
        margin-bottom: 18px; 
        flex-wrap: wrap; 
        gap: 12px; 
    }
    
    .panel-title { font-size: 1.05rem; font-weight: 700; color: #0f172a; margin: 0; }
    
    .custom-table { width: 100%; border-collapse: collapse; }
    .custom-table th { 
        text-align: left; 
        padding: 10px 12px; 
        font-size: 0.75rem; 
        text-transform: uppercase; 
        color: #64748b; 
        border-bottom: 1px solid #e2e8f0; 
        background: #f8fafc; 
    }
    .custom-table td { padding: 12px; font-size: 0.875rem; border-bottom: 1px solid #f1f5f9; color: #334155; }
    
    .btn-tbl-action {
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 0.775rem;
        font-weight: 600;
        text-decoration: none;
        display: inline-block;
        margin-left: 4px;
        transition: all 0.2s ease;
    }
    
    .btn-grade { background: #e0e7ff; color: #3730a3; border: 1px solid #c7d2fe; }
    .btn-grade:hover { background: #4338ca; color: #ffffff; }
    
    .btn-attendance { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
    .btn-attendance:hover { background: #16a34a; color: #ffffff; }
</style>

<div class="dashboard-wrapper">
    <!-- Header Banner with Main Actions -->
    <div class="welcome-banner">
        <div>
            <h1>Teacher Portal 👨‍🏫</h1>
            <p>Manage student records, mark daily attendance, and post student grades.</p>
        </div>
        <div class="banner-actions">
            <a href="attendance.php" class="btn-action-light">
                📋 Take Attendance
            </a>
            <a href="grades.php" class="btn-action-light">
                📝 Add Grade
            </a>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-info">
                <h4>Total Assigned Students</h4>
                <div class="value"><?= number_format($students_count) ?></div>
            </div>
            <div class="stat-icon bg-sky">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
            </div>
        </div>
    </div>

    <!-- Class List Panel -->
    <div class="panel">
        <div class="panel-header">
            <h3 class="panel-title">Assigned Students</h3>
            <div>
                <a href="attendance.php" class="btn-tbl-action btn-attendance" style="font-size: 0.85rem; padding: 8px 14px;">📋 Class Attendance Page</a>
                <a href="grades.php" class="btn-tbl-action btn-grade" style="font-size: 0.85rem; padding: 8px 14px;">📝 Input Grades Page</a>
            </div>
        </div>

        <table class="custom-table">
            <thead>
                <tr>
                    <th>Student LRN</th>
                    <th>Full Name</th>
                    <th>Grade / Level</th>
                    <th style="text-align: right;">Quick Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($students) > 0): ?>
                    <?php foreach ($students as $student): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($student['student_number']) ?></strong></td>
                            <td><?= htmlspecialchars($student['full_name']) ?></td>
                            <td><?= htmlspecialchars($student['grade_level']) ?></td>
                            <td style="text-align: right;">
                                <a href="attendance.php?student_id=<?= $student['id'] ?>" class="btn-tbl-action btn-attendance">+ Attendance</a>
                                <a href="grades.php?student_id=<?= $student['id'] ?>" class="btn-tbl-action btn-grade">+ Add Grade</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="4" style="text-align: center; color: #94a3b8; padding: 24px;">No students assigned yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>