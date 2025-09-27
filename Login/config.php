<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ntipay";

$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
    die("فشل الاتصال: " . mysqli_connect_error());
}
// ?>
