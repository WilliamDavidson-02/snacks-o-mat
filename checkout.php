<?php
session_start();

require_once __DIR__ . '/functions.php';

if (isset($_GET['minus'])) {
    $key = array_search($_GET['minus'], $_SESSION['cart']); // $key returns int representing the index.
    if ($key !== false) {
        unset($_SESSION['cart'][$key]);
    }
    if (array_key_exists($_GET['add'], $inventory)) {
        $_SESSION['inventory'][$_GET['minus']]['stock'] += 1;
        $_SESSION['wallet'] += $_SESSION['inventory'][$_GET['minus']]['price'];
    } else { // else it is a pack.
        foreach ($_SESSION['mixPacks'][$_GET['minus']]['items'] as $itemName => $items) {
            $_SESSION['inventory'][$itemName]['stock'] += 1;
        }
        $_SESSION['wallet'] += $_SESSION['mixPacks'][$_GET['minus']]['price'];
    }
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
} else if (isset($_GET['add'])) {
    if (array_key_exists($_GET['add'], $inventory)) {
        addItemToCart($_GET['add']);
    } else { // else it is a pack.
        addPackToCart($_GET['add']);
    }
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
} else if (isset($_GET['delete'])) {
    $_SESSION['cart'] = array_filter($_SESSION['cart'], function ($item) {
        return $item !== $_GET['delete'];
    });
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

$purchased = false;

if (isset($_GET['purchase'])) {
    $_SESSION['cart'] = [];
    $purchased = true;
}

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
                            <button type="submit" name="minus" value="<?= $itemName; ?>" class="stock-btn item-card-btn blue-container"><i class="fa-solid fa-minus"></i></button>
                            <button type="submit" name="add" value="<?= $itemName; ?>" class="stock-btn item-card-btn blue-container"><i class="fa-solid fa-plus"></i></button>
                            <button type="submit" name="delete" value="<?= $itemName; ?>" class="stock-btn item-card-btn blue-container"><i class="fa-solid fa-trash-can"></i></button>
                        </form>
                    </div>
                    <?php if ($itemName !== end($cartKeys)) : ?>
                        <div class="cart-item-line"></div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="purchase-container">
        <?php if (!$purchased) : ?>
            <div>Total $<?= $totalPrice; ?></div>
            <form>
                <button type="submit" name="purchase" class="stock-btn blue-container item-card-btn">Purchase</button>
            </form>
        <?php else : ?>
            <div>Your order it complete!</div>
            <a href="/" class="stock-btn blue-container item-card-btn">Return to store.</a>
        <?php endif; ?>
    </div>
</main>

<?php require_once __DIR__ . '/footer.php'; ?>