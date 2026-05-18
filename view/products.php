<?php
require_once "header.php";
require_once __DIR__ . "/../model/ProductModel.php";

$gender = $_GET["gender"] ?? "";
$category = $_GET["category"] ?? "";
$q = $_GET["q"] ?? "";
$collection = $_GET["collection"] ?? "";
$products = getProducts("", $category, $gender, 0, $collection);
$categories = getCategories();
?>

<h2>Products</h2>
<p class="page-intro">Filter the collection by name, gender, or category to find pieces that match your daily routine and budget.</p>
<form method="get" class="filters product-filters">
    <input type="text" name="q" placeholder="Search name" value="<?php echo clean($q); ?>">
    <select name="collection">
        <option value="">All Collections</option>
        <option value="new" <?php if ($collection == "new") echo "selected"; ?>>New Arrivals</option>
        <option value="featured" <?php if ($collection == "featured") echo "selected"; ?>>Featured</option>
    </select>
    <select name="gender">
        <option value="">All Gender</option>
        <option value="Men" <?php if ($gender == "Men") echo "selected"; ?>>Men</option>
        <option value="Women" <?php if ($gender == "Women") echo "selected"; ?>>Women</option>
    </select>
    <select name="category">
        <option value="">All Categories</option>
        <?php foreach ($categories as $cat) { ?>
            <option value="<?php echo $cat["id"]; ?>" <?php if ($category == $cat["id"]) echo "selected"; ?>><?php echo clean($cat["gender"] . " - " . $cat["name"]); ?></option>
        <?php } ?>
    </select>
    <button type="submit">Filter</button>
</form>

<?php
if (isset($_GET["q"])) {
    $products = getProducts($q, $category, $gender, 0, $collection);
}
?>

<p class="results-count"><?php echo count($products); ?> product<?php if (count($products) != 1) echo "s"; ?> found</p>
<div class="grid">
    <?php foreach ($products as $product) { ?>
        <div class="card">
            <img class="product-img" src="../<?php echo clean($product["image_path"]); ?>" alt="<?php echo clean($product["name"]); ?>">
            <h3><?php echo clean($product["name"]); ?></h3>
            <p class="product-meta"><?php echo clean($product["gender"] . " / " . $product["category_name"]); ?></p>
            <p><?php echo clean(substr($product["description"], 0, 80)); ?></p>
            <p class="price">BDT <?php echo clean($product["price"]); ?></p>
            <a class="btn" href="product_details.php?id=<?php echo $product["id"]; ?>">View Details</a>
        </div>
    <?php } ?>
    <?php if (count($products) == 0) { ?>
        <div class="empty-state">No products found. Try clearing the filters or searching another item.</div>
    <?php } ?>
</div>

<?php require_once "footer.php"; ?>
