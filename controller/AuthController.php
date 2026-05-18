<?php
require_once __DIR__ . "/../model/UserModel.php";

function registerUser($data)
{
    $name = trim($data["name"] ?? "");
    $email = trim($data["email"] ?? "");
    $password = $data["password"] ?? "";
    $role = $data["role"] ?? "customer";
    $address = trim($data["address"] ?? "");
    $phone = trim($data["phone"] ?? "");

    if ($name == "" || $email == "" || $password == "" || $address == "" || $phone == "") {
        return "All fields are required.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return "Invalid email address.";
    }

    if (strlen($password) < 8) {
        return "Password must be at least 8 characters.";
    }

    if ($role != "admin" && $role != "customer") {
        return "Invalid role selected.";
    }

    if (getUserByEmail($email)) {
        return "Email already exists.";
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);

    if (createUser($name, $email, $hash, $role, $address, $phone)) {
        return "success";
    }

    return "Registration failed.";
}

function loginUser($data)
{
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    $email = trim($data["email"] ?? "");
    $password = $data["password"] ?? "";

    if ($email == "" || $password == "") {
        return "Email and password are required.";
    }

    $user = getUserByEmail($email);

    if (!$user || !password_verify($password, $user["password_hash"])) {
        return "Wrong email or password.";
    }

    $_SESSION["user_id"] = $user["id"];
    $_SESSION["name"] = $user["name"];
    $_SESSION["role"] = $user["role"];

    if (isset($data["remember"])) {
        $token = bin2hex(random_bytes(32));
        saveRememberToken($user["id"], $token);
        setcookie("remember_token", $token, time() + (86400 * 7), "/");
    }

    return "success";
}
?>
