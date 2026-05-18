<?php
header("Content-Type: application/json");
require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/../model/OrderModel.php";

if (!isAdmin()) {
    echo json_encode(array("status" => "error", "message" => "Admin access required."));
    exit;
}

$status = $_POST["status"] ?? "";

if ($status != "confirmed" && $status != "rejected") {
    echo json_encode(array("status" => "error", "message" => "Invalid status."));
    exit;
}

if (updateOrderStatus($_POST["order_id"] ?? 0, $status)) {
    echo json_encode(array("status" => "success"));
} else {
    echo json_encode(array("status" => "error", "message" => "Update failed."));
}
?>
