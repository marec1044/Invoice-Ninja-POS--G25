<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8" />
    <title>لوحة التحكم</title>
</head>
<body>
    <h1>أهلاً بك، <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>
    <p>تم تسجيل الدخول بنجاح.</p>
    <a href="logout.php">تسجيل خروج</a>
</body>
</html>
