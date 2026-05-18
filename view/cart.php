<?php
require_once "header.php";
requireCustomer();
require_once __DIR__ . "/../model/CartModel.php";

$items = getCartItems($_SESSION["user_id"]);
$total = 0;
?>

<h2>My Cart</h2>
<div id="cartMsg"></div>

<?php if (count($items) > 0) { ?>
    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Size</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Subtotal</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item) {
                $subtotal = $item["price"] * $item["quantity"];
                $total += $subtotal;
            ?>
                <tr id="row<?php echo $item["id"]; ?>">
                    <td><?php echo clean($item["name"]); ?></td>
                    <td><?php echo clean($item["selected_size"]); ?></td>
                    <td>BDT <?php echo clean($item["price"]); ?></td>
                    <td><input type="number" min="1" max="<?php echo $item["stock"]; ?>" value="<?php echo $item["quantity"]; ?>" onchange="updateQty(<?php echo $item["id"]; ?>, this.value)"></td>
                    <td>BDT <?php echo $subtotal; ?></td>
                    <td><button class="danger" onclick="removeItem(<?php echo $item["id"]; ?>)">Remove</button></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <h3>Total: BDT <?php echo $total; ?></h3>
    <a class="btn" href="checkout.php">Proceed To Checkout</a>
<?php } else { ?>
    <div class="empty-state">Your cart is empty. Add something you like from the products page.</div>
    <a class="btn" href="products.php">Start Shopping</a>
<?php } ?>

<script>
function sendCart(action, id, quantity) {
    let data = new FormData();
    data.append("action", action);
    data.append("cart_id", id);
    if (quantity !== undefined) {
        data.append("quantity", quantity);
    }
    fetch("../ajax/cart_handler.php", { method: "POST", body: data })
    .then(res => res.json())
    .then(data => {
        document.getElementById("cartMsg").innerHTML = "<p class='message success'>" + data.message + "</p>";
        setTimeout(function(){ location.reload(); }, 500);
    });
}

function updateQty(id, quantity) {
    quantity = parseInt(quantity);
    if (!quantity || quantity < 1) {
        alert("Quantity must be positive.");
        return;
    }
    sendCart("update", id, quantity);
}

function removeItem(id) {
    sendCart("remove", id);
}
</script>

<?php require_once "footer.php"; ?>
