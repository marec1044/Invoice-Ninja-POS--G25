<?php
session_start();
if (!isset($_SESSION["reset_email"])) {
    header("Location: forgot_password.php");
    exit();
}
// ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Set New Password</title>
  <style>
    body {
      margin: 0;
      padding: 0;
      font-family: Arial, sans-serif;
      background: linear-gradient(to bottom right, #002f6c, #004080);
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }
    .container {
      background: #0d2c54;
      padding: 40px;
      border-radius: 15px;
      box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.3);
      width: 350px;
      text-align: center;
    }
    h2 {
      color: #ffcc00;
      margin-bottom: 20px;
    }
    .input-box {
      width: 100%;
      padding: 12px;
      margin: 12px 0;
      border: none;
      border-radius: 8px;
      font-size: 16px;
    }
    .btn {
      width: 100%;
      padding: 12px;
      background: #ffcc00;
      border: none;
      border-radius: 8px;
      font-size: 16px;
      font-weight: bold;
      cursor: pointer;
      margin-top: 10px;
    }
    .btn:hover {
      background: #e6b800;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Set New Password</h2>
    <form action="update_password.php" method="post">
      <input type="password" name="new_password" class="input-box" placeholder="Enter new password" required>
      <input type="password" name="confirm_password" class="input-box" placeholder="Confirm new password" required>
      <button type="submit" class="btn">Update Password</button>
    </form>
  </div>
</body>
</html>
