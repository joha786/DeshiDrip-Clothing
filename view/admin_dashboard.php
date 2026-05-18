<?php
require_once "header.php";
requireAdmin();
require_once __DIR__ . "/../model/OrderModel.php";

$counts = getDashboardCounts();
?>

<h2>Admin Dashboard</h2>
<div class="stats">
    <div class="stat"><strong><?php echo $counts["products"]; ?></strong>Products</div>
    <div class="stat"><strong><?php echo $counts["customers"]; ?></strong>Customers</div>
    <div class="stat"><strong><?php echo $counts["orders"]; ?></strong>Orders</div>
    <div class="stat"><strong><?php echo $counts["pending"]; ?></strong>Pending Orders</div>
</div>

<?php require_once "footer.php"; ?>
