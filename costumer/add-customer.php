<?php 
include 'db.php';

if (isset($_POST['save'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    $sql = "INSERT INTO customers (name, email, phone, address) 
            VALUES ('$name', '$email', '$phone', '$address')";
    
    if (mysqli_query($conn, $sql)) {
        header("Location: Customers.php"); // بعد الإضافة يرجع لصفحة العملاء
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Customer</title>
  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- ملف CSS الموحد -->
  <link rel="stylesheet" href="pay.css">
</head>
<body>

<div class="container mt-5">
  <h2 class="mb-4">Add New Customer</h2>

  <form method="post">
    <div class="mb-3">
      <label class="form-label">Name</label>
      <input type="text" name="name" class="form-control" placeholder="Enter customer name" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Email</label>
      <input type="email" name="email" class="form-control" placeholder="Enter customer email">
    </div>
    <div class="mb-3">
      <label class="form-label">Phone</label>
      <input type="text" name="phone" class="form-control" placeholder="Enter customer phone">
    </div>
    <div class="mb-3">
      <label class="form-label">Address</label>
      <input type="text" name="address" class="form-control" placeholder="Enter customer address">
    </div>
    <button type="submit" name="save" class="btn btn-primary">Save</button>
    <a href="Customers.php" class="btn btn-secondary">Cancel</a>
  </form>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
