<?php
include 'config.php';
session_start();

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email='$email' AND password='$password'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {
        $_SESSION['user'] = mysqli_fetch_assoc($result);
        header("Location: welcome.php");
        exit;
    } else {
        $error = "Incorrect email or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>NTI Pay - Login</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
  <style>
    *{margin:0;padding:0;box-sizing:border-box;font-family:'Poppins',sans-serif}
    body{height:100vh;display:flex;justify-content:center;align-items:center;background:linear-gradient(135deg,#001f3f,#00509e)}
    .card{width:380px;background:rgba(255,255,255,0.1);backdrop-filter:blur(15px);border-radius:20px;padding:30px;box-shadow:0 8px 25px rgba(0,0,0,0.5);color:white}
    h2{text-align:center;margin-bottom:20px;font-weight:700;font-size:26px;color:#ffca28}
    .input-group{position:relative;margin-bottom:15px}
    .input-group input{width:100%;padding:12px 40px 12px 15px;border:none;border-radius:10px;background:rgba(255,255,255,0.2);color:white;font-size:14px}
    .input-group input::placeholder{color:rgba(255,255,255,0.7)}
    .input-group i{position:absolute;right:12px;top:50%;transform:translateY(-50%);color:#ffca28}
    button{width:100%;padding:12px;border:none;border-radius:10px;margin-top:10px;background:#ffca28;color:#000;font-weight:600;font-size:16px;cursor:pointer;transition:.3s}
    button:hover{background:#ffc107;transform:scale(1.02)}
    .links{text-align:center;margin-top:15px;font-size:13px}
    .links a{color:#ffca28;text-decoration:none}
    .message{margin-top:10px;text-align:center;font-size:14px;color:#ff4c4c}
  </style>
  <script src="https://kit.fontawesome.com/5d33e5e5e0.js" crossorigin="anonymous"></script>
</head>
<body>
  <div class="card">
    <h2>NTI Pay Login</h2>
    <form method="POST">
      <div class="input-group">
        <input type="email" name="email" placeholder="Email address" required>
        <i class="fas fa-envelope"></i>
      </div>
      <div class="input-group">
        <input type="password" name="password" placeholder="Password" required>
        <i class="fas fa-lock"></i>
      </div>
      <button type="submit" name="login">Login</button>
    </form>
    <div class="links">
      <a href="forgot_password.php">Forgot Password?</a> | <a href="register.php">Create an account</a>
    </div>
    <?php if (isset($error)) echo "<div class='message'>$error</div>"; ?>
  </div>


</body>
</html>
