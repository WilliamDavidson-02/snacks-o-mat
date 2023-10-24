<?php

session_start();

require_once __DIR__ . '/functions.php';

if (!isset($_SESSION['inventory'])) {
    $_SESSION['inventory'] = generatePriceAndInventory();
}

if (!isset($_SESSION['mixPacks'])) {
    $_SESSION['mixPacks'] = generateMixPacks($_SESSION['inventory']);
}

// echo '<pre/>';
// die(var_dump(generateMixPacks($_SESSION['inventory'])));

if (isset($_GET['pack'])) {
    $pack = $_SESSION['mixPacks'][$_GET['pack']];
    if (($_SESSION['wallet'] - $pack['price']) >= 0) {
        $isInStock = true;
        foreach ($pack['items'] as $key => $item) {
            if ($_SESSION['inventory'][$key]['stock'] - 1 < 0) {
                $isInStock = false;
            }
        }
        if ($isInStock) {
            $_SESSION['cart'][] = $_GET['pack'];
            foreach ($_SESSION['inventory'] as $itemName => $item) {
                if (array_key_exists($itemName, $pack['items'])) {
                    $_SESSION['inventory'][$itemName]['stock'] -= 1;
                }
            }
            $_SESSION['wallet'] -= $pack['price'];
        }
    }
    // preventing same action to run again if page is reloaded.
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

require_once __DIR__ . '/header.php';
?>
<main>
    <?php require_once __DIR__ . '/navigation.php'; ?>
    <form class="item-card-container">
        <?php foreach ($_SESSION['mixPacks'] as $packName => $pack) : ?>
            <div class="item-card">
                <div class="item-title-container">
                    <h3><?= ucfirst($packName); ?></h3>
                </div>
                <div class="item-content">
                    <div class="item-icon mix-icon-container">
                        <?php
                        $keys = array_keys($pack['items']);
                        $iconLength = count($pack['items']) <= 4 ? count($pack['items']) : 4;
                        for ($i = 0; $i < $iconLength; $i++) : ?>
                            <div class="mix-icon"><?= $pack['items'][$keys[$i]]['icon']; ?></div>
                        <?php endfor; ?>
                    </div>
                    <div class="item-info-container">
                        <div class="green-container price">$<?= $pack['price']; ?></div>
                        <div class="items-words-container">
                            <?php foreach ($pack['items'] as $itemName => $item) : ?>
                                <div class="item-name"><?= ucfirst($itemName); ?></div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <button name="pack" value="<?= $packName; ?>" type="submit" class="item-card-btn green-container">Add to cart</button>
                </div>
            </div>
        <?php endforeach; ?>
    </form>
</main>
<?php require_once __DIR__ . '/footer.php'; ?>