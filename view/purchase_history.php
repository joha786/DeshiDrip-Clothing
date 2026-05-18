<?php
require_once "header.php";
requireCustomer();
require_once __DIR__ . "/../model/OrderModel.php";

$orders = getUserOrders($_SESSION["user_id"]);
?>

<h2>Purchase History</h2>
<p class="page-intro">Review your orders here. Pending orders can be cancelled until the admin confirms them.</p>
<div id="orderMsg"></div>

<?php if (count($orders) == 0) { ?>
    <div class="empty-state">No orders found.</div>
<?php } ?>

<?php foreach ($orders as $order) { ?>
    <div class="card order-card" id="order<?php echo $order["id"]; ?>">
        <div class="order-card-header">
            <div>
                <h3>Order #<?php echo $order["id"]; ?></h3>
                <p>Total: BDT <?php echo clean($order["total_amount"]); ?> | Date: <?php echo clean($order["order_date"]); ?></p>
            </div>
            <span class="badge" id="status<?php echo $order["id"]; ?>"><?php echo clean($order["status"]); ?></span>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Size</th>
                    <th>Qty</th>
                    <th>Unit Price</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (getOrderItems($order["id"]) as $item) { ?>
                    <tr>
                        <td><?php echo clean($item["name"]); ?></td>
                        <td><?php echo clean($item["selected_size"]); ?></td>
                        <td><?php echo clean($item["quantity"]); ?></td>
                        <td>BDT <?php echo clean($item["unit_price"]); ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <?php if ($order["status"] == "pending") { ?>
            <button class="danger cancel-order-btn" id="cancel<?php echo $order["id"]; ?>" onclick="cancelOrder(<?php echo $order["id"]; ?>)">Cancel Order</button>
        <?php } ?>
    </div>
<?php } ?>

<script>
function cancelOrder(orderId) {
    if (!confirm("Cancel this pending order?")) {
        return;
    }

    let data = new FormData();
    data.append("order_id", orderId);

    fetch("../ajax/cancel_order.php", { method: "POST", body: data })
    .then(res => res.json())
    .then(data => {
        if (data.status == "success") {
            document.getElementById("status" + orderId).innerText = "cancelled";
            document.getElementById("cancel" + orderId).remove();
            document.getElementById("orderMsg").innerHTML = "<p class='message success'>Order cancelled.</p>";
        } else {
            document.getElementById("orderMsg").innerHTML = "<p class='message'>" + data.message + "</p>";
        }
    });
}
</script>

<?php require_once "footer.php"; ?>
