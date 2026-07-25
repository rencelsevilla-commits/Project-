<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
check_role('admin');

if (isset($_GET['id']) && isset($_GET['type'])) {
    $id = intval($_GET['id']);
    $type = $_GET['type'];

    try {
        if ($type === 'user') {
            // Prevent admin from deleting their own logged-in account
            if ($id == $_SESSION['user_id']) {
                header("Location: dashboard.php?msg=cannot_delete_self");
                exit;
            }

            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$id]);
        } elseif ($type === 'student') {
            $stmt = $pdo->prepare("DELETE FROM students WHERE id = ?");
            $stmt->execute([$id]);
        }

        header("Location: dashboard.php?msg=deleted");
        exit;
    } catch (PDOException $e) {
        header("Location: dashboard.php?msg=error");
        exit;
    }
} else {
    header("Location: dashboard.php");
    exit;
}