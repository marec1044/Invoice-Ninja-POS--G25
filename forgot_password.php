<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Forgot Password</title>
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

    .container h2 {
      color: #ffcc00;
      margin-bottom: 20px;
      font-size: 24px;
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

    .back-link {
      margin-top: 15px;
      display: block;
      color: #ffcc00;
      text-decoration: none;
      font-size: 14px;
    }

    .back-link:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Forgot Password</h2>
    <form action="reset_password.php" method="post">
      <input type="email" name="email" class="input-box" placeholder="Enter your email" required>
      <button type="submit" class="btn">Send Reset Code</button>
    </form>
    <a href="login.php" class="back-link">Back to Login</a>
  </div>
</body>
</html>
