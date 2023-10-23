<?php

session_start();

require_once __DIR__ . '/inventory.php';
require_once __DIR__ . '/functions.php';

if (!isset($_SESSION['inventory'])) {
    $_SESSION['inventory'] = generatePriceAndInventory($inventory);
}

$inventory = $_SESSION['inventory'];

require_once __DIR__ . '/header.php'; ?>

<main>
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
                    <button type="submit" class="item-card-btn">Add to cart</button>
                </div>
            </div>
        <?php endforeach; ?>
    </form>
</main>

<?php require_once __DIR__ . '/footer.php'; ?>