<?php
$host = "localhost";   // اسم السيرفر
$user = "root";        // اليوزر بتاع MySQL (في XAMPP بيكون root)
$pass = "";            // الباسورد (غالبًا فاضي في XAMPP)
$db   = "store";  // 👈 غير الاسم لاسم قاعدة البيانات بتاعتك

$con = mysqli_connect($host, $user, $pass, $db);

// Check connection
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
