<?php
session_start();

if (isset($_SESSION['user_id'])) {
    switch ($_SESSION['role']) {
        case 'admin':
            header("Location: admin/dashboard.php");
            break;
        case 'teacher':
            header("Location: teacher/dashboard.php");
            break;
        case 'parent':
            header("Location: parent/dashboard.php");
            break;
        default:
            header("Location: login.php");
    }
    exit();
} else {
    header("Location: login.php");
    exit();
}
?>