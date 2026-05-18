<?php
require_once "header.php";
requireAdmin();
require_once __DIR__ . "/../model/OrderModel.php";

$orders = getAllPurchaseHistory();
?>

<h2>All Purchase History</h2>

<?php foreach ($orders as $order) { ?>
    <div class="card" style="margin-bottom:16px">
        <h3>Order #<?php echo $order["id"]; ?> - <?php echo clean($order["customer_name"]); ?></h3>
        <p>Email: <?php echo clean($order["email"]); ?> | Total: BDT <?php echo clean($order["total_amount"]); ?> | Status: <?php echo clean($order["status"]); ?></p>
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
    </div>
<?php } ?>

<?php require_once "footer.php"; ?>
