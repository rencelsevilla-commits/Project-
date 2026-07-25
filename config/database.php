<?php
$host = "localhost";
$dbname = "parent_student_portal";
$username = "root";
$password = ""; // Ilagay ang password ng MySQL mo kung meron

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database Connection Error: " . $e->getMessage());
}
?>