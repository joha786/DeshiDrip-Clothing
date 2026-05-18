<?php
require_once "header.php";
requireCustomer();
require_once __DIR__ . "/../model/UserModel.php";
//conflict resolved
$user = getUserById($_SESSION["user_id"]);
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $address = trim($_POST["address"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $picture = "";

    if ($name == "" || $email == "" || $address == "" || $phone == "") {
        $message = "All profile fields are required.";
    } else {
        if (isset($_FILES["profile_picture"]) && $_FILES["profile_picture"]["name"] != "") {
            if (in_array($_FILES["profile_picture"]["type"], array("image/jpeg", "image/png")) && $_FILES["profile_picture"]["size"] <= 2097152) {
                $ext = pathinfo($_FILES["profile_picture"]["name"], PATHINFO_EXTENSION);
                $fileName = "profile_" . time() . "." . $ext;
                move_uploaded_file($_FILES["profile_picture"]["tmp_name"], __DIR__ . "/../public/uploads/profiles/" . $fileName);
                $picture = "public/uploads/profiles/" . $fileName;
            } else {
                $message = "Profile picture must be JPG or PNG under 2MB.";
            }
        }

        if ($message == "" && updateUserProfile($_SESSION["user_id"], $name, $email, $address, $phone, $picture)) {
            $_SESSION["name"] = $name;
            $message = "Profile updated successfully.";
            $user = getUserById($_SESSION["user_id"]);
        }

        if (($_POST["new_password"] ?? "") != "") {
            if (!password_verify($_POST["current_password"] ?? "", $user["password_hash"])) {
                $message = "Current password is wrong.";
            } elseif (strlen($_POST["new_password"]) < 8) {
                $message = "New password must be 8 characters.";
            } else {
                updateUserPassword($_SESSION["user_id"], password_hash($_POST["new_password"], PASSWORD_DEFAULT));
                $message = "Profile and password updated successfully.";
            }
        }
    }
}
?>

<h2>Profile</h2>
<?php if ($message != "") { ?><div class="message success"><?php echo clean($message); ?></div><?php } ?>

<p class="page-intro">Manage your customer details here. Your orders and purchase history are available on a separate page.</p>
<a class="btn secondary" href="purchase_history.php">View Purchase History</a>

<form method="post" enctype="multipart/form-data" onsubmit="return validateProfile()">
    <label>Name</label>
    <input type="text" name="name" id="name" value="<?php echo clean($user["name"]); ?>">

    <label>Email</label>
    <input type="email" name="email" id="email" value="<?php echo clean($user["email"]); ?>">

    <label>Address</label>
    <textarea name="address" id="address"><?php echo clean($user["address"]); ?></textarea>

    <label>Phone</label>
    <input type="text" name="phone" id="phone" value="<?php echo clean($user["phone"]); ?>">

    <label>Profile Picture</label>
    <input type="file" name="profile_picture" accept="image/jpeg,image/png">

    <label>Current Password</label>
    <input type="password" name="current_password">

    <label>New Password</label>
    <input type="password" name="new_password">

    <button type="submit">Update Profile</button>
</form>

<script>
function validateProfile() {
    if (document.getElementById("name").value.trim() == "" || document.getElementById("email").value.trim() == "" || document.getElementById("address").value.trim() == "" || document.getElementById("phone").value.trim() == "") {
        alert("All profile fields are required.");
        return false;
    }
    return true;
}
</script>

<?php require_once "footer.php"; ?>
