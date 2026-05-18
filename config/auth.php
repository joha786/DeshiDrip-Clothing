<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../model/UserModel.php";

if (!isset($_SESSION["user_id"]) && isset($_COOKIE["remember_token"])) {
    $user = getUserByRememberToken($_COOKIE["remember_token"]);

    if ($user) {
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["name"] = $user["name"];
        $_SESSION["role"] = $user["role"];
    }
}

function isLoggedIn()
{
    return isset($_SESSION["user_id"]);
}

function isAdmin()
{
    return isset($_SESSION["role"]) && $_SESSION["role"] == "admin";
}

function isCustomer()
{
    return isset($_SESSION["role"]) && $_SESSION["role"] == "customer";
}

function requireLogin()
{
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit;
    }
}

function requireAdmin()
{
    requireLogin();

    if (!isAdmin()) {
        header("Location: home.php");
        exit;
    }
}

function requireCustomer()
{
    requireLogin();

    if (!isCustomer()) {
        header("Location: home.php");
        exit;
    }
}

function clean($value)
{
    return htmlspecialchars($value, ENT_QUOTES, "UTF-8");
}
?>
