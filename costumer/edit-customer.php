<?php 
include 'db.php';

// جلب بيانات العميل المطلوب تعديله
$id = $_GET['id'];
$result = mysqli_query($conn, "SELECT * FROM customers WHERE id=$id");
$customer = mysqli_fetch_assoc($result);

// حفظ التعديل
if (isset($_POST['update'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    mysqli_query($conn, "UPDATE customers 
                         SET name='$name', email='$email', phone='$phone', address='$address' 
                         WHERE id=$id");

    header("Location: Customers.php"); // رجوع لصفحة العملاء
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Customer</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container mt-5">
  <h2>Edit Customer</h2>
  <form method="post">
    <div class="mb-3">
      <label class="form-label">Name</label>
      <input type="text" name="name" class="form-control" value="<?php echo $customer['name']; ?>" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Email</label>
      <input type="email" name="email" class="form-control" value="<?php echo $customer['email']; ?>">
    </div>
    <div class="mb-3">
      <label class="form-label">Phone</label>
      <input type="text" name="phone" class="form-control" value="<?php echo $customer['phone']; ?>">
    </div>
    <div class="mb-3">
      <label class="form-label">Address</label>
      <input type="text" name="address" class="form-control" value="<?php echo $customer['address']; ?>">
    </div>
    <button type="submit" name="update" class="btn btn-primary">Update</button>
    <a href="Customers.php" class="btn btn-secondary">Cancel</a>
  </form>
</div>
</body>
</html>
