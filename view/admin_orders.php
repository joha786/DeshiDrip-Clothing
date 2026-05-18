<?php
require_once "header.php";
requireAdmin();
require_once __DIR__ . "/../model/OrderModel.php";

$orders = getAllOrders();
?>

<h2>Order List</h2>
<div id="orderMsg"></div>
<table>
    <thead>
        <tr>
            <th>Order</th>
            <th>Customer</th>
            <th>Total</th>
            <th>Status</th>
            <th>Date</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($orders as $order) { ?>
            <tr>
                <td>#<?php echo $order["id"]; ?></td>
                <td><?php echo clean($order["customer_name"]); ?></td>
                <td>BDT <?php echo clean($order["total_amount"]); ?></td>
                <td id="status<?php echo $order["id"]; ?>"><?php echo clean($order["status"]); ?></td>
                <td><?php echo clean($order["order_date"]); ?></td>
                <td>
                    <button onclick="changeStatus(<?php echo $order["id"]; ?>, 'confirmed')">Confirm</button>
                    <button class="danger" onclick="changeStatus(<?php echo $order["id"]; ?>, 'rejected')">Reject</button>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>

<script>
function changeStatus(orderId, status) {
    let data = new FormData();
    data.append("order_id", orderId);
    data.append("status", status);

    fetch("../ajax/order_status.php", { method: "POST", body: data })
    .then(res => res.json())
    .then(data => {
        if (data.status == "success") {
            document.getElementById("status" + orderId).innerText = status;
            document.getElementById("orderMsg").innerHTML = "<p class='message success'>Order updated.</p>";
        } else {
            document.getElementById("orderMsg").innerHTML = "<p class='message'>" + data.message + "</p>";
        }
    });
}
</script>

<?php require_once "footer.php"; ?>
