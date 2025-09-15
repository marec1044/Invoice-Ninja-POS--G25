<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);

    if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $resetCode = rand(100000, 999999); // كود 6 أرقام

        $_SESSION["reset_email"] = $email;
        $_SESSION["reset_code"] = $resetCode;
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
          <meta charset="UTF-8">
          <meta name="viewport" content="width=device-width, initial-scale=1.0">
          <title>Reset Code</title>
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
            <h2>Reset Code Generated</h2>
            <p>Your reset code is:</p>
            <p style="font-size: 24px; font-weight: bold; color: #ffcc00;"><?php echo $resetCode; ?></p>
            <a href="verify_code.php" class="btn">Continue</a>
          </div>
        </body>
        </html>
        <?php
    } else {
        echo "Invalid email address!";
    }
} else {
    echo "Access denied!";
}
?>
