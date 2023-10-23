<?php

session_start();

require_once __DIR__ . '/inventory.php';
require_once __DIR__ . '/functions.php';

if (!isset($_SESSION['inventory'])) {
    $_SESSION['inventory'] = generatePriceAndInventory($inventory);
}

if (!isset($_SESSION['wallet'])) {
    $_SESSION['wallet'] = 9999;
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (isset($_GET['snack'])) {
    $snack = $_SESSION['inventory'][$_GET['snack']];
    if (($_SESSION['wallet'] - $snack['price']) >= 0 && $snack['stock'] > 0) {
        $_SESSION['cart'][] = $_GET['snack'];
        $_SESSION['inventory'][$_GET['snack']]['stock'] -= 1;
    }
    // preventing same action to run again if page is reloaded.
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

$inventory = $_SESSION['inventory'];
$wallet = $_SESSION['wallet'];
$cart = $_SESSION['cart'];

require_once __DIR__ . '/header.php'; ?>

<main>
    <nav>
        <div class="stock-wallet-container">
            <form><button class="item-card-btn stock-btn" type="submit">Order new stock</button></form>
            <div class="wallet">$<?= $wallet; ?></div>
        </div>
        <div class="cart">
            <?php if (count($cart) > 0) : ?>
                <div class="cart-notification"><?= count($cart) < 10 ? count($cart) : count($cart) . '+'; ?></div>
            <?php endif; ?>
            <i class="fa-solid fa-cart-shopping"></i>
        </div>
    </nav>
    <form method="get" class="item-card-container">
        <?php foreach ($inventory as $index => $item) : ?>
            <div class="item-card">
                <div class="item-title-container">
                    <h3><?= ucfirst($index); ?></h3>
                </div>
                <div class="item-content">
                    <div class="item-icon"><?= $item['icon']; ?></div>
                    <div class="item-info-container">
                        <p class="price">$<?= $item['price']; ?></p>
                        <p class="stock">Stock <?= $item['stock']; ?></p>
                    </div>
                    <button name="snack" value="<?= $index; ?>" type="submit" class="item-card-btn">Add to cart</button>
                </div>
            </div>
        <?php endforeach; ?>
    </form>
</main>

<?php require_once __DIR__ . '/footer.php'; ?>