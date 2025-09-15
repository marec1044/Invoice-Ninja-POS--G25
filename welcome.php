<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}
$user = $_SESSION['user'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Welcome - NTI Pay</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
  <style>
    *{margin:0;padding:0;box-sizing:border-box;font-family:'Poppins',sans-serif}
    body{
      height:100vh;
      display:flex;
      justify-content:center;
      align-items:center;
      background:linear-gradient(135deg,#001f3f,#00509e);
      color:white;
      text-align:center;
    }
    .container{
      background:rgba(255,255,255,0.1);
      backdrop-filter:blur(15px);
      padding:40px;
      border-radius:20px;
      box-shadow:0 8px 25px rgba(0,0,0,0.5);
      animation:fadeIn 1s ease;
    }
    @keyframes fadeIn{
      from{opacity:0;transform:translateY(20px);}
      to{opacity:1;transform:translateY(0);}
    }
    h1{
      font-size:32px;
      font-weight:700;
      margin-bottom:15px;
      color:#ffca28;
    }
    p{
      font-size:18px;
      margin-bottom:25px;
      color:#e0e0e0;
    }
    .btn{
      display:inline-block;
      padding:12px 25px;
      background:#ffca28;
      color:#000;
      border:none;
      border-radius:10px;
      font-weight:600;
      font-size:16px;
      text-decoration:none;
      transition:0.3s;
    }
    .btn:hover{
      background:#ffc107;
      transform:scale(1.05);
    }
    .logout{
      margin-top:20px;
      display:block;
      color:#ff4c4c;
      text-decoration:none;
      font-size:14px;
    }
    .logout:hover{color:#ff6b6b;}
  </style>
</head>
<body>
  <div class="container">
    <h1>Welcome, <?php echo htmlspecialchars($user['name']); ?>!</h1>
    <p>You are successfully logged in to <strong>NTI Pay</strong>.</p>
    <a href="payment.php" class="btn">Proceed to Payment</a>
    <a href="logout.php" class="logout">Logout</a>
  </div>
</body>
</html>
