<?php
header("Content-Type: application/json");
require_once __DIR__ . "/../model/ProductModel.php";

$q = $_GET["q"] ?? "";
$category = $_GET["category"] ?? "";
$gender = $_GET["gender"] ?? "";

echo json_encode(array("status" => "success", "products" => getProducts($q, $category, $gender, 0)));
?>
