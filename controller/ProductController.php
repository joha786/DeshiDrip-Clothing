<?php
require_once __DIR__ . "/../model/ProductModel.php";

function saveProductController($data, $file)
{
    $id = (int) ($data["id"] ?? 0);
    $name = trim($data["name"] ?? "");
    $description = trim($data["description"] ?? "");
    $sizeChart = trim($data["size_chart"] ?? "");
    $availableSizes = trim($data["available_sizes"] ?? "");
    $price = (float) ($data["price"] ?? 0);
    $categoryId = (int) ($data["category_id"] ?? 0);
    $stock = (int) ($data["stock"] ?? 0);
    $gender = $data["gender"] ?? "";
    $isFeatured = isset($data["is_featured"]) ? 1 : 0;
    $isNewArrival = isset($data["is_new_arrival"]) ? 1 : 0;

    if ($name == "" || $description == "" || $sizeChart == "" || $availableSizes == "" || $price <= 0 || $categoryId == 0 || $stock < 0) {
        return "Please fill all product fields correctly.";
    }

    if ($gender != "Men" && $gender != "Women") {
        return "Invalid gender.";
    }

    $imagePath = uploadProductImage($file);

    if ($imagePath == "invalid") {
        return "Only JPG and PNG images under 2MB are allowed.";
    }

    if ($id > 0) {
        return updateProduct($id, $name, $description, $sizeChart, $availableSizes, $price, $categoryId, $imagePath, $stock, $gender, $isFeatured, $isNewArrival) ? "success" : "Product update failed.";
    }

    if ($imagePath == "") {
        return "Product image is required.";
    }

    return createProduct($name, $description, $sizeChart, $availableSizes, $price, $categoryId, $imagePath, $stock, $gender, $isFeatured, $isNewArrival) ? "success" : "Product create failed.";
}

function uploadProductImage($file)
{
    if (!isset($file["image"]) || $file["image"]["name"] == "") {
        return "";
    }

    $allowed = array("image/jpeg", "image/png");

    if (!in_array($file["image"]["type"], $allowed) || $file["image"]["size"] > 2097152) {
        return "invalid";
    }

    $extension = pathinfo($file["image"]["name"], PATHINFO_EXTENSION);
    $name = "product_" . time() . "_" . rand(1000, 9999) . "." . $extension;
    $target = __DIR__ . "/../public/uploads/products/" . $name;

    if (move_uploaded_file($file["image"]["tmp_name"], $target)) {
        return "public/uploads/products/" . $name;
    }

    return "";
}
?>
