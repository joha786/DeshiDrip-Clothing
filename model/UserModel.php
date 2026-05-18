<?php
require_once __DIR__ . "/../config/db.php";

function createUser($name, $email, $passwordHash, $role, $address, $phone)
{
    global $conn;

    $name = mysqli_real_escape_string($conn, $name);
    $email = mysqli_real_escape_string($conn, $email);
    $passwordHash = mysqli_real_escape_string($conn, $passwordHash);
    $role = mysqli_real_escape_string($conn, $role);
    $address = mysqli_real_escape_string($conn, $address);
    $phone = mysqli_real_escape_string($conn, $phone);

    $query = "INSERT INTO users (name, email, password_hash, role, address, phone)
              VALUES ('$name', '$email', '$passwordHash', '$role', '$address', '$phone')";

    return mysqli_query($conn, $query);
}

function getUserByEmail($email)
{
    global $conn;

    $email = mysqli_real_escape_string($conn, $email);
    $query = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $query);

    return mysqli_fetch_assoc($result);
}

function getUserById($id)
{
    global $conn;

    $id = (int) $id;
    $query = "SELECT * FROM users WHERE id = $id";
    $result = mysqli_query($conn, $query);

    return mysqli_fetch_assoc($result);
}

function getUserByRememberToken($token)
{
    global $conn;

    $token = mysqli_real_escape_string($conn, $token);
    $query = "SELECT * FROM users WHERE remember_token = '$token'";
    $result = mysqli_query($conn, $query);

    return mysqli_fetch_assoc($result);
}

function saveRememberToken($userId, $token)
{
    global $conn;

    $userId = (int) $userId;
    $token = mysqli_real_escape_string($conn, $token);
    $query = "UPDATE users SET remember_token = '$token' WHERE id = $userId";

    return mysqli_query($conn, $query);
}

function clearRememberToken($userId)
{
    global $conn;

    $userId = (int) $userId;
    $query = "UPDATE users SET remember_token = NULL WHERE id = $userId";

    return mysqli_query($conn, $query);
}

function updateUserProfile($id, $name, $email, $address, $phone, $profilePicture)
{
    global $conn;

    $id = (int) $id;
    $name = mysqli_real_escape_string($conn, $name);
    $email = mysqli_real_escape_string($conn, $email);
    $address = mysqli_real_escape_string($conn, $address);
    $phone = mysqli_real_escape_string($conn, $phone);
    $profilePicture = mysqli_real_escape_string($conn, $profilePicture);

    $query = "UPDATE users SET name = '$name', email = '$email', address = '$address', phone = '$phone'";

    if ($profilePicture != "") {
        $query .= ", profile_picture = '$profilePicture'";
    }

    $query .= " WHERE id = $id";

    return mysqli_query($conn, $query);
}

function updateUserPassword($id, $passwordHash)
{
    global $conn;

    $id = (int) $id;
    $passwordHash = mysqli_real_escape_string($conn, $passwordHash);
    $query = "UPDATE users SET password_hash = '$passwordHash' WHERE id = $id";

    return mysqli_query($conn, $query);
}

function getAllCustomers()
{
    global $conn;

    $query = "SELECT * FROM users WHERE role = 'customer' ORDER BY id DESC";
    $result = mysqli_query($conn, $query);
    $customers = array();

    while ($row = mysqli_fetch_assoc($result)) {
        $customers[] = $row;
    }

    return $customers;
}

function deleteCustomerById($id)
{
    global $conn;

    $id = (int) $id;
    mysqli_query($conn, "DELETE FROM cart WHERE user_id = $id");
    mysqli_query($conn, "DELETE payments FROM payments INNER JOIN orders ON payments.order_id = orders.id WHERE orders.user_id = $id");
    mysqli_query($conn, "DELETE order_items FROM order_items INNER JOIN orders ON order_items.order_id = orders.id WHERE orders.user_id = $id");
    mysqli_query($conn, "DELETE FROM orders WHERE user_id = $id");

    return mysqli_query($conn, "DELETE FROM users WHERE id = $id AND role = 'customer'");
}
?>
