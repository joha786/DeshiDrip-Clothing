<?php
require_once "header.php";
requireAdmin();
require_once __DIR__ . "/../model/UserModel.php";

if (isset($_GET["delete"])) {
    deleteCustomerById($_GET["delete"]);
}

$customers = getAllCustomers();
?>

<h2>Customers</h2>
<table>
    <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Address</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($customers as $customer) { ?>
            <tr>
                <td><?php echo clean($customer["name"]); ?></td>
                <td><?php echo clean($customer["email"]); ?></td>
                <td><?php echo clean($customer["phone"]); ?></td>
                <td><?php echo clean($customer["address"]); ?></td>
                <td><a class="btn danger" onclick="return confirm('Delete customer and all data?')" href="admin_customers.php?delete=<?php echo $customer["id"]; ?>">Delete</a></td>
            </tr>
        <?php } ?>
    </tbody>
</table>

<?php require_once "footer.php"; ?>
