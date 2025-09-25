<?php 
include 'db.php'; // ربط بقاعدة البيانات
$result = mysqli_query($conn, "SELECT * FROM customers");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Customers</title>
  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- ملف التنسيقات الموحد -->
  <link rel="stylesheet" href="pay.css">
</head>
<body>

<!-- النافبار -->
<nav class="navbar navbar-expand-lg navbar-dark custom-navbar">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="#">NTI<span class="text-warning">pay</span></a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse justify-content-between" id="navbarNav">
      <ul class="navbar-nav">
        <li class="nav-item"><a class="nav-link active" href="Customers.php">Customers</a></li>
        <li class="nav-item"><a class="nav-link" href="Products.php">Products</a></li>
        <li class="nav-item"><a class="nav-link" href="Invoices.php">Invoices</a></li>
      </ul>

      <ul class="navbar-nav">
        <li class="nav-item me-3">
          <a class="btn btn-light btn-sm fw-bold" href="#">My Account</a>
        </li>
        <li class="nav-item">
          <a class="btn btn-outline-light btn-sm fw-bold" href="#">Logout</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<!-- المحتوى -->
<div class="container mt-4">
  <h2 class="mb-4">Customers List</h2>

  <a href="Add-Customer.php" class="btn btn-primary mb-3">+ Add Customer</a>

  <table class="table table-bordered table-striped">
    <thead>
      <tr>
        <th>ID</th>
        <th>Customer Name</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Address</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = mysqli_fetch_assoc($result)) { ?>
        <tr>
          <td><?php echo $row['id']; ?></td>
          <td><?php echo $row['name']; ?></td>
          <td><?php echo $row['email']; ?></td>
          <td><?php echo $row['phone']; ?></td>
          <td><?php echo $row['address']; ?></td>
          <td>
            <a href="Edit-Customer.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
            <a href="Delete-Customer.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this customer?')">Delete</a>
          </td>
        </tr>
      <?php } ?>
    </tbody>
  </table>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>