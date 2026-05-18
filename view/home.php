<?php
require_once "header.php";
require_once __DIR__ . "/../model/ProductModel.php";

$featured = getFeaturedProducts(6);
$newArrivals = getNewArrivalProducts(6);
$menCategories = getCategories("Men");
$womenCategories = getCategories("Women");

if (count($featured) == 0) {
    $featured = getProducts("", "", "", 6);
}
?>

<section class="hero">
    <div>
        <h1>DeshiDrip Clothing</h1>
        <p>Everyday fits for Dhaka days, campus plans, office hours, and weekend hangouts. Find comfortable shirts, kurtis, jeans, and essentials that are easy to wear and simple to order.</p>
        <div class="hero-actions">
            <a class="btn" href="products.php?collection=new">Shop New Arrivals</a>
            <a class="btn secondary" href="#featuredProducts">See Featured</a>
        </div>
    </div>
    <div class="hero-box">
        <h3>Find Your Fit</h3>
        <p>Quickly filter the collection by everyday menswear or womenswear, then choose the size and quantity that works for you.</p>
        <p><a class="btn secondary" href="products.php?gender=Men">Men</a> <a class="btn secondary" href="products.php?gender=Women">Women</a></p>
    </div>
</section>

<h2 class="section-title">Categories</h2>
<div class="two-col">
    <div class="card category-card">
        <h3>Men</h3>
        <p>Shirts, tees, pants, and easy everyday pieces.</p>
        <?php foreach ($menCategories as $category) { ?>
            <a class="btn secondary" href="products.php?gender=Men&category=<?php echo $category["id"]; ?>"><?php echo clean($category["name"]); ?></a>
        <?php } ?>
    </div>
    <div class="card category-card">
        <h3>Women</h3>
        <p>Kurtis, salwar, jeans, and comfortable daily wear.</p>
        <?php foreach ($womenCategories as $category) { ?>
            <a class="btn secondary" href="products.php?gender=Women&category=<?php echo $category["id"]; ?>"><?php echo clean($category["name"]); ?></a>
        <?php } ?>
    </div>
</div>

<h2 class="section-title">New Arrivals</h2>
<div class="grid">
    <?php foreach ($newArrivals as $product) { ?>
        <div class="card">
            <img class="product-img" src="../<?php echo clean($product["image_path"]); ?>" alt="<?php echo clean($product["name"]); ?>">
            <h3><?php echo clean($product["name"]); ?></h3>
            <p class="product-meta"><?php echo clean($product["gender"] . " / " . $product["category_name"]); ?></p>
            <p class="price">BDT <?php echo clean($product["price"]); ?></p>
            <a class="btn" href="product_details.php?id=<?php echo $product["id"]; ?>">View Details</a>
        </div>
    <?php } ?>
    <?php if (count($newArrivals) == 0) { ?>
        <div class="empty-state">New arrivals will appear here after the admin marks products as New Arrival.</div>
    <?php } ?>
</div>

<h2 class="section-title">Search Products</h2>
<div class="filters">
    <input type="text" id="searchText" placeholder="Search by product name">
    <select id="genderFilter">
        <option value="">All Gender</option>
        <option value="Men">Men</option>
        <option value="Women">Women</option>
    </select>
    <select id="categoryFilter">
        <option value="">All Categories</option>
        <?php foreach (getCategories() as $category) { ?>
            <option value="<?php echo $category["id"]; ?>"><?php echo clean($category["gender"] . " - " . $category["name"]); ?></option>
        <?php } ?>
    </select>
    <button type="button" onclick="searchProducts()">Search</button>
</div>
<div id="searchResult" class="grid"></div>

<h2 class="section-title" id="featuredProducts">Featured Products</h2>
<div class="grid">
    <?php foreach ($featured as $product) { ?>
        <div class="card">
            <img class="product-img" src="../<?php echo clean($product["image_path"]); ?>" alt="<?php echo clean($product["name"]); ?>">
            <h3><?php echo clean($product["name"]); ?></h3>
            <p class="product-meta"><?php echo clean($product["gender"] . " / " . $product["category_name"]); ?></p>
            <p class="price">BDT <?php echo clean($product["price"]); ?></p>
            <a class="btn" href="product_details.php?id=<?php echo $product["id"]; ?>">View Details</a>
        </div>
    <?php } ?>
</div>

<script>
function searchProducts() {
    let q = document.getElementById("searchText").value;
    let gender = document.getElementById("genderFilter").value;
    let category = document.getElementById("categoryFilter").value;
    let searchResult = document.getElementById("searchResult");

    searchResult.innerHTML = "<div class='empty-state'>Searching the collection...</div>";
    fetch("../ajax/product_search.php?q=" + encodeURIComponent(q) + "&gender=" + encodeURIComponent(gender) + "&category=" + encodeURIComponent(category))
    .then(res => res.json())
    .then(data => {
        let html = "";
        data.products.forEach(function(product) {
            html += `<div class="card">
                <img class="product-img" src="../${escapeHtml(product.image_path)}" alt="${escapeHtml(product.name)}">
                <h3>${escapeHtml(product.name)}</h3>
                <p class="product-meta">${escapeHtml(product.gender)} / ${escapeHtml(product.category_name)}</p>
                <p class="price">BDT ${escapeHtml(product.price)}</p>
                <a class="btn" href="product_details.php?id=${encodeURIComponent(product.id)}">View Details</a>
            </div>`;
        });
        searchResult.innerHTML = html || "<div class='empty-state'>No products matched your search. Try another category or product name.</div>";
    })
    .catch(() => {
        searchResult.innerHTML = "<div class='empty-state'>Search is not available right now. Please try again.</div>";
    });
}

function escapeHtml(value) {
    return String(value).replace(/[&<>"']/g, function(character) {
        return {
            "&": "&amp;",
            "<": "&lt;",
            ">": "&gt;",
            '"': "&quot;",
            "'": "&#039;"
        }[character];
    });
}
</script>

<?php require_once "footer.php"; ?>
