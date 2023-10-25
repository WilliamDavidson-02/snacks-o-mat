<?php
session_start();

require_once __DIR__ . '/functions.php';

$cart = array_count_values($_SESSION['cart']);

$cart = array_combine(array_keys($cart), array_map(function ($count) {
    return ['quantity' => $count];
}, $cart));

$totalPrice = 0;

foreach ($cart as $item => $quantity) {
    $sessionType = array_key_exists($item, $_SESSION['inventory']) ? 'inventory' : 'mixPacks';
    $cart[$item]['price'] = $_SESSION[$sessionType][$item]['price'] * $quantity['quantity'];
    $totalPrice += $cart[$item]['price'];
}

$cartKeys = array_keys($cart);

require_once __DIR__ . '/header.php';
?>

<main>
    <div class="cart-main">
        <h1>Cart</h1>
        <div class="cart-item-container">
            <?php foreach ($cart as $itemName => $item) : ?>
                <div class="cart-item">
                    <div class="cart-item-info">
                        <div>
                            <h3><?= ucfirst($itemName) . ' x ' . $item['quantity']; ?></h3>
                            <div>$<?= $item['price']; ?></div>
                        </div>
                        <form class="flex">
                            <button class="stock-btn item-card-btn blue-container"><i class="fa-solid fa-minus"></i></button>
                            <button class="stock-btn item-card-btn blue-container"><i class="fa-solid fa-plus"></i></button>
                            <button class="stock-btn item-card-btn blue-container"><i class="fa-solid fa-trash-can"></i></button>
                        </form>
                    </div>
                    <?php if ($itemName !== end($cartKeys)) : ?>
                        <div class="cart-item-line"></div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <div>
        <div>Total $<?= $totalPrice; ?></div>
    </div>
</main>

<?php require_once __DIR__ . '/footer.php'; ?>