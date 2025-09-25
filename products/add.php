<?php
include("connection.php");


$message = "";

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];

    if (!empty($name) && $price > 0) {
        $query = "INSERT INTO products (name, description, price, quantity) 
                  VALUES ('$name', '$description', '$price', '$quantity')";
        if (mysqli_query($con, $query)) {
            $message = "<div class='success-message'>✅ Product added successfully!</div>";
        } else {
            $message = "<div class='error-message'>❌ Error: " . mysqli_error($con) . "</div>";
        }
    } else {
        $message = "<div class='error-message'>⚠️ Please fill required fields correctly.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Product</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../payment/pay.css">
</head>
<body>
  <div class="app-container">
    <div class="app-header">
      <h1>Add Product</h1>
      <p>Fill in the details below to add a new product</p>
    </div>

    <?php if ($message) echo $message; ?>

    <form method="POST" action="">
        <div class="mb-3">
            <label class="form-label">Product Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="3"></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Price</label>
            <input type="number" step="0.01" name="price" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Quantity</label>
            <input type="number" name="quantity" class="form-control" required>
        </div>

        <button type="submit" class="payment-button w-100">💾 Save Product</button>
    </form>
  </div>
</body>
</html>
