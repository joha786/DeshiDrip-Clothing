<?php
header("Content-Type: application/json");
require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/../model/OrderModel.php";

if (!isCustomer()) {
    echo json_encode(array("status" => "error", "message" => "Customer login required."));
    exit;
}

$orderId = (int) ($_POST["order_id"] ?? 0);

if ($orderId <= 0) {
    echo json_encode(array("status" => "error", "message" => "Invalid order."));
    exit;
}

$result = cancelCustomerOrder($orderId, $_SESSION["user_id"]);

if ($result == "success") {
    echo json_encode(array("status" => "success", "message" => "Order cancelled."));
} else {
    echo json_encode(array("status" => "error", "message" => $result));
}
?>
