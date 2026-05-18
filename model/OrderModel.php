<?php
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/CartModel.php";

function ensureOrderSizeColumn()
{
    global $conn;
    static $checked = false;

    if ($checked) {
        return;
    }

    $selectedSize = mysqli_query($conn, "SHOW COLUMNS FROM order_items LIKE 'selected_size'");
    if ($selectedSize && mysqli_num_rows($selectedSize) == 0) {
        mysqli_query($conn, "ALTER TABLE order_items ADD selected_size VARCHAR(50) NOT NULL DEFAULT ''");
    }

    mysqli_query($conn, "ALTER TABLE orders MODIFY status ENUM('pending','confirmed','rejected','cancelled') NOT NULL DEFAULT 'pending'");

    $checked = true;
}

function createOrderFromCart($userId, $paymentMethod, $transactionId)
{
    global $conn;
    ensureOrderSizeColumn();

    $userId = (int) $userId;
    $paymentMethod = mysqli_real_escape_string($conn, $paymentMethod);
    $transactionId = mysqli_real_escape_string($conn, $transactionId);
    $items = getCartItems($userId);

    if (count($items) == 0) {
        return 0;
    }

    $total = 0;
    foreach ($items as $item) {
        if ((int) $item["quantity"] > (int) $item["stock"]) {
            return 0;
        }
        $total += ((float) $item["price"] * (int) $item["quantity"]);
    }

    mysqli_query($conn, "INSERT INTO orders (user_id, total_amount, status) VALUES ($userId, $total, 'pending')");
    $orderId = mysqli_insert_id($conn);

    foreach ($items as $item) {
        $productId = (int) $item["product_id"];
        $quantity = (int) $item["quantity"];
        $unitPrice = (float) $item["price"];
        $selectedSize = mysqli_real_escape_string($conn, $item["selected_size"]);
        mysqli_query($conn, "INSERT INTO order_items (order_id, product_id, quantity, unit_price, selected_size)
                             VALUES ($orderId, $productId, $quantity, $unitPrice, '$selectedSize')");
        mysqli_query($conn, "UPDATE products SET stock = stock - $quantity WHERE id = $productId");
    }

    mysqli_query($conn, "INSERT INTO payments (order_id, amount, payment_method, transaction_id)
                         VALUES ($orderId, $total, '$paymentMethod', '$transactionId')");
    clearCart($userId);

    return $orderId;
}

function getAllOrders()
{
    global $conn;

    $query = "SELECT orders.*, users.name AS customer_name, users.email
              FROM orders
              INNER JOIN users ON orders.user_id = users.id
              WHERE orders.status != 'cancelled'
              ORDER BY orders.id DESC";
    $result = mysqli_query($conn, $query);
    $orders = array();

    while ($row = mysqli_fetch_assoc($result)) {
        $orders[] = $row;
    }

    return $orders;
}

function updateOrderStatus($orderId, $status)
{
    global $conn;
    ensureOrderSizeColumn();

    $orderId = (int) $orderId;
    $status = mysqli_real_escape_string($conn, $status);
    $result = mysqli_query($conn, "SELECT * FROM orders WHERE id = $orderId LIMIT 1");
    $order = mysqli_fetch_assoc($result);

    if (!$order || $order["status"] == "cancelled") {
        return false;
    }

    if ($status == "confirmed") {
        mysqli_query($conn, "UPDATE orders SET status = 'confirmed' WHERE id = $orderId AND status = 'pending'");
        return mysqli_affected_rows($conn) > 0;
    }

    if ($status == "rejected" && $order["status"] != "rejected") {
        mysqli_query($conn, "UPDATE orders SET status = 'rejected' WHERE id = $orderId AND status IN ('pending', 'confirmed')");

        if (mysqli_affected_rows($conn) > 0) {
            foreach (getOrderItems($orderId) as $item) {
                $productId = (int) $item["product_id"];
                $quantity = (int) $item["quantity"];
                mysqli_query($conn, "UPDATE products SET stock = stock + $quantity WHERE id = $productId");
            }

            return true;
        }
    }

    return false;
}

function cancelCustomerOrder($orderId, $userId)
{
    global $conn;
    ensureOrderSizeColumn();

    $orderId = (int) $orderId;
    $userId = (int) $userId;
    $result = mysqli_query($conn, "SELECT * FROM orders WHERE id = $orderId AND user_id = $userId LIMIT 1");
    $order = mysqli_fetch_assoc($result);

    if (!$order) {
        return "Order not found.";
    }

    if ($order["status"] == "confirmed") {
        return "Confirmed orders cannot be cancelled.";
    }

    if ($order["status"] != "pending") {
        return "Only pending orders can be cancelled.";
    }

    $items = getOrderItems($orderId);
    mysqli_query($conn, "UPDATE orders SET status = 'cancelled' WHERE id = $orderId AND user_id = $userId AND status = 'pending'");

    if (mysqli_affected_rows($conn) == 0) {
        return "Unable to cancel order.";
    }

    foreach ($items as $item) {
        $productId = (int) $item["product_id"];
        $quantity = (int) $item["quantity"];
        mysqli_query($conn, "UPDATE products SET stock = stock + $quantity WHERE id = $productId");
    }

    return "success";
}

function getUserOrders($userId)
{
    global $conn;
    ensureOrderSizeColumn();

    $userId = (int) $userId;
    $result = mysqli_query($conn, "SELECT * FROM orders WHERE user_id = $userId ORDER BY id DESC");
    $orders = array();

    while ($row = mysqli_fetch_assoc($result)) {
        $orders[] = $row;
    }

    return $orders;
}

function getOrderItems($orderId)
{
    global $conn;
    ensureOrderSizeColumn();

    $orderId = (int) $orderId;
    $query = "SELECT order_items.*, products.name, products.image_path
              FROM order_items
              INNER JOIN products ON order_items.product_id = products.id
              WHERE order_items.order_id = $orderId";
    $result = mysqli_query($conn, $query);
    $items = array();

    while ($row = mysqli_fetch_assoc($result)) {
        $items[] = $row;
    }

    return $items;
}

function getAllPurchaseHistory()
{
    global $conn;

    $query = "SELECT orders.*, users.name AS customer_name, users.email
              FROM orders
              INNER JOIN users ON orders.user_id = users.id
              ORDER BY orders.id DESC";
    $result = mysqli_query($conn, $query);
    $orders = array();

    while ($row = mysqli_fetch_assoc($result)) {
        $orders[] = $row;
    }

    return $orders;
}

function getDashboardCounts()
{
    global $conn;

    $counts = array();
    $result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM products");
    $counts["products"] = mysqli_fetch_assoc($result)["total"];
    $result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE role = 'customer'");
    $counts["customers"] = mysqli_fetch_assoc($result)["total"];
    $result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM orders");
    $counts["orders"] = mysqli_fetch_assoc($result)["total"];
    $result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM orders WHERE status = 'pending'");
    $counts["pending"] = mysqli_fetch_assoc($result)["total"];

    return $counts;
}
?>
