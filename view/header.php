<?php
require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/../model/CartModel.php";

$cartCount = 0;
if (isLoggedIn() && isCustomer()) {
    $cartCount = getCartCount($_SESSION["user_id"]);
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DeshiDrip Clothing</title>
    <link rel="stylesheet" href="../public/css/style.css?v=3">
</head>
<body>
<div class="navbar">
    <a class="brand" href="home.php">DeshiDrip</a>
    <div class="nav-links">
        <a href="home.php">Home</a>
        <a href="products.php">Products</a>
        <?php if (isLoggedIn() && isCustomer()) { ?>
            <a href="cart.php">Cart <span id="cartCount"><?php echo $cartCount; ?></span></a>
            <a href="profile.php">Profile</a>
            <a href="purchase_history.php">Purchase History</a>
        <?php } ?>
        <?php if (isLoggedIn() && isAdmin()) { ?>
            <a href="admin_dashboard.php">Dashboard</a>
            <a href="admin_products.php">Products</a>
            <a href="admin_customers.php">Customers</a>
            <a href="admin_orders.php">Orders</a>
            <a href="admin_history.php">History</a>
        <?php } ?>
        <?php if (isLoggedIn()) { ?>
            <span class="hello"><?php echo clean($_SESSION["name"]); ?></span>
            <a href="logout.php">Logout</a>
        <?php } else { ?>
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
        <?php } ?>
    </div>
</div>
<main class="container">
