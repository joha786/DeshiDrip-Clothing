<?php
require_once "header.php";
requireCustomer();
require_once __DIR__ . "/../model/CartModel.php";

$items = getCartItems($_SESSION["user_id"]);
$total = 0;
?>

<h2>Checkout Invoice</h2>

<?php if (count($items) == 0) { ?>
    <div class="message">Your cart is empty.</div>
<?php } else { ?>
    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Size</th>
                <th>Qty</th>
                <th>Price</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item) {
                $subtotal = $item["price"] * $item["quantity"];
                $total += $subtotal;
            ?>
                <tr>
                    <td><?php echo clean($item["name"]); ?></td>
                    <td><?php echo clean($item["selected_size"]); ?></td>
                    <td><?php echo clean($item["quantity"]); ?></td>
                    <td>BDT <?php echo clean($item["price"]); ?></td>
                    <td>BDT <?php echo $subtotal; ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    <h3>Total: BDT <?php echo $total; ?></h3>

    <form id="checkoutForm">
        <label>Payment Method</label>
        <select name="payment_method" id="paymentMethod">
            <option value="">Select Payment</option>
            <option value="Credit Card">Credit Card</option>
            <option value="bKash">bKash</option>
            <option value="Nagad">Nagad</option>
            <option value="Bank Transfer">Bank Transfer</option>
            <option value="Cash on Delivery">Cash on Delivery</option>
        </select>

        <label>Transaction ID (optional for Cash on Delivery)</label>
        <input type="text" name="transaction_id">

        <button type="submit">Place Order</button>
        <a class="btn secondary" href="cart.php">Cancel</a>
    </form>
    <div id="orderMsg"></div>
<?php } ?>

<script>
let form = document.getElementById("checkoutForm");
if (form) {
    form.addEventListener("submit", function(e) {
        e.preventDefault();
        if (document.getElementById("paymentMethod").value == "") {
            alert("Select payment method.");
            return;
        }

        let data = new FormData(form);
        fetch("../ajax/checkout_handler.php", { method: "POST", body: data })
        .then(res => res.json())
        .then(data => {
            if (data.status == "success") {
                document.getElementById("orderMsg").innerHTML = "<p class='message success'>Order placed. Order ID: " + data.order_id + "</p>";
                setTimeout(function(){ window.location = "profile.php"; }, 1000);
            } else {
                document.getElementById("orderMsg").innerHTML = "<p class='message'>" + data.message + "</p>";
            }
        });
    });
}
</script>

<?php require_once "footer.php"; ?>
