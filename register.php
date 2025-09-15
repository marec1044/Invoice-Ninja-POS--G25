<?php
include 'config.php';

if (isset($_POST['register'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "INSERT INTO users (name, email, password) VALUES ('$name','$email','$password')";
    if (mysqli_query($conn, $sql)) {
        $success = "Account created successfully. <a href='login.php'>Login here</a>";
    } else {
        $error = "Error while registering. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>NTI Pay - Register</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
  <style>
    *{margin:0;padding:0;box-sizing:border-box;font-family:'Poppins',sans-serif}
    body{height:100vh;display:flex;justify-content:center;align-items:center;background:linear-gradient(135deg,#00509e,#001f3f)}
    .card{width:400px;background:rgba(255,255,255,0.1);backdrop-filter:blur(15px);border-radius:20px;padding:30px;box-shadow:0 8px 25px rgba(0,0,0,0.5);color:white}
    h2{text-align:center;margin-bottom:20px;font-weight:700;font-size:26px;color:#00e676}
    .input-group{position:relative;margin-bottom:15px}
    .input-group input{width:100%;padding:12px 40px 12px 15px;border:none;border-radius:10px;background:rgba(255,255,255,0.2);color:white;font-size:14px}
    .input-group input::placeholder{color:rgba(255,255,255,0.7)}
    .input-group i{position:absolute;right:12px;top:50%;transform:translateY(-50%);color:#00e676}
    button{width:100%;padding:12px;border:none;border-radius:10px;margin-top:10px;background:#00e676;color:#000;font-weight:600;font-size:16px;cursor:pointer;transition:.3s}
    button:hover{background:#00c853;transform:scale(1.02)}
    .links{text-align:center;margin-top:15px;font-size:13px}
    .links a{color:#00e676;text-decoration:none}
    .message{margin-top:10px;text-align:center;font-size:14px}
    .error{color:#ff4c4c}
    .success{color:#00e676}
  </style>
  <script src="https://kit.fontawesome.com/5d33e5e5e0.js" crossorigin="anonymous"></script>
</head>
<body>
  <div class="card">
    <h2>Create Account</h2>
    <form method="POST">
      <div class="input-group">
        <input type="text" name="name" placeholder="Full name" required>
        <i class="fas fa-user"></i>
      </div>
      <div class="input-group">
        <input type="email" name="email" placeholder="Email address" required>
        <i class="fas fa-envelope"></i>
      </div>
      <div class="input-group">
        <input type="password" name="password" placeholder="Password" required>
        <i class="fas fa-lock"></i>
      </div>
      <button type="submit" name="register">Register</button>
    </form>
    <div class="links">
      Already have an account? <a href="login.php">Login</a>
    </div>
    <?php if (isset($error)) echo "<div class='message error'>$error</div>"; ?>
    <?php if (isset($success)) echo "<div class='message success'>$success</div>"; ?>
  </div>
</body>
</html>
