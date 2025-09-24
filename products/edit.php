<?php
include_once("../Login/config.php");

$message = "";

// جلب بيانات المنتج حسب id
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM products WHERE id = $id";
    $result = mysqli_query($conn, $sql);
    $product = mysqli_fetch_assoc($result);
} else {
    header("Location: index.php");
    exit;
}

// تحديث البيانات عند الضغط على submit
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];

    if (!empty($name) && $price > 0) {
        $update = "UPDATE products SET 
                    name='$name', 
                    description='$description', 
                    price='$price', 
                    quantity='$quantity' 
                   WHERE id=$id";

        if (mysqli_query($conn, $update)) {
            $message = "<div class='success-message' style='display:block;'>✅ Product updated successfully!</div>";
            // تحديث المتغير product لعرض القيم الجديدة
            $product = ['name'=>$name,'description'=>$description,'price'=>$price,'quantity'=>$quantity];
        } else {
            $message = "<div class='error-message' style='display:block;'>❌ Error: ".mysqli_error($conn)."</div>";
        }
    } else {
        $message = "<div class='error-message' style='display:block;'>⚠️ Please fill required fields correctly.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Product</title>
    <link rel="stylesheet" href="../payment/pay.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="font-family: var(--font-family); background-color: var(--background-light);">
<div class="app-container">
    <div class="app-header">
        <h1 style="color: var(--primary-color);">Edit Product</h1>
        <p style="color: var(--text-primary);">Modify the product details below</p>
    </div>

    <!-- زر العودة للصفحة الرئيسية -->
    <a href="index.php" class="btn btn-secondary mb-3">← Back to Products</a>

    <?php if ($message) echo $message; ?>

    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Product Name</label>
            <input type="text" name="name" class="form-control" required value="<?= htmlspecialchars($product['name']) ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($product['description']) ?></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Price</label>
            <input type="number" step="0.01" name="price" class="form-control" required value="<?= $product['price'] ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Quantity</label>
            <input type="number" name="quantity" class="form-control" required value="<?= $product['quantity'] ?>">
        </div>

        <button type="submit" class="payment-button w-100">💾 Update Product</button>
    </form>
</div>

</body>
</html>
