<?php
require_once __DIR__ . "/../config/db.php";

function getCategories($gender = "")
{
    global $conn;

    $where = "WHERE child.parent_id IS NOT NULL";
    if ($gender != "") {
        $gender = mysqli_real_escape_string($conn, $gender);
        $where .= " AND parent.name = '$gender'";
    }

    $query = "SELECT child.*, parent.name AS gender
              FROM categories child
              INNER JOIN categories parent ON child.parent_id = parent.id
              $where
              ORDER BY parent.name, child.name";
    $result = mysqli_query($conn, $query);
    $categories = array();

    while ($row = mysqli_fetch_assoc($result)) {
        $categories[] = $row;
    }

    return $categories;
}

function ensureProductDisplayColumns()
{
    global $conn;
    static $checked = false;

    if ($checked) {
        return;
    }

    $featured = mysqli_query($conn, "SHOW COLUMNS FROM products LIKE 'is_featured'");
    if ($featured && mysqli_num_rows($featured) == 0) {
        mysqli_query($conn, "ALTER TABLE products ADD is_featured TINYINT(1) NOT NULL DEFAULT 0");
    }

    $newArrival = mysqli_query($conn, "SHOW COLUMNS FROM products LIKE 'is_new_arrival'");
    if ($newArrival && mysqli_num_rows($newArrival) == 0) {
        mysqli_query($conn, "ALTER TABLE products ADD is_new_arrival TINYINT(1) NOT NULL DEFAULT 0");
    }

    $availableSizes = mysqli_query($conn, "SHOW COLUMNS FROM products LIKE 'available_sizes'");
    if ($availableSizes && mysqli_num_rows($availableSizes) == 0) {
        mysqli_query($conn, "ALTER TABLE products ADD available_sizes VARCHAR(255) NOT NULL DEFAULT 'S,M,L,XL'");
    }

    $checked = true;
}

function createProduct($name, $description, $sizeChart, $availableSizes, $price, $categoryId, $imagePath, $stock, $gender, $isFeatured = 0, $isNewArrival = 0)
{
    global $conn;
    ensureProductDisplayColumns();

    $name = mysqli_real_escape_string($conn, $name);
    $description = mysqli_real_escape_string($conn, $description);
    $sizeChart = mysqli_real_escape_string($conn, $sizeChart);
    $availableSizes = mysqli_real_escape_string($conn, $availableSizes);
    $price = (float) $price;
    $categoryId = (int) $categoryId;
    $imagePath = mysqli_real_escape_string($conn, $imagePath);
    $stock = (int) $stock;
    $gender = mysqli_real_escape_string($conn, $gender);
    $isFeatured = (int) $isFeatured;
    $isNewArrival = (int) $isNewArrival;

    $query = "INSERT INTO products (name, description, size_chart, available_sizes, price, category_id, image_path, stock, gender, is_featured, is_new_arrival)
              VALUES ('$name', '$description', '$sizeChart', '$availableSizes', $price, $categoryId, '$imagePath', $stock, '$gender', $isFeatured, $isNewArrival)";

    return mysqli_query($conn, $query);
}

function updateProduct($id, $name, $description, $sizeChart, $availableSizes, $price, $categoryId, $imagePath, $stock, $gender, $isFeatured = 0, $isNewArrival = 0)
{
    global $conn;
    ensureProductDisplayColumns();

    $id = (int) $id;
    $name = mysqli_real_escape_string($conn, $name);
    $description = mysqli_real_escape_string($conn, $description);
    $sizeChart = mysqli_real_escape_string($conn, $sizeChart);
    $availableSizes = mysqli_real_escape_string($conn, $availableSizes);
    $price = (float) $price;
    $categoryId = (int) $categoryId;
    $imagePath = mysqli_real_escape_string($conn, $imagePath);
    $stock = (int) $stock;
    $gender = mysqli_real_escape_string($conn, $gender);
    $isFeatured = (int) $isFeatured;
    $isNewArrival = (int) $isNewArrival;

    $query = "UPDATE products SET name = '$name', description = '$description', size_chart = '$sizeChart',
              available_sizes = '$availableSizes',
              price = $price, category_id = $categoryId, stock = $stock, gender = '$gender',
              is_featured = $isFeatured, is_new_arrival = $isNewArrival";

    if ($imagePath != "") {
        $query .= ", image_path = '$imagePath'";
    }

    $query .= " WHERE id = $id";

    return mysqli_query($conn, $query);
}

function deleteProductById($id)
{
    global $conn;

    $id = (int) $id;
    $check = mysqli_query($conn, "SELECT id FROM order_items WHERE product_id = $id LIMIT 1");

    if (mysqli_num_rows($check) > 0) {
        return false;
    }

    mysqli_query($conn, "DELETE FROM cart WHERE product_id = $id");
    return mysqli_query($conn, "DELETE FROM products WHERE id = $id");
}

function getProductById($id)
{
    global $conn;
    ensureProductDisplayColumns();

    $id = (int) $id;
    $query = "SELECT products.*, categories.name AS category_name
              FROM products
              LEFT JOIN categories ON products.category_id = categories.id
              WHERE products.id = $id";
    $result = mysqli_query($conn, $query);

    return mysqli_fetch_assoc($result);
}

function getProducts($search = "", $categoryId = "", $gender = "", $limit = 0, $collection = "")
{
    global $conn;
    ensureProductDisplayColumns();

    $where = "WHERE 1";

    if ($search != "") {
        $search = mysqli_real_escape_string($conn, $search);
        $where .= " AND products.name LIKE '%$search%'";
    }

    if ($categoryId != "") {
        $categoryId = (int) $categoryId;
        $where .= " AND products.category_id = $categoryId";
    }

    if ($gender != "") {
        $gender = mysqli_real_escape_string($conn, $gender);
        $where .= " AND products.gender = '$gender'";
    }

    if ($collection == "featured") {
        $where .= " AND products.is_featured = 1";
    }

    if ($collection == "new") {
        $where .= " AND products.is_new_arrival = 1";
    }

    $query = "SELECT products.*, categories.name AS category_name
              FROM products
              LEFT JOIN categories ON products.category_id = categories.id
              $where
              ORDER BY products.id DESC";

    if ($limit > 0) {
        $limit = (int) $limit;
        $query .= " LIMIT $limit";
    }

    $result = mysqli_query($conn, $query);
    $products = array();

    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = $row;
    }

    return $products;
}

function getFeaturedProducts($limit = 6)
{
    return getProducts("", "", "", $limit, "featured");
}

function getNewArrivalProducts($limit = 6)
{
    return getProducts("", "", "", $limit, "new");
}
?>
