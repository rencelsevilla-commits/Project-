<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
check_role('parent');

$parent_id = $_SESSION['user_id'];

// Fetch children linked to this parent
$stmt_children = $pdo->prepare("SELECT * FROM students WHERE parent_id = ?");
$stmt_children->execute([$parent_id]);
$children = $stmt_children->fetchAll();

// Prepared statements para sa Grades at Attendance ng bawat anak
$stmt_grades = $pdo->prepare("SELECT * FROM grades WHERE student_id = ? ORDER BY id DESC");
$stmt_attendance = $pdo->prepare("SELECT * FROM attendance WHERE student_id = ? ORDER BY date DESC LIMIT 5");

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
        background: linear-gradient(135deg, #16a34a 0%, #15803d 100%);
        color: #ffffff; 
        padding: 28px 32px; 
        border-radius: 16px; 
        margin-bottom: 28px;
        box-shadow: 0 10px 20px -5px rgba(22, 163, 74, 0.25);
    }
    .welcome-banner h1 { font-size: 1.5rem; font-weight: 700; margin-bottom: 6px; }
    .welcome-banner p { color: #dcfce7; font-size: 0.95rem; margin: 0; }

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
    .bg-emerald { background: #dcfce7; color: #16a34a; }

    /* Student Card Container */
    .child-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 28px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
    }

    .child-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-bottom: 16px;
        margin-bottom: 20px;
        border-bottom: 2px solid #f1f5f9;
        flex-wrap: wrap;
        gap: 12px;
    }

    .child-info h3 {
        font-size: 1.2rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 2px;
    }

    .child-info p {
        font-size: 0.875rem;
        color: #64748b;
        margin: 0;
    }

    .child-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    @media (max-width: 850px) {
        .child-grid { grid-template-columns: 1fr; }
    }

    .sub-panel {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 18px;
    }

    .sub-panel-title {
        font-size: 0.95rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .custom-table { width: 100%; border-collapse: collapse; }
    .custom-table th { 
        text-align: left; 
        padding: 8px 10px; 
        font-size: 0.75rem; 
        text-transform: uppercase; 
        color: #64748b; 
        border-bottom: 1px solid #cbd5e1; 
        background: #ffffff; 
    }
    .custom-table td { padding: 10px; font-size: 0.85rem; border-bottom: 1px solid #e2e8f0; color: #334155; }

    /* Attendance Status Badges */
    .status-badge {
        padding: 3px 8px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: capitalize;
        display: inline-block;
    }
    .status-present { background: #dcfce7; color: #15803d; }
    .status-absent { background: #fee2e2; color: #991b1b; }
    .status-late { background: #fef3c7; color: #b45309; }

    .empty-state {
        text-align: center;
        color: #94a3b8;
        padding: 20px;
        font-size: 0.875rem;
    }
</style>

<div class="dashboard-wrapper">
    <!-- Header Banner -->
    <div class="welcome-banner">
        <h1>Parent Portal 👨‍👩‍👧‍👦</h1>
        <p>Monitor your child's grades, daily attendance, and academic progress.</p>
    </div>

    <!-- Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-info">
                <h4>Registered Children</h4>
                <div class="value"><?= count($children) ?></div>
            </div>
            <div class="stat-icon bg-emerald">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            </div>
        </div>
    </div>

    <!-- Children Records Section -->
    <?php if (count($children) > 0): ?>
        <?php foreach ($children as $child): ?>
            <?php
                // Fetch grades & attendance for this child
                $stmt_grades->execute([$child['id']]);
                $grades = $stmt_grades->fetchAll();

                $stmt_attendance->execute([$child['id']]);
                $attendances = $stmt_attendance->fetchAll();
            ?>
            <div class="child-card">
                <div class="child-header">
                    <div class="child-info">
                        <h3>🎓 <?= htmlspecialchars($child['full_name']) ?></h3>
                        <p>Student LRN: <strong><?= htmlspecialchars($child['student_number']) ?></strong> | Level: <strong><?= htmlspecialchars($child['grade_level']) ?></strong></p>
                    </div>
                </div>

                <div class="child-grid">
                    <!-- Academic Grades Panel -->
                    <div class="sub-panel">
                        <div class="sub-panel-title">
                            📝 Academic Report / Grades
                        </div>
                        <?php if (count($grades) > 0): ?>
                            <table class="custom-table">
                                <thead>
                                    <tr>
                                        <th>Subject</th>
                                        <th>Grade</th>
                                        <th>Remarks / Term</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($grades as $g): ?>
                                        <tr>
                                            <td><strong><?= htmlspecialchars($g['subject'] ?? $g['subject_name'] ?? 'N/A') ?></strong></td>
                                            <td><span style="font-weight: 700; color: #16a34a;"><?= htmlspecialchars($g['grade'] ?? $g['score'] ?? '-') ?></span></td>
                                            <td><?= htmlspecialchars($g['remarks'] ?? $g['quarter'] ?? 'Final') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div class="empty-state">No grade records posted yet.</div>
                        <?php endif; ?>
                    </div>

                    <!-- Attendance Records Panel -->
                    <div class="sub-panel">
                        <div class="sub-panel-title">
                            📋 Recent Attendance
                        </div>
                        <?php if (count($attendances) > 0): ?>
                            <table class="custom-table">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th style="text-align: right;">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($attendances as $att): ?>
                                        <?php 
                                            $status = strtolower($att['status']); 
                                            $badge_class = 'status-present';
                                            if ($status === 'absent') $badge_class = 'status-absent';
                                            if ($status === 'late') $badge_class = 'status-late';
                                        ?>
                                        <tr>
                                            <td><?= date('M d, Y', strtotime($att['date'])) ?></td>
                                            <td style="text-align: right;">
                                                <span class="status-badge <?= $badge_class ?>">
                                                    <?= ucfirst($status) ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div class="empty-state">No attendance records found.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="child-card" style="text-align: center; color: #64748b; padding: 40px;">
            <p style="font-size: 1rem; margin-bottom: 8px;">No children registered under this parent account yet.</p>
            <p style="font-size: 0.875rem; color: #94a3b8;">Please contact the administrator to link your child's student profile.</p>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>