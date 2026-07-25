<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Function para siguraduhing naka-login ang user
function check_login() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../login.php");
        exit();
    }
}

// Function para sa Role Security (Admin, Teacher, or Parent)
function check_role($allowed_role) {
    check_login();
    if ($_SESSION['role'] !== $allowed_role) {
        echo "<h2 style='color:red;'>Access Denied! Wala kang permiso para sa page na ito.</h2>";
        exit();
    }
}
?>