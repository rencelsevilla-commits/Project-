<?php
require_once 'config/database.php';

$username = 'admin';
$password = 'adminpassword';
// Gagawa ng tamang hash para sa PHP version mo
$hashed_password = password_hash($password, PASSWORD_BCRYPT);

try {
    // I-update ang password ng admin
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE username = ?");
    $stmt->execute([$hashed_password, $username]);

    if ($stmt->rowCount() > 0) {
        echo "<h2 style='color:green;'>Success! Na-update na ang Admin Password.</h2>";
        echo "<p>Puwede ka nang mag-login gamit ang:</p>";
        echo "<b>Username:</b> admin<br>";
        echo "<b>Password:</b> adminpassword<br><br>";
        echo "<a href='login.php'>Pumunta sa Login Page</a>";
    } else {
        // Kung sakaling wala pang admin sa table, gawa ng bago
        $stmt2 = $pdo->prepare("INSERT INTO users (username, password, full_name, role) VALUES (?, ?, 'System Administrator', 'admin')");
        $stmt2->execute([$username, $hashed_password]);
        
        echo "<h2 style='color:green;'>Success! Na-create na ang Admin Account.</h2>";
        echo "<a href='login.php'>Pumunta sa Login Page</a>";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>