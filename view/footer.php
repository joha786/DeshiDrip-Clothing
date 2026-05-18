</main>
<footer class="footer">
    <div class="footer-inner">
        <div class="footer-brand">
            <a class="footer-logo" href="home.php">DeshiDrip</a>
            <p>Bangladesh streetwear inspired by local culture, everyday movement, and modern urban style.</p>
        </div>
        <div class="footer-links" aria-label="Footer links">
            <a href="home.php">Home</a>
            <a href="products.php">Products</a>
            <?php if (isLoggedIn() && isCustomer()) { ?>
                <a href="cart.php">Cart</a>
                <a href="profile.php">Profile</a>
            <?php } ?>
        </div>
        <div class="footer-contact">
            <span>Support: support@deshidrip.com</span>
            <span>Dhaka, Bangladesh</span>
        </div>
    </div>
    <div class="footer-bottom">
        <span>&copy; <?php echo date("Y"); ?> <strong>DeshiDrip Clothing</strong>. All rights reserved.</span>
        <span>All trademarks and brand assets are the property of their respective owners.</span>
    </div>
</footer>
</body>
</html>
