<?php
require_once "header.php";
requireAdmin();
require_once __DIR__ . "/../controller/ProductController.php";

$message = "";
$edit = null;

if (isset($_GET["delete"])) {
    $message = deleteProductById($_GET["delete"]) ? "Product deleted." : "Cannot delete product already used in an order.";
}

if (isset($_GET["edit"])) {
    $edit = getProductById($_GET["edit"]);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $result = saveProductController($_POST, $_FILES);
    $message = $result == "success" ? "Product saved successfully." : $result;
}

$products = getProducts();
$categories = getCategories();
?>

<h2>Product Management</h2>
<?php if ($message != "") { ?><div class="message success"><?php echo clean($message); ?></div><?php } ?>

<form method="post" enctype="multipart/form-data" onsubmit="return validateProduct()">
    <input type="hidden" name="id" value="<?php echo $edit ? $edit["id"] : ""; ?>">

    <label>Name</label>
    <input type="text" name="name" id="name" value="<?php echo $edit ? clean($edit["name"]) : ""; ?>">

    <label>Description</label>
    <textarea name="description" id="description"><?php echo $edit ? clean($edit["description"]) : ""; ?></textarea>

    <label>Size Chart</label>
    <textarea name="size_chart" id="size_chart"><?php echo $edit ? clean($edit["size_chart"]) : "S, M, L, XL"; ?></textarea>

    <label>Available Sizes</label>
    <input type="text" name="available_sizes" id="available_sizes" value="<?php echo $edit ? clean($edit["available_sizes"]) : "S,M,L,XL"; ?>" placeholder="Example: S,M,L,XL">

    <label>Price</label>
    <input type="number" step="0.01" name="price" id="price" value="<?php echo $edit ? clean($edit["price"]) : ""; ?>">

    <label>Gender</label>
    <select name="gender" id="gender">
        <option value="Men" <?php if ($edit && $edit["gender"] == "Men") echo "selected"; ?>>Men</option>
        <option value="Women" <?php if ($edit && $edit["gender"] == "Women") echo "selected"; ?>>Women</option>
    </select>

    <label>Category</label>
    <select name="category_id" id="category_id">
        <?php foreach ($categories as $category) { ?>
            <option value="<?php echo $category["id"]; ?>" <?php if ($edit && $edit["category_id"] == $category["id"]) echo "selected"; ?>>
                <?php echo clean($category["gender"] . " - " . $category["name"]); ?>
            </option>
        <?php } ?>
    </select>

    <label>Stock</label>
    <input type="number" name="stock" id="stock" value="<?php echo $edit ? clean($edit["stock"]) : ""; ?>">

    <div class="form-options">
        <label><input type="checkbox" name="is_featured" <?php if ($edit && $edit["is_featured"]) echo "checked"; ?>> Featured Product</label>
        <label><input type="checkbox" name="is_new_arrival" <?php if ($edit && $edit["is_new_arrival"]) echo "checked"; ?>> New Arrival</label>
    </div>

    <label>Image</label>
    <input type="file" name="image" id="image" accept="image/jpeg,image/png">

    <button type="submit">Save Product</button>
</form>

<h3>All Products</h3>
<table>
    <thead>
        <tr>
            <th>Name</th>
            <th>Gender</th>
            <th>Category</th>
            <th>Sizes</th>
            <th>Price</th>
            <th>Stock</th>
            <th>Display</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($products as $product) { ?>
            <tr>
                <td><?php echo clean($product["name"]); ?></td>
                <td><?php echo clean($product["gender"]); ?></td>
                <td><?php echo clean($product["category_name"]); ?></td>
                <td><?php echo clean($product["available_sizes"]); ?></td>
                <td>BDT <?php echo clean($product["price"]); ?></td>
                <td><?php echo clean($product["stock"]); ?></td>
                <td>
                    <?php if ($product["is_featured"]) { ?><span class="badge">Featured</span><?php } ?>
                    <?php if ($product["is_new_arrival"]) { ?><span class="badge accent">New</span><?php } ?>
                    <?php if (!$product["is_featured"] && !$product["is_new_arrival"]) { ?><span class="muted-text">Regular</span><?php } ?>
                </td>
                <td>
                    <a class="btn secondary" href="admin_products.php?edit=<?php echo $product["id"]; ?>">Edit</a>
                    <a class="btn danger" onclick="return confirm('Delete product?')" href="admin_products.php?delete=<?php echo $product["id"]; ?>">Delete</a>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>

<script>
function validateProduct() {
    if (document.getElementById("name").value.trim() == "" || document.getElementById("description").value.trim() == "" || document.getElementById("size_chart").value.trim() == "" || document.getElementById("available_sizes").value.trim() == "") {
        alert("Name, description, size chart and available sizes are required.");
        return false;
    }
    if (parseFloat(document.getElementById("price").value) <= 0) {
        alert("Price must be positive.");
        return false;
    }
    if (parseInt(document.getElementById("stock").value) < 0) {
        alert("Stock cannot be negative.");
        return false;
    }
    return true;
}
</script>

<?php require_once "footer.php"; ?>
