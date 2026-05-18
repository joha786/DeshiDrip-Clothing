<?php
require_once "header.php";
require_once __DIR__ . "/../controller/AuthController.php";

$message = "";

if (isset($_GET["registered"])) {
    $message = "Registration successful. Please login.";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $result = loginUser($_POST);

    if ($result == "success") {
        if ($_SESSION["role"] == "admin") {
            header("Location: admin_dashboard.php");
        } else {
            header("Location: home.php");
        }
        exit;
    }

    $message = $result;
}
?>

<h2>Login</h2>
<?php if ($message != "") { ?><div class="message <?php if (isset($_GET["registered"])) echo "success"; ?>"><?php echo clean($message); ?></div><?php } ?>

<form method="post" onsubmit="return validateLogin()">
    <label>Email</label>
    <input type="email" name="email" id="email">

    <label>Password</label>
    <input type="password" name="password" id="password">

    <label><input type="checkbox" name="remember" style="width:auto"> Remember Me</label>

    <button type="submit">Login</button>
</form>

<script>
function validateLogin() {
    if (document.getElementById("email").value.trim() == "" || document.getElementById("password").value.trim() == "") {
        alert("Email and password are required.");
        return false;
    }
    return true;
}
</script>

<?php require_once "footer.php"; ?>
