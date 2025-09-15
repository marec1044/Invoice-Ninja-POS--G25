<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user = $_SESSION['user'];

if (isset($_POST['pay'])) {
    $bill_name = $_POST['bill_name'];
    $payment_type = $_POST['payment_type'];
    $transaction_number = $_POST['transaction_number'];
    $amount = $_POST['amount'];
    $user_id = $user['id'];

    $sql = "INSERT INTO payments (user_id, bill_name, payment_type, transaction_number, amount) 
            VALUES ('$user_id', '$bill_name', '$payment_type', '$transaction_number', '$amount')";

    if (mysqli_query($conn, $sql)) {
        $success = "Payment submitted successfully!";
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>NTI Pay - Payment</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
  <style>
    *{margin:0;padding:0;box-sizing:border-box;font-family:'Poppins',sans-serif}
    body{
      height:100vh;
      display:flex;
      justify-content:center;
      align-items:center;
      background:linear-gradient(135deg,#00509e,#001f3f);
      color:white;
    }
    .card{
      width:450px;
      background:rgba(255,255,255,0.1);
      backdrop-filter:blur(15px);
      padding:30px;
      border-radius:20px;
      box-shadow:0 8px 25px rgba(0,0,0,0.5);
      animation:fadeIn 1s ease;
    }
    @keyframes fadeIn{from{opacity:0;transform:translateY(20px);}to{opacity:1;transform:translateY(0);}}
    h2{text-align:center;margin-bottom:20px;font-weight:700;font-size:28px;color:#ffca28}
    .input-group{margin-bottom:15px}
    .input-group label{display:block;margin-bottom:6px;font-size:14px;color:#ffca28}
    .input-group input,.input-group select{
      width:100%;padding:12px;border:none;border-radius:10px;
      background:rgba(255,255,255,0.2);color:white;font-size:14px
    }
    .input-group input::placeholder{color:rgba(255,255,255,0.7)}
    button{
      width:100%;padding:12px;border:none;border-radius:10px;
      margin-top:10px;background:#ffca28;color:#000;font-weight:600;font-size:16px;cursor:pointer;transition:.3s
    }
    button:hover{background:#ffc107;transform:scale(1.02)}
    .message{text-align:center;margin-top:15px;font-size:14px}
    .success{color:#00e676}
    .error{color:#ff4c4c}
    .links{text-align:center;margin-top:20px}
    .links a{color:#ffca28;text-decoration:none;margin:0 10px}
  </style>
</head>
<body>
  <div class="card">
    <h2>Payment Portal</h2>
    <form method="POST">
      <div class="input-group">
        <label>Bill Name</label>
        <input type="text" name="bill_name" placeholder="Enter bill name" required>
      </div>
      <div class="input-group">
        <label>Payment Type</label>
        <select name="payment_type" required>
          <option value="">Select payment type</option>
          <option value="Electricity">Electricity</option>
          <option value="Water">Water</option>
          <option value="Internet">Internet</option>
          <option value="Other">Other</option>
        </select>
      </div>
      <div class="input-group">
        <label>Transaction Number</label>
        <input type="text" name="transaction_number" placeholder="Enter transaction number" required>
      </div>
      <div class="input-group">
        <label>Amount</label>
        <input type="number" step="0.01" name="amount" placeholder="Enter amount" required>
      </div>
      <button type="submit" name="pay">Submit Payment</button>
    </form>

    <?php if (isset($error)) echo "<div class='message error'>$error</div>"; ?>
    <?php if (isset($success)) echo "<div class='message success'>$success</div>"; ?>

    <div class="links">
      <a href="welcome.php">Back to Dashboard</a> | <a href="logout.php">Logout</a>
    </div>
  </div>
</body>
</html>
