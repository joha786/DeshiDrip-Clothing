<?php
header("Content-Type: application/json");
require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/../model/OrderModel.php";

if (!isCustomer()) {
    echo json_encode(array("status" => "error", "message" => "Customer login required."));
    exit;
}

$paymentMethod = $_POST["payment_method"] ?? "";
$transactionId = trim($_POST["transaction_id"] ?? "");
$allowed = array("Credit Card", "bKash", "Nagad", "Bank Transfer", "Cash on Delivery");

if (!in_array($paymentMethod, $allowed)) {
    echo json_encode(array("status" => "error", "message" => "Select a valid payment method."));
    exit;
}

$orderId = createOrderFromCart($_SESSION["user_id"], $paymentMethod, $transactionId);

if ($orderId > 0) {
    echo json_encode(array("status" => "success", "order_id" => $orderId));
} else {
    echo json_encode(array("status" => "error", "message" => "Unable to place order. Check cart stock."));
}
?>
