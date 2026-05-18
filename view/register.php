<?php
require_once "header.php";
require_once __DIR__ . "/../controller/AuthController.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $result = registerUser($_POST);

    if ($result == "success") {
        header("Location: login.php?registered=1");
        exit;
    }

    $message = $result;
}
?>

<h2>Register</h2>
<?php if ($message != "") { ?><div class="message"><?php echo clean($message); ?></div><?php } ?>

<form method="post" onsubmit="return validateRegister()">
    <label>Name</label>
    <input type="text" name="name" id="name">

    <label>Email</label>
    <input type="email" name="email" id="email">

    <label>Password</label>
    <input type="password" name="password" id="password">

    <label>Address</label>
    <textarea name="address" id="address"></textarea>

    <label>Phone</label>
    <input type="text" name="phone" id="phone">

    <label>Role</label>
    <select name="role" id="role">
        <option value="customer">Customer</option>
        <option value="admin">Admin</option>
    </select>

    <button type="submit">Register</button>
</form>

<script>
function validateRegister() {
    let password = document.getElementById("password").value;
    if (document.getElementById("name").value.trim() == "" || document.getElementById("email").value.trim() == "" || document.getElementById("address").value.trim() == "" || document.getElementById("phone").value.trim() == "") {
        alert("All fields are required.");
        return false;
    }
    if (password.length < 8) {
        alert("Password must be at least 8 characters.");
        return false;
    }
    return true;
}
</script>

<?php require_once "footer.php"; ?>
