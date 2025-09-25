<?php
include_once("../Login/config.php");

if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // تحويل القيمة لرقم صحيح
    if (mysqli_query($conn, "DELETE FROM products WHERE id = $id")) {
        // إعادة التوجيه مع رسالة نجاح
        header("Location: index.php?msg=deleted");
        exit;
    } else {
        // خطأ أثناء الحذف
        echo "Error deleting product: " . mysqli_error($conn);
    }
} else {
    header("Location: index.php");
    exit;
}
?>
