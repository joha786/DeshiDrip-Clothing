<?php
header("Content-Type: application/json");
require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/../model/CartModel.php";
require_once __DIR__ . "/../model/ProductModel.php";

if (!isCustomer()) {
    echo json_encode(array("status" => "error", "message" => "Login as customer first."));
    exit;
}

$action = $_POST["action"] ?? "";
$userId = $_SESSION["user_id"];

if ($action == "add") {
    $productId = (int) ($_POST["product_id"] ?? 0);
    $quantity = (int) ($_POST["quantity"] ?? 0);
    $selectedSize = trim($_POST["selected_size"] ?? "");
    $product = getProductById($productId);

    if (!$product || $quantity < 1 || $quantity > (int) $product["stock"]) {
        echo json_encode(array("status" => "error", "message" => "Invalid quantity or product."));
        exit;
    }

    $sizes = array_filter(array_map("trim", explode(",", $product["available_sizes"])));
    if ($selectedSize == "" || !in_array($selectedSize, $sizes)) {
        echo json_encode(array("status" => "error", "message" => "Select an available size."));
        exit;
    }

    addToCart($userId, $productId, $quantity, $selectedSize);
    echo json_encode(array("status" => "success", "message" => "Product added to cart.", "count" => getCartCount($userId)));
    exit;
}

if ($action == "update") {
    $quantity = (int) ($_POST["quantity"] ?? 0);
    if ($quantity < 1) {
        echo json_encode(array("status" => "error", "message" => "Invalid quantity."));
        exit;
    }
    updateCartQuantity($_POST["cart_id"] ?? 0, $userId, $quantity);
    echo json_encode(array("status" => "success", "message" => "Cart updated.", "count" => getCartCount($userId)));
    exit;
}

if ($action == "remove") {
    removeCartItem($_POST["cart_id"] ?? 0, $userId);
    echo json_encode(array("status" => "success", "message" => "Item removed.", "count" => getCartCount($userId)));
    exit;
}

echo json_encode(array("status" => "error", "message" => "Invalid request."));
?>
