<?php

session_start();

require_once __DIR__ . '/functions.php';

initialSessionSet();

if (isset($_GET['snack'])) {
    $snack = $_SESSION['inventory'][$_GET['snack']];
    if (($_SESSION['wallet'] - $snack['price']) >= 0 && $snack['stock'] > 0) {
        $_SESSION['cart'][] = $_GET['snack'];
        $_SESSION['inventory'][$_GET['snack']]['stock'] -= 1;
        $_SESSION['wallet'] -= $snack['price'];
    }
    // preventing same action to run again if page is reloaded.
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

if (isset($_GET['stock'])) {
    orderNewStock();
}

require_once __DIR__ . '/header.php'; ?>

<main>
    <?php require_once __DIR__ . '/navigation.php'; ?>
    <form class="item-card-container">
        <?php foreach ($_SESSION['inventory'] as $index => $item) : ?>
            <div class="item-card">
                <div class="item-title-container">
                    <h3><?= ucfirst($index); ?></h3>
                </div>
                <div class="item-content">
                    <div class="item-icon"><?= $item['icon']; ?></div>
                    <div class="item-info-container">
                        <div class="price">$<?= $item['price']; ?></div>
                        <div class="item-name">Stock <?= $item['stock']; ?></div>
                    </div>
                    <button name="snack" value="<?= $index; ?>" type="submit" class="item-card-btn blue-container">Add to cart</button>
                </div>
            </div>
        <?php endforeach; ?>
    </form>
</main>

<?php require_once __DIR__ . '/footer.php'; ?>