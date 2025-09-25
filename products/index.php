<?php
include_once("../Login/config.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Products List</title>
    <link rel="stylesheet" href="../payment/pay.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="font-family: var(--font-family); background-color: var(--background-light);">
<div class="app-container">
    <div class="app-header">
        <h1 style="color: var(--primary-color);">Products List</h1>
        <p style="color: var(--text-primary);">Manage your products below</p>
    </div>

    <!-- رسالة بعد الحذف -->
    <?php if(isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
    <div class="success-message" style="display:block;">✅ Product deleted successfully!</div>
    <?php endif; ?>

    <!-- حقل البحث -->
    <input type="text" id="searchInput" class="form-control mb-3" placeholder="Search by name..." style="margin-bottom:15px;">

    <a href="add.php" class="btn" style="background: var(--primary-color); color: var(--text-on-primary); margin-bottom:15px;">+ Add Product</a>

    <table class="table table-striped table-bordered" id="productsTable">
        <thead class="table-dark" style="background-color: var(--primary-color); color: var(--text-on-primary);">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Description</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Created At</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody id="productsBody">
        <?php
        $sql = "SELECT * FROM products";
        $result = mysqli_query($conn, $sql);
        while ($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td class="product-name"><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['description']) ?></td>
                <td><?= $row['price'] ?></td>
                <td><?= $row['quantity'] ?></td>
                <td><?= $row['created_at'] ?></td>
                <td>
                    <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                    <a href="delete.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?');">Delete</a>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>

<script>
// فلترة الجدول مباشرة أثناء الكتابة
document.getElementById('searchInput').addEventListener('input', function() {
    const filter = this.value.toLowerCase();
    const rows = document.querySelectorAll('#productsBody tr');

    rows.forEach(row => {
        const name = row.querySelector('.product-name').textContent.toLowerCase();
        if (name.includes(filter)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});
</script>

</body>
</html>
