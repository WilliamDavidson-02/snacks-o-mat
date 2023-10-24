<nav>
    <div class="stock-wallet-container">
        <form><button class="item-card-btn stock-btn green-container" type="submit">Order new stock</button></form>
        <div class="wallet">$<?= $_SESSION['wallet']; ?></div>
        <a class="item-card-btn stock-btn green-container" href="/">Snacks</a>
        <a class="item-card-btn stock-btn green-container" href="/mix.php">Mix</a>
    </div>
    <div class="cart">
        <?php if (count($_SESSION['cart']) > 0) : ?>
            <div class="cart-notification"><?= count($_SESSION['cart']) < 10 ? count($_SESSION['cart']) : '9+'; ?></div>
        <?php endif; ?>
        <i class="fa-solid fa-cart-shopping"></i>
    </div>
</nav>