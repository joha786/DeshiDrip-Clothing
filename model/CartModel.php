<?php
require_once __DIR__ . "/../config/db.php";

function ensureCartSizeColumn()
{
    global $conn;
    static $checked = false;

    if ($checked) {
        return;
    }

    $selectedSize = mysqli_query($conn, "SHOW COLUMNS FROM cart LIKE 'selected_size'");
    if ($selectedSize && mysqli_num_rows($selectedSize) == 0) {
        mysqli_query($conn, "ALTER TABLE cart ADD selected_size VARCHAR(50) NOT NULL DEFAULT ''");
    }

    $checked = true;
}

function getCartItem($userId, $productId, $selectedSize)
{
    global $conn;
    ensureCartSizeColumn();

    $userId = (int) $userId;
    $productId = (int) $productId;
    $selectedSize = mysqli_real_escape_string($conn, $selectedSize);
    $result = mysqli_query($conn, "SELECT * FROM cart WHERE user_id = $userId AND product_id = $productId AND selected_size = '$selectedSize'");

    return mysqli_fetch_assoc($result);
}

function addToCart($userId, $productId, $quantity, $selectedSize)
{
    global $conn;
    ensureCartSizeColumn();

    $userId = (int) $userId;
    $productId = (int) $productId;
    $quantity = (int) $quantity;
    $selectedSize = mysqli_real_escape_string($conn, $selectedSize);
    $item = getCartItem($userId, $productId, $selectedSize);

    if ($item) {
        $newQuantity = (int) $item["quantity"] + $quantity;
        return mysqli_query($conn, "UPDATE cart SET quantity = $newQuantity WHERE id = " . (int) $item["id"]);
    }

    return mysqli_query($conn, "INSERT INTO cart (user_id, product_id, quantity, selected_size) VALUES ($userId, $productId, $quantity, '$selectedSize')");
}

function updateCartQuantity($cartId, $userId, $quantity)
{
    global $conn;
    ensureCartSizeColumn();

    $cartId = (int) $cartId;
    $userId = (int) $userId;
    $quantity = (int) $quantity;
    $sql = "UPDATE cart SET quantity = $quantity WHERE id = $cartId AND user_id = $userId";
    return mysqli_query($conn, $sql);
}

function removeCartItem($cartId, $userId)
{
    global $conn;
    ensureCartSizeColumn();

    $cartId = (int) $cartId;
    $userId = (int) $userId;

    return mysqli_query($conn, "DELETE FROM cart WHERE id = $cartId AND user_id = $userId");
}

function clearCart($userId)
{
    global $conn;
    ensureCartSizeColumn();

    $userId = (int) $userId;

    return mysqli_query($conn, "DELETE FROM cart WHERE user_id = $userId");
}

function getCartItems($userId)
{
    global $conn;
    ensureCartSizeColumn();

    $userId = (int) $userId;
    $query = "SELECT cart.*, products.name, products.price, products.stock, products.image_path
              FROM cart
              INNER JOIN products ON cart.product_id = products.id
              WHERE cart.user_id = $userId
              ORDER BY cart.id DESC";
    $result = mysqli_query($conn, $query);
    $items = array();

    while ($row = mysqli_fetch_assoc($result)) {
        $items[] = $row;
    }

    return $items;
}

function getCartCount($userId)
{
    global $conn;

    $userId = (int) $userId;
    $result = mysqli_query($conn, "SELECT SUM(quantity) AS total FROM cart WHERE user_id = $userId");
    $row = mysqli_fetch_assoc($result);

    return (int) $row["total"];
}
?>
