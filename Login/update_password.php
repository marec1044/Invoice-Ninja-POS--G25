<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $newPassword = trim($_POST["new_password"]);
    $confirmPassword = trim($_POST["confirm_password"]);

    if ($newPassword === $confirmPassword && !empty($newPassword)) {
        // هنا المفروض نخزن الباسورد الجديد في قاعدة البيانات
        // مؤقتًا للتجربة بس، هنخزنه في السيشن
        $_SESSION["user_password"] = password_hash($newPassword, PASSWORD_DEFAULT);
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
          <meta charset="UTF-8">
          <meta name="viewport" content="width=device-width, initial-scale=1.0">
          <title>Password Updated</title>
          <style>
            body {
              font-family: Arial, sans-serif;
              background: linear-gradient(to bottom right, #002f6c, #004080);
              color: white;
              display: flex;
              justify-content: center;
              align-items: center;
              height: 100vh;
              margin: 0;
            }
            .box {
              background: #0d2c54;
              padding: 30px;
              border-radius: 12px;
              text-align: center;
              box-shadow: 0 4px 12px rgba(0,0,0,0.3);
              width: 350px;
            }
            h2 {
              color: #ffcc00;
            }
            .btn {
              display: inline-block;
              margin-top: 15px;
              padding: 10px 20px;
              background: #ffcc00;
              color: black;
              text-decoration: none;
              border-radius: 6px;
              font-weight: bold;
            }
            .btn:hover {
              background: #e6b800;
            }
          </style>
        </head>
        <body>
          <div class="box">
            <h2>Password Updated Successfully</h2>
            <p>Your password has been changed!</p>
            <a href="login.php" class="btn">Back to Login</a>
          </div>
        </body>
        </html>
        <?php
    } else {
        echo "Passwords do not match! <a href='new_password.php'>Try again</a>";
    }
} else {
    echo "Access denied!";
}
?>
