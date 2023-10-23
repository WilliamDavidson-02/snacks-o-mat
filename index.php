<?php

session_start();

require_once __DIR__ . '/inventory.php';
require_once __DIR__ . '/functions.php';

if (!isset($_SESSION['inventory'])) {
    $_SESSION['inventory'] = generatePriceAndInventory($inventory);
}

echo '<pre/>';
print_r($_SESSION['inventory']);

require_once __DIR__ . '/header.php'; ?>


<?php require_once __DIR__ . '/footer.php'; ?>