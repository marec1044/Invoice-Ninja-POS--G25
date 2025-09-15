<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userCode = trim($_POST["reset_code"]);

    if ($userCode == $_SESSION["reset_code"]) {
        // الكود صحيح -> يفتح صفحة تغيير الباسورد
        header("Location: new_password.php");
        exit();
    } else {
        echo "Invalid code! <a href='verify_code.php'>Try again</a>";
    }
} else {
    echo "Access denied!";
}
?>
